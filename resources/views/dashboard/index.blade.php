@extends('layouts.dashboard')

@section('content')
    @php
        $overview = $stats ?? [];
        $profile = \App\Models\CompanyProfile::first();
        $currency = $profile?->currency_code ?? 'EGP';

        $projects = collect($chartData['projects'] ?? []);
        $dealStages = collect($chartData['deal_stages'] ?? []);
        $recentDeals = collect($chartData['recent_deals'] ?? []);
        $recentLeads = collect($chartData['recent_leads'] ?? []);
        $recentConversations = collect($chartData['recent_conversations'] ?? []);
        $recentActivities = collect($chartData['recent_activities'] ?? []);
        $reservations = collect($chartData['reservations'] ?? []);

        $totalUnits = (int) ($overview['units'] ?? 0);
        $availableUnits = (int) ($overview['available_units'] ?? 0);
        $reservedUnits = (int) ($overview['reserved_units'] ?? 0);
        $soldUnits = (int) ($overview['sold_units'] ?? 0);
        $occupiedUnits = $reservedUnits + $soldUnits;
        $occupancyRate = $totalUnits > 0 ? (int) round($occupiedUnits / $totalUnits * 100) : 0;
        $availablePct = $totalUnits > 0 ? (int) round($availableUnits / $totalUnits * 100) : 0;

        $fmtMoney = fn (float $val) => number_format($val, 0, '.', ',');
        $openDealsVal = (float) ($overview['open_deals_value'] ?? 0);
        $wonDealsVal = (float) ($overview['won_deals_value'] ?? 0);
        $fmtShortMoney = function (float $val) use ($currency) {
            if ($val >= 1_000_000_000) {
                return number_format($val / 1_000_000_000, 2) . ' ' . __('B') . ' ' . __($currency);
            }
            if ($val >= 1_000_000) {
                return number_format($val / 1_000_000, 2) . ' ' . __('M') . ' ' . __($currency);
            }
            if ($val >= 1_000) {
                return number_format($val / 1_000, 1) . ' ' . __('K') . ' ' . __($currency);
            }
            return number_format($val) . ' ' . __($currency);
        };

        $chartLabels = $chartData['sales']['labels'] ?? [];
        $chartLeads = $chartData['sales']['leads'] ?? [];
        $chartOffers = $chartData['sales']['offers'] ?? [];
        $chartDeals = $chartData['sales']['deals'] ?? [];
    @endphp

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-12 xl:gap-5" x-data="{ recentTab: 'deals' }">
        {{-- ══════════════════════════════════════════════════════════════
             HERO HEADER & QUICK LAUNCH
        ══════════════════════════════════════════════════════════════ --}}
        <section class="dashboard-hero-card col-span-1 md:col-span-2 xl:col-span-12 p-5 sm:p-7 relative overflow-hidden">
            <div class="absolute -right-16 -top-16 h-56 w-56 rounded-full bg-brand-500/25 blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-16 left-1/3 h-56 w-56 rounded-full bg-emerald-500/15 blur-3xl pointer-events-none"></div>

            <div class="relative flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="space-y-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="badge badge-success px-2.5 py-0.5 text-[10px] sm:text-xs">● {{ __('System Online') }}</span>
                        <span class="badge badge-brand px-2.5 py-0.5 text-[10px] sm:text-xs">{{ __('Control Center') }}</span>
                        <span class="badge badge-muted px-2.5 py-0.5 text-[10px] sm:text-xs">{{ number_format($totalUnits) }} {{ __('units in stock') }}</span>
                        @if($overview['whatsapp_unread'] > 0)
                            <a href="{{ route('dashboard.whatsapp.index') }}" class="badge badge-warning animate-pulse px-2.5 py-0.5 text-[10px] sm:text-xs">
                                💬 {{ $overview['whatsapp_unread'] }} {{ __('unread WhatsApp') }}
                            </a>
                        @endif
                    </div>

                    <div>
                        <p class="mobile-section-title">{{ __('Real Estate Management Platform') }}</p>
                        <h1 class="mt-1.5 text-2xl font-black tracking-tight text-white sm:text-3xl text-balance">
                            {{ $profile?->name ?? __('Venecia Developments') }}
                        </h1>
                        <p class="mt-1.5 max-w-2xl text-xs leading-relaxed text-slate-400 sm:text-sm">
                            {{ __('Live overview of projects, units inventory, CRM deals velocity, customer communications, and portfolio health.') }}
                        </p>
                    </div>
                </div>

                {{-- Quick Action Buttons --}}
                <div class="flex flex-wrap items-center gap-2">
                    @canany(['create leads', 'manage crm'])
                        <a href="{{ route('dashboard.crm.quick') }}" class="app-button !min-h-10 gap-1.5 text-xs sm:text-sm">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round"/></svg>
                            {{ __('Quick Lead') }}
                        </a>
                    @endcanany

                    <a href="{{ route('dashboard.projects.index') }}" class="app-button--ghost !min-h-10 gap-1.5 text-xs sm:text-sm">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 21h18M5 21V7a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v14M9 9h.01M15 9h.01M9 13h.01M15 13h.01M9 17h.01M15 17h.01" stroke-width="1.8"/></svg>
                        {{ __('Projects & Units') }}
                    </a>

                    <a href="{{ route('dashboard.installments.index') }}" class="app-button--ghost !min-h-10 gap-1.5 text-xs sm:text-sm">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="1.8"/><path d="M14.5 9a3.5 3.5 0 0 0-5 0M14.5 15a3.5 3.5 0 0 1-5 0M12 7v10" stroke-width="1.8"/></svg>
                        {{ __('Calculator') }}
                    </a>

                    @can('view whatsapp')
                        <a href="{{ route('dashboard.whatsapp.index') }}" class="app-button--ghost !min-h-10 gap-1.5 text-xs sm:text-sm relative">
                            <svg class="h-4 w-4 text-emerald-400" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                            {{ __('WhatsApp') }}
                            @if($overview['whatsapp_unread'] > 0)
                                <span class="h-2 w-2 rounded-full bg-emerald-400 absolute top-2 right-2"></span>
                            @endif
                        </a>
                    @endcan
                </div>
            </div>
        </section>

        {{-- ══════════════════════════════════════════════════════════════
             BENTO ROW 1 — 4 PRIMARY KPI METRIC CARDS
        ══════════════════════════════════════════════════════════════ --}}
        <div class="col-span-1 md:col-span-2 xl:col-span-12">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4 xl:gap-5 mx-auto w-full max-w-7xl">
                {{-- Card 1: Available Units & Portfolio --}}
                <div class="stagger-item app-card app-card--gradient !p-4.5 group relative overflow-hidden transition-all duration-300 hover:border-emerald-500/30">
            <div class="absolute -right-6 -top-6 h-28 w-28 rounded-full bg-emerald-500/10 blur-2xl transition-all duration-500 group-hover:bg-emerald-500/25 group-hover:scale-125"></div>
            <div class="relative flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ __('Available Units') }}</p>
                    <div class="mt-1.5 flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-black tabular-nums text-emerald-400">{{ number_format($availableUnits) }}</span>
                        <span class="text-xs text-slate-500">/ {{ number_format($totalUnits) }}</span>
                    </div>
                </div>
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500/25 to-teal-500/10 text-emerald-300 ring-1 ring-emerald-500/25 shadow-lg shadow-emerald-500/10">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" stroke-width="1.8" stroke-linecap="round"/><path d="m9 11 3 3L22 4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
            </div>
            <div class="relative mt-3 pt-2.5 border-t border-white/5 flex items-center justify-between text-[11px]">
                <span class="text-slate-400">{{ __('Available') }}</span>
                <span class="font-bold text-white tabular-nums">{{ $availablePct }}%</span>
            </div>
            <div class="relative mt-2 h-1.5 w-full overflow-hidden rounded-full bg-white/5">
                <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-400 transition-all duration-700" style="width: {{ $availablePct }}%"></div>
            </div>
        </div>

                {{-- Card 2: CRM Deals & Pipeline Value --}}
                <div class="stagger-item app-card app-card--gradient !p-4.5 group relative overflow-hidden transition-all duration-300 hover:border-violet-500/30">
            <div class="absolute -right-6 -top-6 h-28 w-28 rounded-full bg-violet-500/10 blur-2xl transition-all duration-500 group-hover:bg-violet-500/25 group-hover:scale-125"></div>
            <div class="relative flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ __('Active Pipeline') }}</p>
                    <div class="mt-1.5 flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-black tabular-nums text-violet-300">{{ number_format($overview['open_deals_count'] ?? 0) }}</span>
                        <span class="text-xs text-violet-400/80">{{ __('Deals') }}</span>
                    </div>
                </div>
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-violet-500/25 to-fuchsia-500/10 text-violet-300 ring-1 ring-violet-500/25 shadow-lg shadow-violet-500/10">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 4h11a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H9z" stroke-width="1.8"/><path d="M4 20a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2v16z" stroke-width="1.8"/></svg>
                </span>
            </div>
            <div class="relative mt-3 pt-2.5 border-t border-white/5 flex items-center justify-between text-[11px]">
                <span class="text-slate-400">{{ __('Pipeline Value') }}</span>
                <span class="font-bold text-violet-300 tabular-nums">{{ $fmtShortMoney($openDealsVal) }}</span>
            </div>
            <div class="relative mt-2 flex items-center justify-between text-[10px] text-slate-500">
                <span>{{ __('Won Deals') }}: <strong class="text-slate-300">{{ $fmtShortMoney($wonDealsVal) }}</strong></span>
                <a href="{{ route('dashboard.crm.deals.index') }}" class="font-semibold text-violet-400 hover:text-violet-300">{{ __('View Pipeline') }} →</a>
            </div>
        </div>

                {{-- Card 3: Leads & Engagement --}}
                <div class="stagger-item app-card app-card--gradient !p-4.5 group relative overflow-hidden transition-all duration-300 hover:border-blue-500/30">
            <div class="absolute -right-6 -top-6 h-28 w-28 rounded-full bg-blue-500/10 blur-2xl transition-all duration-500 group-hover:bg-blue-500/25 group-hover:scale-125"></div>
            <div class="relative flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ __('Total Leads') }}</p>
                    <div class="mt-1.5 flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-black tabular-nums text-white">{{ number_format($overview['total_leads'] ?? 0) }}</span>
                        @if(($overview['today_leads'] ?? 0) > 0)
                            <span class="badge badge-success !px-1.5 !py-0 text-[10px] font-bold">+{{ $overview['today_leads'] }} {{ __('today') }}</span>
                        @endif
                    </div>
                </div>
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500/25 to-sky-500/10 text-blue-300 ring-1 ring-blue-500/25 shadow-lg shadow-blue-500/10">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="8.5" cy="7" r="4" stroke-width="1.8"/><path d="M20 8v6M23 11h-6" stroke-width="1.8" stroke-linecap="round"/></svg>
                </span>
            </div>
            <div class="relative mt-3 pt-2.5 border-t border-white/5 flex items-center justify-between text-[11px]">
                <span class="text-slate-400">{{ __('Customers') }}</span>
                <span class="font-bold text-white tabular-nums">{{ number_format($overview['total_customers'] ?? 0) }}</span>
            </div>
            <div class="relative mt-2 flex items-center justify-between text-[10px] text-slate-500">
                <span>{{ $overview['week_leads'] ?? 0 }} {{ __('this week') }}</span>
                <a href="{{ route('dashboard.crm.leads.index') }}" class="font-semibold text-blue-400 hover:text-blue-300">{{ __('All Leads') }} →</a>
            </div>
        </div>

                {{-- Card 4: WhatsApp & Live Conversations --}}
                <div class="stagger-item app-card app-card--gradient !p-4.5 group relative overflow-hidden transition-all duration-300 hover:border-emerald-500/30">
            <div class="absolute -right-6 -top-6 h-28 w-28 rounded-full bg-emerald-500/10 blur-2xl transition-all duration-500 group-hover:bg-emerald-500/25 group-hover:scale-125"></div>
            <div class="relative flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ __('WhatsApp Conversations') }}</p>
                    <div class="mt-1.5 flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-black tabular-nums text-white">{{ number_format($overview['whatsapp_conversations'] ?? 0) }}</span>
                        @if(($overview['whatsapp_unread'] ?? 0) > 0)
                            <span class="badge badge-danger !px-1.5 !py-0 text-[10px] font-bold animate-pulse">{{ $overview['whatsapp_unread'] }} {{ __('unread') }}</span>
                        @else
                            <span class="badge badge-success !px-1.5 !py-0 text-[10px]">{{ __('Up to date') }}</span>
                        @endif
                    </div>
                </div>
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500/25 to-teal-500/10 text-emerald-300 ring-1 ring-emerald-500/25 shadow-lg shadow-emerald-500/10">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                </span>
            </div>
            <div class="relative mt-3 pt-2.5 border-t border-white/5 flex items-center justify-between text-[11px]">
                <span class="text-slate-400">{{ __('Website Visitors') }}</span>
                <span class="font-bold text-emerald-400 tabular-nums">{{ number_format($overview['total_visits'] ?? 0) }}</span>
            </div>
            <div class="relative mt-2 flex items-center justify-between text-[10px] text-slate-500">
                <span>{{ $overview['today_visits'] ?? 0 }} {{ __('visits today') }}</span>
                <a href="{{ route('dashboard.whatsapp.index') }}" class="font-semibold text-emerald-400 hover:text-emerald-300">{{ __('Open Inbox') }} →</a>
            </div>
        </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════
             BENTO ROW 2 — VELOCITY CHART (8 COLS) + PIPELINE & OCCUPANCY (4 COLS)
        ══════════════════════════════════════════════════════════════ --}}
        {{-- Velocity Chart --}}
        <div class="stagger-item app-card app-card--gradient !p-5 space-y-4 md:col-span-2 xl:col-span-8">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/5 pb-3.5">
                <div>
                    <p class="mobile-section-title">{{ __('7-Day Activity Trends') }}</p>
                    <h2 class="mt-1 text-base font-bold text-white flex items-center gap-2">
                        <svg class="h-4 w-4 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 3v18h18" stroke-width="1.8" stroke-linecap="round"/><path d="M7 15l4-5 3 3 5-7" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        {{ __('Deals, Leads & Offers Velocity') }}
                    </h2>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-xs">
                    <span class="inline-flex items-center gap-1.5 font-semibold text-indigo-300 bg-indigo-500/10 px-2.5 py-1 rounded-lg border border-indigo-500/20">
                        <span class="h-2 w-2 rounded-full bg-indigo-400 shadow-[0_0_6px_rgba(129,140,248,.8)]"></span>
                        {{ __('Deals') }} ({{ array_sum($chartDeals) }})
                    </span>
                    <span class="inline-flex items-center gap-1.5 font-semibold text-sky-300 bg-sky-500/10 px-2.5 py-1 rounded-lg border border-sky-500/20">
                        <span class="h-2 w-2 rounded-full bg-sky-400 shadow-[0_0_6px_rgba(56,189,248,.8)]"></span>
                        {{ __('Leads') }} ({{ array_sum($chartLeads) }})
                    </span>
                    <span class="inline-flex items-center gap-1.5 font-semibold text-emerald-300 bg-emerald-500/10 px-2.5 py-1 rounded-lg border border-emerald-500/20">
                        <span class="h-2 w-2 rounded-full bg-emerald-400 shadow-[0_0_6px_rgba(52,211,153,.8)]"></span>
                        {{ __('Offers') }} ({{ array_sum($chartOffers) }})
                    </span>
                </div>
            </div>

            <div class="relative h-[290px] w-full">
                <canvas id="velocityChart"></canvas>
            </div>
        </div>

        {{-- Inventory & Pipeline Momentum --}}
        <div class="stagger-item app-card app-card--gradient !p-5 space-y-4 md:col-span-2 xl:col-span-4 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center justify-between gap-3 border-b border-white/5 pb-3">
                    <div>
                        <p class="mobile-section-title">{{ __('Inventory & Pipeline') }}</p>
                        <h2 class="mt-1 text-base font-bold text-white">{{ __('Portfolio Status') }}</h2>
                    </div>
                    <span class="badge badge-brand">{{ $occupancyRate }}% {{ __('occupied') }}</span>
                </div>

                {{-- Inventory status breakdown --}}
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="rounded-xl border border-emerald-500/20 bg-emerald-500/5 p-2.5">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-400">{{ __('Available') }}</p>
                        <p class="mt-1 text-lg font-black tabular-nums text-white">{{ number_format($availableUnits) }}</p>
                    </div>
                    <div class="rounded-xl border border-amber-500/20 bg-amber-500/5 p-2.5">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-amber-400">{{ __('Reserved') }}</p>
                        <p class="mt-1 text-lg font-black tabular-nums text-white">{{ number_format($reservedUnits) }}</p>
                        <p class="text-[10px] text-slate-400 truncate">{{ $overview['active_reservations'] ?? 0 }} {{ __('active') }}</p>
                    </div>
                    <div class="rounded-xl border border-blue-500/20 bg-blue-500/5 p-2.5">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-blue-400">{{ __('Sold') }}</p>
                        <p class="mt-1 text-lg font-black tabular-nums text-white">{{ number_format($soldUnits) }}</p>
                    </div>
                </div>

                {{-- Portfolio multi-color progress bar --}}
                <div class="space-y-1.5 pt-1">
                    <div class="flex justify-between text-[11px] text-slate-400">
                        <span>{{ __('Inventory Allocation') }}</span>
                        <span class="font-bold text-white">{{ number_format($totalUnits) }} {{ __('Total Units') }}</span>
                    </div>
                    <div class="h-2.5 w-full overflow-hidden rounded-full bg-white/5 flex">
                        @if($totalUnits > 0)
                            <div class="h-full bg-emerald-500 transition-all duration-700" style="width: {{ round($availableUnits / $totalUnits * 100) }}%" title="{{ __('Available') }}: {{ $availableUnits }}"></div>
                            <div class="h-full bg-amber-500 transition-all duration-700" style="width: {{ round($reservedUnits / $totalUnits * 100) }}%" title="{{ __('Reserved') }}: {{ $reservedUnits }}"></div>
                            <div class="h-full bg-blue-500 transition-all duration-700" style="width: {{ round($soldUnits / $totalUnits * 100) }}%" title="{{ __('Sold') }}: {{ $soldUnits }}"></div>
                        @endif
                    </div>
                </div>

                {{-- Deals Stages Mini-Breakdown --}}
                @if($dealStages->isNotEmpty())
                    <div class="space-y-2 pt-2 border-t border-white/5">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center justify-between">
                            <span>{{ __('CRM Deals Pipeline') }}</span>
                            <span class="text-violet-400">{{ $dealStages->sum('deals_count') }} {{ __('Deals') }}</span>
                        </p>
                        <div class="space-y-1.5">
                            @foreach($dealStages->take(4) as $stg)
                                <div class="flex items-center justify-between text-xs py-1 px-2 rounded-lg bg-white/5 border border-white/5">
                                    <span class="flex items-center gap-1.5 truncate">
                                        <span class="h-2 w-2 rounded-full" style="background-color: {{ $stg->color ?: '#818cf8' }}"></span>
                                        <span class="text-slate-300 font-medium truncate">{{ $stg->name }}</span>
                                    </span>
                                    <span class="text-[11px] font-bold text-white tabular-nums">{{ $stg->deals_count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="pt-3 border-t border-white/5">
                <a href="{{ route('dashboard.crm.deals.index') }}" class="app-button--ghost w-full justify-center !py-2 text-xs">
                    {{ __('Open CRM Pipeline') }} →
                </a>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════
             BENTO ROW 3 — LIVE DATA FEEDS: RECENT DEALS / LEADS (7 COLS) + WHATSAPP & PROJECTS (5 COLS)
        ══════════════════════════════════════════════════════════════ --}}
        {{-- Deals / Leads Tabbed Feed --}}
        <div class="stagger-item app-card app-card--gradient !p-5 space-y-4 md:col-span-2 xl:col-span-7">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/5 pb-3">
                <div class="flex items-center gap-2">
                    <button type="button" @click="recentTab = 'deals'" :class="recentTab === 'deals' ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/20' : 'text-slate-400 hover:bg-white/5 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition">
                        💼 {{ __('Recent Deals') }} ({{ $recentDeals->count() }})
                    </button>
                    <button type="button" @click="recentTab = 'leads'" :class="recentTab === 'leads' ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/20' : 'text-slate-400 hover:bg-white/5 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition">
                        🎯 {{ __('Recent Leads') }} ({{ $recentLeads->count() }})
                    </button>
                </div>
                <template x-if="recentTab === 'deals'">
                    <a href="{{ route('dashboard.crm.deals.index') }}" class="text-xs font-semibold text-brand-400 hover:text-brand-300">{{ __('View All Deals') }} →</a>
                </template>
                <template x-if="recentTab === 'leads'">
                    <a href="{{ route('dashboard.crm.leads.index') }}" class="text-xs font-semibold text-brand-400 hover:text-brand-300">{{ __('View All Leads') }} →</a>
                </template>
            </div>

            {{-- Recent Deals Content --}}
            <div x-show="recentTab === 'deals'" class="space-y-2.5">
                @forelse($recentDeals as $deal)
                    <div class="flex items-center justify-between gap-3 p-3 rounded-xl border border-white/5 bg-white/[0.03] transition hover:bg-white/[0.07] hover:border-white/10">
                        <div class="min-w-0 flex items-center gap-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-violet-500/20 text-violet-300 text-xs font-black ring-1 ring-violet-500/30">
                                {{ mb_strtoupper(mb_substr($deal->title, 0, 1)) }}
                            </span>
                            <div class="min-w-0">
                                <a href="{{ route('dashboard.crm.deals.show', $deal) }}" class="text-sm font-bold text-white hover:text-brand-300 truncate block">
                                    {{ $deal->title }}
                                </a>
                                <p class="text-[11px] text-slate-400 flex items-center gap-2 mt-0.5">
                                    <span>{{ $deal->assignedUser?->name ?? __('Unassigned') }}</span>
                                    @if($deal->stage)
                                        <span>·</span>
                                        <span class="text-violet-300 font-medium">{{ $deal->stage->name }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="text-end shrink-0">
                            <p class="text-sm font-black text-emerald-400 tabular-nums">
                                {{ $fmtMoney((float) $deal->value) }} <span class="text-[10px] font-normal text-slate-400">{{ __($currency) }}</span>
                            </p>
                            <p class="text-[10px] text-slate-500">{{ $deal->created_at?->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-500 text-sm">
                        {{ __('No recent deals recorded.') }}
                    </div>
                @endforelse
            </div>

            {{-- Recent Leads Content --}}
            <div x-show="recentTab === 'leads'" x-cloak class="space-y-2.5">
                @forelse($recentLeads as $lead)
                    <div class="flex items-center justify-between gap-3 p-3 rounded-xl border border-white/5 bg-white/[0.03] transition hover:bg-white/[0.07] hover:border-white/10">
                        <div class="min-w-0 flex items-center gap-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-500/20 text-blue-300 text-xs font-black ring-1 ring-blue-500/30">
                                {{ mb_strtoupper(mb_substr($lead->name, 0, 1)) }}
                            </span>
                            <div class="min-w-0">
                                <a href="{{ route('dashboard.crm.leads.show', $lead) }}" class="text-sm font-bold text-white hover:text-brand-300 truncate block">
                                    {{ $lead->name }}
                                </a>
                                <p class="text-[11px] text-slate-400 flex items-center gap-2 mt-0.5">
                                    <span class="ltr">{{ $lead->phone }}</span>
                                    @if($lead->assignedSales)
                                        <span>·</span>
                                        <span>{{ $lead->assignedSales->name }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="text-end shrink-0">
                            <span class="badge badge-brand !px-2 !py-0.5 text-[10px] font-semibold">
                                {{ $lead->stage?->label() ?? 'New' }}
                            </span>
                            <p class="text-[10px] text-slate-500 mt-1">{{ $lead->created_at?->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-500 text-sm">
                        {{ __('No leads recorded yet.') }}
                    </div>
                @endforelse
            </div>
        </div>

        {{-- WhatsApp Live Feed & Projects Spotlight --}}
        <div class="stagger-item app-card app-card--gradient !p-5 space-y-4 md:col-span-2 xl:col-span-5">
            <div class="flex items-center justify-between gap-3 border-b border-white/5 pb-3">
                <div>
                    <p class="mobile-section-title">{{ __('Direct Communications') }}</p>
                    <h2 class="mt-1 text-base font-bold text-white flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        {{ __('Live WhatsApp Chats') }}
                    </h2>
                </div>
                <a href="{{ route('dashboard.whatsapp.index') }}" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300">{{ __('Open Hub') }} →</a>
            </div>

            @if($recentConversations->isNotEmpty())
                <div class="space-y-2.5">
                    @foreach($recentConversations as $conv)
                        <a href="{{ route('dashboard.whatsapp.index') }}" class="flex items-center justify-between gap-3 p-3 rounded-xl border border-white/5 bg-white/[0.03] transition hover:bg-emerald-500/10 hover:border-emerald-500/30 group">
                            <div class="min-w-0 flex items-center gap-2.5">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-500/20 text-emerald-300 font-bold text-xs ring-1 ring-emerald-500/30">
                                    {{ mb_strtoupper(mb_substr($conv->customer_name ?: ($conv->customer_phone ?: 'W'), 0, 1)) }}
                                </span>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-white group-hover:text-emerald-300 truncate">
                                        {{ $conv->customer_name ?: __('Unknown Contact') }}
                                    </p>
                                    <p class="text-[11px] text-slate-400 truncate ltr text-start">
                                        {{ $conv->customer_phone }}
                                    </p>
                                </div>
                            </div>

                            <div class="text-end shrink-0">
                                @if($conv->unread_count > 0)
                                    <span class="badge badge-success !px-2 !py-0.5 text-[10px] font-black animate-pulse">
                                        {{ $conv->unread_count }}
                                    </span>
                                @else
                                    <span class="text-[10px] text-slate-500">{{ $conv->last_message_at?->diffForHumans() ?? __('Active') }}</span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6 text-slate-500 text-sm">
                    {{ __('No active WhatsApp conversations.') }}
                </div>
            @endif

            {{-- Projects Quick Overview --}}
            @if($projects->isNotEmpty())
                <div class="pt-3 border-t border-white/5 space-y-2.5">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ __('Portfolio Spotlight') }}</p>
                    @foreach($projects->take(2) as $proj)
                        @php
                            $pTotal = (int) ($proj->units_count ?? 0);
                            $pAvail = (int) ($proj->available_units_count ?? 0);
                            $pSold = (int) ($proj->sold_units_count ?? 0);
                        @endphp
                        <div class="p-3 rounded-xl border border-white/5 bg-white/5 space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-white">{{ $proj->name }}</span>
                            </div>
                            <div class="flex items-center justify-between text-[11px] text-slate-400">
                                <span>{{ $pAvail }} {{ __('available') }} · {{ $pSold }} {{ __('sold') }}</span>
                                <span>{{ $pTotal }} {{ __('total units') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ══════════════════════════════════════════════════════════════
             BENTO ROW 4 — SYSTEM MAINTENANCE & TRASH STATUS
        ══════════════════════════════════════════════════════════════ --}}
        <section class="stagger-item app-card app-card--gradient !p-4.5 md:col-span-2 xl:col-span-12">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-x-8 gap-y-3">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ __('Plans in Trash') }}</p>
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
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ __('Last Automatic Cleanup') }}</p>
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

            const tickColor = (() => {
                const v = getComputedStyle(document.body).getPropertyValue('--slate-400').trim();
                return v ? `rgb(${v})` : '#64748b';
            })();

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [
                        {
                            label: '{{ __('Deals') }}',
                            data: {!! json_encode($chartDeals) !!},
                            borderColor: '#818cf8',
                            backgroundColor: 'rgba(129, 140, 248, 0.12)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2.5,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#818cf8',
                        },
                        {
                            label: '{{ __('Leads') }}',
                            data: {!! json_encode($chartLeads) !!},
                            borderColor: '#38bdf8',
                            backgroundColor: 'rgba(56, 189, 248, 0.10)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2.5,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#38bdf8',
                        },
                        {
                            label: '{{ __('Offers') }}',
                            data: {!! json_encode($chartOffers) !!},
                            borderColor: '#34d399',
                            backgroundColor: 'rgba(52, 211, 153, 0.08)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2.5,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#34d399',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: window.innerWidth >= 768 ? { duration: 700, easing: 'easeOutQuart' } : false,
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
                            borderColor: 'rgba(255,255,255,0.12)',
                            borderWidth: 1,
                            padding: 12,
                            cornerRadius: 12,
                            displayColors: true,
                            bodyFont: { family: 'Cairo', size: 12 },
                            titleFont: { family: 'Cairo', size: 12, weight: 'bold' }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: tickColor, font: { size: 11, family: 'Cairo' }, maxRotation: 0 }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(255, 255, 255, 0.05)', drawBorder: false },
                            ticks: {
                                color: tickColor,
                                font: { size: 11, family: 'Cairo' },
                                padding: 8,
                                precision: 0
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
@endsection
