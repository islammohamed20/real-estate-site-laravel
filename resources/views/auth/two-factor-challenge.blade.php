@php($hideSplash = true)
@extends('layouts.public')

@section('content')
    <div class="relative mx-auto mt-10 max-w-md sm:mt-16">
        {{-- Floating ambient orbs behind the card --}}
        <div class="pointer-events-none absolute -inset-10 -z-10" aria-hidden="true">
            <div class="animate-float-slow absolute -top-12 right-0 h-44 w-44 rounded-full bg-brand-500/15 blur-3xl"></div>
            <div class="animate-float-slower absolute -bottom-14 -left-8 h-52 w-52 rounded-full bg-violet-500/15 blur-3xl"></div>
            <div class="animate-float-slow absolute top-1/3 -right-6 h-28 w-28 rounded-full bg-sky-500/10 blur-2xl" style="animation-delay:1.2s"></div>
        </div>

        <div class="stagger-item app-card space-y-6 p-6 sm:p-8 transition-all duration-500 hover:border-brand-500/30">
            <div class="text-center">
                <span class="animate-glow-pulse mx-auto flex h-14 w-14 items-center justify-center rounded-3xl bg-gradient-to-br from-brand-500 to-violet-600 shadow-lg shadow-brand-500/30">
                    <svg class="h-7 w-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z"/>
                        <path d="m9 12 2 2 4-4"/>
                    </svg>
                </span>
                <h1 class="stagger-item mt-4 text-2xl font-bold tracking-tight text-white" style="animation-delay:120ms">{{ __('Two-Factor Authentication') }}</h1>
                <p class="stagger-item mt-2 text-sm text-slate-400" style="animation-delay:200ms">{{ __('Enter the 6-digit code from your authenticator app, or a one-time recovery code.') }}</p>
            </div>

            @if (session('status'))
                <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('2fa.verify.store') }}" class="space-y-4">
                @csrf

                <label class="stagger-item block space-y-2" style="animation-delay:280ms">
                    <span class="text-sm font-medium text-slate-300">{{ __('Authentication code') }}</span>
                    <input
                        type="text"
                        name="code"
                        required
                        autofocus
                        autocomplete="one-time-code"
                        inputmode="numeric"
                        pattern="[0-9a-zA-Z]*"
                        maxlength="32"
                        dir="ltr"
                        class="app-input text-center text-lg font-mono tracking-[0.4em] transition-all duration-300 focus:scale-[1.01]"
                        placeholder="••••••"
                    >
                    @error('code')
                        <span class="block text-sm text-rose-400">{{ $message }}</span>
                    @enderror
                </label>

                <button type="submit" class="stagger-item app-button w-full transition-transform duration-300 hover:scale-[1.02] active:scale-[0.97]" style="animation-delay:360ms">
                    {{ __('Verify & Continue') }}
                </button>
            </form>

            <div class="stagger-item rounded-2xl border border-white/10 bg-white/5 p-4 text-center" style="animation-delay:440ms">
                <p class="text-xs text-slate-400">{{ __('Lost your authenticator app?') }}</p>
                <p class="mt-1 text-xs text-slate-400">{{ __('Use one of your one-time recovery codes instead.') }}</p>
            </div>
        </div>

        <p class="stagger-item mt-6 text-center text-sm text-slate-500" style="animation-delay:520ms">
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="transition hover:text-slate-300">← {{ __('Sign out and try again') }}</button>
            </form>
        </p>
    </div>
@endsection
