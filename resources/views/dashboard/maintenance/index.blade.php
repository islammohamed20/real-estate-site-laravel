@extends('layouts.dashboard')

@section('content')
    <div class="space-y-6">
        <section class="dashboard-hero-card p-6 sm:p-8">
            <div class="relative flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="mobile-section-title">{{ __('System') }}</p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('Maintenance Tools') }}</h1>
                    <p class="mt-2 text-sm text-slate-400">{{ __('Database backups, cache management, and system health.') }}</p>
                </div>
                <form method="POST" action="{{ route('dashboard.maintenance.backup.create') }}">
                    @csrf
                    <button type="submit" class="app-button shrink-0">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-2.64-6.36" stroke-linecap="round"/><path d="M21 3v6h-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        {{ __('Backup now') }}
                    </button>
                </form>
            </div>
        </section>

        {{-- Critical warning: config cache (the cause of the data-loss incident) --}}
        @if ($configCacheExists)
            <div class="flex flex-col gap-3 rounded-2xl border border-rose-500/40 bg-rose-500/10 p-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-500/20 text-rose-300">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 9v4M12 17h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke-linecap="round"/></svg>
                    </span>
                    <div>
                        <p class="text-sm font-bold text-rose-200">{{ __('Config cache is active') }}</p>
                        <p class="mt-1 text-xs leading-relaxed text-rose-200/80">{{ __('bootstrap/cache/config.php pins APP_ENV and the database — running `php artisan test` while it exists targets the LIVE database (this caused the data-loss incident). Clear it before running tests.') }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('dashboard.maintenance.cache.clear') }}" class="shrink-0">
                    @csrf
                    <input type="hidden" name="type" value="config">
                    <button type="submit" class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-rose-500">{{ __('Clear config cache') }}</button>
                </form>
            </div>
        @endif

        {{-- Health cards --}}
        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            <div class="app-card app-card--gradient flex items-center gap-3 !p-4">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-brand-500/15 text-brand-400"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v14c0 1.66 3.58 3 8 3s8-1.34 8-3V5" stroke-linecap="round"/><path d="M4 12c0 1.66 3.58 3 8 3s8-1.34 8-3" stroke-linecap="round"/></svg></span>
                <div class="min-w-0"><p class="text-lg font-extrabold tabular-nums text-white">{{ $dbSize }}</p><p class="truncate text-[11px] text-slate-400">{{ __('Database size') }}</p></div>
            </div>
            <div class="app-card app-card--gradient flex items-center gap-3 !p-4">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-400"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 12H2M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11Z" stroke-linecap="round"/></svg></span>
                <div class="min-w-0"><p class="text-lg font-extrabold tabular-nums text-white">{{ $diskFree }}</p><p class="truncate text-[11px] text-slate-400">{{ __('Disk free') }}</p></div>
            </div>
            <div class="app-card app-card--gradient flex items-center gap-3 !p-4">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-violet-500/15 text-violet-400"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m22 12-3.38 7.6a2 2 0 0 1-1.8 1.15H7.18a2 2 0 0 1-1.8-1.15L2 12M5.5 8.5 12 3l6.5 5.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                <div class="min-w-0"><p class="text-lg font-extrabold tabular-nums text-white">{{ $uploadsSize }}</p><p class="truncate text-[11px] text-slate-400">{{ __('Uploaded files') }}</p></div>
            </div>
            <div class="app-card app-card--gradient flex items-center gap-3 !p-4">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-500/15 text-amber-400"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3" stroke-linecap="round"/></svg></span>
                <div class="min-w-0"><p class="truncate text-lg font-extrabold text-white">{{ $lastBackupAt ?? __('Never') }}</p><p class="truncate text-[11px] text-slate-400">{{ __('Last backup') }}</p></div>
            </div>
        </div>

        {{-- Backups --}}
        <section class="app-card app-card--gradient overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/10 px-4 py-3">
                <h2 class="text-sm font-bold uppercase tracking-wide text-white">{{ __('Database backups') }}</h2>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-[11px] text-slate-500">{{ $backupDir }}</span>
                    <span class="rounded-lg bg-white/5 px-2 py-1 text-[10px] font-semibold text-slate-400">{{ __('automatic daily at 02:30 · keeps 30') }}</span>
                </div>
            </div>

            @if ($errors->has('backup') || $errors->has('restore'))
                <div class="border-b border-rose-500/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">{{ $errors->first('backup') ?? $errors->first('restore') }}</div>
            @endif

            @if ($backups->isEmpty())
                <div class="py-12 text-center">
                    <p class="text-sm text-slate-400">{{ __('No backups yet. Create your first one now — a daily automatic backup is also scheduled.') }}</p>
                    <form method="POST" action="{{ route('dashboard.maintenance.backup.create') }}" class="mt-4">
                        @csrf
                        <button type="submit" class="app-button inline-flex">{{ __('Backup now') }}</button>
                    </form>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[560px] text-sm">
                        <thead>
                            <tr class="border-b border-white/10 text-left text-xs uppercase tracking-wide text-slate-500">
                                <th class="px-4 py-2.5 font-semibold">{{ __('File') }}</th>
                                <th class="px-2 py-2.5 text-center font-semibold">{{ __('Size') }}</th>
                                <th class="px-2 py-2.5 text-center font-semibold">{{ __('Created') }}</th>
                                <th class="px-4 py-2.5 text-right font-semibold">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($backups as $backup)
                                <tr class="border-b border-white/5 last:border-0 hover:bg-white/[0.03]">
                                    <td class="px-4 py-3">
                                        <p class="font-mono text-xs font-semibold text-slate-200">{{ $backup['name'] }}</p>
                                    </td>
                                    <td class="px-2 py-3 text-center tabular-nums text-slate-300">{{ $backup['human_size'] }}</td>
                                    <td class="px-2 py-3 text-center tabular-nums text-slate-400">{{ $backup['modified_at'] }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('dashboard.maintenance.backup.download', $backup['name']) }}" class="inline-flex h-9 items-center gap-1.5 rounded-xl border border-white/10 bg-white/5 px-3 text-xs font-semibold text-slate-200 transition hover:bg-white/10">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                {{ __('Download') }}
                                            </a>
                                            <form method="POST" action="{{ route('dashboard.maintenance.backup.restore', $backup['name']) }}" class="contents" onsubmit="return confirm('{{ __('Restore this backup? The current database will be replaced (a safety backup is taken first).') }}')">
                                                @csrf
                                                <button type="submit" class="inline-flex h-9 items-center gap-1.5 rounded-xl border border-amber-500/20 bg-amber-500/10 px-3 text-xs font-semibold text-amber-300 transition hover:bg-amber-500/20">
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 3v5h5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    {{ __('Restore') }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('dashboard.maintenance.backup.destroy', $backup['name']) }}" class="contents" onsubmit="return confirm('{{ __('Delete this backup file?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex h-9 items-center gap-1.5 rounded-xl border border-rose-500/20 bg-rose-500/10 px-3 text-xs font-semibold text-rose-300 transition hover:bg-rose-500/20">
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-linecap="round"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        {{-- Scheduled jobs controls --}}
        <section class="app-card app-card--gradient p-5 sm:p-6">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-sm font-bold uppercase tracking-wide text-white">{{ __('Scheduled Jobs Control') }}</h2>
                    <p class="mt-1 text-xs text-slate-400">{{ __('Turn jobs on/off and change the database backup schedule. The scheduler checks these settings automatically.') }}</p>
                </div>
                <span class="rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-3 py-1.5 text-[11px] font-semibold text-emerald-300">{{ __('Controlled from this page') }}</span>
            </div>
            <form method="POST" action="{{ route('dashboard.maintenance.scheduled-jobs.update') }}" class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
                @csrf @method('PUT')
                @foreach ([
                    ['key' => 'whatsapp_sync_enabled', 'label' => __('WhatsApp sync'), 'desc' => __('Fallback sync every 2 minutes')],
                    ['key' => 'whatsapp_unassigned_enabled', 'label' => __('Unassigned WhatsApp alerts'), 'desc' => __('Notify managers every 5 minutes')],
                    ['key' => 'queue_worker_enabled', 'label' => __('Queue worker'), 'desc' => __('Drain queued notifications every minute')],
                    ['key' => 'database_backup_enabled', 'label' => __('Database backup'), 'desc' => __('Create compressed backup automatically')],
                ] as $job)
                    <label class="flex cursor-pointer items-center justify-between gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 hover:bg-white/[0.08]">
                        <span><span class="block text-sm font-semibold text-white">{{ $job['label'] }}</span><span class="mt-1 block text-[11px] text-slate-500">{{ $job['desc'] }}</span></span>
                        <input type="checkbox" name="{{ $job['key'] }}" value="1" class="peer sr-only" @checked(($jobSettings[$job['key']] ?? '1') === '1')>
                        <span class="relative h-7 w-12 shrink-0 rounded-full bg-white/10 transition peer-checked:bg-brand-600 after:absolute after:start-1 after:top-1 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition peer-checked:after:translate-x-5 rtl:peer-checked:after:-translate-x-5"></span>
                    </label>
                @endforeach
                <div class="grid grid-cols-2 gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 md:col-span-2 sm:max-w-lg">
                    <div><label for="database_backup_time" class="mb-1.5 block text-xs font-medium text-slate-300">{{ __('Backup time') }}</label><input type="time" id="database_backup_time" name="database_backup_time" class="app-input" value="{{ $jobSettings['database_backup_time'] ?? '02:30' }}"></div>
                    <div><label for="database_backup_keep" class="mb-1.5 block text-xs font-medium text-slate-300">{{ __('Keep backups') }}</label><input type="number" id="database_backup_keep" name="database_backup_keep" min="1" max="365" class="app-input" value="{{ $jobSettings['database_backup_keep'] ?? '30' }}"></div>
                </div>
                <div class="flex justify-end md:col-span-2"><button type="submit" class="app-button">{{ __('Save scheduled jobs') }}</button></div>
            </form>
        </section>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            {{-- Cache management --}}
            <section class="app-card app-card--gradient p-5">
                <h2 class="text-sm font-bold uppercase tracking-wide text-white">{{ __('Cache management') }}</h2>
                <p class="mt-1 text-xs text-slate-400">{{ __('Clearing caches is safe and does not touch your data.') }}</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach ([
                        ['config', __('Config')],
                        ['view', __('Views')],
                        ['route', __('Routes')],
                        ['app', __('App cache')],
                        ['all', __('All caches')],
                    ] as [$key, $label])
                        <form method="POST" action="{{ route('dashboard.maintenance.cache.clear') }}" class="contents">
                            @csrf
                            <input type="hidden" name="type" value="{{ $key }}">
                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-xs font-semibold text-slate-200 transition hover:bg-white/10">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" stroke-linecap="round"/><path d="m3.3 7 8.7 5 8.7-5M12 22V12" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                {{ $label }}
                            </button>
                        </form>
                    @endforeach
                </div>
                @if ($maintenanceMode)
                    <p class="mt-4 rounded-xl border border-amber-500/20 bg-amber-500/10 px-3 py-2 text-xs text-amber-200">{{ __('Maintenance mode is currently ON — the public site shows a maintenance page.') }}</p>
                @endif
            </section>

            {{-- Scheduled jobs --}}
            <section class="app-card app-card--gradient p-5">
                <h2 class="text-sm font-bold uppercase tracking-wide text-white">{{ __('Scheduled jobs') }}</h2>
                <p class="mt-1 text-xs text-slate-400">{{ __('The Laravel scheduler runs every minute (cron).') }}</p>
                <pre class="mt-4 max-h-64 overflow-auto rounded-xl border border-white/10 bg-slate-950/60 p-3 text-[11px] leading-relaxed text-slate-300" style="scrollbar-width: thin;">{{ implode("\n", $scheduleOutput) }}</pre>
            </section>
        </div>

        <p class="text-center text-[11px] text-slate-600">{{ __('Reminder: run `php artisan config:clear` before running the test suite so tests never target the live database.') }}</p>
    </div>
@endsection
