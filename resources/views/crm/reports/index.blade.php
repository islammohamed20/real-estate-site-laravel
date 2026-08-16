@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    @include('crm.partials.crm-nav')
    <section class="dashboard-hero-card p-6 sm:p-8">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('CRM Reports') }}</h1>
            <p class="mt-2 text-sm text-slate-300">{{ __('Overview of leads, offers, reservations and team activity.') }}</p>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['label' => __('Total Leads'), 'value' => $overview['total_leads']],
            ['label' => __('Total Customers'), 'value' => $overview['total_customers']],
            ['label' => __('Active Deals'), 'value' => $overview['active_deals']],
            ['label' => __('Open Offers'), 'value' => $overview['open_offers']],
            ['label' => __('Active Reservations'), 'value' => $overview['active_reservations']],
            ['label' => __('Overdue Tasks'), 'value' => $overview['overdue_tasks']],
            ['label' => __('Overdue Follow-ups'), 'value' => $overview['overdue_follow_ups']],
            ['label' => __('Activities Today'), 'value' => $overview['activities_today']],
        ] as $stat)
            <div class="app-card app-card--gradient p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $stat['label'] }}</p>
                <p class="mt-2 text-2xl font-bold text-white">{{ number_format($stat['value']) }}</p>
            </div>
        @endforeach
    </section>

    <section class="grid gap-6 lg:grid-cols-2">
        <div class="overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-lg p-4">
            <h2 class="text-lg font-semibold text-white">{{ __('Leads by Stage') }}</h2>
            <div class="mt-4 space-y-3">
                @foreach ($leadsByStage as $row)
                    <div class="flex items-center justify-between rounded-xl bg-white/5 p-3">
                        <span class="text-sm text-slate-300">{{ $row['stage'] }}</span>
                        <div class="text-right">
                            <span class="block text-lg font-semibold text-white">{{ number_format($row['count']) }}</span>
                            <span class="text-xs text-slate-500">{{ 'EGP ' . number_format($row['value']) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-lg p-4">
            <h2 class="text-lg font-semibold text-white">{{ __('Conversion Funnel') }}</h2>
            <div class="mt-4 space-y-3">
                @foreach ($funnel as $step)
                    <div class="flex items-center justify-between rounded-xl bg-white/5 p-3">
                        <span class="text-sm text-slate-300">{{ $step['label'] }}</span>
                        <div class="text-right">
                            <span class="block text-lg font-semibold text-white">{{ number_format($step['count']) }}</span>
                            <span class="text-xs text-slate-500">{{ $step['rate'] }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-lg p-4">
            <h2 class="text-lg font-semibold text-white">{{ __('Top Performers') }}</h2>
            <div class="mt-4 space-y-3">
                @forelse ($topPerformers as $user)
                    <div class="flex items-center justify-between rounded-xl bg-white/5 p-3">
                        <span class="text-sm text-slate-300">{{ $user->name }}</span>
                        <div class="text-right">
                            <span class="block text-sm font-semibold text-white">{{ $user->offers_count }} {{ __('offers') }}</span>
                            <span class="text-xs text-slate-500">{{ $user->reservations_count }} {{ __('reservations') }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">{{ __('No activity yet.') }}</p>
                @endforelse
            </div>
        </div>

        <div class="overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-lg p-4">
            <h2 class="text-lg font-semibold text-white">{{ __('Daily Activity') }}</h2>
            <div class="mt-4 space-y-3">
                @forelse ($dailyActivity as $day)
                    <div class="flex items-center justify-between rounded-xl bg-white/5 p-3">
                        <span class="text-sm text-slate-300">{{ $day->date }}</span>
                        <span class="text-lg font-semibold text-white">{{ $day->total }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">{{ __('No recent activity.') }}</p>
                @endforelse
            </div>
        </div>
    </section>
</div>
@endsection
