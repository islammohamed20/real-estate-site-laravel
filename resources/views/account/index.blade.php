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

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <section class="app-card space-y-4 p-5">
                <div>
                    <h2 class="text-lg font-semibold text-white">{{ __('Profile') }}</h2>
                    <p class="mt-1 text-sm text-slate-400">{{ __('Keep your contact details up to date.') }}</p>
                </div>
                <form method="POST" action="{{ route('customer.profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <label class="block space-y-2">
                        <span class="text-sm font-medium text-slate-300">{{ __('Full name') }}</span>
                        <input type="text" name="name" value="{{ old('name', $customer->name) }}" required maxlength="255" class="app-input">
                        @error('name') <span class="block text-sm text-rose-400">{{ $message }}</span> @enderror
                    </label>
                    <label class="block space-y-2">
                        <span class="text-sm font-medium text-slate-300">{{ __('Occupation') }}</span>
                        <input type="text" name="occupation" value="{{ old('occupation', $customer->occupation) }}" maxlength="255" class="app-input">
                        @error('occupation') <span class="block text-sm text-rose-400">{{ $message }}</span> @enderror
                    </label>
                    <label class="block space-y-2">
                        <span class="text-sm font-medium text-slate-300">{{ __('Address') }}</span>
                        <textarea name="address" maxlength="1000" class="app-input min-h-24">{{ old('address', $customer->address) }}</textarea>
                        @error('address') <span class="block text-sm text-rose-400">{{ $message }}</span> @enderror
                    </label>
                    <button type="submit" class="app-button">{{ __('Save changes') }}</button>
                </form>
            </section>

            <section class="app-card space-y-4 p-5">
                <div>
                    <h2 class="text-lg font-semibold text-white">{{ __('Security') }}</h2>
                    <p class="mt-1 text-sm text-slate-400">{{ __('Use a strong password to protect your account.') }}</p>
                </div>
                <form method="POST" action="{{ route('customer.password.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <label class="block space-y-2">
                        <span class="text-sm font-medium text-slate-300">{{ __('Current password') }}</span>
                        <input type="password" name="current_password" required autocomplete="current-password" class="app-input">
                        @error('current_password') <span class="block text-sm text-rose-400">{{ $message }}</span> @enderror
                    </label>
                    <label class="block space-y-2">
                        <span class="text-sm font-medium text-slate-300">{{ __('New password') }}</span>
                        <input type="password" name="password" required autocomplete="new-password" class="app-input">
                        @error('password') <span class="block text-sm text-rose-400">{{ $message }}</span> @enderror
                    </label>
                    <label class="block space-y-2">
                        <span class="text-sm font-medium text-slate-300">{{ __('Confirm password') }}</span>
                        <input type="password" name="password_confirmation" required autocomplete="new-password" class="app-input">
                    </label>
                    <button type="submit" class="app-button">{{ __('Change password') }}</button>
                </form>

                @if (session('customer.password_otp_pending'))
                    <form method="POST" action="{{ route('customer.password.verify') }}" class="space-y-3 rounded-2xl border border-brand-500/20 bg-brand-500/10 p-4">
                        @csrf
                        <p class="text-sm text-brand-200">{{ __('Enter the code sent to your WhatsApp to finish changing your password.') }}</p>
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <input type="text" name="code" required inputmode="numeric" pattern="[0-9]*" maxlength="6" dir="ltr" class="app-input text-center font-mono tracking-[0.4em] sm:max-w-[220px]" placeholder="••••••">
                            <button type="submit" class="app-button">{{ __('Confirm password change') }}</button>
                        </div>
                        @error('password_code') <span class="block text-sm text-rose-400">{{ $message }}</span> @enderror
                    </form>
                @endif
            </section>
        </div>

        <section class="app-card mt-8 space-y-5 p-5">
            <div>
                <h2 class="text-lg font-semibold text-white">{{ __('Two-factor authentication') }}</h2>
                <p class="mt-1 text-sm text-slate-400">{{ __('Add an extra verification step to protect password changes.') }}</p>
            </div>
            @if ($errors->has('two_factor') || $errors->has('two_factor_code'))
                <p class="text-sm text-rose-400">{{ $errors->first('two_factor') ?: $errors->first('two_factor_code') }}</p>
            @endif
            <div class="grid gap-4 lg:grid-cols-2">
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="font-semibold text-white">{{ __('WhatsApp OTP') }}</p>
                            <p class="mt-1 text-xs text-slate-400">{{ $customer->whatsapp ?: $customer->phone ?: __('No WhatsApp number configured.') }}</p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $customer->whatsapp_two_factor_enabled ? 'bg-emerald-500/15 text-emerald-300' : 'bg-white/10 text-slate-400' }}">{{ $customer->whatsapp_two_factor_enabled ? __('Enabled') : __('Disabled') }}</span>
                    </div>
                    @if ($customer->whatsapp_two_factor_enabled)
                        <form method="POST" action="{{ route('customer.2fa.whatsapp.disable') }}" class="mt-4 flex flex-col gap-3 sm:flex-row">
                            @csrf
                            <input type="password" name="current_password" required autocomplete="current-password" class="app-input" placeholder="{{ __('Current password') }}">
                            <button type="submit" class="app-button--ghost shrink-0 px-4 py-2 text-sm">{{ __('Disable') }}</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('customer.2fa.whatsapp.request') }}" class="mt-4">
                            @csrf
                            <button type="submit" class="app-button px-4 py-2 text-sm">{{ __('Send WhatsApp code') }}</button>
                        </form>
                        @if (session('customer.whatsapp_2fa_pending'))
                            <form method="POST" action="{{ route('customer.2fa.whatsapp.enable') }}" class="mt-3 flex flex-col gap-3 sm:flex-row">
                                @csrf
                                <input type="text" name="code" required inputmode="numeric" pattern="[0-9]*" maxlength="6" dir="ltr" class="app-input text-center font-mono tracking-[0.4em]" placeholder="••••••">
                                <button type="submit" class="app-button shrink-0 px-4 py-2 text-sm">{{ __('Enable WhatsApp OTP') }}</button>
                            </form>
                            @error('two_factor_code') <span class="mt-2 block text-sm text-rose-400">{{ $message }}</span> @enderror
                        @endif
                    @endif
                </div>

                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="font-semibold text-white">{{ __('Google Authenticator') }}</p>
                            <p class="mt-1 text-xs text-slate-400">{{ __('Use a time-based code from an authenticator app.') }}</p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $customer->authenticator_two_factor_enabled ? 'bg-emerald-500/15 text-emerald-300' : 'bg-white/10 text-slate-400' }}">{{ $customer->authenticator_two_factor_enabled ? __('Enabled') : __('Disabled') }}</span>
                    </div>
                    @if ($customer->authenticator_two_factor_enabled)
                        <form method="POST" action="{{ route('customer.2fa.authenticator.disable') }}" class="mt-4 space-y-3">
                            @csrf
                            <input type="text" name="code" required maxlength="255" dir="ltr" class="app-input" placeholder="{{ __('Authenticator or recovery code') }}">
                            <button type="submit" class="app-button--ghost px-4 py-2 text-sm">{{ __('Disable Authenticator') }}</button>
                            @error('authenticator_code') <span class="block text-sm text-rose-400">{{ $message }}</span> @enderror
                        </form>
                    @else
                        <a href="{{ route('customer.2fa.authenticator') }}" class="app-button mt-4 inline-flex px-4 py-2 text-sm">{{ __('Set up Authenticator') }}</a>
                    @endif
                </div>
            </div>
        </section>

        @if (session('customer.recovery_codes'))
            <section class="app-card mt-6 border-amber-500/20 bg-amber-500/5 p-5">
                <h2 class="text-lg font-semibold text-amber-200">{{ __('Save your recovery codes') }}</h2>
                <p class="mt-2 text-sm text-amber-200/80">{{ __('These codes are shown once. Store them somewhere safe in case you lose access to your authenticator.') }}</p>
                <div class="mt-4 grid grid-cols-2 gap-2 sm:grid-cols-5">
                    @foreach (session('customer.recovery_codes') as $recoveryCode)
                        <code class="rounded-lg border border-amber-500/20 bg-slate-950 px-2 py-2 text-center font-mono text-xs text-amber-200">{{ $recoveryCode }}</code>
                    @endforeach
                </div>
            </section>
        @endif

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
                            @if ($reservation->unit?->project?->name)
                                · {{ $reservation->unit->project->name }}
                            @endif
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
                            @if ($offer->unit?->unit_number)
                                · {{ __('Unit') }} {{ $offer->unit->unit_number }}
                            @endif
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
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="flex items-center gap-2 text-lg font-semibold text-white">
                    <svg class="h-5 w-5 text-violet-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2-2h12a2 2 0 0 0 2-2V8z" stroke-width="1.8"/><path d="M14 2v6h6" stroke-width="1.8"/><path d="M16 13H8M16 17H8M10 9H8" stroke-width="1.8" stroke-linecap="round"/></svg>
                    {{ __('Payment plans') }}
                </h2>
                <a href="{{ route('installments.index') }}" class="app-button--ghost px-4 py-2 text-sm">{{ __('Open calculator') }}</a>
            </div>
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

        <section class="app-card mt-6 space-y-4 p-5">
            <h2 class="flex items-center gap-2 text-lg font-semibold text-white">
                <svg class="h-5 w-5 text-sky-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M4 4h16v16H4z" stroke-width="1.8" stroke-linejoin="round"/><path d="M8 8h8M8 12h8M8 16h5" stroke-width="1.8" stroke-linecap="round"/></svg>
                {{ __('Documents') }}
            </h2>
            @forelse ($customer->documents as $document)
                <div class="flex flex-col gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <p class="truncate font-semibold text-white">{{ $document->name }}</p>
                        <p class="mt-1 text-xs text-slate-500">
                            {{ $document->mime_type ?: __('Document') }}
                            @if ($document->size)
                                · {{ number_format($document->size / 1024, 1) }} KB
                            @endif
                        </p>
                    </div>
                    <a href="{{ route('customer.documents.download', $document) }}" class="app-button--ghost shrink-0 px-4 py-2 text-sm">{{ __('Download') }}</a>
                </div>
            @empty
                <p class="text-sm text-slate-400">{{ __('No documents available.') }}</p>
            @endforelse
        </section>
    </div>
@endsection
