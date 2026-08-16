@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    @include('crm.partials.crm-nav')
    <section class="dashboard-hero-card p-6 sm:p-8">
        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="badge badge-brand">{{ __('Offer') }}</span>
                    @include('crm.partials.status-badge', ['status' => $offer->status, 'label' => __(ucfirst($offer->status))])
                </div>
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ $offer->offer_number }}</h1>
                    <p class="mt-2 text-sm text-slate-300">{{ $offer->customer?->name ?? $offer->lead?->name ?? '—' }}</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('dashboard.crm.offers.edit', $offer) }}" class="app-button">{{ __('Edit') }}</a>
                <form action="{{ route('dashboard.crm.offers.destroy', $offer) }}" method="POST" onsubmit="return confirm('{{ __('Delete this offer?') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="app-button app-button--danger">{{ __('Delete') }}</button>
                </form>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-lg p-4">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="app-card app-card--gradient p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Customer') }}</p>
                <p class="mt-1 text-lg font-semibold text-white">{{ $offer->customer?->name ?? '—' }}</p>
            </div>
            <div class="app-card app-card--gradient p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Lead') }}</p>
                <p class="mt-1 text-lg font-semibold text-white">{{ $offer->lead?->name ?? '—' }}</p>
            </div>
            <div class="app-card app-card--gradient p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Unit') }}</p>
                <p class="mt-1 text-lg font-semibold text-white">{{ $offer->unit?->project?->name ?? '—' }} #{{ $offer->unit?->unit_number ?? '—' }}</p>
            </div>
            <div class="app-card app-card--gradient p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Subtotal') }}</p>
                <p class="mt-1 text-lg font-semibold text-white">{{ 'EGP ' . number_format((float) $offer->subtotal) }}</p>
            </div>
            <div class="app-card app-card--gradient p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Discount') }}</p>
                <p class="mt-1 text-lg font-semibold text-white">{{ 'EGP ' . number_format((float) $offer->discount_amount) }}</p>
            </div>
            <div class="app-card app-card--gradient p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Total') }}</p>
                <p class="mt-1 text-lg font-semibold text-white">{{ 'EGP ' . number_format((float) $offer->total_amount) }}</p>
            </div>
        </div>

        @if ($offer->notes)
            <div class="app-card app-card--gradient mt-4 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Notes') }}</p>
                <p class="mt-2 whitespace-pre-line text-sm text-slate-300">{{ $offer->notes }}</p>
            </div>
        @endif
    </section>
</div>
@endsection
