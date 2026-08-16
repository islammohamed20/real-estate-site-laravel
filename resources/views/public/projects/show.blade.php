@extends('layouts.public')

@section('content')
    <div class="animate-fade-up pt-6">
        <a href="{{ route('public.projects.index') }}" class="link-arrow mb-6 inline-flex">
            <svg class="h-4 w-4 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                <path d="M19 12H5M11 6l-6 6 6 6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            {{ __('All projects') }}
        </a>

        <section class="app-card space-y-5 p-6 sm:p-8">
            <div class="flex flex-wrap items-center gap-2">
                <span class="badge badge-brand">{{ __($project->status) }}</span>
                @if ($project->featured)
                    <span class="badge badge-violet">{{ __('★ Featured') }}</span>
                @endif
                @if ($project->published_at)
                    <span class="badge badge-muted">{{ __('Published :date', ['date' => $project->published_at->format('M j, Y')]) }}</span>
                @endif
            </div>

            <div>
                <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ $project->name }}</h1>
                <p class="mt-3 flex items-center gap-1.5 text-sm text-slate-400">
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path d="M12 21s-7-5.5-7-11a7 7 0 1 1 14 0c0 5.5-7 11-7 11Z" stroke-width="1.8"/>
                        <circle cx="12" cy="10" r="2.5" stroke-width="1.8"/>
                    </svg>
                    {{ $project->location ?? __('Location pending') }}
                </p>
            </div>

            @if ($project->description)
                <p class="max-w-2xl text-sm leading-7 text-slate-400 sm:text-base">{{ $project->description }}</p>
            @endif

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="touch-card">
                    <p class="touch-card__label">{{ __('City') }}</p>
                    <p class="mt-2 font-semibold text-white">{{ $project->city ?? __('—') }}</p>
                </div>
                <div class="touch-card">
                    <p class="touch-card__label">{{ __('Country') }}</p>
                    <p class="mt-2 font-semibold text-white">{{ $project->country ?? __('—') }}</p>
                </div>
                <div class="touch-card">
                    <p class="touch-card__label">{{ __('Phases') }}</p>
                    <p class="mt-2 font-semibold text-white">{{ $project->phases->count() }}</p>
                </div>
                <div class="touch-card">
                    <p class="touch-card__label">{{ __('Units') }}</p>
                    <p class="mt-2 font-semibold text-white">{{ $project->units_count ?? $project->units->count() }}</p>
                </div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('installments.index', ['project_id' => $project->id]) }}" class="app-button flex-1">{{ __('Plan a purchase') }}</a>
                <a href="#units" class="app-button--ghost flex-1">{{ __('View units') }}</a>
            </div>
        </section>

        @php
            $projectCoverImage = $project->cover_image_path ? asset('storage/'.$project->cover_image_path) : null;
            $galleryImages = collect([$projectCoverImage])
                ->merge(collect($project->images ?? [])->filter()->map(fn ($img) => asset('storage/'.$img)))
                ->filter()
                ->unique()
                ->values()
                ->all();
            $hasMap = $project->map_lat && $project->map_lng;
        @endphp

        @if ($galleryImages !== [] || $hasMap)
            <section class="mt-9 grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,0.92fr)]">
                @if ($galleryImages !== [])
                    <div class="app-card p-6 sm:p-8">
                        <div class="mb-5">
                            <p class="mobile-section-title">{{ __('معرض الصور') }}</p>
                            <h2 class="mt-2 text-2xl font-bold tracking-tight text-white">{{ __('صور المشروع') }}</h2>
                        </div>
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                            @foreach ($galleryImages as $img)
                                <a href="{{ $img }}" target="_blank" class="group relative block aspect-[4/3] overflow-hidden rounded-2xl border border-white/10">
                                    <img src="{{ $img }}" alt="{{ $project->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-110">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($hasMap)
                    <div class="app-card space-y-4 p-6 sm:p-8">
                        <div>
                            <p class="mobile-section-title">{{ __('الموقع على الخريطة') }}</p>
                            <h2 class="mt-1 text-2xl font-bold text-white">{{ __('موقع المشروع على خرائط جوجل') }}</h2>
                        </div>
                        <div class="overflow-hidden rounded-2xl border border-white/10">
                            <iframe
                                src="https://www.google.com/maps?q={{ $project->map_lat }},{{ $project->map_lng }}&z=16&output=embed"
                                class="h-72 w-full"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                allowfullscreen
                            ></iframe>
                        </div>
                        <a href="https://www.google.com/maps?q={{ $project->map_lat }},{{ $project->map_lng }}" target="_blank" rel="noopener noreferrer" class="link-arrow text-xs">
                            {{ __('فتح في خرائط جوجل') }} ←
                        </a>
                    </div>
                @endif
            </section>
        @endif

        @if ($project->buildings->isNotEmpty() || $project->units->isNotEmpty())
            @if ($project->buildings->isNotEmpty())
            @php
                $buildingExplorerData = $project->buildings->map(fn ($building) => [
                    'id' => $building->id,
                    'name' => $building->name,
                    'code' => $building->code,
                    'available' => $building->floors->flatMap->units->where('status', 'available')->count(),
                    'floors' => $building->floors
                        ->sortByDesc('number')
                        ->values()
                        ->map(fn ($floor) => [
                            'number' => $floor->number,
                            'name' => $floor->number === 0 ? __('Ground floor') : __('Floor :number', ['number' => $floor->number]),
                            'units' => $floor->units->map(fn ($unit) => [
                                'id' => $unit->id,
                                'number' => $unit->unit_number,
                                'type' => $unit->unit_type,
                                'area' => (float) $unit->area,
                                'bedrooms' => (int) $unit->bedrooms,
                                'bathrooms' => (int) $unit->bathrooms,
                                'price' => (float) $unit->current_price,
                                'status' => $unit->status?->value ?? 'available',
                                'url' => route('public.units.show', $unit->unit_number),
                                'calc_url' => route('installments.index', ['unit_id' => $unit->id]),
                                'floor_plan' => $unit->floor_plan_path ? asset('storage/'.$unit->floor_plan_path) : null,
                                'floor_plan_is_image' => $unit->floor_plan_path
                                    ? in_array(strtolower(pathinfo($unit->floor_plan_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'avif'], true)
                                    : false,
                            ])->values()->all(),
                        ])->values()->all(),
                ])->values()->all();
            @endphp
            @endif
            <section
                class="mt-9 grid gap-6 lg:grid-cols-[minmax(0,1.06fr)_minmax(18rem,0.94fr)]"
                x-data="{
                    buildings: {{ Js::from($buildingExplorerData) }},
                    statusLabels: { available: @js(__('Available')), reserved: @js(__('Reserved')), sold: @js(__('Sold')), hidden: @js(__('Hidden')) },
                    floorsLabel: @js(__('Floors')),
                    floorWord: @js(__('Floor')),
                    availableWord: @js(__('available')),
                    m2: @js(__('m²')),
                    egp: @js(__('EGP')),
                    areaLabel: @js(__('Area (m²)')),
                    selectedBuildingId: {{ (int) ($project->buildings->first()?->id ?? 0) }},
                    selectedUnit: null,
                    get selectedBuilding() {
                        return this.buildings.find(b => b.id === this.selectedBuildingId) || null;
                    },
                    selectBuilding(id) {
                        this.selectedBuildingId = id;
                        this.selectedUnit = null;
                    },
                    selectUnit(u) {
                        this.selectedUnit = (this.selectedUnit && this.selectedUnit.id === u.id) ? null : u;
                    },
                    tileClass(u) {
                        const base = 'group relative flex min-h-14 min-w-0 flex-col items-center justify-center rounded-xl border px-2 py-2 text-center transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg';
                        if (this.selectedUnit && this.selectedUnit.id === u.id) {
                            return base + ' border-brand-400/80 bg-brand-500/20 ring-2 ring-brand-400/50 shadow-brand-500/20';
                        }
                        const map = {
                            available: 'border-emerald-500/40 bg-emerald-500/10 hover:border-emerald-400/70 hover:bg-emerald-500/20',
                            reserved: 'border-amber-500/40 bg-amber-500/10 hover:border-amber-400/70 hover:bg-amber-500/20',
                            sold: 'border-rose-500/40 bg-rose-500/10 hover:border-rose-400/70 hover:bg-rose-500/20',
                            hidden: 'border-white/10 bg-white/5 hover:border-white/20 hover:bg-white/10',
                        };
                        return base + ' ' + (map[u.status] || map.hidden);
                    },
                    dotClass(u) {
                        return { available: 'bg-emerald-400', reserved: 'bg-amber-400', sold: 'bg-rose-400', hidden: 'bg-slate-500' }[u.status] || 'bg-slate-500';
                    },
                    badgeClass(u) {
                        return { available: 'badge badge-success', reserved: 'badge badge-warning', sold: 'badge badge-danger', hidden: 'badge badge-muted' }[u.status] || 'badge badge-muted';
                    },
                    money(v) {
                        return new Intl.NumberFormat(undefined, { maximumFractionDigits: 0 }).format(v || 0);
                    },
                }"
            >
        @if ($project->buildings->isNotEmpty())
            <div class="space-y-5">
                <div>
                    <p class="mobile-section-title">{{ __('Buildings & Floors') }}</p>
                    <h2 class="mt-2 text-2xl font-bold tracking-tight text-white">{{ __('Building floor plan & availability') }}</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-400">{{ __('Interactive building map') }} — {{ __('Explore units floor by floor and check availability at a glance.') }}</p>
                </div>

                <div class="app-card overflow-hidden p-4 sm:p-6">
                    {{-- Building tabs --}}
                    <div class="flex flex-wrap gap-2">
                        <template x-for="b in buildings" :key="b.id">
                            <button
                                type="button"
                                @click="selectBuilding(b.id)"
                                class="inline-flex items-center gap-2 rounded-2xl border px-3.5 py-2 text-sm font-semibold transition"
                                :class="selectedBuildingId === b.id ? 'border-brand-500/60 bg-brand-500/15 text-white shadow-lg shadow-brand-500/10' : 'border-white/10 bg-white/5 text-slate-300 hover:bg-white/10 hover:text-white'"
                            >
                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M4 21V7l8-4 8 4v14M9 21v-4h6v4M8 7h.01M12 7h.01M16 7h.01M8 11h.01M12 11h.01M16 11h.01" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <span class="min-w-0" x-text="b.name"></span>
                                <span class="rounded-full bg-emerald-500/15 px-1.5 py-0.5 text-[10px] font-bold text-emerald-300" x-text="b.available"></span>
                            </button>
                        </template>
                    </div>

                    {{-- Legend --}}
                    <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 rounded-2xl border border-white/5 bg-white/[0.03] px-4 py-2.5 text-xs text-slate-400">
                        <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span>{{ __('Available') }}</span>
                        <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span>{{ __('Reserved') }}</span>
                        <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-rose-400"></span>{{ __('Sold') }}</span>
                        <span class="ms-auto inline-flex items-center gap-1.5 text-[11px] text-slate-500">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5M9 18h6M10 22h4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            {{ __('Click a unit to view its details.') }}
                        </span>
                    </div>

                    {{-- Building canvas --}}
                    <div class="relative mt-4 overflow-hidden rounded-3xl border border-white/10 bg-slate-950/70 p-3 sm:p-5">
                        <div class="pointer-events-none absolute -end-24 -top-24 h-48 w-48 rounded-full bg-brand-500/10 blur-3xl"></div>

                        {{-- Active building header --}}
                        <template x-if="selectedBuilding">
                            <div class="relative mb-3 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-white/5 bg-white/[0.04] px-4 py-2.5">
                                <div class="flex items-center gap-2.5">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-500/15 text-brand-300">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M4 21V7l8-4 8 4v14M9 21v-4h6v4M8 7h.01M12 7h.01M16 7h.01M8 11h.01M12 11h.01M16 11h.01" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </span>
                                    <div>
                                        <h3 class="text-base font-bold text-white" x-text="selectedBuilding.name"></h3>
                                        <p class="text-[11px] text-slate-500">
                                            <span x-text="selectedBuilding.floors.length"></span> <span x-text="floorsLabel"></span>
                                            <template x-if="selectedBuilding.code"><span> · <span x-text="selectedBuilding.code"></span></span></template>
                                        </p>
                                    </div>
                                </div>
                                <span class="rounded-2xl border border-emerald-500/25 bg-emerald-500/10 px-3 py-1.5 text-center">
                                    <span class="block text-sm font-extrabold leading-none text-emerald-300" x-text="selectedBuilding.available"></span>
                                    <span class="mt-0.5 block text-[10px] uppercase tracking-wide text-emerald-400/70" x-text="availableWord"></span>
                                </span>
                            </div>
                        </template>

                        {{-- Floors & units --}}
                        <div class="relative space-y-2.5">
                            <template x-for="floor in selectedBuilding ? selectedBuilding.floors : []" :key="floor.number">
                                <div class="flex items-stretch gap-2.5">
                                    <div class="flex w-20 shrink-0 flex-col items-center justify-center rounded-2xl border border-white/5 bg-white/[0.03] px-1 py-2 text-center sm:w-24">
                                        <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-500" x-text="floorWord"></span>
                                        <span class="mt-0.5 text-xs font-bold leading-tight text-slate-200" x-text="floor.name"></span>
                                    </div>
                                    <div class="flex-1 rounded-2xl border border-white/5 bg-white/[0.02] p-2">
                                        <div x-show="floor.units.length > 0" class="grid grid-cols-[repeat(auto-fill,minmax(5.5rem,1fr))] gap-2">
                                            <template x-for="u in floor.units" :key="u.id">
                                                <button
                                                    type="button"
                                                    @click="selectUnit(u)"
                                                    :class="tileClass(u)"
                                                    :title="u.number + ' — ' + (statusLabels[u.status] || u.status)"
                                                >
                                                    <span class="absolute end-1.5 top-1.5 h-2 w-2 rounded-full ring-2 ring-slate-950/60" :class="dotClass(u)"></span>
                                                    <span class="text-sm font-extrabold text-white" x-text="u.number"></span>
                                                    <span class="mt-0.5 max-w-full truncate text-[10px] text-slate-400" x-text="u.area ? u.area + ' ' + m2 : ''"></span>
                                                </button>
                                            </template>
                                        </div>
                                        <div x-show="floor.units.length === 0" class="flex min-h-12 items-center justify-center rounded-xl border border-dashed border-white/10 text-xs text-slate-500">
                                            {{ __('No units on this floor') }}
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Selected unit summary --}}
                    <template x-if="selectedUnit">
                        <div class="relative mt-4 overflow-hidden rounded-2xl border border-brand-500/25 bg-brand-500/[0.06] p-4">
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="rounded-xl border border-white/10 bg-white/5 px-3 py-1.5 text-lg font-extrabold text-white" x-text="selectedUnit.number"></span>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-white" x-text="selectedUnit.type || '—'"></p>
                                    <p class="text-xs text-slate-400" x-text="selectedUnit.area ? areaLabel + ': ' + selectedUnit.area + ' ' + m2 : ''"></p>
                                </div>
                                <span class="ms-auto text-end">
                                    <span class="block text-lg font-extrabold leading-none text-emerald-400" x-text="selectedUnit.price ? money(selectedUnit.price) + ' ' + egp : '—'"></span>
                                    <span class="mt-1 block text-[10px] uppercase tracking-wide text-slate-500">{{ __('Price') }}</span>
                                </span>
                            </div>
                            <div class="mt-3 grid gap-2 sm:grid-cols-2">
                                <a :href="selectedUnit.url" class="app-button justify-center gap-2 py-2.5 text-sm">
                                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke-width="1.8"/></svg>
                                    {{ __('View unit details') }}
                                </a>
                                <a :href="selectedUnit.calc_url" class="app-button--ghost justify-center gap-2 py-2.5 text-sm">
                                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><rect x="5" y="3" width="14" height="18" rx="2" stroke-width="1.8"/><path d="M8 7h8M8 11h.01M12 11h.01M16 11h.01M8 15h.01M12 15h.01M16 15h.01M8 18.5h.01M12 18.5h.01M16 18.5h.01" stroke-width="1.8" stroke-linecap="round"/></svg>
                                    {{ __('Calculate installment') }}
                                </a>
                            </div>
                        </div>
                    </template>

                </div>
            </div>

            {{-- Selected unit preview — beside the explorer box --}}
            <div class="space-y-5">
                <div id="units" class="rounded-2xl border border-white/10 bg-white/[0.04] p-5 sm:p-6">
                    <div class="mb-4">
                        <p class="mobile-section-title">{{ __('Selected unit preview') }}</p>
                        <h2 class="mt-1 text-2xl font-bold tracking-tight text-white">{{ __('Selected unit preview') }}</h2>
                        <p class="mt-1 text-sm text-slate-400">{{ __('The horizontal projection (floor plan) of the unit you choose above.') }}</p>
                    </div>

                    <template x-if="selectedUnit">
                        <div>
                            <template x-if="selectedUnit.floor_plan">
                                <div>
                                    <template x-if="selectedUnit.floor_plan_is_image">
                                        <a :href="selectedUnit.floor_plan" target="_blank" rel="noopener noreferrer" class="group relative block overflow-hidden rounded-2xl border border-white/10 bg-slate-950/60">
                                            <img :src="selectedUnit.floor_plan" :alt="selectedUnit.number" class="max-h-[32rem] w-full object-contain transition duration-500 group-hover:scale-[1.02]">
                                            <span class="absolute bottom-2 end-2 rounded-full bg-slate-950/80 px-3 py-1 text-[11px] font-semibold text-white backdrop-blur">
                                                <span x-text="selectedUnit.number"></span> · {{ __('Floor Plan') }}
                                            </span>
                                        </a>
                                    </template>
                                    <template x-if="! selectedUnit.floor_plan_is_image">
                                        <div class="flex items-center gap-3 rounded-2xl border border-white/10 bg-slate-950/60 p-4">
                                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand-500/15 text-brand-400">
                                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-width="1.8"/><path d="M14 2v6h6" stroke-width="1.8"/><path d="M9 15h6M9 11h2" stroke-width="1.8" stroke-linecap="round"/></svg>
                                            </span>
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-white"><span x-text="selectedUnit.number"></span> — {{ __('Floor Plan') }}</p>
                                                <p class="text-xs text-slate-400">{{ __('Click to open the floor plan file.') }}</p>
                                            </div>
                                            <a :href="selectedUnit.floor_plan" target="_blank" rel="noopener noreferrer" class="app-button ms-auto shrink-0 gap-1.5 py-2 text-xs">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 3v12m0 0 4-4m-4 4-4-4M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                {{ __('Open') }}
                                            </a>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="! selectedUnit.floor_plan">
                                <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/5 py-12 text-center">
                                    <svg class="h-10 w-10 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 4h16v16H4z" stroke-width="1.8"/><path d="M4 12h16M12 4v16M7 12v8M17 12v8" stroke-width="1.8"/></svg>
                                    <p class="mt-3 text-sm font-semibold text-white">{{ __('No floor plan for this unit') }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ __('The floor plan has not been uploaded yet.') }}</p>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="! selectedUnit">
                        <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/5 py-12 text-center">
                            <svg class="h-10 w-10 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 4h16v16H4z" stroke-width="1.8"/><path d="M4 12h16M12 4v16M7 12v8M17 12v8" stroke-width="1.8"/></svg>
                            <p class="mt-3 text-sm font-semibold text-white">{{ __('Choose a unit to preview its floor plan') }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ __('Select any unit from the building above.') }}</p>
                        </div>
                    </template>
                </div>
            </div>
        @endif
            </section>
        @endif
    </div>
@endsection
