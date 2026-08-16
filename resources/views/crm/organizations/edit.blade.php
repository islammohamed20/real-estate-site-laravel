@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    @include('crm.partials.crm-nav')

    <section class="dashboard-hero-card p-6 sm:p-8">
        <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-brand-500/20 blur-3xl"></div>
        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-3">
                <span class="badge badge-brand">{{ __('Edit') }} {{ __('Organization') }}</span>
                <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ $organization->name }}</h1>
                <p class="text-sm text-slate-400">{{ __('Update the company profile and its contact information.') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('dashboard.crm.organizations.show', $organization) }}" class="app-button--ghost text-sm">{{ __('Cancel') }}</a>
            </div>
        </div>
    </section>

    <form method="POST" action="{{ route('dashboard.crm.organizations.update', $organization) }}" class="app-card app-card--gradient space-y-5">
        @csrf
        @method('PUT')

        <div class="flex items-center gap-3 border-b border-white/5 pb-4">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-500/15 text-brand-400">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 21h18M3 7v1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7M4 21V4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v17" stroke-width="1.8"/></svg>
            </span>
            <div>
                <h2 class="text-lg font-semibold text-white">{{ __('Organization details') }}</h2>
                <p class="text-sm text-slate-400">{{ __('Company profile information') }}</p>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label for="name" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Organization name') }} <span class="text-rose-400">*</span></label>
                <input id="name" name="name" class="app-input" value="{{ old('name', $organization->name) }}" required>
                @error('name')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="industry" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Industry') }}</label>
                <input id="industry" name="industry" class="app-input" value="{{ old('industry', $organization->industry) }}">
                @error('industry')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="phone" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Phone number') }}</label>
                <input id="phone" name="phone" type="tel" inputmode="tel" class="app-input" value="{{ old('phone', $organization->phone) }}">
                @error('phone')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="email" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Email address') }}</label>
                <input id="email" name="email" type="email" inputmode="email" class="app-input" value="{{ old('email', $organization->email) }}">
                @error('email')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="website" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Website') }}</label>
                <input id="website" name="website" type="url" class="app-input" placeholder="https://..." value="{{ old('website', $organization->website) }}">
                @error('website')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="tax_id" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Tax ID') }}</label>
                <input id="tax_id" name="tax_id" class="app-input" value="{{ old('tax_id', $organization->tax_id) }}">
                @error('tax_id')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="address" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Address') }}</label>
                <input id="address" name="address" class="app-input" value="{{ old('address', $organization->address) }}">
                @error('address')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="city" class="mb-1 block text-sm font-medium text-slate-300">{{ __('City') }}</label>
                <input id="city" name="city" class="app-input" value="{{ old('city', $organization->city) }}">
                @error('city')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="country" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Country') }}</label>
                <input id="country" name="country" class="app-input" value="{{ old('country', $organization->country) }}">
                @error('country')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <label for="notes" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Notes') }}</label>
                <textarea id="notes" name="notes" rows="4" class="app-input min-h-24">{{ old('notes', $organization->notes) }}</textarea>
                @error('notes')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex flex-wrap justify-end gap-2 border-t border-white/5 pt-4">
            <a href="{{ route('dashboard.crm.organizations.show', $organization) }}" class="app-button--ghost">{{ __('Cancel') }}</a>
            <button type="submit" class="app-button">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" stroke-width="1.8"/><path d="M17 21v-8H7v8M7 3v5h8" stroke-width="1.8" stroke-linecap="round"/></svg>
                {{ __('Save Changes') }}
            </button>
        </div>
    </form>
</div>
@endsection
