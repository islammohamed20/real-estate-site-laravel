<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\HomeSection;
use App\Models\InstallmentTemplate;
use App\Models\Project;
use App\Models\Unit;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use App\Repositories\Interfaces\UnitRepositoryInterface;
use App\Support\Features;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicWebsiteController extends Controller
{
    public function home(ProjectRepositoryInterface $projects): View
    {
        $banners = Banner::query()->active()->orderBy('sort_order')->orderBy('id')->get();

        $homeSections = HomeSection::query()->orderBy('sort_order')->orderBy('id')->get()->keyBy('key');
        $featuredUnits = Unit::query()
            ->where('hidden_from_website', false)
            ->where('status', 'available')
            ->with(['project', 'phase', 'building', 'floor'])
            ->latest()
            ->get();
        $featuredUnitsByProject = $featuredUnits
            ->groupBy('project_id')
            ->map(fn ($units) => $units->take(6))
            ->filter(fn ($units) => $units->isNotEmpty())
            ->values();

        return view('public.home', [
            'homeSections' => $homeSections,
            'hero' => $homeSections['hero'] ?? null,
            'pillars' => $homeSections['pillars'] ?? null,
            'projectsSection' => $homeSections['projects'] ?? null,
            'unitsSection' => $homeSections['units'] ?? null,
            'calculator' => $homeSections['calculator'] ?? null,
            'banners' => $banners,
            'projects' => $projects->all()->take(6),
            'featuredUnits' => $featuredUnits,
            'featuredUnitsByProject' => $featuredUnitsByProject,
            'projectCount' => Project::query()->count(),
            'unitCount' => Unit::query()->publiclyVisible()->count(),
            'availableUnitCount' => Unit::query()
                ->where('hidden_from_website', false)
                ->where('status', 'available')
                ->count(),
            'availableFeatures' => Features::list(),
            'defaultDownPaymentPercent' => $this->defaultDownPaymentPercent(),
        ]);
    }

    public function projects(Request $request, UnitRepositoryInterface $units, ProjectRepositoryInterface $projects): View
    {
        $currentProject = $request->string('project')->trim()->toString();
        $currentBuilding = $request->string('building')->trim()->toString();
        $currentFloor = $request->string('floor')->trim()->toString();

        $filters = array_filter([
            'search' => $request->string('q')->trim()->toString(),
            'unit_type' => $request->string('type')->trim()->toString(),
            'bedrooms' => $request->string('rooms')->trim()->toString(),
            'project_id' => $currentProject,
            'building_id' => $currentBuilding,
            'floor_id' => $currentFloor,
            'website_visible' => true,
        ]);

        // Projects with the counts needed for the selection cards.
        $allProjects = Project::query()
            ->where('status', 'active')
            ->withCount([
                'buildings',
                'units' => fn ($q) => $q->publiclyVisible(),
                'units as available_units_count' => fn ($q) => $q
                    ->where('hidden_from_website', false)
                    ->where('status', 'available'),
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Buildings — filtered by selected project if any
        $buildingsQuery = \App\Models\Building::query()
            ->where('hidden_from_website', false)
            ->whereHas('project', fn ($q) => $q->where('status', 'active'))
            ->with(['project:id,name,slug'])
            ->orderBy('sort_order')
            ->orderBy('name');
        if ($currentProject !== '') {
            $buildingsQuery
                ->where('project_id', (int) $currentProject)
                ->whereHas('units', fn ($q) => $q->publiclyVisible());
        }
        $buildings = $buildingsQuery
            ->withCount([
                'floors',
                'units' => fn ($q) => $q->publiclyVisible(),
                'units as available_units_count' => fn ($q) => $q
                    ->where('hidden_from_website', false)
                    ->where('status', 'available'),
            ])
            ->get(['id', 'name', 'code', 'project_id', 'sort_order']);

        // Floors — only loaded when a building is selected
        $floors = collect();
        if ($currentBuilding !== '') {
            $floors = \App\Models\Floor::query()
                ->where('building_id', (int) $currentBuilding)
                ->whereHas('units', fn ($query) => $query->publiclyVisible())
                ->withCount(['units' => fn ($query) => $query->publiclyVisible()])
                ->orderBy('number')
                ->get();
        }

        // Selected models for breadcrumb display
        $selectedProject = $currentProject !== '' ? $allProjects->firstWhere('id', (int) $currentProject) : null;
        $selectedBuilding = $currentBuilding !== '' ? $buildings->firstWhere('id', (int) $currentBuilding) : null;
        $selectedFloor = $currentFloor !== '' ? $floors->firstWhere('id', (int) $currentFloor) : null;

        $unitTypes = \App\Models\Unit::query()
            ->publiclyVisible()
            ->whereNotNull('unit_type')
            ->where('unit_type', '!=', '')
            ->distinct()
            ->orderBy('unit_type')
            ->pluck('unit_type')
            ->values()
            ->all();

        return view('public.projects.index', [
            'units' => $units->paginate(12, $filters),
            'projects' => $allProjects,
            'buildings' => $buildings,
            'floors' => $floors,
            'unitTypes' => $unitTypes,
            'currentSearch' => $request->string('q')->toString(),
            'currentType' => $request->string('type')->toString(),
            'currentRooms' => $request->string('rooms')->toString(),
            'currentProject' => $currentProject,
            'currentBuilding' => $currentBuilding,
            'currentFloor' => $currentFloor,
            'selectedProject' => $selectedProject,
            'selectedBuilding' => $selectedBuilding,
            'selectedFloor' => $selectedFloor,
            'totalUnitCount' => Unit::query()->publiclyVisible()->count(),
            'defaultDownPaymentPercent' => $this->defaultDownPaymentPercent(),
        ]);
    }

    public function projectShow(string $slug, ProjectRepositoryInterface $projects, UnitRepositoryInterface $units): View
    {
        $project = $projects->findBySlug($slug);

        abort_if($project === null, 404);

        return view('public.projects.show', [
            'project' => $project
                ->load([
                    'phases',
                    'buildings' => fn ($query) => $query
                        ->where('hidden_from_website', false)
                        ->orderBy('sort_order')
                        ->with(['floors' => fn ($floorQuery) => $floorQuery
                            ->orderBy('number')
                            ->with(['units' => fn ($unitQuery) => $unitQuery->publiclyVisible()])]),
                    'units' => fn ($query) => $query->publiclyVisible()->latest(),
                ])
                ->loadCount(['units' => fn ($query) => $query->publiclyVisible()]),
            'featuredUnits' => $units->paginate(6, ['project_id' => $project->id, 'website_visible' => true]),
        ]);
    }

    public function unitShow(Unit $unit): View
    {
        abort_if($unit->hidden_from_website || $unit->status?->value === 'hidden', 404);

        $unit->load(['project', 'phase', 'building', 'floor']);

        $price = (float) $unit->current_price;
        $downPaymentPercent = $this->defaultDownPaymentPercent();
        $floorPlanPath = $unit->floor_plan_path;
        $floorPlanExtension = strtolower(pathinfo((string) $floorPlanPath, PATHINFO_EXTENSION));
        $floorPlanIsImage = $floorPlanPath && in_array($floorPlanExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'avif'], true);
        $floorPlanUrl = $floorPlanPath
            ? (filter_var($floorPlanPath, FILTER_VALIDATE_URL)
                ? $floorPlanPath
                : asset('storage/'.ltrim((string) preg_replace('#^/?storage/#', '', $floorPlanPath), '/')))
            : null;
        $downPaymentAmount = $price * ($downPaymentPercent / 100);
        $remainingAmount = $price - $downPaymentAmount;
        $installmentYears = max(1, (int) ($unit->project?->max_installment_years ?? 5));
        $quarterlyCount = $installmentYears * 4;
        $monthlyCount = $installmentYears * 12;
        $quarterlyInstallment = $remainingAmount > 0 ? ($remainingAmount / $quarterlyCount) : 0;
        $monthlyInstallment = $remainingAmount > 0 ? ($remainingAmount / $monthlyCount) : 0;
        $cashDiscountPercent = 27;
        $cashDiscountAmount = $price * ($cashDiscountPercent / 100);
        $cashPrice = $price - $cashDiscountAmount;

        return view('public.units.show', [
            'unit' => $unit,
            'floorPlanUrl' => $floorPlanUrl,
            'floorPlanIsImage' => $floorPlanIsImage,
            'floorPlanExtension' => $floorPlanExtension,
            'installmentYears' => $installmentYears,
            'availableFeatures' => Features::list(),
            'downPaymentPercent' => $downPaymentPercent,
            'downPaymentAmount' => $downPaymentAmount,
            'remainingAmount' => $remainingAmount,
            'quarterlyCount' => $quarterlyCount,
            'monthlyCount' => $monthlyCount,
            'quarterlyInstallment' => $quarterlyInstallment,
            'monthlyInstallment' => $monthlyInstallment,
            'cashDiscountPercent' => $cashDiscountPercent,
            'cashDiscountAmount' => $cashDiscountAmount,
            'cashPrice' => $cashPrice,
        ]);
    }

    public function portfolio(): View
    {
        return view('public.portfolio', [
            'projectCount' => Project::query()->count(),
            'unitCount' => Unit::query()->publiclyVisible()->count(),
            'availableUnitCount' => Unit::query()
                ->where('hidden_from_website', false)
                ->where('status', 'available')
                ->count(),
        ]);
    }

    public function portfolioPdf(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $path = public_path('documents/venecia-track-record.pdf');
        if (! file_exists($path)) {
            $path = base_path('بيان بسابقة أعمال الشركة موسع.pdf');
        }

        abort_if(! file_exists($path), 404);

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="venecia-track-record.pdf"',
        ]);
    }

    public function about(): View
    {
        return view('public.about', [
            'projectCount' => Project::query()->count(),
            'unitCount' => Unit::query()->publiclyVisible()->count(),
        ]);
    }

    private function defaultDownPaymentPercent(): float
    {
        return (float) (InstallmentTemplate::query()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->value('down_payment_percent') ?? 25);
    }

    public function contact(): View
    {
        return view('public.contact');
    }
}
