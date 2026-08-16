@extends('layouts.dashboard')

@section('content')
    @php
        $overview = $stats ?? [];
        $projects = collect($chartData['projects'] ?? []);
        $reservations = collect($chartData['reservations'] ?? []);
        $totalUnits = (int) ($overview['units'] ?? 0);
        $occupiedUnits = (int) (($overview['reserved_units'] ?? 0) + ($overview['sold_units'] ?? 0));
        $occupancyRate = $totalUnits > 0 ? (int) round($occupiedUnits / $totalUnits * 100) : 0;
        $projectPeak = max(1, (int) ($projects->max(fn ($project) => (int) ($project->units_count ?? 0)) ?? 0));
        $reservationPeak = max(1, (int) ($reservations->max(fn ($reservation) => (int) ($reservation->total ?? 0)) ?? 0));
        $todayTouches = (int) ($overview['today_leads'] ?? 0) + (int) ($overview['today_offers'] ?? 0);
        $availablePct = $totalUnits > 0 ? (int) round(($overview['available_units'] ?? 0) / $totalUnits * 100) : 0;
    @endphp

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-12 xl:gap-5">
        {{-- ══ Header ══ --}}
        <section class="dashboard-hero-card xl:col-span-12 p-5 sm:p-7">
            <div class="absolute -right-16 -top-16 h-48 w-48 rounded-full bg-brand-500/20 blur-3xl"></div>
            <div class="absolute -bottom-16 left-1/3 h-48 w-48 rounded-full bg-violet-500/10 blur-3xl"></div>

            <div class="relative flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="badge badge-success px-2 py-0.5 text-[10px] sm:text-xs">● {{ __('System Online') }}</span>
                        <span class="badge badge-brand px-2 py-0.5 text-[10px] sm:text-xs">{{ __('Management hub') }}</span>
                        <span class="badge badge-muted px-2 py-0.5 text-[10px] sm:text-xs">{{ number_format($totalUnits) }} {{ __('units') }}</span>
                    </div>

                    <div>
                        <p class="mobile-section-title">{{ __('Welcome back') }}</p>
                        <h1 class="mt-2 text-2xl font-bold tracking-tight text-white sm:text-3xl text-balance">
                            {{ __('Venecia Developments Dashboard') }}
                        </h1>
                        <p class="mt-2 max-w-2xl text-xs leading-relaxed text-slate-400 sm:text-sm">
                            {{ __('Track portfolio health, lead flow, and project activity from one polished control center.') }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('dashboard.projects.index') }}" class="app-button !min-h-10 text-xs sm:text-sm">{{ __('Open Projects') }}</a>
                    @can('view reports')
                        <a href="{{ route('dashboard.reports.index') }}" class="app-button--ghost !min-h-10 text-xs sm:text-sm">{{ __('Reports') }}</a>
                    @endcan
                </div>
            </div>
        </section>

        {{-- ══ Bento row 1 — Primary KPIs ══ --}}
        <div class="stagger-item app-card app-card--gradient !p-4 group relative overflow-hidden md:col-span-1 xl:col-span-3" style="animation-delay:60ms">
            <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-blue-500/10 blur-2xl transition-all duration-500 group-hover:bg-blue-500/25 group-hover:scale-125"></div>
            <div class="relative flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500/25 to-sky-500/10 text-blue-300 ring-1 ring-blue-500/20">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 21h18M3 7v1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7M4 21V4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v17" stroke-width="1.8"/></svg>
                </span>
                <div class="min-w-0">
                    <p class="text-xl font-bold leading-tight tabular-nums text-white">{{ number_format($overview['projects'] ?? 0) }}</p>
                    <p class="truncate text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ __('Projects') }}</p>
                </div>
            </div>
        </div>

        <div class="stagger-item app-card app-card--gradient !p-4 group relative overflow-hidden md:col-span-1 xl:col-span-3" style="animation-delay:120ms">
            <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-violet-500/10 blur-2xl transition-all duration-500 group-hover:bg-violet-500/25 group-hover:scale-125"></div>
            <div class="relative flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500/25 to-fuchsia-500/10 text-violet-300 ring-1 ring-violet-500/20">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 21h18M6 21V7a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v14M9 21v-4a3 3 0 0 1 3-3v0a3 3 0 0 1 3 3v4M9 10h.01M15 10h.01" stroke-width="1.8" stroke-linecap="round"/></svg>
                </span>
                <div class="min-w-0">
                    <p class="text-xl font-bold leading-tight tabular-nums text-white">{{ number_format($totalUnits) }}</p>
                    <p class="truncate text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ __('Total Units') }}</p>
                </div>
            </div>
        </div>

        <div class="stagger-item app-card app-card--gradient !p-4 group relative overflow-hidden md:col-span-1 xl:col-span-3" style="animation-delay:180ms">
            <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-emerald-500/10 blur-2xl transition-all duration-500 group-hover:bg-emerald-500/25 group-hover:scale-125"></div>
            <div class="relative flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500/25 to-teal-500/10 text-emerald-300 ring-1 ring-emerald-500/20">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" stroke-width="1.8" stroke-linecap="round"/><path d="m9 11 3 3L22 4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <div class="min-w-0">
                    <p class="text-xl font-bold leading-tight tabular-nums text-emerald-400">{{ number_format($overview['available_units'] ?? 0) }}</p>
                    <p class="truncate text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ __('Available') }}</p>
                </div>
            </div>
            <div class="relative mt-2.5 h-1.5 w-full overflow-hidden rounded-full bg-emerald-500/10">
                <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-400 transition-all duration-700" style="width: {{ $availablePct }}%"></div>
            </div>
        </div>

        <div class="stagger-item app-card app-card--gradient !p-4 group relative overflow-hidden md:col-span-1 xl:col-span-3" style="animation-delay:240ms">
            <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-amber-500/10 blur-2xl transition-all duration-500 group-hover:bg-amber-500/25 group-hover:scale-125"></div>
            <div class="relative flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500/25 to-orange-500/10 text-amber-300 ring-1 ring-amber-500/20">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="9" stroke-width="1.8"/><path d="M12 7v5l3 3" stroke-width="1.8" stroke-linecap="round"/></svg>
                </span>
                <div class="min-w-0">
                    <p class="text-xl font-bold leading-tight tabular-nums text-white">{{ number_format($todayTouches) }}</p>
                    <p class="truncate text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ __('Today\'s Touchpoints') }}</p>
                </div>
            </div>
            <p class="relative mt-2.5 text-[11px] text-slate-500">{{ ($overview['today_offers'] ?? 0) }} {{ __('offers today') }}</p>
        </div>

        {{-- ══ Bento row 2 — Chart (wide) + Pipeline (narrow) ══ --}}
        <div class="stagger-item app-card app-card--gradient !p-4 space-y-4 md:col-span-2 xl:col-span-8" style="animation-delay:300ms">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="mobile-section-title">{{ __('Real-time performance') }}</p>
                    <h2 class="mt-1.5 text-base font-semibold text-white">{{ __('Lead & Offer Velocity') }}</h2>
                </div>
                <div class="flex gap-2.5">
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase text-blue-400">
                        <span class="h-2 w-2 rounded-full bg-blue-400 shadow-[0_0_6px_rgba(96,165,250,.8)]"></span> {{ __('Leads') }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase text-violet-400">
                        <span class="h-2 w-2 rounded-full bg-violet-400 shadow-[0_0_6px_rgba(167,139,250,.8)]"></span> {{ __('Offers') }}
                    </span>
                </div>
            </div>

            <div class="relative h-[300px] w-full">
                <canvas id="velocityChart"></canvas>
            </div>
        </div>

        <div class="stagger-item app-card app-card--gradient !p-4 space-y-4 md:col-span-2 xl:col-span-4" style="animation-delay:360ms">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="mobile-section-title">{{ __('Sales Pipeline') }}</p>
                    <h2 class="mt-1.5 text-base font-semibold text-white">{{ __('Momentum') }}</h2>
                </div>
                <span class="badge badge-brand">{{ $occupancyRate }}% {{ __('occupied') }}</span>
            </div>

            <div class="grid grid-cols-2 gap-2.5">
                <div class="rounded-xl border border-blue-500/15 bg-blue-500/5 p-3 transition-colors hover:bg-blue-500/10">
                    <p class="text-[10px] font-semibold uppercase tracking-wide text-blue-300">{{ __('Leads') }}</p>
                    <p class="mt-1.5 text-lg font-bold tabular-nums text-white">{{ number_format($overview['today_leads'] ?? 0) }}</p>
                    <p class="mt-0.5 text-[10px] text-slate-500">{{ __('Today') }}</p>
                </div>
                <div class="rounded-xl border border-violet-500/15 bg-violet-500/5 p-3 transition-colors hover:bg-violet-500/10">
                    <p class="text-[10px] font-semibold uppercase tracking-wide text-violet-300">{{ __('Offers') }}</p>
                    <p class="mt-1.5 text-lg font-bold tabular-nums text-white">{{ number_format($overview['today_offers'] ?? 0) }}</p>
                    <p class="mt-0.5 text-[10px] text-slate-500">{{ __('Today') }}</p>
                </div>
                <div class="rounded-xl border border-amber-500/15 bg-amber-500/5 p-3 transition-colors hover:bg-amber-500/10">
                    <p class="text-[10px] font-semibold uppercase tracking-wide text-amber-300">{{ __('Reserved') }}</p>
                    <p class="mt-1.5 text-lg font-bold tabular-nums text-white">{{ number_format($overview['reserved_units'] ?? 0) }}</p>
                    <p class="mt-0.5 text-[10px] text-slate-500">{{ __('Committed') }}</p>
                </div>
                <div class="rounded-xl border border-emerald-500/15 bg-emerald-500/5 p-3 transition-colors hover:bg-emerald-500/10">
                    <p class="text-[10px] font-semibold uppercase tracking-wide text-emerald-300">{{ __('Sold') }}</p>
                    <p class="mt-1.5 text-lg font-bold tabular-nums text-white">{{ number_format($overview['sold_units'] ?? 0) }}</p>
                    <p class="mt-0.5 text-[10px] text-slate-500">{{ __('Closed') }}</p>
                </div>
            </div>

            <div class="rounded-xl border border-white/10 bg-white/5 p-3.5">
                <div class="flex items-center justify-between gap-3 text-[11px] text-slate-400">
                    <span>{{ __('Portfolio conversion') }}</span>
                    <span class="font-semibold text-slate-300">{{ $occupancyRate }}%</span>
                </div>
                <div class="mt-2.5 h-2 w-full overflow-hidden rounded-full bg-white/5">
                    <div class="h-full rounded-full bg-gradient-to-r from-blue-500 via-violet-500 to-emerald-500 transition-all duration-700" style="width: {{ $occupancyRate }}%"></div>
                </div>
            </div>
        </div>

        {{-- ══ Bento row 3 — Recent Projects (wide) + Reservations (narrow) ══ --}}
        <div class="stagger-item app-card app-card--gradient !p-4 space-y-3 md:col-span-2 xl:col-span-7" style="animation-delay:420ms">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="mobile-section-title">{{ __('Portfolio spotlight') }}</p>
                    <h2 class="mt-1.5 text-base font-semibold text-white">{{ __('Recent Projects') }}</h2>
                </div>
                <span class="text-[11px] text-slate-500">{{ count($projects) }} {{ __('loaded') }}</span>
            </div>

            @if($projects->isNotEmpty())
                <div class="space-y-2.5">
                    @foreach($projects->take(6) as $index => $project)
                        @php
                            $projectUnits = (int) ($project->units_count ?? 0);
                            $projectShare = $projectPeak > 0 ? (int) round($projectUnits / $projectPeak * 100) : 0;
                            $status = $project->status ?? 'draft';
                            $statusLabel = match ($status) {
                                'active' => __('Active'),
                                'launching' => __('Launching'),
                                'sold' => __('Sold Out'),
                                default => __('Draft'),
                            };
                            $statusClass = match ($status) {
                                'active' => 'badge-success',
                                'launching' => 'badge-warning',
                                'sold' => 'badge-danger',
                                default => 'badge-muted',
                            };
                        @endphp

                        <div class="rounded-xl border border-white/10 bg-white/5 p-3 transition-all duration-300 hover:border-white/20 hover:bg-white/[0.08]">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex min-w-0 items-center gap-2.5">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-violet-500/25 to-brand-500/15 text-[11px] font-bold text-violet-300 ring-1 ring-violet-500/20">{{ mb_strtoupper(mb_substr($project->name, 0, 1)) }}</span>
                                    <div class="min-w-0">
                                        <h3 class="truncate text-sm font-semibold text-white">
                                            {{ $project->name }}
                                            @if($project->featured)
                                                <span class="badge badge-warning ml-1 !px-1.5 !py-0 text-[9px]">{{ __('Featured') }}</span>
                                            @endif
                                        </h3>
                                        <p class="truncate text-[11px] text-slate-500">{{ $project->location ?? __('Location pending') }}</p>
                                    </div>
                                </div>
                                <div class="flex shrink-0 items-center gap-3">
                                    <span class="text-[11px] font-semibold tabular-nums text-slate-300">{{ number_format($projectUnits) }} <span class="font-normal text-slate-500">{{ __('units') }}</span></span>
                                    <span class="badge {{ $statusClass }} !px-2 !py-0.5 text-[10px]">{{ $statusLabel }}</span>
                                </div>
                            </div>
                            <div class="mt-2.5 h-1.5 w-full overflow-hidden rounded-full bg-white/5">
                                <div class="h-full rounded-full bg-gradient-to-r from-violet-500 via-brand-500 to-emerald-500 transition-all duration-700" style="width: {{ $projectShare }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex min-h-32 items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/5 text-sm text-slate-500">
                    {{ __('No project data available yet.') }}
                </div>
            @endif
        </div>

        <div class="stagger-item app-card app-card--gradient !p-4 space-y-3 md:col-span-2 xl:col-span-5" style="animation-delay:480ms">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="mobile-section-title">{{ __('Reservation trend') }}</p>
                    <h2 class="mt-1.5 text-base font-semibold text-white">{{ __('Recent Activity') }}</h2>
                </div>
                <span class="badge badge-success">{{ __('Last 7 days') }}</span>
            </div>

            @if($reservations->isNotEmpty())
                <div class="space-y-2.5">
                    @foreach($reservations as $reservation)
                        @php
                            $reservationTotal = (int) ($reservation->total ?? 0);
                            $reservationWidth = $reservationPeak > 0 ? (int) round($reservationTotal / $reservationPeak * 100) : 0;
                        @endphp
                        <div class="rounded-xl border border-white/10 bg-white/5 p-3 transition-colors hover:bg-white/[0.08]">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-[13px] font-semibold text-white">
                                    {{ \Illuminate\Support\Carbon::parse($reservation->date)->format('D, d M') }}
                                </p>
                                <span class="text-[11px] font-semibold tabular-nums text-brand-300">{{ number_format($reservationTotal) }} {{ __('reserved') }}</span>
                            </div>
                            <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-white/5">
                                <div class="h-full rounded-full bg-gradient-to-r from-brand-500 to-emerald-500 transition-all duration-700" style="width: {{ $reservationWidth }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex min-h-32 items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/5 text-sm text-slate-500">
                    {{ __('No reservation activity has been recorded yet.') }}
                </div>
            @endif
        </div>

        {{-- ══ Bento row 4 — Trash & Cleanup ══ --}}
        <section class="stagger-item app-card app-card--gradient !p-4 md:col-span-2 xl:col-span-12" style="animation-delay:540ms">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-x-8 gap-y-3">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ __('Plans in trash') }}</p>
                        <div class="mt-1 flex items-baseline gap-2">
                            <p class="text-xl font-bold tabular-nums {{ $trashedPlansCount > 0 ? 'text-amber-400' : 'text-emerald-400' }}">{{ number_format($trashedPlansCount) }}</p>
                            @canany(['view crm dashboard', 'manage crm', 'view reports'])
                                <a href="{{ route('dashboard.crm.plans.trash') }}" class="text-xs font-semibold text-brand-400 hover:text-brand-300">{{ __('View trash') }} →</a>
                            @endcanany
                        </div>
                        <p class="mt-1 text-[11px] text-slate-500">
                            {{ __('Retention: :days days', ['days' => $trashRetentionDays]) }} ·
                            {{ $autoCleanupEnabled ? __('Auto-cleanup on') : __('Auto-cleanup off') }}
                        </p>
                    </div>

                    <div class="min-w-44">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ __('Last automatic cleanup') }}</p>
                        @if($lastAutoCleanup && $lastAutoCleanup['time'])
                            <p class="mt-1 text-sm font-bold text-white">{{ $lastAutoCleanup['time']->format('Y-m-d H:i') }}</p>
                            <p class="mt-0.5 text-[11px] text-slate-500">
                                @if($lastAutoCleanup['deleted_count'] !== null)
                                    {{ __(':count plan(s) permanently deleted', ['count' => $lastAutoCleanup['deleted_count']]) }}
                                @else
                                    {{ __('Nothing to purge') }}
                                @endif
                            </p>
                        @else
                            <p class="mt-1 text-sm font-bold text-slate-300">{{ __('Never run yet') }}</p>
                            <p class="mt-0.5 text-[11px] text-slate-500">{{ __('Runs daily at 03:00') }}</p>
                        @endif
                    </div>
                </div>

                @can('manage crm')
                    <form method="POST" action="{{ route('dashboard.trash-cleanup.run') }}"
                          onsubmit="return confirm('{{ __('Run manual cleanup now? Trashed plans past the retention period will be permanently deleted.') }}')">
                        @csrf
                        <button type="submit" class="app-button !min-h-10 text-xs sm:text-sm">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" stroke-width="1.8"/><path d="M10 11v6M14 11v6" stroke-width="1.8" stroke-linecap="round"/></svg>
                            {{ __('Run manual cleanup') }}
                        </button>
                    </form>
                @endcan
            </div>
        </section>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('velocityChart');
            if (!ctx) return;

            // Axis label color adapts to theme: dark slate on light bg, light slate on dark bg.
            const tickColor = (() => {
                const v = getComputedStyle(document.body).getPropertyValue('--slate-400').trim();
                return v ? `rgb(${v})` : '#64748b';
            })();

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartData['sales']['labels'] ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']) !!},
                    datasets: [
                        {
                            label: '{{ __('Leads') }}',
                            data: [12, 19, 15, 25, 22, 30, 28],
                            borderColor: '#60a5fa',
                            backgroundColor: 'rgba(96, 165, 250, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                        },
                        {
                            label: '{{ __('Offers') }}',
                            data: [5, 12, 8, 15, 10, 20, 18],
                            borderColor: '#a78bfa',
                            backgroundColor: 'rgba(167, 139, 250, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    // No self-drawing animation on mobile — the page must stay perfectly still.
                    animation: window.innerWidth >= 768 ? { duration: 800, easing: 'easeOutQuart' } : false,
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleColor: '#f8fafc',
                            bodyColor: '#f8fafc',
                            borderColor: 'rgba(255,255,255,0.1)',
                            borderWidth: 1,
                            padding: 12,
                            cornerRadius: 12,
                            displayColors: true,
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: tickColor, font: { size: 10, family: 'Cairo' } }
                        },
                        y: {
                            grid: { color: 'rgba(255, 255, 255, 0.05)', drawBorder: false },
                            ticks: { color: tickColor, font: { size: 10 }, padding: 8 }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
@endsection
