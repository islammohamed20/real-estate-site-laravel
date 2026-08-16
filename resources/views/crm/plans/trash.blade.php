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
                    <span class="badge badge-danger">{{ $trashedCount }} {{ __('deleted plans') }}</span>
                    <span class="badge badge-success">{{ __('Restorable') }}</span>
                </div>

                <div>
                    <p class="mobile-section-title">{{ __('Recycle bin') }}</p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('Trashed plans') }}</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">
                        {{ __('Soft-deleted installment plans. Restore them or permanently delete them.') }}
                    </p>
                    <p class="mt-2 text-xs text-rose-300/80">
                        {{ __('Permanently deleting an item cannot be undone.') }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('dashboard.crm.plans.index') }}" class="app-button--ghost">{{ __('Back to Plans') }}</a>
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4 shadow-lg shadow-black/10 backdrop-blur-xl">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Deleted plans') }}</p>
                    <p class="mt-2 text-3xl font-bold text-rose-400">{{ number_format($trashedCount) }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4 shadow-lg shadow-black/10 backdrop-blur-xl">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Restorable value') }}</p>
                    <p class="mt-2 text-3xl font-bold text-emerald-300">{{ number_format((float) $restorableValue, 0) }} {{ __('ج.م') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="app-card app-card--gradient space-y-3 p-5 sm:p-6">
        @forelse ($plans as $plan)
            <div class="flex flex-col gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex min-w-0 items-center gap-3">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/5">
                        <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M9 3h6M10 3v4m4-4v4M5 7h14v13a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 12h.01M12 12h.01M16 12h.01M8 16h.01M12 16h.01M16 16h.01" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <div class="min-w-0">
                        <p class="truncate font-semibold text-white">
                            {{ $plan->customer?->name ?? __('No customer') }}
                            @if ($plan->unit?->unit_number)
                                <span class="text-xs font-normal text-slate-400">#{{ $plan->unit->unit_number }} · {{ $plan->unit->project?->name ?? '' }}</span>
                            @endif
                        </p>
                        <p class="text-xs text-slate-400">
                            {{ number_format((float) $plan->final_price, 0) }} {{ __('ج.م') }}
                            · {{ $plan->installment_count }} {{ __('installments') }}
                            · {{ __('Deleted :date', ['date' => $plan->deleted_at?->diffForHumans() ?? '—']) }}
                            @if ($plan->items_count > 0) · {{ $plan->items_count }} {{ __('items') }} @endif
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

        <div class="mt-4">
            {{ $plans->links() }}
        </div>
    </section>
</div>
@endsection
