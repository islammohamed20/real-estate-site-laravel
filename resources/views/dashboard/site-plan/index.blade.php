@extends('layouts.dashboard')

@section('content')
<style>
    /* ===== PRINT STYLES FOR A3 LANDSCAPE ===== */
    @media print {
        @page {
            size: A3 landscape;
            margin: 8mm;
        }

        /* Hide dashboard chrome */
        .sidebar, .topbar, .dashboard-header, nav, .breadcrumb,
        .app-sidebar, aside, .no-print, [x-show="mobileMenuOpen"] {
            display: none !important;
        }

        body {
            background: #fff !important;
            color: #000 !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: visible !important;
        }

        .dashboard-content, .main-content, main, [role="main"] {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        /* Reset all dark backgrounds to white for printing */
        .app-card, .app-card--gradient {
            background: #fff !important;
            border: 1px solid #ccc !important;
            box-shadow: none !important;
        }

        /* Print Header */
        .print-header {
            display: flex !important;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 4mm;
            border-bottom: 2pt solid #0f172a;
            padding-bottom: 3mm;
        }
        .print-header .print-brand {
            text-align: left;
        }
        .print-header h1 {
            font-size: 16pt !important;
            color: #0f172a !important;
            font-weight: bold;
            margin: 0;
            line-height: 1.2;
        }
        .print-header p {
            font-size: 9pt !important;
            color: #475569 !important;
            margin: 1.5mm 0 0 0;
        }
        .print-header .print-date {
            font-size: 8pt !important;
            color: #94a3b8 !important;
            white-space: nowrap;
            text-align: right;
        }

        /* Screen header - hide on print */
        .dashboard-hero-card { display: none !important; }

        /* Legend for print */
        .print-legend {
            display: flex !important;
            justify-content: center;
            gap: 12mm;
            margin-bottom: 4mm;
            font-size: 8pt;
        }
        .print-legend .legend-item {
            display: flex;
            align-items: center;
            gap: 2mm;
        }
        .print-legend .legend-box {
            width: 4mm;
            height: 4mm;
            border: 0.5pt solid #999;
        }

        /* Floor section headers */
        .floor-label {
            background: #333 !important;
            color: #fff !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            padding: 1.5mm 4mm !important;
            font-size: 9pt !important;
            font-weight: bold;
            border-radius: 2mm;
            margin: 2mm 0;
        }

        /* Building cells */
        .building-cell {
            border: 0.5pt solid #999 !important;
            background: #f8f8f8 !important;
            padding: 1mm !important;
            page-break-inside: avoid;
        }

        .building-code {
            font-size: 7pt !important;
            color: #333 !important;
            font-weight: bold;
            text-align: center;
            margin-bottom: 1mm;
        }

        /* Unit cells */
        .unit-cell {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            border: 0.3pt solid rgba(0,0,0,0.2) !important;
            font-size: 6.5pt !important;
            padding: 0.5mm 0.3mm !important;
            text-align: center;
            line-height: 1.2;
        }
        .unit-area {
            font-size: 7pt !important;
            font-weight: bold;
        }
        .unit-number {
            font-size: 5pt !important;
            opacity: 0.85;
        }

        /* Summary stats for print */
        .print-summary {
            display: flex !important;
            justify-content: center;
            gap: 8mm;
            margin-top: 4mm;
            padding-top: 3mm;
            border-top: 1px solid #ccc;
            font-size: 8pt;
        }

        /* Hide screen-only elements */
        .screen-only { display: none !important; }

        /* Tooltip - hide on print */
        .unit-tooltip { display: none !important; }

        /* Orientation labels */
        .orientation-label {
            font-size: 7pt !important;
            color: #666 !important;
            font-style: italic;
        }

        /* Landmark labels */
        .landmark-label {
            font-size: 7pt !important;
            color: #999 !important;
            font-style: italic;
            text-align: center;
        }

        /* Don't break floor sections across pages */
        .floor-section {
            page-break-inside: avoid;
        }

        /* Ensure theme dark panels print cleanly */
        .space-y-6, .app-card__body, .app-card--gradient > * {
            background: transparent !important;
        }
        .floor-label {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        /* Building top border accent on print */
        .building-cell {
            border-top: 1.2pt solid #1e40af !important;
        }
    }

    /* ===== SCREEN STYLES ===== */
    @media screen {
        .print-header, .print-legend, .print-summary { display: none; }
    }

    /* ===== FILTER / SEARCH HELPERS (screen only) ===== */
    .site-dim { opacity: 0.18 !important; filter: saturate(0.25) brightness(0.6); }
    .site-search-hit { box-shadow: 0 0 0 2px #fbbf24, 0 0 14px rgb(251 191 36 / 0.55) !important; z-index: 6; }
    .site-search-miss { opacity: 0.15 !important; filter: saturate(0.2) brightness(0.55); }
    .site-filter-hidden { display: none !important; }
    .unit-cell { transition: opacity .18s ease, box-shadow .18s ease, transform .18s ease, filter .18s ease; }
    .unit-cell:focus-visible { outline: 2px solid #fbbf24; outline-offset: 1px; }
    .site-plan-canvas { direction: ltr; }
    .building-groups { display: flex; width: 100%; align-items: end; gap: 28px; }
    .building-group { display: flex; align-items: end; justify-content: center; gap: 5px; min-width: 0; }
    .building-group--10 { flex: 1.1 0 auto; }
    .building-group--20 { flex: .75 0 auto; }
    .building-group--30 { flex: 1.75 0 auto; }
    .building-group--40 { flex: .45 0 auto; }
    .building-group--90 { flex: 1 0 auto; }
    .building-group--30 { gap: 0; }
    .building-cell { direction: ltr; min-width: 86px; border: 1px solid rgb(148 163 184 / .16); }
    .building-group--30 .building-cell { border-radius: 0; }
    .building-group--30 .building-cell:first-child { border-radius: .75rem 0 0 .75rem; }
    .building-group--30 .building-cell:last-child { border-radius: 0 .75rem .75rem 0; }
    .unit-cell { min-height: 42px; min-width: 40px; display: flex; flex-direction: column; align-items: center; justify-content: center; }
    .unit-slot { position: absolute; top: -9px; left: 50%; transform: translateX(-50%); display: inline-flex; width: 14px; height: 14px; align-items: center; justify-content: center; border: 1px solid #ef4444; border-radius: 999px; background: white; color: #dc2626; font-size: 7px; font-weight: 800; }
    .unit-kind { font-size: 6px; font-weight: 800; line-height: 1; text-transform: uppercase; }
    @media (max-width: 900px) {
        .building-groups { width: max-content; gap: 20px; }
        .building-group { flex: 0 0 auto; }
    }
</style>

<div class="space-y-6"
     x-init="$nextTick(() => applyFilters())"
     x-data="{
        visibleOnly: true,
        zoom: 1,
        searchTerm: '',
        activeStatus: null,
        results: 0,
        visibleTotal: 0,
        visibleBuildingTotal: 0,
        statusCounts: @js(collect($statusColors)->mapWithKeys(fn ($color, $key) => [$key => 0])->all()),
        floorCounts: {},
        applyFilters() {
            const q = (this.searchTerm || '').trim().toLowerCase();
            let found = 0;
            const statusCounts = Object.fromEntries(Object.keys(this.statusCounts).map((status) => [status, 0]));
            const floorCounts = {};
            let visibleTotal = 0;
            let visibleBuildingTotal = 0;
            this.$root.querySelectorAll('[data-unit-cell]').forEach((el) => {
                const num = (el.dataset.unit || '').toLowerCase();
                const st  = el.dataset.status;
                const matchStatus = !this.activeStatus || st === this.activeStatus;
                const matchSearch = !q || num.includes(q);
                const matchVisibility = !this.visibleOnly || el.dataset.visible === '1';
                const matches = matchStatus && matchSearch && matchVisibility;
                el.classList.toggle('site-filter-hidden', !matchVisibility);
                el.classList.toggle('site-dim', matchVisibility && !matchStatus);
                el.classList.toggle('site-search-hit', !!q && matches);
                el.classList.toggle('site-search-miss', matchVisibility && !!q && !matchSearch);
                if (matchVisibility) {
                    visibleTotal++;
                    statusCounts[st] = (statusCounts[st] || 0) + 1;
                    floorCounts[el.dataset.floor] = (floorCounts[el.dataset.floor] || 0) + 1;
                }
                if (q && matches) found++;
            });
            this.$root.querySelectorAll('[data-building-cell]').forEach((building) => {
                const units = [...building.querySelectorAll('[data-unit-cell]')];
                const isHidden = units.length === 0 || units.every((unit) => unit.classList.contains('site-filter-hidden'));
                building.classList.toggle('site-filter-hidden', isHidden);
                if (!isHidden) visibleBuildingTotal++;
            });
            this.$root.querySelectorAll('[data-building-group]').forEach((group) => {
                const buildings = [...group.querySelectorAll('[data-building-cell]')];
                group.classList.toggle('site-filter-hidden', buildings.length === 0 || buildings.every((building) => building.classList.contains('site-filter-hidden')));
            });
            this.results = found;
            this.visibleTotal = visibleTotal;
            this.visibleBuildingTotal = visibleBuildingTotal;
            this.statusCounts = statusCounts;
            this.floorCounts = floorCounts;
        },
        revealFirstResult() {
            const firstHit = this.$root.querySelector('.site-search-hit');
            firstHit?.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'center' });
        }
     }">

    {{-- Screen Header --}}
    <section class="dashboard-hero-card p-6 sm:p-8 no-print">
        <div class="relative flex flex-col gap-4">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="mobile-section-title">{{ __('Site Plan') }}</p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('Project Map') }}</h1>
                    <p class="mt-2 text-sm text-slate-400">{{ $project->name }} &mdash; <span class="tabular-nums" x-text="visibleBuildingTotal">{{ $buildings->count() }}</span> {{ __('building(s)') }} &mdash; <span class="tabular-nums" x-text="visibleTotal">{{ $allUnits->count() }}</span> {{ __('unit(s)') }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <label class="relative">
                        <span class="sr-only">{{ __('Project') }}</span>
                        <select class="app-input max-w-[200px]" onchange="window.location='{{ route('dashboard.site-plan') }}?project_id='+this.value">
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}" {{ $p->id === $project->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </label>

                    {{-- Visible-only toggle --}}
                    <button
                        type="button"
                        @click="visibleOnly = !visibleOnly; $nextTick(() => applyFilters())"
                        :class="visibleOnly ? 'bg-emerald-600 hover:bg-emerald-500 text-white' : 'bg-slate-700 hover:bg-slate-600 text-slate-300'"
                        class="inline-flex items-center gap-2 rounded-lg px-3 py-2.5 text-sm font-semibold transition shadow"
                        :title="visibleOnly ? '{{ __('Showing: Visible on website only') }}' : '{{ __('Showing: All buildings') }}'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <span x-text="visibleOnly ? '{{ __('Visible on website') }}' : '{{ __('All buildings') }}'"></span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- Sticky Toolbar: search + status filter + actions --}}
    <div class="sticky top-16 z-30 no-print">
        <div class="flex flex-wrap items-center gap-2 rounded-xl border border-white/10 bg-slate-950/85 px-3 py-2 shadow-lg shadow-black/30 backdrop-blur-xl">
            {{-- Unit search --}}
            <div class="relative min-w-[180px] flex-1 sm:w-72 sm:flex-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute top-1/2 start-2.5 h-4 w-4 -translate-y-1/2 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input
                    type="text"
                    x-model="searchTerm"
                    @input.debounce.150ms="applyFilters()"
                    @keydown.enter.prevent="revealFirstResult()"
                    placeholder="{{ __('Search by unit number…') }}"
                    class="app-input w-full ps-9 pe-8 py-2 text-sm"
                    :class="searchTerm ? 'border-amber-400/60' : ''"
                >
                <button
                    type="button"
                    x-show="searchTerm"
                    @click="searchTerm = ''; applyFilters()"
                    class="absolute top-1/2 end-2 -translate-y-1/2 inline-flex h-5 w-5 items-center justify-center rounded-full bg-white/10 text-slate-300 hover:bg-white/20 hover:text-white"
                    :title="'{{ __('Clear search') }}'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <span class="text-xs font-semibold tabular-nums text-amber-300" x-show="searchTerm" x-cloak x-text="results + ' / ' + visibleTotal + ' — ' + (results === 1 ? '{{ __('match') }}' : '{{ __('matches') }}')"></span>

            {{-- Status filter chips --}}
            <div class="flex flex-wrap items-center gap-1.5" role="group" aria-label="{{ __('Filter by status') }}">
                <button
                    type="button"
                    @click="activeStatus = null; applyFilters()"
                    :class="activeStatus === null ? 'bg-white/15 text-white ring-1 ring-white/50' : 'bg-white/5 text-slate-400 hover:bg-white/10 hover:text-white'"
                    class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-bold transition"
                >{{ __('All') }}</button>

                @foreach($statusColors as $key => $color)
                    @php $count = $allUnits->filter(fn($u) => $u['status'] === $key)->count(); @endphp
                    <button
                        type="button"
                        @click="activeStatus = activeStatus === '{{ $key }}' ? null : '{{ $key }}'; applyFilters()"
                        :class="activeStatus === '{{ $key }}' ? 'bg-white/15 text-white ring-1 ring-white/50' : 'bg-white/5 text-slate-400 hover:bg-white/10 hover:text-white'"
                        class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-bold transition"
                    >
                        <span class="h-3 w-3 shrink-0 rounded-sm" style="background-color: {{ $color['bg'] }}"></span>
                        <span>{{ $color['label'] }}</span>
                        <span class="tabular-nums opacity-70" x-text="statusCounts['{{ $key }}'] ?? 0">{{ $count }}</span>
                    </button>
                @endforeach
            </div>

            {{-- Actions --}}
            <div class="ms-auto flex items-center gap-2">
                <a :href="'{{ route('dashboard.site-plan.pdf', ['project_id' => $project->id, 'inline' => 1]) }}&visible_only=' + (visibleOnly ? 1 : 0)" target="_blank" class="inline-flex items-center gap-2 rounded-lg bg-slate-600 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-500 transition shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span class="hidden sm:inline">{{ __('Print A3') }}</span>
                </a>
                <a :href="'{{ route('dashboard.site-plan.pdf', ['project_id' => $project->id]) }}&visible_only=' + (visibleOnly ? 1 : 0)" target="_blank" class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-3 py-2 text-sm font-semibold text-white hover:bg-brand-500 transition shadow shadow-brand-600/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span class="hidden sm:inline">{{ __('Download PDF') }}</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Print Header (only visible when printing) --}}
    <div class="print-header" style="display:none;">
        <div class="print-brand">
            <h1>خريطة الموقع — {{ $project->name }}</h1>
            <p>{{ $buildings->count() }} مبنى — {{ $allUnits->count() }} وحدة</p>
        </div>
        <div class="print-date">{{ now()->format('Y-m-d H:i') }}</div>
    </div>

    {{-- Print Legend --}}
    <div class="print-legend" style="display:none;">
        @foreach($statusColors as $key => $color)
            <div class="legend-item">
                <span class="legend-box" style="background-color: {{ $color['bg'] }};"></span>
                <span>{{ $color['label'] }}</span>
            </div>
        @endforeach
    </div>

    {{-- Site Plan Grid --}}
    <div class="app-card app-card--gradient overflow-x-auto p-4 relative">
        {{-- Zoom Controls --}}
        <div class="absolute top-4 end-4 z-10 flex items-center gap-1 bg-slate-950/80 backdrop-blur border border-white/10 p-1.5 rounded-xl no-print">
            <button type="button" @click="zoom = Math.max(0.5, zoom - 0.15)" class="touch-target inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/5 text-slate-300 hover:bg-white/10 hover:text-white transition active:scale-95" title="{{ __('Zoom Out') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
            </button>
            <span class="text-xs font-extrabold text-slate-300 min-w-[42px] text-center select-none tabular-nums" x-text="Math.round(zoom * 100) + '%'"></span>
            <button type="button" @click="zoom = Math.min(1.5, zoom + 0.15)" class="touch-target inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/5 text-slate-300 hover:bg-white/10 hover:text-white transition active:scale-95" title="{{ __('Zoom In') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            </button>
            <div class="w-px h-5 bg-white/10 mx-1"></div>
            <button type="button" @click="zoom = 1" class="touch-target inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/5 text-slate-300 hover:bg-white/10 hover:text-white transition active:scale-95" title="{{ __('Reset') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18.5"/></svg>
            </button>
        </div>

        <div class="min-w-[1200px] transition-all duration-300 origin-top-right" :style="'zoom: ' + zoom">
            @if($allUnits->isEmpty())
                {{-- Empty state --}}
                <div class="flex flex-col items-center justify-center gap-3 py-20 text-center">
                    <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-white/5 border border-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-slate-300">{{ __('No units in this project yet') }}</p>
                    <p class="text-xs text-slate-500">{{ __('Add buildings and units to start building your site plan') }}</p>
                </div>
            @else
                @php
                    $allFloors = $buildings->flatMap(function($b) {
                        return $b['floors']->map(fn($f) => ['number' => $f->number, 'name' => $f->name ?? 'الدور ' . $f->number]);
                    })->sortBy('number')->unique('number')->values();
                @endphp

                @foreach($allFloors as $floorInfo)
                    @php $floorNum = $floorInfo['number']; @endphp
                    <div class="mb-8 floor-section">
                        @php
                            $floorUnitTotal = $buildings->sum(function ($b) use ($floorNum) {
                                $fid = $b['floors']->firstWhere('number', $floorNum)?->id;
                                return $fid ? ($b['units_by_floor'][$fid] ?? collect())->count() : 0;
                            });
                        @endphp
                        {{-- Floor Label --}}
                        <div class="mb-3 flex items-center gap-3 no-print">
                            <div class="h-px flex-1 bg-gradient-to-r from-brand-500/50 to-transparent"></div>
                            <span class="floor-label shrink-0 rounded-lg bg-brand-500/15 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-brand-300 border border-brand-500/20">
                                {{ $floorInfo['name'] }}
                                <span class="ms-2 normal-case tracking-normal text-brand-400/80 tabular-nums">(<span x-text="floorCounts['{{ $floorNum }}'] ?? 0">{{ $floorUnitTotal }}</span> {{ __('unit(s)') }})</span>
                            </span>
                            <div class="h-px flex-1 bg-gradient-to-l from-brand-500/50 to-transparent"></div>
                        </div>
                        {{-- Floor Label (Print) --}}
                        <div class="floor-label text-center" style="display:none;">
                            {{ $floorInfo['name'] }} ({{ $floorUnitTotal }})
                        </div>

                        {{-- Architectural landmarks (screen) --}}
                        <div class="mb-3 grid grid-cols-3 text-[10px] font-semibold text-lime-500 no-print" dir="ltr">
                            <span>{{ __('جامعة سفنكس') }}</span>
                            <span class="text-center">{{ __('اللاند سكيب') }}</span>
                            <span class="text-right">{{ __('وادي دجلة') }}</span>
                        </div>

                        @php
                            $floorBuildingGroups = $buildings
                                ->groupBy('layout_group')
                                ->map(fn ($group) => $group->filter(function ($building) use ($floorNum) {
                                    $floorId = $building['floors']->firstWhere('number', $floorNum)?->id;
                                    return $floorId && ($building['units_by_floor'][$floorId] ?? collect())->isNotEmpty();
                                }))
                                ->filter(fn ($group) => $group->isNotEmpty());
                        @endphp
                        <div class="site-plan-canvas overflow-x-auto pb-4 pt-2">
                            <div class="building-groups">
                            @foreach($floorBuildingGroups as $layoutGroup => $groupBuildings)
                              <div data-building-group class="building-group building-group--{{ $layoutGroup }}">
                              @foreach($groupBuildings as $building)
                                @php
                                    $targetFloorId = $building['floors']->firstWhere('number', $floorNum)?->id;
                                    $units = $targetFloorId ? ($building['units_by_floor'][$targetFloorId] ?? collect()) : collect();
                                @endphp

                                @if($units->isNotEmpty())
                                    <div
                                        data-building-cell
                                        class="building-cell shrink-0 rounded-xl bg-slate-900/50 p-2 hover:border-brand-500/30 transition {{ $building['hidden_from_website'] ? 'border-2 border-slate-500/60 bg-slate-950/40 opacity-60' : '' }}"
                                        style="print-color-adjust: exact;"
                                    >
                                        <div class="building-code mb-1.5 text-center flex items-center justify-center gap-1">
                                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $building['code'] }}</span>
                                            <span class="text-[9px] font-semibold text-slate-500 tabular-nums">{{ $units->count() }}</span>
                                            @if($building['hidden_from_website'])
                                                <span class="text-[8px] px-1 py-0.25 rounded bg-slate-700 text-slate-400 font-bold scale-90" title="{{ __('Hidden from website') }}">{{ __('مخفي') }}</span>
                                            @endif
                                        </div>

                                        @php
                                            // Try to arrange units in 2x2 grid like the architectural plan
                                            $sortedUnits = $units->sortBy(fn ($unit) => [
                                                (int) ($unit['grid_row'] ?? 0),
                                                (int) ($unit['grid_col'] ?? $unit['position_in_grid'] ?? 0),
                                            ])->values();
                                            $cols = $sortedUnits->count() <= 4 ? 2 : (int)ceil(sqrt($sortedUnits->count()));
                                        @endphp
                                        <div class="grid gap-0.5" style="grid-template-columns: repeat({{ $cols }}, 1fr);">
                                            @foreach($sortedUnits as $unit)
                                                @php
                                                    $statusVal = $unit['status'];
                                                    $color = $statusColors[$statusVal] ?? ['bg' => '#6b7280', 'text' => '#fff', 'label' => $statusVal];
                                                    $area = (float) $unit['area'];
                                                    $areaLabel = floor($area) == $area ? number_format($area, 0) : number_format($area, 1);
                                                    $savedPosition = (int) ($unit['position_in_grid'] ?? 0);
                                                    $slotNumber = $savedPosition > 0 ? $savedPosition : $loop->iteration;
                                                @endphp
                                                <div
                                                    data-unit-cell
                                                    data-unit="{{ $unit['unit_number'] }}"
                                                    data-status="{{ $statusVal }}"
                                                    data-floor="{{ $floorNum }}"
                                                    data-visible="{{ ! $building['hidden_from_website'] && ! $unit['hidden_from_website'] ? '1' : '0' }}"
                                                    role="group"
                                                    tabindex="0"
                                                    aria-label="{{ $unit['unit_number'] }} — {{ $areaLabel }} m² — {{ $color['label'] }}"
                                                    @click="$el.focus()"
                                                    class="unit-cell relative group cursor-pointer rounded-sm border border-cyan-300/60 text-center transition hover:scale-105 hover:z-10 hover:shadow-lg {{ str_contains(strtolower((string) ($unit['unit_type'] ?? '')), 'duplex') ? 'ring-2 ring-inset ring-yellow-300' : '' }}"
                                                    style="background-color: {{ $color['bg'] }}; color: {{ $color['text'] }}; print-color-adjust: exact; -webkit-print-color-adjust: exact;"
                                                    title="{{ $unit['unit_number'] }} — {{ $unit['area'] }}m² — {{ $color['label'] }}"
                                                >
                                                    <span class="unit-slot">{{ $slotNumber }}</span>
                                                    <p class="unit-area text-sm font-black leading-none tabular-nums">{{ $areaLabel }}</p>
                                                    @if((float) ($unit['garden_area'] ?? 0) > 0)
                                                        <span class="unit-kind mt-1">Garden</span>
                                                    @elseif(str_contains(strtolower((string) ($unit['unit_type'] ?? '')), 'duplex'))
                                                        <span class="unit-kind mt-1">Duplex</span>
                                                    @endif

                                                    @if(($unit['balcony_area'] ?? 0) > 0)
                                                        <div class="absolute -top-1 -right-1"><span class="inline-flex h-2.5 w-2.5 items-center justify-center rounded-full border border-white/30 bg-white/20 text-[4px] font-bold">B</span></div>
                                                    @endif

                                                    {{-- Tooltip (screen only) --}}
                                                    <div class="unit-tooltip pointer-events-none absolute bottom-full left-1/2 z-50 mb-1.5 hidden w-44 -translate-x-1/2 rounded-lg border border-slate-600 bg-slate-950 p-2 text-right shadow-2xl group-hover:block group-focus:block group-focus-within:block" dir="rtl">
                                                        <p class="text-xs font-bold text-white">{{ $unit['unit_number'] }} <span class="font-normal text-slate-400">({{ $color['label'] }})</span></p>
                                                        <p class="mt-1 text-[11px] text-slate-300">{{ __('Area') }}: {{ $areaLabel }} m²</p>
                                                        <p class="text-[11px] text-slate-300">{{ __('Status') }}: {{ $color['label'] }}</p>
                                                        @if($unit['bedrooms'] ?? 0)
                                                            <p class="text-[11px] text-slate-300">{{ __('Bedrooms') }}: {{ $unit['bedrooms'] }}</p>
                                                        @endif
                                                        @if(($unit['price_per_meter'] ?? 0) > 0)
                                                            <p class="text-[11px] text-slate-300">{{ __('Price per m²') }}: {{ number_format($unit['price_per_meter'], 0) }}</p>
                                                        @endif
                                                        @hasanyrole('Administrator|Sales Executive|Data Entry|Owner')
                                                        <a href="{{ route('dashboard.projects.units.edit', ['project' => $project->id, 'unit' => $unit['id']]) }}"
                                                           target="_blank"
                                                           class="pointer-events-auto mt-2 inline-flex items-center gap-1 text-[11px] font-bold text-brand-300 hover:text-brand-200"
                                                        >
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                            {{ __('Edit unit') }}
                                                        </a>
                                                        @endhasanyrole
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                              @endforeach
                              </div>
                            @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- Summary Stats (print) --}}
    <div class="print-summary" style="display:none;">
        @foreach($statusColors as $key => $color)
            @php $count = $allUnits->filter(fn($u) => $u['status'] === $key)->count(); @endphp
            <div class="legend-item">
                <span class="legend-box" style="background-color: {{ $color['bg'] }};"></span>
                <span><strong>{{ $count }}</strong> {{ $color['label'] }}</span>
            </div>
        @endforeach
    </div>
</div>
@endsection
