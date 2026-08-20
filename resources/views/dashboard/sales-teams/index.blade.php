@extends('layouts.dashboard')

@section('content')
    <div class="space-y-6">
        <section class="dashboard-hero-card p-6 sm:p-8">
            <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="mobile-section-title">{{ __('Sales Management') }}</p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('Sales Teams') }}</h1>
                    <p class="mt-2 text-sm text-slate-400">{{ __('Organize your salespeople into teams, assign managers, and track each team\u2019s leads.') }}</p>
                </div>
                @can('manage teams')
                    <a href="{{ route('dashboard.sales-teams.create') }}" class="app-button shrink-0">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14" stroke-linecap="round"/></svg>
                        {{ __('New Team') }}
                    </a>
                @endcan
            </div>
        </section>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($teams as $team)
                <article class="app-card flex flex-col gap-4 p-5 transition {{ ! $team->is_active ? 'opacity-60' : '' }}">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="badge {{ $team->is_active ? 'badge-success' : 'badge-muted' }}">
                                    {{ $team->is_active ? __('Active') : __('Inactive') }}
                                </span>
                                @if ($team->manager_id === auth()->id())
                                    <span class="badge badge-brand">{{ __('You manage this team') }}</span>
                                @endif
                            </div>
                            <h2 class="mt-2 truncate text-lg font-bold text-white">{{ $team->name }}</h2>
                            @if ($team->description)
                                <p class="mt-1 line-clamp-2 text-xs leading-relaxed text-slate-400">{{ $team->description }}</p>
                            @endif
                        </div>
                        <span class="shrink-0 rounded-2xl border border-white/10 bg-white/5 px-3 py-2 text-center">
                            <span class="block text-xl font-extrabold text-brand-300">{{ $team->members->count() }}</span>
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Members') }}</span>
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 border-t border-white/5 pt-3 text-xs text-slate-400">
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            {{ __('Manager:') }}
                            <strong class="text-white">{{ $team->manager?->name ?? __('—') }}</strong>
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            {{ __('Leads:') }}
                            <strong class="text-white">{{ $team->active_leads_count }}</strong>
                        </span>
                    </div>

                    @if ($team->members->isNotEmpty())
                        <div class="flex items-center gap-2">
                            <div class="flex -space-x-2 rtl:space-x-reverse">
                                @foreach ($team->members->take(5) as $member)
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full border-2 border-slate-900 bg-brand-600/30 text-[11px] font-bold text-brand-200" title="{{ $member->name }}">
                                        {{ strtoupper(mb_substr($member->name, 0, 1)) }}
                                    </span>
                                @endforeach
                            </div>
                            @if ($team->members->count() > 5)
                                <span class="text-[11px] font-semibold text-slate-500">+{{ $team->members->count() - 5 }}</span>
                            @endif
                            <span class="ms-1 line-clamp-1 text-[11px] text-slate-500">
                                {{ $team->members->take(5)->pluck('name')->implode('، ') }}
                            </span>
                        </div>
                    @else
                        <p class="text-xs text-slate-500">{{ __('No members yet.') }}</p>
                    @endif

                    <div class="mt-auto flex flex-wrap items-center gap-2 border-t border-white/5 pt-3">
                        <a href="{{ route('dashboard.sales-teams.edit', $team) }}" class="app-button--ghost text-xs px-3 py-2">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            {{ __('Edit') }}
                        </a>
                        <form method="POST" action="{{ route('dashboard.sales-teams.toggle', $team) }}" class="contents">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl border border-white/10 px-3 py-2 text-xs font-semibold transition {{ $team->is_active ? 'bg-amber-500/10 text-amber-300 hover:bg-amber-500/20' : 'bg-emerald-500/10 text-emerald-300 hover:bg-emerald-500/20' }}">
                                {{ $team->is_active ? __('Deactivate') : __('Activate') }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('dashboard.sales-teams.destroy', $team) }}" class="contents" onsubmit="return confirm('{{ __('Delete this sales team?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl border border-rose-500/20 bg-rose-500/10 px-3 py-2 text-xs font-semibold text-rose-300 transition hover:bg-rose-500/20">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-linecap="round"/></svg>
                                {{ __('Delete') }}
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="app-card col-span-full py-14 text-center text-slate-400">
                    <p class="text-lg font-semibold text-white">{{ __('No sales teams yet') }}</p>
                    <p class="mt-1 text-sm">{{ __('Create your first team to organize your salespeople.') }}</p>
                    @can('manage teams')
                        <a href="{{ route('dashboard.sales-teams.create') }}" class="app-button mt-5 inline-flex">{{ __('New Team') }}</a>
                    @endcan
                </div>
            @endforelse
        </div>

        <div>
            {{ $teams->links() }}
        </div>
    </div>
@endsection
