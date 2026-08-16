@extends('layouts.dashboard')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <section class="dashboard-hero-card p-5 sm:p-6">
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-emerald-500/20 blur-3xl"></div>
            <div class="relative flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-500/15 text-emerald-400">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21.21 15.89A10 10 0 1 1 8 2.83" stroke-linecap="round"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>
                    </span>
                    <div>
                        <h1 class="text-lg font-bold text-white">{{ __('WhatsApp Team Performance') }}</h1>
                        <p class="text-xs text-slate-400">{{ __('Conversations, response time, lead conversion and deals per sales rep.') }}</p>
                    </div>
                </div>
                <div class="ms-auto flex items-center gap-2">
                    <form method="GET" class="flex items-center gap-2">
                        <select name="days" onchange="this.form.submit()" class="rounded-xl border border-white/10 bg-slate-950/60 px-3 py-1.5 text-xs font-semibold text-slate-200 focus:border-brand-500 focus:outline-none">
                            <option value="7" @selected($days === 7)>@lang('Last 7 days')</option>
                            <option value="30" @selected($days === 30)>@lang('Last 30 days')</option>
                            <option value="90" @selected($days === 90)>@lang('Last 90 days')</option>
                            <option value="365" @selected($days === 365)>@lang('Last year')</option>
                        </select>
                    </form>
                    <a href="{{ route('dashboard.whatsapp.index') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:bg-white/10">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        {{ __('Back to panel') }}
                    </a>
                </div>
            </div>
        </section>

        {{-- Summary cards --}}
        <div class="grid grid-cols-2 gap-3 lg:grid-cols-5">
            <div class="app-card app-card--gradient flex items-center gap-3 !p-4">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-400">
                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <div class="min-w-0">
                    <p class="text-xl font-extrabold tabular-nums text-white">{{ number_format($totals['conversations']) }}</p>
                    <p class="truncate text-[11px] text-slate-400">{{ __('Conversations') }}</p>
                </div>
            </div>
            <div class="app-card app-card--gradient flex items-center gap-3 !p-4">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-brand-500/15 text-brand-400">
                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-linecap="round"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6M23 11h-6" stroke-linecap="round"/></svg>
                </span>
                <div class="min-w-0">
                    <p class="text-xl font-extrabold tabular-nums text-white">{{ number_format($totals['leads']) }}</p>
                    <p class="truncate text-[11px] text-slate-400">{{ __('Leads from WhatsApp') }}</p>
                </div>
            </div>
            <div class="app-card app-card--gradient flex items-center gap-3 !p-4">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-violet-500/15 text-violet-400">
                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 4h11a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H9z" stroke-linecap="round"/><path d="M4 20a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2v16z" stroke-linecap="round"/></svg>
                </span>
                <div class="min-w-0">
                    <p class="text-xl font-extrabold tabular-nums text-white">{{ number_format($totals['deals']) }}</p>
                    <p class="truncate text-[11px] text-slate-400">{{ __('Deals') }}</p>
                </div>
            </div>
            <div class="app-card app-card--gradient flex items-center gap-3 !p-4">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-400">
                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" stroke-linecap="round"/><path d="M22 4 12 14.01l-3-3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <div class="min-w-0">
                    <p class="text-xl font-extrabold tabular-nums text-white">{{ number_format($totals['deals_won']) }}</p>
                    <p class="truncate text-[11px] text-slate-400">{{ __('Deals won') }}</p>
                </div>
            </div>
            <div class="app-card app-card--gradient flex items-center gap-3 !p-4 col-span-2 lg:col-span-1">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-500/15 text-amber-400">
                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-linecap="round"/></svg>
                </span>
                <div class="min-w-0">
                    <p class="text-xl font-extrabold tabular-nums text-white">{{ number_format($totals['deal_value']) }} <span class="text-xs font-semibold text-slate-400">{{ __('ج.م') }}</span></p>
                    <p class="truncate text-[11px] text-slate-400">{{ __('Deal value') }}</p>
                </div>
            </div>
        </div>

        {{-- Per-rep breakdown --}}
        <section class="app-card app-card--gradient">
            <div class="flex items-center justify-between border-b border-white/10 pb-3">
                <h2 class="text-sm font-bold text-white">{{ __('Sales reps breakdown') }}</h2>
                @if (!$canManage)
                    <span class="rounded-lg bg-white/5 px-2 py-1 text-[10px] font-semibold text-slate-400">{{ __('Showing your stats only') }}</span>
                @endif
            </div>

            @if (count($rows) === 0)
                <div class="flex flex-col items-center gap-2 py-14 text-center">
                    <svg class="h-10 w-10 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-linecap="round"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-linecap="round"/></svg>
                    <p class="text-sm text-slate-500">{{ __('No activity in this period yet.') }}</p>
                </div>
            @else
                <div class="mt-4 space-y-3">
                    @foreach ($rows as $row)
                        <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-500/15 text-sm font-bold text-emerald-300">
                                    {{ mb_substr($row['rep_name'], 0, 1) }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-bold text-white">{{ $row['rep_name'] }}</p>
                                    <div class="mt-1 flex items-center gap-4 text-[11px] text-slate-400">
                                        <span>{{ __('Response') }}: <strong class="text-slate-200">{{ $row['avg_response_minutes'] !== null ? $row['avg_response_minutes'].' '.__('min') : '—' }}</strong></span>
                                        <span>{{ __('Conversion') }}: <strong class="text-emerald-300">{{ $row['lead_rate'] }}%</strong></span>
                                        <span>{{ __('Leads → Deals') }}: <strong class="text-violet-300">{{ $row['deal_rate'] }}%</strong></span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-2 gap-2 sm:grid-cols-6">
                                <div class="rounded-xl bg-white/5 px-3 py-2 text-center">
                                    <p class="text-lg font-extrabold tabular-nums text-white">{{ number_format($row['conversations']) }}</p>
                                    <p class="text-[10px] text-slate-500">{{ __('Conversations') }}</p>
                                </div>
                                <div class="rounded-xl bg-white/5 px-3 py-2 text-center">
                                    <p class="text-lg font-extrabold tabular-nums text-white">{{ number_format($row['messages_sent']) }}</p>
                                    <p class="text-[10px] text-slate-500">{{ __('Messages') }}</p>
                                </div>
                                <div class="rounded-xl bg-white/5 px-3 py-2 text-center">
                                    <p class="text-lg font-extrabold tabular-nums text-brand-300">{{ number_format($row['leads']) }}</p>
                                    <p class="text-[10px] text-slate-500">{{ __('Leads') }}</p>
                                </div>
                                <div class="rounded-xl bg-white/5 px-3 py-2 text-center">
                                    <p class="text-lg font-extrabold tabular-nums text-violet-300">{{ number_format($row['deals']) }}</p>
                                    <p class="text-[10px] text-slate-500">{{ __('Deals') }}</p>
                                </div>
                                <div class="rounded-xl bg-white/5 px-3 py-2 text-center">
                                    <p class="text-lg font-extrabold tabular-nums text-emerald-300">{{ number_format($row['deals_won']) }}</p>
                                    <p class="text-[10px] text-slate-500">{{ __('Won') }}</p>
                                </div>
                                <div class="rounded-xl bg-white/5 px-3 py-2 text-center">
                                    <p class="text-lg font-extrabold tabular-nums text-amber-300">{{ number_format($row['deal_value']) }}</p>
                                    <p class="text-[10px] text-slate-500">{{ __('Value (ج.م)') }}</p>
                                </div>
                            </div>

                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                <div>
                                    <div class="mb-1 flex items-center justify-between text-[10px] text-slate-400">
                                        <span>{{ __('Conversations → Leads') }}</span>
                                        <span class="font-bold text-emerald-300">{{ $row['lead_rate'] }}%</span>
                                    </div>
                                    <div class="h-1.5 overflow-hidden rounded-full bg-white/5">
                                        <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-400" style="width: {{ min(100, $row['lead_rate']) }}%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="mb-1 flex items-center justify-between text-[10px] text-slate-400">
                                        <span>{{ __('Leads → Deals') }}</span>
                                        <span class="font-bold text-violet-300">{{ $row['deal_rate'] }}%</span>
                                    </div>
                                    <div class="h-1.5 overflow-hidden rounded-full bg-white/5">
                                        <div class="h-full rounded-full bg-gradient-to-r from-violet-500 to-fuchsia-400" style="width: {{ min(100, $row['deal_rate']) }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection
