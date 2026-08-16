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
                    <svg class="h-7 w-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <circle cx="12" cy="8" r="4" stroke-width="1.8"/>
                        <path d="M4 21c0-4 3.6-6.5 8-6.5s8 2.5 8 6.5" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </span>
                <h1 class="stagger-item mt-4 text-2xl font-bold tracking-tight text-white" style="animation-delay:120ms">{{ __('Customer login') }}</h1>
                <p class="stagger-item mt-2 text-sm text-slate-400" style="animation-delay:200ms">{{ __('Access your reservations, offers and documents.') }}</p>
            </div>

            <form method="POST" action="{{ route('customer.login.store') }}" class="space-y-4">
                @csrf

                <label class="stagger-item block space-y-2" style="animation-delay:280ms">
                    <span class="text-sm font-medium text-slate-300">{{ __('Email or phone') }}</span>
                    <input type="text" name="login" value="{{ old('login') }}" required inputmode="email" autocomplete="username" class="app-input transition-all duration-300 focus:scale-[1.01]" placeholder="{{ __('you@example.com or 01XXXXXXXXX') }}" dir="ltr">
                    @error('login')
                        <span class="block text-sm text-rose-400">{{ $message }}</span>
                    @enderror
                </label>

                <label class="stagger-item block space-y-2" style="animation-delay:360ms">
                    <span class="text-sm font-medium text-slate-300">{{ __('Password') }}</span>
                    <input type="password" name="password" required autocomplete="current-password" class="app-input transition-all duration-300 focus:scale-[1.01]" placeholder="••••••••">
                    @error('password')
                        <span class="block text-sm text-rose-400">{{ $message }}</span>
                    @enderror
                </label>

                <label class="stagger-item flex items-center gap-3 rounded-2xl bg-white/5 px-4 py-3 text-sm text-slate-300 transition-colors duration-300 hover:bg-white/10" style="animation-delay:440ms">
                    <input type="checkbox" name="remember" value="1" class="rounded border-slate-700 bg-slate-900 text-brand-500 focus:ring-brand-500">
                    {{ __('Remember me') }}
                </label>

                <button type="submit" class="stagger-item app-button w-full transition-transform duration-300 hover:scale-[1.02] active:scale-[0.97]" style="animation-delay:520ms">{{ __('Sign in') }}</button>
            </form>

            <p class="stagger-item text-center text-sm text-slate-400" style="animation-delay:600ms">
                {{ __('Don\'t have an account?') }}
                <a href="{{ route('customer.register') }}" class="font-semibold text-brand-400 transition hover:text-brand-300">{{ __('Create one') }}</a>
            </p>
        </div>

        <p class="stagger-item mt-6 text-center text-sm text-slate-500" style="animation-delay:680ms">
            <a href="{{ route('home') }}" class="transition hover:text-slate-300">← {{ __('Back to website') }}</a>
        </p>
    </div>
@endsection
