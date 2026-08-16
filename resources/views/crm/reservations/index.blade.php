@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    @include('crm.partials.crm-nav')
    <section class="dashboard-hero-card p-6 sm:p-8">
        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-4">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('Reservations') }}</h1>
                    <p class="mt-2 text-sm text-slate-300">{{ __('Track unit reservations from leads and customers.') }}</p>
                </div>
            </div>
            <a href="{{ route('dashboard.crm.reservations.create') }}" class="app-button">{{ __('+ New Reservation') }}</a>
        </div>
    </section>

    <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-lg">
        <div class="p-4">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($reservations as $reservation)
                    <a href="{{ route('dashboard.crm.reservations.show', $reservation) }}" class="app-card app-card--gradient p-4 block transition hover:border-brand-500/30">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="truncate text-lg font-semibold text-white">{{ $reservation->reservation_number }}</h3>
                                <p class="text-sm text-slate-400">{{ $reservation->customer?->name ?? $reservation->lead?->name ?? '—' }}</p>
                            </div>
                            @include('crm.partials.status-badge', ['status' => $reservation->status, 'label' => __(ucfirst($reservation->status))])
                        </div>
                        <div class="mt-3 text-sm text-slate-300">
                            <span class="block text-xs text-slate-500">{{ __('Deposit') }}</span>
                            <span class="font-semibold text-white">{{ 'EGP ' . number_format((float) $reservation->deposit_amount) }}</span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full rounded-2xl border border-white/10 bg-white/5 p-8 text-center text-slate-400">
                        <p>{{ __('No reservations found.') }}</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $reservations->links() }}
            </div>
        </div>
    </section>
</div>
@endsection
