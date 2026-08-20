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
                    <span class="text-sm font-medium text-slate-300">{{ __('Email') }}</span>
                    <input type="email" name="email" value="{{ old('email') }}" required inputmode="email" autocomplete="email" class="app-input transition-all duration-300 focus:scale-[1.01]" placeholder="{{ __('you@example.com') }}">
                    @error('email')
                        <span class="block text-sm text-rose-400">{{ $message }}</span>
                    @enderror
                </label>

                <p class="stagger-item rounded-2xl border border-brand-500/20 bg-brand-500/10 px-4 py-3 text-xs leading-relaxed text-brand-200" style="animation-delay:480ms">
                    {{ __('If your phone number is already in our customer records, registering will link this account to it.') }}
                    {{ __('We will send a 6-digit verification code to your email to activate the account.') }}
                </p>

                <label class="stagger-item block space-y-2" style="animation-delay:540ms">
                    <span class="text-sm font-medium text-slate-300">{{ __('Password') }}</span>
                    <input type="password" name="password" required autocomplete="new-password" class="app-input transition-all duration-300 focus:scale-[1.01]" placeholder="••••••••">
                    @error('password')
                        <span class="block text-sm text-rose-400">{{ $message }}</span>
                    @enderror

                    <div class="password-strength hidden pt-0.5" data-password-strength>
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex flex-1 gap-1.5" role="meter" aria-valuemin="0" aria-valuemax="4" aria-valuenow="0" aria-label="{{ __('Password strength') }}" data-strength-segments>
                                <span class="h-1.5 flex-1 rounded-full bg-white/10 transition-all duration-300"></span>
                                <span class="h-1.5 flex-1 rounded-full bg-white/10 transition-all duration-300"></span>
                                <span class="h-1.5 flex-1 rounded-full bg-white/10 transition-all duration-300"></span>
                                <span class="h-1.5 flex-1 rounded-full bg-white/10 transition-all duration-300"></span>
                            </div>
                            <span class="min-w-16 text-end text-xs font-semibold text-slate-500" data-strength-label>{{ __('Weak') }}</span>
                        </div>
                    </div>
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

@push('scripts')
<script>
    (function () {
        const field = document.querySelector('input[name="password"]');
        if (!field) return;

        const meter = field.closest('label')?.querySelector('[data-password-strength]');
        if (!meter) return;

        const segments = meter.querySelectorAll('[data-strength-segments] > span');
        const label = meter.querySelector('[data-strength-label]');
        const bar = meter.querySelector('[role="meter"]');

        const LABELS = {!! json_encode([__('Weak'), __('Fair'), __('Good'), __('Strong')]) !!};
        const FILL = ['bg-rose-500', 'bg-rose-500', 'bg-amber-500', 'bg-brand-500', 'bg-emerald-500'];
        const TEXT = ['text-rose-400', 'text-rose-400', 'text-amber-400', 'text-brand-300', 'text-emerald-400'];

        const COMMON = new Set([
            'password', 'password1', 'password123', '12345678', '123456789',
            '1234567890', 'qwerty123', 'qwertyuiop', 'abc12345', 'admin123',
            'letmein', 'iloveyou', '11111111', '00000000', '12345678a',
        ]);

        function levelOf(value) {
            const v = value.toLowerCase();

            if (COMMON.has(v)) return 1;

            let score = 0;
            if (value.length >= 8) score += 1;
            if (value.length >= 12) score += 1;
            if (/[a-z]/.test(value) && /[A-Z]/.test(value)) score += 1;
            if (/\d/.test(value)) score += 1;
            if (/[^A-Za-z0-9]/.test(value)) score += 1;

            if (new Set(v).size <= 3) score = Math.min(score, 1);

            if (score >= 5) return 4;
            if (score >= 3) return 3;
            if (score === 2) return 2;
            return 1;
        }

        function render() {
            if (!field.value) {
                meter.classList.add('hidden');
                return;
            }

            meter.classList.remove('hidden');
            const level = levelOf(field.value);

            segments.forEach((segment, i) => {
                FILL.forEach((color) => segment.classList.remove(color));
                segment.classList.toggle('bg-white/10', i >= level);
                if (i < level) segment.classList.add(FILL[level]);
            });

            label.textContent = LABELS[level - 1];
            label.className = 'min-w-16 text-end text-xs font-semibold ' + TEXT[level];
            bar.setAttribute('aria-valuenow', String(level));
        }

        field.addEventListener('input', render);

        if (field.value) render();
    })();
</script>
@endpush
