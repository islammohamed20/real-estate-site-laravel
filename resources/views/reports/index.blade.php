@extends('layouts.dashboard')

@section('content')
    @php
        $totalUnits = array_sum(array_filter($stats, fn($k) => str_contains($k, 'units'), ARRAY_FILTER_USE_KEY) ?: [0]);
        $available = $stats['available_units'] ?? 0;
        $reserved = $stats['reserved_units'] ?? 0;
        $sold = $stats['sold_units'] ?? 0;
        $occupancyRate = $totalUnits > 0 ? round(($sold + $reserved) / $totalUnits * 100) : 0;
        $revenue = $stats['active_reservations'] ?? 0;
    @endphp

    <div class="space-y-6">
        {{-- Header --}}
        <section class="dashboard-hero-card p-6 sm:p-8">
            <div class="absolute -right-16 -top-16 h-48 w-48 rounded-full bg-brand-500/20 blur-3xl"></div>
            <div class="absolute -bottom-16 left-1/3 h-48 w-48 rounded-full bg-violet-500/10 blur-3xl"></div>

            <div class="relative grid gap-6 lg:grid-cols-[minmax(0,1.4fr)_minmax(16rem,0.9fr)] lg:items-end">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="badge badge-success">● {{ __('Live Data') }}</span>
                        <span class="badge badge-brand">{{ __('Analytics') }}</span>
                        <span class="badge badge-muted">{{ number_format($stats['projects'] ?? 0) }} {{ __('projects') }}</span>
                    </div>

                    <div>
                        <p class="mobile-section-title">{{ __('Analytics') }}</p>
                        <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('Reports & Insights') }}</h1>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">{{ __('Real-time performance metrics for your real estate portfolio, with a clean view of occupancy, activity, and portfolio distribution.') }}</p>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 shadow-lg shadow-black/10 backdrop-blur-xl">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Occupancy Rate') }}</p>
                        <p class="mt-2 text-3xl font-bold text-emerald-400">{{ $occupancyRate }}%</p>
                        <p class="mt-1 text-xs text-slate-400">{{ __('Sold + reserved') }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 shadow-lg shadow-black/10 backdrop-blur-xl">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Today\'s Leads') }}</p>
                        <p class="mt-2 text-3xl font-bold text-white">{{ number_format($stats['today_leads'] ?? 0) }}</p>
                        <p class="mt-1 text-xs text-slate-400">{{ __('Captured today') }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 shadow-lg shadow-black/10 backdrop-blur-xl">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Active Reservations') }}</p>
                        <p class="mt-2 text-3xl font-bold text-violet-400">{{ number_format($stats['active_reservations'] ?? 0) }}</p>
                        <p class="mt-1 text-xs text-slate-400">{{ __('Currently in pipeline') }}</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Primary KPIs --}}
        <section class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            <div class="app-card app-card--gradient group relative overflow-hidden">
                <div class="absolute -right-4 -top-4 h-20 w-20 rounded-full bg-blue-500/10 blur-2xl transition-all group-hover:bg-blue-500/20"></div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('Projects') }}</p>
                <p class="mt-3 text-3xl font-bold text-white">{{ number_format($stats['projects'] ?? 0) }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ __('Active developments') }}</p>
            </div>

            <div class="app-card app-card--gradient group relative overflow-hidden">
                <div class="absolute -right-4 -top-4 h-20 w-20 rounded-full bg-violet-500/10 blur-2xl transition-all group-hover:bg-violet-500/20"></div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('Total Units') }}</p>
                <p class="mt-3 text-3xl font-bold text-white">{{ number_format($stats['units'] ?? 0) }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ __('Across all projects') }}</p>
            </div>

            <div class="app-card app-card--gradient group relative overflow-hidden">
                <div class="absolute -right-4 -top-4 h-20 w-20 rounded-full bg-emerald-500/10 blur-2xl transition-all group-hover:bg-emerald-500/20"></div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('Occupancy Rate') }}</p>
                <p class="mt-3 text-3xl font-bold text-emerald-400">{{ $occupancyRate }}%</p>
                <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-white/5">
                    <div class="h-full rounded-full bg-emerald-500 transition-all" style="width: {{ $occupancyRate }}%"></div>
                </div>
            </div>

            <div class="app-card app-card--gradient group relative overflow-hidden">
                <div class="absolute -right-4 -top-4 h-20 w-20 rounded-full bg-amber-500/10 blur-2xl transition-all group-hover:bg-amber-500/20"></div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('Today\'s Leads') }}</p>
                <p class="mt-3 text-3xl font-bold text-white">{{ number_format($stats['today_leads'] ?? 0) }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $stats['today_offers'] ?? 0 }} {{ __('offers generated') }}</p>
            </div>
        </section>

        {{-- Activity, Pipeline & Unit Portfolio on the same row --}}
        <section class="grid items-start gap-6 lg:grid-cols-3">
            <div class="lg:col-span-1">
                <section class="app-card app-card--gradient space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white">{{ __('Unit Portfolio') }}</h2>
                <span class="text-xs text-slate-400">{{ number_format($stats['units'] ?? 0) }} {{ __('total units') }}</span>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                {{-- Available --}}
                <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/5 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="inline-block h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                            <span class="text-sm font-medium text-emerald-300">{{ __('Available') }}</span>
                        </div>
                        <span class="text-2xl font-bold text-white">{{ $available }}</span>
                    </div>
                    <div class="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-emerald-500/10">
                        <div class="h-full rounded-full bg-emerald-500" style="width: {{ $totalUnits > 0 ? round($available / $totalUnits * 100) : 0 }}%"></div>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">{{ $totalUnits > 0 ? round($available / $totalUnits * 100) : 0 }}% {{ __('of portfolio') }}</p>
                </div>

                {{-- Reserved --}}
                <div class="rounded-2xl border border-amber-500/20 bg-amber-500/5 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="inline-block h-2.5 w-2.5 rounded-full bg-amber-400"></span>
                            <span class="text-sm font-medium text-amber-300">{{ __('Reserved') }}</span>
                        </div>
                        <span class="text-2xl font-bold text-white">{{ $reserved }}</span>
                    </div>
                    <div class="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-amber-500/10">
                        <div class="h-full rounded-full bg-amber-500" style="width: {{ $totalUnits > 0 ? round($reserved / $totalUnits * 100) : 0 }}%"></div>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">{{ $totalUnits > 0 ? round($reserved / $totalUnits * 100) : 0 }}% {{ __('of portfolio') }}</p>
                </div>

                {{-- Sold --}}
                <div class="rounded-2xl border border-rose-500/20 bg-rose-500/5 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="inline-block h-2.5 w-2.5 rounded-full bg-rose-400"></span>
                            <span class="text-sm font-medium text-rose-300">{{ __('Sold') }}</span>
                        </div>
                        <span class="text-2xl font-bold text-white">{{ $sold }}</span>
                    </div>
                    <div class="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-rose-500/10">
                        <div class="h-full rounded-full bg-rose-500" style="width: {{ $totalUnits > 0 ? round($sold / $totalUnits * 100) : 0 }}%"></div>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">{{ $totalUnits > 0 ? round($sold / $totalUnits * 100) : 0 }}% {{ __('of portfolio') }}</p>
                </div>
            </div>

            {{-- Combined progress bar --}}
            <div class="space-y-2">
                <div class="flex items-center justify-between text-xs text-slate-400">
                    <span>{{ __('Portfolio Distribution') }}</span>
                    <span>{{ number_format($totalUnits) }} {{ __('units') }}</span>
                </div>
                <div class="h-3 w-full overflow-hidden rounded-full bg-white/5 flex">
                    @if($totalUnits > 0)
                        <div class="h-full bg-emerald-500 transition-all" style="width: {{ round($available / $totalUnits * 100) }}%" title="{{ __('Available') }}: {{ $available }}"></div>
                        <div class="h-full bg-amber-500 transition-all" style="width: {{ round($reserved / $totalUnits * 100) }}%" title="{{ __('Reserved') }}: {{ $reserved }}"></div>
                        <div class="h-full bg-rose-500 transition-all" style="width: {{ round($sold / $totalUnits * 100) }}%" title="{{ __('Sold') }}: {{ $sold }}"></div>
                    @endif
                </div>
                <div class="flex gap-4 text-xs">
                    <span class="flex items-center gap-1.5"><span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>{{ __('Available') }} {{ $available }}</span>
                    <span class="flex items-center gap-1.5"><span class="inline-block h-2 w-2 rounded-full bg-amber-500"></span>{{ __('Reserved') }} {{ $reserved }}</span>
                    <span class="flex items-center gap-1.5"><span class="inline-block h-2 w-2 rounded-full bg-rose-500"></span>{{ __('Sold') }} {{ $sold }}</span>
                </div>
                </div>
            </section>
            </div>

            <div class="space-y-6 lg:col-span-2">
                <div class="grid gap-4 lg:grid-cols-2">
            {{-- Today's Activity --}}
            <div class="app-card app-card--gradient space-y-4">
                <h2 class="text-lg font-semibold text-white">{{ __('Today\'s Activity') }}</h2>

                <div class="space-y-3">
                    <div class="flex items-center justify-between rounded-xl bg-white/5 p-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/15">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="9" cy="7" r="4" stroke-width="1.8"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-width="1.8"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-white">{{ __('New Leads') }}</p>
                                <p class="text-xs text-slate-400">{{ __('Captured today') }}</p>
                            </div>
                        </div>
                        <span class="text-2xl font-bold text-blue-400">{{ $stats['today_leads'] ?? 0 }}</span>
                    </div>

                    <div class="flex items-center justify-between rounded-xl bg-white/5 p-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-500/15">
                                <svg class="h-5 w-5 text-violet-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" stroke-width="1.8"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" stroke-width="1.8" stroke-linecap="round"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-white">{{ __('Offers Sent') }}</p>
                                <p class="text-xs text-slate-400">{{ __('Quotations generated') }}</p>
                            </div>
                        </div>
                        <span class="text-2xl font-bold text-violet-400">{{ $stats['today_offers'] ?? 0 }}</span>
                    </div>

                    <div class="flex items-center justify-between rounded-xl bg-white/5 p-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/15">
                                <svg class="h-5 w-5 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" stroke-width="1.8" stroke-linecap="round"/><path d="M22 4 12 14.01l-3-3" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-white">{{ __('Active Reservations') }}</p>
                                <p class="text-xs text-slate-400">{{ __('Currently in pipeline') }}</p>
                            </div>
                        </div>
                        <span class="text-2xl font-bold text-emerald-400">{{ $stats['active_reservations'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

            {{-- Project Distribution --}}
            <div class="app-card app-card--gradient space-y-4">
                <h2 class="text-lg font-semibold text-white">{{ __('Project Distribution') }}</h2>

                @if(isset($chartData['projects']) && count($chartData['projects']) > 0)
                    <div class="space-y-3">
                        @foreach($chartData['projects'] as $index => $project)
                            @php
                                $projectUnits = $project->units_count ?? 0;
                                $maxUnits = $chartData['projects']->max(fn($p) => $p->units_count ?? 0);
                                $width = $maxUnits > 0 ? round($projectUnits / $maxUnits * 100) : 0;
                                $colors = ['bg-blue-500', 'bg-violet-500', 'bg-emerald-500', 'bg-amber-500', 'bg-rose-500'];
                                $color = $colors[$index % count($colors)];
                            @endphp
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-slate-200">{{ $project->name }}</span>
                                    <span class="text-xs text-slate-400">{{ $projectUnits }} {{ __('units') }}</span>
                                </div>
                                <div class="h-2 w-full overflow-hidden rounded-full bg-white/5">
                                    <div class="{{ $color }} h-full rounded-full transition-all" style="width: {{ $width }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex h-32 items-center justify-center rounded-xl border border-dashed border-white/10 text-sm text-slate-500">
                        {{ __('No project data available yet') }}
                    </div>
                @endif

                {{-- Sales Pipeline Summary --}}
                <div class="mt-4 rounded-xl bg-white/5 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('Sales Pipeline') }}</p>
                    <div class="mt-3 flex items-end gap-4">
                        <div class="flex-1 text-center">
                            <p class="text-2xl font-bold text-blue-400">{{ $stats['today_leads'] ?? 0 }}</p>
                            <p class="text-[10px] uppercase tracking-wide text-slate-500">{{ __('Leads') }}</p>
                        </div>
                        <div class="text-lg text-slate-600">→</div>
                        <div class="flex-1 text-center">
                            <p class="text-2xl font-bold text-violet-400">{{ $stats['today_offers'] ?? 0 }}</p>
                            <p class="text-[10px] uppercase tracking-wide text-slate-500">{{ __('Offers') }}</p>
                        </div>
                        <div class="text-lg text-slate-600">→</div>
                        <div class="flex-1 text-center">
                            <p class="text-2xl font-bold text-emerald-400">{{ $stats['active_reservations'] ?? 0 }}</p>
                            <p class="text-[10px] uppercase tracking-wide text-slate-500">{{ __('Reserved') }}</p>
                        </div>
                        <div class="text-lg text-slate-600">→</div>
                        <div class="flex-1 text-center">
                            <p class="text-2xl font-bold text-rose-400">{{ $sold }}</p>
                            <p class="text-[10px] uppercase tracking-wide text-slate-500">{{ __('Sold') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
        </section>

        {{-- Quick Actions --}}
        <section class="app-card app-card--gradient">
            <h2 class="mb-4 text-lg font-semibold text-white">{{ __('Quick Actions') }}</h2>
            <div class="grid gap-3 sm:grid-cols-3">
                <a href="{{ route('dashboard.crm.index') }}" class="group flex items-center gap-3 rounded-xl bg-white/5 p-4 transition hover:bg-white/10">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/15 transition group-hover:bg-blue-500/25">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="9" cy="7" r="4" stroke-width="1.8"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-width="1.8"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-white">{{ __('Open CRM') }}</p>
                        <p class="text-xs text-slate-400">{{ __('Manage leads & customers') }}</p>
                    </div>
                    <svg class="ml-auto h-4 w-4 text-slate-500 transition group-hover:text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12h14M12 5l7 7-7 7" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>

                <a href="{{ route('dashboard.projects.index') }}" class="group flex items-center gap-3 rounded-xl bg-white/5 p-4 transition hover:bg-white/10">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-500/15 transition group-hover:bg-violet-500/25">
                        <svg class="h-5 w-5 text-violet-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16" stroke-width="1.8"/><path d="M9 7h2M13 7h2M9 11h2M13 11h2M9 15h2M13 15h2" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-white">{{ __('Browse Projects') }}</p>
                        <p class="text-xs text-slate-400">{{ __('View & manage inventory') }}</p>
                    </div>
                    <svg class="ml-auto h-4 w-4 text-slate-500 transition group-hover:text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12h14M12 5l7 7-7 7" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>

                <a href="{{ route('dashboard.installments.index') }}" class="group flex items-center gap-3 rounded-xl bg-white/5 p-4 transition hover:bg-white/10">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/15 transition group-hover:bg-emerald-500/25">
                        <svg class="h-5 w-5 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="5" y="3" width="14" height="18" rx="3" stroke-width="1.8"/><path d="M8 7h8M8 11h2M12 11h2M16 11h0M8 15h2M12 15h2M16 15h0" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-white">{{ __('Calculator') }}</p>
                        <p class="text-xs text-slate-400">{{ __('Installment plans') }}</p>
                    </div>
                    <svg class="ml-auto h-4 w-4 text-slate-500 transition group-hover:text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12h14M12 5l7 7-7 7" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            </div>
        </section>
    </div>
@endsection
