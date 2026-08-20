@extends('layouts.dashboard')

@section('content')
    <div class="space-y-6">
        {{-- Header + month navigation --}}
        <section class="dashboard-hero-card p-6 sm:p-8">
            <div class="relative flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="mobile-section-title">{{ __('Sales Management') }}</p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('Monthly Sales Targets') }}</h1>
                    <p class="mt-2 text-sm text-slate-400">{{ __('Set targets per salesperson or per team and track achievement.') }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <form method="GET" class="flex items-center gap-1">
                        <a href="{{ route('dashboard.sales-targets.index', ['period' => $prevPeriod]) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-slate-300 transition hover:bg-white/10" aria-label="{{ __('Previous month') }}">
                            <svg class="h-4 w-4 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                        <input type="month" name="period" value="{{ $period }}" onchange="this.form.submit()" class="rounded-xl border border-white/10 bg-slate-950/60 px-3 py-2 text-xs font-semibold text-slate-200 focus:border-brand-500 focus:outline-none">
                        <a href="{{ route('dashboard.sales-targets.index', ['period' => $nextPeriod]) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-slate-300 transition hover:bg-white/10" aria-label="{{ __('Next month') }}">
                            <svg class="h-4 w-4 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                    </form>
                    @can('manage teams')
                        <a href="{{ route('dashboard.sales-targets.create', ['period' => $period]) }}" class="app-button shrink-0">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14" stroke-linecap="round"/></svg>
                            {{ __('New Target') }}
                        </a>
                    @endcan
                </div>
            </div>
        </section>

        {{-- Targets list --}}
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            @forelse ($targets as $target)
                @php
                    $a = $target->actuals;
                    $isTeam = $target->sales_team_id !== null;
                    $entityName = $isTeam ? $target->team?->name : $target->user?->name;
                    $targetsList = [
                        ['key' => 'leads', 'label' => __('Leads'), 'actual' => (int) $a['leads'], 'target' => (int) $target->leads_target, 'color' => 'from-brand-500 to-indigo-400'],
                        ['key' => 'offers', 'label' => __('Offers'), 'actual' => (int) $a['offers'], 'target' => (int) $target->offers_target, 'color' => 'from-sky-500 to-cyan-400'],
                        ['key' => 'reservations', 'label' => __('Reservations'), 'actual' => (int) $a['reservations'], 'target' => (int) $target->reservations_target, 'color' => 'from-indigo-500 to-violet-400'],
                        ['key' => 'deals', 'label' => __('Deals won'), 'actual' => (int) $a['deals_won'], 'target' => (int) $target->deals_target, 'color' => 'from-emerald-500 to-teal-400'],
                    ];
                @endphp
                <article class="app-card app-card--gradient flex flex-col p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="badge {{ $isTeam ? 'badge-violet' : 'badge-brand' }}">{{ $isTeam ? __('Team') : __('Salesperson') }}</span>
                                @if ($isTeam)
                                    <span class="text-[10px] text-slate-500">{{ $target->team?->members->count() }} {{ __('members') }}</span>
                                @endif
                            </div>
                            <h2 class="mt-2 truncate text-lg font-bold text-white">{{ $entityName ?? __('Deleted entity') }}</h2>
                        </div>
                        <div class="flex shrink-0 items-center gap-2">
                            <a href="{{ route('dashboard.sales-targets.edit', $target) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-slate-300 transition hover:bg-white/10" title="{{ __('Edit') }}">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('dashboard.sales-targets.destroy', $target) }}" class="contents" onsubmit="return confirm('{{ __('Delete this target?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-rose-500/20 bg-rose-500/10 text-rose-300 transition hover:bg-rose-500/20" title="{{ __('Delete') }}">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-linecap="round"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Deal value target --}}
                    @php
                        $valueTarget = (float) $target->deal_value_target;
                        $valueActual = (float) $a['deal_value'];
                        $valuePct = $valueTarget > 0 ? min(120, round($valueActual / $valueTarget * 100)) : 0;
                    @endphp
                    @if ($valueTarget > 0)
                        <div class="mt-4 rounded-2xl border border-amber-500/20 bg-amber-500/5 p-3">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-semibold text-amber-300">{{ __('Deal value target') }}</span>
                                <span class="tabular-nums text-slate-300">
                                    <strong class="text-amber-300">{{ number_format($valueActual) }}</strong> / {{ number_format($valueTarget) }} {{ __('ج.م') }}
                                    <span class="ms-1 rounded-full bg-amber-500/15 px-2 py-0.5 text-[10px] font-bold {{ $valuePct >= 100 ? 'text-emerald-300' : 'text-amber-300' }}">{{ $valuePct }}%</span>
                                </span>
                            </div>
                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-white/10">
                                <div class="h-full rounded-full bg-gradient-to-r from-amber-500 to-orange-400" style="width: {{ min(100, $valuePct) }}%"></div>
                            </div>
                        </div>
                    @endif

                    <div class="mt-4 grid grid-cols-2 gap-3">
                        @foreach ($targetsList as $item)
                            @php($pct = $item['target'] > 0 ? min(120, round($item['actual'] / $item['target'] * 100)) : 0)
                            <div>
                                <div class="mb-1 flex items-center justify-between text-[11px]">
                                    <span class="text-slate-400">{{ $item['label'] }}</span>
                                    <span class="tabular-nums font-semibold {{ $pct >= 100 ? 'text-emerald-300' : ($pct >= 50 ? 'text-amber-300' : 'text-slate-300') }}">
                                        {{ $item['actual'] }}<span class="text-slate-500">/{{ $item['target'] }}</span>
                                    </span>
                                </div>
                                <div class="h-1.5 overflow-hidden rounded-full bg-white/10">
                                    <div class="h-full rounded-full bg-gradient-to-r {{ $item['color'] }}" style="width: {{ min(100, $pct) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </article>
            @empty
                <div class="app-card col-span-full py-14 text-center text-slate-400">
                    <p class="text-lg font-semibold text-white">{{ __('No targets for this month') }}</p>
                    <p class="mt-1 text-sm">{{ __('Set targets to start tracking achievement.') }}</p>
                    @can('manage teams')
                        <a href="{{ route('dashboard.sales-targets.create', ['period' => $period]) }}" class="app-button mt-5 inline-flex">{{ __('New Target') }}</a>
                    @endcan
                </div>
            @endforelse
        </div>

        <p class="text-center text-[11px] text-slate-600">{{ __('Achievement is computed from actual activity inside the selected month.') }}</p>
    </div>
@endsection
