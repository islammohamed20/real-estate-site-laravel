@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    @include('crm.partials.crm-nav')

    @if (session('status'))
        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="flex items-center gap-3 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12l5 5L20 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>{{ session('status') }}</span>
            <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-200">×</button>
        </div>
    @endif

    <section class="dashboard-hero-card p-6 sm:p-8">
        <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-brand-500/20 blur-3xl"></div>
        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-start">
                <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500/25 to-violet-600/25 text-2xl font-bold text-brand-200 ring-1 ring-brand-500/20">
                    {{ mb_strtoupper(mb_substr($organization->name, 0, 1)) }}
                </span>
                <div class="space-y-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="badge badge-brand">{{ __('Organization') }}</span>
                        @if ($organization->industry)
                            <span class="badge badge-muted">{{ $organization->industry }}</span>
                        @endif
                        <span class="badge badge-success">{{ $organization->contacts_count }} {{ __('contacts') }}</span>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ $organization->name }}</h1>
                        <p class="mt-2 flex flex-wrap items-center gap-3 text-sm text-slate-300">
                            @if ($organization->phone)
                                <a href="tel:{{ $organization->phone }}" class="flex items-center gap-1.5 hover:text-white hover:underline">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" stroke-width="1.8"/></svg>
                                    {{ $organization->phone }}
                                </a>
                            @endif
                            @if ($organization->email)
                                <a href="mailto:{{ $organization->email }}" class="flex items-center gap-1.5 hover:text-white hover:underline">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="2" y="4" width="20" height="16" rx="2" stroke-width="1.8"/><path d="m22 7-10 6L2 7" stroke-width="1.8" stroke-linecap="round"/></svg>
                                    {{ $organization->email }}
                                </a>
                            @endif
                            @if ($organization->website)
                                <a href="{{ $organization->website }}" target="_blank" class="flex items-center gap-1.5 text-brand-300 hover:underline">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="9" stroke-width="1.8"/><path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18" stroke-width="1.8"/></svg>
                                    {{ parse_url($organization->website, PHP_URL_HOST) }}
                                </a>
                            @endif
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('dashboard.crm.organizations.edit', $organization) }}" class="app-button text-sm">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34" stroke-width="1.8" stroke-linecap="round"/><path d="M18 2l4 4-10 10-4 1 1-4z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            {{ __('Edit') }}
                        </a>
                        <a href="{{ route('dashboard.crm.organizations.index') }}" class="app-button--ghost text-sm">{{ __('All organizations') }}</a>
                    </div>
                </div>
            </div>

            <div class="grid w-full gap-3 sm:grid-cols-2 lg:w-80 lg:grid-cols-1">
                @if ($organization->city || $organization->country || $organization->address)
                    <div class="app-card app-card--gradient p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Location') }}</p>
                        <p class="mt-2 text-sm font-semibold text-white">
                            {{ collect([$organization->address, $organization->city, $organization->country])->filter()->implode(' · ') ?: '—' }}
                        </p>
                    </div>
                @endif
                @if ($organization->tax_id)
                    <div class="app-card app-card--gradient p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Tax ID') }}</p>
                        <p class="mt-2 text-sm font-semibold text-white">{{ $organization->tax_id }}</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-lg">
        <div class="flex items-center justify-between border-b border-white/10 px-4 py-3">
            <h2 class="text-lg font-semibold text-white">{{ __('Contacts') }}</h2>
            <a href="{{ route('dashboard.crm.index', ['quick_tab' => 'contact']) }}" class="app-button text-sm">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                {{ __('+ New Contact') }}
            </a>
        </div>
        <div class="p-4">
            @forelse ($contacts as $contact)
                <article class="mb-3 flex flex-col gap-3 rounded-2xl border border-white/10 bg-slate-900/50 p-4 transition hover:border-brand-500/30 sm:flex-row sm:items-center sm:justify-between last:mb-0">
                    <div class="flex min-w-0 items-center gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-500/15 text-sm font-bold text-brand-300">
                            {{ mb_strtoupper(mb_substr($contact->full_name ?? $contact->first_name, 0, 1)) }}
                        </span>
                        <div class="min-w-0">
                            <p class="flex items-center gap-2 font-semibold text-white">
                                {{ $contact->full_name ?? $contact->first_name }}
                                @if ($contact->is_primary)
                                    <span class="rounded-full bg-brand-500/20 px-2 py-0.5 text-[10px] font-bold uppercase text-brand-300">{{ __('Primary') }}</span>
                                @endif
                            </p>
                            <p class="flex flex-wrap items-center gap-x-3 gap-y-0.5 text-xs text-slate-400">
                                @if ($contact->job_title)<span>{{ $contact->job_title }}</span>@endif
                                @if ($contact->phone)<span>· {{ $contact->phone }}</span>@endif
                                @if ($contact->email)<span>· {{ $contact->email }}</span>@endif
                            </p>
                        </div>
                    </div>
                    <div class="flex shrink-0 items-center gap-2">
                        <a href="tel:{{ $contact->phone ?? $contact->mobile }}" class="app-button--ghost p-2" aria-label="{{ __('Call') }}">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" stroke-width="1.8"/></svg>
                        </a>
                        <a href="{{ route('dashboard.crm.contacts.edit', $contact) }}" class="app-button--ghost p-2" aria-label="{{ __('Edit') }}">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34" stroke-width="1.8" stroke-linecap="round"/><path d="M18 2l4 4-10 10-4 1 1-4z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                        <form method="POST" id="delete-contact-{{ $contact->id }}" action="{{ route('dashboard.crm.contacts.destroy', $contact) }}" onsubmit="return confirm('{{ __('Delete this contact?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="app-button app-button--danger p-2" aria-label="{{ __('Delete') }}">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="flex min-h-32 flex-col items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/5 text-center text-sm text-slate-500">
                    <p>{{ __('No contacts yet for this organization.') }}</p>
                </div>
            @endforelse
        </div>
    </section>

    @if ($organization->notes)
        <section class="app-card app-card--gradient space-y-2">
            <h2 class="text-lg font-semibold">{{ __('Notes') }}</h2>
            <p class="whitespace-pre-line text-sm text-slate-300 leading-relaxed">{{ $organization->notes }}</p>
        </section>
    @endif
</div>
@endsection
