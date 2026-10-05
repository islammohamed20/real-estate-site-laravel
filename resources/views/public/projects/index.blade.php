@extends('layouts.public')

@section('content')
    {{-- Hero Section --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-b from-brand-950 via-slate-900 to-slate-950 px-6 py-10 text-center shadow-2xl sm:py-14">
        <div class="pointer-events-none absolute inset-0 bg-grid opacity-30"></div>
        <div class="relative z-10 mx-auto max-w-3xl space-y-4">
            <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-400/30 bg-amber-500/10 px-4 py-1.5 text-xs font-semibold text-amber-300 backdrop-blur-md">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3Z" stroke-width="1.8" stroke-linejoin="round"/></svg> {{ __('نخبة المشروعات والوحدات العقارية') }}
            </span>
            <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
                {{ __('دليل الوحدات والمشاريع المتاحة') }}
            </h1>
            <p class="mx-auto max-w-2xl text-sm leading-relaxed text-slate-300 sm:text-base">
                {{ __('اختر المشروع ثم العمارة ثم الدور لاستكشاف الوحدات المتاحة.') }}
            </p>
        </div>
    </section>

    {{-- Breadcrumb / Stepper Navigation --}}
    @php
        $crumbBase = ['type' => $currentType, 'rooms' => $currentRooms, 'q' => $currentSearch];
        $crumbParams = array_filter($crumbBase, fn($v) => $v !== '');
    @endphp
    <nav class="mt-6 flex flex-wrap items-center gap-1.5 text-xs sm:text-sm" aria-label="{{ __('Breadcrumb') }}">
        <a href="{{ route('public.projects.index', $crumbParams) }}" class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 font-semibold transition {{ empty($currentProject) ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30' : 'bg-white/5 text-slate-300 hover:bg-white/10' }}">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ __('المشاريع') }}
        </a>
        @if ($selectedProject)
            <svg class="h-4 w-4 text-slate-500 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 18l6-6-6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <a href="{{ route('public.projects.index', array_merge($crumbParams, ['project' => $currentProject])) }}" class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 font-semibold transition {{ empty($currentBuilding) ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30' : 'bg-white/5 text-slate-300 hover:bg-white/10' }}">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 21V7l8-4 8 4v14M9 21v-4h6v4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                {{ $selectedProject->name }}
            </a>
        @endif
        @if ($selectedBuilding)
            <svg class="h-4 w-4 text-slate-500 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 18l6-6-6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <a href="{{ route('public.projects.index', array_merge($crumbParams, ['project' => $currentProject, 'building' => $currentBuilding])) }}" class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 font-semibold transition {{ empty($currentFloor) ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30' : 'bg-white/5 text-slate-300 hover:bg-white/10' }}">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 3h18v18H3zM3 9h18M3 15h18" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                {{ $selectedBuilding->name }}
            </a>
        @endif
        @if ($selectedFloor)
            <svg class="h-4 w-4 text-slate-500 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 18l6-6-6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span class="inline-flex items-center gap-1.5 rounded-xl bg-brand-600 px-3 py-1.5 font-semibold text-white shadow-md shadow-brand-600/30">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 3v18M3 12h18" stroke-width="1.8" stroke-linecap="round"/></svg>
                {{ __('الدور') }} {{ $selectedFloor->number }}
            </span>
        @endif
    </nav>

    {{-- Floating Search & Filter Bar --}}
    <section class="mt-4 relative z-20 mx-auto max-w-5xl px-2">
        <form method="GET" action="{{ route('public.projects.index') }}" class="app-card space-y-4 p-4 sm:p-6 shadow-2xl backdrop-blur-2xl">
            {{-- Preserve hierarchy filters --}}
            <input type="hidden" name="project" value="{{ $currentProject }}">
            <input type="hidden" name="building" value="{{ $currentBuilding }}">
            <input type="hidden" name="floor" value="{{ $currentFloor }}">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                {{-- Search Input --}}
                <div class="relative flex-1">
                    <svg class="pointer-events-none absolute right-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400 rtl:right-3.5 rtl:left-auto ltr:left-3.5 ltr:right-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="11" cy="11" r="7" stroke-width="1.8"/>
                        <path d="m20 20-3.5-3.5" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    <input
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="{{ __('بحث باسم المشروع، الحي، أو نوع الوحدة...') }}"
                        class="app-input w-full pr-10 pl-4 text-sm rtl:pr-10 rtl:pl-4 ltr:pl-10 ltr:pr-4"
                    >
                </div>

                {{-- Bedroom Filters --}}
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
                    <span class="text-xs font-semibold text-slate-400 shrink-0">{{ __('عدد الغرف:') }}</span>
                    @foreach (['' => __('الكل'), '2' => '+2', '3' => '+3', '4' => '+4', '5+' => '+5'] as $roomVal => $roomLabel)
                        <a
                            href="{{ route('public.projects.index', array_merge($crumbParams, ['project' => $currentProject, 'building' => $currentBuilding, 'floor' => $currentFloor], array_filter(['rooms' => $roomVal]))) }}"
                            class="inline-flex h-9 min-w-9 items-center justify-center rounded-xl px-3 text-xs font-bold transition-all {{ request('rooms') === (string)$roomVal ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 ring-2 ring-brand-400' : 'bg-white/5 text-slate-300 hover:bg-white/10' }}"
                        >
                            {{ $roomLabel }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Category / Type Pills --}}
            <div class="flex items-center gap-2 overflow-x-auto border-t border-white/10 pt-3 no-scrollbar">
                <a
                    href="{{ route('public.projects.index', array_merge($crumbParams, ['project' => $currentProject, 'building' => $currentBuilding, 'floor' => $currentFloor])) }}"
                    class="inline-flex shrink-0 items-center gap-1.5 rounded-xl px-4 py-2 text-xs font-semibold transition-all {{ empty($currentType) ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30' : 'bg-white/5 text-slate-300 hover:bg-white/10' }}"
                >
                    {{ __('جميع الأنواع') }}
                </a>
                @foreach ($unitTypes as $typeKey)
                    <a
                        href="{{ route('public.projects.index', array_merge($crumbParams, ['project' => $currentProject, 'building' => $currentBuilding, 'floor' => $currentFloor, 'type' => $typeKey])) }}"
                        class="inline-flex shrink-0 items-center gap-1.5 rounded-xl px-4 py-2 text-xs font-semibold transition-all {{ $currentType === (string)$typeKey ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30' : 'bg-white/5 text-slate-300 hover:bg-white/10' }}"
                    >
                        {{ $typeKey }}
                    </a>
                @endforeach
            </div>
        </form>
    </section>

    {{-- Step 1: Project Selection --}}
    @if (empty($currentProject) && $projects->isNotEmpty())
        <section class="mt-10">
            <div class="mb-6 flex items-center gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-brand-500/15 text-brand-300 ring-1 ring-brand-500/20 text-sm font-bold">1</span>
                <div>
                    <p class="mobile-section-title">{{ __('الخطوة الأولى') }}</p>
                    <h2 class="text-xl font-bold text-white sm:text-2xl">{{ __('اختر المشروع') }}</h2>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($projects as $project)
                    @php
                        $projectImage = $project->cover_image_path
                            ?: collect($project->images ?? [])->filter(fn ($img) => is_string($img))->first();
                        $pImg = $projectImage
                            ? asset('storage/'.$projectImage)
                            : 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80';
                        $pStatusLabel = match ($project->status) {
                            'active' => __('نشط'),
                            'launching' => __('Launching'),
                            'sold' => __('مباع'),
                            default => __('Draft'),
                        };
                        $projectUnitsCount = (int) $project->units_count;
                        $projectAvailableCount = (int) $project->available_units_count;
                        $projectBuildingsCount = (int) $project->buildings_count;
                        $projectAvailability = $projectUnitsCount > 0
                            ? (int) round(($projectAvailableCount / $projectUnitsCount) * 100)
                            : 0;
                    @endphp
                    <article class="app-card card-hover group flex flex-col overflow-hidden p-0 transition-all duration-300">
                        <div class="relative h-48 w-full overflow-hidden bg-slate-900">
                            <img src="{{ $pImg }}" alt="{{ $project->name }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent"></div>
                            <div class="absolute top-3 right-3 flex flex-wrap gap-1.5">
                                <span class="badge badge-brand">{{ $pStatusLabel }}</span>
                                @if ($project->current_phase)
                                    <span class="badge badge-success">{{ $project->current_phase }}</span>
                                @endif
                            </div>
                            <div class="absolute bottom-3 right-3 text-xs text-slate-300 font-semibold">
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M4 21V7l8-4 8 4v14M9 21v-4h6v4M8 7h.01M12 7h.01M16 7h.01M8 11h.01M12 11h.01M16 11h.01" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    {{ $projectUnitsCount }} {{ __('وحدة') }}
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-1 flex-col p-5 space-y-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="mb-1 text-[10px] font-bold uppercase tracking-[0.18em] text-brand-400">{{ $project->code ?: __('Venecia Developments') }}</p>
                                    <h3 class="text-xl font-bold text-white group-hover:text-brand-300 transition-colors">
                                        {{ $project->name }}
                                    </h3>
                                </div>
                                <span class="shrink-0 rounded-full bg-emerald-500/10 px-2.5 py-1 text-[10px] font-bold text-emerald-300">{{ $projectAvailableCount }} {{ __('متاح') }}</span>
                            </div>
                            <p class="flex items-center gap-1.5 text-xs text-slate-400">
                                <svg class="h-4 w-4 shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M12 21s-7-5.5-7-11a7 7 0 1 1 14 0c0 5.5-7 11-7 11Z" stroke-width="1.8"/>
                                    <circle cx="12" cy="10" r="2.5" stroke-width="1.8"/>
                                </svg>
                                {{ $project->location ?? __('المربع الذهبي، القاهرة الجديدة') }}
                            </p>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-3">
                                    <p class="text-[10px] text-slate-500">{{ __('العمارات') }}</p>
                                    <p class="mt-1 text-lg font-extrabold text-white">{{ $projectBuildingsCount }}</p>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-3">
                                    <p class="text-[10px] text-slate-500">{{ __('الوحدات المتاحة') }}</p>
                                    <p class="mt-1 text-lg font-extrabold text-emerald-300">{{ $projectAvailableCount }}<span class="text-xs font-normal text-slate-500"> / {{ $projectUnitsCount }}</span></p>
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <div class="flex justify-between text-[10px] text-slate-500"><span>{{ __('نسبة التوافر') }}</span><span>{{ $projectAvailability }}%</span></div>
                                <div class="h-1.5 overflow-hidden rounded-full bg-white/10"><div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-brand-400" style="width: {{ $projectAvailability }}%"></div></div>
                            </div>
                            <div class="mt-auto grid grid-cols-2 gap-2">
                                <a href="{{ route('public.projects.show', $project->slug) }}" class="app-button--ghost justify-center text-[11px] py-2.5" onclick="event.stopPropagation()">
                                    {{ __('تفاصيل المشروع') }}
                                </a>
                                <a href="{{ route('public.projects.index', array_merge($crumbParams, ['project' => $project->id])) }}" class="app-button justify-center text-[11px] py-2.5" onclick="event.stopPropagation()">
                                    {{ __('استكشف العمارات') }} <span class="rtl:rotate-180">→</span>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Step 2: Building Selection (only when project is selected) --}}
    @if (! empty($currentProject) && $buildings->isNotEmpty())
        <section class="mt-8">
            <div class="mb-6 flex items-center gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-brand-500/15 text-brand-300 ring-1 ring-brand-500/20 text-sm font-bold">2</span>
                <div>
                    <p class="mobile-section-title">{{ __('الخطوة الثانية') }}</p>
                    <h2 class="text-xl font-bold text-white sm:text-2xl">{{ __('اختر العمارة') }} — {{ $selectedProject?->name }}</h2>
                </div>
            </div>

            <div class="grid grid-cols-4 gap-2 sm:grid-cols-6 sm:gap-3 lg:grid-cols-8">
                @foreach ($buildings as $building)
                    @php
                        $isActive = $currentBuilding === (string)$building->id;
                        $availCount = (int) $building->available_units_count;
                        $totalCount = (int) $building->units_count;
                        $floorsCount = (int) $building->floors_count;
                        $availability = $totalCount > 0 ? (int) round(($availCount / $totalCount) * 100) : 0;
                    @endphp
                    <a
                        href="{{ route('public.projects.index', array_merge($crumbParams, ['project' => $currentProject, 'building' => $building->id])) }}#units-results"
                        class="app-card card-hover group flex min-w-0 flex-col items-center justify-center gap-2 p-2.5 text-center transition-all duration-300 sm:gap-2.5 sm:p-3 {{ $isActive ? 'ring-2 ring-brand-500 bg-brand-500/10' : '' }}"
                    >
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-500/15 text-brand-300 ring-1 ring-brand-500/20 transition group-hover:scale-110 sm:h-11 sm:w-11 sm:rounded-2xl">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 21V7l8-4 8 4v14M9 21v-4h6v4M8 7h.01M12 7h.01M16 7h.01M8 11h.01M12 11h.01M16 11h.01" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <div class="space-y-1">
                            <h3 class="truncate text-sm font-bold text-white group-hover:text-brand-300 transition-colors sm:text-base">{{ $building->name }}</h3>
                            @if ($building->code)
                                <p class="truncate text-[9px] text-slate-500">{{ $building->code }}</p>
                            @endif
                        </div>
                        <div class="flex w-full items-center justify-between gap-1 text-[10px] text-slate-400">
                            <span><strong class="text-white">{{ $floorsCount }}</strong> {{ __('دور') }}</span>
                            <span class="font-bold text-emerald-300">{{ $availCount }} {{ __('متاح') }}</span>
                        </div>
                        <div class="w-full space-y-1">
                            <div class="flex justify-between text-[9px] text-slate-500"><span>{{ __('التوافر') }}</span><span>{{ $availability }}%</span></div>
                            <div class="h-1 overflow-hidden rounded-full bg-white/10"><div class="h-full rounded-full bg-emerald-500" style="width: {{ $availability }}%"></div></div>
                        </div>
                        @if ($isActive)
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-brand-300">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12l5 5L20 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                {{ __('محدد') }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Step 3: Floor Selection (only when building is selected) --}}
    @if (! empty($currentBuilding) && $floors->isNotEmpty())
        <section class="mt-8">
            <div class="mb-6 flex items-center gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-brand-500/15 text-brand-300 ring-1 ring-brand-500/20 text-sm font-bold">3</span>
                <div>
                    <p class="mobile-section-title">{{ __('الخطوة الثالثة') }}</p>
                    <h2 class="text-xl font-bold text-white sm:text-2xl">{{ __('اختر الدور') }} — {{ $selectedBuilding?->name }}</h2>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 sm:gap-3">
                {{-- All floors tab --}}
                <a
                    href="{{ route('public.projects.index', array_merge($crumbParams, ['project' => $currentProject, 'building' => $currentBuilding])) }}"
                    class="inline-flex shrink-0 items-center gap-2 rounded-2xl border px-5 py-3 text-sm font-semibold transition-all duration-200 {{ empty($currentFloor) ? 'border-brand-500/60 bg-brand-500/15 text-white shadow-md shadow-brand-500/10' : 'border-white/10 bg-white/5 text-slate-300 hover:border-brand-400/30 hover:bg-white/10 hover:text-white' }}"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 6h18M3 12h18M3 18h18" stroke-width="2" stroke-linecap="round"/></svg>
                    <span>{{ __('كل الأدوار') }}</span>
                </a>

                @foreach ($floors as $floor)
                    @php
                        $isActive = $currentFloor === (string)$floor->id;
                        $floorLabel = $floor->number === 0 ? __('أرضي') : ($floor->number === 1 ? __('أول') : ($floor->number === 2 ? __('ثاني') : ($floor->number === 3 ? __('ثالث') : ($floor->number === 4 ? __('رابع') : ($floor->number === 5 ? __('خامس') : __('دور') . ' ' . $floor->number)))));
                        $floorUnits = $floor->units_count ?? 0;
                    @endphp
                    <a
                        href="{{ route('public.projects.index', array_merge($crumbParams, ['project' => $currentProject, 'building' => $currentBuilding, 'floor' => $floor->id])) }}#units-results"
                        class="group inline-flex shrink-0 items-center gap-2 rounded-2xl border px-5 py-3 text-sm font-semibold transition-all duration-200 {{ $isActive ? 'border-brand-500/60 bg-brand-500/15 text-white shadow-md shadow-brand-500/10' : 'border-white/10 bg-white/5 text-slate-300 hover:border-brand-400/30 hover:bg-white/10 hover:text-white' }}"
                    >
                        <svg class="h-4 w-4 {{ $isActive ? 'text-brand-300' : 'text-slate-400 group-hover:text-brand-300' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 3h18v18H3zM3 9h18M3 15h18" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>{{ $floorLabel }}</span>
                        @if ($floorUnits > 0)
                            <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-emerald-500/20 px-1.5 text-[10px] font-bold text-emerald-300 {{ $isActive ? 'ring-1 ring-emerald-400/30' : '' }}">{{ $floorUnits }}</span>
                        @endif
                        @if ($isActive)
                            <svg class="h-3.5 w-3.5 text-brand-300" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12l5 5L20 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        @endif
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Step 4: Units Results --}}
    @if (! empty($currentBuilding))
        <section class="mt-10">
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-brand-500/15 text-brand-300 ring-1 ring-brand-500/20 text-sm font-bold">4</span>
                    <div>
                        <p class="mobile-section-title">{{ __('الخطوة الرابعة') }}</p>
                        <h2 class="text-xl font-bold text-white sm:text-2xl">
                            {{ __('Units') }}
                            <span class="text-sm font-normal text-slate-400">({{ $units->total() }} {{ __('وحدة') }})</span>
                        </h2>
                    </div>
                </div>
                @if (request()->hasAny(['q', 'type', 'rooms', 'building', 'floor']))
                    <a href="{{ route('public.projects.index', ['project' => $currentProject]) }}" class="text-xs text-rose-400 hover:underline">
                        <span class="inline-flex items-center gap-1">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M6 6l12 12M18 6L6 18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            {{ __('إلغاء التصفية') }}
                        </span>
                    </a>
                @endif
            </div>

            <div id="units-results" class="scroll-mt-24 grid grid-cols-2 gap-3 sm:gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($units as $unit)
                    @php
                        $price = (float) $unit->current_price;
                        $installmentYears = max(1, (int) ($unit->project?->max_installment_years ?? 5));
                        $estQuarterly = $price > 0 ? round(($price * (1 - ($defaultDownPaymentPercent / 100))) / ($installmentYears * 4)) : 0;
                        $deliveryYear = $unit->delivery_date?->year;

                        $unitTypeTag = strtoupper($unit->unit_type ?? 'APARTMENT');
                        if (str_contains($unitTypeTag, 'فيلا')) $unitTypeTag = 'VILLA';
                        elseif (str_contains($unitTypeTag, 'دوبلكس')) $unitTypeTag = 'DUPLEX';
                        elseif (str_contains($unitTypeTag, 'بنتهاوس')) $unitTypeTag = 'PENTHOUSE';
                        elseif (str_contains($unitTypeTag, 'شقة')) $unitTypeTag = 'APARTMENT';

                        $unitImage = (is_string($unit->thumbnail) && $unit->thumbnail !== '')
                            ? $unit->thumbnail
                            : collect($unit->images ?? [])->filter(fn ($img) => is_string($img))->first();
                        $imageUrls = [
                            'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80',
                            'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80',
                            'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=800&q=80',
                            'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80',
                        ];
                        $imgUrl = $unitImage
                            ? (filter_var($unitImage, FILTER_VALIDATE_URL)
                                ? $unitImage
                                : asset('storage/'.ltrim((string) preg_replace('#^/?storage/#', '', $unitImage), '/')))
                            : $imageUrls[$unit->id % count($imageUrls)];
                    @endphp

                    <article class="app-card card-hover group flex flex-col overflow-hidden p-0 transition-all duration-300">
                        {{-- Card Media Section --}}
                        <div class="relative h-36 w-full overflow-hidden bg-slate-900 sm:h-56">
                            <img
                                src="{{ $imgUrl }}"
                                alt="{{ $unit->unit_type }} {{ $unit->unit_number }}"
                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                loading="lazy"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent"></div>

                            {{-- Top Badges --}}
                            <div class="absolute top-2 inset-x-2 flex items-center justify-between gap-1 sm:top-3 sm:inset-x-3 sm:gap-2">
                                <span class="rounded-lg bg-brand-950/90 px-1.5 py-0.5 text-[9px] font-extrabold uppercase tracking-wider text-brand-200 backdrop-blur-md border border-brand-400/20 sm:px-2.5 sm:py-1 sm:text-[11px]">
                                    {{ $unitTypeTag }}
                                </span>
                                @include('partials.unit-status', ['status' => $unit->status])
                            </div>

                            {{-- Bottom Overlay Tag --}}
                            <div class="absolute bottom-2 left-2 right-2 flex items-center justify-between text-[10px] text-white sm:bottom-3 sm:left-3 sm:right-3 sm:text-xs">
                                <span class="rounded-lg bg-slate-900/80 px-1.5 py-0.5 font-medium backdrop-blur-md border border-white/10 sm:px-2.5 sm:py-1">
                                    {{ __('الاستلام:') }} <strong class="text-brand-300">{{ $deliveryYear ?? __('—') }}</strong>
                                </span>
                                @if ($unit->floor)
                                    <span class="rounded-lg bg-slate-900/80 px-1.5 py-0.5 font-medium backdrop-blur-md border border-white/10 sm:px-2.5 sm:py-1">
                                        {{ __('الدور') }} {{ $unit->floor->number === 0 ? __('أرضي') : $unit->floor->number }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Card Content Body --}}
                        <div class="flex flex-1 flex-col p-2.5 space-y-2 sm:p-5 sm:space-y-3">
                            {{-- Developer & Subtitle --}}
                            <p class="text-[10px] font-semibold text-brand-400 line-clamp-1 sm:text-xs">
                                {{ $unit->project?->name ?? __('كمبوند راقي') }}
                            </p>

                            {{-- Main Title --}}
                            <h3 class="text-xs font-bold text-white group-hover:text-brand-300 transition-colors line-clamp-2 sm:text-lg">
                                <a href="{{ route('public.units.show', $unit->id) }}">
                                    {{ $unit->unit_type }} - {{ __('وحدة') }} {{ $unit->unit_number }}
                                </a>
                            </h3>

                            {{-- Specs Icons Row --}}
                            <div class="flex flex-wrap items-center gap-x-2 gap-y-1 border-y border-white/10 py-1.5 text-[10px] text-slate-300 sm:flex-nowrap sm:py-3 sm:text-xs">
                                <span class="flex items-center gap-0.5 sm:gap-1">
                                    <svg class="h-3 w-3 text-slate-400 sm:h-4 sm:w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="1.8"/><path d="M3 9h18M9 21V9" stroke-width="1.8"/></svg>
                                    <strong>{{ number_format((float)$unit->area) }}</strong> {{ __('م²') }}
                                </span>
                                @if ($unit->bedrooms)
                                    <span class="flex items-center gap-0.5 sm:gap-1">
                                        <svg class="h-3 w-3 text-slate-400 sm:h-4 sm:w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M2 4v16M2 8h20v12M2 17h20M6 8v9" stroke-width="1.8"/></svg>
                                        <strong>{{ $unit->bedrooms }}</strong>{{ __('غرف') }}
                                    </span>
                                @endif
                                @if ($unit->bathrooms)
                                    <span class="flex items-center gap-0.5 sm:gap-1">
                                        <svg class="h-3 w-3 text-slate-400 sm:h-4 sm:w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 12h16a1 1 0 0 1 1 1v3a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4v-3a1 1 0 0 1 1-1Z" stroke-width="1.8"/><path d="M6 12V5a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v7" stroke-width="1.8"/></svg>
                                        <strong>{{ $unit->bathrooms }}</strong>{{ __('حمام') }}
                                    </span>
                                @endif
                            </div>

                            {{-- Financial Summary Box --}}
                            <div class="rounded-xl border border-white/10 bg-white/5 p-2 space-y-1 sm:rounded-2xl sm:p-3 sm:space-y-1.5">
                                <div class="flex items-center justify-between text-[10px] sm:text-xs">
                                    <span class="text-slate-400">{{ __('السعر:') }}</span>
                                    <strong class="text-xs font-bold text-white sm:text-sm">{{ number_format($price) }} {{ __('ج.م') }}</strong>
                                </div>
                                @if ($unit->status?->value === 'available')
                                    <div class="flex items-center justify-between text-[10px] border-t border-white/5 pt-1 sm:text-xs sm:pt-1.5">
                                        <span class="text-slate-400">{{ __('قسط:') }}</span>
                                        <strong class="text-[10px] font-bold text-emerald-400 sm:text-xs">{{ number_format($estQuarterly) }} {{ __('ج.م') }}</strong>
                                    </div>
                                @endif
                            </div>

                            {{-- Action Buttons --}}
                            <div class="{{ $unit->status?->value === 'available' ? 'grid grid-cols-2' : 'flex' }} gap-1.5 pt-1 mt-auto sm:gap-2">
                                <a
                                    href="{{ route('public.units.show', $unit->id) }}"
                                    class="app-button--ghost text-[10px] justify-center py-2 sm:text-xs sm:py-2.5 min-h-9 sm:min-h-10 {{ $unit->status?->value === 'available' ? '' : 'w-full' }}"
                                >
                                    {{ __('تفاصيل') }}
                                </a>
                                @if ($unit->status?->value === 'available')
                                    <a
                                        href="{{ route('installments.index', ['unit_id' => $unit->id]) }}"
                                        class="app-button text-[10px] justify-center gap-1 py-2 sm:text-xs sm:py-2.5 min-h-9 sm:min-h-10"
                                    >
                                        <span>{{ __('احسب') }}</span>
                                        <svg class="hidden h-3.5 w-3.5 rtl:rotate-180 sm:block" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path d="M5 12h14M13 6l6 6-6 6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="app-card col-span-full flex flex-col items-center py-16 text-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-white/5 text-slate-400">
                            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <circle cx="11" cy="11" r="7" stroke-width="1.8"/>
                                <path d="m20 20-3.5-3.5" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-bold text-white">{{ __('لم نجد وحدات مطابقة') }}</h3>
                        <p class="mt-1 text-sm text-slate-400">{{ __('جرب اختيار دور أو عمارة أخرى.') }}</p>
                        @if (! empty($currentBuilding))
                            <a href="{{ route('public.projects.index', array_merge($crumbParams, ['project' => $currentProject, 'building' => $currentBuilding])) }}" class="app-button mt-6">{{ __('عرض كل أدوار هذه العمارة') }}</a>
                        @else
                            <a href="{{ route('public.projects.index', array_merge($crumbParams, ['project' => $currentProject])) }}" class="app-button mt-6">{{ __('عرض كل وحدات المشروع') }}</a>
                        @endif
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if ($units->hasPages())
                <div class="mt-10 flex justify-center">
                    {{ $units->links() }}
                </div>
            @endif
        </section>
    @endif
@endsection
