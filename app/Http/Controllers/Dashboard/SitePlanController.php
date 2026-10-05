<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class SitePlanController extends Controller
{
    public static function statusColors(): array
    {
        return [
            'available' => ['bg' => '#22c55e', 'text' => '#ffffff', 'label' => __('Available')],
            'sold'      => ['bg' => '#ef4444', 'text' => '#ffffff', 'label' => __('Sold')],
            'reserved'  => ['bg' => '#f59e0b', 'text' => '#1e293b', 'label' => __('Reserved')],
            'hidden'    => ['bg' => '#6b7280', 'text' => '#ffffff', 'label' => __('Hidden')],
        ];
    }

    /**
     * Shared inventory query. The dashboard can inspect hidden inventory,
     * while exports default to the public website subset.
     */
    private function siteData(int $projectId, bool $visibleOnly = false): \Illuminate\Support\Collection
    {
        $buildings = Building::where('project_id', $projectId)
            ->when($visibleOnly, fn ($query) => $query->where('hidden_from_website', false))
            ->with([
                'floors' => fn ($q) => $q->orderBy('number'),
                'units' => fn ($q) => $q
                    ->when($visibleOnly, fn ($query) => $query->where('hidden_from_website', false))
                    ->orderBy('position_in_grid')
                    ->orderBy('sort_order'),
            ])
            ->orderBy('grid_y')
            ->orderBy('grid_x')
            ->orderBy('sort_order')
            ->get();

        return $this->prepareBuildingData($buildings);
    }

    public function sitePlan(Request $request): View
    {
        $projectId = $request->input('project_id', Project::first()?->id);
        $project = Project::findOrFail($projectId);

        $buildingData = $this->siteData((int) $projectId);

        $projects = Project::whereNull('deleted_at')->orderBy('name')->get();
        $allUnits = $buildingData->flatMap(fn($b) => $b['units_by_floor'])->flatten(1);

        return view('dashboard.site-plan.index', [
            'project'      => $project,
            'projects'     => $projects,
            'buildings'    => $buildingData,
            'statusColors' => self::statusColors(),
            'allUnits'     => $allUnits,
        ]);
    }

    /**
     * Export site plan as PDF (A3 Landscape) — website-visible units only.
     */
    public function exportPdf(Request $request): Response
    {
        $projectId = $request->input('project_id', Project::first()?->id);
        $project = Project::findOrFail($projectId);

        $visibleOnly = $request->boolean('visible_only', true);
        $buildingData = $this->siteData((int) $projectId, $visibleOnly);
        $allUnits = $buildingData->flatMap(fn($b) => $b['units_by_floor'])->flatten(1);

        $html = view('dashboard.site-plan.print', [
            'project'      => $project,
            'buildings'    => $buildingData,
            'statusColors' => self::statusColors(),
            'allUnits'     => $allUnits,
        ])->render();

        $pdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A3-L',
            'margin_left'   => 6,
            'margin_right'  => 6,
            'margin_top'    => 6,
            'margin_bottom' => 6,
            'tempDir' => storage_path('mpdf-temp'),
            'fontDir' => array_merge((new \Mpdf\Config\ConfigVariables)->getDefaults()['fontDir'], [public_path('fonts')]),
            'fontdata' => (new \Mpdf\Config\FontVariables)->getDefaults()['fontdata'] + [
                'tajawal' => [
                    'R' => 'Tajawal-Regular.ttf',
                    'B' => 'Tajawal-Bold.ttf',
                    'useOTL' => 0xFF,
                    'useKashida' => 75,
                ],
            ],
            'default_font' => 'tajawal',
        ]);

        $pdf->WriteHTML($html);
        $filename = 'site-plan-' . $project->name . '-' . now()->format('Y-m-d') . '.pdf';

        // Page-number footer (mPDF tokens work inside SetFooter HTML)
        $pdf->SetFooter(
            '<div dir="rtl" style="text-align:center; direction:rtl; font-size:6.5pt; color:#64748b; font-family:Tajawal;">'
            . e($project->name) . ' — ' . __('خريطة الموقع (الوحدات المعروضة)') . ' — '
            . now()->format('Y/m/d H:i')
            . ' &nbsp;&middot;&nbsp; ' . __('صفحة') . ' {PAGENO} / {nb}'
            . '</div>'
        );

        $contents = $pdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);

        $disposition = $request->boolean('inline') ? 'inline' : 'attachment';

        return response($contents, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $disposition.'; filename="'.rawurlencode($filename).'"',
        ]);
    }

    /**
     * Prepare building data arrays (shared between screen and PDF).
     */
    private function prepareBuildingData($buildings): \Illuminate\Support\Collection
    {
        return $buildings->map(function (Building $building) {
            $floors = $building->floors->sortBy('number');
            $unitsByFloor = [];
            foreach ($floors as $floor) {
                $units = $building->units->filter(fn ($u) => $u->floor_id === $floor->id);
                $unitsByFloor[$floor->id] = $units->map(fn ($u) => [
                    'id'              => $u->id,
                    'unit_number'     => $u->unit_number,
                    'area'            => $u->area,
                    'status'          => is_object($u->status) ? $u->status->value : (string) $u->status,
                    'bedrooms'        => $u->bedrooms,
                    'bathrooms'       => $u->bathrooms,
                    'balcony_area'    => $u->balcony_area,
                    'garden_area'     => $u->garden_area,
                    'unit_type'       => $u->unit_type,
                    'hidden_from_website' => (bool) $u->hidden_from_website,
                    'grid_row'        => $u->grid_row,
                    'grid_col'        => $u->grid_col,
                    'terrace_count'   => $u->terrace_count,
                    'price_per_meter' => $u->price_per_meter,
                    'position_in_grid' => $u->position_in_grid ?? 0,
                ])->values();
            }
            return [
                'id'                  => $building->id,
                'name'                => $building->name,
                'code'                => $building->code,
                'hidden_from_website' => (bool) $building->hidden_from_website,
                'grid_x'              => $building->grid_x ?? 0,
                'grid_y'              => $building->grid_y ?? 0,
                'layout_group'        => $this->layoutGroup((string) $building->code),
                'layout_order'        => $this->layoutOrder((string) $building->code, (int) $building->sort_order),
                'floors'              => $floors->values(),
                'units_by_floor'      => $unitsByFloor,
            ];
        })->filter(function ($b) {
            // Keep only buildings that have at least one unit in the selected dataset.
            return array_sum(array_map(fn ($u) => $u->count(), $b['units_by_floor'])) > 0;
        })->sortBy(['layout_group', 'layout_order'])->values();
    }

    /**
     * Group the Venecia building families so the inventory follows the
     * architectural plan. Unknown codes remain usable in a final group.
     */
    private function layoutGroup(string $code): int
    {
        if (! preg_match('/(\d{3})$/', $code, $matches)) {
            return 90;
        }

        return match ((int) floor(((int) $matches[1]) / 100)) {
            6 => 10,
            1 => 20,
            2 => 30,
            4 => 40,
            default => 90,
        };
    }

    private function layoutOrder(string $code, int $fallback): int
    {
        return preg_match('/(\d{3})$/', $code, $matches)
            ? (int) $matches[1]
            : $fallback;
    }
}
