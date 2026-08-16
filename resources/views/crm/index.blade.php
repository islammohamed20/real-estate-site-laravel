@extends('layouts.dashboard')

@section('content')
    @php
        $totalStats = array_sum($stats);
    @endphp

    <div class="space-y-6">
    @include('crm.partials.crm-nav')

        @if (session('status'))
            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="flex items-center gap-3 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12l5 5L20 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span>{{ session('status') }}</span>
                <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-200">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 6L6 18M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="flex items-center gap-3 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-300">
                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="2"/><path d="M12 8v4M12 16h.01" stroke-width="2" stroke-linecap="round"/></svg>
                <span>{{ session('error') }}</span>
                <button @click="show = false" class="ml-auto text-rose-400 hover:text-rose-200">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 6L6 18M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        @endif

        <section class="dashboard-hero-card p-6 sm:p-8">
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-brand-500/20 blur-3xl"></div>
            <div class="absolute -bottom-20 left-1/3 h-56 w-56 rounded-full bg-violet-500/10 blur-3xl"></div>

            <div class="relative space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="badge badge-brand">{{ __('Customer hub') }}</span>
                    <span class="badge badge-success">{{ __('Live CRM') }}</span>
                    <span class="badge badge-muted">{{ number_format($totalStats) }} {{ __('total signals') }}</span>
                </div>

                <div>
                    <p class="mobile-section-title">{{ __('Pipeline') }}</p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('CRM') }}</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">{{ __('Mobile-first customer and lead management with fast access to leads, tasks, notes, and reservation activity.') }}</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('dashboard.crm.quick') }}" class="app-button">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        {{ __('+ New Lead') }}
                    </a>
                    <a href="{{ route('dashboard.crm.deals.index') }}" class="app-button--ghost">{{ __('Deals board') }}</a>
                </div>
            </div>
        </section>

        {{-- ── KPI row ── --}}
        <section class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            @foreach ([
                ['label' => __('Total Leads'), 'value' => $stats['leads'] ?? 0, 'route' => 'dashboard.crm.leads.index', 'tint' => 'bg-emerald-500/15 text-emerald-400', 'icon' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="8.5" cy="7" r="4" stroke-width="1.8"/><path d="M20 8v6M23 11h-6" stroke-width="1.8" stroke-linecap="round"/></svg>'],
                ['label' => __('Total Customers'), 'value' => $stats['customers'] ?? 0, 'route' => 'dashboard.crm.customers.index', 'tint' => 'bg-brand-500/15 text-brand-400', 'icon' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="9" cy="7" r="4" stroke-width="1.8"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-width="1.8"/></svg>'],
                ['label' => __('Follow-ups Today'), 'value' => $stats['follow_ups_today'] ?? 0, 'route' => 'dashboard.crm.follow_ups.index', 'tint' => 'bg-sky-500/15 text-sky-400', 'icon' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="9" stroke-width="1.8"/><path d="M12 7v5l3 3" stroke-width="1.8" stroke-linecap="round"/></svg>'],
                ['label' => __('Open Tasks'), 'value' => $stats['open_tasks'] ?? 0, 'route' => 'dashboard.crm.tasks.index', 'tint' => 'bg-violet-500/15 text-violet-400', 'icon' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 11l3 3L22 4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" stroke-width="1.8" stroke-linecap="round"/></svg>'],
            ] as $kpi)
                <a href="{{ route($kpi['route']) }}" class="stagger-item group app-card app-card--gradient !p-3.5 transition-all duration-300 hover:border-brand-500/40" style="animation-delay:{{ 200 + $loop->index * 70 }}ms">
                    <div class="flex items-center gap-3">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $kpi['tint'] }} ring-1 ring-white/10 transition-transform duration-300 group-hover:scale-105">{!! $kpi['icon'] !!}</span>
                        <div class="min-w-0">
                            <p class="text-xl font-bold leading-tight tabular-nums text-white">{{ number_format($kpi['value']) }}</p>
                            <p class="truncate text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ $kpi['label'] }}</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </section>

        {{-- ── Recent leads + timeline ── --}}
        <div class="grid gap-6 lg:grid-cols-2">
            <section class="space-y-3">
                <div class="flex items-center justify-between px-1">
                    <h2 class="text-lg font-semibold">{{ __('Recent Leads') }}</h2>
                    <a href="{{ route('dashboard.crm.leads.index') }}" class="link-arrow">{{ __('View all') }}</a>
                </div>
                @forelse ($leads as $lead)
                    <article class="stagger-item app-card app-card--gradient !p-4 transition-all duration-300 hover:-translate-y-0.5 hover:border-brand-500/30" style="animation-delay:{{ $loop->index * 60 }}ms">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500/25 to-violet-600/15 text-sm font-bold text-brand-200 ring-1 ring-brand-500/20">{{ mb_strtoupper(mb_substr($lead->name, 0, 1)) }}</span>
                                <div class="min-w-0">
                                    <h3 class="truncate font-semibold text-white">{{ $lead->name }}</h3>
                                    <p class="text-sm text-slate-400">{{ $lead->phone }}</p>
                                </div>
                            </div>
                            @include('crm.partials.status-badge', ['status' => $lead->stage->value, 'label' => __($lead->stage->label())])
                        </div>
                        <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-400">
                            @if ($lead->budget)
                                <span class="flex items-center gap-1"><svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="9" stroke-width="1.8"/><path d="M12 7v10M9.5 9.5c0-.83 1.12-1.5 2.5-1.5s2.5.67 2.5 1.5-1.12 1.5-2.5 1.5-2.5.67-2.5 1.5 1.12 1.5 2.5 1.5 2.5.67 2.5 1.5-1.12 1.5-2.5 1.5-2.5-.67-2.5-1.5" stroke-width="1.6"/></svg> EGP {{ number_format((float) $lead->budget) }}</span>
                            @endif
                            @if ($lead->follow_up_at)
                                <span class="flex items-center gap-1"><svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="9" stroke-width="1.8"/><path d="M12 7v5l3 3" stroke-width="1.8" stroke-linecap="round"/></svg> {{ $lead->follow_up_at->diffForHumans() }}</span>
                            @endif
                            <span class="flex items-center gap-1">{{ __('Assigned') }}: {{ $lead->assignedSales?->name ?? __('Unassigned') }}</span>
                        </div>
                        <div class="mt-3 flex gap-2">
                            <a href="{{ route('dashboard.crm.leads.show', $lead) }}" class="app-button w-full justify-center text-sm">{{ __('View') }}</a>
                            <a href="tel:{{ $lead->phone }}" class="app-button--ghost shrink-0 p-2" aria-label="{{ __('Call') }}">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="flex min-h-32 flex-col items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/5 text-center text-sm text-slate-500">
                        <p>{{ __('No leads yet. Use the New Lead button to create one.') }}</p>
                    </div>
                @endforelse
            </section>

            <section class="space-y-3">
                <div class="flex items-center justify-between px-1">
                    <h2 class="text-lg font-semibold">{{ __('Recent Activity') }}</h2>
                    <a href="{{ route('dashboard.crm.reports.index') }}" class="link-arrow">{{ __('Reports') }}</a>
                </div>
                <div class="app-card app-card--gradient !p-4 space-y-4">
                    @forelse ($timeline as $event)
                        <div class="stagger-item flex gap-3 border-b border-white/10 pb-3 transition-colors duration-300 hover:bg-white/[0.03] last:border-0 last:pb-0" style="animation-delay:{{ $loop->index * 60 }}ms">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl
                                {{ $event['type'] === 'stage' ? 'bg-violet-500/15 text-violet-400' : '' }}
                                {{ $event['type'] === 'task' ? 'bg-sky-500/15 text-sky-400' : '' }}
                                {{ $event['type'] === 'note' ? 'bg-emerald-500/15 text-emerald-400' : '' }}">
                                @if ($event['type'] === 'stage')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 5l7 7-7 7" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                @elseif ($event['type'] === 'task')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m9 11 3 3L22 4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                @else
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-width="1.8" stroke-linecap="round"/><polyline points="14 2 14 8 20 8" stroke-width="1.8" stroke-linecap="round"/></svg>
                                @endif
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-white">{{ $event['title'] }}</p>
                                @if ($event['body'])
                                    <p class="truncate text-xs text-slate-400">{{ $event['body'] }}</p>
                                @endif
                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $event['at']?->diffForHumans() ?? '' }}
                                    @if ($event['user'])
                                        · {{ $event['user'] }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">{{ __('No timeline events yet.') }}</p>
                    @endforelse
                </div>
            </section>
        </div>

        <section class="space-y-3">
            <div class="flex items-center justify-between px-1">
                <h2 class="text-lg font-semibold">{{ __('Customers') }}</h2>
                <a href="{{ route('dashboard.crm.customers.index') }}" class="link-arrow">{{ __('View all') }}</a>
            </div>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($customers as $customer)
                    <a href="{{ route('dashboard.crm.customers.show', $customer) }}" class="app-card app-card--gradient p-4 transition hover:border-brand-500/30">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-500/15 text-sm font-bold text-brand-300">{{ mb_strtoupper(mb_substr($customer->name, 0, 1)) }}</span>
                                <div class="min-w-0">
                                    <h3 class="truncate font-semibold text-white">{{ $customer->name }}</h3>
                                    <p class="truncate text-sm text-slate-400">{{ $customer->phone }}</p>
                                </div>
                            </div>
                            <span class="shrink-0 rounded-full bg-slate-800 px-3 py-1 text-xs text-slate-300">{{ $customer->leads_count }} {{ __('leads') }}</span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full flex min-h-32 flex-col items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/5 text-center text-sm text-slate-500">
                        <p>{{ __('No customers yet.') }}</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
