<?php

declare(strict_types=1);

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\ReservationRequest;
use App\Models\Customer;
use App\Services\PushNotificationService;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Reservation;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Reservation::class);

        $reservations = Reservation::query()
            ->with(['customer', 'lead', 'unit.project', 'sales'])
            ->when($request->filled('search'), function (Builder $q, $search) {
                $q->where('reservation_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn (Builder $c) => $c->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('lead', fn (Builder $l) => $l->where('name', 'like', "%{$search}%"));
            })
            ->when($request->filled('status'), fn (Builder $q, $status) => $q->where('status', $status))
            ->when($request->filled('assigned'), fn (Builder $q, $user) => $q->where('sales_id', $user))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('crm.reservations.index', [
            'reservations' => $reservations,
            'users' => User::active()->pluck('name', 'id'),
            'filters' => $request->only(['search', 'status', 'assigned']),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Reservation::class);

        return view('crm.reservations.form', [
            'reservation' => null,
            'customers' => Customer::query()->orderBy('name')->pluck('name', 'id'),
            'leads' => Lead::query()->orderBy('name')->pluck('name', 'id'),
            'projects' => Project::query()->orderBy('name')->pluck('name', 'id'),
            'units' => Unit::query()->with('project')->orderBy('unit_number')->get()->mapWithKeys(fn ($u) => [$u->id => ($u->project?->name ?? __('Unit')).' #'.$u->unit_number]),
        ]);
    }

    public function store(ReservationRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['reservation_number'] = 'RES-'.now()->format('Ymd').'-'.strtoupper(uniqid());
        $data['sales_id'] = auth()->id();
        $data['reserved_at'] = $data['reserved_at'] ?? now();

        $reservation = Reservation::query()->create($data);

        // Push notification: new reservation
        app(PushNotificationService::class)->notifyCrmEvent(
            '🏠 حجز جديد',
            ($reservation->customer?->name ?? $reservation->lead?->name ?? __('Customer'))
                . ' — ' . ($reservation->unit?->unit_number ?? '')
                . ' · ' . ($reservation->unit?->project?->name ?? ''),
            '/real-statement-control/crm/reservations/' . $reservation->id
        );

        return redirect()->route('dashboard.crm.reservations.show', $reservation)
            ->with('status', __('Reservation created successfully.'));
    }

    public function show(Reservation $reservation): View
    {
        $this->authorize('view', $reservation);

        $reservation->load(['customer', 'lead', 'unit.project', 'sales']);

        return view('crm.reservations.show', [
            'reservation' => $reservation,
        ]);
    }

    public function edit(Reservation $reservation): View
    {
        $this->authorize('update', $reservation);

        $reservation->load(['customer', 'lead', 'unit.project']);

        return view('crm.reservations.form', [
            'reservation' => $reservation,
            'customers' => Customer::query()->orderBy('name')->pluck('name', 'id'),
            'leads' => Lead::query()->orderBy('name')->pluck('name', 'id'),
            'projects' => Project::query()->orderBy('name')->pluck('name', 'id'),
            'units' => Unit::query()->with('project')->orderBy('unit_number')->get()->mapWithKeys(fn ($u) => [$u->id => ($u->project?->name ?? __('Unit')).' #'.$u->unit_number]),
        ]);
    }

    public function update(ReservationRequest $request, Reservation $reservation): RedirectResponse
    {
        $reservation->update($request->validated());

        return redirect()->route('dashboard.crm.reservations.show', $reservation)
            ->with('status', __('Reservation updated successfully.'));
    }

    public function destroy(Reservation $reservation): RedirectResponse
    {
        $this->authorize('delete', $reservation);

        $reservation->delete();

        return redirect()->route('dashboard.crm.reservations.index')
            ->with('status', __('Reservation deleted successfully.'));
    }
}
