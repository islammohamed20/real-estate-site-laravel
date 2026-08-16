@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    @include('crm.partials.crm-nav')

    <section class="dashboard-hero-card p-6 sm:p-8">
        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="badge badge-brand">{{ __('Organizations') }}</span>
                    <span class="badge badge-success">{{ __('Companies') }}</span>
                </div>
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('Organizations') }}</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">{{ __('Companies and institutions with their contacts.') }}</p>
                </div>
            </div>
            <a href="{{ route('dashboard.crm.index') }}" class="app-button--ghost">{{ __('CRM Home') }}</a>
        </div>
    </section>

    <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-lg">
        <div class="border-t border-white/10 p-4">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($organizations as $organization)
                    <a href="{{ route('dashboard.crm.organizations.show', $organization) }}" class="app-card app-card--gradient p-4 transition hover:border-brand-500/30">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-brand-500/15 text-base font-bold text-brand-300">
                                    {{ mb_strtoupper(mb_substr($organization->name, 0, 1)) }}
                                </span>
                                <div class="min-w-0">
                                    <h3 class="truncate font-semibold text-white">{{ $organization->name }}</h3>
                                    <p class="truncate text-sm text-slate-400">{{ $organization->industry ?? $organization->email ?? $organization->phone ?? '—' }}</p>
                                </div>
                            </div>
                            <span class="shrink-0 rounded-full bg-slate-800 px-3 py-1 text-xs text-slate-300">{{ $organization->contacts_count }} {{ __('contacts') }}</span>
                        </div>
                        @if ($organization->city || $organization->country)
                            <p class="mt-3 text-xs text-slate-500">
                                @if ($organization->city){{ $organization->city }}@endif
                                @if ($organization->city && $organization->country) · @endif
                                @if ($organization->country){{ $organization->country }}@endif
                            </p>
                        @endif
                    </a>
                @empty
                    <div class="col-span-full rounded-2xl border border-white/10 bg-white/5 p-8 text-center text-slate-400">
                        <p class="text-lg font-semibold text-white">{{ __('No organizations yet.') }}</p>
                        <p class="mt-1 text-sm">{{ __('Create one from the CRM home page.') }}</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $organizations->links() }}
            </div>
        </div>
    </section>
</div>
@endsection
