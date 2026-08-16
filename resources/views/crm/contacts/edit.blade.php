@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    @include('crm.partials.crm-nav')

    <section class="dashboard-hero-card p-6 sm:p-8">
        <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-brand-500/20 blur-3xl"></div>
        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-3">
                <span class="badge badge-brand">{{ __('Edit') }} {{ __('Contact') }}</span>
                <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ $contact->full_name ?? $contact->first_name }}</h1>
                <p class="text-sm text-slate-400">{{ __('Update the contact details.') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('dashboard.crm.organizations.show', $contact->organization_id) }}" class="app-button--ghost text-sm">{{ __('Cancel') }}</a>
            </div>
        </div>
    </section>

    <form method="POST" action="{{ route('dashboard.crm.contacts.update', $contact) }}" class="app-card app-card--gradient space-y-5">
        @csrf
        @method('PUT')

        <div class="flex items-center gap-3 border-b border-white/5 pb-4">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-500/15 text-brand-400">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34" stroke-width="1.8" stroke-linecap="round"/><path d="M18 2l4 4-10 10-4 1 1-4z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <div>
                <h2 class="text-lg font-semibold text-white">{{ __('Contact details') }}</h2>
                <p class="text-sm text-slate-400">{{ __('Person information') }}</p>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label for="organization_id" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Organization') }} <span class="text-rose-400">*</span></label>
                <select id="organization_id" name="organization_id" class="app-input" required>
                    @foreach ($organizations as $id => $name)
                        <option value="{{ $id }}" @selected(old('organization_id', $contact->organization_id) == $id)>{{ $name }}</option>
                    @endforeach
                </select>
                @error('organization_id')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="first_name" class="mb-1 block text-sm font-medium text-slate-300">{{ __('First name') }} <span class="text-rose-400">*</span></label>
                <input id="first_name" name="first_name" class="app-input" value="{{ old('first_name', $contact->first_name) }}" required>
                @error('first_name')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="last_name" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Last name') }}</label>
                <input id="last_name" name="last_name" class="app-input" value="{{ old('last_name', $contact->last_name) }}">
                @error('last_name')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="job_title" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Job title') }}</label>
                <input id="job_title" name="job_title" class="app-input" value="{{ old('job_title', $contact->job_title) }}">
                @error('job_title')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="email" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Email address') }}</label>
                <input id="email" name="email" type="email" inputmode="email" class="app-input" value="{{ old('email', $contact->email) }}">
                @error('email')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="phone" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Phone number') }}</label>
                <input id="phone" name="phone" type="tel" inputmode="tel" class="app-input" value="{{ old('phone', $contact->phone) }}">
                @error('phone')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="mobile" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Mobile') }}</label>
                <input id="mobile" name="mobile" type="tel" inputmode="tel" class="app-input" value="{{ old('mobile', $contact->mobile) }}">
                @error('mobile')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="source" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Source') }}</label>
                <input id="source" name="source" class="app-input" list="contact-source-list" value="{{ old('source', $contact->source) }}">
                <datalist id="contact-source-list">
                    <option value="website">
                    <option value="referral">
                    <option value="walk-in">
                    <option value="phone call">
                    <option value="social media">
                    <option value="event">
                </datalist>
                @error('source')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-300 sm:col-span-2">
                <input type="checkbox" name="is_primary" value="1" class="h-4 w-4 rounded border-slate-600 bg-slate-700 text-brand-600" @checked(old('is_primary', $contact->is_primary))>
                {{ __('Primary contact for this organization') }}
            </label>
            <div class="sm:col-span-2">
                <label for="notes" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Notes') }}</label>
                <textarea id="notes" name="notes" rows="4" class="app-input min-h-24">{{ old('notes', $contact->notes) }}</textarea>
                @error('notes')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex flex-wrap justify-end gap-2 border-t border-white/5 pt-4">
            <a href="{{ route('dashboard.crm.organizations.show', $contact->organization_id) }}" class="app-button--ghost">{{ __('Cancel') }}</a>
            <button type="submit" class="app-button">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" stroke-width="1.8"/><path d="M17 21v-8H7v8M7 3v5h8" stroke-width="1.8" stroke-linecap="round"/></svg>
                {{ __('Save Changes') }}
            </button>
        </div>
    </form>
</div>
@endsection
