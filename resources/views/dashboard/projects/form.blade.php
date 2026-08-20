@extends('layouts.dashboard')

@section('content')
    @php
        $errors = $errors ?? new \Illuminate\Support\ViewErrorBag();
        $buildingsData = $project ? $project->buildings->map(function ($b) use ($project) {
            $floors = $b->floors->map(function ($f) use ($project, $b) {
                $units = $f->units->map(function ($u) use ($project) {
                    $status = $u->status?->value ?? 'available';
                    $img = (is_string($u->thumbnail) && $u->thumbnail !== '')
                        ? $u->thumbnail
                        : collect($u->images ?? [])->filter(fn ($i) => is_string($i))->first();

                    return [
                        'id' => (int) $u->id,
                        'unit_number' => (string) $u->unit_number,
                        'unit_type' => (string) ($u->unit_type ?: __('Unit')),
                        'status' => $status,
                        'status_label' => match ($status) {
                            'available' => __('Available'),
                            'reserved' => __('Reserved'),
                            'sold' => __('Sold'),
                            default => __('Hidden'),
                        },
                        'area' => (float) $u->area,
                        'price' => (float) $u->current_price,
                        'price_formatted' => number_format((float) $u->current_price) . ' ' . __('EGP'),
                        'bedrooms' => (int) $u->bedrooms,
                        'bathrooms' => (int) $u->bathrooms,
                        'featured' => (bool) $u->featured,
                        'image_url' => $img ? asset('storage/'.$img) : null,
                        'edit_url' => route('dashboard.projects.units.edit', [$project, $u]),
                        'public_url' => route('public.units.show', $u->unit_number),
                        'delete_id' => 'delete-unit-' . $u->id,
                    ];
                })->values()->all();

                return [
                    'id' => (int) $f->id,
                    'number' => (int) $f->number,
                    'name' => (string) ($f->name ?: ($f->number === 0 ? __('Ground') : __('Floor :number', ['number' => $f->number]))),
                    'units_count' => count($units),
                    'add_unit_url' => route('dashboard.projects.units.create', ['project' => $project->id, 'building_id' => $b->id, 'floor_id' => $f->id]),
                    'units' => $units,
                ];
            })->values()->all();

            $allUnits = collect($floors)->flatMap(fn ($fl) => $fl['units']);
            $totalUnitsCount = $allUnits->count();
            $availableCount = $allUnits->where('status', 'available')->count();
            $reservedCount = $allUnits->where('status', 'reserved')->count();
            $soldCount = $allUnits->where('status', 'sold')->count();

            return [
                'id' => (int) $b->id,
                'name' => (string) $b->name,
                'code' => (string) $b->code,
                'floors_count' => count($floors),
                'total_units' => $totalUnitsCount,
                'available_units' => $availableCount,
                'reserved_units' => $reservedCount,
                'sold_units' => $soldCount,
                'occupancy_rate' => $totalUnitsCount > 0 ? (int) round(($reservedCount + $soldCount) / $totalUnitsCount * 100) : 0,
                'floors' => $floors,
            ];
        })->values()->all() : [];

        $defaultBuildingId = count($buildingsData) > 0 ? $buildingsData[0]['id'] : null;
    @endphp

    <div class="space-y-6" x-data="{ submitting: false }">
        @if (session('status'))
            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="flex items-center gap-3 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12l5 5L20 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span>{{ session('status') }}</span>
                <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-200">×</button>
            </div>
        @endif

        {{-- ══════════════════════════════════════════════════════════════
             HERO HEADER
        ══════════════════════════════════════════════════════════════ --}}
        <section class="dashboard-hero-card p-6 sm:p-8 relative overflow-hidden">
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-brand-500/20 blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-20 left-1/3 h-56 w-56 rounded-full bg-violet-500/15 blur-3xl pointer-events-none"></div>

            <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <span class="badge badge-brand">{{ $project ? __('Project Workspace') : __('New Development') }}</span>
                        @if($project)
                            <span class="badge {{ match($project->status) { 'active' => 'badge-success', 'launching' => 'badge-warning', 'sold' => 'badge-danger', default => 'badge-muted' } }}">
                                {{ __(ucfirst($project->status ?? 'Draft')) }}
                            </span>
                            <span class="badge badge-muted">{{ $project->units->count() }} {{ __('Units total') }}</span>
                        @endif
                    </div>
                    <h1 class="text-2xl font-black tracking-tight text-white sm:text-3xl text-balance">
                        {{ $project ? $project->name : __('Create New Project') }}
                    </h1>
                    <p class="mt-1.5 text-xs text-slate-400 sm:text-sm">
                        {{ __('Manage project specifications, identity, buildings layout, and floor-by-floor unit inventory.') }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="submit" form="project-form" class="app-button" :disabled="submitting" :class="submitting ? 'pointer-events-none opacity-60' : ''">
                        <svg x-show="!submitting" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z" stroke-width="1.8"/><path d="M17 21v-8H7v8M7 3v5h8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <svg x-show="submitting" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 12a9 9 0 1 1-6.219-8.56" stroke-width="2" stroke-linecap="round"/></svg>
                        <span x-text="submitting ? '{{ __('Saving...') }}' : '{{ $project ? __('Update Project') : __('Create Project') }}'"></span>
                    </button>
                    @if($project)
                        <a href="{{ route('public.projects.show', $project->slug) }}" target="_blank" class="app-button--ghost gap-1">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" stroke-width="1.8"/><polyline points="15 3 21 3 21 9" stroke-width="1.8"/><line x1="10" y1="14" x2="21" y2="3" stroke-width="1.8"/></svg>
                            {{ __('Public View') }}
                        </a>
                    @endif
                    <a href="{{ route('dashboard.projects.index') }}" class="app-button--ghost">{{ __('Back to Projects') }}</a>
                </div>
            </div>
        </section>

        {{-- ══════════════════════════════════════════════════════════════
             MAIN PROJECT FORM (IDENTITY, LOCATION, BUILDINGS, IMAGES)
        ══════════════════════════════════════════════════════════════ --}}
        <form id="project-form" method="POST" action="{{ $project ? route('dashboard.projects.update', $project) : route('dashboard.projects.store') }}" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]" data-save-shortcut @submit="submitting = true">
            @csrf
            @if($project) @method('PUT') @endif

            {{-- 1. Project Identity --}}
            <section class="app-card app-card--gradient space-y-5">
                <div class="flex items-center gap-3 border-b border-white/5 pb-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-500/15 text-brand-400">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 2H2v10h10V2zM22 12H12v10h10V12zM12 12H2v10h10V12zM22 2H12v10h10V2z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <div>
                        <h2 class="text-lg font-bold text-white">{{ __('Project Identity') }}</h2>
                        <p class="text-xs text-slate-400">{{ __('Basic information and public branding.') }}</p>
                    </div>
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
                        @error('slug') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="description" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Description') }}</label>
                        <textarea id="description" name="description" class="app-textarea min-h-24">{{ old('description', $project?->description) }}</textarea>
                        @error('description') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="price_per_meter" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Default Price per m² (EGP)') }}</label>
                        <input type="number" step="0.01" min="0" id="price_per_meter" name="price_per_meter" class="app-input" value="{{ old('price_per_meter', $project?->price_per_meter) }}" placeholder="0.00">
                        @error('price_per_meter') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="sort_order" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Display Sort Order') }}</label>
                        <input type="number" id="sort_order" name="sort_order" class="app-input" value="{{ old('sort_order', $project?->sort_order ?? 0) }}">
                    </div>
                </div>
            </section>

            {{-- 2. Location & Media --}}
            <section class="app-card app-card--gradient space-y-5">
                <div class="flex items-center gap-3 border-b border-white/5 pb-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-500/15 text-violet-400">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" stroke-width="1.8"/><circle cx="12" cy="10" r="3" stroke-width="1.8"/></svg>
                    </span>
                    <div>
                        <h2 class="text-lg font-bold text-white">{{ __('Location & Media') }}</h2>
                        <p class="text-xs text-slate-400">{{ __('Address, coordinates, and cover visuals.') }}</p>
                    </div>
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
                    </div>
                    <div>
                        <label for="country" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Country') }}</label>
                        <input type="text" id="country" name="country" class="app-input" value="{{ old('country', $project?->country) }}">
                    </div>

                    {{-- Cover Image --}}
                    <div class="sm:col-span-2 space-y-3 pt-2 border-t border-white/5">
                        <label class="block text-sm font-medium text-slate-300">{{ __('Main Project Cover Image') }}</label>
                        <input type="file" name="cover_image" accept="image/*">
                        @if ($project?->cover_image_path)
                            <div class="overflow-hidden rounded-2xl border border-white/10 mt-2">
                                <img src="{{ asset('storage/'.$project->cover_image_path) }}" alt="{{ $project->name }}" class="h-36 w-full object-cover">
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            {{-- 3. Buildings & Floors Structure Editor --}}
            <section class="app-card app-card--gradient lg:col-span-2" x-data="{ buildings: @js($buildingsJson), open: false }">
                <div class="flex flex-wrap items-center justify-between gap-3" :class="open ? 'border-b border-white/5 pb-4' : 'border-transparent pb-0'">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500/15 text-amber-400">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 21h18M5 21V7a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v14M9 9h.01M15 9h.01M9 13h.01M15 13h.01M9 17h.01M15 17h.01" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </span>
                        <div>
                            <h2 class="text-lg font-bold text-white">{{ __('Building & Floor Structure') }}</h2>
                            <p class="text-xs text-slate-400">{{ __('Configure the towers/buildings in this project and the floor count for each.') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="buildings.push({ id: '', name: '{{ __('Building') }} ' + (buildings.length + 1), floors_count: 5 }); open = true" class="app-button--ghost !py-2 text-xs">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round"/></svg>
                            {{ __('+ Add Building') }}
                        </button>
                        <button type="button" @click="open = !open" class="p-2 rounded-xl bg-white/5 text-slate-400 hover:text-white hover:bg-white/10 transition" :title="open ? '{{ __('Collapse') }}' : '{{ __('Expand') }}'">
                            <svg class="h-4 w-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m6 9 6 6 6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>
                </div>

                <div x-show="open" x-cloak x-transition.opacity.duration.250ms class="pt-5">
                <div class="grid gap-3.5 sm:grid-cols-2 xl:grid-cols-3">
                    <template x-for="(building, index) in buildings" :key="index">
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 space-y-3 relative group">
                            <div class="flex items-center justify-between">
                                <span class="badge badge-brand text-[10px] font-bold" x-text="'{{ __('Building') }} #' + (index + 1)"></span>
                                <button type="button" onclick="confirmAction('{{ __('Remove building') }}', '{{ __('Removing this building will delete it on save if it has no units. Continue?') }}', () => buildings.splice(index, 1), '{{ __('Remove') }}')" class="text-rose-400 hover:text-rose-300 p-1 text-xs">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 6 6 18M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg>
                                </button>
                            </div>

                            <input type="hidden" :name="'buildings[' + index + '][id]'" :value="building.id || ''">

                            <div class="flex items-start gap-3">
                                <div class="flex-1 min-w-0">
                                    <label :for="'building_name_' + index" class="mb-1 block text-xs font-medium text-slate-300">{{ __('Building Name') }}</label>
                                    <input type="text" :id="'building_name_' + index" :name="'buildings[' + index + '][name]'" x-model="building.name" class="app-input w-full text-xs" placeholder="{{ __('e.g. Tower A') }}" required>
                                </div>
                                <div class="w-20 shrink-0">
                                    <label :for="'building_floors_' + index" class="mb-1 block text-xs font-medium text-slate-300">{{ __('Floors') }}</label>
                                    <input type="number" :id="'building_floors_' + index" :name="'buildings[' + index + '][floors_count]'" x-model.number="building.floors_count" class="app-input w-full text-xs text-center" min="1" max="10" required>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                </div>
            </section>
        </form>

        {{-- ══════════════════════════════════════════════════════════════
             4. INTERACTIVE BUILDING & FLOOR MATRIX (VISUAL ELEVATION / STACK)
        ══════════════════════════════════════════════════════════════ --}}
        @if ($project)
            <section class="app-card app-card--gradient space-y-6 pt-6" x-data="{
                buildingsData: @js($buildingsData),
                activeBuildingId: {{ $defaultBuildingId ?? 'null' }},
                expandedFloors: {},
                statusFilter: 'all',
                searchQuery: '',
                viewMode: 'elevation',
                init() {
                    this.setActiveBuilding(this.activeBuildingId);
                },
                setActiveBuilding(id) {
                    this.activeBuildingId = id;
                    const building = this.buildingsData.find(b => Number(b.id) === Number(id));
                    this.expandedFloors = {};
                    if (building?.floors?.[0]) {
                        this.expandedFloors[building.floors[0].id] = true;
                    }
                },
                toggleFloor(id) {
                    this.expandedFloors[id] = ! this.expandedFloors[id];
                },
                expandAll() {
                    this.filteredFloors.forEach(floor => this.expandedFloors[floor.id] = true);
                },
                collapseAll() {
                    this.expandedFloors = {};
                },
                isFloorOpen(floor) {
                    return Boolean(this.expandedFloors[floor.id]) || this.searchQuery.trim() !== '' || this.statusFilter !== 'all';
                },
                get activeBuilding() {
                    return this.buildingsData.find(b => Number(b.id) === Number(this.activeBuildingId)) || this.buildingsData[0] || null;
                },
                get filteredFloors() {
                    if (!this.activeBuilding) return [];
                    return this.activeBuilding.floors.map(floor => {
                        const units = floor.units.filter(u => {
                            const matchStatus = this.statusFilter === 'all' || u.status === this.statusFilter;
                            const matchQuery = !this.searchQuery.trim() || 
                                u.unit_number.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                u.unit_type.toLowerCase().includes(this.searchQuery.toLowerCase());
                            return matchStatus && matchQuery;
                        });
                        return { ...floor, filtered_units: units };
                    });
                },
                get totalFilteredUnits() {
                    return this.filteredFloors.reduce((acc, f) => acc + f.filtered_units.length, 0);
                }
            }">
                {{-- Matrix Header & Controls --}}
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between border-b border-white/5 pb-5">
                    <div class="flex items-center gap-3">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500/25 to-violet-600/20 text-brand-300 ring-1 ring-brand-500/30 shadow-lg shadow-brand-500/10">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 21h18M5 21V7a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v14M9 9h.01M15 9h.01M9 13h.01M15 13h.01M9 17h.01M15 17h.01" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-xl font-black text-white">{{ __('Interactive Building & Floor Matrix') }}</h2>
                                <span class="badge badge-brand text-xs font-bold">{{ $project->units->count() }} {{ __('Units') }}</span>
                            </div>
                            <p class="text-xs text-slate-400 mt-0.5">
                                {{ __('Visual architectural stacking view — select any building and floor to review or modify units directly.') }}
                            </p>
                        </div>
                    </div>

                    {{-- Right-side toolbar --}}
                    <div class="flex flex-wrap items-center gap-2.5">
                        {{-- Search Input --}}
                        <div class="relative min-w-48">
                            <input type="text" x-model="searchQuery" placeholder="{{ __('Search unit / type...') }}" class="app-input text-xs !py-2 !pl-8 !pr-3">
                            <svg class="h-3.5 w-3.5 text-slate-500 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="8" stroke-width="2"/><path d="m21 21-4.3-4.3" stroke-width="2" stroke-linecap="round"/></svg>
                        </div>

                        {{-- Status Filter Pills --}}
                        <div class="flex items-center gap-1 rounded-xl bg-slate-950/60 p-1 border border-white/5">
                            <button type="button" @click="statusFilter = 'all'" :class="statusFilter === 'all' ? 'bg-white/10 text-white font-bold' : 'text-slate-400 hover:text-white'" class="px-2.5 py-1 rounded-lg text-xs transition">
                                {{ __('All') }}
                            </button>
                            <button type="button" @click="statusFilter = 'available'" :class="statusFilter === 'available' ? 'bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/30' : 'text-slate-400 hover:text-emerald-300'" class="px-2.5 py-1 rounded-lg text-xs transition">
                                🟢 {{ __('Available') }}
                            </button>
                            <button type="button" @click="statusFilter = 'reserved'" :class="statusFilter === 'reserved' ? 'bg-amber-500/20 text-amber-300 font-bold border border-amber-500/30' : 'text-slate-400 hover:text-amber-300'" class="px-2.5 py-1 rounded-lg text-xs transition">
                                🟡 {{ __('Reserved') }}
                            </button>
                            <button type="button" @click="statusFilter = 'sold'" :class="statusFilter === 'sold' ? 'bg-rose-500/20 text-rose-300 font-bold border border-rose-500/30' : 'text-slate-400 hover:text-rose-300'" class="px-2.5 py-1 rounded-lg text-xs transition">
                                🔴 {{ __('Sold') }}
                            </button>
                        </div>

                        {{-- View mode switcher --}}
                        <div class="flex items-center gap-1 rounded-xl bg-slate-950/60 p-1 border border-white/5">
                            <button type="button" @click="viewMode = 'elevation'" :class="viewMode === 'elevation' ? 'bg-brand-600 text-white font-bold shadow' : 'text-slate-400 hover:text-white'" class="px-2.5 py-1 rounded-lg text-xs transition flex items-center gap-1.5" title="{{ __('Building Elevation Matrix') }}">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="1.8"/><path d="M3 9h18M3 15h18M9 3v18M15 3v18" stroke-width="1.8"/></svg>
                                {{ __('Elevation View') }}
                            </button>
                            <button type="button" @click="viewMode = 'table'" :class="viewMode === 'table' ? 'bg-brand-600 text-white font-bold shadow' : 'text-slate-400 hover:text-white'" class="px-2.5 py-1 rounded-lg text-xs transition flex items-center gap-1.5" title="{{ __('Table View') }}">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 6h16M4 12h16M4 18h16" stroke-width="2" stroke-linecap="round"/></svg>
                                {{ __('Table') }}
                            </button>
                        </div>

                        <a href="{{ route('dashboard.projects.units.create', $project) }}" class="app-button !py-2 text-xs">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round"/></svg>
                            {{ __('+ New Unit') }}
                        </a>
                    </div>
                </div>

                {{-- Building Selector Tabs --}}
                @if(count($buildingsData) > 0)
                    {{-- Compact selector for phones --}}
                    <label class="block sm:hidden">
                        <span class="sr-only">{{ __('Choose building') }}</span>
                        <select class="app-select" x-model="activeBuildingId" @change="setActiveBuilding($event.target.value)">
                            <template x-for="b in buildingsData" :key="'mobile-building-' + b.id">
                                <option :value="b.id" x-text="b.name + ' · ' + b.total_units + ' {{ __('units') }}'"></option>
                            </template>
                        </select>
                    </label>

                    {{-- Visual selector for tablets and desktop --}}
                    <div class="hidden sm:flex items-center gap-2 overflow-x-auto pb-2 scrollbar-thin">
                        <template x-for="b in buildingsData" :key="b.id">
                            <button type="button" @click="setActiveBuilding(b.id)" :class="Number(activeBuildingId) === Number(b.id) ? 'bg-gradient-to-r from-brand-600 to-violet-600 text-white shadow-lg shadow-brand-600/30 border-brand-500 ring-2 ring-brand-500/30' : 'bg-white/5 text-slate-300 border-white/10 hover:bg-white/10 hover:border-white/20'" class="flex shrink-0 items-center gap-2.5 px-4 py-2.5 rounded-2xl border text-xs font-bold transition-all">
                                <span class="h-2 w-2 rounded-full" :class="b.available_units > 0 ? 'bg-emerald-400 shadow-[0_0_6px_rgba(52,211,153,.8)]' : 'bg-slate-500'"></span>
                                <span x-text="b.name"></span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-black/30" x-text="b.total_units + ' {{ __('units') }}'"></span>
                            </button>
                        </template>
                    </div>
                @endif

                <div class="flex flex-wrap items-center justify-between gap-2 rounded-xl border border-white/5 bg-white/[0.03] px-3 py-2">
                    <span class="text-xs text-slate-400"><span x-text="totalFilteredUnits"></span> {{ __('units visible') }}</span>
                    <div class="flex items-center gap-1.5">
                        <button type="button" @click="expandAll()" class="rounded-lg px-2.5 py-1 text-[11px] font-semibold text-slate-300 transition hover:bg-white/10 hover:text-white">{{ __('Expand all') }}</button>
                        <button type="button" @click="collapseAll()" class="rounded-lg px-2.5 py-1 text-[11px] font-semibold text-slate-400 transition hover:bg-white/10 hover:text-white">{{ __('Collapse all') }}</button>
                    </div>
                </div>

                {{-- Visual Elevation Building Stack --}}
                <div x-show="viewMode === 'elevation'" class="space-y-4">
                    <template x-if="activeBuilding">
                        <div class="space-y-3">
                            {{-- Architectural Roof / Building Crown --}}
                            <div class="rounded-2xl border border-brand-500/30 bg-gradient-to-r from-brand-900/50 via-slate-900/80 to-violet-900/50 p-4 sm:p-5 flex flex-wrap items-center justify-between gap-4 shadow-xl">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-500/20 text-brand-300 ring-1 ring-brand-500/30">
                                        🏢
                                    </span>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-lg font-black text-white" x-text="activeBuilding.name"></h3>
                                            <span class="badge badge-muted text-xs" x-text="activeBuilding.code"></span>
                                        </div>
                                        <p class="text-xs text-slate-400 mt-0.5">
                                            <span x-text="activeBuilding.floors_count + ' {{ __('Floors') }}'"></span> · 
                                            <span class="text-emerald-400 font-bold" x-text="activeBuilding.available_units + ' {{ __('Available') }}'"></span> · 
                                            <span class="text-amber-400" x-text="activeBuilding.reserved_units + ' {{ __('Reserved') }}'"></span> · 
                                            <span class="text-rose-400" x-text="activeBuilding.sold_units + ' {{ __('Sold') }}'"></span>
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    <div class="text-end hidden sm:block">
                                        <p class="text-[11px] text-slate-400">{{ __('Occupancy') }}</p>
                                        <p class="text-sm font-black text-white" x-text="activeBuilding.occupancy_rate + '%'"></p>
                                    </div>
                                    <div class="w-24 h-2 rounded-full bg-white/10 overflow-hidden hidden sm:block">
                                        <div class="h-full bg-gradient-to-r from-brand-500 to-emerald-400 transition-all duration-700" :style="'width: ' + activeBuilding.occupancy_rate + '%'"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Floors Vertically Stacked (Top Floor down to Ground 0) --}}
                            <div class="space-y-3">
                                <template x-for="floor in filteredFloors" :key="floor.id">
                                    <div class="rounded-2xl border border-white/10 bg-slate-950/50 p-3 sm:p-4 transition-all hover:border-white/20">
                                        {{-- Collapsible Floor Header --}}
                                        <div class="flex items-center gap-2 pb-2.5 border-b border-white/5">
                                            <button type="button" @click="toggleFloor(floor.id)" class="flex min-w-0 flex-1 items-center gap-2.5 text-start rounded-xl p-1.5 hover:bg-white/5 transition">
                                                <span class="flex h-8 min-w-8 px-2 items-center justify-center rounded-lg bg-white/10 text-slate-200 text-xs font-black ring-1 ring-white/10" x-text="floor.name"></span>
                                                <span class="min-w-0 flex-1">
                                                    <span class="block text-xs font-semibold text-white" x-text="floor.units_count + ' {{ __('units on this level') }}'"></span>
                                                    <span class="block text-[10px] text-slate-500" x-show="floor.filtered_units.length !== floor.units_count" x-text="floor.filtered_units.length + ' {{ __('matching filter') }}'"></span>
                                                </span>
                                                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform duration-200" :class="isFloorOpen(floor) ? 'rotate-180 text-brand-300' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m6 9 6 6 6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </button>

                                            <a :href="floor.add_unit_url" class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-brand-500/15 px-2.5 py-1.5 text-xs font-bold text-brand-300 transition hover:bg-brand-500/25" title="{{ __('+ Add Unit on this Floor') }}">
                                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round"/></svg>
                                                <span class="hidden sm:inline">{{ __('Add Unit') }}</span>
                                            </a>
                                        </div>

                                        {{-- Units Grid on this Floor --}}
                                        <div x-show="isFloorOpen(floor)" x-cloak x-transition.opacity.duration.200ms class="grid gap-2 grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 pt-3">
                                            <template x-for="unit in floor.filtered_units" :key="unit.id">
                                                <div class="rounded-xl border p-2 sm:p-3 transition-all duration-300 hover:scale-[1.02] relative group flex flex-col justify-between min-w-0"
                                                     :class="{
                                                         'border-emerald-500/30 bg-emerald-500/5 hover:border-emerald-500/60 shadow-lg shadow-emerald-500/5': unit.status === 'available',
                                                         'border-amber-500/30 bg-amber-500/5 hover:border-amber-500/60 shadow-lg shadow-amber-500/5': unit.status === 'reserved',
                                                         'border-rose-500/30 bg-rose-500/5 hover:border-rose-500/60 shadow-lg shadow-rose-500/5': unit.status === 'sold',
                                                         'border-slate-700 bg-white/5': unit.status !== 'available' && unit.status !== 'reserved' && unit.status !== 'sold'
                                                     }">
                                                    <div class="space-y-2">
                                                        <div class="flex items-start justify-between gap-2">
                                                            <div class="min-w-0">
                                                                <h4 class="text-xs sm:text-sm font-black text-white truncate" x-text="'#' + unit.unit_number"></h4>
                                                                <p class="text-[10px] sm:text-[11px] text-slate-400 truncate" x-text="unit.unit_type"></p>
                                                            </div>
                                                            <span class="badge text-[10px] !px-2 !py-0.5 font-bold"
                                                                  :class="{
                                                                      'badge-success': unit.status === 'available',
                                                                      'badge-warning': unit.status === 'reserved',
                                                                      'badge-danger': unit.status === 'sold',
                                                                      'badge-muted': unit.status !== 'available' && unit.status !== 'reserved' && unit.status !== 'sold'
                                                                  }"
                                                                  x-text="unit.status_label">
                                                            </span>
                                                        </div>

                                                        {{-- Unit specs chips --}}
                                                        <div class="flex flex-wrap items-center gap-1 sm:gap-2 text-[10px] sm:text-[11px] text-slate-300 py-0.5 sm:py-1">
                                                            <span class="font-bold tabular-nums" x-text="unit.area + ' م²'"></span>
                                                            <template x-if="unit.bedrooms > 0">
                                                                <span>· <span x-text="unit.bedrooms + ' غرف'"></span></span>
                                                            </template>
                                                        </div>

                                                        {{-- Unit Price --}}
                                                        <div class="pt-0.5 sm:pt-1 border-t border-white/5 flex items-baseline justify-between">
                                                            <span class="text-[10px] uppercase text-slate-400">{{ __('Price') }}</span>
                                                            <span class="text-[11px] sm:text-xs font-black text-emerald-400 tabular-nums" x-text="unit.price_formatted"></span>
                                                        </div>
                                                    </div>

                                                    {{-- Quick Actions footer --}}
                                                    <div class="mt-1.5 pt-1.5 border-t border-white/5 flex items-center justify-end gap-1">
                                                        <a :href="unit.public_url" target="_blank" class="p-1 sm:p-1.5 rounded-lg bg-white/5 text-slate-400 hover:text-white hover:bg-white/10 transition text-xs" title="{{ __('View') }}">
                                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke-width="1.8"/></svg>
                                                        </a>
                                                        <a :href="unit.edit_url" class="px-2 py-1 sm:px-2.5 sm:py-1.5 rounded-lg bg-brand-600 text-white hover:bg-brand-500 font-bold text-[10px] sm:text-[11px] transition shadow flex items-center gap-1">
                                                            <svg class="h-3 w-3 sm:h-3.5 sm:w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke-width="1.8"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke-width="1.8"/></svg>
                                                            {{ __('Edit') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </template>

                                            <template x-if="floor.filtered_units.length === 0">
                                                <div class="col-span-full py-6 text-center text-xs text-slate-500 border border-dashed border-white/10 rounded-xl bg-white/[0.02]">
                                                    {{ __('No matching units on this floor.') }}
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            {{-- Architectural Foundation Base --}}
                            <div class="h-3 rounded-xl bg-gradient-to-r from-slate-800 via-brand-950 to-slate-800 border-t border-white/10 shadow-inner"></div>
                        </div>
                    </template>
                </div>

                {{-- Table View --}}
                <div x-show="viewMode === 'table'" x-cloak class="overflow-x-auto rounded-2xl border border-white/10 bg-white/5">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-white/10 text-left text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400 bg-slate-950/40">
                                <th class="px-4 py-3">{{ __('Image') }}</th>
                                <th class="px-4 py-3">{{ __('Unit Number') }}</th>
                                <th class="px-4 py-3">{{ __('Building & Floor') }}</th>
                                <th class="px-4 py-3">{{ __('Unit type') }}</th>
                                <th class="px-4 py-3">{{ __('Area (m²)') }}</th>
                                <th class="px-4 py-3">{{ __('Current price') }}</th>
                                <th class="px-4 py-3">{{ __('Status') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($project->units as $unit)
                                @php
                                    $unitStatus = $uStatus = $unit->status?->value ?? 'available';
                                    $statusLabel = match ($uStatus) {
                                        'available' => __('Available'),
                                        'reserved' => __('Reserved'),
                                        'sold' => __('Sold'),
                                        default => __('Hidden'),
                                    };
                                    $statusClass = match ($uStatus) {
                                        'available' => 'badge-success',
                                        'reserved' => 'badge-warning',
                                        'sold' => 'badge-danger',
                                        default => 'badge-muted',
                                    };
                                    $unitImg = (is_string($unit->thumbnail) && $unit->thumbnail !== '')
                                        ? $unit->thumbnail
                                        : collect($unit->images ?? [])->filter(fn ($img) => is_string($img))->first();
                                @endphp
                                <tr class="border-b border-white/5 hover:bg-white/5 transition">
                                    <td class="px-4 py-3">
                                        @if ($unitImg)
                                            <img src="{{ asset('storage/'.$unitImg) }}" alt="{{ $unit->unit_number }}" class="h-10 w-14 rounded-lg border border-white/10 object-cover" loading="lazy">
                                        @else
                                            <div class="flex h-10 w-14 items-center justify-center rounded-lg border border-dashed border-white/10 bg-white/5 text-slate-500">
                                                🏢
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 font-bold text-white">#{{ $unit->unit_number }}</td>
                                    <td class="px-4 py-3 text-slate-300">
                                        {{ $unit->building?->name ?: '—' }} <span class="text-slate-500">·</span> {{ $unit->floor?->name ?: '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-300">{{ $unit->unit_type ?: '—' }}</td>
                                    <td class="px-4 py-3 text-slate-300 tabular-nums">{{ number_format((float) $unit->area, 1) }} م²</td>
                                    <td class="px-4 py-3 font-bold text-emerald-400 tabular-nums">{{ number_format((float) $unit->current_price) }} {{ __('EGP') }}</td>
                                    <td class="px-4 py-3"><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('public.units.show', $unit->unit_number) }}" target="_blank" class="p-1.5 rounded-lg bg-white/5 text-slate-400 hover:text-white hover:bg-white/10 transition text-xs" title="{{ __('View') }}">
                                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke-width="1.8"/></svg>
                                            </a>
                                            <a href="{{ route('dashboard.projects.units.edit', [$project, $unit]) }}" class="app-button !py-1.5 !px-3 text-xs">{{ __('Edit') }}</a>
                                            <form id="delete-unit-{{ $unit->id }}" method="POST" action="{{ route('dashboard.projects.units.destroy', [$project, $unit]) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                @can('delete', $unit)
                                                    <button type="button" onclick="confirmAction('{{ __('Delete unit') }}', '{{ __('Are you sure you want to delete unit :num?', ['num' => $unit->unit_number]) }}', () => document.getElementById('delete-unit-{{ $unit->id }}').submit(), '{{ __('Delete') }}')" class="app-button app-button--danger !py-1.5 !px-2.5 text-xs">{{ __('Delete') }}</button>
                                                @endcan
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-8 text-center text-slate-500">
                                        {{ __('No units created yet.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        @endif
    </div>
@endsection
