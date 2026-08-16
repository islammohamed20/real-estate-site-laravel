<?php

declare(strict_types=1);

namespace App\Http\Controllers\Crm;

use App\Events\OfferCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\OfferRequest;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Offer;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use App\Notifications\CrmActivityNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OfferController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Offer::class);

        $offers = Offer::query()
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

        return view('crm.offers.form', [
            'offer' => null,
            'customers' => Customer::query()->orderBy('name')->pluck('name', 'id'),
            'leads' => Lead::query()->orderBy('name')->pluck('name', 'id'),
            'projects' => Project::query()->orderBy('name')->pluck('name', 'id'),
            'units' => Unit::query()->with('project')->orderBy('unit_number')->get()->mapWithKeys(fn ($u) => [$u->id => ($u->project?->name ?? __('Unit')).' #'.$u->unit_number]),
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

        CrmActivityNotification::notifyManagers(new CrmActivityNotification(
            'offer',
            [
                'offer_number' => $offer->offer_number,
                'customer_name' => $offer->customer?->name ?? __('Unknown customer'),
                'amount' => $offer->total_amount,
                'action_url' => route('dashboard.crm.offers.show', $offer),
            ],
            auth()->user()?->name,
        ));

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

        return view('crm.offers.form', [
            'offer' => $offer,
            'customers' => Customer::query()->orderBy('name')->pluck('name', 'id'),
            'leads' => Lead::query()->orderBy('name')->pluck('name', 'id'),
            'projects' => Project::query()->orderBy('name')->pluck('name', 'id'),
            'units' => Unit::query()->with('project')->orderBy('unit_number')->get()->mapWithKeys(fn ($u) => [$u->id => ($u->project?->name ?? __('Unit')).' #'.$u->unit_number]),
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
}
