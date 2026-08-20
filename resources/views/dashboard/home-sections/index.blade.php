@extends('layouts.dashboard')

@section('content')
    <div class="space-y-6">
        <section class="dashboard-hero-card p-6 sm:p-8">
            <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="mobile-section-title">{{ __('Content Management') }}</p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('Homepage Sections') }}</h1>
                    <p class="mt-2 text-sm text-slate-400">{{ __('Control the order, visibility, and content of the public homepage sections.') }}</p>
                </div>
                <div class="flex shrink-0 items-center gap-2 rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-slate-400">
                    <span class="h-2 w-2 rounded-full bg-brand-400"></span>
                    {{ __('Drag not supported — use arrows to reorder') }}
                </div>
            </div>
        </section>

        <div class="space-y-3">
            @forelse ($sections as $section)
                <div class="app-card flex flex-col gap-4 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5 {{ ! $section->is_active ? 'opacity-60' : '' }}">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full border border-white/10 bg-white/5 px-2.5 py-0.5 text-[11px] font-bold text-slate-400">#{{ $section->sort_order }}</span>
                            <span class="badge {{ $section->is_active ? 'badge-success' : 'badge-muted' }}">
                                {{ $section->is_active ? __('Active') : __('Hidden') }}
                            </span>
                            <span class="text-[11px] font-mono uppercase text-slate-500">{{ $section->key }}</span>
                        </div>
                        <h2 class="mt-2 truncate text-lg font-bold text-white">{{ $section->title ?: __('(No title)') }}</h2>
                        @if ($section->subtitle)
                            <p class="text-sm text-brand-300">{{ $section->subtitle }}</p>
                        @endif
                        @if ($section->content)
                            <p class="mt-1 line-clamp-2 text-xs leading-relaxed text-slate-400">{{ $section->content }}</p>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-2 sm:flex-col sm:items-end lg:flex-row lg:items-center">
                        <div class="flex items-center gap-1">
                            <form method="POST" action="{{ route('dashboard.home-sections.move-up', $section) }}" class="contents">
                                @csrf
                                <button type="submit" class="touch-target inline-flex h-9 w-9 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-slate-300 transition hover:bg-white/10 hover:text-white" title="{{ __('Move up') }}">
                                    <svg class="h-4 w-4 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 15l-6-6-6 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('dashboard.home-sections.move-down', $section) }}" class="contents">
                                @csrf
                                <button type="submit" class="touch-target inline-flex h-9 w-9 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-slate-300 transition hover:bg-white/10 hover:text-white" title="{{ __('Move down') }}">
                                    <svg class="h-4 w-4 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </form>
                        </div>

                        <form method="POST" action="{{ route('dashboard.home-sections.toggle', $section) }}" class="contents">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl border border-white/10 px-3 py-2 text-xs font-semibold transition {{ $section->is_active ? 'bg-emerald-500/10 text-emerald-300 hover:bg-emerald-500/20' : 'bg-slate-500/10 text-slate-300 hover:bg-slate-500/20' }}">
                                @if ($section->is_active)
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    {{ __('Hide') }}
                                @else
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.93 9.93 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                    {{ __('Show') }}
                                @endif
                            </button>
                        </form>

                        <a href="{{ route('dashboard.home-sections.edit', $section) }}" class="app-button--ghost text-xs px-3 py-2">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            {{ __('Edit') }}
                        </a>
                    </div>
                </div>
            @empty
                <div class="app-card py-12 text-center text-slate-400">
                    {{ __('No homepage sections found. Run the seeder to create defaults.') }}
                </div>
            @endforelse
        </div>
    </div>
@endsection
