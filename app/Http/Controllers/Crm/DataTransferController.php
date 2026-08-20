<?php

declare(strict_types=1);

namespace App\Http\Controllers\Crm;

use App\Enums\LeadStage;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DataTransferController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type', 'leads');

        abort_unless(in_array($type, ['leads', 'customers'], true), 404);
        abort_unless($this->canView($type), 403);

        return view('crm.data-transfer.index', [
            'type' => $type,
            'columns' => $this->columns($type),
        ]);
    }

    public function template(Request $request): StreamedResponse
    {
        $type = $request->input('type', 'leads');
        abort_unless(in_array($type, ['leads', 'customers'], true), 404);
        abort_unless($this->canView($type), 403);

        $columns = $this->columns($type);
        $filename = $type.'-import-template.xlsx';

        return response()->streamDownload(function () use ($columns): void {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->fromArray([array_keys($columns)], null, 'A1');
            $sheet->getStyle('A1:'.$sheet->getHighestColumn().'1')->getFont()->setBold(true);
            $sheet->freezePane('A2');
            foreach (range(1, count($columns)) as $index) {
                $sheet->getColumnDimensionByColumn($index)->setWidth(22);
            }
            // Header names are database keys so the importer maps them safely.
            // No example row is included to prevent accidental fake imports.
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    public function export(Request $request): StreamedResponse
    {
        $type = $request->input('type', 'leads');
        abort_unless(in_array($type, ['leads', 'customers'], true), 404);
        abort_unless($this->canView($type), 403);

        $columns = array_values(array_intersect($request->input('columns', []), array_keys($this->columns($type))));
        if ($columns === []) {
            $columns = array_keys($this->columns($type));
        }

        $query = $type === 'leads' ? Lead::query() : Customer::query();
        if ($type === 'leads' && ! auth()->user()->hasAnyPermission(['view all leads', 'manage crm'])) {
            $query->where('assigned_sales_id', auth()->id());
        }
        if ($type === 'customers' && ! auth()->user()->hasAnyPermission(['view all customers', 'manage crm'])) {
            $query->whereHas('leads', fn ($q) => $q->where('assigned_sales_id', auth()->id()));
        }

        $filename = $type.'-'.now()->format('Y-m-d_H-i').'.csv';
        $definitions = $this->columns($type);

        return response()->streamDownload(function () use ($query, $columns, $definitions, $type): void {
            $handle = fopen('php://output', 'wb');
            fprintf($handle, "\xEF\xBB\xBF");
            fputcsv($handle, array_map(fn ($column) => $definitions[$column], $columns));

            $query->orderBy('id')->chunkById(500, function ($rows) use ($handle, $columns, $type): void {
                foreach ($rows as $row) {
                    fputcsv($handle, array_map(fn ($column) => $this->valueFor($row, $column, $type), $columns));
                }
            });
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function import(Request $request): RedirectResponse
    {
        $type = $request->input('type', 'leads');
        abort_unless(in_array($type, ['leads', 'customers'], true), 404);
        abort_unless($this->canImport($type), 403);

        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls', 'max:10240'],
            'columns' => ['required', 'array', 'min:1'],
            'columns.*' => ['string'],
        ]);

        $allowed = array_keys($this->columns($type));
        $columns = array_values(array_intersect($validated['columns'], $allowed));
        $file = $request->file('file');
        $isExcel = in_array(strtolower($file->getClientOriginalExtension()), ['xlsx', 'xls'], true);

        if ($isExcel) {
            $sheet = IOFactory::load($file->getRealPath())->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, false);
            $headers = array_map(fn ($header) => strtolower(trim((string) $header)), array_shift($rows) ?: []);
        } else {
            $handle = fopen($file->getRealPath(), 'rb');
            $headers = fgetcsv($handle) ?: [];
            $rows = [];
            while (($row = fgetcsv($handle)) !== false) {
                $rows[] = $row;
            }
            fclose($handle);
        }

        $headers = array_map(fn ($header) => str_replace([' ', '-'], '_', $header), $headers);
        $columns = array_values(array_intersect($columns, $headers));

        if (! in_array('phone', $columns, true)) {
            return back()->withErrors(['file' => __('The Excel/CSV file must include the phone column.')]);
        }

        $created = 0;
        $updated = 0;
        foreach ($rows as $values) {
            $data = [];
            foreach ($columns as $index => $column) {
                $data[$column] = trim((string) ($values[array_search($column, $headers, true)] ?? '')) ?: null;
            }
            if (empty($data['phone'])) continue;

            if ($type === 'leads') {
                $existing = Lead::query()->where('phone', $data['phone'])->latest('id')->first();
                $data['stage'] = $data['stage'] ?? LeadStage::New->value;
                $data['status'] = $data['status'] ?? 'active';
                $data['source'] = $data['source'] ?? 'import';
                if ($existing) { $existing->update($data); $updated++; }
                else { Lead::query()->create($data); $created++; }
            } else {
                $existing = Customer::query()->where('phone', $data['phone'])->first();
                $data['source'] = $data['source'] ?? 'import';
                if ($existing) { $existing->update($data); $updated++; }
                else { Customer::query()->create($data); $created++; }
            }
        }

        return back()->with('status', __('Import complete: :created created, :updated updated.', compact('created', 'updated')));
    }

    private function columns(string $type): array
    {
        return $type === 'leads' ? [
            'name' => __('Name'), 'phone' => __('Phone'), 'whatsapp' => __('WhatsApp'), 'email' => __('Email'),
            'address' => __('Address'), 'occupation' => __('Occupation'), 'budget' => __('Budget'), 'stage' => __('Stage'),
            'status' => __('Status'), 'source' => __('Source'), 'campaign' => __('Campaign'), 'unit_type' => __('Unit type'),
            'bedrooms' => __('Bedrooms'), 'required_area' => __('Required area'), 'preferred_payment_plan' => __('Preferred payment plan'),
            'priority' => __('Priority'), 'notes' => __('Notes'), 'follow_up_at' => __('Follow-up at'),
        ] : [
            'name' => __('Name'), 'phone' => __('Phone'), 'whatsapp' => __('WhatsApp'), 'email' => __('Email'),
            'occupation' => __('Occupation'), 'address' => __('Address'), 'budget' => __('Budget'), 'source' => __('Source'), 'notes' => __('Notes'),
        ];
    }

    private function valueFor($row, string $column, string $type): mixed
    {
        $value = $row->{$column};
        return is_object($value) && method_exists($value, 'value') ? $value->value : $value;
    }

    private function canView(string $type): bool
    {
        return auth()->user()->can($type === 'leads' ? 'view own leads' : 'view own customers')
            || auth()->user()->hasAnyPermission(['view all '.$type, 'manage crm']);
    }

    private function canImport(string $type): bool
    {
        return auth()->user()->hasAnyPermission(['create '.$type, 'manage crm']);
    }
}
