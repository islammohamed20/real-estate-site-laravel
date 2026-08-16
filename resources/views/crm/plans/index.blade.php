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

    <section class="dashboard-hero-card p-6 sm:p-8">
        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="badge badge-brand">{{ __('Customer hub') }}</span>
                    <span class="badge badge-success">{{ __('Installment Plans') }}</span>
                </div>
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('Installment Plans') }}</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">{{ __('All payment plans saved from the calculator and CRM, with their schedules and PDFs.') }}</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('dashboard.crm.index') }}" class="app-button--ghost">{{ __('CRM Home') }}</a>
                <a href="{{ route('dashboard.installments.index') }}" class="app-button">{{ __('Calculator') }}</a>
                <a href="{{ route('dashboard.crm.plans.trash') }}" class="app-button--ghost !text-rose-300 hover:!text-rose-200">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                    {{ __('Trash') }}
                    @if ($trashedCount > 0)<span class="rounded-full bg-rose-500/20 px-2 py-0.5 text-xs font-semibold text-rose-300">{{ $trashedCount }}</span>@endif
                </a>
            </div>
        </div>
    </section>

    <div class="grid gap-3 sm:grid-cols-3">
        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Total plans') }}</p>
            <p class="mt-1 text-2xl font-bold text-white">{{ number_format($totalPlans) }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Total value') }}</p>
            <p class="mt-1 text-2xl font-bold text-emerald-300">{{ number_format((float) $totalValue, 0) }} {{ __('ج.م') }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('From calculator') }}</p>
            <p class="mt-1 text-2xl font-bold text-brand-300">{{ number_format($fromCalculator) }}</p>
        </div>
    </div>

    <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-lg">
        <div class="border-t border-white/10 p-4">
            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($plans as $plan)
                    <div class="app-card app-card--gradient flex flex-col p-4 transition hover:border-brand-500/30">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="truncate text-lg font-semibold text-white">{{ $plan->customer?->name ?? __('No customer') }}</h3>
                                <p class="text-sm text-slate-400">{{ $plan->unit?->unit_number ? $plan->unit->unit_number.' · '.($plan->unit->project?->name ?? '') : __('Custom plan') }}</p>
                            </div>
                            <span class="shrink-0 rounded-full bg-brand-500/20 px-3 py-1 text-xs font-semibold text-brand-300">{{ $plan->installment_count }} {{ __('installments') }}</span>
                        </div>

                        <div class="mt-3 grid grid-cols-2 gap-3 text-sm text-slate-300">
                            <div>
                                <span class="block text-xs text-slate-500">{{ __('Final Price') }}</span>
                                <span class="font-semibold text-emerald-300">{{ number_format((float) $plan->final_price, 0) }} {{ __('ج.م') }}</span>
                            </div>
                            <div>
                                <span class="block text-xs text-slate-500">{{ __('Installment Amount') }}</span>
                                <span class="font-semibold text-white">{{ number_format((float) $plan->installment_amount, 0) }} {{ __('ج.م') }}</span>
                            </div>
                            <div>
                                <span class="block text-xs text-slate-500">{{ __('Down Payment') }}</span>
                                <span class="font-semibold text-white">{{ number_format((float) $plan->down_payment, 0) }} {{ __('ج.م') }}</span>
                            </div>
                            <div>
                                <span class="block text-xs text-slate-500">{{ __('Saved') }}</span>
                                <span class="font-semibold text-white">{{ $plan->created_at?->format('Y-m-d') }}</span>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center gap-2 border-t border-white/10 pt-3">
                            <a href="{{ route('dashboard.crm.plans.show', $plan) }}" class="app-button flex-1 !py-2 text-xs">{{ __('View') }}</a>
                            <a href="{{ route('dashboard.crm.plans.pdf', $plan) }}" class="app-button--ghost flex-1 !py-2 text-xs">{{ __('PDF') }}</a>
                            <form action="{{ route('dashboard.crm.plans.destroy', $plan) }}" method="POST" class="contents" onsubmit="return confirm('{{ __('Delete this plan?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-rose-500/30 bg-rose-500/10 text-rose-400 transition hover:bg-rose-500/20" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full rounded-2xl border border-white/10 bg-white/5 p-8 text-center text-slate-400">
                        <p class="text-lg font-semibold text-white">{{ __('No plans yet') }}</p>
                        <p class="mt-1 text-sm">{{ __('Save a plan from the calculator or an offer to see it here.') }}</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $plans->links() }}
            </div>
        </div>
    </section>
</div>
@endsection
