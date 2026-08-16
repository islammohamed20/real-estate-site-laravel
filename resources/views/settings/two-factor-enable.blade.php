@extends('layouts.dashboard')

@section('content')
    <div class="mx-auto max-w-2xl space-y-6">
        <section class="dashboard-hero-card p-4 sm:p-6">
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-brand-500/20 blur-3xl"></div>
            <div class="relative">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="badge badge-brand">{{ __('Two-Factor Authentication') }}</span>
                        <span class="badge badge-success">{{ __('Google Authenticator') }}</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">{{ __('Enable Two-Factor Authentication') }}</h1>
                        <p class="mt-3 max-w-xl text-sm leading-6 text-slate-300">{{ __('Scan the QR code with Google Authenticator (or any TOTP app), then enter the 6-digit code to confirm.') }}</p>
                    </div>
                </div>
            </div>
        </section>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        <div class="app-card app-card--gradient space-y-6">
            <div class="grid gap-6 sm:grid-cols-2">
                {{-- QR code --}}
                <div class="flex flex-col items-center justify-center rounded-2xl border border-white/10 bg-white/5 p-6">
                    <p class="mb-4 text-sm font-semibold text-white">{{ __('Scan this QR code') }}</p>
                    <div class="rounded-2xl bg-white p-3 shadow-2xl shadow-black/40">
                        <img src="{{ $qrCode }}" alt="{{ __('QR Code') }}" class="h-48 w-48">
                    </div>
                    <p class="mt-4 text-center text-xs text-slate-400">{{ __('Open Google Authenticator → + → Scan QR code') }}</p>
                </div>

                {{-- Manual secret --}}
                <div class="space-y-4">
                    <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('Or enter the secret manually') }}</p>
                        <div class="mt-3 flex items-center gap-2">
                            <code class="flex-1 select-all rounded-lg border border-white/10 bg-slate-900 px-3 py-2 text-center font-mono text-sm tracking-[0.2em] text-brand-300" dir="ltr">{{ $secret }}</code>
                            <button
                                type="button"
                                onclick="navigator.clipboard?.writeText(this.previousElementSibling.textContent.trim())"
                                class="app-button--ghost shrink-0 !px-2.5 !py-2"
                                title="{{ __('Copy') }}"
                            >
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                            </button>
                        </div>
                        <p class="mt-3 text-xs text-slate-500">{{ __('Account: your email · Type: Time-based (TOTP)') }}</p>
                    </div>

                    <div class="rounded-2xl border border-amber-500/20 bg-amber-500/10 p-4">
                        <div class="flex items-start gap-3">
                            <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/><path d="M12 9v4M12 17h.01"/></svg>
                            <p class="text-xs leading-5 text-amber-200/80">{{ __('Store your secret somewhere safe. If you lose access to both your phone and recovery codes, the account cannot be recovered.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Confirmation form --}}
            <form method="POST" action="{{ route('dashboard.2fa.enable.store') }}" class="space-y-4 border-t border-white/10 pt-6">
                @csrf
                <label class="block space-y-2">
                    <span class="text-sm font-medium text-slate-300">{{ __('Confirm with a 6-digit code') }}</span>
                    <input
                        type="text"
                        name="code"
                        required
                        autofocus
                        inputmode="numeric"
                        pattern="[0-9]*"
                        maxlength="6"
                        dir="ltr"
                        class="app-input w-full max-w-[220px] text-center font-mono text-lg tracking-[0.4em]"
                        placeholder="••••••"
                    >
                    @error('code')
                        <span class="block text-sm text-rose-400">{{ $message }}</span>
                    @enderror
                </label>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="app-button">{{ __('Enable Two-Factor Authentication') }}</button>
                    <a href="{{ route('dashboard.settings.index', ['tab' => 'security']) }}" class="app-button--ghost">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection
