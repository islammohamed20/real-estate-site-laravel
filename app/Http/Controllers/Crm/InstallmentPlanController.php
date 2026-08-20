<?php

declare(strict_types=1);

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\CompanyProfile;
use App\Models\Customer;
use App\Models\InstallmentPlan;
use App\Models\InstallmentPlanItem;
use App\Models\Offer;
use App\Models\Unit;
use App\Services\PushNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;

class InstallmentPlanController extends Controller
{
    public function index(): View
    {
        $plans = InstallmentPlan::query()
            ->with(['customer', 'unit.project', 'unit.building', 'unit.floor', 'offer'])
            ->latest('id')
            ->paginate(15);

        return view('crm.plans.index', [
            'plans' => $plans,
            'totalPlans' => InstallmentPlan::query()->count(),
            'totalValue' => InstallmentPlan::query()->sum('final_price'),
            'fromCalculator' => InstallmentPlan::query()->where('saved_from_calculator', true)->count(),
            'trashedCount' => InstallmentPlan::onlyTrashed()->count(),
        ]);
    }

    public function show(InstallmentPlan $plan): View
    {
        $plan->load(['customer', 'unit.project', 'unit.building', 'unit.floor', 'offer', 'items', 'creator', 'lead']);

        $items = $plan->items()->orderBy('installment_number')->get();

        return view('crm.plans.show', [
            'plan' => $plan,
            'schedule' => $plan->schedule_json ?? [],
            'items' => $items,
        ]);
    }

    public function updateItem(InstallmentPlan $plan, InstallmentPlanItem $item, Request $request): RedirectResponse
    {
        $this->authorize('update', $plan);

        $validated = $request->validate([
            'paid_amount' => ['required', 'numeric', 'min:0', 'max:'.(float) $item->amount],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'payment_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $previousPaid = (float) $item->paid_amount;
        $newPaid = (float) $validated['paid_amount'];
        $isFullPayment = $newPaid >= (float) $item->amount && (float) $item->amount > 0;

        $updateData = [
            'paid_amount' => $newPaid,
        ];

        if ($isFullPayment && $previousPaid < (float) $item->amount) {
            $updateData['paid_at'] = now();
            $updateData['paid_by'] = auth()->user()?->name ?? 'System';
        } elseif ($newPaid === 0) {
            $updateData['paid_at'] = null;
            $updateData['paid_by'] = null;
        }

        if (! empty($validated['payment_method'])) {
            $updateData['payment_method'] = $validated['payment_method'];
        }
        if (! empty($validated['payment_notes'])) {
            $updateData['payment_notes'] = $validated['payment_notes'];
        }

        $item->update($updateData);

        $plan->load('customer');
        $clientName = $plan->customer?->name ?? __('Customer');
        $installmentLabel = $item->installment_number ?? $item->id;
        $statusText = $isFullPayment ? '✅ سداد كامل' : '💰 دفعة جزئية';

        app(PushNotificationService::class)->notifyCrmEvent(
            $statusText,
            $clientName.' — '.number_format($newPaid).' EGP (قسط #'.$installmentLabel.')',
            '/real-statement-control/crm/plans/'.$plan->id
        );

        return back()->with('status', __('Installment payment updated successfully.'));
    }

    public function fullPay(InstallmentPlan $plan, InstallmentPlanItem $item): RedirectResponse
    {
        $this->authorize('update', $plan);

        $remaining = max(0, (float) $item->amount - (float) $item->paid_amount);

        $item->update([
            'paid_amount' => $item->amount,
            'paid_at' => now(),
            'paid_by' => auth()->user()?->name ?? 'System',
            'payment_method' => $item->payment_method ?? 'cash',
        ]);

        $plan->load('customer');
        $clientName = $plan->customer?->name ?? __('Customer');

        app(PushNotificationService::class)->notifyCrmEvent(
            '✅ سداد كامل للقسط',
            $clientName.' — '.number_format($remaining).' EGP (قسط #'.($item->installment_number ?? $item->id).')',
            '/real-statement-control/crm/plans/'.$plan->id
        );

        return back()->with('status', __('Installment fully paid successfully.'));
    }

    public function fullPayAll(InstallmentPlan $plan): RedirectResponse
    {
        $this->authorize('update', $plan);

        $items = $plan->items()->where('paid_amount', '<', DB::raw('amount'))->get();

        foreach ($items as $item) {
            $item->update([
                'paid_amount' => $item->amount,
                'paid_at' => now(),
                'paid_by' => auth()->user()?->name ?? 'System',
            ]);
        }

        $plan->load('customer');
        $clientName = $plan->customer?->name ?? __('Customer');

        app(PushNotificationService::class)->notifyCrmEvent(
            '✅ سداد جميع الأقساط',
            $clientName.' — '.$items->count().' قسط (إجمالي: '.number_format((float) $plan->remaining_amount).')',
            '/real-statement-control/crm/plans/'.$plan->id
        );

        return back()->with('status', __('All installments paid successfully.'));
    }

    public function receipt(InstallmentPlan $plan, InstallmentPlanItem $item): Response
    {
        $plan->load(['customer', 'unit.project', 'unit.building', 'unit.floor', 'creator']);

        $company = CompanyProfile::query()->first();
        $isArabic = app()->getLocale() === 'ar';

        $html = view('crm.plans.receipt', [
            'plan' => $plan,
            'item' => $item,
            'company' => $company,
            'logoDataUri' => $this->imageDataUri($company?->logo_dark_path ?? $company?->logo_path),
            'isArabic' => $isArabic,
        ])->render();

        $defaultConfig = (new ConfigVariables)->getDefaults();
        $defaultFontConfig = (new FontVariables)->getDefaults();

        $tempDir = storage_path('mpdf-temp');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => [120, 200],
            'orientation' => 'P',
            'tempDir' => $tempDir,
            'fontDir' => array_merge($defaultConfig['fontDir'], [public_path('fonts')]),
            'fontdata' => $defaultFontConfig['fontdata'] + [
                'tajawal' => [
                    'R' => 'Tajawal-Regular.ttf',
                    'B' => 'Tajawal-Bold.ttf',
                ],
            ],
            'default_font' => 'tajawal',
            'default_font_size' => 10,
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
        ]);

        $mpdf->SetDirectionality($isArabic ? 'rtl' : 'ltr');
        $mpdf->WriteHTML($html);

        $filename = 'receipt-plan-'.$plan->id.'-item-'.$item->id.'-'.now()->format('Ymd-His').'.pdf';
        $content = $mpdf->Output('', 'S');

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function destroy(InstallmentPlan $plan): RedirectResponse
    {
        $this->authorize('delete', $plan);

        $this->logPlanAction($plan, 'installment_plan.deleted', ['deleted_at' => (string) now()]);

        $plan->delete();

        return redirect()->route('dashboard.crm.plans.index')->with('status', __('Installment plan moved to trash.'));
    }

    public function trash(): View
    {
        $trashedPlans = InstallmentPlan::onlyTrashed()
            ->with(['customer', 'unit.project', 'offer'])
            ->withCount('items')
            ->orderByDesc('deleted_at')
            ->paginate(15);

        return view('crm.plans.trash', [
            'plans' => $trashedPlans,
            'trashedCount' => InstallmentPlan::onlyTrashed()->count(),
            'restorableValue' => InstallmentPlan::onlyTrashed()->sum('final_price'),
        ]);
    }

    public function restore(InstallmentPlan $plan): RedirectResponse
    {
        $this->authorize('delete', $plan);

        $this->logPlanAction($plan, 'installment_plan.restored', ['deleted_at' => null]);

        $plan->restore();

        return back()->with('status', __('Installment plan restored successfully.'));
    }

    public function forceDelete(InstallmentPlan $plan): RedirectResponse
    {
        $this->authorize('delete', $plan);

        $this->logPlanAction($plan, 'installment_plan.force_deleted');

        Offer::query()->where('installment_plan_id', $plan->id)->update(['installment_plan_id' => null]);
        $plan->forceDelete();

        return back()->with('status', __('Installment plan permanently deleted.'));
    }

    public function pdf(InstallmentPlan $plan): Response
    {
        $schedule = $plan->schedule_json ?? [];
        $finalPrice = (float) $plan->final_price;
        $downPayment = (float) $plan->down_payment;
        $isCash = count($schedule) === 0;

        $result = [
            'is_cash' => $isCash,
            'base_price' => $plan->base_price,
            'excellence_percent' => 0,
            'excellence_amount' => 0,
            'base_price_with_excellence' => (float) $plan->base_price,
            'discount_amount' => $plan->discount_amount,
            'final_price' => $finalPrice,
            'maintenance_percent' => $finalPrice > 0 ? round((float) $plan->maintenance_deposit / $finalPrice * 100, 2) : 0,
            'maintenance_deposit' => $plan->maintenance_deposit,
            'remaining' => $plan->remaining_amount,
            'installment_amount' => $plan->installment_amount,
            'down_payment' => $downPayment,
            'schedule' => $schedule,
        ];

        $input = [
            'down_payment_percent' => $finalPrice > 0 ? round($downPayment / $finalPrice * 100, 1) : 0,
            'customer_id' => $plan->customer_id,
            'offer_id' => $plan->offer_id,
        ];

        $company = CompanyProfile::query()->first();
        $unit = $plan->unit_id ? Unit::query()->with(['project', 'building', 'floor'])->find($plan->unit_id) : null;
        $customer = $plan->customer_id ? Customer::query()->find($plan->customer_id) : null;
        $offer = $plan->offer_id ? Offer::query()->find($plan->offer_id) : null;

        $html = view('installments.pdf', [
            'input' => $input,
            'result' => $result,
            'company' => $company,
            'logoDataUri' => $this->imageDataUri($company?->logo_dark_path ?? $company?->logo_path),
            'stampDataUri' => $this->imageDataUri($company?->stamp_path),
            'unit' => $unit,
            'customer' => $customer,
            'offer' => $offer,
        ])->render();

        $defaultConfig = (new ConfigVariables)->getDefaults();
        $defaultFontConfig = (new FontVariables)->getDefaults();

        $isArabic = app()->getLocale() === 'ar';
        $tempDir = storage_path('mpdf-temp');

        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'tempDir' => $tempDir,
            'fontDir' => array_merge($defaultConfig['fontDir'], [public_path('fonts')]),
            'fontdata' => $defaultFontConfig['fontdata'] + [
                'tajawal' => [
                    'R' => 'Tajawal-Regular.ttf',
                    'B' => 'Tajawal-Bold.ttf',
                ],
            ],
            'default_font' => 'tajawal',
            'default_font_size' => 12,
            'margin_left' => 20,
            'margin_right' => 20,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
        ]);

        $mpdf->SetDirectionality($isArabic ? 'rtl' : 'ltr');
        $mpdf->SetFooter($isArabic ? '{PAGENO} / {nb}' : '{PAGENO} / {nb}');
        $mpdf->WriteHTML($html);

        $filename = 'installment-plan-'.$plan->id.'-'.now()->format('Ymd-His').'.pdf';
        $content = $mpdf->Output('', 'S');

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function logPlanAction(InstallmentPlan $plan, string $event, array $newValues = []): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'auditable_type' => $plan::class,
            'auditable_id' => $plan->id,
            'event' => $event,
            'old_values' => [
                'name' => $plan->name,
                'status' => $plan->status,
                'final_price' => $plan->final_price,
                'installment_count' => $plan->installment_count,
                'deleted_at' => $plan->deleted_at?->toDateTimeString(),
            ],
            'new_values' => $newValues,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'properties' => [
                'plan_name' => $plan->name,
                'customer_name' => $plan->customer?->name,
                'customer_id' => $plan->customer_id,
                'unit_number' => $plan->unit?->unit_number,
                'final_price' => $plan->final_price,
                'currency_code' => $plan->currency_code,
            ],
        ]);
    }

    private function imageDataUri(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        $path = parse_url($url, PHP_URL_PATH);
        $file = $path ? public_path(ltrim($path, '/')) : null;

        if ($file !== null && is_file($file) && is_readable($file)) {
            $mime = function_exists('mime_content_type') ? mime_content_type($file) : 'image/png';

            return 'data:'.($mime ?: 'image/png').';base64,'.base64_encode((string) file_get_contents($file));
        }

        return null;
    }
}
