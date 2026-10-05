<?php

declare(strict_types=1);

namespace App\Http\Controllers\Crm;

use App\Events\OfferCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\OfferRequest;
use App\Models\Customer;
use App\Models\InstallmentTemplate;
use App\Models\Lead;
use App\Models\Offer;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use App\Models\Building;
use App\Models\CompanyProfile;
use App\Models\Floor;
use App\Notifications\CrmActivityNotification;
use App\Services\PushNotificationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OfferController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Offer::class);

        $canViewAll = auth()->user()->hasAnyPermission(['view reports', 'manage crm']);

        $offers = Offer::query()
            ->when(! $canViewAll, fn ($q) => $q->where(function ($scope): void {
                $scope->where('sales_id', auth()->id())
                    ->orWhereHas('lead', fn ($lead) => $lead->where('assigned_sales_id', auth()->id()))
                    ->orWhereHas('customer.leads', fn ($lead) => $lead->where('assigned_sales_id', auth()->id()));
            }))
            ->with(['customer', 'lead', 'unit.project', 'sales'])
            ->when($request->filled('search'), function (Builder $q, $search) {
                $q->where('offer_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn (Builder $c) => $c->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('lead', fn (Builder $l) => $l->where('name', 'like', "%{$search}%"));
            })
            ->when($request->filled('status'), fn (Builder $q, $status) => $q->where('status', $status))
            ->when($request->filled('assigned'), fn (Builder $q, $user) => $q->where('sales_id', $user))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('crm.offers.index', [
            'offers' => $offers,
            'users' => User::active()->pluck('name', 'id'),
            'filters' => $request->only(['search', 'status', 'assigned']),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Offer::class);

        [$units, $unitsData, $buildingsData, $floorsData] = $this->buildUnitSelectionData($offer = null);

        return view('crm.offers.form', [
            'offer' => null,
            'customers' => Customer::query()
                ->when(! auth()->user()->hasAnyPermission(['view reports', 'manage crm']), fn ($q) => $q->whereHas('leads', fn ($lead) => $lead->where('assigned_sales_id', auth()->id())))
                ->orderBy('name')->pluck('name', 'id'),
            'leads' => Lead::query()
                ->when(! auth()->user()->hasAnyPermission(['view reports', 'manage crm']), fn ($q) => $q->where('assigned_sales_id', auth()->id()))
                ->orderBy('name')->pluck('name', 'id'),
            'projects' => Project::query()->orderBy('name')->pluck('name', 'id'),
            'units' => $units,
            'unitsData' => $unitsData,
            'buildingsData' => $buildingsData,
            'floorsData' => $floorsData,
            'installmentTemplates' => InstallmentTemplate::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id'),
            'currency' => CompanyProfile::first()?->currency_code ?? 'EGP',
        ]);
    }

    public function store(OfferRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['offer_number'] = 'OFF-'.now()->format('Ymd').'-'.strtoupper(uniqid());
        $data['sales_id'] = auth()->id();
        $data['issue_date'] = $data['issue_date'] ?? now()->format('Y-m-d');
        $data['valid_until'] = $data['valid_until'] ?? now()->addDays(7)->format('Y-m-d');

        $offer = Offer::query()->create($data);

        OfferCreated::dispatch($offer);

        CrmActivityNotification::notifyRelevant(new CrmActivityNotification(
            'offer',
            [
                'offer_number' => $offer->offer_number,
                'customer_name' => $offer->customer?->name ?? __('Unknown customer'),
                'amount' => $offer->total_amount,
                'action_url' => route('dashboard.crm.offers.show', $offer),
            ],
            auth()->user()?->name,
        ), auth()->user());

        app(PushNotificationService::class)->notifyCrmEvent('💨 عرض جديد', $offer->title ?? 'New offer', '/real-statement-control/crm/offers/'.$offer->id);

        return redirect()->route('dashboard.crm.offers.show', $offer)
            ->with('status', __('Offer created successfully.'));
    }

    public function show(Offer $offer): View
    {
        $this->authorize('view', $offer);

        $offer->load(['customer', 'lead', 'unit.project', 'sales']);

        return view('crm.offers.show', [
            'offer' => $offer,
        ]);
    }

    public function edit(Offer $offer): View
    {
        $this->authorize('update', $offer);

        $offer->load(['customer', 'lead', 'unit.project']);

        [$units, $unitsData, $buildingsData, $floorsData] = $this->buildUnitSelectionData($offer);

        return view('crm.offers.form', [
            'offer' => $offer,
            'customers' => Customer::query()
                ->when(! auth()->user()->hasAnyPermission(['view reports', 'manage crm']), fn ($q) => $q->whereHas('leads', fn ($lead) => $lead->where('assigned_sales_id', auth()->id())))
                ->orderBy('name')->pluck('name', 'id'),
            'leads' => Lead::query()
                ->when(! auth()->user()->hasAnyPermission(['view reports', 'manage crm']), fn ($q) => $q->where('assigned_sales_id', auth()->id()))
                ->orderBy('name')->pluck('name', 'id'),
            'projects' => Project::query()->orderBy('name')->pluck('name', 'id'),
            'units' => $units,
            'unitsData' => $unitsData,
            'buildingsData' => $buildingsData,
            'floorsData' => $floorsData,
            'installmentTemplates' => InstallmentTemplate::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id'),
            'currency' => CompanyProfile::first()?->currency_code ?? 'EGP',
        ]);
    }

    public function update(OfferRequest $request, Offer $offer): RedirectResponse
    {
        $offer->update($request->validated());

        return redirect()->route('dashboard.crm.offers.show', $offer)
            ->with('status', __('Offer updated successfully.'));
    }

    public function destroy(Offer $offer): RedirectResponse
    {
        $this->authorize('delete', $offer);

        $offer->delete();

        return redirect()->route('dashboard.crm.offers.index')
            ->with('status', __('Offer moved to trash.'));
    }

    /**
     * Build the unit selection data for the offers form.
     *
     * Only available units are offered for sale by default, but the currently
     * selected unit (when editing) is always included so it stays selectable.
     *
     * @return array{0: \Illuminate\Support\Collection, 1: \Illuminate\Support\Collection, 2: \Illuminate\Support\Collection, 3: \Illuminate\Support\Collection}
     */
    private function buildUnitSelectionData(?Offer $offer): array
    {
        $currentUnitId = $offer?->unit_id;

        $unitsQuery = Unit::query()
            ->with(['project', 'building', 'floor'])
            ->where(function (Builder $q) use ($currentUnitId): void {
                $q->where('status', 'available')
                    ->when($currentUnitId, fn (Builder $sq) => $sq->orWhere('id', $currentUnitId));
            })
            ->orderBy('unit_number')
            ->get();

        $units = $unitsQuery->mapWithKeys(fn ($u) => [$u->id => ($u->project?->name ?? __('Unit')).' #'.$u->unit_number]);

        $unitsData = $unitsQuery->map(fn ($u) => [
            'id' => $u->id,
            'project_id' => $u->project_id,
            'building_id' => $u->building_id,
            'floor_id' => $u->floor_id,
            'label' => ($u->project?->name ?? __('Unit')).' #'.$u->unit_number,
            'unit_number' => $u->unit_number,
            'unit_type' => $u->unit_type,
            'area' => (float) $u->area,
            'bedrooms' => $u->bedrooms,
            'bathrooms' => $u->bathrooms,
            'current_price' => (float) $u->current_price,
            'status' => $u->status?->value ?? $u->status,
        ]);

        $buildingsData = Building::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'project_id', 'name'])
            ->map(fn ($b) => ['id' => $b->id, 'project_id' => $b->project_id, 'name' => $b->name]);

        $floorsData = Floor::query()
            ->orderBy('sort_order')
            ->orderByDesc('number')
            ->get(['id', 'project_id', 'building_id', 'number', 'name'])
            ->map(fn ($f) => [
                'id' => $f->id,
                'project_id' => $f->project_id,
                'building_id' => $f->building_id,
                'number' => $f->number,
                'name' => $f->name ?? (string) $f->number,
            ]);

        return [$units, $unitsData, $buildingsData, $floorsData];
    }
}
