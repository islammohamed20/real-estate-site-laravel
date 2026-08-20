@php($hideSplash = true)
@extends('layouts.public')

@section('content')
    <div class="relative mx-auto mt-10 max-w-md sm:mt-16">
        <div class="pointer-events-none absolute -inset-10 -z-10" aria-hidden="true">
            <div class="animate-float-slow absolute -top-12 right-0 h-44 w-44 rounded-full bg-brand-500/15 blur-3xl"></div>
            <div class="animate-float-slower absolute -bottom-14 -left-8 h-52 w-52 rounded-full bg-violet-500/15 blur-3xl"></div>
        </div>

        <div class="stagger-item app-card space-y-6 p-6 sm:p-8 transition-all duration-500 hover:border-brand-500/30">
            <div class="text-center">
                <span class="animate-glow-pulse mx-auto flex h-14 w-14 items-center justify-center rounded-3xl bg-gradient-to-br from-brand-500 to-violet-600 shadow-lg shadow-brand-500/30">
                    <svg class="h-7 w-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path d="M15 7a2 2 0 0 1 2 2m4 0a6 6 0 0 1-7.74 5.74L11 17H9v2H7v2H4a1 1 0 0 1-1-1v-2.59a1 1 0 0 1 .29-.7l3.97-3.97A6 6 0 0 1 21 9Z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <h1 class="stagger-item mt-4 text-2xl font-bold tracking-tight text-white" style="animation-delay:120ms">{{ __('Forgot password?') }}</h1>
                <p class="stagger-item mt-2 text-sm text-slate-400" style="animation-delay:200ms">{{ __('Enter your email and we will send you a password reset link.') }}</p>
            </div>

            @if (session('status'))
                <div class="stagger-item rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300" style="animation-delay:240ms">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="stagger-item rounded-2xl border border-rose-500/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-300" style="animation-delay:240ms">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="/real-statement-control/forgot-password" class="space-y-4">
                @csrf
                <input type="hidden" name="locale" value="{{ app()->getLocale() }}">

                <label class="stagger-item block space-y-2" style="animation-delay:280ms">
                    <span class="text-sm font-medium text-slate-300">{{ __('Email') }}</span>
                    <input type="email" name="email" value="{{ old('email') }}" required inputmode="email" autocomplete="email" class="app-input transition-all duration-300 focus:scale-[1.01]" placeholder="{{ __('you@company.com') }}">
                </label>

                <button type="submit" class="stagger-item app-button w-full transition-transform duration-300 hover:scale-[1.02] active:scale-[0.97]" style="animation-delay:360ms">{{ __('Send reset link') }}</button>
            </form>

            <p class="stagger-item text-center text-sm text-slate-400" style="animation-delay:440ms">
                <a href="{{ route('login') }}" class="transition hover:text-slate-300">← {{ __('Back to login') }}</a>
            </p>
        </div>
    </div>
@endsection
