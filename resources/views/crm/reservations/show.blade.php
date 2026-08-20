@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    @include('crm.partials.crm-nav')
    <section class="dashboard-hero-card p-6 sm:p-8">
        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="badge badge-brand">{{ __('Reservation') }}</span>
                    @include('crm.partials.status-badge', ['status' => $reservation->status, 'label' => __(ucfirst($reservation->status))])
                </div>
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ $reservation->reservation_number }}</h1>
                    <p class="mt-2 text-sm text-slate-300">{{ $reservation->customer?->name ?? $reservation->lead?->name ?? '—' }}</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('dashboard.crm.reservations.edit', $reservation) }}" class="app-button">{{ __('Edit') }}</a>
                <form action="{{ route('dashboard.crm.reservations.destroy', $reservation) }}" method="POST" onsubmit="return confirm('{{ __('Delete this reservation?') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="app-button app-button--danger">{{ __('Delete') }}</button>
                </form>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-lg p-4">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="app-card app-card--gradient p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Customer') }}</p>
                <p class="mt-1 text-lg font-semibold text-white">{{ $reservation->customer?->name ?? '—' }}</p>
            </div>
            <div class="app-card app-card--gradient p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Lead') }}</p>
                <p class="mt-1 text-lg font-semibold text-white">{{ $reservation->lead?->name ?? '—' }}</p>
            </div>
            <div class="app-card app-card--gradient p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Unit') }}</p>
                <p class="mt-1 text-lg font-semibold text-white">{{ $reservation->unit?->project?->name ?? '—' }} #{{ $reservation->unit?->unit_number ?? '—' }}</p>
            </div>
            <div class="app-card app-card--gradient p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Deposit') }}</p>
                <p class="mt-1 text-lg font-semibold text-white">{{ 'EGP ' . number_format((float) $reservation->deposit_amount) }}</p>
            </div>
            <div class="app-card app-card--gradient p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Reserved at') }}</p>
                <p class="mt-1 text-lg font-semibold text-white">{{ $reservation->reserved_at?->format('Y-m-d H:i') }}</p>
            </div>
            <div class="app-card app-card--gradient p-4" x-data="reservationCountdown('{{ $reservation->expires_at?->toIso8601String() }}')" x-init="start()">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Expires at') }}</p>
                <p class="mt-1 text-lg font-semibold text-white">{{ $reservation->expires_at?->format('Y-m-d H:i') }}</p>
                <p class="mt-1 text-sm font-bold" :class="expired ? 'text-rose-400' : (critical ? 'text-amber-300' : 'text-emerald-300')">
                    <span x-text="label"></span>
                    <template x-if="!expired"><span class="text-slate-400">{{ __('remaining') }}</span></template>
                </p>
            </div>
        </div>

        @if ($reservation->notes)
            <div class="app-card app-card--gradient mt-4 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Notes') }}</p>
                <p class="mt-2 whitespace-pre-line text-sm text-slate-300">{{ $reservation->notes }}</p>
            </div>
        @endif
    </section>
</div>
@endsection

@push('scripts')
    <script>
        function reservationCountdown(expiresAt) {
            return {
                label: '',
                expired: false,
                critical: false,
                timer: null,
                start() {
                    const tick = () => {
                        if (!expiresAt) {
                            this.label = '—';
                            return;
                        }
                        const diff = new Date(expiresAt).getTime() - Date.now();
                        if (diff <= 0) {
                            this.expired = true;
                            this.critical = false;
                            this.label = '{{ __('Expired') }}';
                            return;
                        }
                        const d = Math.floor(diff / 86400000);
                        const h = Math.floor((diff % 86400000) / 3600000);
                        const m = Math.floor((diff % 3600000) / 60000);
                        this.label = (d > 0 ? d + 'd ' : '') + h + 'h ' + m + 'm';
                        this.critical = diff < 86400000;
                    };
                    tick();
                    this.timer = setInterval(tick, 30000);
                }
            };
        }
    </script>
@endpush
