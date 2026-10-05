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

    @canany(['create follow-ups', 'manage crm'])
        <section class="app-card app-card--gradient p-5 sm:p-6" x-data="{ relatedType: '{{ old('related_type', 'customer') }}' }">
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-white">{{ __('Schedule a follow-up') }}</h2>
                <p class="mt-1 text-sm text-slate-400">{{ __('Create a reminder for a customer, lead or deal.') }}</p>
            </div>

            <form action="{{ route('dashboard.crm.follow_ups.store') }}" method="POST" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                @csrf
                <div>
                    <label for="follow-up-related-type" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Related to') }}</label>
                    <select id="follow-up-related-type" x-model="relatedType" class="form-select w-full rounded-xl text-sm">
                        <option value="customer">{{ __('Customer') }}</option>
                        <option value="lead">{{ __('Lead') }}</option>
                        <option value="deal">{{ __('Deal') }}</option>
                    </select>
                </div>
                <div x-show="relatedType === 'customer'">
                    <label for="follow-up-customer" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Customer') }}</label>
                    <select id="follow-up-customer" name="customer_id" :disabled="relatedType !== 'customer'" class="form-select w-full rounded-xl text-sm">
                        <option value="">{{ __('Select customer') }}</option>
                        @foreach ($customers as $id => $name)
                            <option value="{{ $id }}" @selected(old('customer_id') == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div x-show="relatedType === 'lead'">
                    <label for="follow-up-lead" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Lead') }}</label>
                    <select id="follow-up-lead" name="lead_id" :disabled="relatedType !== 'lead'" class="form-select w-full rounded-xl text-sm">
                        <option value="">{{ __('Select lead') }}</option>
                        @foreach ($leads as $id => $name)
                            <option value="{{ $id }}" @selected(old('lead_id') == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div x-show="relatedType === 'deal'">
                    <label for="follow-up-deal" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Deal') }}</label>
                    <select id="follow-up-deal" name="deal_id" :disabled="relatedType !== 'deal'" class="form-select w-full rounded-xl text-sm">
                        <option value="">{{ __('Select deal') }}</option>
                        @foreach ($deals as $id => $title)
                            <option value="{{ $id }}" @selected(old('deal_id') == $id)>{{ $title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="follow-up-at" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Date and time') }}</label>
                    <input id="follow-up-at" type="datetime-local" name="follow_up_at" min="{{ now()->format('Y-m-d\\TH:i') }}" value="{{ old('follow_up_at') }}" required class="form-input w-full rounded-xl text-sm">
                </div>
                <div>
                    <label for="follow-up-type" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Type') }}</label>
                    <select id="follow-up-type" name="type" required class="form-select w-full rounded-xl text-sm">
                        @foreach ($types as $value => $label)
                            <option value="{{ $value }}" @selected(old('type', 'phone_call') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="follow-up-priority" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Priority') }}</label>
                    <select id="follow-up-priority" name="priority" class="form-select w-full rounded-xl text-sm">
                        @foreach ($priorities as $value => $label)
                            <option value="{{ $value }}" @selected(old('priority', 'normal') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="follow-up-assignee" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Assign to') }}</label>
                    <select id="follow-up-assignee" name="assigned_to" class="form-select w-full rounded-xl text-sm">
                        <option value="">{{ __('Unassigned') }}</option>
                        @foreach ($users as $id => $name)
                            <option value="{{ $id }}" @selected(old('assigned_to', auth()->id()) == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2 pt-7">
                    <input id="follow-up-reminder" type="checkbox" name="reminder" value="1" @checked(old('reminder', true)) class="h-4 w-4 rounded border-white/20 bg-slate-900 text-brand-600">
                    <label for="follow-up-reminder" class="text-sm text-slate-300">{{ __('Send reminder') }}</label>
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <label for="follow-up-notes" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Notes') }}</label>
                    <textarea id="follow-up-notes" name="notes" rows="2" maxlength="4000" class="form-textarea w-full rounded-xl text-sm" placeholder="{{ __('Add notes for the follow-up...') }}">{{ old('notes') }}</textarea>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="app-button w-full text-sm">{{ __('Schedule follow-up') }}</button>
                </div>
                @if ($errors->any())
                    <div class="sm:col-span-2 lg:col-span-4 rounded-xl border border-rose-400/30 bg-rose-500/10 p-3 text-sm text-rose-200">
                        {{ $errors->first() }}
                    </div>
                @endif
            </form>
        </section>
    @endcanany

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
