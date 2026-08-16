@extends('layouts.dashboard')

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <section class="dashboard-hero-card p-4 sm:p-6">
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-brand-500/20 blur-3xl"></div>
            <div class="relative">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="badge badge-brand">{{ __('Security & Access') }}</span>
                    @if ($user?->two_factor_enabled)
                        <span class="badge badge-success">{{ __('2FA Enabled') }}</span>
                    @else
                        <span class="badge badge-warning">{{ __('2FA Disabled') }}</span>
                    @endif
                </div>
                <h1 class="mt-3 text-2xl font-bold tracking-tight text-white sm:text-3xl">{{ __('Account Security') }}</h1>
                <p class="mt-3 max-w-xl text-sm leading-6 text-slate-300">{{ __('Manage your login activity and protect your account with Google Authenticator (2FA).') }}</p>
            </div>
        </section>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-500/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-300">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Recent activity --}}
        <section class="app-card app-card--gradient space-y-5">
            <div>
                <h2 class="text-lg font-semibold text-white">{{ __('Recent Login Activity') }}</h2>
                <p class="text-sm text-slate-400">{{ __('Your most recent sign-ins to the control panel.') }}</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">{{ __('Last Login') }}</p>
                    <p class="mt-2 text-sm font-semibold text-white">{{ $user?->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : __('Never') }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">{{ __('Last IP') }}</p>
                    <p class="mt-2 text-sm font-semibold text-white ltr">{{ $user?->last_login_ip ?? '-' }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">{{ __('Two-Factor Auth') }}</p>
                    <p class="mt-2 text-sm font-semibold {{ $user?->two_factor_enabled ? 'text-emerald-400' : 'text-slate-300' }}">
                        {{ $user?->two_factor_enabled ? __('Enabled') : __('Disabled') }}
                    </p>
                </div>
            </div>

            @if ($histories->isNotEmpty())
                <div class="overflow-hidden rounded-2xl border border-white/10">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-950/60 text-left text-xs uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-4 py-2.5 font-semibold">{{ __('Date') }}</th>
                                <th class="px-4 py-2.5 font-semibold">{{ __('IP') }}</th>
                                <th class="px-4 py-2.5 font-semibold">{{ __('Device') }}</th>
                                <th class="px-4 py-2.5 font-semibold">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach ($histories as $history)
                                <tr>
                                    <td class="px-4 py-2.5 text-slate-300">{{ $history->logged_in_at?->format('Y-m-d H:i') }}</td>
                                    <td class="px-4 py-2.5 font-mono text-xs text-slate-400 ltr">{{ $history->ip_address ?? '-' }}</td>
                                    <td class="px-4 py-2.5 text-slate-300">{{ $history->user_agent ? (Str::contains($history->user_agent, 'Mobile') ? __('Mobile') : __('Desktop')) : '-' }}</td>
                                    <td class="px-4 py-2.5">
                                        @if ($history->successful ?? true)
                                            <span class="badge badge-success">{{ __('Success') }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ __('Failed') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-slate-500">{{ __('No login history yet.') }}</p>
            @endif
        </section>

        {{-- Two-Factor Authentication management --}}
        <section class="app-card app-card--gradient space-y-5">
            <div>
                <h2 class="text-lg font-semibold text-white">{{ __('Google Authenticator (2FA)') }}</h2>
                <p class="text-sm text-slate-400">{{ __('Add an extra security layer: after your password you must enter a time-based code from your phone.') }}</p>
            </div>

            @if (session('2fa:recovery_codes'))
                <div class="rounded-2xl border border-emerald-500/30 bg-emerald-500/10 p-4">
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-emerald-300">{{ __('Save these recovery codes') }}</p>
                            <p class="mt-1 text-xs text-emerald-200/70">{{ __('Each code can be used only once. Store them somewhere safe — they are the only way in if you lose your phone.') }}</p>
                            <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
                                @foreach (session('2fa:recovery_codes') as $recoveryCode)
                                    <code class="select-all rounded-lg border border-emerald-500/20 bg-slate-950/60 px-3 py-2 text-center font-mono text-sm tracking-widest text-emerald-200" dir="ltr">{{ $recoveryCode }}</code>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-brand-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z"/><path d="m9 12 2 2 4-4"/></svg>
                        <div>
                            <p class="text-sm font-semibold text-white">{{ $user?->two_factor_enabled ? __('Two-Factor Authentication is active') : __('Two-Factor Authentication is off') }}</p>
                            <p class="mt-1 text-xs text-slate-400">
                                @if ($user?->two_factor_enabled)
                                    {{ __('Your account requires a code from the Google Authenticator app after your password.') }}
                                @else
                                    {{ __('Scan the QR code with Google Authenticator (or any TOTP app), then enter the 6-digit code to confirm.') }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        @if ($user?->two_factor_enabled)
                            <form method="POST" action="{{ route('dashboard.2fa.recovery-codes') }}" class="inline">
                                @csrf
                                <button type="submit" class="app-button--ghost" onclick="return confirm('{{ __('Generate a new set of recovery codes? The old ones will stop working.') }}')">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 4v5h5M20 20v-5h-5M4.6 9a8 8 0 0 1 14-2.4L20 8M4 16l1.4 1.4A8 8 0 0 0 19.4 15"/></svg>
                                    {{ __('Regenerate recovery codes') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('dashboard.2fa.disable') }}" class="inline">
                                @csrf
                                <div x-data="{ open: false }">
                                    <button type="button" @click="open = true" class="app-button app-button--danger">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                                        {{ __('Disable 2FA') }}
                                    </button>
                                    <div x-show="open" x-cloak class="fixed inset-0 z-[80] flex items-center justify-center bg-black/70 p-4" @keydown.escape.window="open = false">
                                        <div class="w-full max-w-sm rounded-2xl border border-white/10 bg-slate-900 p-6 shadow-2xl">
                                            <h3 class="text-lg font-bold text-white">{{ __('Disable Two-Factor Authentication') }}</h3>
                                            <p class="mt-2 text-sm text-slate-400">{{ __('Enter your current authenticator code or a recovery code to confirm disabling 2FA.') }}</p>
                                            <input type="text" name="code" required inputmode="numeric" autocomplete="one-time-code" dir="ltr" class="app-input mt-4 text-center font-mono tracking-[0.4em]" placeholder="••••••" maxlength="32">
                                            @error('2fa_disable')
                                                <span class="mt-2 block text-sm text-rose-400">{{ $message }}</span>
                                            @enderror
                                            <div class="mt-5 flex justify-end gap-2">
                                                <button type="button" @click="open = false" class="app-button--ghost">{{ __('Cancel') }}</button>
                                                <button type="submit" class="app-button app-button--danger">{{ __('Disable 2FA') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @else
                            <a href="{{ route('dashboard.2fa.enable') }}" class="app-button">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z"/><path d="m9 12 2 2 4-4"/></svg>
                                {{ __('Enable 2FA') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
