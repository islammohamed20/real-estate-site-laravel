@extends('layouts.dashboard')

@php($isEdit = $customer->exists)

@section('content')
<div class="space-y-6">
    @include('crm.partials.crm-nav')

    <section class="dashboard-hero-card p-6 sm:p-8">
        <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-brand-500/20 blur-3xl"></div>
        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-3">
                <span class="badge badge-brand">{{ $isEdit ? __('Edit Customer') : __('New Customer') }}</span>
                <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ $isEdit ? $customer->name : __('New Customer') }}</h1>
                <p class="text-sm text-slate-400">{{ $isEdit ? __('Update the customer profile.') : __('Create a new customer profile.') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ $isEdit ? route('dashboard.crm.customers.show', $customer) : route('dashboard.crm.customers.index') }}" class="app-button--ghost text-sm">{{ __('Cancel') }}</a>
            </div>
        </div>
    </section>

    <form method="POST" action="{{ $isEdit ? route('dashboard.crm.customers.update', $customer) : route('dashboard.crm.customers.store') }}" class="app-card app-card--gradient space-y-5">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <div class="flex items-center gap-3 border-b border-white/5 pb-4">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-500/15 text-brand-400">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34" stroke-width="1.8" stroke-linecap="round"/><path d="M18 2l4 4-10 10-4 1 1-4z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <div>
                <h2 class="text-lg font-semibold text-white">{{ __('Customer details') }}</h2>
                <p class="text-sm text-slate-400">{{ __('Basic contact information') }}</p>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label for="name" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Customer name') }} <span class="text-rose-400">*</span></label>
                <input id="name" name="name" class="app-input" value="{{ old('name', $customer->name) }}" required>
                @error('name')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="phone" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Phone number') }} <span class="text-rose-400">*</span></label>
                <input id="phone" name="phone" type="tel" inputmode="tel" class="app-input" value="{{ old('phone', $customer->phone) }}" required>
                @error('phone')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="whatsapp" class="mb-1 block text-sm font-medium text-slate-300">{{ __('WhatsApp') }}</label>
                <input id="whatsapp" name="whatsapp" type="tel" inputmode="tel" class="app-input" value="{{ old('whatsapp', $customer->whatsapp) }}">
                @error('whatsapp')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="email" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Email address') }}</label>
                <input id="email" name="email" type="email" inputmode="email" class="app-input" value="{{ old('email', $customer->email) }}">
                @error('email')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="occupation" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Occupation') }}</label>
                <input id="occupation" name="occupation" class="app-input" value="{{ old('occupation', $customer->occupation) }}">
                @error('occupation')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="address" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Address') }}</label>
                <input id="address" name="address" class="app-input" value="{{ old('address', $customer->address) }}">
                @error('address')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="source" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Source') }}</label>
                <select id="source" name="source" class="app-input">
                    <option value="">{{ __('Select source') }}</option>
                    @foreach ($sources as $sourceName)
                        <option value="{{ $sourceName }}" @selected(old('source', $customer->source) === $sourceName)>{{ $sourceName }}</option>
                    @endforeach
                </select>
                @error('source')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="budget" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Budget') }}</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-slate-500">EGP</span>
                    <input id="budget" name="budget" type="number" step="0.01" min="0" class="app-input pl-12" value="{{ old('budget', $customer->budget) }}">
                </div>
                @error('budget')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="budget_min" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Budget min') }}</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-slate-500">EGP</span>
                    <input id="budget_min" name="budget_min" type="number" step="0.01" min="0" class="app-input pl-12" value="{{ old('budget_min', $customer->budget_min) }}">
                </div>
                @error('budget_min')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="budget_max" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Budget max') }}</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-slate-500">EGP</span>
                    <input id="budget_max" name="budget_max" type="number" step="0.01" min="0" class="app-input pl-12" value="{{ old('budget_max', $customer->budget_max) }}">
                </div>
                @error('budget_max')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="notes" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Notes') }}</label>
                <textarea id="notes" name="notes" rows="4" class="app-input min-h-24">{{ old('notes', $customer->notes) }}</textarea>
                @error('notes')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex items-center gap-3 border-b border-white/5 pb-4">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-500/15 text-brand-400">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h.01M9 12h.01M9 15h.01M15 9h.01M15 12h.01M15 15h.01" stroke-width="1.8" stroke-linecap="round"/></svg>
            </span>
            <div>
                <h2 class="text-lg font-semibold text-white">{{ __('Interested projects') }}</h2>
                <p class="text-sm text-slate-400">{{ __('Select the projects this customer is interested in.') }}</p>
            </div>
        </div>

        @if ($projects->isNotEmpty())
            <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($projects as $project)
                    <label @class(['flex cursor-pointer items-start gap-3 rounded-2xl border p-3 transition hover:border-brand-500/40 hover:bg-brand-500/5', 'border-brand-500/50 bg-brand-500/10' => $customer->interestedProjects->contains('id', $project->id), 'border-white/10' => ! $customer->interestedProjects->contains('id', $project->id)])>
                        <input type="checkbox" name="interested_project_ids[]" value="{{ $project->id }}" @checked($customer->interestedProjects->contains('id', $project->id)) class="mt-0.5 h-4 w-4 rounded border-white/20 bg-slate-900 text-brand-500 focus:ring-brand-500">
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-semibold text-white">{{ $project->name }}</span>
                            <span class="block truncate text-xs text-slate-400">{{ $project->location ?? __('No location') }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
        @else
            <p class="text-sm text-slate-400">{{ __('No projects available yet.') }}</p>
        @endif

        <div class="flex flex-wrap justify-end gap-2 border-t border-white/5 pt-4">
            <a href="{{ $isEdit ? route('dashboard.crm.customers.show', $customer) : route('dashboard.crm.customers.index') }}" class="app-button--ghost">{{ __('Cancel') }}</a>
            <button type="submit" class="app-button">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" stroke-width="1.8"/><path d="M17 21v-8H7v8M7 3v5h8" stroke-width="1.8" stroke-linecap="round"/></svg>
                {{ $isEdit ? __('Save Changes') : __('Create Customer') }}
            </button>
        </div>
    </form>
</div>
@endsection
