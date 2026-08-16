@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    @include('crm.partials.crm-nav')

    @if (session('status'))
        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="flex items-center gap-3 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12l5 5L20 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>{{ session('status') }}</span>
            <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-200">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 6L6 18M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 8000)" class="flex items-center gap-3 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-300">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 8v4m0 4h.01M10.3 3.9L1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>{{ $errors->first() }}</span>
            <button @click="show = false" class="ml-auto text-rose-400 hover:text-rose-200">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 6L6 18M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    @endif

    <section class="dashboard-hero-card p-6 sm:p-8">
        <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-rose-500/10 blur-3xl"></div>

        <div class="relative grid gap-6 lg:grid-cols-[minmax(0,1.4fr)_minmax(16rem,0.9fr)] lg:items-end">
            <div class="space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="badge badge-danger">{{ $customersCount }} {{ __('customers') }}</span>
                    <span class="badge badge-warning">{{ $leadsCount }} {{ __('leads') }}</span>
                    <span class="badge badge-brand">{{ $offersCount }} {{ __('offers') }}</span>
                    <span class="badge badge-muted">{{ $plansCount }} {{ __('plans') }}</span>
                </div>

                <div>
                    <p class="mobile-section-title">{{ __('Recycle bin') }}</p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('CRM Trash') }}</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">
                        {{ __('Soft-deleted customers, leads, offers, and installment plans. Restore them or permanently delete them.') }}
                    </p>
                    <p class="mt-2 text-xs text-rose-300/80">
                        {{ __('Permanently deleting an item cannot be undone.') }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('dashboard.crm.index') }}" class="app-button--ghost">{{ __('CRM Home') }}</a>
                    <a href="{{ route('dashboard.crm.plans.trash') }}" class="app-button--ghost">{{ __('Plans Trash') }}</a>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-2">
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4 shadow-lg shadow-black/10 backdrop-blur-xl">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Customers') }}</p>
                    <p class="mt-2 text-3xl font-bold text-rose-400">{{ number_format($customersCount) }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4 shadow-lg shadow-black/10 backdrop-blur-xl">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Leads') }}</p>
                    <p class="mt-2 text-3xl font-bold text-amber-400">{{ number_format($leadsCount) }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4 shadow-lg shadow-black/10 backdrop-blur-xl">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Offers') }}</p>
                    <p class="mt-2 text-3xl font-bold text-brand-300">{{ number_format($offersCount) }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4 shadow-lg shadow-black/10 backdrop-blur-xl">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Plans') }}</p>
                    <p class="mt-2 text-3xl font-bold text-emerald-300">{{ number_format($plansCount) }}</p>
                </div>
            </div>
        </div>
    </section>

    <div x-data="{ tab: 'customers' }" class="space-y-4">
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" @click="tab = 'customers'" class="app-button--ghost px-5 py-2.5 text-sm" :class="tab === 'customers' ? '!bg-brand-600 !text-white' : ''">
                {{ __('Customers') }} ({{ $customersCount }})
            </button>
            <button type="button" @click="tab = 'leads'" class="app-button--ghost px-5 py-2.5 text-sm" :class="tab === 'leads' ? '!bg-brand-600 !text-white' : ''">
                {{ __('Leads') }} ({{ $leadsCount }})
            </button>
            <button type="button" @click="tab = 'offers'" class="app-button--ghost px-5 py-2.5 text-sm" :class="tab === 'offers' ? '!bg-brand-600 !text-white' : ''">
                {{ __('Offers') }} ({{ $offersCount }})
            </button>
            <button type="button" @click="tab = 'plans'" class="app-button--ghost px-5 py-2.5 text-sm" :class="tab === 'plans' ? '!bg-brand-600 !text-white' : ''">
                {{ __('Plans') }} ({{ $plansCount }})
            </button>
        </div>

        {{-- Customers tab --}}
        <section x-show="tab === 'customers'" x-cloak class="app-card app-card--gradient space-y-3 p-5 sm:p-6">
            @forelse ($trashedCustomers as $customer)
                <div class="flex flex-col gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex min-w-0 items-center gap-3">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/5">
                            <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="9" cy="7" r="4" stroke-width="1.8"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-width="1.8"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="truncate font-semibold text-white">{{ $customer->name }} <span class="text-xs font-normal text-slate-400 ltr">{{ $customer->phone }}</span></p>
                            <p class="text-xs text-slate-400">
                                @if ($customer->email) {{ $customer->email }} · @endif
                                {{ __('Deleted :date', ['date' => $customer->deleted_at?->diffForHumans() ?? '—']) }}
                                @if ($customer->offers_count > 0) · {{ $customer->offers_count }} {{ __('offers') }} @endif
                                @if ($customer->leads_count > 0) · {{ $customer->leads_count }} {{ __('leads') }} @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex shrink-0 flex-wrap items-center gap-2">
                        <form method="POST" action="{{ route('dashboard.crm.trash.customers.restore', $customer) }}">
                            @csrf
                            <button type="submit" class="app-button--ghost px-4 py-2 text-xs text-emerald-300 hover:text-emerald-200">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 3v5h5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                {{ __('Restore') }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('dashboard.crm.trash.customers.force-delete', $customer) }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmAction('{{ __('Delete forever') }}', '{{ __('Permanently delete customer :name? This cannot be undone.', ['name' => $customer->name]) }}', () => this.closest('form').submit(), '{{ __('Delete forever') }}')" class="app-button app-button--danger px-4 py-2 text-xs">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                                {{ __('Delete forever') }}
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/5 py-14 text-center">
                    <svg class="h-10 w-10 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                    <p class="mt-3 font-semibold text-white">{{ __('No deleted customers') }}</p>
                    <p class="mt-1 text-sm text-slate-400">{{ __('The trash is empty.') }}</p>
                </div>
            @endforelse
            <div class="mt-4">{{ $trashedCustomers->links() }}</div>
        </section>

        {{-- Leads tab --}}
        <section x-show="tab === 'leads'" x-cloak class="app-card app-card--gradient space-y-3 p-5 sm:p-6">
            @forelse ($trashedLeads as $lead)
                <div class="flex flex-col gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex min-w-0 items-center gap-3">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/5">
                            <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="8.5" cy="7" r="4" stroke-width="1.8"/><path d="M20 8v6M23 11h-6" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="truncate font-semibold text-white">{{ $lead->name }} <span class="text-xs font-normal text-slate-400 ltr">{{ $lead->phone }}</span></p>
                            <p class="text-xs text-slate-400">
                                {{ $lead->customer?->name ?? __('No customer') }}
                                @if ($lead->offers_count > 0) · {{ $lead->offers_count }} {{ __('offers') }} @endif
                                · {{ __('Deleted :date', ['date' => $lead->deleted_at?->diffForHumans() ?? '—']) }}
                            </p>
                        </div>
                    </div>
                    <div class="flex shrink-0 flex-wrap items-center gap-2">
                        <form method="POST" action="{{ route('dashboard.crm.trash.leads.restore', $lead) }}">
                            @csrf
                            <button type="submit" class="app-button--ghost px-4 py-2 text-xs text-emerald-300 hover:text-emerald-200">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 3v5h5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                {{ __('Restore') }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('dashboard.crm.trash.leads.force-delete', $lead) }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmAction('{{ __('Delete forever') }}', '{{ __('Permanently delete lead :name? This cannot be undone.', ['name' => $lead->name]) }}', () => this.closest('form').submit(), '{{ __('Delete forever') }}')" class="app-button app-button--danger px-4 py-2 text-xs">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                                {{ __('Delete forever') }}
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/5 py-14 text-center">
                    <svg class="h-10 w-10 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                    <p class="mt-3 font-semibold text-white">{{ __('No deleted leads') }}</p>
                    <p class="mt-1 text-sm text-slate-400">{{ __('The trash is empty.') }}</p>
                </div>
            @endforelse
            <div class="mt-4">{{ $trashedLeads->links() }}</div>
        </section>

        {{-- Offers tab --}}
        <section x-show="tab === 'offers'" x-cloak class="app-card app-card--gradient space-y-3 p-5 sm:p-6">
            @forelse ($trashedOffers as $offer)
                <div class="flex flex-col gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex min-w-0 items-center gap-3">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/5">
                            <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-width="1.8"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="truncate font-semibold text-white">#{{ $offer->offer_number }}</p>
                            <p class="text-xs text-slate-400">
                                {{ $offer->customer?->name ?? $offer->lead?->name ?? __('No customer') }}
                                @if ($offer->project?->name) · {{ $offer->project->name }} @endif
                                @if ($offer->unit?->unit_number) · #{{ $offer->unit->unit_number }} @endif
                                · {{ number_format((float) $offer->total_amount, 0) }} {{ __('ج.م') }}
                                · {{ __('Deleted :date', ['date' => $offer->deleted_at?->diffForHumans() ?? '—']) }}
                            </p>
                        </div>
                    </div>
                    <div class="flex shrink-0 flex-wrap items-center gap-2">
                        <form method="POST" action="{{ route('dashboard.crm.trash.offers.restore', $offer) }}">
                            @csrf
                            <button type="submit" class="app-button--ghost px-4 py-2 text-xs text-emerald-300 hover:text-emerald-200">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 3v5h5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                {{ __('Restore') }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('dashboard.crm.trash.offers.force-delete', $offer) }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmAction('{{ __('Delete forever') }}', '{{ __('Permanently delete offer :num? This cannot be undone.', ['num' => $offer->offer_number]) }}', () => this.closest('form').submit(), '{{ __('Delete forever') }}')" class="app-button app-button--danger px-4 py-2 text-xs">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                                {{ __('Delete forever') }}
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/5 py-14 text-center">
                    <svg class="h-10 w-10 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                    <p class="mt-3 font-semibold text-white">{{ __('No deleted offers') }}</p>
                    <p class="mt-1 text-sm text-slate-400">{{ __('The trash is empty.') }}</p>
                </div>
            @endforelse
            <div class="mt-4">{{ $trashedOffers->links() }}</div>
        </section>

        {{-- Plans tab --}}
        <section x-show="tab === 'plans'" x-cloak class="app-card app-card--gradient space-y-3 p-5 sm:p-6">
            @forelse ($trashedPlans as $plan)
                <div class="flex flex-col gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex min-w-0 items-center gap-3">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/5">
                            <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M9 3h6M10 3v4m4-4v4M5 7h14v13a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 12h.01M12 12h.01M16 12h.01M8 16h.01M12 16h.01M16 16h.01" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="truncate font-semibold text-white">{{ $plan->customer?->name ?? __('No customer') }}</p>
                            <p class="text-xs text-slate-400">
                                @if ($plan->unit?->unit_number) #{{ $plan->unit->unit_number }} · {{ $plan->unit->project?->name ?? '' }} · @endif
                                {{ number_format((float) $plan->final_price, 0) }} {{ __('ج.م') }}
                                @if ($plan->items_count > 0) · {{ $plan->items_count }} {{ __('items') }} @endif
                                · {{ __('Deleted :date', ['date' => $plan->deleted_at?->diffForHumans() ?? '—']) }}
                            </p>
                        </div>
                    </div>
                    <div class="flex shrink-0 flex-wrap items-center gap-2">
                        <form method="POST" action="{{ route('dashboard.crm.plans.restore', $plan) }}">
                            @csrf
                            <button type="submit" class="app-button--ghost px-4 py-2 text-xs text-emerald-300 hover:text-emerald-200">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 3v5h5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                {{ __('Restore') }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('dashboard.crm.plans.force-delete', $plan) }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmAction('{{ __('Delete forever') }}', '{{ __('Permanently delete this plan :customer? This cannot be undone and will also remove its installment items.', ['customer' => $plan->customer?->name ?? $plan->name ?? '']) }}', () => this.closest('form').submit(), '{{ __('Delete forever') }}')" class="app-button app-button--danger px-4 py-2 text-xs">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                                {{ __('Delete forever') }}
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/5 py-14 text-center">
                    <svg class="h-10 w-10 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                    <p class="mt-3 font-semibold text-white">{{ __('No deleted plans') }}</p>
                    <p class="mt-1 text-sm text-slate-400">{{ __('The trash is empty.') }}</p>
                </div>
            @endforelse
            <div class="mt-4">{{ $trashedPlans->links() }}</div>
        </section>
    </div>
</div>
@endsection
