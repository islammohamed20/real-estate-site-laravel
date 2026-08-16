@extends('layouts.dashboard')

@section('content')
    <div class="space-y-6">
        @if (session('status'))
            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="flex items-center gap-3 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12l5 5L20 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span>{{ session('status') }}</span>
                <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-200">×</button>
            </div>
        @endif

        <section class="dashboard-hero-card p-6 sm:p-8">
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-brand-500/20 blur-3xl"></div>
            <div class="absolute -bottom-20 left-1/3 h-56 w-56 rounded-full bg-violet-500/10 blur-3xl"></div>

            <div class="relative">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="badge badge-brand">{{ $banners->count() }} {{ __('banners') }}</span>
                        <span class="badge badge-success">{{ $banners->where('is_active', true)->count() }} {{ __('active') }}</span>
                    </div>

                    <div>
                        <p class="mobile-section-title">{{ __('Marketing') }}</p>
                        <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('Banners') }}</h1>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">
                            {{ __('Create promotional banners shown on the public website home page, and control the transition effect and how long each slide stays visible.') }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('dashboard.banners.create') }}" class="app-button">{{ __('+ New Banner') }}</a>
                        <a href="{{ route('home') }}" class="app-button--ghost">{{ __('View Public Site') }}</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="app-card app-card--gradient space-y-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="mobile-section-title">{{ __('Slider') }}</p>
                    <h2 class="mt-2 text-lg font-semibold text-white">{{ __('Home page banner slider') }}</h2>
                </div>
                <span class="badge badge-brand">{{ __('Fade / Slide / Zoom') }}</span>
            </div>

            <div class="space-y-3">
                @forelse ($banners as $banner)
                    <article class="rounded-2xl border border-white/10 bg-white/5 p-4 transition hover:border-white/20 hover:bg-white/[0.08]">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div class="flex min-w-0 items-center gap-4">
                                <div class="relative h-20 w-32 shrink-0 overflow-hidden rounded-xl border border-white/10 bg-slate-900">
                                    @if ($banner->image_path)
                                        <img src="{{ asset('storage/'.$banner->image_path) }}" alt="{{ $banner->title }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center">
                                            <svg class="h-8 w-8 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2" stroke-width="1.8"/><circle cx="9" cy="10" r="1.5" stroke-width="1.8"/><path d="M3 16l4.5-4.5 4 4 3.5-3.5 6 6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="truncate font-semibold text-white">{{ $banner->title ?: __('Untitled banner') }}</p>
                                        <span class="badge {{ $banner->is_active ? 'badge-success' : 'badge-muted' }}">{{ $banner->is_active ? __('Active') : __('Inactive') }}</span>
                                    </div>
                                    @if ($banner->subtitle)
                                        <p class="mt-1 line-clamp-1 text-sm text-slate-400">{{ $banner->subtitle }}</p>
                                    @endif
                                    <div class="mt-2 flex flex-wrap gap-2 text-xs text-slate-400">
                                        <span class="badge badge-muted">{{ __('Effect') }}: {{ __(ucfirst($banner->effect)) }}</span>
                                        <span class="badge badge-muted">{{ $banner->slide_duration }} {{ __('sec') }}</span>
                                        <span class="badge badge-muted">{{ __('Sort') }}: {{ $banner->sort_order }}</span>
                                        @if ($banner->link_url)
                                            <span class="badge badge-muted truncate max-w-[220px]">{{ $banner->link_url }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="flex shrink-0 gap-2">
                                <a href="{{ route('dashboard.banners.edit', $banner) }}" class="app-button--ghost">{{ __('Edit') }}</a>
                                <form id="delete-banner-{{ $banner->id }}" method="POST" action="{{ route('dashboard.banners.destroy', $banner) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmAction('{{ __('Delete banner') }}', '{{ __('Are you sure you want to delete this banner?') }}', () => document.getElementById('delete-banner-{{ $banner->id }}').submit(), '{{ __('Delete') }}')" class="app-button app-button--danger">{{ __('Delete') }}</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="flex min-h-48 flex-col items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/5 text-center text-sm text-slate-400">
                        <svg class="h-10 w-10 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2" stroke-width="1.8"/><circle cx="9" cy="10" r="1.5" stroke-width="1.8"/><path d="M3 16l4.5-4.5 4 4 3.5-3.5 6 6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <p class="mt-3 font-semibold text-white">{{ __('No banners yet') }}</p>
                        <p class="mt-1 text-slate-400">{{ __('Create your first banner to display it on the home page slider.') }}</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
