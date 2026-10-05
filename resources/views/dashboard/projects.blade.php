@extends('layouts.dashboard')

@section('content')
    @php
        $errors = $errors ?? new \Illuminate\Support\ViewErrorBag();
        $totalUnits = (int) ($stats['units'] ?? 0);
        $available = (int) ($stats['available_units'] ?? 0);
        $featured = (int) ($stats['featured_units'] ?? 0);
        $projectCollection = collect($projects->items());
        $projectCount = count($projects);
        $exportColumns = [
            'building' => __('Building'),
            'building_visibility' => __('Building visibility'),
            'floor' => __('Floor'),
            'unit_number' => __('Unit number'),
            'unit_type' => __('Unit type'),
            'status' => __('Status'),
            'unit_visibility' => __('Unit visibility'),
            'area' => __('Area'),
            'garden_area' => __('Garden area'),
            'roof_area' => __('Roof area'),
            'balcony_area' => __('Balcony area'),
            'bedrooms' => __('Bedrooms'),
            'bathrooms' => __('Bathrooms'),
            'price_per_meter' => __('Price per m²'),
            'excellence_percent' => __('Excellence percentage'),
            'current_price' => __('Current price'),
            'delivery_date' => __('Delivery date'),
            'featured' => __('Featured'),
        ];
    @endphp

    @if (session('status'))
        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="flex items-center gap-3 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12l5 5L20 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>{{ session('status') }}</span>
            <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-200">×</button>
        </div>
    @endif

    @if(isset($errors) && $errors->has('delete'))
        <div class="flex items-center gap-3 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-300">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke-width="1.8"/><path d="M12 9v4M12 17h.01" stroke-width="1.8" stroke-linecap="round"/></svg>
            <span>{{ $errors->first('delete') }}</span>
        </div>
    @endif

    <div class="space-y-6" x-data="{ statusFilter: 'all', search: '' }">
        {{-- ══════════════════════════════════════════════════════════════
             HERO & PORTFOLIO OVERVIEW
        ══════════════════════════════════════════════════════════════ --}}
        <section class="dashboard-hero-card p-5 sm:p-8 relative overflow-hidden">
            <div class="absolute -right-16 -top-16 h-56 w-56 rounded-full bg-brand-500/20 blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-16 left-1/3 h-56 w-56 rounded-full bg-violet-500/15 blur-3xl pointer-events-none"></div>

            <div class="relative grid gap-6 lg:grid-cols-[minmax(0,1.4fr)_minmax(16rem,0.9fr)] lg:items-end">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="badge badge-brand">{{ $stats['projects'] ?? $projectCount }} {{ __('Projects Total') }}</span>
                        <span class="badge badge-success">{{ number_format($available) }} {{ __('Available Units') }}</span>
                        <span class="badge badge-warning">{{ number_format($featured) }} {{ __('Featured Units') }}</span>
                    </div>

                    <div>
                        <p class="mobile-section-title">{{ __('Real Estate Portfolio') }}</p>
                        <h1 class="mt-2 text-3xl font-black tracking-tight text-white sm:text-4xl">{{ __('Projects Management') }}</h1>
                        <p class="mt-2 max-w-2xl text-xs leading-relaxed text-slate-300 sm:text-sm">
                            {{ __('Comprehensive overview of all real estate developments, towers, buildings, floor layouts, and live inventory status.') }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2 pt-1">
                        @can('create', App\Models\Project::class)
                            <a href="{{ route('dashboard.projects.create') }}" class="app-button !min-h-10 gap-1.5 text-xs sm:text-sm">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round"/></svg>
                                {{ __('+ New Project') }}
                            </a>
                        @endcan
                        @can('view reports')
                            <a href="{{ route('dashboard.reports.index') }}" class="app-button--ghost !min-h-10 text-xs sm:text-sm">{{ __('Open Reports') }}</a>
                        @endcan
                        <a href="{{ route('dashboard.home') }}" class="app-button--ghost !min-h-10 text-xs sm:text-sm">{{ __('Dashboard') }}</a>
                    </div>
                </div>

                {{-- 3 KPI mini-cards --}}
                <div class="grid gap-2.5 sm:grid-cols-3">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-3.5 shadow-lg shadow-black/10 backdrop-blur-xl">
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500/25 to-violet-600/15 text-brand-300 ring-1 ring-brand-500/20">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 21h18M3 7v1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7M4 21V4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v17" stroke-width="1.8"/></svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-xl font-bold leading-tight tabular-nums text-white">{{ number_format($stats['projects'] ?? 0) }}</p>
                                <p class="truncate text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('Projects') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-3.5 shadow-lg shadow-black/10 backdrop-blur-xl">
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500/25 to-fuchsia-500/10 text-violet-300 ring-1 ring-violet-500/20">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 21h18M6 21V7a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v14M9 21v-4a3 3 0 0 1 3-3v0a3 3 0 0 1 3 3v4M9 10h.01M15 10h.01" stroke-width="1.8" stroke-linecap="round"/></svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-xl font-bold leading-tight tabular-nums text-white">{{ number_format($totalUnits) }}</p>
                                <p class="truncate text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('Total Units') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-3.5 shadow-lg shadow-black/10 backdrop-blur-xl">
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500/25 to-teal-500/10 text-emerald-300 ring-1 ring-emerald-500/20">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" stroke-width="1.8" stroke-linecap="round"/><path d="m9 11 3 3L22 4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-xl font-bold leading-tight tabular-nums text-emerald-400">{{ number_format($available) }}</p>
                                <p class="truncate text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('Available') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ══════════════════════════════════════════════════════════════
             PROJECTS LIST / CARDS
        ══════════════════════════════════════════════════════════════ --}}
        <section class="space-y-4">
            @forelse($projectCollection as $project)
                @php
                    $pTotal = (int) ($project->units_count ?? 0);
                    $pAvailable = (int) ($project->available_units_count ?? 0);
                    $pReserved = (int) ($project->reserved_units_count ?? 0);
                    $pSold = (int) ($project->sold_units_count ?? 0);
                    $pOccupied = $pReserved + $pSold;
                    $pOccupancy = $pTotal > 0 ? (int) round($pOccupied / $pTotal * 100) : 0;
                    $coverImg = $project->cover_image_path
                        ?: collect($project->images ?? [])->filter(fn ($img) => is_string($img))->first();
                    $coverUrl = $coverImg ? asset('storage/'.$coverImg) : null;
                    $status = $project->status ?? 'draft';
                    $statusLabel = match ($status) {
                        'active' => __('Active'),
                        'launching' => __('Launching'),
                        'sold' => __('Sold Out'),
                        default => __('Draft'),
                    };
                    $statusClass = match ($status) {
                        'active' => 'badge-success',
                        'launching' => 'badge-warning',
                        'sold' => 'badge-danger',
                        default => 'badge-muted',
                    };
                @endphp

                <article x-data="{ exportOpen: false, includeHidden: false, allExportColumns: {{ json_encode(array_keys($exportColumns)) }}, selectedColumns: {{ json_encode(array_keys($exportColumns)) }} }" class="stagger-item app-card app-card--gradient overflow-hidden transition-all duration-300 hover:border-brand-500/30 hover:bg-white/[0.08]" style="animation-delay:{{ 100 + $loop->index * 60 }}ms">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                        {{-- Project Cover Image --}}
                        @if ($coverUrl)
                            <div class="relative h-40 w-full shrink-0 self-center overflow-hidden rounded-2xl border border-white/10 lg:h-44 lg:w-64 lg:self-start group">
                                <img src="{{ $coverUrl }}" alt="{{ $project->name }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
                                <span class="absolute right-2.5 top-2.5 rounded-full bg-slate-950/80 backdrop-blur-md px-2.5 py-0.5 text-[10px] font-bold text-white border border-white/10">
                                    {{ $project->code ?? __('Project') }}
                                </span>
                            </div>
                        @else
                            <div class="flex h-40 w-full shrink-0 items-center justify-center self-center rounded-2xl border border-dashed border-white/10 bg-white/5 lg:h-44 lg:w-64 lg:self-start">
                                <span class="text-4xl">🏢</span>
                            </div>
                        @endif

                        {{-- Project Details --}}
                        <div class="min-w-0 flex-1 space-y-4">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <h2 class="text-xl font-bold text-white">{{ $project->name }}</h2>
                                <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                @if($project->featured)
                                    <span class="badge badge-warning">{{ __('Featured') }}</span>
                                @endif
                                <span class="badge badge-muted text-xs">{{ $pTotal }} {{ __('Units') }}</span>
                            </div>

                            <div class="flex flex-wrap items-center gap-3 text-xs text-slate-400">
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" stroke-width="1.8"/><circle cx="12" cy="10" r="3" stroke-width="1.8"/></svg>
                                    {{ $project->location ?? __('Location pending') }}
                                </span>
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="9" stroke-width="1.8"/><path d="M12 7v5l3 3" stroke-width="1.8" stroke-linecap="round"/></svg>
                                    {{ $project->updated_at ? \Illuminate\Support\Carbon::parse($project->updated_at)->diffForHumans() : __('No updates yet') }}
                                </span>
                            </div>

                            @if($project->description)
                                <p class="max-w-3xl text-xs leading-5 text-slate-400 line-clamp-2">{{ $project->description }}</p>
                            @endif

                            {{-- Numeric Metrics Grid --}}
                            <div class="grid grid-cols-3 gap-2 sm:grid-cols-6 sm:gap-2.5 max-w-2xl">
                                <div class="rounded-xl border border-white/10 bg-white/5 p-2.5 text-center">
                                    <p class="text-[9px] font-bold uppercase tracking-wider text-slate-400">{{ __('Buildings') }}</p>
                                    <p class="mt-1 text-base font-black tabular-nums text-white">{{ number_format((int) ($project->buildings_count ?? 0)) }}</p>
                                </div>
                                <div class="rounded-xl border border-white/10 bg-white/5 p-2.5 text-center">
                                    <p class="text-[9px] font-bold uppercase tracking-wider text-slate-400">{{ __('Floors/Building') }}</p>
                                    <p class="mt-1 text-base font-black tabular-nums text-white">
                                        @if(($project->buildings_count ?? 0) > 0)
                                            {{ number_format((int) ceil(($project->floors_count ?? 0) / $project->buildings_count)) }}
                                        @else
                                            {{ number_format((int) ($project->floors_count ?? 0)) }}
                                        @endif
                                    </p>
                                </div>
                                <div class="rounded-xl border border-white/10 bg-white/5 p-2.5 text-center">
                                    <p class="text-[9px] font-bold uppercase tracking-wider text-slate-400">{{ __('Total Units') }}</p>
                                    <p class="mt-1 text-base font-black tabular-nums text-white">{{ number_format($pTotal) }}</p>
                                </div>
                                <div class="rounded-xl border border-emerald-500/20 bg-emerald-500/5 p-2.5 text-center">
                                    <p class="text-[9px] font-bold uppercase tracking-wider text-emerald-300">{{ __('Available') }}</p>
                                    <p class="mt-1 text-base font-black tabular-nums text-emerald-400">{{ number_format($pAvailable) }}</p>
                                </div>
                                <div class="rounded-xl border border-amber-500/20 bg-amber-500/5 p-2.5 text-center">
                                    <p class="text-[9px] font-bold uppercase tracking-wider text-amber-300">{{ __('Reserved') }}</p>
                                    <p class="mt-1 text-base font-black tabular-nums text-amber-400">{{ number_format($pReserved) }}</p>
                                </div>
                                <div class="rounded-xl border border-rose-500/20 bg-rose-500/5 p-2.5 text-center">
                                    <p class="text-[9px] font-bold uppercase tracking-wider text-rose-300">{{ __('Sold') }}</p>
                                    <p class="mt-1 text-base font-black tabular-nums text-rose-400">{{ number_format($pSold) }}</p>
                                </div>
                            </div>

                            {{-- Buildings Chips --}}
                            @if($project->buildings->isNotEmpty())
                                <div class="space-y-1.5 pt-1">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('Project Buildings') }}</p>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($project->buildings as $bld)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-950/60 border border-white/10 text-xs font-semibold text-slate-300">
                                                <span>{{ $bld->name }}</span>
                                                <span class="text-[10px] text-slate-500">({{ (int) $bld->floors_count }} {{ __('floors') }})</span>
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Action Buttons & Occupancy Bar --}}
                        <div class="flex shrink-0 flex-col gap-3 lg:items-end w-full lg:w-72">
                            <div class="flex flex-wrap gap-2 w-full lg:justify-end">
                                @can('update', $project)
                                    <a href="{{ route('dashboard.projects.edit', $project) }}" class="app-button p-2.5" title="{{ __('Edit & Building Matrix') }}">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke-width="1.8"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke-width="1.8"/></svg>
                                    </a>
                                @endcan
                                @can('create', App\Models\Unit::class)
                                    <a href="{{ route('dashboard.projects.units.create', $project) }}" class="app-button--ghost p-2.5" title="{{ __('Add Unit') }}">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round"/></svg>
                                    </a>
                                @endcan
                                @hasanyrole(['Administrator', 'Owner', 'Sales Manager', 'Data Entry', 'Accountant'])
                                    <a href="{{ route('dashboard.projects.layout', $project) }}" class="app-button--ghost p-2.5" title="{{ __('3D Layout') }}">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m4 15 8-4 8 4-8 4-8-4Z" stroke-width="1.8" stroke-linejoin="round"/><path d="m4 9 8-4 8 4M4 15v4l8 4 8-4v-4" stroke-width="1.8" stroke-linejoin="round"/></svg>
                                    </a>
                                @endhasanyrole
                                <button type="button" @click="exportOpen = true" class="app-button--ghost p-2.5 text-emerald-400 hover:text-emerald-300" title="{{ __('Export unit report') }}">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-width="1.8"/><path d="M14 2v6h6M8 13h8M8 17h8M8 9h2" stroke-width="1.8" stroke-linecap="round"/></svg>
                                </button>
                                <a href="{{ route('dashboard.site-plan', ['project_id' => $project->id]) }}" class="app-button--ghost p-2.5 text-slate-400 hover:text-white" title="{{ __('Project Map') }}" aria-label="{{ __('Project Map') }}">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 18 3 21V6l6-3 6 3 6-3v15l-6 3-6-3Z" stroke-width="1.8" stroke-linejoin="round"/><path d="M9 3v15M15 6v15" stroke-width="1.8"/></svg>
                                </a>
                                @can('delete', $project)
                                    <form id="delete-project-{{ $project->id }}" method="POST" action="{{ route('dashboard.projects.destroy', $project) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmAction('{{ __('Delete project') }}', '{{ __('Are you sure you want to delete :name? Any related units, offers, reservations, or deals must be removed first.', ['name' => $project->name]) }}', () => document.getElementById('delete-project-{{ $project->id }}').submit(), '{{ __('Delete') }}', true)" class="app-button app-button--danger p-2.5 text-xs" title="{{ __('Delete') }}">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" stroke-width="1.8"/></svg>
                                        </button>
                                    </form>
                                @endcan
                            </div>

                            {{-- Occupancy Meter Card --}}
                            <div class="w-full rounded-2xl border border-white/10 bg-white/5 p-3.5 space-y-2.5">
                                <div class="flex items-center justify-between text-xs text-slate-400">
                                    <span class="font-semibold">{{ __('Occupancy Rate') }}</span>
                                    <span class="font-black text-white tabular-nums">{{ $pOccupancy }}%</span>
                                </div>
                                <div class="h-2 w-full overflow-hidden rounded-full bg-white/5 flex">
                                    @if($pTotal > 0)
                                        <div class="bg-emerald-500 transition-all duration-500" style="width: {{ round($pAvailable / $pTotal * 100) }}%" title="{{ __('Available') }}: {{ $pAvailable }}"></div>
                                        <div class="bg-amber-500 transition-all duration-500" style="width: {{ round($pReserved / $pTotal * 100) }}%" title="{{ __('Reserved') }}: {{ $pReserved }}"></div>
                                        <div class="bg-rose-500 transition-all duration-500" style="width: {{ round($pSold / $pTotal * 100) }}%" title="{{ __('Sold') }}: {{ $pSold }}"></div>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between text-[10px] text-slate-400 pt-1">
                                    <span class="text-emerald-400 font-bold">{{ $pAvailable }} {{ __('available') }}</span>
                                    <span class="text-amber-400 font-bold">{{ $pReserved }} {{ __('reserved') }}</span>
                                    <span class="text-rose-400 font-bold">{{ $pSold }} {{ __('sold') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <template x-teleport="body">
                        <div x-show="exportOpen" x-cloak x-transition.opacity class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/80 p-4 backdrop-blur-sm" @keydown.escape.window="exportOpen = false" @click.self="exportOpen = false">
                            <div x-show="exportOpen" x-transition class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-3xl border border-white/10 bg-slate-900 p-6 shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="export-title-{{ $project->id }}">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-500/15 text-emerald-400">
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-width="1.8"/><path d="M14 2v6h6M8 13h8M8 17h8M8 9h2" stroke-width="1.8" stroke-linecap="round"/></svg>
                                        </span>
                                        <div>
                                            <h3 id="export-title-{{ $project->id }}" class="text-lg font-bold text-white">{{ __('Export unit report') }}</h3>
                                            <p class="mt-1 text-xs text-slate-400">{{ $project->name }}</p>
                                        </div>
                                    </div>
                                    <button type="button" @click="exportOpen = false" class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/5 text-slate-400 transition hover:bg-white/10 hover:text-white" aria-label="{{ __('Close') }}">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 6 6 18M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg>
                                    </button>
                                </div>

                                <form action="{{ route('dashboard.projects.units-report', $project) }}" method="GET" class="mt-6 space-y-5" @submit="exportOpen = false">
                                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                        <label class="flex cursor-pointer items-start gap-3">
                                            <input type="checkbox" name="include_hidden" value="1" x-model="includeHidden" class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950 text-emerald-500 focus:ring-emerald-500/30">
                                            <span>
                                                <span class="block text-sm font-semibold text-white">{{ __('Include hidden buildings') }}</span>
                                                <span class="mt-1 block text-xs leading-5 text-slate-400">{{ __('Export hidden buildings together with the buildings currently visible on the website.') }}</span>
                                            </span>
                                        </label>
                                    </div>

                                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                        <div class="mb-3 flex items-center justify-between gap-3">
                                            <div>
                                                <span class="block text-sm font-semibold text-white">{{ __('Excel columns') }}</span>
                                                <span class="mt-1 block text-xs text-slate-400">{{ __('Choose the columns to include in the exported report.') }}</span>
                                            </div>
                                            <button type="button" class="text-xs font-semibold text-brand-300 hover:text-brand-200" @click="selectedColumns = selectedColumns.length === allExportColumns.length ? [] : [...allExportColumns]" x-text="selectedColumns.length === allExportColumns.length ? '{{ __('Clear all') }}' : '{{ __('Select all') }}'"></button>
                                        </div>
                                        <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                            @foreach($exportColumns as $column => $label)
                                                <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/5 bg-slate-950/40 px-3 py-2 text-xs text-slate-300 transition hover:border-brand-500/30 hover:text-white">
                                                    <input type="checkbox" name="columns[]" value="{{ $column }}" x-model="selectedColumns" class="h-4 w-4 rounded border-white/20 bg-slate-950 text-brand-500 focus:ring-brand-500/30">
                                                    <span>{{ $label }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        <p x-show="selectedColumns.length === 0" x-cloak class="mt-3 text-xs font-semibold text-rose-400">{{ __('Select at least one column.') }}</p>
                                    </div>

                                    <div class="rounded-2xl border border-brand-500/20 bg-brand-500/5 px-4 py-3 text-xs leading-5 text-slate-300">
                                        {{ __('The report includes only the columns selected above.') }}
                                    </div>
                                    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                                        <button type="button" @click="exportOpen = false" class="app-button--ghost justify-center">{{ __('Cancel') }}</button>
                                        <button type="submit" :disabled="selectedColumns.length === 0" :class="selectedColumns.length === 0 ? 'cursor-not-allowed opacity-50' : ''" class="app-button justify-center bg-emerald-600 hover:bg-emerald-500">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            {{ __('Export XLSM') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </template>
                </article>
            @empty
                <div class="flex flex-col items-center justify-center gap-4 rounded-3xl border border-dashed border-white/10 bg-white/[0.02] py-16 text-center">
                    <span class="text-5xl">🏗️</span>
                    <div>
                        <h3 class="text-lg font-bold text-white">{{ __('No Projects Found') }}</h3>
                        <p class="text-xs text-slate-400 mt-1">{{ __('Start by creating your first real estate development project.') }}</p>
                    </div>
                    @can('create', App\Models\Project::class)
                        <a href="{{ route('dashboard.projects.create') }}" class="app-button">{{ __('+ Create First Project') }}</a>
                    @endcan
                </div>
            @endforelse

            {{-- Pagination --}}
            @if ($projects->hasPages())
                <div class="pt-4">
                    {{ $projects->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection
