@extends('layouts.dashboard')

@section('content')
    <div class="space-y-6">
        <section class="dashboard-hero-card p-6 sm:p-8">
            <div class="relative flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="mobile-section-title">{{ __('Sales Management') }}</p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('Sales Scorecard') }}</h1>
                    <p class="mt-2 text-sm text-slate-400">{{ __('Automatic monthly evaluation: composite score and grade per salesperson.') }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <form method="GET" class="flex items-center gap-1">
                        <a href="{{ route('dashboard.sales-evaluations.index', ['period' => $prevPeriod]) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-slate-300 transition hover:bg-white/10" aria-label="{{ __('Previous month') }}">
                            <svg class="h-4 w-4 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                        <input type="month" name="period" value="{{ $period }}" onchange="this.form.submit()" class="rounded-xl border border-white/10 bg-slate-950/60 px-3 py-2 text-xs font-semibold text-slate-200 focus:border-brand-500 focus:outline-none">
                        <a href="{{ route('dashboard.sales-evaluations.index', ['period' => $nextPeriod]) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-slate-300 transition hover:bg-white/10" aria-label="{{ __('Next month') }}">
                            <svg class="h-4 w-4 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                    </form>
                    @if ($isCurrentMonth)
                        <span class="rounded-xl border border-amber-500/20 bg-amber-500/10 px-3 py-2 text-xs font-semibold text-amber-300">{{ __('Live month — scores update nightly') }}</span>
                    @endif
                </div>
            </div>
        </section>

        {{-- Grade distribution --}}
        <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
            @foreach (['A' => 'from-emerald-500 to-teal-400 text-emerald-300 bg-emerald-500/10', 'B' => 'from-sky-500 to-cyan-400 text-sky-300 bg-sky-500/10', 'C' => 'from-amber-500 to-orange-400 text-amber-300 bg-amber-500/10', 'D' => 'from-rose-500 to-red-400 text-rose-300 bg-rose-500/10'] as $grade => $style)
                <div class="app-card app-card--gradient flex items-center gap-3 !p-4">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-lg font-extrabold {{ $style }}">{{ $grade }}</span>
                    <div>
                        <p class="text-2xl font-extrabold tabular-nums text-white">{{ $counts[$grade] }}</p>
                        <p class="text-[11px] text-slate-400">{{ __('salespeople') }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Evaluations table --}}
        <section class="app-card app-card--gradient overflow-hidden">
            <div class="flex items-center justify-between border-b border-white/10 px-4 py-3">
                <h2 class="text-sm font-bold uppercase tracking-wide text-white">{{ __('Monthly evaluation — :period', ['period' => $period]) }}</h2>
                <span class="text-xs text-slate-500">{{ __('relative to the best performer') }}</span>
            </div>

            @if ($evaluations->isEmpty())
                <div class="flex flex-col items-center gap-2 py-14 text-center">
                    <svg class="h-10 w-10 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-linecap="round"/></svg>
                    <p class="text-sm text-slate-500">{{ __('No evaluations yet for this month.') }}</p>
                    <p class="text-xs text-slate-600">{{ __('The scorecard is computed automatically — run `php artisan sales:scorecard` or wait for the nightly schedule.') }}</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[640px] text-sm">
                        <thead>
                            <tr class="border-b border-white/10 text-left text-xs uppercase tracking-wide text-slate-500">
                                <th class="px-4 py-2.5 font-semibold">#</th>
                                <th class="px-2 py-2.5 font-semibold">{{ __('Salesperson') }}</th>
                                <th class="px-2 py-2.5 text-center font-semibold">{{ __('Leads') }}</th>
                                <th class="px-2 py-2.5 text-center font-semibold">{{ __('Offers') }}</th>
                                <th class="px-2 py-2.5 text-center font-semibold">{{ __('Res.') }}</th>
                                <th class="px-2 py-2.5 text-center font-semibold">{{ __('Won') }}</th>
                                <th class="px-2 py-2.5 text-center font-semibold">{{ __('Value') }}</th>
                                <th class="px-2 py-2.5 text-center font-semibold">{{ __('Response') }}</th>
                                <th class="px-2 py-2.5 text-center font-semibold">{{ __('Score') }}</th>
                                <th class="px-4 py-2.5 text-right font-semibold">{{ __('Grade') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($evaluations as $index => $evaluation)
                                @php
                                    $m = $evaluation->metrics ?? [];
                                    $gradeStyles = [
                                        'A' => 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30',
                                        'B' => 'bg-sky-500/15 text-sky-300 border-sky-500/30',
                                        'C' => 'bg-amber-500/15 text-amber-300 border-amber-500/30',
                                        'D' => 'bg-rose-500/15 text-rose-300 border-rose-500/30',
                                    ];
                                @endphp
                                <tr class="border-b border-white/5 last:border-0 hover:bg-white/[0.03]">
                                    <td class="px-4 py-3 text-slate-500">{{ $index + 1 }}</td>
                                    <td class="px-2 py-3">
                                        <p class="font-bold text-white">{{ $evaluation->user?->name ?? __('Deleted user') }}</p>
                                        @if ($evaluation->user?->job_title)
                                            <p class="text-[11px] text-slate-500">{{ $evaluation->user->job_title }}</p>
                                        @endif
                                    </td>
                                    <td class="px-2 py-3 text-center tabular-nums text-brand-300">{{ number_format($m['leads'] ?? 0) }}</td>
                                    <td class="px-2 py-3 text-center tabular-nums text-sky-300">{{ number_format($m['offers'] ?? 0) }}</td>
                                    <td class="px-2 py-3 text-center tabular-nums text-indigo-300">{{ number_format($m['reservations'] ?? 0) }}</td>
                                    <td class="px-2 py-3 text-center tabular-nums text-emerald-300">{{ number_format($m['deals_won'] ?? 0) }}</td>
                                    <td class="px-2 py-3 text-center tabular-nums text-amber-300">{{ number_format($m['deal_value'] ?? 0) }}</td>
                                    <td class="px-2 py-3 text-center tabular-nums text-slate-300">{{ ($m['avg_response_minutes'] ?? null) !== null ? $m['avg_response_minutes'].' '.__('min') : '—' }}</td>
                                    <td class="px-2 py-3 text-center font-extrabold tabular-nums text-white">{{ number_format((float) $evaluation->score, 1) }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl border text-sm font-extrabold {{ $gradeStyles[$evaluation->grade] ?? 'bg-white/5 text-slate-300 border-white/10' }}">{{ $evaluation->grade ?? '—' }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <p class="text-center text-[11px] text-slate-600">{{ __('Grade: A ≥ 80% of the period best, B ≥ 60%, C ≥ 40%, D below.') }}</p>
    </div>
@endsection
