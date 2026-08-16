@extends('layouts.dashboard')

@section('content')
    <div class="space-y-6" x-data="{ submitting: false }">
        @if (session('status'))
            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="flex items-center gap-3 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12l5 5L20 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span>{{ session('status') }}</span>
                <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-200">×</button>
            </div>
        @endif

        <section class="dashboard-hero-card p-6 sm:p-8">
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-brand-500/20 blur-3xl"></div>
            
            <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="mobile-section-title">{{ $project ? __('Project management') : __('New development') }}</p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">
                        {{ $project ? $project->name : __('Create project') }}
                    </h1>
                    <p class="mt-2 text-sm text-slate-400">
                        {{ __('Define project identity, location, and visibility settings for the public website.') }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="submit" form="project-form" class="app-button" :disabled="submitting" :class="submitting ? 'pointer-events-none opacity-60' : ''">
                        <svg x-show="!submitting" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z" stroke-width="1.8"/><path d="M17 21v-8H7v8M7 3v5h8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <svg x-show="submitting" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 12a9 9 0 1 1-6.219-8.56" stroke-width="2" stroke-linecap="round"/></svg>
                        <span x-text="submitting ? '{{ __('Saving...') }}' : '{{ $project ? __('Update Project') : __('Create Project') }}'"></span>
                    </button>
                    <a href="{{ route('dashboard.projects.index') }}" class="app-button--ghost">{{ __('Cancel') }}</a>
                </div>
            </div>
        </section>

        <form id="project-form" method="POST" action="{{ $project ? route('dashboard.projects.update', $project) : route('dashboard.projects.store') }}" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-[minmax(0,0.92fr)_minmax(0,1.08fr)]" data-save-shortcut @submit="submitting = true">
            @csrf
            @if($project) @method('PUT') @endif

            {{-- Identity --}}
                <section class="app-card app-card--gradient space-y-5">
                    <div class="flex items-center gap-3 border-b border-white/5 pb-4">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-500/15 text-brand-400">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 2H2v10h10V2zM22 12H12v10h10V12zM12 12H2v10h10V12zM22 2H12v10h10V2z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <h2 class="text-lg font-semibold text-white">{{ __('Project Identity') }}</h2>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="name" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Project Name') }} <span class="text-rose-400">*</span></label>
                            <input type="text" id="name" name="name" class="app-input" value="{{ old('name', $project?->name) }}" required placeholder="{{ __('e.g. Venecia Heights') }}">
                            @error('name') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label for="code" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Project Code') }}</label>
                            <input type="text" id="code" name="code" class="app-input" value="{{ old('code', $project?->code) }}" placeholder="{{ __('e.g. VH-01') }}">
                            @error('code') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label for="status" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Status') }} <span class="text-rose-400">*</span></label>
                            <select id="status" name="status" class="app-select" required>
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', $project?->status) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('status') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                        </div>

                        <div class="sm:col-span-2" x-data="{ slug: '{{ old('slug', $project?->slug) }}' }">
                            <label for="slug" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Project URL (slug)') }}</label>
                            <div class="flex items-center gap-2">
                                <span class="flex h-11 shrink-0 items-center rounded-xl border border-white/10 bg-slate-950/40 px-3 font-mono text-xs text-slate-400 ltr">/projects/</span>
                                <input type="text" id="slug" name="slug" x-model="slug" class="app-input font-mono ltr" value="{{ old('slug', $project?->slug) }}" placeholder="{{ __('e.g. fynsya') }}">
                            </div>
                            <p class="mt-1.5 text-xs text-slate-400">
                                {{ __('Public link') }}:
                                <a :href="'/projects/' + (slug || '{{ $project?->slug ?? '...' }}')" x-text="'/projects/' + (slug || '{{ $project?->slug ?? '...' }}')" class="font-mono text-brand-300 hover:underline" target="_blank" rel="noopener"></a>
                            </p>
                            <p class="mt-1 text-xs text-slate-500">{{ __('Leave empty to auto-generate from the project name. Use only letters, numbers, dashes and underscores.') }}</p>
                            @error('slug') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="description" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Description') }}</label>
                            <textarea id="description" name="description" class="app-textarea min-h-32">{{ old('description', $project?->description) }}</textarea>
                            @error('description') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                {{-- Location --}}
                <section class="app-card app-card--gradient space-y-5">
                    <div class="flex items-center gap-3 border-b border-white/5 pb-4">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-500/15 text-violet-400">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" stroke-width="1.8"/><circle cx="12" cy="10" r="3" stroke-width="1.8"/></svg>
                        </span>
                        <h2 class="text-lg font-semibold text-white">{{ __('Location Details') }}</h2>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="location" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Full Address / Landmark') }}</label>
                            <input type="text" id="location" name="location" class="app-input" value="{{ old('location', $project?->location) }}" placeholder="{{ __('e.g. New Cairo, 5th Settlement') }}">
                            @error('location') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="city" class="mb-2 block text-sm font-medium text-slate-300">{{ __('City') }}</label>
                            <input type="text" id="city" name="city" class="app-input" value="{{ old('city', $project?->city) }}">
                            @error('city') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="country" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Country') }}</label>
                            <input type="text" id="country" name="country" class="app-input" value="{{ old('country', $project?->country) }}">
                            @error('country') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="map_lat" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Google Maps Coordinates') }}</label>
                            <div class="grid grid-cols-2 gap-4" x-data="{ mapLat: '{{ old('map_lat', $project?->map_lat) }}', mapLng: '{{ old('map_lng', $project?->map_lng) }}' }">
                                <div>
                                    <input type="text" id="map_lat" name="map_lat" class="app-input" x-model="mapLat" placeholder="{{ __('Latitude') }} e.g. 30.0444">
                                    @error('map_lat') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <input type="text" id="map_lng" name="map_lng" class="app-input" x-model="mapLng" placeholder="{{ __('Longitude') }} e.g. 31.2357">
                                    @error('map_lng') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                                </div>
                                <div class="col-span-2 overflow-hidden rounded-2xl border border-white/10">
                                    <iframe :src="'https://www.google.com/maps?q=' + (mapLat || '30.0444') + ',' + (mapLng || '31.2357') + '&z=15&output=embed'" class="h-48 w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Buildings & Floors --}}
                <section class="app-card app-card--gradient space-y-5" x-data="{ buildings: @js($buildingsJson) }">
                    <div class="flex items-center gap-3 border-b border-white/5 pb-4">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-500/15 text-brand-400">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 21h18M5 21V7a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v14M9 9h.01M15 9h.01M9 13h.01M15 13h.01M9 17h.01M15 17h.01" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </span>
                        <div>
                            <h2 class="text-lg font-semibold text-white">{{ __('Buildings & Floors') }}</h2>
                            <p class="text-xs text-slate-400">{{ __('Each building can contain up to 10 floors.') }}</p>
                        </div>
                    </div>

                    <p class="text-sm text-slate-300">{{ __('A project can contain one or more buildings (towers, compounds, or blocks).') }}</p>

                    <template x-for="(building, index) in buildings" :key="index">
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <div class="mb-3 flex items-center justify-between">
                                <p class="text-sm font-semibold text-white" x-text="'{{ __('Building') }} ' + (index + 1)"></p>
                                <button type="button" onclick="confirmAction('{{ __('Remove building') }}', '{{ __('Removing this building will delete it on save if it has no units. Continue?') }}', () => buildings.splice(index, 1), '{{ __('Remove') }}')" class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-xs font-semibold text-rose-400 transition hover:bg-rose-500/10">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                                    {{ __('Remove') }}
                                </button>
                            </div>

                            <input type="hidden" :name="'buildings[' + index + '][id]'" :value="building.id || ''">

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label :for="'building_name_' + index" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Building Name') }}</label>
                                    <input type="text" :id="'building_name_' + index" :name="'buildings[' + index + '][name]'" x-model="building.name" class="app-input" placeholder="{{ __('e.g. Tower A') }}" required>
                                </div>
                                <div>
                                    <label :for="'building_floors_' + index" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Number of Floors') }} <span class="text-rose-400">*</span></label>
                                    <input type="number" :id="'building_floors_' + index" :name="'buildings[' + index + '][floors_count]'" x-model.number="building.floors_count" class="app-input" min="1" max="10" required>
                                    <p class="mt-1 text-xs text-slate-500">{{ __('Max 10 floors per building') }}</p>
                                </div>
                            </div>
                        </div>
                    </template>

                    <button type="button" @click="buildings.push({ id: '', name: '', floors_count: 1 })" class="app-button--ghost w-full">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round"/></svg>
                        {{ __('+ Add Building') }}
                    </button>

                    @error('buildings') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                </section>

                {{-- Images --}}
                <section class="app-card app-card--gradient space-y-5">
                    <div class="flex items-center gap-3 border-b border-white/5 pb-4">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-400">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke-width="1.8"/><polyline points="9 22 9 12 15 12 15 22" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <h2 class="text-lg font-semibold text-white">{{ __('Project Images') }}</h2>
                    </div>

                    @php
                        $currentCoverImage = $project?->cover_image_path ? asset('storage/'.$project->cover_image_path) : null;
                    @endphp

                    <div class="space-y-3 rounded-2xl border border-white/10 bg-white/5 p-4">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="text-sm font-semibold text-white">{{ __('Main Project Image') }}</p>
                                <p class="text-xs text-slate-400">{{ __('Choose the primary image shown on the public website.') }}</p>
                            </div>
                            <span class="badge badge-muted">{{ __('Primary') }}</span>
                        </div>

                        <input type="file" name="cover_image" accept="image/*">

                        @if ($currentCoverImage)
                            <div class="overflow-hidden rounded-2xl border border-white/10">
                                <img src="{{ $currentCoverImage }}" alt="{{ $project->name }}" class="h-48 w-full object-cover">
                            </div>
                        @endif
                    </div>

                    @include('dashboard.partials.image-uploader', [
                        'label' => __('Upload Images'),
                        'existing' => $project?->images,
                        'selectable' => true,
                        'selected' => $project?->cover_image_path,
                        'inputName' => 'main_image',
                    ])
                </section>

                <p class="px-2 text-center text-xs text-slate-500 lg:col-span-2">
                    {{ __('All changes are recorded in the system audit log.') }}
                </p>
        </form>

        @if ($project)
            <div class="grid gap-6 lg:grid-cols-[minmax(0,0.86fr)_minmax(0,1.14fr)]">
                {{-- Visibility & Scheduling --}}
                <section class="app-card app-card--gradient space-y-5">
                    <div class="flex items-center gap-3 border-b border-white/5 pb-4">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500/15 text-amber-400">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </span>
                        <h2 class="text-lg font-semibold text-white">{{ __('Visibility') }}</h2>
                    </div>

                    <div class="space-y-4">
                        <label class="flex items-center justify-between rounded-xl border border-white/5 bg-white/5 p-4 transition hover:bg-white/10">
                            <div>
                                <p class="text-sm font-semibold text-white">{{ __('Featured Project') }}</p>
                                <p class="text-xs text-slate-400">{{ __('Highlight on home page') }}</p>
                            </div>
                            <input type="hidden" name="featured" value="0" form="project-form">
                            <input type="checkbox" name="featured" value="1" @checked(old('featured', $project?->featured)) form="project-form" class="h-5 w-5 rounded border-white/10 bg-slate-900 text-brand-600 focus:ring-brand-500/20">
                        </label>

                        <div>
                            <label for="published_at" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Publication Date') }}</label>
                            <input type="datetime-local" id="published_at" name="published_at" form="project-form" class="app-input" value="{{ old('published_at', $project?->published_at?->format('Y-m-d\TH:i')) }}">
                            @error('published_at') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="sort_order" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Sort Order') }}</label>
                            <input type="number" id="sort_order" name="sort_order" form="project-form" class="app-input" value="{{ old('sort_order', $project?->sort_order ?? 0) }}">
                            @error('sort_order') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                {{-- Units --}}
                <section class="app-card app-card--gradient space-y-5">
                    <div class="flex flex-wrap items-center gap-3 border-b border-white/5 pb-4">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-500/15 text-sky-400">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 21V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v16" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 7h2M13 7h2M9 11h2M13 11h2M9 15h2M13 15h2" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </span>
                        <div class="min-w-0">
                            <h2 class="text-lg font-semibold text-white">{{ __('Units') }}</h2>
                            <p class="text-xs text-slate-400">{{ __('Total project units') }}: {{ $project->units->count() }}</p>
                        </div>
                        <a href="{{ route('dashboard.projects.units.create', $project) }}" class="app-button ml-auto">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round"/></svg>
                            {{ __('+ Unit') }}
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-white/10 text-left text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">
                                    <th class="px-3 py-2">{{ __('Image') }}</th>
                                    <th class="px-3 py-2">{{ __('Unit Number') }}</th>
                                    <th class="px-3 py-2">{{ __('Unit type') }}</th>
                                    <th class="px-3 py-2">{{ __('Floor / Level') }}</th>
                                    <th class="px-3 py-2">{{ __('Area (m²)') }}</th>
                                    <th class="px-3 py-2">{{ __('Current price') }}</th>
                                    <th class="px-3 py-2">{{ __('Status') }}</th>
                                    <th class="px-3 py-2 text-right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($project->units as $unit)
                                    @php
                                        $unitStatus = $unit->status?->value ?? 'available';
                                        $statusLabel = match ($unitStatus) {
                                            'available' => __('Available'),
                                            'reserved' => __('Reserved'),
                                            'sold' => __('Sold'),
                                            default => __('Hidden'),
                                        };
                                        $statusClass = match ($unitStatus) {
                                            'available' => 'badge-success',
                                            'reserved' => 'badge-warning',
                                            'sold' => 'badge-danger',
                                            default => 'badge-muted',
                                        };
                                    @endphp
                                    <tr class="border-b border-white/5 hover:bg-white/5">
                                        <td class="px-3 py-3">
                                            @php
                                                $unitImg = (is_string($unit->thumbnail) && $unit->thumbnail !== '')
                                                    ? $unit->thumbnail
                                                    : collect($unit->images ?? [])->filter(fn ($img) => is_string($img))->first();
                                            @endphp
                                            @if ($unitImg)
                                                <img src="{{ asset('storage/'.$unitImg) }}" alt="{{ $unit->unit_number }}" class="h-12 w-16 rounded-lg border border-white/10 object-cover" loading="lazy">
                                            @else
                                                <div class="flex h-12 w-16 items-center justify-center rounded-lg border border-dashed border-white/10 bg-white/5">
                                                    <svg class="h-4 w-4 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2" stroke-width="1.8"/><circle cx="9" cy="10" r="1.5" stroke-width="1.8"/><path d="M3 16l4.5-4.5 4 4 3.5-3.5 6 6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3">
                                            <p class="font-semibold text-white">#{{ $unit->unit_number }}</p>
                                        </td>
                                        <td class="px-3 py-3 text-slate-300">{{ $unit->unit_type ?: '—' }}</td>
                                        <td class="px-3 py-3 text-slate-300">
                                            {{ $unit->building?->name ?: '—' }}<span class="text-slate-500"> · </span>{{ $unit->floor?->name ?: '—' }}
                                        </td>
                                        <td class="px-3 py-3 text-slate-300">{{ rtrim(rtrim(number_format((float) $unit->area, 2, '.', ','), '0'), '.') ?: 0 }} m²</td>
                                        <td class="px-3 py-3 font-semibold text-white">{{ number_format((float) $unit->current_price) }} {{ __('EGP') }}</td>
                                        <td class="px-3 py-3"><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                                        <td class="px-3 py-3">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('dashboard.projects.units.edit', [$project, $unit]) }}" class="app-button--ghost px-3 py-1.5 text-xs">{{ __('Edit') }}</a>
                                                <form id="delete-unit-{{ $unit->id }}" method="POST" action="{{ route('dashboard.projects.units.destroy', [$project, $unit]) }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    @can('delete', $unit)
                                                        <button type="button" onclick="confirmAction('{{ __('Delete unit') }}', '{{ __('Are you sure you want to delete unit :num? Any related offers, reservations, or deals must be removed first.', ['num' => $unit->unit_number]) }}', () => document.getElementById('delete-unit-{{ $unit->id }}').submit(), '{{ __('Delete') }}')" class="app-button app-button--danger px-3 py-1.5 text-xs">{{ __('Delete') }}</button>
                                                    @endcan
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">
                                            <div class="flex flex-col items-center justify-center gap-3 rounded-2xl border border-dashed border-white/10 py-12 text-center">
                                                <p class="text-sm text-slate-400">{{ __('No units available for this project yet.') }}</p>
                                                <a href="{{ route('dashboard.projects.units.create', $project) }}" class="app-button--ghost">{{ __('+ Unit') }}</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        @endif
    </div>
@endsection
