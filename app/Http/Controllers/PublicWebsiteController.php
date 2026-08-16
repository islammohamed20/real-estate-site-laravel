<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Banner;
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

        return view('public.home', [
            'banners' => $banners,
            'projects' => $projects->all()->take(6),
            'featuredUnits' => Unit::query()
                ->where('hidden_from_website', false)
                ->with(['project', 'phase', 'building'])
                ->latest()
                ->take(6)
                ->get(),
            'projectCount' => Project::query()->count(),
            'unitCount' => Unit::query()->where('hidden_from_website', false)->count(),
            'availableUnitCount' => Unit::query()
                ->where('hidden_from_website', false)
                ->where('status', 'available')
                ->count(),
            'availableFeatures' => Features::list(),
        ]);
    }

    public function projects(Request $request, UnitRepositoryInterface $units, ProjectRepositoryInterface $projects): View
    {
        $filters = array_filter([
            'search' => $request->string('q')->trim()->toString(),
            'unit_type' => $request->string('type')->trim()->toString(),
            'bedrooms' => $request->string('rooms')->trim()->toString(),
        ]);

        return view('public.projects.index', [
            'units' => $units->paginate(12, $filters),
            'projects' => $projects->all(),
            'currentSearch' => $request->string('q')->toString(),
            'currentType' => $request->string('type')->toString(),
            'currentRooms' => $request->string('rooms')->toString(),
            'totalUnitCount' => Unit::query()->where('hidden_from_website', false)->count(),
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
                        ->orderBy('sort_order')
                        ->with(['floors' => fn ($floorQuery) => $floorQuery->orderBy('number')->with(['units' => fn ($unitQuery) => $unitQuery->where('hidden_from_website', false)])]),
                    'units' => fn ($query) => $query->where('hidden_from_website', false)->latest(),
                ])
                ->loadCount('units'),
            'featuredUnits' => $units->paginate(6, ['project_id' => $project->id]),
        ]);
    }

    public function unitShow(string $unitNumber, UnitRepositoryInterface $units): View
    {
        $unit = $units->findByNumber($unitNumber);

        abort_if($unit === null || $unit->hidden_from_website, 404);

        $price = (float) $unit->current_price;
        $downPaymentPercent = 10;
        $downPaymentAmount = $price * ($downPaymentPercent / 100);
        $remainingAmount = $price - $downPaymentAmount;
        $installmentYears = 8;
        $quarterlyCount = $installmentYears * 4;
        $monthlyCount = $installmentYears * 12;
        $quarterlyInstallment = $remainingAmount > 0 ? ($remainingAmount / $quarterlyCount) : 0;
        $monthlyInstallment = $remainingAmount > 0 ? ($remainingAmount / $monthlyCount) : 0;
        $cashDiscountPercent = 15;
        $cashDiscountAmount = $price * ($cashDiscountPercent / 100);
        $cashPrice = $price - $cashDiscountAmount;

        return view('public.units.show', [
            'unit' => $unit->load(['project', 'phase', 'building', 'floor']),
            'availableFeatures' => Features::list(),
            'downPaymentPercent' => $downPaymentPercent,
            'downPaymentAmount' => $downPaymentAmount,
            'remainingAmount' => $remainingAmount,
            'quarterlyInstallment' => $quarterlyInstallment,
            'monthlyInstallment' => $monthlyInstallment,
            'cashDiscountPercent' => $cashDiscountPercent,
            'cashDiscountAmount' => $cashDiscountAmount,
            'cashPrice' => $cashPrice,
        ]);
    }

    public function about(): View
    {
        return view('public.about', [
            'projectCount' => Project::query()->count(),
            'unitCount' => Unit::query()->where('hidden_from_website', false)->count(),
        ]);
    }

    public function contact(): View
    {
        return view('public.contact');
    }
}
