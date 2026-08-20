@extends('layouts.dashboard')

@section('content')
    @php
        $toggles = [
            'lead_auto_assign' => ['lead_auto_assign', __('Automatic lead distribution')],
            'sla_enabled' => ['sla_enabled', __('Response SLA')],
            'followup_alerts_enabled' => ['followup_alerts_enabled', __('Overdue follow-up alerts')],
            'weekly_report_enabled' => ['weekly_report_enabled', __('Weekly WhatsApp report')],
            'scorecard_enabled' => ['scorecard_enabled', __('Monthly scorecard')],
        ];
        $enabledCount = collect($toggles)->keys()->filter(fn ($key) => (bool) ($settings[$key] ?? '1'))->count();
    @endphp

    <div class="space-y-4">
        {{-- Header --}}
        <section class="dashboard-hero-card p-4 sm:p-6">
            <div class="relative flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">{{ __('Sales Automation') }}</h1>
                    <p class="mt-1 text-sm text-slate-400">{{ __('Enable or disable each automation and tune its thresholds.') }}</p>
                </div>
                <span class="inline-flex shrink-0 items-center gap-2 rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-xs text-slate-400">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-400"></span>
                    {{ __('Dispatcher runs every 5 minutes') }}
                </span>
            </div>
        </section>

        <form method="POST" action="{{ route('dashboard.automation.update') }}" class="grid grid-cols-1 gap-4 xl:grid-cols-2">
            @csrf
            @method('PUT')

            {{-- 1. Lead distribution --}}
            <section class="app-card app-card--gradient flex flex-col justify-between p-5">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-500/15 text-brand-400">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-linecap="round"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-linecap="round"/><path d="M12 4h6M15 1v6" stroke-linecap="round"/></svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <h2 class="text-sm font-bold text-white">{{ __('Automatic lead distribution') }}</h2>
                        <p class="mt-1 text-xs leading-relaxed text-slate-400">{{ __('New leads without a salesperson are routed to the least-loaded member.') }}</p>
                    </div>
                    <label class="relative inline-flex shrink-0 cursor-pointer items-center">
                        <input type="checkbox" name="lead_auto_assign" value="1" class="peer sr-only" @checked((bool) ($settings['lead_auto_assign'] ?? '1'))>
                        <span class="h-7 w-12 rounded-full bg-white/10 transition peer-checked:bg-brand-600 after:absolute after:start-1 after:top-1 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition peer-checked:after:translate-x-5 rtl:peer-checked:after:-translate-x-5"></span>
                    </label>
                </div>
            </section>

            {{-- 2. Follow-up alerts --}}
            <section class="app-card app-card--gradient flex flex-col justify-between p-5">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-500/15 text-amber-400">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" stroke-linecap="round" stroke-linejoin="round"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <h2 class="text-sm font-bold text-white">{{ __('Overdue follow-up alerts') }}</h2>
                        <p class="mt-1 text-xs leading-relaxed text-slate-400">{{ __('Notify salesperson + managers when a follow-up deadline passes.') }}</p>
                    </div>
                    <label class="relative inline-flex shrink-0 cursor-pointer items-center">
                        <input type="checkbox" name="followup_alerts_enabled" value="1" class="peer sr-only" @checked((bool) ($settings['followup_alerts_enabled'] ?? '1'))>
                        <span class="h-7 w-12 rounded-full bg-white/10 transition peer-checked:bg-brand-600 after:absolute after:start-1 after:top-1 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition peer-checked:after:translate-x-5 rtl:peer-checked:after:-translate-x-5"></span>
                    </label>
                </div>
            </section>

            {{-- 3. Response SLA --}}
            <section class="app-card app-card--gradient p-5">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-500/15 text-rose-400">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9" stroke-linecap="round"/><path d="M12 7v5l3 3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <h2 class="text-sm font-bold text-white">{{ __('Response SLA') }}</h2>
                        <p class="mt-1 text-xs leading-relaxed text-slate-400">{{ __('Alert managers on long waits, then auto re-assign to another salesperson.') }}</p>
                    </div>
                    <label class="relative inline-flex shrink-0 cursor-pointer items-center">
                        <input type="checkbox" name="sla_enabled" value="1" class="peer sr-only" @checked((bool) ($settings['sla_enabled'] ?? '1'))>
                        <span class="h-7 w-12 rounded-full bg-white/10 transition peer-checked:bg-brand-600 after:absolute after:start-1 after:top-1 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition peer-checked:after:translate-x-5 rtl:peer-checked:after:-translate-x-5"></span>
                    </label>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <div>
                        <label for="sla_alert_minutes" class="mb-1.5 block text-xs font-medium text-slate-300">{{ __('Alert after (min)') }}</label>
                        <input type="number" id="sla_alert_minutes" name="sla_alert_minutes" min="1" max="1440" class="app-input" value="{{ old('sla_alert_minutes', $settings['sla_alert_minutes'] ?? '30') }}">
                        @error('sla_alert_minutes') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="sla_escalate_minutes" class="mb-1.5 block text-xs font-medium text-slate-300">{{ __('Re-assign after (min)') }}</label>
                        <input type="number" id="sla_escalate_minutes" name="sla_escalate_minutes" min="2" max="10080" class="app-input" value="{{ old('sla_escalate_minutes', $settings['sla_escalate_minutes'] ?? '120') }}">
                        @error('sla_escalate_minutes') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            {{-- 4. Weekly report --}}
            <section class="app-card app-card--gradient p-5">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-400">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 3v18h18" stroke-linecap="round" stroke-linejoin="round"/><path d="M7 15l4-5 3 3 5-7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <h2 class="text-sm font-bold text-white">{{ __('Weekly WhatsApp report') }}</h2>
                        <p class="mt-1 text-xs leading-relaxed text-slate-400">{{ __('Send the performance summary to the manager\u2019s WhatsApp.') }}</p>
                    </div>
                    <label class="relative inline-flex shrink-0 cursor-pointer items-center">
                        <input type="checkbox" name="weekly_report_enabled" value="1" class="peer sr-only" @checked((bool) ($settings['weekly_report_enabled'] ?? '1'))>
                        <span class="h-7 w-12 rounded-full bg-white/10 transition peer-checked:bg-brand-600 after:absolute after:start-1 after:top-1 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition peer-checked:after:translate-x-5 rtl:peer-checked:after:-translate-x-5"></span>
                    </label>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <div>
                        <label for="weekly_report_day" class="mb-1.5 block text-xs font-medium text-slate-300">{{ __('Day of week') }}</label>
                        <select id="weekly_report_day" name="weekly_report_day" class="app-input">
                            @foreach ([0 => __('Sunday'), 1 => __('Monday'), 2 => __('Tuesday'), 3 => __('Wednesday'), 4 => __('Thursday'), 5 => __('Friday'), 6 => __('Saturday')] as $day => $label)
                                <option value="{{ $day }}" @selected((int) ($settings['weekly_report_day'] ?? '1') === $day)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="weekly_report_time" class="mb-1.5 block text-xs font-medium text-slate-300">{{ __('Time') }}</label>
                        <input type="time" id="weekly_report_time" name="weekly_report_time" class="app-input" value="{{ old('weekly_report_time', $settings['weekly_report_time'] ?? '10:00') }}">
                        @error('weekly_report_time') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            {{-- 5. Scorecard --}}
            <section class="app-card app-card--gradient p-5">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-500/15 text-violet-400">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 3h6M10 3v3.5a4 4 0 0 1-4 4v4a4 4 0 0 1 4 4V22M14 3v3.5a4 4 0 0 0 4 4v4a4 4 0 0 0-4 4V22" stroke-linecap="round"/></svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <h2 class="text-sm font-bold text-white">{{ __('Monthly scorecard') }}</h2>
                        <p class="mt-1 text-xs leading-relaxed text-slate-400">{{ __('Compute the previous month\u2019s evaluations (score + A/B/C/D) automatically.') }}</p>
                    </div>
                    <label class="relative inline-flex shrink-0 cursor-pointer items-center">
                        <input type="checkbox" name="scorecard_enabled" value="1" class="peer sr-only" @checked((bool) ($settings['scorecard_enabled'] ?? '1'))>
                        <span class="h-7 w-12 rounded-full bg-white/10 transition peer-checked:bg-brand-600 after:absolute after:start-1 after:top-1 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition peer-checked:after:translate-x-5 rtl:peer-checked:after:-translate-x-5"></span>
                    </label>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <div>
                        <label for="scorecard_day" class="mb-1.5 block text-xs font-medium text-slate-300">{{ __('Day of month') }}</label>
                        <input type="number" id="scorecard_day" name="scorecard_day" min="1" max="28" class="app-input" value="{{ old('scorecard_day', $settings['scorecard_day'] ?? '1') }}">
                        @error('scorecard_day') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="scorecard_time" class="mb-1.5 block text-xs font-medium text-slate-300">{{ __('Time') }}</label>
                        <input type="time" id="scorecard_time" name="scorecard_time" class="app-input" value="{{ old('scorecard_time', $settings['scorecard_time'] ?? '02:00') }}">
                        @error('scorecard_time') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            {{-- Status summary --}}
            <section class="app-card app-card--gradient flex flex-col justify-between p-5">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-500/15 text-slate-300">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <h2 class="text-sm font-bold text-white">{{ __('Automation status') }}</h2>
                        <p class="mt-1 text-xs leading-relaxed text-slate-400">{{ __('Active automations out of five.') }}</p>
                    </div>
                    <span class="shrink-0 rounded-xl bg-brand-500/10 px-3 py-1.5 text-lg font-extrabold tabular-nums text-brand-300">{{ $enabledCount }}<span class="text-xs font-semibold text-slate-500">/5</span></span>
                </div>
                <ul class="mt-4 space-y-2">
                    @foreach ($toggles as [$key, $label])
                        <li class="flex items-center justify-between gap-2 text-xs">
                            <span class="text-slate-300">{{ $label }}</span>
                            <span class="inline-flex items-center gap-1.5">
                                @if ((bool) ($settings[$key] ?? '1'))
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                                    <span class="font-semibold text-emerald-300">{{ __('On') }}</span>
                                @else
                                    <span class="h-1.5 w-1.5 rounded-full bg-slate-500"></span>
                                    <span class="font-semibold text-slate-400">{{ __('Off') }}</span>
                                @endif
                            </span>
                        </li>
                    @endforeach
                </ul>
            </section>

            @if ($errors->has('sla_escalate_minutes'))
                <div class="rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200 xl:col-span-2">{{ $errors->first('sla_escalate_minutes') }}</div>
            @endif

            <div class="flex flex-col items-center justify-between gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 sm:flex-row xl:col-span-2">
                <p class="text-xs text-slate-500">{{ __('Disabled automations stay available for manual runs via `php artisan`.') }}</p>
                <button type="submit" class="app-button shrink-0">{{ __('Save automation settings') }}</button>
            </div>
        </form>
    </div>
@endsection
