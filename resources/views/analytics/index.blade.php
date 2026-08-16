@extends('layouts.dashboard')

@section('content')
    @php
        // Build a full 14-day series for each chart.
        $visitsSeries = [];
        $otpSeries = [];
        $loginSeries = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $visitsSeries[] = ['label' => now()->subDays($i)->format('d/m'), 'total' => (int) ($dailyVisits[$date] ?? 0)];
            $otpSeries[] = ['label' => now()->subDays($i)->format('d/m'), 'total' => (int) ($otpDaily[$date] ?? 0)];
            $row = $loginDaily->firstWhere('date', $date);
            $loginSeries[] = [
                'label' => now()->subDays($i)->format('d/m'),
                'successful' => (int) ($row->successful ?? 0),
                'failed' => (int) ($row->failed ?? 0),
            ];
        }
        $maxVisits = max(1, max(array_column($visitsSeries, 'total')));
        $maxOtp = max(1, max(array_column($otpSeries, 'total')));
        $maxLogin = max(1, max(array_map(fn ($r) => max($r['successful'], $r['failed']), $loginSeries)));
        $deviceLabels = ['desktop' => __('Desktop'), 'mobile' => __('Mobile'), 'tablet' => __('Tablet'), 'unknown' => __('Unknown')];
        $deviceColors = ['desktop' => 'bg-sky-500', 'mobile' => 'bg-brand-500', 'tablet' => 'bg-violet-500', 'unknown' => 'bg-slate-500'];
        $countryColors = ['bg-blue-500', 'bg-violet-500', 'bg-emerald-500', 'bg-amber-500', 'bg-rose-500', 'bg-sky-500', 'bg-brand-500'];
    @endphp

    <div class="space-y-6">
        <section class="dashboard-hero-card p-6 sm:p-8">
            <div class="absolute -right-16 -top-16 h-48 w-48 rounded-full bg-brand-500/20 blur-3xl"></div>
            <div class="absolute -bottom-16 left-1/3 h-48 w-48 rounded-full bg-violet-500/10 blur-3xl"></div>

            <div class="relative grid gap-6 lg:grid-cols-[minmax(0,1.4fr)_minmax(16rem,0.9fr)] lg:items-end">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="badge badge-success">● {{ __('Live Data') }}</span>
                        <span class="badge badge-brand">{{ __('Performance') }}</span>
                        <span class="badge badge-muted">{{ __('Last 14 days') }}</span>
                    </div>

                    <div>
                        <p class="mobile-section-title">{{ __('Analytics') }}</p>
                        <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('Site Performance & Visitors') }}</h1>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">
                            {{ __('Track public website visitors by country, device and IP, OTP messages sent to customers, login attempts, and a full log of user activity across the site and dashboard.') }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('dashboard.reports.index') }}" class="app-button--ghost">{{ __('Business Reports') }}</a>
                        <a href="{{ route('dashboard.users.index') }}" class="app-button--ghost">{{ __('Users') }}</a>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 shadow-lg shadow-black/10 backdrop-blur-xl">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Total Visits') }}</p>
                        <p class="mt-2 text-3xl font-bold text-white">{{ number_format($visitorStats['total_visits']) }}</p>
                        <p class="mt-1 text-xs text-slate-400">{{ __('All recorded page views') }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 shadow-lg shadow-black/10 backdrop-blur-xl">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Unique Visitors') }}</p>
                        <p class="mt-2 text-3xl font-bold text-brand-400">{{ number_format($visitorStats['unique_ips']) }}</p>
                        <p class="mt-1 text-xs text-slate-400">{{ __('Distinct IP addresses') }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 shadow-lg shadow-black/10 backdrop-blur-xl">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Visits Today') }}</p>
                        <p class="mt-2 text-3xl font-bold text-emerald-400">{{ number_format($visitorStats['today_visits']) }}</p>
                        <p class="mt-1 text-xs text-slate-400">{{ now()->format('Y-m-d') }}</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Visitor KPIs --}}
        <section class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div class="app-card app-card--gradient p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('Total Visits') }}</p>
                <p class="mt-2 text-3xl font-bold text-white">{{ number_format($visitorStats['total_visits']) }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ __('All recorded page views') }}</p>
            </div>
            <div class="app-card app-card--gradient p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('Unique Visitors') }}</p>
                <p class="mt-2 text-3xl font-bold text-brand-400">{{ number_format($visitorStats['unique_ips']) }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ __('Distinct IP addresses') }}</p>
            </div>
            <div class="app-card app-card--gradient p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('Visits Today') }}</p>
                <p class="mt-2 text-3xl font-bold text-emerald-400">{{ number_format($visitorStats['today_visits']) }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ now()->format('Y-m-d') }}</p>
            </div>
        </section>

        {{-- Visitors chart + breakdown --}}
        <section class="grid gap-6 lg:grid-cols-3">
            <div class="app-card app-card--gradient space-y-4 lg:col-span-2">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-white">{{ __('Visitors — Last 14 Days') }}</h2>
                    <span class="text-xs text-slate-400">{{ __('Daily page views') }}</span>
                </div>
                <div class="flex h-44 items-end gap-1.5">
                    @foreach ($visitsSeries as $day)
                        <div class="group relative flex-1">
                            <div class="mx-auto w-full rounded-t-lg bg-brand-500/70 transition group-hover:bg-brand-400" style="height: {{ max(4, round(($day['total'] / $maxVisits) * 160)) }}px" title="{{ $day['label'] }}: {{ $day['total'] }}"></div>
                            <div class="absolute -top-8 left-1/2 z-10 hidden -translate-x-1/2 whitespace-nowrap rounded-lg bg-slate-900 px-2 py-1 text-[10px] font-bold text-white shadow-lg group-hover:block">{{ $day['total'] }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="flex justify-between text-[10px] text-slate-500">
                    @foreach ($visitsSeries as $day)
                        <span>{{ $day['label'] }}</span>
                    @endforeach
                </div>
            </div>

            <div class="space-y-6">
                <div class="app-card app-card--gradient space-y-3">
                    <h2 class="text-lg font-semibold text-white">{{ __('Visits by Device') }}</h2>
                    <div class="space-y-3">
                        @forelse ($byDevice as $device)
                            @php
                                $key = $device->device_type;
                                $max = max(1, $byDevice->max('total'));
                            @endphp
                            <div class="space-y-1">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-300">{{ $deviceLabels[$key] ?? ucfirst($key) }}</span>
                                    <span class="text-slate-400">{{ $device->total }}</span>
                                </div>
                                <div class="h-2 w-full overflow-hidden rounded-full bg-white/5">
                                    <div class="{{ $deviceColors[$key] ?? 'bg-slate-500' }} h-full rounded-full" style="width: {{ round($device->total / $max * 100) }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">{{ __('No visitor data yet.') }}</p>
                        @endforelse
                    </div>
                </div>

                <div class="app-card app-card--gradient space-y-3">
                    <h2 class="text-lg font-semibold text-white">{{ __('Visits by Country') }}</h2>
                    <div class="space-y-2.5">
                        @forelse ($byCountry as $index => $country)
                            @php
                                $max = max(1, $byCountry->max('total'));
                            @endphp
                            <div class="space-y-1">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-medium text-slate-300">{{ $country->country }}</span>
                                    <span class="text-slate-400">{{ $country->total }}</span>
                                </div>
                                <div class="h-2 w-full overflow-hidden rounded-full bg-white/5">
                                    <div class="{{ $countryColors[$index % count($countryColors)] }} h-full rounded-full" style="width: {{ round($country->total / $max * 100) }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">{{ __('No visitor data yet.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        {{-- Top IPs --}}
        <section class="app-card app-card--gradient space-y-4">
            <h2 class="text-lg font-semibold text-white">{{ __('Top IP Addresses') }}</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-slate-300">
                    <thead class="border-b border-white/10 text-left text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="pb-3 pr-4">#</th>
                            <th class="pb-3 pr-4">{{ __('IP Address') }}</th>
                            <th class="pb-3 pr-4">{{ __('Country') }}</th>
                            <th class="pb-3 pr-4">{{ __('Visits') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse ($topIps as $index => $ip)
                            <tr>
                                <td class="py-3 pr-4 text-slate-500">{{ $index + 1 }}</td>
                                <td class="py-3 pr-4 ltr font-semibold text-white">{{ $ip->ip_address }}</td>
                                <td class="py-3 pr-4">{{ $ip->country ?: __('Unknown') }}</td>
                                <td class="py-3 pr-4">{{ $ip->total }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-4 text-slate-500">{{ __('No visitor data yet.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- OTP chart + login attempts --}}
        <section class="grid gap-6 lg:grid-cols-3">
            <div class="app-card app-card--gradient space-y-4 lg:col-span-2">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-white">{{ __('OTP Messages Sent to Customers') }}</h2>
                        <p class="text-xs text-slate-500">{{ __('Verification codes sent via SMS / WhatsApp — last 14 days') }}</p>
                    </div>
                    <div class="flex gap-2">
                        <span class="badge badge-brand">{{ $otpTotals['sent'] }} {{ __('sent') }}</span>
                        <span class="badge badge-danger">{{ $otpTotals['failed'] }} {{ __('failed') }}</span>
                    </div>
                </div>
                <div class="flex h-40 items-end gap-1.5">
                    @foreach ($otpSeries as $day)
                        <div class="group relative flex-1">
                            <div class="mx-auto w-full rounded-t-lg bg-violet-500/70 transition group-hover:bg-violet-400" style="height: {{ max(4, round(($day['total'] / $maxOtp) * 150)) }}px" title="{{ $day['label'] }}: {{ $day['total'] }}"></div>
                            <div class="absolute -top-8 left-1/2 z-10 hidden -translate-x-1/2 whitespace-nowrap rounded-lg bg-slate-900 px-2 py-1 text-[10px] font-bold text-white shadow-lg group-hover:block">{{ $day['total'] }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="flex justify-between text-[10px] text-slate-500">
                    @foreach ($otpSeries as $day)
                        <span>{{ $day['label'] }}</span>
                    @endforeach
                </div>
            </div>

            <div class="app-card app-card--gradient space-y-4">
                <h2 class="text-lg font-semibold text-white">{{ __('Login Attempts') }}</h2>
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/5 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-300">{{ __('Successful') }}</p>
                        <p class="mt-2 text-3xl font-bold text-emerald-400">{{ number_format($loginTotals['successful']) }}</p>
                    </div>
                    <div class="rounded-2xl border border-rose-500/20 bg-rose-500/5 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-rose-300">{{ __('Failed') }}</p>
                        <p class="mt-2 text-3xl font-bold text-rose-400">{{ number_format($loginTotals['failed']) }}</p>
                    </div>
                </div>
                <div class="space-y-3">
                    @foreach ($loginSeries as $day)
                        @if ($day['successful'] > 0 || $day['failed'] > 0)
                            <div class="space-y-1">
                                <div class="flex items-center justify-between text-[11px] text-slate-400">
                                    <span>{{ $day['label'] }}</span>
                                    <span class="flex gap-2">
                                        <span class="text-emerald-400">{{ $day['successful'] }} ✓</span>
                                        <span class="text-rose-400">{{ $day['failed'] }} ✗</span>
                                    </span>
                                </div>
                                <div class="flex h-2 w-full gap-0.5 overflow-hidden rounded-full bg-white/5">
                                    <div class="bg-emerald-500" style="width: {{ round($day['successful'] / max(1, $day['successful'] + $day['failed']) * 100) }}%"></div>
                                    <div class="bg-rose-500" style="width: {{ round($day['failed'] / max(1, $day['successful'] + $day['failed']) * 100) }}%"></div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('Top failed-login IPs') }}</p>
                    <div class="mt-3 space-y-2">
                        @forelse ($failedByIp as $ip)
                            <div class="flex items-center justify-between text-xs">
                                <span class="ltr font-semibold text-rose-300">{{ $ip->ip_address }}</span>
                                <span class="text-slate-400">{{ $ip->total }}</span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-500">{{ __('No failed attempts recorded.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        {{-- Users who logged in --}}
        <section class="app-card app-card--gradient space-y-4">
            <h2 class="text-lg font-semibold text-white">{{ __('Users Who Logged In') }}</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-slate-300">
                    <thead class="border-b border-white/10 text-left text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="pb-3 pr-4">{{ __('User') }}</th>
                            <th class="pb-3 pr-4">{{ __('Email') }}</th>
                            <th class="pb-3 pr-4">{{ __('Logins') }}</th>
                            <th class="pb-3 pr-4">{{ __('Last Login') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse ($userLogins as $login)
                            <tr>
                                <td class="py-3 pr-4">
                                    <div class="flex items-center gap-2">
                                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-600/20 text-xs font-bold text-brand-300">{{ mb_strtoupper(mb_substr($login->user?->name ?? '?', 0, 1)) }}</span>
                                        <span class="font-semibold text-white">{{ $login->user?->name ?? __('Deleted user') }}</span>
                                    </div>
                                </td>
                                <td class="py-3 pr-4">{{ $login->user?->email ?? '-' }}</td>
                                <td class="py-3 pr-4 font-bold text-white">{{ $login->total }}</td>
                                <td class="py-3 pr-4">{{ \Illuminate\Support\Carbon::parse($login->last_login)->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-4 text-slate-500">{{ __('No successful logins recorded yet.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Audit logs --}}
        <section class="app-card app-card--gradient space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-white">{{ __('Activity Logs') }}</h2>
                    <p class="text-xs text-slate-500">{{ __('Latest recorded actions across the site and dashboard') }}</p>
                </div>
                <span class="badge badge-muted">{{ __('Latest 60') }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-slate-300">
                    <thead class="border-b border-white/10 text-left text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="pb-3 pr-4">{{ __('When') }}</th>
                            <th class="pb-3 pr-4">{{ __('User') }}</th>
                            <th class="pb-3 pr-4">{{ __('Action') }}</th>
                            <th class="pb-3 pr-4">{{ __('Target') }}</th>
                            <th class="pb-3 pr-4">{{ __('IP Address') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse ($auditLogs as $log)
                            <tr>
                                <td class="py-3 pr-4 whitespace-nowrap">{{ $log->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td class="py-3 pr-4 font-semibold text-white">{{ $log->user?->name ?? __('System') }}</td>
                                <td class="py-3 pr-4">
                                    <span class="badge badge-brand">{{ $log->event }}</span>
                                </td>
                                <td class="py-3 pr-4 text-xs">{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}</td>
                                <td class="py-3 pr-4 ltr">{{ $log->ip_address ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 text-slate-500">{{ __('No activity recorded yet.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
