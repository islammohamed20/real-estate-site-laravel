@extends('layouts.dashboard')

@section('content')
    @php
        $userCollection = collect($users->items());
        $activeUsers = $userCollection->where('is_active', true)->count();
        $disabledUsers = $userCollection->where('is_active', false)->count();
        $roles = $userCollection->flatMap(fn ($user) => $user->roles->pluck('name'))->unique()->count();
    @endphp

    <div class="space-y-6" x-data="{
        permsOpen: false,
        permsId: null,
        permsName: '',
        permsEmail: '',
        permsCount: 0,
        permsAction: '',
        perms: [],
        openPermissions(btn) {
            this.permsId = btn.dataset.userId;
            this.permsName = btn.dataset.userName;
            this.permsEmail = btn.dataset.userEmail;
            this.permsCount = btn.dataset.userCount;
            this.permsAction = btn.dataset.userAction;
            this.perms = JSON.parse(btn.dataset.userPerms || '[]');
            this.permsOpen = true;
        },
        closePermissions() {
            this.permsOpen = false;
        },
    }" @keydown.escape.window="permsOpen = false">

        <section class="dashboard-hero-card p-6 sm:p-8">
            <div class="absolute -right-16 -top-16 h-48 w-48 rounded-full bg-brand-500/20 blur-3xl"></div>
            <div class="absolute -bottom-16 left-1/3 h-48 w-48 rounded-full bg-violet-500/10 blur-3xl"></div>

            <div class="relative grid gap-6 lg:grid-cols-[minmax(0,1.4fr)_minmax(16rem,0.9fr)] lg:items-end">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="badge badge-brand">{{ count($users) }} {{ __('users on this page') }}</span>
                        <span class="badge badge-success">{{ $activeUsers }} {{ __('active') }}</span>
                        <span class="badge badge-muted">{{ $disabledUsers }} {{ __('disabled') }}</span>
                    </div>

                    <div>
                        <p class="mobile-section-title">{{ __('Access control') }}</p>
                        <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('Users & Permissions') }}</h1>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">
                            {{ __('Manage accounts, roles, and session control from a clean oversight panel built for fast administrative decisions.') }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('dashboard.home') }}" class="app-button--ghost">{{ __('Back to Dashboard') }}</a>
                        <a href="{{ route('dashboard.settings.index') }}" class="app-button--ghost">{{ __('System Settings') }}</a>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2 sm:gap-3">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 shadow-lg shadow-black/10 backdrop-blur-xl">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Users') }}</p>
                        <p class="mt-2 text-3xl font-bold text-white">{{ number_format(count($users)) }}</p>
                        <p class="mt-1 text-xs text-slate-400">{{ __('Current page') }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 shadow-lg shadow-black/10 backdrop-blur-xl">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Active') }}</p>
                        <p class="mt-2 text-3xl font-bold text-emerald-400">{{ number_format($activeUsers) }}</p>
                        <p class="mt-1 text-xs text-slate-400">{{ __('Accounts ready') }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 shadow-lg shadow-black/10 backdrop-blur-xl">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Roles') }}</p>
                        <p class="mt-2 text-3xl font-bold text-violet-400">{{ number_format($roles) }}</p>
                        <p class="mt-1 text-xs text-slate-400">{{ __('Unique role names') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="app-card app-card--gradient space-y-4 p-5 sm:p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="mobile-section-title">{{ __('People') }}</p>
                    <h2 class="mt-2 text-lg font-semibold text-white">{{ __('Account directory') }}</h2>
                    <p class="mt-1 text-xs text-slate-400">{{ __('Assign roles, grant direct permissions, and control account access.') }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="badge badge-success">{{ __('Live access control') }}</span>
                    @can('manage users')
                        <a href="{{ route('dashboard.users.create') }}" class="app-button">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="9" cy="7" r="4" stroke-width="1.8"/><path d="M19 8v6M22 11h-6" stroke-width="1.8" stroke-linecap="round"/></svg>
                            {{ __('New User') }}
                        </a>
                    @endcan
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($userCollection as $user)
                    @php($isProtected = strtolower($user->email) === 'admin@venecia-dev.com')
                    <article class="flex flex-col rounded-2xl border border-white/10 bg-white/5 p-5 transition hover:border-white/20 hover:bg-white/[0.08]">
                        {{-- Header: avatar + identity --}}
                        <div class="flex items-start gap-3">
                            <div class="relative shrink-0">
                                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500/25 to-violet-600/25 text-sm font-bold text-brand-200 ring-1 ring-white/10">
                                    {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                                </span>
                                <span class="absolute -bottom-0.5 -right-0.5 h-3.5 w-3.5 rounded-full border-2 border-slate-950 {{ $user->is_active ? 'bg-emerald-400' : 'bg-slate-600' }}"></span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="truncate font-semibold text-white">{{ $user->name }}</p>
                                    <span class="badge shrink-0 {{ $user->is_active ? 'badge-success' : 'badge-muted' }}">{{ $user->is_active ? __('Active') : __('Disabled') }}</span>
                                </div>
                                <p class="mt-0.5 truncate text-xs text-slate-400">{{ $user->email }}</p>
                            </div>
                        </div>

                        {{-- Roles --}}
                        <div class="mt-3 flex flex-wrap gap-1.5">
                            @forelse($user->roles as $role)
                                <span class="badge badge-brand">{{ $role->name }}</span>
                            @empty
                                <span class="badge badge-muted">{{ __('No Role') }}</span>
                            @endforelse
                        </div>

                        {{-- Role assignment --}}
                        @if ($isProtected)
                            <div class="mt-4 flex items-center gap-2 rounded-xl border border-amber-500/20 bg-amber-500/10 p-3 text-xs text-amber-300 border-t border-white/5">
                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><rect x="4" y="11" width="16" height="10" rx="2" stroke-width="1.8"/><path d="M8 11V7a4 4 0 0 1 8 0v4" stroke-width="1.8" stroke-linecap="round"/></svg>
                                {{ __('Protected account — role and permissions cannot be changed.') }}
                            </div>
                        @else
                            <form method="POST" action="{{ route('dashboard.users.role', $user) }}" class="mt-4 flex items-center gap-2 border-t border-white/5 pt-4">
                                @csrf
                                <select name="role" class="form-select min-w-0 flex-1 rounded-xl text-xs" aria-label="{{ __('Assign role') }}">
                                    @foreach ($roleOptions as $roleName)
                                        <option value="{{ $roleName }}" @selected($user->roles->contains('name', $roleName))>{{ $roleName }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="app-button shrink-0 bg-brand-600/20 px-3 py-1.5 text-xs text-brand-300 hover:bg-brand-600/30">{{ __('Save Role') }}</button>
                            </form>
                        @endif

                        {{-- Actions --}}
                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            @if ($isProtected)
                                <span class="inline-flex items-center gap-1.5 rounded-xl bg-amber-500/10 px-3 py-2 text-xs font-semibold text-amber-300">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><rect x="4" y="11" width="16" height="10" rx="2" stroke-width="1.8"/><path d="M8 11V7a4 4 0 0 1 8 0v4" stroke-width="1.8" stroke-linecap="round"/></svg>
                                    {{ __('Permissions locked') }}
                                </span>
                            @else
                                <button
                                    type="button"
                                    data-user-id="{{ $user->id }}"
                                    data-user-name="{{ $user->name }}"
                                    data-user-email="{{ $user->email }}"
                                    data-user-count="{{ $user->permissions->count() }}"
                                    data-user-action="{{ route('dashboard.users.permissions', $user) }}"
                                    data-user-perms='@json($user->permissions->pluck('name'))'
                                    @click="openPermissions($el)"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-white/5 px-3 py-2 text-xs font-semibold text-slate-200 transition hover:bg-white/10"
                                >
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    {{ __('Permissions') }}
                                    <span class="badge badge-muted">{{ $user->permissions->count() }}</span>
                                </button>
                            @endif

                            @if ($user->is_active)
                                <form id="disable-form-{{ $user->id }}" method="POST" action="{{ route('dashboard.users.disable', $user) }}">
                                    @csrf
                                    <button type="button" onclick="confirmAction('{{ __('Disable account') }}', '{{ __('Are you sure you want to disable :name?', ['name' => $user->name]) }}', () => document.getElementById('disable-form-{{ $user->id }}').submit(), '{{ __('Disable') }}')" class="app-button bg-rose-600/20 px-3 py-2 text-xs text-rose-300 hover:bg-rose-600/30">{{ __('Disable') }}</button>
                                </form>
                            @else
                                <form id="enable-form-{{ $user->id }}" method="POST" action="{{ route('dashboard.users.enable', $user) }}">
                                    @csrf
                                    <button type="button" onclick="confirmAction('{{ __('Enable account') }}', '{{ __('Are you sure you want to enable :name?', ['name' => $user->name]) }}', () => document.getElementById('enable-form-{{ $user->id }}').submit(), '{{ __('Enable') }}')" class="app-button bg-emerald-600/20 px-3 py-2 text-xs text-emerald-300 hover:bg-emerald-600/30">{{ __('Enable') }}</button>
                                </form>
                            @endif

                            <form id="force-logout-form-{{ $user->id }}" method="POST" action="{{ route('dashboard.users.force-logout', $user) }}">
                                @csrf
                                <button type="button" onclick="confirmAction('{{ __('Force logout') }}', '{{ __('Are you sure you want to force logout :name?', ['name' => $user->name]) }}', () => document.getElementById('force-logout-form-{{ $user->id }}').submit(), '{{ __('Logout') }}')" class="app-button app-button--danger px-3 py-2 text-xs font-semibold">{{ __('Force Logout') }}</button>
                            </form>

                            @if (strtolower($user->email) !== 'admin@venecia-dev.com' && $user->id !== auth()->id())
                                <form id="delete-form-{{ $user->id }}" method="POST" action="{{ route('dashboard.users.destroy', $user) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmAction('{{ __('Delete user') }}', '{{ __('Are you sure you want to delete :name? The user will be moved to the trash.', ['name' => $user->name]) }}', () => document.getElementById('delete-form-{{ $user->id }}').submit(), '{{ __('Delete') }}')" class="app-button app-button--danger px-3 py-2 text-xs font-semibold" title="{{ __('Delete user') }}">{{ __('Delete') }}</button>
                                </form>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="flex min-h-48 items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/5 text-center text-sm text-slate-400 md:col-span-2 xl:col-span-3">
                        <div>
                            <p class="font-semibold text-white">{{ __('No users found.') }}</p>
                            <p class="mt-1 text-slate-400">{{ __('There are no accounts to manage right now.') }}</p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if ($users->hasPages())
                <div class="pt-2">{{ $users->links() }}</div>
            @endif
        </section>

        {{-- Permissions modal --}}
        <div x-show="permsOpen" x-cloak x-transition class="fixed inset-0 z-[80] flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-label="{{ __('Permissions') }}">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closePermissions()"></div>

            <div class="relative flex max-h-[85vh] w-full max-w-3xl flex-col overflow-hidden rounded-3xl border border-white/10 bg-slate-950 shadow-2xl shadow-black/40 backdrop-blur-xl">
                {{-- Header --}}
                <div class="flex items-start justify-between gap-4 border-b border-white/5 p-5">
                    <div class="min-w-0">
                        <p class="mobile-section-title">{{ __('Access control') }}</p>
                        <h3 class="mt-1 truncate text-lg font-bold text-white">
                            {{ __('Permissions') }} — <span x-text="permsName"></span>
                        </h3>
                        <p class="mt-1 truncate text-xs text-slate-400">
                            <span x-text="permsEmail"></span> · <span x-text="permsCount"></span> {{ __('direct permissions') }}
                        </p>
                    </div>
                    <button type="button" class="-mr-2 -mt-2 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-slate-400 transition hover:bg-white/5 hover:text-white" @click="closePermissions()" aria-label="{{ __('Close') }}">&times;</button>
                </div>

                {{-- Body --}}
                <form :action="permsAction" method="POST" class="flex min-h-0 flex-1 flex-col">
                    @csrf
                    <div class="flex-1 overflow-y-auto p-5">
                        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($permissionGroups as $group => $perms)
                                <div>
                                    <p class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-brand-300">
                                        {{ $group }}
                                        <span class="badge badge-muted">{{ count($perms) }}</span>
                                    </p>
                                    <div class="space-y-1.5">
                                        @foreach ($perms as $perm)
                                            <label class="flex cursor-pointer items-center gap-2 rounded-lg px-2 py-1 text-xs text-slate-300 transition hover:bg-white/5 hover:text-white">
                                                <input type="checkbox" name="permissions[]" value="{{ $perm }}" :checked="perms.includes('{{ $perm }}')" class="h-3.5 w-3.5 rounded border-white/10 bg-slate-900 text-brand-600 focus:ring-brand-500/20">
                                                {{ $perm }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="flex flex-wrap items-center justify-between gap-2 border-t border-white/5 p-5">
                        <p class="text-xs text-slate-500">{{ __('Direct permissions are added on top of the role permissions and can override them.') }}</p>
                        <div class="flex items-center gap-2">
                            <button type="button" class="app-button--ghost" @click="closePermissions()">{{ __('Cancel') }}</button>
                            <button type="submit" class="app-button px-5 py-2 text-xs text-white">{{ __('Save Permissions') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
