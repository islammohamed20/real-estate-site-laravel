@extends('layouts.dashboard')

@section('content')
<div class="space-y-6" x-data="{
    quickTab: '{{ old('quick_tab', 'lead') }}',
    showLeadDetails: {{ $errors->hasAny(['email', 'occupation', 'budget', 'address', 'assigned_to', 'follow_up_at', 'project_id', 'unit_id', 'source', 'message']) ? 'true' : 'false' }},
    showOrgDetails: {{ $errors->hasAny(['industry', 'website', 'city', 'country', 'tax_id']) ? 'true' : 'false' }},
    showContactDetails: {{ $errors->hasAny(['last_name', 'mobile', 'job_title']) ? 'true' : 'false' }}
}">
    @include('crm.partials.crm-nav')

    <section class="dashboard-hero-card p-6 sm:p-8">
        <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-brand-500/20 blur-3xl"></div>
        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="badge badge-brand">{{ __('Quick CRM') }}</span>
                    <span class="badge badge-success">{{ __('Fast entry') }}</span>
                </div>
                <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('Quick Data Entry') }}</h1>
                <p class="text-sm text-slate-400">{{ __('Create a lead, organization or contact quickly.') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('dashboard.crm.index') }}" class="app-button--ghost text-sm">{{ __('CRM Home') }}</a>
            </div>
        </div>
    </section>

    <section class="app-card p-5 sm:p-8">
        <div class="flex gap-2 overflow-x-auto no-scrollbar">
            @foreach (['lead' => __('Lead'), 'organization' => __('Organization'), 'contact' => __('Contact')] as $key => $label)
                <button type="button" @click="quickTab = '{{ $key }}'" class="whitespace-nowrap rounded-2xl px-4 py-2 text-sm font-semibold transition" :class="quickTab === '{{ $key }}' ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/20' : 'text-slate-400 hover:bg-white/5 hover:text-white'">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- ── Lead Form ── --}}
        <div x-show="quickTab === 'lead'" x-cloak class="mt-6">
            <form method="POST" action="{{ route('dashboard.crm.leads.store') }}" class="grid gap-3 sm:grid-cols-2" x-data="{ leadProject: '{{ old('project_id', '') }}' }">
                @csrf
                <input type="hidden" name="quick_tab" value="lead">

                <div class="sm:col-span-2">
                    <label for="lead-name" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Customer name') }} <span class="text-rose-400">*</span></label>
                    <input id="lead-name" class="app-input w-full" name="name" autocomplete="name" placeholder="{{ __('e.g. Ahmed Mohamed') }}" value="{{ old('name') }}" required>
                    @error('name')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="lead-phone" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Phone number') }} <span class="text-rose-400">*</span></label>
                    <input id="lead-phone" class="app-input w-full" type="tel" inputmode="tel" name="phone" autocomplete="tel" pattern="[0-9+\s\-()]{7,20}" placeholder="{{ __('e.g. 0100 123 4567') }}" value="{{ old('phone') }}" required>
                    <div id="lead-dup-warning"></div>
                    @error('phone')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>

                <button type="button" @click="showLeadDetails = !showLeadDetails" class="flex items-center gap-1.5 text-left text-sm font-medium text-brand-400 sm:col-span-2">
                    <svg class="h-4 w-4 transition-transform" :class="showLeadDetails ? 'rotate-90' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 5l7 7-7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span x-text="showLeadDetails ? @js(__('Hide optional fields')) : @js(__('More details'))"></span>
                </button>

                <div x-show="showLeadDetails" x-collapse x-cloak class="grid gap-3 sm:grid-cols-2 sm:col-span-2">
                    <div>
                        <label for="lead-email" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Email address') }}</label>
                        <input id="lead-email" class="app-input w-full" type="email" inputmode="email" name="email" autocomplete="email" placeholder="{{ __('e.g. ahmed@example.com') }}" value="{{ old('email') }}">
                        @error('email')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="lead-whatsapp" class="mb-1 block text-sm font-medium text-slate-300">{{ __('WhatsApp') }}</label>
                        <input id="lead-whatsapp" class="app-input w-full" type="tel" inputmode="tel" name="whatsapp" placeholder="{{ __('e.g. 0100 123 4567') }}" value="{{ old('whatsapp') }}">
                        @error('whatsapp')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="lead-occupation" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Occupation') }}</label>
                        <input id="lead-occupation" class="app-input w-full" name="occupation" placeholder="{{ __('e.g. Engineer') }}" value="{{ old('occupation') }}">
                        @error('occupation')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="lead-budget" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Budget') }}</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-slate-500">EGP</span>
                            <input id="lead-budget" class="app-input w-full pl-12" type="number" step="0.01" min="0" name="budget" placeholder="{{ __('0.00') }}" value="{{ old('budget') }}">
                        </div>
                        @error('budget')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="lead-address" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Address') }}</label>
                        <input id="lead-address" class="app-input w-full" name="address" autocomplete="street-address" placeholder="{{ __('e.g. New Cairo, 5th Settlement') }}" value="{{ old('address') }}">
                        @error('address')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="lead-stage" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Stage') }}</label>
                        <select id="lead-stage" name="stage" class="app-input w-full">
                            @foreach ($stages as $value => $label)
                                <option value="{{ $value }}" @selected(old('stage', 'new') === $value)>{{ __($label) }}</option>
                            @endforeach
                        </select>
                        @error('stage')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="lead-priority" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Priority') }}</label>
                        <select id="lead-priority" name="priority" class="app-input w-full">
                            @foreach ($priorities as $value => $label)
                                <option value="{{ $value }}" @selected(old('priority', 'normal') === $value)>{{ __($label) }}</option>
                            @endforeach
                        </select>
                        @error('priority')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="lead-assigned-to" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Assign to sales') }}</label>
                        <select id="lead-assigned-to" name="assigned_to" class="app-input w-full">
                            <option value="">{{ __('Unassigned') }}</option>
                            @foreach ($users as $id => $name)
                                <option value="{{ $id }}" @selected(old('assigned_to') == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('assigned_to')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="lead-follow-up" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Follow up at') }}</label>
                        <input id="lead-follow-up" class="app-input w-full" type="datetime-local" name="follow_up_at" min="{{ now()->format('Y-m-d\TH:i') }}" value="{{ old('follow_up_at') }}">
                        @error('follow_up_at')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="lead-campaign" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Campaign') }}</label>
                        <input id="lead-campaign" class="app-input w-full" name="campaign" placeholder="{{ __('e.g. Facebook ad, Referral') }}" value="{{ old('campaign') }}">
                        @error('campaign')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="lead-project" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Interested project') }}</label>
                        <select id="lead-project" name="project_id" class="app-input w-full" x-model="leadProject">
                            <option value="">{{ __('Select project') }}</option>
                            @foreach ($projects as $id => $name)
                                <option value="{{ $id }}" @selected(old('project_id') == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('project_id')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="lead-unit" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Interested unit') }}</label>
                        <select id="lead-unit" name="unit_id" class="app-input w-full">
                            <option value="">{{ __('Select unit') }}</option>
                            @foreach ($units as $id => $label)
                                @php
                                    $unitModel = \App\Models\Unit::find($id);
                                    $unitProjectId = $unitModel?->project_id;
                                @endphp
                                <option value="{{ $id }}" data-project-id="{{ $unitProjectId }}" @selected(old('unit_id') == $id) :style="leadProject && leadProject != '{{ $unitProjectId }}' ? 'display:none' : ''">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('unit_id')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="lead-source-id" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Source') }}</label>
                        <select id="lead-source-id" name="lead_source_id" class="app-input w-full">
                            <option value="">{{ __('Select source') }}</option>
                            @foreach ($sources as $id => $name)
                                <option value="{{ $id }}" @selected(old('lead_source_id') == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('lead_source_id')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="lead-unit-type" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Unit type') }}</label>
                        <input id="lead-unit-type" class="app-input w-full" name="unit_type" list="lead-unit-type-list" placeholder="{{ __('e.g. Apartment, Villa') }}" value="{{ old('unit_type') }}">
                        <datalist id="lead-unit-type-list">
                            <option value="Apartment">
                            <option value="Villa">
                            <option value="Penthouse">
                            <option value="Duplex">
                            <option value="Studio">
                            <option value="Townhouse">
                            <option value="Chalet">
                            <option value="Office">
                            <option value="Shop">
                            <option value="Land">
                            <option value="Other">
                        </datalist>
                        @error('unit_type')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="lead-bedrooms" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Bedrooms') }}</label>
                        <input id="lead-bedrooms" class="app-input w-full" type="number" min="0" name="bedrooms" value="{{ old('bedrooms') }}">
                        @error('bedrooms')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="lead-required-area" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Required area') }} ({{ __('m²') }})</label>
                        <input id="lead-required-area" class="app-input w-full" type="number" step="0.01" min="0" name="required_area" value="{{ old('required_area') }}">
                        @error('required_area')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="lead-payment-plan" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Preferred payment plan') }}</label>
                        <input id="lead-payment-plan" class="app-input w-full" name="preferred_payment_plan" value="{{ old('preferred_payment_plan') }}">
                        @error('preferred_payment_plan')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="lead-tags" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Tags') }}</label>
                        <select id="lead-tags" name="tags[]" multiple class="app-input min-h-24 w-full">
                            @foreach ($tags as $id => $name)
                                <option value="{{ $id }}" @selected(in_array($id, old('tags', [])))>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('tags')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="lead-message" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Notes / request details') }}</label>
                        <textarea id="lead-message" class="app-input min-h-24 w-full" name="message" placeholder="{{ __('What is the customer looking for?') }}">{{ old('message') }}</textarea>
                        @error('message')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                </div>

                <button type="submit" class="app-button sm:col-span-2">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    {{ __('Save Lead') }}
                </button>
            </form>
        </div>

        {{-- ── Organization Form ── --}}
        <div x-show="quickTab === 'organization'" x-cloak class="mt-6">
            <form method="POST" action="{{ route('dashboard.crm.organizations.store') }}" class="grid gap-3 sm:grid-cols-2">
                @csrf
                <input type="hidden" name="quick_tab" value="organization">

                <div class="sm:col-span-2">
                    <label for="org-name" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Organization name') }} <span class="text-rose-400">*</span></label>
                    <input id="org-name" class="app-input w-full" name="name" autocomplete="organization" placeholder="{{ __('e.g. Acme Real Estate') }}" value="{{ old('name') }}" required>
                    @error('name')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>

                <button type="button" @click="showOrgDetails = !showOrgDetails" class="flex items-center gap-1.5 text-left text-sm font-medium text-brand-400 sm:col-span-2">
                    <svg class="h-4 w-4 transition-transform" :class="showOrgDetails ? 'rotate-90' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 5l7 7-7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span x-text="showOrgDetails ? @js(__('Hide optional fields')) : @js(__('More details'))"></span>
                </button>

                <div x-show="showOrgDetails" x-collapse x-cloak class="grid gap-3 sm:grid-cols-2 sm:col-span-2">
                    <div>
                        <label for="org-industry" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Industry') }}</label>
                        <input id="org-industry" class="app-input w-full" name="industry" placeholder="{{ __('e.g. Real Estate') }}" value="{{ old('industry') }}">
                        @error('industry')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="org-website" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Website') }}</label>
                        <input id="org-website" class="app-input w-full" type="url" name="website" autocomplete="url" placeholder="{{ __('e.g. https://acme.com') }}" value="{{ old('website') }}">
                        @error('website')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="org-phone" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Phone number') }}</label>
                        <input id="org-phone" class="app-input w-full" type="tel" inputmode="tel" name="phone" autocomplete="tel" placeholder="{{ __('e.g. 02 1234 5678') }}" value="{{ old('phone') }}">
                        @error('phone')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="org-email" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Email address') }}</label>
                        <input id="org-email" class="app-input w-full" type="email" inputmode="email" name="email" autocomplete="email" placeholder="{{ __('e.g. info@acme.com') }}" value="{{ old('email') }}">
                        @error('email')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="org-address" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Address') }}</label>
                        <input id="org-address" class="app-input w-full" name="address" autocomplete="street-address" placeholder="{{ __('Street, building, office number') }}" value="{{ old('address') }}">
                        @error('address')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="org-city" class="mb-1 block text-sm font-medium text-slate-300">{{ __('City') }}</label>
                        <input id="org-city" class="app-input w-full" name="city" autocomplete="address-level2" placeholder="{{ __('e.g. Cairo') }}" value="{{ old('city') }}">
                        @error('city')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="org-country" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Country') }}</label>
                        <input id="org-country" class="app-input w-full" name="country" autocomplete="country-name" placeholder="{{ __('e.g. Egypt') }}" value="{{ old('country') }}">
                        @error('country')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="org-tax-id" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Tax ID') }}</label>
                        <input id="org-tax-id" class="app-input w-full" name="tax_id" placeholder="{{ __('e.g. 100-200-300') }}" value="{{ old('tax_id') }}">
                        @error('tax_id')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="org-notes" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Notes') }}</label>
                        <textarea id="org-notes" class="app-input min-h-16 w-full" name="notes" placeholder="{{ __('Additional details about this organization') }}">{{ old('notes') }}</textarea>
                        @error('notes')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                </div>

                <button type="submit" class="app-button sm:col-span-2">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    {{ __('Create Organization') }}
                </button>
            </form>
        </div>

        {{-- ── Contact Form ── --}}
        <div x-show="quickTab === 'contact'" x-cloak class="mt-6">
            @if ($organizationOptions->isEmpty())
                <div class="flex flex-col items-center gap-3 rounded-2xl border border-dashed border-white/10 bg-white/5 px-4 py-8 text-center">
                    <svg class="h-10 w-10 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9" cy="7" r="4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <p class="text-sm text-slate-400">{{ __('No organizations yet. Create one first to add contacts.') }}</p>
                    <button type="button" @click="quickTab = 'organization'" class="app-button--ghost">{{ __('+ Create Organization') }}</button>
                </div>
            @else
            <form method="POST" action="{{ route('dashboard.crm.contacts.store') }}" class="grid gap-3 sm:grid-cols-2">
                @csrf
                <input type="hidden" name="quick_tab" value="contact">

                <div class="sm:col-span-2">
                    <label for="contact-org" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Organization') }} <span class="text-rose-400">*</span></label>
                    <select id="contact-org" name="organization_id" class="app-input w-full" required>
                        <option value="">{{ __('Select organization') }}</option>
                        @foreach ($organizationOptions as $id => $name)
                            <option value="{{ $id }}" @selected(old('organization_id') == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('organization_id')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="contact-first-name" class="mb-1 block text-sm font-medium text-slate-300">{{ __('First name') }} <span class="text-rose-400">*</span></label>
                    <input id="contact-first-name" class="app-input w-full" name="first_name" autocomplete="given-name" placeholder="{{ __('e.g. John') }}" value="{{ old('first_name') }}" required>
                    @error('first_name')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>

                <button type="button" @click="showContactDetails = !showContactDetails" class="flex items-center gap-1.5 text-left text-sm font-medium text-brand-400 sm:col-span-2">
                    <svg class="h-4 w-4 transition-transform" :class="showContactDetails ? 'rotate-90' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 5l7 7-7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span x-text="showContactDetails ? @js(__('Hide optional fields')) : @js(__('More details'))"></span>
                </button>

                <div x-show="showContactDetails" x-collapse x-cloak class="grid gap-3 sm:grid-cols-2 sm:col-span-2">
                    <div>
                        <label for="contact-last-name" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Last name') }}</label>
                        <input id="contact-last-name" class="app-input w-full" name="last_name" autocomplete="family-name" placeholder="{{ __('e.g. Doe') }}" value="{{ old('last_name') }}">
                        @error('last_name')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="contact-email" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Email address') }}</label>
                        <input id="contact-email" class="app-input w-full" type="email" inputmode="email" name="email" autocomplete="email" placeholder="{{ __('e.g. john@acme.com') }}" value="{{ old('email') }}">
                        @error('email')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="contact-phone" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Phone number') }}</label>
                        <input id="contact-phone" class="app-input w-full" type="tel" inputmode="tel" name="phone" autocomplete="tel" placeholder="{{ __('e.g. 02 1234 5678') }}" value="{{ old('phone') }}">
                        @error('phone')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="contact-mobile" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Mobile') }}</label>
                        <input id="contact-mobile" class="app-input w-full" type="tel" inputmode="tel" name="mobile" autocomplete="tel" placeholder="{{ __('e.g. 0100 123 4567') }}" value="{{ old('mobile') }}">
                        @error('mobile')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="contact-job-title" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Job title') }}</label>
                        <input id="contact-job-title" class="app-input w-full" name="job_title" autocomplete="organization-title" placeholder="{{ __('e.g. Sales Manager') }}" value="{{ old('job_title') }}">
                        @error('job_title')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="contact-source" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Source') }}</label>
                        <input id="contact-source" class="app-input w-full" name="source" list="contact-source-list" placeholder="{{ __('e.g. referral, website') }}" value="{{ old('source') }}">
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
                        <input type="checkbox" name="is_primary" value="1" class="h-4 w-4 rounded border-slate-600 bg-slate-700 text-brand-600" @checked(old('is_primary'))>
                        {{ __('Primary contact for this organization') }}
                    </label>

                    <div class="sm:col-span-2">
                        <label for="contact-notes" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Notes') }}</label>
                        <textarea id="contact-notes" class="app-input min-h-16 w-full" name="notes" placeholder="{{ __('Additional details about this contact') }}">{{ old('notes') }}</textarea>
                        @error('notes')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                </div>

                <button type="submit" class="app-button sm:col-span-2">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    {{ __('Create Contact') }}
                </button>
            </form>
            @endif
        </div>
    </section>
</div>
@endsection

@push('scripts')
    <script>
        (function () {
            const phoneInput = document.getElementById('lead-phone');
            if (!phoneInput) return;

            const box = document.getElementById('lead-dup-warning');
            if (!box) return;
            let timer = null;

            const show = (data) => {
                const leads = data.leads || [];
                const customers = data.customers || [];
                if (leads.length === 0 && customers.length === 0) {
                    box.innerHTML = '';
                    box.style.display = 'none';
                    return;
                }
                let html = '<div style="margin-top:0.6rem;border:1px solid #f59e0b;background:#fef3c7;border-radius:0.9rem;padding:0.8rem 1rem;color:#78350f;font-size:0.78rem;line-height:1.6;">';
                html += '<div style="display:flex;align-items:flex-start;gap:0.6rem;"><span style="font-size:1rem;">⚠️</span><div style="flex:1;min-width:0;">';
                html += '<p style="font-weight:700;margin-bottom:0.35rem;">{{ __('This phone number already exists in the CRM.') }}</p>';

                if (leads.length > 0) {
                    html += '<p style="opacity:0.9;">{{ __('Existing leads') }}:</p><ul style="list-style:none;margin:0 0 0.4rem;padding:0;display:flex;flex-direction:column;gap:0.25rem;">';
                    leads.forEach(l => {
                        html += '<li><a href="' + l.url + '" target="_blank" style="color:#0369a1;text-decoration:underline;">👤 ' + l.name + ' — ' + l.phone + (l.stage ? ' (' + l.stage + ')' : '') + '</a></li>';
                    });
                    html += '</ul>';
                }

                if (customers.length > 0) {
                    html += '<p style="opacity:0.9;">{{ __('Existing customers') }}:</p><ul style="list-style:none;margin:0 0 0.4rem;padding:0;display:flex;flex-direction:column;gap:0.25rem;">';
                    customers.forEach(c => {
                        html += '<li><a href="' + c.url + '" target="_blank" style="color:#047857;text-decoration:underline;">🏢 ' + c.name + ' — ' + c.phone + '</a></li>';
                    });
                    html += '</ul>';
                }

                html += '<p style="opacity:0.85;">{{ __('It is recommended to open the existing record instead of creating a duplicate.') }}</p>';
                html += '</div></div></div>';
                box.innerHTML = html;
                box.style.display = 'block';
            };

            const check = () => {
                const phone = (phoneInput.value || '').trim();
                if (phone.length < 6) {
                    box.innerHTML = '';
                    box.style.display = 'none';
                    return;
                }
                fetch('/real-statement-control/crm/leads/check-duplicate?phone=' + encodeURIComponent(phone), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(r => r.json())
                    .then(show)
                    .catch(() => {});
            };

            phoneInput.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(check, 600);
            });

            check();
        })();
    </script>
@endpush
