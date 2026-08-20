@extends('layouts.dashboard')

@section('content')
    @php
        $repTeamMap = collect();
        foreach ($teams as $team) {
            foreach ($team->members as $member) {
                $repTeamMap[$member->id] = $team->name;
            }
        }
    @endphp

    <div class="space-y-4">
        {{-- Header + filters --}}
        <section class="dashboard-hero-card p-4 sm:p-6">
            <div class="relative flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">{{ __('Sales Performance') }}</h1>
                    <p class="mt-1 text-sm text-slate-400">{{ __('WhatsApp activity + CRM results, per salesperson and team.') }}</p>
                </div>
                <form method="GET" class="flex flex-wrap items-center gap-2">
                    <select name="days" onchange="this.form.submit()" class="rounded-xl border border-white/10 bg-slate-950/60 px-3 py-2 text-sm font-semibold text-slate-200 focus:border-brand-500 focus:outline-none">
                        <option value="7" @selected($days === 7)>@lang('Last 7 days')</option>
                        <option value="30" @selected($days === 30)>@lang('Last 30 days')</option>
                        <option value="90" @selected($days === 90)>@lang('Last 90 days')</option>
                        <option value="365" @selected($days === 365)>@lang('Last year')</option>
                    </select>
                    <select name="team_id" onchange="this.form.submit()" class="rounded-xl border border-white/10 bg-slate-950/60 px-3 py-2 text-sm font-semibold text-slate-200 focus:border-brand-500 focus:outline-none">
                        <option value="0">@lang('All teams')</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}" @selected($teamId === $team->id)>{{ $team->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </section>

        {{-- Summary strip (responsive: 2 cols mobile / 6 desktop) --}}
        <div class="grid grid-cols-2 gap-2 sm:grid-cols-6">
            <div class="app-card app-card--gradient flex items-center gap-2.5 !p-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-400">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <div class="min-w-0"><p class="whitespace-nowrap text-lg font-extrabold tabular-nums text-white">{{ number_format($totals['conversations']) }}</p><p class="text-[11px] leading-tight text-slate-400">{{ __('Conversations') }}</p></div>
            </div>
            <div class="app-card app-card--gradient flex items-center gap-2.5 !p-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-brand-500/15 text-brand-400">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-linecap="round"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6M23 11h-6" stroke-linecap="round"/></svg>
                </span>
                <div class="min-w-0"><p class="whitespace-nowrap text-lg font-extrabold tabular-nums text-white">{{ number_format($totals['leads']) }}</p><p class="text-[11px] leading-tight text-slate-400">{{ __('Leads') }}</p></div>
            </div>
            <div class="app-card app-card--gradient flex items-center gap-2.5 !p-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-sky-500/15 text-sky-400">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" stroke-linecap="round"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
                </span>
                <div class="min-w-0"><p class="whitespace-nowrap text-lg font-extrabold tabular-nums text-white">{{ number_format($totals['offers']) }}</p><p class="text-[11px] leading-tight text-slate-400">{{ __('Offers') }}</p></div>
            </div>
            <div class="app-card app-card--gradient flex items-center gap-2.5 !p-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-500/15 text-indigo-400">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" stroke-linecap="round"/><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M9 12h6M9 16h4" stroke-linecap="round"/></svg>
                </span>
                <div class="min-w-0"><p class="whitespace-nowrap text-lg font-extrabold tabular-nums text-white">{{ number_format($totals['reservations']) }}</p><p class="text-[11px] leading-tight text-slate-400">{{ __('Reservations') }}</p></div>
            </div>
            <div class="app-card app-card--gradient flex items-center gap-2.5 !p-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-400">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" stroke-linecap="round"/><path d="M22 4 12 14.01l-3-3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <div class="min-w-0"><p class="whitespace-nowrap text-lg font-extrabold tabular-nums text-white">{{ number_format($totals['deals_won']) }}</p><p class="text-[11px] leading-tight text-slate-400">{{ __('Deals won') }}</p></div>
            </div>
            <div class="app-card app-card--gradient col-span-2 flex items-center gap-2.5 !p-3 sm:col-span-1">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-500/15 text-amber-400">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-linecap="round"/></svg>
                </span>
                <div class="min-w-0"><p class="whitespace-nowrap text-lg font-extrabold tabular-nums text-white">{{ number_format($totals['deal_value']) }} <span class="text-sm font-semibold text-slate-400">{{ __('ج.م') }}</span></p><p class="text-[11px] leading-tight text-slate-400">{{ __('Deal value') }}</p></div>
            </div>
        </div>

        {{-- Teams comparison --}}
        @if ($teamMetrics->isNotEmpty())
            <section class="app-card app-card--gradient overflow-hidden">
                <div class="flex items-center justify-between border-b border-white/10 px-4 py-3">
                    <h2 class="text-sm font-bold uppercase tracking-wide text-white">{{ __('Teams') }}</h2>
                    <span class="text-xs text-slate-500">{{ __('sorted by deal value') }}</span>
                </div>

                {{-- Desktop table --}}
                <div class="hidden overflow-x-auto md:block">
                    <table class="w-full min-w-[560px] text-sm">
                        <thead>
                            <tr class="border-b border-white/10 text-left text-xs uppercase tracking-wide text-slate-500">
                                <th class="px-4 py-2.5 font-semibold">{{ __('Team') }}</th>
                                <th class="px-2 py-2.5 text-center font-semibold">{{ __('Members') }}</th>
                                <th class="px-2 py-2.5 text-center font-semibold">{{ __('Leads') }}</th>
                                <th class="px-2 py-2.5 text-center font-semibold">{{ __('Deals') }}</th>
                                <th class="px-2 py-2.5 text-center font-semibold">{{ __('Won') }}</th>
                                <th class="px-2 py-2.5 text-center font-semibold">{{ __('Value') }}</th>
                                <th class="px-4 py-2.5 text-right font-semibold">{{ __('Score') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($teamMetrics as $tm)
                                @php($pct = $teamMetrics->max('deal_value') > 0 ? round($tm['deal_value'] / $teamMetrics->max('deal_value') * 100) : 0)
                                <tr class="border-b border-white/5 last:border-0 hover:bg-white/[0.03]">
                                    <td class="px-4 py-2.5">
                                        <p class="truncate text-sm font-bold text-white">{{ $tm['team']->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $tm['team']->manager?->name ?? __('no manager') }}</p>
                                    </td>
                                    <td class="px-2 py-2.5 text-center text-sm text-slate-300">{{ $tm['team']->members->count() }}</td>
                                    <td class="px-2 py-2.5 text-center text-sm font-bold tabular-nums text-brand-300">{{ number_format($tm['leads']) }}</td>
                                    <td class="px-2 py-2.5 text-center text-sm font-bold tabular-nums text-violet-300">{{ number_format($tm['deals']) }}</td>
                                    <td class="px-2 py-2.5 text-center text-sm font-bold tabular-nums text-emerald-300">{{ number_format($tm['deals_won']) }}</td>
                                    <td class="px-2 py-2.5 text-center text-sm font-bold tabular-nums text-amber-300">{{ number_format($tm['deal_value']) }}</td>
                                    <td class="px-4 py-2.5 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <div class="h-2 w-20 overflow-hidden rounded-full bg-white/5">
                                                <div class="h-full rounded-full bg-gradient-to-r from-amber-500 to-orange-400" style="width: {{ max(2, $pct) }}%"></div>
                                            </div>
                                            <span class="text-sm font-extrabold tabular-nums text-amber-300">{{ number_format($tm['score']) }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile cards --}}
                <div class="space-y-2 p-3 md:hidden">
                    @foreach ($teamMetrics as $tm)
                        @php($pct = $teamMetrics->max('deal_value') > 0 ? round($tm['deal_value'] / $teamMetrics->max('deal_value') * 100) : 0)
                        <div class="rounded-xl border border-white/10 bg-white/[0.03] p-3">
                            <div class="flex items-center justify-between gap-2">
                                <p class="truncate text-sm font-bold text-white">{{ $tm['team']->name }}</p>
                                <span class="text-sm font-extrabold tabular-nums text-amber-300">{{ number_format($tm['score']) }}</span>
                            </div>
                            <p class="text-xs text-slate-500">{{ $tm['team']->manager?->name ?? __('no manager') }} • {{ $tm['team']->members->count() }} {{ __('members') }}</p>
                            <div class="mt-2 grid grid-cols-3 gap-1.5 text-center">
                                <div class="rounded-lg bg-white/5 py-1.5"><p class="text-sm font-bold tabular-nums text-brand-300">{{ number_format($tm['leads']) }}</p><p class="text-[10px] text-slate-500">{{ __('Leads') }}</p></div>
                                <div class="rounded-lg bg-white/5 py-1.5"><p class="text-sm font-bold tabular-nums text-violet-300">{{ number_format($tm['deals']) }}</p><p class="text-[10px] text-slate-500">{{ __('Deals') }}</p></div>
                                <div class="rounded-lg bg-white/5 py-1.5"><p class="text-sm font-bold tabular-nums text-emerald-300">{{ number_format($tm['deals_won']) }}</p><p class="text-[10px] text-slate-500">{{ __('Won') }}</p></div>
                            </div>
                            <div class="mt-2 flex items-center gap-2">
                                <div class="h-2 flex-1 overflow-hidden rounded-full bg-white/5">
                                    <div class="h-full rounded-full bg-gradient-to-r from-amber-500 to-orange-400" style="width: {{ max(2, $pct) }}%"></div>
                                </div>
                                <span class="text-xs font-bold tabular-nums text-amber-300">{{ 'EGP ' . number_format($tm['deal_value']) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Reps leaderboard --}}
        <section class="app-card app-card--gradient overflow-hidden">
            <div class="flex items-center justify-between border-b border-white/10 px-4 py-3">
                <h2 class="text-sm font-bold uppercase tracking-wide text-white">{{ __('Salespeople leaderboard') }}</h2>
                <span class="rounded-lg bg-white/5 px-2.5 py-1 text-xs font-semibold text-slate-400">{{ __('Score = results-weighted') }}</span>
            </div>

            @if ($repMetrics->isEmpty())
                <div class="flex flex-col items-center gap-2 py-10 text-center">
                    <svg class="h-10 w-10 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-linecap="round"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-linecap="round"/></svg>
                    <p class="text-base text-slate-500">{{ __('No activity in this period yet.') }}</p>
                </div>
            @else
                {{-- Desktop table --}}
                <div class="hidden overflow-x-auto md:block">
                    <table class="w-full min-w-[880px] text-sm">
                        <thead>
                            <tr class="border-b border-white/10 text-left text-xs uppercase tracking-wide text-slate-500">
                                <th class="px-3 py-2.5 font-semibold">#</th>
                                <th class="px-2 py-2.5 font-semibold">{{ __('Salesperson') }}</th>
                                <th class="px-2 py-2.5 text-center font-semibold">{{ __('Response') }}</th>
                                <th class="px-2 py-2.5 text-center font-semibold">{{ __('Conv.') }}</th>
                                <th class="px-2 py-2.5 text-center font-semibold">{{ __('Msg') }}</th>
                                <th class="px-2 py-2.5 text-center font-semibold">{{ __('Leads') }}</th>
                                <th class="px-2 py-2.5 text-center font-semibold">{{ __('Offers') }}</th>
                                <th class="px-2 py-2.5 text-center font-semibold">{{ __('Res.') }}</th>
                                <th class="px-2 py-2.5 text-center font-semibold">{{ __('Won') }}</th>
                                <th class="px-2 py-2.5 text-center font-semibold">{{ __('Value') }}</th>
                                <th class="px-3 py-2.5 text-right font-semibold">{{ __('Score') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($repMetrics as $index => $row)
                                <tr class="border-b border-white/5 last:border-0 hover:bg-white/[0.03]">
                                    <td class="px-3 py-2.5">
                                        <span class="flex h-7 w-7 items-center justify-center rounded-lg text-sm font-extrabold {{ $index === 0 ? 'bg-amber-500/20 text-amber-300' : ($index === 1 ? 'bg-slate-400/15 text-slate-200' : ($index === 2 ? 'bg-orange-600/20 text-orange-300' : 'bg-white/5 text-slate-500')) }}" title="{{ __('Rank') }} #{{ $index + 1 }}">#{{ $index + 1 }}</span>
                                    </td>
                                    <td class="px-2 py-2.5">
                                        <div class="flex items-center gap-2.5">
                                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-600/25 text-sm font-bold text-brand-200">
                                                {{ mb_strtoupper(mb_substr($row['user_name'], 0, 1)) }}
                                            </span>
                                            <div class="min-w-0">
                                                <p class="truncate text-sm font-bold text-white">{{ $row['user_name'] }}</p>
                                                <div class="flex items-center gap-1.5">
                                                    @if ($teamName = ($repTeamMap[$row['user_id']] ?? null))
                                                        <span class="rounded bg-brand-500/15 px-1.5 py-px text-[11px] font-semibold text-brand-300">{{ $teamName }}</span>
                                                    @endif
                                                    @if ($row['job_title'])
                                                        <span class="text-[11px] text-slate-500">{{ $row['job_title'] }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-2 py-2.5 text-center text-sm text-slate-400">{{ $row['avg_response_minutes'] !== null ? $row['avg_response_minutes'].' '.__('min') : '—' }}</td>
                                    <td class="px-2 py-2.5 text-center text-sm font-bold tabular-nums text-white">{{ number_format($row['conversations']) }}</td>
                                    <td class="px-2 py-2.5 text-center text-sm font-bold tabular-nums text-white">{{ number_format($row['messages_sent']) }}</td>
                                    <td class="px-2 py-2.5 text-center text-sm font-bold tabular-nums text-brand-300">{{ number_format($row['leads']) }}</td>
                                    <td class="px-2 py-2.5 text-center text-sm font-bold tabular-nums text-sky-300">{{ number_format($row['offers']) }}</td>
                                    <td class="px-2 py-2.5 text-center text-sm font-bold tabular-nums text-indigo-300">{{ number_format($row['reservations']) }}</td>
                                    <td class="px-2 py-2.5 text-center text-sm font-bold tabular-nums text-emerald-300">{{ number_format($row['deals_won']) }}</td>
                                    <td class="px-2 py-2.5 text-center text-sm font-bold tabular-nums text-amber-300">{{ number_format($row['deal_value']) }}</td>
                                    <td class="px-3 py-2.5 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <div class="h-2 w-20 overflow-hidden rounded-full bg-white/5">
                                                <div class="h-full rounded-full bg-gradient-to-r from-brand-500 via-violet-500 to-amber-400" style="width: {{ max(2, round($row['score'] / max(1, $maxScore) * 100)) }}%"></div>
                                            </div>
                                            <span class="text-sm font-extrabold tabular-nums text-amber-300">{{ number_format($row['score']) }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile cards --}}
                <div class="space-y-2 p-3 md:hidden">
                    @foreach ($repMetrics as $index => $row)
                        <div class="rounded-xl border border-white/10 bg-white/[0.03] p-3">
                            <div class="flex items-center gap-2.5">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-sm font-extrabold {{ $index === 0 ? 'bg-amber-500/20 text-amber-300' : ($index === 1 ? 'bg-slate-400/15 text-slate-200' : ($index === 2 ? 'bg-orange-600/20 text-orange-300' : 'bg-white/5 text-slate-500')) }}">#{{ $index + 1 }}</span>
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-600/25 text-base font-bold text-brand-200">{{ mb_strtoupper(mb_substr($row['user_name'], 0, 1)) }}</span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-bold text-white">{{ $row['user_name'] }}</p>
                                    <div class="flex items-center gap-1.5">
                                        @if ($teamName = ($repTeamMap[$row['user_id']] ?? null))
                                            <span class="rounded bg-brand-500/15 px-1.5 py-px text-[11px] font-semibold text-brand-300">{{ $teamName }}</span>
                                        @endif
                                        @if ($row['job_title'])
                                            <span class="truncate text-[11px] text-slate-500">{{ $row['job_title'] }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-base font-extrabold tabular-nums text-amber-300">{{ number_format($row['score']) }}</p>
                                    <p class="text-[10px] text-slate-500">{{ __('Score') }}</p>
                                </div>
                            </div>

                            <div class="mt-2.5 grid grid-cols-3 gap-1.5 text-center">
                                <div class="rounded-lg bg-white/5 py-1.5"><p class="text-sm font-bold tabular-nums text-emerald-300">{{ number_format($row['deals_won']) }}</p><p class="text-[10px] text-slate-500">{{ __('Won') }}</p></div>
                                <div class="rounded-lg bg-white/5 py-1.5"><p class="text-sm font-bold tabular-nums text-amber-300">{{ number_format($row['deal_value']) }}</p><p class="text-[10px] text-slate-500">{{ __('Value') }}</p></div>
                                <div class="rounded-lg bg-white/5 py-1.5"><p class="text-sm font-bold tabular-nums text-white">{{ $row['avg_response_minutes'] !== null ? $row['avg_response_minutes'] : '—' }}</p><p class="text-[10px] text-slate-500">{{ __('Resp. min') }}</p></div>
                            </div>

                            <div class="mt-1.5 grid grid-cols-3 gap-1.5 text-center">
                                <div class="rounded-lg bg-white/5 py-1"><p class="text-sm font-bold tabular-nums text-white">{{ number_format($row['conversations']) }}</p><p class="text-[10px] text-slate-500">{{ __('Conv.') }}</p></div>
                                <div class="rounded-lg bg-white/5 py-1"><p class="text-sm font-bold tabular-nums text-white">{{ number_format($row['messages_sent']) }}</p><p class="text-[10px] text-slate-500">{{ __('Msg') }}</p></div>
                                <div class="rounded-lg bg-white/5 py-1"><p class="text-sm font-bold tabular-nums text-brand-300">{{ number_format($row['leads']) }}</p><p class="text-[10px] text-slate-500">{{ __('Leads') }}</p></div>
                                <div class="rounded-lg bg-white/5 py-1"><p class="text-sm font-bold tabular-nums text-sky-300">{{ number_format($row['offers']) }}</p><p class="text-[10px] text-slate-500">{{ __('Offers') }}</p></div>
                                <div class="rounded-lg bg-white/5 py-1"><p class="text-sm font-bold tabular-nums text-indigo-300">{{ number_format($row['reservations']) }}</p><p class="text-[10px] text-slate-500">{{ __('Res.') }}</p></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <p class="text-center text-xs text-slate-600">{{ __('Score formula: leads ×1 + offers ×3 + reservations ×5 + deals won ×10 + deal value / 50,000 + fast-response bonus.') }}</p>
    </div>
@endsection
