@php($hideSplash = true)
@extends('layouts.public')

@section('content')
    <div class="relative mx-auto mt-10 max-w-md sm:mt-16">
        {{-- Floating ambient orbs behind the card --}}
        <div class="pointer-events-none absolute -inset-10 -z-10" aria-hidden="true">
            <div class="animate-float-slow absolute -top-12 right-0 h-44 w-44 rounded-full bg-brand-500/15 blur-3xl"></div>
            <div class="animate-float-slower absolute -bottom-14 -left-8 h-52 w-52 rounded-full bg-violet-500/15 blur-3xl"></div>
        </div>

        @if (session('status'))
            <div class="stagger-item mb-4 rounded-2xl border border-emerald-500/25 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        <div class="stagger-item app-card space-y-6 p-6 sm:p-8 transition-all duration-500 hover:border-brand-500/30">
            <div class="text-center">
                <span class="animate-glow-pulse mx-auto flex h-14 w-14 items-center justify-center rounded-3xl bg-gradient-to-br from-brand-500 to-violet-600 shadow-lg shadow-brand-500/30">
                    <svg class="h-7 w-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <rect x="2" y="4" width="20" height="16" rx="2" stroke-width="1.8"/>
                        <path d="m22 7-10 6L2 7" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <h1 class="stagger-item mt-4 text-2xl font-bold tracking-tight text-white" style="animation-delay:120ms">{{ __('Verify your email') }}</h1>
                <p class="stagger-item mt-2 text-sm leading-relaxed text-slate-400" style="animation-delay:180ms">
                    {{ __('We sent a 6-digit verification code to') }}
                    <strong dir="ltr" class="font-semibold text-brand-300">{{ $customer->email }}</strong>.
                </p>
            </div>

            <form method="POST" action="{{ route('customer.verify.store') }}" class="space-y-4">
                @csrf

                <label class="stagger-item block space-y-2" style="animation-delay:240ms">
                    <span class="text-sm font-medium text-slate-300">{{ __('Verification code') }}</span>
                    <input
                        type="text"
                        name="code"
                        value="{{ old('code') }}"
                        required
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        maxlength="6"
                        pattern="[0-9]{6}"
                        dir="ltr"
                        placeholder="••••••"
                        class="app-input text-center text-2xl font-bold tracking-[0.5em] transition-all duration-300 focus:scale-[1.01]"
                        autofocus
                    >
                    @error('code')
                        <span class="block text-sm text-rose-400">{{ $message }}</span>
                    @enderror
                </label>

                <button type="submit" class="stagger-item app-button w-full transition-transform duration-300 hover:scale-[1.02] active:scale-[0.97]" style="animation-delay:300ms">
                    {{ __('Verify and continue') }}
                </button>
            </form>

            <form method="POST" action="{{ route('customer.verify.resend') }}" class="stagger-item text-center" style="animation-delay:360ms">
                @csrf
                <span class="text-sm text-slate-400">{{ __('Didn\'t receive the code?') }}</span>
                <button type="submit" class="text-sm font-semibold text-brand-400 transition hover:text-brand-300">
                    {{ __('Resend code') }}
                </button>
            </form>

            <p class="stagger-item text-center text-sm text-slate-500" style="animation-delay:420ms">
                <a href="{{ route('customer.login') }}" class="transition hover:text-slate-300">← {{ __('Back to login') }}</a>
            </p>
        </div>
    </div>
@endsection
