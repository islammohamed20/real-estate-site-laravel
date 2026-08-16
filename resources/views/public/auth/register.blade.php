@php($hideSplash = true)
@extends('layouts.public')

@section('content')
    <div class="relative mx-auto mt-10 max-w-md sm:mt-16">
        {{-- Floating ambient orbs behind the card --}}
        <div class="pointer-events-none absolute -inset-10 -z-10" aria-hidden="true">
            <div class="animate-float-slow absolute -top-12 right-0 h-44 w-44 rounded-full bg-brand-500/15 blur-3xl"></div>
            <div class="animate-float-slower absolute -bottom-14 -left-8 h-52 w-52 rounded-full bg-violet-500/15 blur-3xl"></div>
        </div>

        <div class="stagger-item app-card space-y-6 p-6 sm:p-8 transition-all duration-500 hover:border-brand-500/30">
            <div class="text-center">
                <span class="animate-glow-pulse mx-auto flex h-14 w-14 items-center justify-center rounded-3xl bg-gradient-to-br from-brand-500 to-violet-600 shadow-lg shadow-brand-500/30">
                    <svg class="h-7 w-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <circle cx="12" cy="8" r="4" stroke-width="1.8"/>
                        <path d="M4 21c0-4 3.6-6.5 8-6.5s8 2.5 8 6.5" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M19 3v6M16 6h6" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </span>
                <h1 class="stagger-item mt-4 text-2xl font-bold tracking-tight text-white" style="animation-delay:120ms">{{ __('Create account') }}</h1>
                <p class="stagger-item mt-2 text-sm text-slate-400" style="animation-delay:180ms">{{ __('Follow your reservations and offers.') }}</p>
            </div>

            <form method="POST" action="{{ route('customer.register.store') }}" class="space-y-4">
                @csrf

                <label class="stagger-item block space-y-2" style="animation-delay:240ms">
                    <span class="text-sm font-medium text-slate-300">{{ __('Full name') }}</span>
                    <input type="text" name="name" value="{{ old('name') }}" required autocomplete="name" class="app-input transition-all duration-300 focus:scale-[1.01]" placeholder="{{ __('Your name') }}">
                    @error('name')
                        <span class="block text-sm text-rose-400">{{ $message }}</span>
                    @enderror
                </label>

                <label class="stagger-item block space-y-2" style="animation-delay:300ms">
                    <span class="text-sm font-medium text-slate-300">{{ __('Occupation') }}</span>
                    <input type="text" name="occupation" value="{{ old('occupation') }}" class="app-input transition-all duration-300 focus:scale-[1.01]" placeholder="{{ __('e.g. Engineer, Doctor, Business owner…') }}">
                    @error('occupation')
                        <span class="block text-sm text-rose-400">{{ $message }}</span>
                    @enderror
                </label>

                <label class="stagger-item block space-y-2" style="animation-delay:360ms">
                    <span class="text-sm font-medium text-slate-300">{{ __('Phone') }}</span>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required inputmode="tel" autocomplete="tel" class="app-input transition-all duration-300 focus:scale-[1.01]" placeholder="{{ __('01XXXXXXXXX') }}">
                    @error('phone')
                        <span class="block text-sm text-rose-400">{{ $message }}</span>
                    @enderror
                </label>

                <label class="stagger-item block space-y-2" style="animation-delay:420ms">
                    <span class="text-sm font-medium text-slate-300">{{ __('Email') }} <span class="text-xs text-slate-500">{{ __('(optional)') }}</span></span>
                    <input type="email" name="email" value="{{ old('email') }}" inputmode="email" autocomplete="email" class="app-input transition-all duration-300 focus:scale-[1.01]" placeholder="{{ __('you@example.com') }}">
                    @error('email')
                        <span class="block text-sm text-rose-400">{{ $message }}</span>
                    @enderror
                </label>

                <p class="stagger-item rounded-2xl border border-brand-500/20 bg-brand-500/10 px-4 py-3 text-xs leading-relaxed text-brand-200" style="animation-delay:480ms">
                    {{ __('If your phone number is already in our customer records, registering will link this account to it.') }}
                </p>

                <label class="stagger-item block space-y-2" style="animation-delay:540ms">
                    <span class="text-sm font-medium text-slate-300">{{ __('Password') }}</span>
                    <input type="password" name="password" required autocomplete="new-password" class="app-input transition-all duration-300 focus:scale-[1.01]" placeholder="••••••••">
                    @error('password')
                        <span class="block text-sm text-rose-400">{{ $message }}</span>
                    @enderror
                </label>

                <label class="stagger-item block space-y-2" style="animation-delay:600ms">
                    <span class="text-sm font-medium text-slate-300">{{ __('Confirm password') }}</span>
                    <input type="password" name="password_confirmation" required autocomplete="new-password" class="app-input transition-all duration-300 focus:scale-[1.01]" placeholder="••••••••">
                </label>

                <button type="submit" class="stagger-item app-button w-full transition-transform duration-300 hover:scale-[1.02] active:scale-[0.97]" style="animation-delay:660ms">{{ __('Create account') }}</button>
            </form>

            <p class="stagger-item text-center text-sm text-slate-400" style="animation-delay:720ms">
                {{ __('Already have an account?') }}
                <a href="{{ route('customer.login') }}" class="font-semibold text-brand-400 transition hover:text-brand-300">{{ __('Sign in') }}</a>
            </p>
        </div>

        <p class="stagger-item mt-6 text-center text-sm text-slate-500" style="animation-delay:780ms">
            <a href="{{ route('home') }}" class="transition hover:text-slate-300">← {{ __('Back to website') }}</a>
        </p>
    </div>
@endsection
