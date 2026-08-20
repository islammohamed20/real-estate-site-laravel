@extends('layouts.dashboard')

@section('content')
<div class="space-y-6" x-data="{ showFilters: false }">
    @include('crm.partials.crm-nav')
    <section class="dashboard-hero-card p-6 sm:p-8">
        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="badge badge-brand">{{ __('Lead management') }}</span>
                    <span class="badge badge-success">{{ __('Mobile-first') }}</span>
                </div>
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('Leads') }}</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">{{ __('Track, assign, and convert leads through your sales pipeline.') }}</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('dashboard.crm.index') }}" class="app-button--ghost">{{ __('CRM Home') }}</a>
                <a href="{{ route('dashboard.crm.data-transfer.index', ['type' => 'leads']) }}" class="app-button--ghost">{{ __('Import / Export') }}</a>
                <a href="{{ route('dashboard.crm.quick') }}" class="app-button">{{ __('+ New Lead') }}</a>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-lg">
        <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">
            <form action="{{ route('dashboard.crm.leads.index') }}" method="GET" class="flex w-full max-w-md items-center gap-2">
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="{{ __('Search leads...') }}" class="form-input w-full rounded-xl text-sm">
                <button type="submit" class="app-button whitespace-nowrap">{{ __('Search') }}</button>
            </form>
            <button type="button" @click="showFilters = !showFilters" class="app-button--ghost text-sm">{{ __('Filters') }}</button>
        </div>

        <div x-show="showFilters" x-cloak class="border-t border-white/10 p-4">
            <form action="{{ route('dashboard.crm.leads.index') }}" method="GET" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                <select name="stage" class="form-select rounded-xl text-sm">
                    <option value="">{{ __('All stages') }}</option>
                    @foreach ($stages as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['stage'] ?? '') === $value)>{{ __($label) }}</option>
                    @endforeach
                </select>
                <select name="priority" class="form-select rounded-xl text-sm">
                    <option value="">{{ __('All priorities') }}</option>
                    @foreach ($priorities as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['priority'] ?? '') === $value)>{{ __($label) }}</option>
                    @endforeach
                </select>
                <select name="source" class="form-select rounded-xl text-sm">
                    <option value="">{{ __('All sources') }}</option>
                    @foreach ($sources as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['source'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="assigned" class="form-select rounded-xl text-sm">
                    <option value="">{{ __('All users') }}</option>
                    @foreach ($users as $id => $name)
                        <option value="{{ $id }}" @selected(($filters['assigned'] ?? '') == $id)>{{ $name }}</option>
                    @endforeach
                </select>
                <select name="status" class="form-select rounded-xl text-sm">
                    <option value="">{{ __('All statuses') }}</option>
                    <option value="active" @selected(($filters['status'] ?? '') === 'active')>{{ __('Active') }}</option>
                    <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>{{ __('Inactive') }}</option>
                    <option value="converted" @selected(($filters['status'] ?? '') === 'converted')>{{ __('Converted') }}</option>
                    <option value="lost" @selected(($filters['status'] ?? '') === 'lost')>{{ __('Lost') }}</option>
                </select>
                <div class="sm:col-span-2 lg:col-span-5 flex gap-2">
                    <button type="submit" class="app-button text-sm">{{ __('Apply filters') }}</button>
                    <a href="{{ route('dashboard.crm.leads.index') }}" class="app-button--ghost text-sm">{{ __('Reset') }}</a>
                </div>
            </form>
        </div>

        <div class="border-t border-white/10 p-4">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($leads as $lead)
                    <article class="app-card app-card--gradient p-4 transition hover:border-brand-500/30">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="truncate text-lg font-semibold text-white">{{ $lead->name }}</h3>
                                <p class="text-sm text-slate-400">{{ $lead->phone }}</p>
                            </div>
                            @include('crm.partials.status-badge', ['status' => $lead->stage->value, 'label' => __($lead->stage->label())])
                        </div>

                        <div class="mt-3 grid grid-cols-2 gap-3 text-sm text-slate-300">
                            <div>
                                <span class="block text-xs text-slate-500">{{ __('Priority') }}</span>
                                <span class="font-semibold text-white capitalize">{{ $lead->priority ?? '—' }}</span>
                            </div>
                            <div>
                                <span class="block text-xs text-slate-500">{{ __('Source') }}</span>
                                <span class="font-semibold text-white">{{ $lead->leadSource?->name ?? $lead->source ?? '—' }}</span>
                            </div>
                            <div>
                                <span class="block text-xs text-slate-500">{{ __('Budget') }}</span>
                                <span class="font-semibold text-white">{{ $lead->budget ? 'EGP ' . number_format((float) $lead->budget) : '—' }}</span>
                            </div>
                            <div>
                                <span class="block text-xs text-slate-500">{{ __('Assigned') }}</span>
                                <span class="font-semibold text-white">{{ $lead->assignedSales?->name ?? __('Unassigned') }}</span>
                            </div>
                        </div>

                        @if ($lead->tags->isNotEmpty())
                            <div class="mt-3 flex flex-wrap gap-1">
                                @foreach ($lead->tags as $tag)
                                    <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold" style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-4 flex items-center gap-2">
                            <a href="{{ route('dashboard.crm.leads.show', $lead) }}" class="app-button w-full justify-center text-sm">{{ __('View') }}</a>
                            @if (\App\Support\WhatsApp::number($lead->whatsapp ?? $lead->phone))
                                <a href="{{ route('dashboard.whatsapp.index', ['phone' => $lead->whatsapp ?? $lead->phone, 'name' => $lead->name]) }}" class="app-button--ghost shrink-0 p-2" aria-label="{{ __('WhatsApp') }}">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M.5 23.5l1.6-5.8A11.3 11.3 0 0 1 0 11.3C0 5.1 5.1 0 11.3 0A11.3 11.3 0 0 1 22.5 11.3c0 6.2-5.1 11.3-11.3 11.3-1.9 0-3.8-.5-5.5-1.3L.5 23.5Zm6-3.4.3.2a9.3 9.3 0 0 0 4.5 1.2c5.1 0 9.3-4.2 9.3-9.3S16.4 2.9 11.3 2.9 2 7.1 2 12.2c0 1.8.5 3.5 1.4 5l.2.3-1 3.6 3.9-1Zm9.2-4.3c-.2-.1-1.4-.7-1.6-.8-.2-.1-.4-.1-.6.1l-.9 1.1c-.1.1-.3.1-.5 0-.2-.1-.8-.3-1.6-.9-.7-.5-1.3-1.2-1.5-1.4-.2-.2 0-.4.1-.5l.4-.4c.1-.1.2-.3.3-.4.1-.1 0-.3 0-.4 0-.1-.6-1.5-.8-2-.2-.4-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.2.2-.8.7-.8 1.7 0 1 .8 1.9.9 2 .1.1 1.5 2.4 3.6 3.3.5.2.9.4 1.3.5.6.2 1.2.2 1.7.1.5-.1 1.4-.6 1.6-1.1.2-.5.2-1 .1-1.1-.1-.1-.2-.2-.4-.3Z"/></svg>
                                </a>
                            @endif
                            <a href="tel:{{ $lead->phone }}" class="app-button--ghost shrink-0 p-2" aria-label="{{ __('Call') }}">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl border border-white/10 bg-white/5 p-8 text-center text-slate-400">
                        <p class="text-lg font-semibold text-white">{{ __('No leads found') }}</p>
                        <p class="mt-1 text-sm">{{ __('Create a new lead to get started.') }}</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $leads->links() }}
            </div>
        </div>
    </section>
</div>
@endsection
