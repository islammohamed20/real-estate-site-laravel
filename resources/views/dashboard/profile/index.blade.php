@extends('layouts.dashboard')

@section('content')
    <div class="space-y-6">
        @if ($errors->any())
            <div class="flex items-start gap-3 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-300">
                <svg class="mt-0.5 h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke-width="1.8"/><path d="M12 9v4M12 17h.01" stroke-width="1.8" stroke-linecap="round"/></svg>
                <div>
                    <p class="font-semibold">{{ __('Please fix the errors below.') }}</p>
                    <ul class="mt-1 list-inside list-disc text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <section class="dashboard-hero-card p-6 sm:p-8">
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-brand-500/20 blur-3xl"></div>

            <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="mobile-section-title">{{ __('Account') }}</p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('My Profile') }}</h1>
                    <p class="mt-2 text-sm text-slate-400">
                        {{ __('Update your personal information and password.') }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="submit" form="profile-form" class="app-button">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round"/></svg>
                        {{ __('Save Changes') }}
                    </button>
                </div>
            </div>
        </section>

        <form id="profile-form" method="POST" action="{{ route('dashboard.profile.update') }}" class="grid gap-6 lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)]">
            @csrf
            @method('PUT')

            {{-- Personal Information --}}
            <section class="app-card app-card--gradient space-y-5">
                <div class="flex items-center gap-3 border-b border-white/5 pb-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-500/15 text-brand-400">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="9" cy="7" r="4" stroke-width="1.8"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-width="1.8"/></svg>
                    </span>
                    <div>
                        <h2 class="text-lg font-semibold text-white">{{ __('Personal Information') }}</h2>
                        <p class="text-xs text-slate-400">{{ __('Your contact and job details.') }}</p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="name" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Full Name') }} <span class="text-rose-400">*</span></label>
                        <input type="text" id="name" name="name" class="app-input" value="{{ old('name', $user->name) }}" required autocomplete="name">
                        @error('name') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="email" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Email') }} <span class="text-rose-400">*</span></label>
                        <input type="email" id="email" name="email" class="app-input" value="{{ old('email', $user->email) }}" required autocomplete="email">
                        @error('email') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="phone" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Phone') }}</label>
                        <input type="tel" id="phone" name="phone" class="app-input" value="{{ old('phone', $user->phone) }}" autocomplete="tel">
                        @error('phone') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="job_title" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Job Title') }}</label>
                        <input type="text" id="job_title" name="job_title" class="app-input" value="{{ old('job_title', $user->job_title) }}">
                        @error('job_title') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="department" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Department') }}</label>
                        <input type="text" id="department" name="department" class="app-input" value="{{ old('department', $user->department) }}">
                        @error('department') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            {{-- Security --}}
            <section class="app-card app-card--gradient space-y-5">
                <div class="flex items-center gap-3 border-b border-white/5 pb-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-400">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2" stroke-width="1.8"/><path d="M7 11V7a5 5 0 0 1 10 0v4" stroke-width="1.8"/></svg>
                    </span>
                    <div>
                        <h2 class="text-lg font-semibold text-white">{{ __('Security') }}</h2>
                        <p class="text-xs text-slate-400">{{ __('Change your password if needed.') }}</p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="avatar_path" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Avatar URL') }}</label>
                        <input type="url" id="avatar_path" name="avatar_path" class="app-input" value="{{ old('avatar_path', $user->avatar_path) }}" placeholder="https://...">
                        @error('avatar_path') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium text-slate-300">{{ __('New Password') }}</label>
                        <input type="password" id="password" name="password" class="app-input" minlength="8" autocomplete="new-password" placeholder="{{ __('Leave blank to keep current password') }}">
                        @error('password') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Confirm Password') }}</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="app-input" autocomplete="new-password" placeholder="{{ __('Leave blank to keep current password') }}">
                        @error('password_confirmation') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="rounded-xl border border-white/5 bg-white/[0.03] p-4 text-sm text-slate-400">
                    <p>{{ __('Two-factor authentication settings can be managed from the Security page.') }}</p>
                    <a href="{{ route('dashboard.security') }}" class="mt-2 inline-flex items-center gap-1 font-medium text-brand-400 hover:text-brand-300 transition">
                        {{ __('Open Security & 2FA') }} →
                    </a>
                </div>
            </section>
        </form>
    </div>
@endsection
