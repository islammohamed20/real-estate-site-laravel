@extends('layouts.public')

@php
    $reservationLabels = [
        'pending' => __('Pending'),
        'paid' => __('Paid'),
        'converted' => __('Converted'),
        'cancelled' => __('Cancelled'),
        'expired' => __('Expired'),
    ];
    $offerLabels = [
        'draft' => __('Draft'),
        'sent' => __('Sent'),
        'accepted' => __('Accepted'),
        'rejected' => __('Rejected'),
        'expired' => __('Expired'),
    ];
@endphp

@section('content')
    <div class="mx-auto w-full max-w-5xl px-4 py-8 sm:py-10">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-brand-400">{{ __('Customer portal') }}</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-white sm:text-3xl">
                    {{ __('Welcome, :name', ['name' => $customer->name]) }}
                </h1>
                <p class="mt-2 text-sm text-slate-400">{{ $customer->email ?? $customer->phone }}</p>
            </div>
            <form method="POST" action="{{ route('customer.logout') }}">
                @csrf
                <button type="submit" class="app-button bg-rose-600/20 text-rose-300 hover:bg-rose-600/30">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    {{ __('Sign out') }}
                </button>
            </form>
        </div>

        <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="app-card flex items-center gap-4 p-5">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-brand-500/15 text-brand-300">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M6 22V6a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16l-3-2-2 2-2-2-3 2Z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $customer->reservations->count() }}</p>
                    <p class="text-xs text-slate-400">{{ __('Reservations') }}</p>
                </div>
            </div>

            <div class="app-card flex items-center gap-4 p-5">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-500/15 text-emerald-300">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M12 3v18M3 12h18" stroke-width="1.8" stroke-linecap="round"/></svg>
                </span>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $customer->offers->count() }}</p>
                    <p class="text-xs text-slate-400">{{ __('Offers') }}</p>
                </div>
            </div>

            <div class="app-card flex items-center gap-4 p-5">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-500/15 text-amber-300">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M19 5H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2ZM3 9h18" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $customer->deals->count() }}</p>
                    <p class="text-xs text-slate-400">{{ __('Deals') }}</p>
                </div>
            </div>

            <div class="app-card flex items-center gap-4 p-5">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-violet-500/15 text-violet-300">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-width="1.8"/><path d="M14 2v6h6" stroke-width="1.8"/><path d="M16 13H8M16 17H8M10 9H8" stroke-width="1.8" stroke-linecap="round"/></svg>
                </span>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $customer->plans->count() }}</p>
                    <p class="text-xs text-slate-400">{{ __('Payment plans') }}</p>
                </div>
            </div>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            {{-- Reservations --}}
            <section class="app-card h-full space-y-4 p-5">
                <h2 class="flex items-center gap-2 text-lg font-semibold text-white">
                    <svg class="h-5 w-5 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M8 7V3m8 4V3M4 7h16v13H4z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    {{ __('Reservations') }}
                </h2>
                @forelse ($customer->reservations as $reservation)
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <p class="font-semibold text-white">{{ $reservation->reservation_number }}</p>
                            <span class="rounded-full bg-brand-500/15 px-2.5 py-1 text-xs font-medium text-brand-300">{{ $reservationLabels[$reservation->status] ?? $reservation->status }}</span>
                        </div>
                        <p class="mt-1 text-sm text-slate-400">
                            {{ $reservation->unit?->unit_number ?? '—' }}
                            @if ($reservation->deposit_amount)
                                · {{ __('Deposit') }}: {{ number_format((float) $reservation->deposit_amount, 2) }}
                            @endif
                        </p>
                        @if ($reservation->expires_at)
                            <p class="mt-1 text-xs text-slate-500">{{ __('Valid until') }} {{ $reservation->expires_at->format('d M Y') }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-400">{{ __('No reservations yet.') }}</p>
                @endforelse
            </section>

            {{-- Offers --}}
            <section class="app-card h-full space-y-4 p-5">
                <h2 class="flex items-center gap-2 text-lg font-semibold text-white">
                    <svg class="h-5 w-5 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M12 3v18M3 12h18" stroke-width="1.8" stroke-linecap="round"/></svg>
                    {{ __('Offers') }}
                </h2>
                @forelse ($customer->offers as $offer)
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <p class="font-semibold text-white">{{ $offer->offer_number }}</p>
                            <span class="rounded-full bg-emerald-500/15 px-2.5 py-1 text-xs font-medium text-emerald-300">{{ $offerLabels[$offer->status] ?? $offer->status }}</span>
                        </div>
                        <p class="mt-1 text-sm text-slate-400">
                            {{ $offer->project?->name ?? '—' }}
                            @if ($offer->total_amount)
                                · {{ number_format((float) $offer->total_amount, 2) }}
                            @endif
                        </p>
                        @if ($offer->valid_until)
                            <p class="mt-1 text-xs text-slate-500">{{ __('Valid until') }} {{ $offer->valid_until->format('d M Y') }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-400">{{ __('No offers yet.') }}</p>
                @endforelse
            </section>
        </div>

        {{-- Payment plans --}}
        <section class="app-card mt-6 space-y-4 p-5">
            <h2 class="flex items-center gap-2 text-lg font-semibold text-white">
                <svg class="h-5 w-5 text-violet-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-width="1.8"/><path d="M14 2v6h6" stroke-width="1.8"/><path d="M16 13H8M16 17H8M10 9H8" stroke-width="1.8" stroke-linecap="round"/></svg>
                {{ __('Payment plans') }}
            </h2>
            @forelse ($customer->plans as $plan)
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <p class="font-semibold text-white">{{ $plan->name }}</p>
                        <span class="rounded-full bg-emerald-500/15 px-2.5 py-1 text-xs font-medium text-emerald-300">{{ $plan->status === 'active' ? __('Active') : __(ucfirst($plan->status)) }}</span>
                    </div>
                    <p class="mt-1 text-sm text-slate-400">
                        {{ $plan->unit?->unit_number ?? $plan->project?->name ?? __('Custom plan') }}
                        @if ($plan->final_price)
                            · {{ __('Final Price') }}: {{ number_format((float) $plan->final_price, 2) }}
                        @endif
                        @if ($plan->installment_count)
                            · {{ $plan->installment_count }} {{ __('installments') }}
                        @endif
                    </p>
                    @if ($plan->created_at)
                        <p class="mt-1 text-xs text-slate-500">{{ __('Saved on') }} {{ $plan->created_at->format('d M Y') }}</p>
                    @endif
                </div>
            @empty
                <p class="text-sm text-slate-400">{{ __('No payment plans yet. Use the calculator to build your plan.') }}</p>
            @endforelse
        </section>
    </div>
@endsection
