@extends('layouts.dashboard')

@section('content')
    @php
        $initialFeatures = $profile->available_features ?? [
            ['icon' => 'sparkles', 'title' => 'Super lux finishing', 'desc' => 'Premium finishing materials and fixtures to the highest international standards.'],
            ['icon' => 'car', 'title' => 'Private covered garage', 'desc' => 'Dedicated parking space in the basement with electric charging capability.'],
            ['icon' => 'view', 'title' => 'Main promenade view', 'desc' => 'Direct view of the promenade, green fence, and landscaped gardens.'],
            ['icon' => 'ac', 'title' => 'Central AC ready', 'desc' => 'Fully prepared for concealed and central air conditioning connections.'],
            ['icon' => 'security', 'title' => '24/7 security', 'desc' => 'Complete security system with surveillance cameras and electronic gates.'],
            ['icon' => 'elevator', 'title' => 'Luxury electronic elevators', 'desc' => 'Italian Schindler high-speed elevators with wide capacity.'],
        ];
        $tabs = [
            'company' => ['label' => __('Company Info'), 'icon' => 'building'],
            'contact' => ['label' => __('Contact Details'), 'icon' => 'phone'],
            'financial' => ['label' => __('Financial'), 'icon' => 'currency'],
            'email' => ['label' => __('Email / SMTP'), 'icon' => 'mail'],
            'whatsapp' => ['label' => __('WhatsApp'), 'icon' => 'message'],
            'seo' => ['label' => __('SEO / Social Preview'), 'icon' => 'globe'],
            'notifications' => ['label' => __('Notifications'), 'icon' => 'bell'],
            'security' => ['label' => __('Security'), 'icon' => 'shield'],
            'trash' => ['label' => __('Trash & Cleanup'), 'icon' => 'trash'],
        ];
    @endphp

    <div x-data="{ activeTab: 'company' }" class="space-y-6">
        <section class="dashboard-hero-card p-4 sm:p-6">
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-brand-500/20 blur-3xl"></div>
            <div class="absolute -bottom-20 left-1/3 h-56 w-56 rounded-full bg-violet-500/10 blur-3xl"></div>

            <div class="relative">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="badge badge-brand">{{ __('Configuration center') }}</span>
                        <span class="badge badge-success">{{ __('Profile') }}</span>
                        <span class="badge badge-muted">{{ __('Email') }}</span>
                    </div>

                    <div>
                        <p class="mobile-section-title">{{ __('System') }}</p>
                        <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('Settings') }}</h1>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">{{ __('Configure your company profile, contact channels, financial defaults, and mail settings from one polished admin workspace.') }}</p>
                    </div>
                </div>

            </div>
        </section>

        <section class="w-full rounded-2xl border border-white/5 bg-slate-900/40 p-1.5 backdrop-blur-sm">
            <div class="flex w-full flex-nowrap gap-1.5 overflow-x-auto no-scrollbar">
                @foreach($tabs as $key => $tab)
                    <button
                        type="button"
                        @click="activeTab = '{{ $key }}'"
                        class="flex min-w-[130px] flex-1 items-center justify-center gap-2 rounded-xl px-3 py-2.5 text-sm font-semibold transition"
                        :class="activeTab === '{{ $key }}' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-400 hover:bg-white/5 hover:text-white'"
                    >
                        @switch($tab['icon'])
                            @case('building')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16" stroke-width="1.8"/><path d="M9 7h2M13 7h2M9 11h2M13 11h2M9 15h2M13 15h2" stroke-width="1.8" stroke-linecap="round"/></svg>
                                @break
                            @case('phone')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92Z" stroke-width="1.8"/></svg>
                                @break
                            @case('currency')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="1.8"/><path d="M14.5 9a3.5 3.5 0 0 0-5 0M14.5 15a3.5 3.5 0 0 1-5 0M12 7v10" stroke-width="1.8" stroke-linecap="round"/></svg>
                                @break
                            @case('mail')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="2" y="4" width="20" height="16" rx="2" stroke-width="1.8"/><path d="M22 7-8.97 12.98a1.94 1.94 0 0 1-2.06 0L2 7" stroke-width="1.8" stroke-linecap="round"/></svg>
                                @break
                            @case('globe')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="9" stroke-width="1.8"/><path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18" stroke-width="1.8" stroke-linecap="round"/></svg>
                                @break
                            @case('bell')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M13.73 21a2 2 0 0 1-3.46 0" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                @break
                            @case('shield')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                @break
                            @case('trash')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" stroke-width="1.8"/><path d="M10 11v6M14 11v6" stroke-width="1.8" stroke-linecap="round"/></svg>
                                @break
                        @endswitch
                        {{ __($tab['label']) }}
                    </button>
                @endforeach
            </div>
        </section>

        {{-- Form --}}
        <form method="POST" action="{{ route('dashboard.settings.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Company Info --}}
            <section x-show="activeTab === 'company'" x-cloak class="app-card app-card--gradient space-y-5">
                <div>
                    <h2 class="text-lg font-semibold text-white">{{ __('Company Information') }}</h2>
                    <p class="text-sm text-slate-400">{{ __('Basic details about your real estate company.') }}</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="space-y-2">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-300">
                            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16" stroke-width="1.8"/></svg>
                            {{ __('Company Name') }} <span class="text-rose-400">*</span>
                        </span>
                        <input class="app-input" name="name" value="{{ old('name', $profile->name) }}" required placeholder="Venecia Developments">
                    </label>

                    <label class="space-y-2">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-300">
                            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" stroke-width="1.8"/><path d="M14 2v6h6" stroke-width="1.8"/></svg>
                            {{ __('Legal Name') }}
                        </span>
                        <input class="app-input" name="legal_name" value="{{ old('legal_name', $profile->legal_name) }}" placeholder="Legal entity name">
                    </label>

                    <label class="space-y-2">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-300">
                            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" stroke-width="1.8"/><circle cx="12" cy="10" r="3" stroke-width="1.8"/></svg>
                            {{ __('Address') }}
                        </span>
                        <input class="app-input" name="address" value="{{ old('address', $profile->address) }}" placeholder="Company address">
                    </label>

                    <label class="space-y-2">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-300">
                            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke-width="1.8"/></svg>
                            {{ __('Website') }}
                        </span>
                        <input class="app-input" name="website" value="{{ old('website', $profile->website) }}" placeholder="https://example.com" type="url">
                    </label>

                    <label class="space-y-2">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-300">
                            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="1.8"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10Z" stroke-width="1.8"/></svg>
                            {{ __('Default Language') }}
                        </span>
                        <select class="app-input" name="default_language">
                            <option value="en" {{ ($profile->default_language ?? 'en') === 'en' ? 'selected' : '' }}>{{ __('English') }}</option>
                            <option value="ar" {{ ($profile->default_language ?? '') === 'ar' ? 'selected' : '' }}>{{ __('العربية') }}</option>
                        </select>
                    </label>

                    <div class="sm:col-span-2 rounded-2xl border border-dashed border-white/10 bg-white/5 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-white">{{ __('Brand Assets') }}</p>
                                <p class="text-xs text-slate-400">{{ __('Upload logo, stamp, and favicon images. SVG and PNG work best in both themes.') }}</p>
                            </div>
                            <span class="badge badge-muted">{{ __('Light / Dark ready') }}</span>
                        </div>

                        <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <label class="space-y-2">
                                <span class="flex items-center gap-2 text-sm font-medium text-slate-300">
                                    <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="4" y="4" width="16" height="16" rx="3" stroke-width="1.8"/></svg>
                                    {{ __('Light Mode Logo') }}
                                </span>
                                <input class="app-input file:mr-3 file:rounded-lg file:border-0 file:bg-brand-600 file:px-3 file:py-1.5 file:text-xs file:text-white hover:file:bg-brand-500" type="file" name="logo_light" accept="image/png,image/jpg,image/jpeg,image/svg+xml,image/webp">
                                @if ($profile->logo_light_path)
                                    <img src="{{ $profile->logo_light_path }}" alt="{{ __('Light Mode Logo') }}" class="mt-2 h-16 w-auto rounded-lg border border-white/10 bg-white object-contain p-1">
                                @endif
                            </label>

                            <label class="space-y-2">
                                <span class="flex items-center gap-2 text-sm font-medium text-slate-300">
                                    <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="4" y="4" width="16" height="16" rx="3" stroke-width="1.8"/></svg>
                                    {{ __('Dark Mode Logo') }}
                                </span>
                                <input class="app-input file:mr-3 file:rounded-lg file:border-0 file:bg-brand-600 file:px-3 file:py-1.5 file:text-xs file:text-white hover:file:bg-brand-500" type="file" name="logo_dark" accept="image/png,image/jpg,image/jpeg,image/svg+xml,image/webp">
                                @if ($profile->logo_dark_path)
                                    <img src="{{ $profile->logo_dark_path }}" alt="{{ __('Dark Mode Logo') }}" class="mt-2 h-16 w-auto rounded-lg border border-white/10 bg-white/5 object-contain p-1">
                                @endif
                            </label>

                            <label class="space-y-2">
                                <span class="flex items-center gap-2 text-sm font-medium text-slate-300">
                                    <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M7 3h10l4 4v14H3V3h4Z" stroke-width="1.8"/><path d="M8 13h8M8 17h5" stroke-width="1.8" stroke-linecap="round"/></svg>
                                    {{ __('Company Stamp') }}
                                </span>
                                <input class="app-input file:mr-3 file:rounded-lg file:border-0 file:bg-brand-600 file:px-3 file:py-1.5 file:text-xs file:text-white hover:file:bg-brand-500" type="file" name="stamp" accept="image/png,image/jpg,image/jpeg,image/svg+xml,image/webp">
                                @if ($profile->stamp_path)
                                    <img src="{{ $profile->stamp_path }}" alt="{{ __('Company Stamp') }}" class="mt-2 h-16 w-auto rounded-lg border border-white/10 bg-white/5 object-contain p-1">
                                @endif
                            </label>

                            <label class="space-y-2">
                                <span class="flex items-center gap-2 text-sm font-medium text-slate-300">
                                    <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="1.8"/><path d="M12 7v10M7 12h10" stroke-width="1.8" stroke-linecap="round"/></svg>
                                    {{ __('Favicon') }}
                                </span>
                                <input class="app-input file:mr-3 file:rounded-lg file:border-0 file:bg-brand-600 file:px-3 file:py-1.5 file:text-xs file:text-white hover:file:bg-brand-500" type="file" name="favicon" accept="image/png,image/x-icon,image/vnd.microsoft.icon">
                                @if ($profile->favicon_path)
                                    <img src="{{ $profile->favicon_path }}" alt="{{ __('Favicon') }}" class="mt-2 h-10 w-10 rounded-lg border border-white/10 bg-white/5 object-contain p-1">
                                @endif
                            </label>
                        </div>

                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <label class="space-y-2">
                                <span class="flex items-center gap-2 text-sm font-medium text-slate-300">
                                    <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="4" y="4" width="16" height="16" rx="3" stroke-width="1.8"/></svg>
                                    {{ __('Logo Height — Desktop (px)') }}
                                </span>
                                <input class="app-input" type="number" name="logo_height_desktop" min="16" max="200" value="{{ old('logo_height_desktop', $profile->logo_height_desktop ?? 40) }}">
                                <p class="text-xs text-slate-500">{{ __('Logo size in the navbar on desktop screens') }}</p>
                            </label>
                            <label class="space-y-2">
                                <span class="flex items-center gap-2 text-sm font-medium text-slate-300">
                                    <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="4" y="4" width="16" height="16" rx="3" stroke-width="1.8"/></svg>
                                    {{ __('Logo Height — Mobile (px)') }}
                                </span>
                                <input class="app-input" type="number" name="logo_height_mobile" min="16" max="200" value="{{ old('logo_height_mobile', $profile->logo_height_mobile ?? 36) }}">
                                <p class="text-xs text-slate-500">{{ __('Logo size in the navbar on mobile screens') }}</p>
                            </label>
                        </div>
                    </div>

                    {{-- Available Features (finishing specs & additional services selection list) --}}
                    <div class="sm:col-span-2 mt-4 rounded-2xl border border-dashed border-white/10 bg-white/5 p-4" x-data="{
                        features: @js($initialFeatures),
                        icons: {
                            sparkles: '<svg viewBox=&quot;0 0 24 24&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; stroke-width=&quot;1.8&quot; stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; class=&quot;h-5 w-5&quot;><path d=&quot;M12 3l1.9 5.8a2 2 0 0 0 1.3 1.3L21 12l-5.8 1.9a2 2 0 0 0-1.3 1.3L12 21l-1.9-5.8a2 2 0 0 0-1.3-1.3L3 12l5.8-1.9a2 2 0 0 0 1.3-1.3L12 3z&quot;/><path d=&quot;M18 3v4M16 5h4&quot;/></svg>',
                            car: '<svg viewBox=&quot;0 0 24 24&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; stroke-width=&quot;1.8&quot; stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; class=&quot;h-5 w-5&quot;><path d=&quot;M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2&quot;/><circle cx=&quot;7&quot; cy=&quot;17&quot; r=&quot;2&quot;/><path d=&quot;M9 17h6&quot;/><circle cx=&quot;17&quot; cy=&quot;17&quot; r=&quot;2&quot;/></svg>',
                            view: '<svg viewBox=&quot;0 0 24 24&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; stroke-width=&quot;1.8&quot; stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; class=&quot;h-5 w-5&quot;><rect x=&quot;3&quot; y=&quot;4&quot; width=&quot;18&quot; height=&quot;16&quot; rx=&quot;2&quot;/><path d=&quot;M3 15l4.5-4.5 4 4 3.5-3.5L21 15&quot;/><circle cx=&quot;16&quot; cy=&quot;8&quot; r=&quot;1.5&quot;/></svg>',
                            ac: '<svg viewBox=&quot;0 0 24 24&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; stroke-width=&quot;1.8&quot; stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; class=&quot;h-5 w-5&quot;><path d=&quot;M12 2v20M2 12h20M4.9 4.9l14.2 14.2M19.1 4.9 4.9 19.1&quot;/><path d=&quot;M12 6.5 9.5 5M12 6.5 14.5 5M12 17.5 9.5 19M12 17.5 14.5 19M6.5 12 5 9.5M6.5 12 5 14.5M17.5 12 19 9.5M17.5 12 19 14.5&quot;/></svg>',
                            security: '<svg viewBox=&quot;0 0 24 24&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; stroke-width=&quot;1.8&quot; stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; class=&quot;h-5 w-5&quot;><path d=&quot;M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z&quot;/><path d=&quot;m9 12 2 2 4-4&quot;/></svg>',
                            elevator: '<svg viewBox=&quot;0 0 24 24&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; stroke-width=&quot;1.8&quot; stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; class=&quot;h-5 w-5&quot;><rect x=&quot;7&quot; y=&quot;3&quot; width=&quot;10&quot; height=&quot;18&quot; rx=&quot;2&quot;/><path d=&quot;m10 8 2-2 2 2M10 16l2 2 2-2&quot;/></svg>'
                        },
                        addFeature() { this.features.push({ icon: 'sparkles', title: '', desc: '' }); },
                        removeFeature(i) { this.features.splice(i, 1); },
                    }">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-white">{{ __('Available Features') }}</p>
                                <p class="text-xs text-slate-400">{{ __('Finishing specs & additional services shown on public unit pages. Each unit selects from this list.') }}</p>
                            </div>
                            <span class="badge badge-muted">{{ __('Selection list') }}</span>
                        </div>

                        <div class="mt-4 space-y-3">
                            <template x-for="(feature, i) in features" :key="i">
                                <div class="rounded-xl border border-white/10 bg-slate-950/40 p-3">
                                    <div class="mb-2 flex items-center justify-between">
                                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400" x-text="'{{ __('Feature') }} ' + (i + 1)"></p>
                                        <button type="button" @click="removeFeature(i)" class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-xs font-semibold text-rose-400 transition hover:bg-rose-500/10">{{ __('Remove') }}</button>
                                    </div>
                                    <div class="grid gap-3 sm:grid-cols-[9.5rem_1fr]">
                                        <label class="space-y-1">
                                            <span class="text-[11px] text-slate-500">{{ __('Icon') }}</span>
                                            <div class="flex items-center gap-2">
                                                <select class="app-input h-10 min-w-0 flex-1 text-xs" x-model="feature.icon" :name="'available_features[' + i + '][icon]'">
                                                    <template x-for="(svg, key) in icons" :key="key">
                                                        <option :value="key" x-text="key"></option>
                                                    </template>
                                                </select>
                                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-brand-500/15 text-brand-300" x-html="icons[feature.icon] || icons.sparkles"></span>
                                            </div>
                                        </label>
                                        <label class="space-y-1">
                                            <span class="text-[11px] text-slate-500">{{ __('Title') }}</span>
                                            <input class="app-input" x-model="feature.title" :name="'available_features[' + i + '][title]'">
                                        </label>
                                        <label class="space-y-1 sm:col-span-2">
                                            <span class="text-[11px] text-slate-500">{{ __('Description') }}</span>
                                            <textarea class="app-textarea min-h-16" x-model="feature.desc" :name="'available_features[' + i + '][desc]'"></textarea>
                                        </label>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <button type="button" @click="addFeature()" class="app-button--ghost mt-4 w-full">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round"/></svg>
                            {{ __('+ Add Feature') }}
                        </button>
                    </div>

                    <div class="mt-4 hidden">
                            <input type="hidden" name="logo_path" value="{{ old('logo_path', $profile->logo_path) }}">
                            <input type="hidden" name="logo_light_path" value="{{ old('logo_light_path', $profile->logo_light_path) }}">
                            <input type="hidden" name="logo_dark_path" value="{{ old('logo_dark_path', $profile->logo_dark_path) }}">
                            <input type="hidden" name="stamp_path" value="{{ old('stamp_path', $profile->stamp_path) }}">
                            <input type="hidden" name="favicon_path" value="{{ old('favicon_path', $profile->favicon_path) }}">
                        </div>
                </div>
            </section>

            {{-- Contact Details --}}
            <section x-show="activeTab === 'contact'" x-cloak class="app-card app-card--gradient space-y-5">
                <div>
                    <h2 class="text-lg font-semibold text-white">{{ __('Contact Details') }}</h2>
                    <p class="text-sm text-slate-400">{{ __('How customers and partners can reach you.') }}</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="space-y-2">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-300">
                            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92Z" stroke-width="1.8"/></svg>
                            {{ __('Phone Number') }}
                        </span>
                        <input class="app-input" name="phone" value="{{ old('phone', $profile->phone) }}" placeholder="+966 XX XXX XXXX" type="tel" inputmode="tel">
                    </label>

                    <label class="space-y-2">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-300">
                            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="2" y="4" width="20" height="16" rx="2" stroke-width="1.8"/><path d="M22 7-8.97 12.98a1.94 1.94 0 0 1-2.06 0L2 7" stroke-width="1.8" stroke-linecap="round"/></svg>
                            {{ __('Email Address') }}
                        </span>
                        <input class="app-input" name="email" value="{{ old('email', $profile->email) }}" placeholder="info@example.com" type="email" inputmode="email">
                    </label>

                    <label class="space-y-2">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-300">
                            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="currentColor"><path d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.258.42-.374.995-.374 1.752v1.297h3.919l-.386 3.667H9.101V23.691h.001z"/></svg>
                            {{ __('Facebook URL') }}
                        </span>
                        <input class="app-input" name="facebook_url" value="{{ old('facebook_url', $profile->facebook_url) }}" placeholder="https://facebook.com/venecia" type="url" inputmode="url" dir="ltr">
                    </label>

                    <label class="space-y-2">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-300">
                            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>
                            {{ __('Instagram URL') }}
                        </span>
                        <input class="app-input" name="instagram_url" value="{{ old('instagram_url', $profile->instagram_url) }}" placeholder="https://instagram.com/venecia" type="url" inputmode="url" dir="ltr">
                    </label>

                    <label class="space-y-2 sm:col-span-2">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-300">
                            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" stroke-width="1.8"/><circle cx="12" cy="10" r="3" stroke-width="1.8"/></svg>
                            {{ __('Google Maps API Key') }}
                        </span>
                        <input class="app-input" name="google_maps_api_key" value="{{ old('google_maps_api_key', $profile->google_maps_api_key) }}" placeholder="AIza..." autocomplete="off">
                        <p class="text-xs text-slate-500">{{ __('Used to embed the company location map on the contact page. If empty, an OpenStreetMap fallback is shown.') }}</p>
                    </label>
                </div>
            </section>

            {{-- Financial --}}
            <section x-show="activeTab === 'financial'" x-cloak class="app-card app-card--gradient space-y-5">
                <div>
                    <h2 class="text-lg font-semibold text-white">{{ __('Financial Settings') }}</h2>
                    <p class="text-sm text-slate-400">{{ __('Currency and maintenance fee configuration.') }}</p>
                </div>

                <div class="rounded-2xl border border-brand-500/20 bg-brand-500/10 px-4 py-3 text-sm text-brand-100/90">
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-brand-300" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M13 16h-1v-4h-1m1-4h.01M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <p>{{ __('Pricing and fee values are stored here and they apply in both Dark Mode and Light Mode because the theme only changes colors, not settings.') }}</p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="space-y-2">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-300">
                            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="1.8"/><path d="M14.5 9a3.5 3.5 0 0 0-5 0M14.5 15a3.5 3.5 0 0 1-5 0M12 7v10" stroke-width="1.8" stroke-linecap="round"/></svg>
                            {{ __('Currency Code') }}
                        </span>
                        <input class="app-input" name="currency_code" value="{{ old('currency_code', $profile->currency_code ?? 'SAR') }}" placeholder="{{ __('SAR') }}" maxlength="10">
                        <p class="text-xs text-slate-500">{{ __('ISO 4217 currency code (e.g. SAR, AED, EGP)') }}</p>
                    </label>

                    <label class="space-y-2">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-300">
                            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            {{ __('Maintenance Percentage') }}
                        </span>
                        <div class="relative">
                            <input class="app-input pr-8" name="maintenance_percent" value="{{ old('maintenance_percent', $profile->maintenance_percent ?? 0.00) }}" placeholder="0.00" type="number" step="0.01" min="0" max="100" required>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-slate-500">%</span>
                        </div>
                        <p class="text-xs text-slate-500">{{ __('Applied as a percentage on unit prices') }}</p>
                    </label>

                </div>
            </section>

            {{-- Email / SMTP --}}
            <section x-show="activeTab === 'email'" x-cloak class="app-card app-card--gradient space-y-5">
                <div>
                    <h2 class="text-lg font-semibold text-white">{{ __('Email Configuration') }}</h2>
                    <p class="text-sm text-slate-400">{{ __('Configure email sending settings for notifications and reports.') }}</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="space-y-2">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-300">
                            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="12" cy="7" r="4" stroke-width="1.8"/></svg>
                            {{ __('From Name') }}
                        </span>
                        <input class="app-input" name="smtp_from_name" value="{{ old('smtp_from_name', $profile->smtp_from_name) }}" placeholder="Venecia Developments Notifications">
                    </label>

                    <label class="space-y-2">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-300">
                            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="2" y="4" width="20" height="16" rx="2" stroke-width="1.8"/><path d="M22 7-8.97 12.98a1.94 1.94 0 0 1-2.06 0L2 7" stroke-width="1.8" stroke-linecap="round"/></svg>
                            {{ __('From Email') }}
                        </span>
                        <input class="app-input" name="smtp_from_email" value="{{ old('smtp_from_email', $profile->smtp_from_email) }}" placeholder="noreply@example.com" type="email" inputmode="email">
                    </label>
                </div>

                <div class="rounded-xl bg-amber-500/10 border border-amber-500/20 p-4">
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke-width="1.8"/><path d="M12 9v4M12 17h.01" stroke-width="1.8" stroke-linecap="round"/></svg>
                        <div>
                            <p class="text-sm font-semibold text-amber-300">{{ __('SMTP Configuration') }}</p>
                            <p class="mt-1 text-xs text-amber-200/70">{{ __('SMTP server settings are configured via environment variables (.env file). Contact your system administrator to modify mail server settings.') }}</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- WhatsApp --}}
            <section x-show="activeTab === 'whatsapp'" x-cloak class="app-card app-card--gradient space-y-5">
                <div>
                    <h2 class="text-lg font-semibold text-white">{{ __('WhatsApp Configuration') }}</h2>
                    <p class="text-sm text-slate-400">{{ __('Configure WhatsApp notifications using Evolution API for sales manager alerts.') }}</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="space-y-2">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-300">
                            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M.5 23.5l1.6-5.8A11.3 11.3 0 0 1 0 11.3C0 5.1 5.1 0 11.3 0A11.3 11.3 0 0 1 22.5 11.3c0 6.2-5.1 11.3-11.3 11.3-1.9 0-3.8-.5-5.5-1.3L.5 23.5Zm6-3.4.3.2a9.3 9.3 0 0 0 4.5 1.2c5.1 0 9.3-4.2 9.3-9.3S16.4 2.9 11.3 2.9 2 7.1 2 12.2c0 1.8.5 3.5 1.4 5l.2.3-1 3.6 3.9-1Zm9.2-4.3c-.2-.1-1.4-.7-1.6-.8-.2-.1-.4-.1-.6.1l-.9 1.1c-.1.1-.3.1-.5 0-.2-.1-.8-.3-1.6-.9-.7-.5-1.3-1.2-1.5-1.4-.2-.2 0-.4.1-.5l.4-.4c.1-.1.2-.3.3-.4.1-.1 0-.3 0-.4 0-.1-.6-1.5-.8-2-.2-.4-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.2.2-.8.7-.8 1.7 0 1 .8 1.9.9 2 .1.1 1.5 2.4 3.6 3.3.5.2.9.4 1.3.5.6.2 1.2.2 1.7.1.5-.1 1.4-.6 1.6-1.1.2-.5.2-1 .1-1.1-.1-.1-.2-.2-.4-.3Z"/></svg>
                            {{ __('Sales Manager WhatsApp') }}
                        </span>
                        <input class="app-input" name="sales_manager_whatsapp" value="{{ old('sales_manager_whatsapp', $profile->sales_manager_whatsapp) }}" placeholder="201234567890" type="tel" inputmode="tel">
                        <p class="text-[11px] text-slate-500">{{ __('Phone number to receive WhatsApp notifications (international format without +).') }}</p>
                    </label>
                </div>

                <div class="space-y-4 rounded-2xl border border-white/10 bg-white/5 p-4">
                    <p class="text-sm font-semibold text-white">{{ __('Evolution API Settings') }}</p>
                    <p class="text-xs text-slate-400">{{ __('Configure your Evolution API instance for WhatsApp message sending.') }}</p>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-slate-300">{{ __('API URL') }}</span>
                            <input class="app-input" name="evolution_api_url" value="{{ old('evolution_api_url', $profile->evolution_api_url ?: \App\Models\CompanyProfile::DEFAULT_EVOLUTION_API_URL) }}" placeholder="https://evolution.example.com" type="url">
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-medium text-slate-300">{{ __('API Key') }}</span>
                            <input class="app-input" name="evolution_api_key" value="{{ old('evolution_api_key', $profile->evolution_api_key) }}" placeholder="your-api-key" type="password">
                        </label>

                        <label class="space-y-2 sm:col-span-2">
                            <span class="text-sm font-medium text-slate-300">{{ __('Instance Name') }}</span>
                            <input class="app-input" name="evolution_instance_name" value="{{ old('evolution_instance_name', $profile->evolution_instance_name ?: \App\Models\CompanyProfile::DEFAULT_EVOLUTION_INSTANCE_NAME) }}" placeholder="my-whatsapp-instance">
                        </label>

                        <label class="space-y-2 sm:col-span-2">
                            <span class="text-sm font-medium text-slate-300">{{ __('Instance Dashboard URL') }}</span>
                            <input class="app-input" name="evolution_dashboard_url" value="{{ old('evolution_dashboard_url', $profile->evolution_dashboard_url ?: \App\Models\CompanyProfile::DEFAULT_EVOLUTION_DASHBOARD_URL) }}" placeholder="{{ \App\Models\CompanyProfile::DEFAULT_EVOLUTION_DASHBOARD_URL }}" type="url">
                            <p class="text-[11px] text-slate-500">{{ __('This opens the Evolution Manager page for this instance. It is separate from the API base URL used for sending messages.') }}</p>
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-medium text-slate-300">{{ __('Outgoing message color') }}</span>
                            <input class="app-input h-10 w-full" name="evolution_outgoing_color" value="{{ old('evolution_outgoing_color', $profile->evolution_outgoing_color ?: '#005c4b') }}" type="color">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-slate-300">{{ __('Incoming message color') }}</span>
                            <input class="app-input h-10 w-full" name="evolution_incoming_color" value="{{ old('evolution_incoming_color', $profile->evolution_incoming_color ?: '#ffffff') }}" type="color">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-slate-300">{{ __('Chat background color') }}</span>
                            <input class="app-input h-10 w-full" name="evolution_chat_background" value="{{ old('evolution_chat_background', $profile->evolution_chat_background ?: '#0f172a') }}" type="color">
                        </label>
                    </div>

                    <div class="rounded-xl border border-white/10 bg-slate-950/40 p-3">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">{{ __('Incoming webhook URL') }}</p>
                        <p class="mt-1 font-mono text-xs text-emerald-300" dir="ltr">{{ url('/webhook/whatsapp/evolution') }}</p>
                        <p class="mt-1.5 text-[11px] text-slate-500">{{ __('Point the Evolution instance webhook to this URL so incoming customer messages appear in the WhatsApp panel. You can also press “Register Webhook” inside the panel.') }}</p>
                    </div>
                </div>

                <div class="rounded-xl bg-emerald-500/10 border border-emerald-500/20 p-4">
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke-width="1.8" stroke-linecap="round"/></svg>
                        <div>
                            <p class="text-sm font-semibold text-emerald-300">{{ __('Notifications') }}</p>
                            <p class="mt-1 text-xs text-emerald-200/70">{{ __('When configured, the system will automatically send WhatsApp notifications to the sales manager for new leads, offers, and pipeline updates.') }}</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- SEO / Social Preview --}}
            <section x-show="activeTab === 'seo'" x-cloak class="app-card app-card--gradient space-y-5">
                <div>
                    <h2 class="text-lg font-semibold text-white">{{ __('SEO & Social Sharing') }}</h2>
                    <p class="text-sm text-slate-400">{{ __('The title, description and image shown when your site link is shared on WhatsApp, Facebook, Instagram and other platforms.') }}</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="space-y-2 sm:col-span-2">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-300">
                            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 7h16M4 12h16M4 17h10" stroke-width="1.8" stroke-linecap="round"/></svg>
                            {{ __('SEO Title') }}
                        </span>
                        <input class="app-input" name="seo_title" value="{{ old('seo_title', $profile->seo_title) }}" placeholder="Venecia Developments — شقق فاخرة في أسيوط" maxlength="120">
                        <p class="text-[11px] text-slate-500">{{ __('Used as the page title and the bold headline in social previews (max 120 characters).') }}</p>
                    </label>

                    <label class="space-y-2 sm:col-span-2">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-300">
                            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 6h16M4 12h10M4 18h7" stroke-width="1.8" stroke-linecap="round"/></svg>
                            {{ __('SEO Description') }}
                        </span>
                        <textarea class="app-input min-h-24" name="seo_description" maxlength="500" placeholder="{{ __('رواد في صناعة التطوير العقاري والمجتمعات السكنية الفاخرة...') }}">{{ old('seo_description', $profile->seo_description) }}</textarea>
                        <p class="text-[11px] text-slate-500">{{ __('The gray preview text under the link — keep it between 70 and 160 characters for best results.') }}</p>
                    </label>

                    <label class="space-y-2 sm:col-span-2">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-300">
                            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="18" height="18" rx="3" stroke-width="1.8"/><circle cx="9" cy="9" r="2" stroke-width="1.8"/><path d="m21 15-5-5L5 21" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            {{ __('Preview Image') }}
                        </span>
                        <input class="app-input file:mr-3 file:rounded-lg file:border-0 file:bg-brand-600 file:px-3 file:py-1.5 file:text-xs file:text-white hover:file:bg-brand-500" type="file" name="seo_image" accept="image/png,image/jpg,image/jpeg,image/webp">
                        @if ($profile->seo_image_path)
                            <img src="{{ $profile->seo_image_path }}" alt="{{ __('Preview Image') }}" class="mt-2 h-32 w-auto max-w-full rounded-xl border border-white/10 bg-white object-contain p-1">
                        @endif
                        <p class="text-[11px] text-slate-500">{{ __('Recommended size 1200×630px (the image card shown when sharing the link). If empty, the company logo is used.') }}</p>
                    </label>
                </div>

                <div class="rounded-xl bg-brand-500/10 border border-brand-500/20 p-4">
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke-width="1.8" stroke-linecap="round"/></svg>
                        <div>
                            <p class="text-sm font-semibold text-brand-300">{{ __('Social sharing preview') }}</p>
                            <p class="mt-1 text-xs text-brand-200/70">{{ __('When you share the site link on WhatsApp or Facebook, the card shows: the SEO title as the headline, the description below it, and the preview image on the side (1200×630).') }}</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Security --}}
            <section x-show="activeTab === 'security'" x-cloak class="app-card app-card--gradient space-y-5">
                <div>
                    <h2 class="text-lg font-semibold text-white">{{ __('Security & Access') }}</h2>
                    <p class="text-sm text-slate-400">{{ __('Review your login activity and account security options.') }}</p>
                </div>
                @php($user = auth()->user())
                @php($histories = $user?->loginHistories()->latest('logged_in_at')->limit(10)->get() ?? collect())

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

                {{-- Two-Factor Authentication management --}}
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
                                <p class="text-sm font-semibold text-white">{{ __('Google Authenticator (2FA)') }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ __('Add an extra security layer: after your password you must enter a time-based code from your phone.') }}</p>
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

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="border-b border-white/10 text-xs uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="pb-3 pr-4">{{ __('When') }}</th>
                                <th class="pb-3 pr-4">{{ __('IP Address') }}</th>
                                <th class="pb-3 pr-4">{{ __('Device') }}</th>
                                <th class="pb-3 pr-4">{{ __('Status') }}</th>
                                <th class="pb-3 pr-4">{{ __('Logged Out') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse ($histories as $history)
                                <tr>
                                    <td class="py-3 pr-4">{{ $history->logged_in_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                    <td class="py-3 pr-4 ltr">{{ $history->ip_address ?? '-' }}</td>
                                    <td class="py-3 pr-4">{{ $history->device_name ?? $history->device_type ?? '-' }}</td>
                                    <td class="py-3 pr-4">
                                        <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $history->is_successful ? 'bg-emerald-500/10 text-emerald-300' : 'bg-rose-500/10 text-rose-300' }}">
                                            {{ $history->is_successful ? __('Successful') : __('Failed') }}
                                        </span>
                                    </td>
                                    <td class="py-3 pr-4">{{ $history->logged_out_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-slate-500">{{ __('No login history found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- Trash & Cleanup --}}
            <section x-show="activeTab === 'trash'" x-cloak class="app-card app-card--gradient space-y-5">
                <div>
                    <h2 class="text-lg font-semibold text-white">{{ __('Trash & Cleanup') }}</h2>
                    <p class="text-sm text-slate-400">{{ __('Control how long deleted items stay in the trash before they are permanently removed.') }}</p>
                </div>

                <div class="rounded-2xl border border-amber-500/20 bg-amber-500/10 px-4 py-3 text-sm text-amber-100/90">
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-300" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke-width="1.8"/><path d="M12 9v4M12 17h.01" stroke-width="1.8" stroke-linecap="round"/></svg>
                        <p>{{ __('Permanently deleted items cannot be recovered. A warning notification is sent 7 days before automatic deletion.') }}</p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="space-y-2">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-300">
                            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.8"/><path d="M16 2v4M8 2v4M3 10h18" stroke-width="1.8" stroke-linecap="round"/></svg>
                            {{ __('Trash retention period (days)') }}
                        </span>
                        <div class="relative">
                            <input class="app-input pr-10" name="trash_retention_days" value="{{ old('trash_retention_days', $profile->trash_retention_days ?? 30) }}" placeholder="30" type="number" min="1" max="365" inputmode="numeric" required>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-slate-500">{{ __('days') }}</span>
                        </div>
                        <p class="text-xs text-slate-500">{{ __('How many days an item can stay in the trash before automatic permanent deletion.') }}</p>
                    </label>

                    <label class="space-y-2">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-300">
                            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="1.8"/><path d="M12 8v4M12 16h.01" stroke-width="1.8" stroke-linecap="round"/></svg>
                            {{ __('Automatic trash cleanup') }}
                        </span>
                        <label class="flex cursor-pointer items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-3">
                            <span class="text-sm text-slate-300">{{ __('Permanently delete trashed items automatically') }}</span>
                            <input type="checkbox" name="auto_purge_enabled" value="1" {{ $profile->auto_purge_enabled ?? true ? 'checked' : '' }} class="h-5 w-5 rounded border-white/20 bg-slate-800 text-brand-500 focus:ring-brand-500">
                        </label>
                        <p class="text-xs text-slate-500">{{ __('When disabled, trashed items are kept forever until you restore or delete them manually.') }}</p>
                    </label>
                </div>
            </section>

            {{-- Save Button --}}
            <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 p-4">
                <p class="text-sm text-slate-400">{{ __('Changes are saved immediately.') }}</p>
                <button type="submit" class="app-button">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z" stroke-width="1.8"/><path d="M17 21v-8H7v8M7 3v5h8" stroke-width="1.8"/></svg>
                    {{ __('Save All Settings') }}
                </button>
            </div>
        </form>

        {{-- Notification preferences (personal, per signed-in user) --}}
        <section x-show="activeTab === 'notifications'" x-cloak class="app-card app-card--gradient space-y-5">
            <div>
                <h2 class="text-lg font-semibold text-white">{{ __('Notification Preferences') }}</h2>
                <p class="text-sm text-slate-400">{{ __('Choose which notifications you personally receive. Your role permissions decide what you are allowed to get; these toggles let you opt out of specific types.') }}</p>
            </div>

            <form method="POST" action="{{ route('dashboard.settings.notifications.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="space-y-3">
                    @forelse ($allowedTypes as $typeKey => $typeMeta)
                        <label class="flex cursor-pointer items-center justify-between gap-4 rounded-2xl border border-white/10 bg-white/5 px-4 py-3 transition hover:border-white/20">
                            <span class="min-w-0">
                                <span class="block text-sm font-semibold text-white">{{ $typeMeta['title_'.app()->getLocale()] ?? $typeMeta['title_en'] }}</span>
                                <span class="mt-0.5 block text-xs text-slate-500">{{ $typeMeta['permission'] }}</span>
                            </span>
                            <input
                                type="checkbox"
                                name="disabled_notifications[]"
                                value="{{ $typeKey }}"
                                {{ in_array($typeKey, $userPrefs, true) ? '' : 'checked' }}
                                class="h-5 w-5 shrink-0 rounded border-white/20 bg-slate-800 text-brand-500 focus:ring-brand-500"
                            >
                        </label>
                    @empty
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-6 text-center">
                            <p class="text-sm text-slate-400">{{ __('Your role does not include any notification permissions yet. Ask an administrator to grant them.') }}</p>
                        </div>
                    @endforelse
                </div>

                <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 p-4">
                    <p class="text-sm text-slate-400">{{ __('Unchecking a type stops future notifications of that kind from reaching you.') }}</p>
                    <button type="submit" class="app-button">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 6 9 17l-5-5" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        {{ __('Save Preferences') }}
                    </button>
                </div>
            </form>
        </section>
    </div>
@endsection
