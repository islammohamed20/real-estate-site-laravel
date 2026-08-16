@extends('layouts.dashboard')

@section('content')
    @php
        $totalUnits = (int) ($stats['units'] ?? 0);
        $available = (int) ($stats['available_units'] ?? 0);
        $featured = (int) ($stats['featured_units'] ?? 0);
        $projectCollection = collect($projects->items());
        $projectCount = count($projects);
    @endphp

    @if (session('status'))
        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="flex items-center gap-3 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12l5 5L20 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>{{ session('status') }}</span>
            <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-200">×</button>
        </div>
    @endif

    @error('delete')
        <div class="flex items-center gap-3 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-300">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke-width="1.8"/><path d="M12 9v4M12 17h.01" stroke-width="1.8" stroke-linecap="round"/></svg>
            <span>{{ $message }}</span>
        </div>
    @enderror

    <div class="space-y-6">
        {{-- Hero --}}
        <section class="dashboard-hero-card p-5 sm:p-8">
            <div class="absolute -right-16 -top-16 h-48 w-48 rounded-full bg-brand-500/20 blur-3xl"></div>
            <div class="absolute -bottom-16 left-1/3 h-48 w-48 rounded-full bg-violet-500/10 blur-3xl"></div>

            <div class="relative grid gap-6 lg:grid-cols-[minmax(0,1.4fr)_minmax(16rem,0.9fr)] lg:items-end">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="badge badge-brand">{{ $projectCount }} {{ __('projects on this page') }}</span>
                        <span class="badge badge-success">{{ number_format($available) }} {{ __('available units') }}</span>
                        <span class="badge badge-warning">{{ number_format($featured) }} {{ __('featured units') }}</span>
                    </div>

                    <div>
                        <p class="mobile-section-title">{{ __('Portfolio') }}</p>
                        <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('Projects') }}</h1>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">
                            {{ __('Manage project visibility, inventory health, and public presentation from a polished command view.') }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('dashboard.projects.create') }}" class="app-button">{{ __('+ New Project') }}</a>
                        <a href="{{ route('dashboard.reports.index') }}" class="app-button--ghost">{{ __('Open Reports') }}</a>
                        <a href="{{ route('dashboard.home') }}" class="app-button--ghost">{{ __('Back to Dashboard') }}</a>
                    </div>
                </div>

                <div class="grid gap-2.5 sm:grid-cols-3">
                    <div class="stagger-item rounded-xl border border-white/10 bg-white/5 px-3.5 py-3 shadow-lg shadow-black/10 backdrop-blur-xl transition-all duration-300 hover:-translate-y-0.5 hover:border-brand-500/30" style="animation-delay:80ms">
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-brand-500/25 to-violet-600/15 text-brand-300 ring-1 ring-brand-500/20">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 21h18M3 7v1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7M4 21V4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v17" stroke-width="1.8"/></svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-xl font-bold leading-tight tabular-nums text-white">{{ number_format($stats['projects'] ?? 0) }}</p>
                                <p class="truncate text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ __('Projects') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="stagger-item rounded-xl border border-white/10 bg-white/5 px-3.5 py-3 shadow-lg shadow-black/10 backdrop-blur-xl transition-all duration-300 hover:-translate-y-0.5 hover:border-violet-500/30" style="animation-delay:160ms">
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-violet-500/25 to-fuchsia-500/10 text-violet-300 ring-1 ring-violet-500/20">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 21h18M6 21V7a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v14M9 21v-4a3 3 0 0 1 3-3v0a3 3 0 0 1 3 3v4M9 10h.01M15 10h.01" stroke-width="1.8" stroke-linecap="round"/></svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-xl font-bold leading-tight tabular-nums text-white">{{ number_format($totalUnits) }}</p>
                                <p class="truncate text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ __('Total Units') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="stagger-item rounded-xl border border-white/10 bg-white/5 px-3.5 py-3 shadow-lg shadow-black/10 backdrop-blur-xl transition-all duration-300 hover:-translate-y-0.5 hover:border-emerald-500/30" style="animation-delay:240ms">
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-500/25 to-teal-500/10 text-emerald-300 ring-1 ring-emerald-500/20">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" stroke-width="1.8" stroke-linecap="round"/><path d="m9 11 3 3L22 4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-xl font-bold leading-tight tabular-nums text-emerald-400">{{ number_format($available) }}</p>
                                <p class="truncate text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ __('Available') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Project Cards --}}
        <section class="space-y-4">
            @forelse($projectCollection as $project)
                @php
                    $pTotal = (int) ($project->units_count ?? 0);
                    $pAvailable = (int) ($project->available_units_count ?? 0);
                    $pReserved = (int) ($project->reserved_units_count ?? 0);
                    $pSold = (int) ($project->sold_units_count ?? 0);
                    $pOccupied = $pReserved + $pSold;
                    $pOccupancy = $pTotal > 0 ? (int) round($pOccupied / $pTotal * 100) : 0;
                    // Cover image first, then first gallery image — prefixed exactly once.
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

                <article class="stagger-item app-card app-card--gradient overflow-hidden transition-all hover:border-white/20 hover:bg-white/[0.08]" style="animation-delay:{{ 120 + $loop->index * 70 }}ms">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                        {{-- Project cover / primary image --}}
                        @if ($coverUrl)
                            <div class="relative h-32 w-full shrink-0 self-center overflow-hidden rounded-2xl border border-white/10 lg:h-32 lg:w-56 lg:self-start">
                                <img src="{{ $coverUrl }}" alt="{{ $project->name }}" class="h-full w-full object-cover" loading="lazy">
                                @if ($project->cover_image_path)
                                    <span class="absolute right-2 top-2 rounded-full bg-brand-600 px-2 py-0.5 text-[10px] font-bold text-white">{{ __('Primary') }}</span>
                                @endif
                            </div>
                        @else
                            <div class="flex h-32 w-full shrink-0 items-center justify-center self-center rounded-2xl border border-dashed border-white/10 bg-white/5 lg:h-32 lg:w-56 lg:self-start">
                                <svg class="h-8 w-8 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2" stroke-width="1.8"/><circle cx="9" cy="10" r="1.5" stroke-width="1.8"/><path d="M3 16l4.5-4.5 4 4 3.5-3.5 6 6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                        @endif

                        <div class="min-w-0 flex-1 space-y-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-xl font-bold text-white">{{ $project->name }}</h2>
                                <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                @if($project->featured)
                                    <span class="badge badge-warning">{{ __('Featured') }}</span>
                                @endif
                            </div>

                            <div class="flex flex-wrap items-center gap-3 text-sm text-slate-400">
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" stroke-width="1.8"/><circle cx="12" cy="10" r="3" stroke-width="1.8"/></svg>
                                    {{ $project->location ?? __('Location pending') }}
                                </span>
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 8v5l3 3" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="9" stroke-width="1.8"/></svg>
                                    {{ $project->updated_at ? \Illuminate\Support\Carbon::parse($project->updated_at)->diffForHumans() : __('No updates yet') }}
                                </span>
                            </div>

                            @if($project->description)
                                <p class="max-w-3xl text-sm leading-6 text-slate-400">{{ $project->description }}</p>
                            @endif

                            <div class="grid grid-cols-3 gap-2 sm:grid-cols-6 sm:gap-3 max-w-2xl">
                                <div class="rounded-xl border border-white/10 bg-white/5 p-2.5 sm:p-3">
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">{{ __('Buildings') }}</p>
                                    <p class="mt-1.5 text-lg font-bold sm:text-xl tabular-nums text-white">{{ number_format((int) ($project->buildings_count ?? 0)) }}</p>
                                </div>
                                <div class="rounded-xl border border-white/10 bg-white/5 p-2.5 sm:p-3">
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">{{ __('Floors') }}</p>
                                    <p class="mt-1.5 text-lg font-bold sm:text-xl tabular-nums text-white">{{ number_format((int) ($project->floors_count ?? 0)) }}</p>
                                </div>
                                <div class="rounded-xl border border-white/10 bg-white/5 p-2.5 sm:p-3">
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">{{ __('Total Units') }}</p>
                                    <p class="mt-1.5 text-lg font-bold sm:text-xl tabular-nums text-white">{{ number_format($pTotal) }}</p>
                                </div>
                                <div class="rounded-xl border border-emerald-500/20 bg-emerald-500/5 p-2.5 sm:p-3">
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-emerald-300">{{ __('Available') }}</p>
                                    <p class="mt-1.5 text-lg font-bold sm:text-xl tabular-nums text-white">{{ number_format($pAvailable) }}</p>
                                </div>
                                <div class="rounded-xl border border-amber-500/20 bg-amber-500/5 p-2.5 sm:p-3">
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-amber-300">{{ __('Reserved') }}</p>
                                    <p class="mt-1.5 text-lg font-bold sm:text-xl tabular-nums text-white">{{ number_format($pReserved) }}</p>
                                </div>
                                <div class="rounded-xl border border-rose-500/20 bg-rose-500/5 p-2.5 sm:p-3">
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-rose-300">{{ __('Sold') }}</p>
                                    <p class="mt-1.5 text-lg font-bold sm:text-xl tabular-nums text-white">{{ number_format($pSold) }}</p>
                                </div>
                            </div>

                            @if($project->buildings->isNotEmpty())
                                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <p class="mb-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('Buildings & Floors') }}</p>
                                    <div class="grid gap-2 sm:grid-cols-3">
                                        @foreach($project->buildings as $building)
                                            <div class="flex items-center justify-between gap-2 rounded-xl border border-white/10 bg-slate-950/40 px-3 py-2">
                                                <div class="min-w-0">
                                                    <p class="truncate text-sm font-semibold text-white">{{ $building->name }}</p>
                                                    <p class="text-[11px] text-slate-500">{{ $building->code }}</p>
                                                </div>
                                                <span class="badge badge-muted shrink-0">{{ (int) $building->floors_count }} {{ __('floors') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="flex shrink-0 flex-col gap-2 lg:items-end">
                            <div class="flex flex-wrap gap-2 lg:justify-end">
                                <a href="{{ route('public.projects.show', $project->slug) }}" class="app-button--ghost">{{ __('Public View') }}</a>
                                @hasanyrole(['Administrator', 'Sales Manager', 'Sales Executive'])
                                    <a href="{{ route('dashboard.projects.edit', $project) }}" class="app-button--ghost">{{ __('Edit') }}</a>
                                    <a href="{{ route('dashboard.projects.units.create', $project) }}" class="app-button--ghost">{{ __('+ Unit') }}</a>
                                @endhasanyrole
                                @can('delete', $project)
                                    <form id="delete-project-{{ $project->id }}" method="POST" action="{{ route('dashboard.projects.destroy', $project) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmAction('{{ __('Delete project') }}', '{{ __('Are you sure you want to delete :name? Any related units, offers, reservations, or deals must be removed first.', ['name' => $project->name]) }}', () => document.getElementById('delete-project-{{ $project->id }}').submit(), '{{ __('Delete') }}')" class="app-button app-button--danger">{{ __('Delete') }}</button>
                                    </form>
                                @endcan
                            </div>

                            <div class="w-full max-w-sm rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="flex items-center justify-between text-xs text-slate-400">
                                    <span>{{ __('Occupancy') }}</span>
                                    <span>{{ $pOccupancy }}%</span>
                                </div>
                                <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-white/5 flex">
                                    @if($pTotal > 0)
                                        <div class="bg-emerald-500 transition-all" style="width: {{ round($pAvailable / $pTotal * 100) }}%" title="{{ __('Available') }}: {{ $pAvailable }}"></div>
                                        <div class="bg-amber-500 transition-all" style="width: {{ round($pReserved / $pTotal * 100) }}%" title="{{ __('Reserved') }}: {{ $pReserved }}"></div>
                                        <div class="bg-rose-500 transition-all" style="width: {{ round($pSold / $pTotal * 100) }}%" title="{{ __('Sold') }}: {{ $pSold }}"></div>
                                    @endif
                                </div>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span class="badge badge-success">{{ $pAvailable }} {{ __('available') }}</span>
                                    <span class="badge badge-warning">{{ $pReserved }} {{ __('reserved') }}</span>
                                    <span class="badge badge-danger">{{ $pSold }} {{ __('sold') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="app-card app-card--gradient flex flex-col items-center justify-center py-16 text-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/5">
                        <svg class="h-8 w-8 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16" stroke-width="1.5"/><path d="M9 7h2M13 7h2M9 11h2M13 11h2M9 15h2M13 15h2" stroke-width="1.5" stroke-linecap="round"/></svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-white">{{ __('No projects yet') }}</h3>
                    <p class="mt-1 max-w-sm text-sm text-slate-400">{{ __('Create your first project to start managing your real estate portfolio.') }}</p>
                </div>
            @endforelse
        </section>

        {{-- Pagination --}}
        @if($projects->hasPages())
            <div class="flex justify-center">
                {{ $projects->links() }}
            </div>
        @endif
    </div>
@endsection
