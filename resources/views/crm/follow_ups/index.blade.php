@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    @include('crm.partials.crm-nav')
    <section class="dashboard-hero-card p-6 sm:p-8">
        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-4">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('Follow-ups') }}</h1>
                    <p class="mt-2 text-sm text-slate-300">{{ __('Schedule calls, meetings and site visits with leads and customers.') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-lg">
        <div class="p-4">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($followUps as $followUp)
                    <div class="app-card app-card--gradient p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="truncate text-lg font-semibold text-white">{{ __(ucfirst($followUp->type)) }}</h3>
                                <p class="text-sm text-slate-400">
                                    @if ($followUp->lead)
                                        {{ $followUp->lead->name }}
                                    @elseif ($followUp->customer)
                                        {{ $followUp->customer->name }}
                                    @elseif ($followUp->deal)
                                        {{ $followUp->deal->title }}
                                    @else
                                        —
                                    @endif
                                </p>
                            </div>
                            @include('crm.partials.status-badge', ['status' => $followUp->completed_at ? 'completed' : ($followUp->follow_up_at < now() ? 'overdue' : 'pending'), 'label' => $followUp->completed_at ? __('Completed') : ($followUp->follow_up_at < now() ? __('Overdue') : __('Pending'))])
                        </div>
                        <p class="mt-2 text-sm text-slate-300">{{ $followUp->notes }}</p>
                        <div class="mt-3 flex items-center gap-2 text-xs text-slate-500">
                            <span>{{ $followUp->assignee?->name ?? __('Unassigned') }}</span>
                            <span>•</span>
                            <span>{{ $followUp->follow_up_at?->format('Y-m-d H:i') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full rounded-2xl border border-white/10 bg-white/5 p-8 text-center text-slate-400">
                        <p>{{ __('No follow-ups found.') }}</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $followUps->links() }}
            </div>
        </div>
    </section>
</div>
@endsection
