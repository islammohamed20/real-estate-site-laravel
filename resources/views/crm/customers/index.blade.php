@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    @include('crm.partials.crm-nav')
    <section class="dashboard-hero-card p-6 sm:p-8">
        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="badge badge-brand">{{ __('Customer hub') }}</span>
                    <span class="badge badge-success">{{ __('Customer 360') }}</span>
                </div>
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('Customers') }}</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">{{ __('Manage customer profiles, timelines, and conversion history.') }}</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('dashboard.crm.index') }}" class="app-button--ghost">{{ __('CRM Home') }}</a>
                <a href="{{ route('dashboard.crm.customers.create') }}" class="app-button">{{ __('+ New Customer') }}</a>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-lg">
        <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">
            <form action="{{ route('dashboard.crm.customers.index') }}" method="GET" class="flex w-full max-w-md items-center gap-2">
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="{{ __('Search customers...') }}" class="form-input w-full rounded-xl text-sm">
                <button type="submit" class="app-button whitespace-nowrap">{{ __('Search') }}</button>
            </form>
        </div>

        <div class="border-t border-white/10 p-4">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($customers as $customer)
                    <a href="{{ route('dashboard.crm.customers.show', $customer) }}" class="app-card app-card--gradient p-4 block transition hover:border-brand-500/30">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="truncate text-lg font-semibold text-white">{{ $customer->name }}</h3>
                                <p class="text-sm text-slate-400">{{ $customer->phone }}</p>
                            </div>
                            <span class="shrink-0 rounded-full bg-brand-500/20 px-3 py-1 text-xs font-semibold text-brand-300">{{ $customer->leads_count }} {{ __('leads') }}</span>
                        </div>
                        <div class="mt-3 grid grid-cols-2 gap-3 text-sm text-slate-300">
                            <div>
                                <span class="block text-xs text-slate-500">{{ __('Budget') }}</span>
                                <span class="font-semibold text-white">{{ $customer->budget ? 'EGP ' . number_format((float) $customer->budget) : '—' }}</span>
                            </div>
                            <div>
                                <span class="block text-xs text-slate-500">{{ __('Source') }}</span>
                                <span class="font-semibold text-white">{{ $customer->source ?? '—' }}</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full rounded-2xl border border-white/10 bg-white/5 p-8 text-center text-slate-400">
                        <p class="text-lg font-semibold text-white">{{ __('No customers found') }}</p>
                        <p class="mt-1 text-sm">{{ __('Create a customer to get started.') }}</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $customers->links() }}
            </div>
        </div>
    </section>
</div>
@endsection
