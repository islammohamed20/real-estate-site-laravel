@extends('layouts.public')

@section('content')
    <div class="mx-auto w-full max-w-2xl px-4 py-8 sm:py-10">
        <section class="app-card space-y-6 p-5 sm:p-8">
            <div>
                <p class="text-sm text-brand-400">{{ __('Customer portal') }}</p>
                <h1 class="mt-1 text-2xl font-bold text-white">{{ __('Set up Google Authenticator') }}</h1>
                <p class="mt-2 text-sm leading-6 text-slate-400">{{ __('Scan the QR code with your authenticator app, then enter the current 6-digit code.') }}</p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div class="flex items-center justify-center rounded-2xl border border-white/10 bg-white p-4">
                    <img src="{{ $qrCode }}" alt="{{ __('QR Code') }}" class="h-52 w-52">
                </div>
                <div class="space-y-4">
                    <p class="text-sm font-semibold text-white">{{ __('Manual setup key') }}</p>
                    <code class="block select-all rounded-xl border border-white/10 bg-slate-950 px-3 py-3 text-center font-mono text-sm tracking-[0.2em] text-brand-300" dir="ltr">{{ $secret }}</code>
                    <p class="text-xs leading-5 text-slate-500">{{ __('Keep this key private. It can generate codes for your account.') }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('customer.2fa.authenticator.enable') }}" class="space-y-4 border-t border-white/10 pt-5">
                @csrf
                <label class="block space-y-2">
                    <span class="text-sm font-medium text-slate-300">{{ __('Authenticator code') }}</span>
                    <input type="text" name="code" required inputmode="numeric" pattern="[0-9]*" maxlength="6" dir="ltr" autofocus class="app-input max-w-[240px] text-center font-mono text-lg tracking-[0.4em]" placeholder="••••••">
                    @error('code') <span class="block text-sm text-rose-400">{{ $message }}</span> @enderror
                </label>
                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="app-button">{{ __('Enable Authenticator') }}</button>
                    <a href="{{ route('customer.account') }}" class="app-button--ghost">{{ __('Cancel') }}</a>
                </div>
            </form>
        </section>
    </div>
@endsection
