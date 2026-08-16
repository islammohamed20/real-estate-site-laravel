@extends('layouts.dashboard')

@section('content')
    <div class="space-y-6">
        <section class="dashboard-hero-card p-6 sm:p-8">
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-brand-500/20 blur-3xl"></div>
            
            <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="mobile-section-title">{{ $unit ? __('Inventory Management') : __('New inventory item') }}</p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">
                        {{ $unit ? __('Unit :num', ['num' => $unit->unit_number]) : __('Create unit') }}
                    </h1>
                    <p class="mt-2 text-sm text-slate-400">
                        {{ __('Define unit specifications, pricing, and status within :project.', ['project' => $project->name]) }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('dashboard.projects.index') }}" class="app-button--ghost">{{ __('Cancel') }}</a>
                    <button type="submit" form="unit-form" class="app-button">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z" stroke-width="1.8"/><path d="M17 21v-8H7v8M7 3v5h8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        {{ $unit ? __('Update Unit') : __('Create Unit') }}
                    </button>
                    @if ($unit && auth()->user()?->can('delete', $unit))
                        <button type="button" onclick="confirmAction('{{ __('Delete unit') }}', '{{ __('Are you sure you want to delete unit :num? Any related offers, reservations, or deals must be removed first.', ['num' => $unit->unit_number]) }}', () => document.getElementById('delete-unit-form-{{ $unit->id }}').submit(), '{{ __('Delete') }}')" class="app-button app-button--danger">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                            {{ __('Delete Unit') }}
                        </button>
                    @endif
                </div>
            </div>
        </section>

        <form id="unit-form" method="POST" action="{{ $unit ? route('dashboard.projects.units.update', [$project, $unit]) : route('dashboard.projects.units.store', $project) }}" enctype="multipart/form-data" class="space-y-6" data-save-shortcut>
            @csrf
            @if($unit) @method('PUT') @endif

            <div class="space-y-6">
                <div class="grid gap-6 lg:grid-cols-3 lg:items-start">
                    {{-- Unit Specifications --}}
                    <section class="app-card app-card--gradient h-full space-y-4 order-1">
                        <div class="flex items-center gap-3 border-b border-white/5 pb-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-500/15 text-brand-400">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><polyline points="9 22 9 12 15 12 15 22" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            <h2 class="text-lg font-semibold text-white">{{ __('Unit Specifications') }}</h2>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label for="floor_id" class="mb-1.5 block text-sm font-medium text-slate-300">{{ __('Floor / Level') }} <span class="text-rose-400">*</span></label>
                                <select id="floor_id" name="floor_id" class="app-select" required>
                                    @foreach($floors as $floor)
                                        <option value="{{ $floor->id }}" @selected(old('floor_id', $unit?->floor_id) == $floor->id)>
                                            {{ $floor->building?->name }} — {{ $floor->name }} (#{{ $floor->number }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('floor_id') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="unit_number" class="mb-1.5 block text-sm font-medium text-slate-300">{{ __('Unit Number') }} <span class="text-rose-400">*</span></label>
                                <input type="text" id="unit_number" name="unit_number" class="app-input" value="{{ old('unit_number', $unit?->unit_number) }}" required placeholder="e.g. 101">
                                @error('unit_number') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="unit_type" class="mb-1.5 block text-sm font-medium text-slate-300">{{ __('Unit Type') }}</label>
                                <select id="unit_type" name="unit_type" class="app-select">
                                    @php
                                        $currentUnitType = old('unit_type', $unit?->unit_type);
                                    @endphp
                                    @if ($currentUnitType && ! array_key_exists($currentUnitType, $unitTypes))
                                        <option value="{{ $currentUnitType }}" selected>{{ $currentUnitType }}</option>
                                    @endif
                                    @foreach ($unitTypes as $value => $label)
                                        <option value="{{ $value }}" @selected($currentUnitType === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('unit_type') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-3 gap-3 sm:col-span-2">
                                <div>
                                    <label for="bedrooms" class="mb-1.5 block text-sm font-medium text-slate-300">{{ __('Bedrooms') }}</label>
                                    <input type="number" id="bedrooms" name="bedrooms" class="app-input" value="{{ old('bedrooms', $unit?->bedrooms ?? 0) }}" min="0">
                                </div>
                                <div>
                                    <label for="bathrooms" class="mb-1.5 block text-sm font-medium text-slate-300">{{ __('Bathrooms') }}</label>
                                    <input type="number" id="bathrooms" name="bathrooms" class="app-input" value="{{ old('bathrooms', $unit?->bathrooms ?? 0) }}" min="0">
                                </div>
                                <div>
                                    <label for="terrace_count" class="mb-1.5 block text-sm font-medium text-slate-300">{{ __('Terrace Count') }}</label>
                                    <input type="number" id="terrace_count" name="terrace_count" class="app-input" value="{{ old('terrace_count', $unit?->terrace_count ?? 0) }}" min="0">
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Status & Visibility --}}
                    <section class="app-card app-card--gradient h-full space-y-4 order-3">
                        <div class="flex items-center gap-3 border-b border-white/5 pb-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-500/15 text-amber-400">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 11l3 3L22 4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" stroke-width="1.8" stroke-linecap="round"/></svg>
                            </span>
                            <h2 class="text-lg font-semibold text-white">{{ __('Status') }}</h2>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <label for="status" class="mb-1.5 block text-sm font-medium text-slate-300">{{ __('Current Status') }} <span class="text-rose-400">*</span></label>
                                <select id="status" name="status" class="app-select" required>
                                    @foreach($statuses as $value => $label)
                                        <option value="{{ $value }}" @selected(old('status', $unit?->status?->value ?? 'available') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <label class="flex items-center justify-between gap-3 rounded-xl border border-white/5 bg-white/5 p-3.5 transition hover:bg-white/10">
                                <div>
                                    <p class="text-sm font-semibold text-white">{{ __('Featured Unit') }}</p>
                                    <p class="text-xs text-slate-400">{{ __('Priority in listings') }}</p>
                                </div>
                                <input type="hidden" name="featured" value="0">
                                <input type="checkbox" name="featured" value="1" @checked(old('featured', $unit?->featured)) class="h-5 w-5 shrink-0 rounded border-white/10 bg-slate-900 text-brand-600 focus:ring-brand-500/20">
                            </label>

                            <label class="flex items-center justify-between gap-3 rounded-xl border border-white/5 bg-white/5 p-3.5 transition hover:bg-white/10">
                                <div>
                                    <p class="text-sm font-semibold text-white">{{ __('Hide from Website') }}</p>
                                    <p class="text-xs text-slate-400">{{ __('Internal management only') }}</p>
                                </div>
                                <input type="hidden" name="hidden_from_website" value="0">
                                <input type="checkbox" name="hidden_from_website" value="1" @checked(old('hidden_from_website', $unit?->hidden_from_website)) class="h-5 w-5 shrink-0 rounded border-white/10 bg-slate-900 text-brand-600 focus:ring-brand-500/20">
                            </label>

                            <div>
                                <label for="sort_order" class="mb-1.5 block text-sm font-medium text-slate-300">{{ __('Sort Order') }}</label>
                                <input type="number" id="sort_order" name="sort_order" class="app-input" value="{{ old('sort_order', $unit?->sort_order ?? 0) }}">
                            </div>
                        </div>
                    </section>

                    {{-- Dimensions & Pricing --}}
                    <section class="app-card app-card--gradient h-full space-y-5 order-2">
                        <div class="flex items-center gap-3 border-b border-white/5 pb-4">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-500/15 text-violet-400">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="1.8"/><path d="M14.5 9a3.5 3.5 0 0 0-5 0M14.5 15a3.5 3.5 0 0 1-5 0M12 7v10" stroke-width="1.8" stroke-linecap="round"/></svg>
                            </span>
                            <h2 class="text-lg font-semibold text-white">{{ __('Dimensions & Pricing') }}</h2>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="area" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Primary Area (m²)') }}</label>
                                <input type="number" step="0.01" id="area" name="area" class="app-input" value="{{ old('area', $unit?->area ?? 0) }}" min="0">
                            </div>
                            <div>
                                <label for="price_per_meter" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Price per m²') }}</label>
                                <input type="number" step="0.01" id="price_per_meter" name="price_per_meter" class="app-input" value="{{ old('price_per_meter', $unit?->price_per_meter ?? 0) }}" min="0">
                            </div>

                            <div class="grid grid-cols-2 gap-4 sm:col-span-2">
                                <div>
                                    <label for="garden_area" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Garden Area (m²)') }}</label>
                                    <input type="number" step="0.01" id="garden_area" name="garden_area" class="app-input" value="{{ old('garden_area', $unit?->garden_area ?? 0) }}" min="0">
                                </div>
                                <div>
                                    <label for="garden_price" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Garden Total Price') }}</label>
                                    <input type="number" step="0.01" id="garden_price" name="garden_price" class="app-input" value="{{ old('garden_price', $unit?->garden_price ?? 0) }}" min="0">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 sm:col-span-2">
                                <div>
                                    <label for="roof_area" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Roof Area (m²)') }}</label>
                                    <input type="number" step="0.01" id="roof_area" name="roof_area" class="app-input" value="{{ old('roof_area', $unit?->roof_area ?? 0) }}" min="0">
                                </div>
                                <div>
                                    <label for="roof_price" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Roof Total Price') }}</label>
                                    <input type="number" step="0.01" id="roof_price" name="roof_price" class="app-input" value="{{ old('roof_price', $unit?->roof_price ?? 0) }}" min="0">
                                </div>
                            </div>

                            <div>
                                <label for="balcony_area" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Terrace Area (m²)') }}</label>
                                <input type="number" step="0.01" id="balcony_area" name="balcony_area" class="app-input" value="{{ old('balcony_area', $unit?->balcony_area ?? 0) }}" min="0">
                            </div>
                            <div>
                                <label for="excellence_percent" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Excellence %') }}</label>
                                <input type="number" step="0.1" min="0" max="100" id="excellence_percent" name="excellence_percent" class="app-input" value="{{ old('excellence_percent', $unit?->excellence_percent ?? 0) }}">
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5 rounded-xl border border-brand-500/20 bg-brand-500/10 p-3">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 8v4m0 4h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <p class="text-xs leading-relaxed text-slate-400">{{ __('Unit pricing affects financial reports and installment projections.') }}</p>
                        </div>
                    </section>

                    {{-- Finishing & Additional Services (selectable from the global list) --}}
                    <section class="app-card app-card--gradient h-full space-y-5 order-4">
                        <div class="flex items-center gap-3 border-b border-white/5 pb-4">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-400">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 11l3 3L22 4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M17 21v-8H7v8M7 3v5h8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            <div>
                                <h2 class="text-lg font-semibold text-white">{{ __('مواصفات التشطيب والخدمات الإضافية') }}</h2>
                                <p class="text-xs text-slate-400">{{ __('Select the finishing specs and additional services shown on the public unit page.') }}</p>
                            </div>
                        </div>

                        @php
                            $unitFeatureTitles = collect(old('features', $unit?->features ?? []))->filter(fn ($title) => is_string($title))->all();
                            $availableTitles = collect($availableFeatures ?? [])->pluck('title')->filter()->all();
                            $customFeatureTitles = array_values(array_diff($unitFeatureTitles, $availableTitles));
                        @endphp

                        <div x-data="{
                            customFeatures: {{ Js::from($customFeatureTitles) }},
                            newFeature: '',
                            addFeature() {
                                const title = this.newFeature.trim();
                                if (! title) return;
                                if (! this.customFeatures.includes(title)) {
                                    this.customFeatures.push(title);
                                }
                                this.newFeature = '';
                            },
                            removeFeature(index) {
                                this.customFeatures.splice(index, 1);
                            },
                        }">
                            @if (isset($availableFeatures) && count($availableFeatures) > 0)
                                <div class="grid gap-3 sm:grid-cols-2">
                                    @foreach ($availableFeatures as $feature)
                                        @php
                                            $title = $feature['title'] ?? '';
                                        @endphp
                                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-white/10 bg-white/5 p-3.5 transition hover:border-brand-500/30 hover:bg-white/10">
                                            <input type="checkbox" name="features[]" value="{{ $title }}" @checked(in_array($title, $unitFeatureTitles, true)) class="mt-0.5 h-4 w-4 rounded border-white/10 bg-slate-900 text-brand-600 focus:ring-brand-500/20">
                                            <span class="min-w-0">
                                                <span class="flex items-center gap-2 text-sm font-semibold text-white">
                                                    <span>{!! \App\Support\Features::iconSvg($feature['icon'] ?? 'sparkles') !!}</span>
                                                    <span>{{ $title }}</span>
                                                </span>
                                                <span class="mt-0.5 block text-xs leading-relaxed text-slate-400">{{ $feature['desc'] ?? '' }}</span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                <p class="text-xs text-slate-500">{{ __('The list of features is managed in Settings → Company Info → Available Features.') }}</p>
                            @else
                                <p class="text-sm text-slate-400">{{ __('No available features defined yet. Add them in Settings → Company Info.') }}</p>
                            @endif

                            {{-- Custom / other specifications added by the admin --}}
                            <div class="space-y-3 rounded-2xl border border-dashed border-white/15 bg-white/5 p-4">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-sm font-semibold text-white">{{ __('Other specifications') }}</p>
                                    <span class="text-[11px] text-slate-500">{{ __('Optional') }}</span>
                                </div>

                                <template x-for="(feat, i) in customFeatures" :key="i">
                                    <div class="flex items-center gap-2 rounded-xl border border-white/10 bg-slate-950/40 px-3 py-2">
                                        <input type="hidden" name="features[]" :value="feat">
                                        <span class="flex min-w-0 flex-1 items-center gap-2 truncate text-sm text-slate-200">
                                            <span class="shrink-0 text-brand-400">{!! \App\Support\Features::iconSvg('sparkles') !!}</span>
                                            <span class="truncate" x-text="feat"></span>
                                        </span>
                                        <button type="button" @click="removeFeature(i)" class="shrink-0 rounded-lg p-1 text-slate-400 transition hover:bg-rose-500/10 hover:text-rose-300" aria-label="{{ __('Remove') }}">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 6 6 18M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg>
                                        </button>
                                    </div>
                                </template>

                                <div class="flex gap-2">
                                    <input type="text" x-model="newFeature" class="app-input" placeholder="{{ __('Add another specification…') }}" @keydown.enter.prevent="addFeature()">
                                    <button type="button" @click="addFeature()" class="app-button shrink-0 gap-1.5">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round"/></svg>
                                        {{ __('Add') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Photos & Map --}}
                    <section class="app-card app-card--gradient h-full space-y-4 order-5 lg:col-span-2">
                    <div class="flex items-center gap-3 border-b border-white/5 pb-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-sky-500/15 text-sky-400">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke-width="1.8"/><polyline points="9 22 9 12 15 12 15 22" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <h2 class="text-lg font-semibold text-white">{{ __('Photos & Map') }}</h2>
                    </div>

                    <div class="space-y-3" x-data="{ mapLat: '{{ old('map_lat', $unit?->map_lat) }}', mapLng: '{{ old('map_lng', $unit?->map_lng) }}' }">
                        <div>
                            @include('dashboard.partials.image-uploader', [
                                'compact' => true,
                                'label' => __('Unit Images'),
                                'existing' => $unit?->images,
                                'selectable' => true,
                                'selected' => $unit?->thumbnail,
                                'inputName' => 'main_image',
                            ])
                        </div>

                    {{-- Floor plan (horizontal projection) --}}
                    @php
                        $floorPlanPath = $unit?->floor_plan_path;
                        $floorPlanExt = $floorPlanPath ? strtolower(pathinfo($floorPlanPath, PATHINFO_EXTENSION)) : null;
                        $floorPlanIsImage = in_array($floorPlanExt, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'avif'], true);
                    @endphp
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <div class="mb-2 flex items-center gap-2">
                            <svg class="h-5 w-5 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 4h16v16H4z" stroke-width="1.8"/><path d="M4 12h16M12 4v16M7 12v8M17 12v8" stroke-width="1.8"/></svg>
                            <h3 class="text-sm font-semibold text-white">{{ __('Floor Plan') }}</h3>
                        </div>
                        <p class="mb-3 text-xs text-slate-500">{{ __('Upload the unit floor plan — an image, PDF, DWG or DXF file. It is shown to visitors in the project page preview.') }}</p>
                        <input type="file" id="floor_plan" name="floor_plan" accept="image/*,.pdf,.dwg,.dxf,.dgn,.rvt,.skp,.stp,.step" class="app-input file:mr-3 file:rounded-lg file:border-0 file:bg-brand-600 file:px-3 file:py-1.5 file:text-xs file:text-white hover:file:bg-brand-500">
                        @error('floor_plan') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror

                        @if ($floorPlanPath)
                            <div class="mt-3 overflow-hidden rounded-2xl border border-white/10 bg-slate-950/40">
                                @if ($floorPlanIsImage)
                                    <img src="{{ asset('storage/'.$floorPlanPath) }}" alt="{{ __('Floor Plan') }}" class="max-h-56 w-full object-contain">
                                @else
                                    <div class="flex items-center gap-3 p-4">
                                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-500/15 text-brand-400">
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-width="1.8"/><path d="M14 2v6h6" stroke-width="1.8"/><path d="M9 15h6M9 11h2" stroke-width="1.8" stroke-linecap="round"/></svg>
                                        </span>
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-semibold text-white">{{ basename($floorPlanPath) }}</p>
                                            <p class="text-xs text-slate-500">{{ strtoupper($floorPlanExt ?? '') }} — {{ __('Floor Plan') }}</p>
                                        </div>
                                        <a href="{{ asset('storage/'.$floorPlanPath) }}" target="_blank" rel="noopener noreferrer" class="app-button--ghost ms-auto shrink-0 px-3 py-1.5 text-xs">{{ __('Open') }}</a>
                                    </div>
                                @endif
                            </div>
                            <label class="mt-3 flex cursor-pointer items-center gap-2 text-xs text-slate-400 transition hover:text-rose-300">
                                <input type="checkbox" name="remove_floor_plan" value="1" class="h-4 w-4 rounded border-white/10 bg-slate-900 text-rose-600 focus:ring-rose-500/20">
                                {{ __('Remove floor plan') }}
                            </label>
                        @endif
                    </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="map_lat" class="mb-1.5 block text-sm font-medium text-slate-300">{{ __('Latitude') }}</label>
                                <input type="text" id="map_lat" name="map_lat" class="app-input" x-model="mapLat" placeholder="e.g. 30.0444">
                                @error('map_lat') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="map_lng" class="mb-1.5 block text-sm font-medium text-slate-300">{{ __('Longitude') }}</label>
                                <input type="text" id="map_lng" name="map_lng" class="app-input" x-model="mapLng" placeholder="e.g. 31.2357">
                                @error('map_lng') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <p class="text-xs text-slate-500">{{ __('Google Maps location') }} — {{ __('right-click on Google Maps to copy coordinates') }}</p>
                        <div class="overflow-hidden rounded-2xl border border-white/10">
                            <iframe :src="'https://www.google.com/maps?q=' + (mapLat || '30.0444') + ',' + (mapLng || '31.2357') + '&z=15&output=embed'" class="h-40 w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                    </section>
                </div>
            </div>

            @error('delete')
                <p class="text-sm text-rose-400">{{ $message }}</p>
            @enderror
        </form>

        @if ($unit && auth()->user()?->can('delete', $unit))
            <form id="delete-unit-form-{{ $unit->id }}" method="POST" action="{{ route('dashboard.projects.units.destroy', [$project, $unit]) }}">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>
@endsection
