@extends('layouts.dashboard')

@section('content')
    @php
        $userCollection = collect($users->items());
        $activeUsers = $userCollection->where('is_active', true)->count();
        $disabledUsers = $userCollection->where('is_active', false)->count();
        $roles = $userCollection->flatMap(fn ($user) => $user->roles->pluck('name'))->unique()->count();
    @endphp

    <div class="space-y-6" x-data="{
        search: '',
        roleFilter: '',
        statusFilter: '',
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
        matchUser(name, email, roles, isActive) {
            const q = this.search.trim().toLowerCase();
            const matchQuery = !q || name.toLowerCase().includes(q) || email.toLowerCase().includes(q);
            const matchRole = !this.roleFilter || roles.includes(this.roleFilter);
            const matchStatus = !this.statusFilter || (this.statusFilter === 'active' && isActive) || (this.statusFilter === 'disabled' && !isActive);
            return matchQuery && matchRole && matchStatus;
        }
    }" @keydown.escape.window="permsOpen = false">

        {{-- Hero Header --}}
        <section class="dashboard-hero-card p-6 sm:p-8">
            <div class="absolute -right-16 -top-16 h-56 w-56 rounded-full bg-brand-500/20 blur-3xl"></div>
            <div class="absolute -bottom-16 left-1/3 h-56 w-56 rounded-full bg-violet-500/10 blur-3xl"></div>

            <div class="relative grid gap-6 lg:grid-cols-[minmax(0,1.4fr)_minmax(18rem,0.9fr)] lg:items-end">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="badge badge-brand">{{ count($users) }} {{ __('users on this page') }}</span>
                        <span class="badge badge-success">{{ $activeUsers }} {{ __('active') }}</span>
                        @if($disabledUsers > 0)
                            <span class="badge badge-danger">{{ $disabledUsers }} {{ __('disabled') }}</span>
                        @endif
                    </div>

                    <div>
                        <p class="mobile-section-title">{{ __('Access control') }}</p>
                        <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('Users & Permissions') }}</h1>
                        <p class="mt-2.5 max-w-2xl text-sm leading-6 text-slate-300">
                            {{ __('Manage accounts, roles, and session control from a clean oversight panel built for fast administrative decisions.') }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2 pt-1">
                        <a href="{{ route('dashboard.home') }}" class="app-button--ghost text-xs sm:text-sm">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke-width="1.8"/><path d="M9 22V12h6v10" stroke-width="1.8"/></svg>
                            {{ __('Dashboard') }}
                        </a>
                        <a href="{{ route('dashboard.settings.index') }}" class="app-button--ghost text-xs sm:text-sm">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="3" stroke-width="1.8"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" stroke-width="1.8"/></svg>
                            {{ __('System Settings') }}
                        </a>
                    </div>
                </div>

                {{-- Stats Grid --}}
                <div class="grid grid-cols-3 gap-2 sm:gap-3">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-3.5 sm:p-4 shadow-lg backdrop-blur-xl transition hover:border-white/20">
                        <div class="flex items-center justify-between text-slate-400">
                            <span class="text-[10px] font-bold uppercase tracking-[0.15em] sm:text-[11px]">{{ __('Users') }}</span>
                            <svg class="h-4 w-4 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="9" cy="7" r="4" stroke-width="1.8"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-width="1.8"/></svg>
                        </div>
                        <p class="mt-2 text-2xl font-black text-white sm:text-3xl tabular-nums">{{ number_format(count($users)) }}</p>
                        <p class="mt-0.5 text-[11px] text-slate-400">{{ __('Current page') }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-3.5 sm:p-4 shadow-lg backdrop-blur-xl transition hover:border-white/20">
                        <div class="flex items-center justify-between text-slate-400">
                            <span class="text-[10px] font-bold uppercase tracking-[0.15em] sm:text-[11px]">{{ __('Active') }}</span>
                            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        </div>
                        <p class="mt-2 text-2xl font-black text-emerald-400 sm:text-3xl tabular-nums">{{ number_format($activeUsers) }}</p>
                        <p class="mt-0.5 text-[11px] text-slate-400">{{ __('Ready accounts') }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-3.5 sm:p-4 shadow-lg backdrop-blur-xl transition hover:border-white/20">
                        <div class="flex items-center justify-between text-slate-400">
                            <span class="text-[10px] font-bold uppercase tracking-[0.15em] sm:text-[11px]">{{ __('Roles') }}</span>
                            <svg class="h-4 w-4 text-violet-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z" stroke-width="1.8"/></svg>
                        </div>
                        <p class="mt-2 text-2xl font-black text-violet-400 sm:text-3xl tabular-nums">{{ number_format($roles) }}</p>
                        <p class="mt-0.5 text-[11px] text-slate-400">{{ __('Unique roles') }}</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Main Content Section --}}
        <section class="app-card app-card--gradient space-y-4 p-4 sm:p-6">
            {{-- Search & Controls Bar --}}
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between border-b border-white/10 pb-4">
                <div class="flex flex-1 flex-col gap-2.5 sm:flex-row sm:items-center">
                    {{-- Search Input --}}
                    <div class="relative flex-1 sm:max-w-xs">
                        <svg class="pointer-events-none absolute start-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="8" stroke-width="2"/><path d="m21 21-4.3-4.3" stroke-width="2" stroke-linecap="round"/></svg>
                        <input x-model="search" type="text" placeholder="{{ __('Search by name or email...') }}" class="app-input h-10 w-full ps-9 text-xs sm:text-sm">
                    </div>

                    {{-- Role Filter --}}
                    <div class="w-full sm:w-44">
                        <select x-model="roleFilter" class="app-input h-10 w-full text-xs sm:text-sm">
                            <option value="">— {{ __('All Roles') }} —</option>
                            @foreach ($roleOptions as $roleName)
                                <option value="{{ $roleName }}">{{ $roleName }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status Filter --}}
                    <div class="w-full sm:w-36">
                        <select x-model="statusFilter" class="app-input h-10 w-full text-xs sm:text-sm">
                            <option value="">— {{ __('All Status') }} —</option>
                            <option value="active">{{ __('Active') }}</option>
                            <option value="disabled">{{ __('Disabled') }}</option>
                        </select>
                    </div>
                </div>

                {{-- Action Button --}}
                <div class="flex items-center gap-2">
                    @can('manage users')
                        <a href="{{ route('dashboard.users.create') }}" class="app-button w-full sm:w-auto text-xs sm:text-sm">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="9" cy="7" r="4" stroke-width="1.8"/><path d="M19 8v6M22 11h-6" stroke-width="1.8" stroke-linecap="round"/></svg>
                            {{ __('New User') }}
                        </a>
                    @endcan
                </div>
            </div>

            {{-- Users View --}}
            <div class="users-table-wrap overflow-x-auto rounded-2xl border border-white/10 bg-slate-950/20">
                <table class="users-table w-full min-w-[56rem] text-sm">
                    <thead>
                        <tr class="border-b border-white/10 bg-white/[0.03] text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            <th class="px-4 py-3.5 text-start">{{ __('User') }}</th>
                            <th class="px-4 py-3.5 text-start">{{ __('Role') }}</th>
                            <th class="px-4 py-3.5 text-start">{{ __('Status') }}</th>
                            <th class="px-4 py-3.5 text-start">{{ __('Last login') }}</th>
                            <th class="px-4 py-3.5 text-start">{{ __('Created') }}</th>
                            <th class="px-4 py-3.5 text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse ($userCollection as $user)
                            @php
                                $isProtected = strtolower($user->email) === 'admin@venecia-dev.com';
                                $userRolesJson = json_encode($user->roles->pluck('name')->all());
                            @endphp
                            <tr
                                x-show="matchUser(@js($user->name), @js($user->email), {{ $userRolesJson }}, {{ $user->is_active ? 'true' : 'false' }})"
                                class="transition hover:bg-white/[0.04]"
                            >
                                {{-- User Info --}}
                                <td class="px-4 py-3.5" data-label="{{ __('User') }}">
                                    <div class="flex items-center gap-3">
                                        <div class="relative shrink-0">
                                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500/30 to-violet-600/30 text-sm font-bold text-brand-200 ring-1 ring-white/15 shadow-sm">
                                                {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                                            </span>
                                            <span class="absolute -bottom-0.5 -end-0.5 h-3 w-3 rounded-full border-2 border-slate-950 {{ $user->is_active ? 'bg-emerald-400 shadow-sm shadow-emerald-400/50' : 'bg-slate-500' }}"></span>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <p class="truncate font-semibold text-white">{{ $user->name }}</p>
                                                @if ($isProtected)
                                                    <span class="inline-flex items-center gap-1 rounded-md bg-amber-500/15 px-1.5 py-0.5 text-[10px] font-bold text-amber-300 ring-1 ring-amber-500/30" title="{{ __('Protected account') }}">
                                                        <svg class="h-2.5 w-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="4" y="11" width="16" height="10" rx="2" stroke-width="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4" stroke-width="2"/></svg>
                                                        {{ __('Protected') }}
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="truncate text-xs text-slate-400 font-mono mt-0.5">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Roles --}}
                                <td class="px-4 py-3.5" data-label="{{ __('Role') }}">
                                    <div class="flex flex-wrap gap-1.5">
                                        @forelse($user->roles as $role)
                                            <span class="inline-flex items-center rounded-lg bg-brand-500/15 px-2.5 py-1 text-xs font-semibold text-brand-300 ring-1 ring-brand-500/25">
                                                {{ $role->name }}
                                            </span>
                                        @empty
                                            <span class="badge badge-muted">{{ __('No Role') }}</span>
                                        @endforelse
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td class="px-4 py-3.5" data-label="{{ __('Status') }}">
                                    @if($user->is_active)
                                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-500/15 px-2.5 py-1 text-xs font-semibold text-emerald-300 ring-1 ring-emerald-500/25">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                                            {{ __('Active') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-500/15 px-2.5 py-1 text-xs font-semibold text-slate-400 ring-1 ring-slate-500/25">
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-500"></span>
                                            {{ __('Disabled') }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Last Login --}}
                                <td class="px-4 py-3.5 text-xs" data-label="{{ __('Last login') }}">
                                    @if ($user->last_login_at)
                                        <div class="space-y-0.5">
                                            <p class="font-medium text-slate-200 tabular-nums">{{ $user->last_login_at->format('Y-m-d H:i') }}</p>
                                            @if($user->last_login_ip)
                                                <p class="text-[10px] font-mono text-slate-500">{{ $user->last_login_ip }}</p>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-500">—</span>
                                    @endif
                                </td>

                                {{-- Created At --}}
                                <td class="px-4 py-3.5 text-xs tabular-nums text-slate-400" data-label="{{ __('Created') }}">
                                    {{ $user->created_at?->format('Y-m-d') }}
                                </td>

                                {{-- Action Buttons --}}
                                <td class="px-4 py-3.5" data-label="">
                                    <div class="user-actions-group flex flex-wrap items-center justify-end gap-1.5">
                                        @if ($isProtected)
                                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-amber-500/10 text-amber-300 ring-1 ring-amber-500/20" title="{{ __('Protected account') }}">
                                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="4" y="11" width="16" height="10" rx="2" stroke-width="1.8"/><path d="M8 11V7a4 4 0 0 1 8 0v4" stroke-width="1.8"/></svg>
                                            </span>
                                        @else
                                            {{-- Permissions --}}
                                            <button
                                                type="button"
                                                data-user-id="{{ $user->id }}"
                                                data-user-name="{{ $user->name }}"
                                                data-user-email="{{ $user->email }}"
                                                data-user-count="{{ $user->permissions->count() }}"
                                                data-user-action="{{ route('dashboard.users.permissions', $user) }}"
                                                data-user-perms='@json($user->permissions->pluck('name'))'
                                                @click="openPermissions($el)"
                                                class="inline-flex h-8 items-center gap-1.5 rounded-xl border border-white/10 bg-white/5 px-2.5 text-xs font-semibold text-slate-200 transition hover:bg-white/10 hover:border-white/20"
                                                title="{{ __('Permissions') }}"
                                            >
                                                <svg class="h-3.5 w-3.5 text-brand-300" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z" stroke-width="1.8"/></svg>
                                                <span class="tabular-nums text-[11px]">{{ $user->permissions->count() }}</span>
                                            </button>

                                            {{-- Role Selector --}}
                                            <form method="POST" action="{{ route('dashboard.users.role', $user) }}" class="inline-flex items-center gap-1">
                                                @csrf
                                                <select name="role" class="form-select h-8 rounded-xl border-white/10 bg-slate-900/80 px-2.5 text-xs text-slate-200 focus:border-brand-500 focus:ring-0" aria-label="{{ __('Assign role') }}">
                                                    @foreach ($roleOptions as $roleName)
                                                        <option value="{{ $roleName }}" @selected($user->roles->contains('name', $roleName))>{{ $roleName }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-brand-600/20 text-brand-300 ring-1 ring-brand-500/30 transition hover:bg-brand-600/30" title="{{ __('Save Role') }}">
                                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 6 9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Toggle Active --}}
                                        @if ($user->is_active)
                                            <form id="disable-form-{{ $user->id }}" method="POST" action="{{ route('dashboard.users.disable', $user) }}">
                                                @csrf
                                                <button type="button" onclick="confirmAction('{{ __('Disable account') }}', '{{ __('Are you sure you want to disable :name?', ['name' => $user->name]) }}', () => document.getElementById('disable-form-{{ $user->id }}').submit(), '{{ __('Disable') }}')" class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-rose-600/15 text-rose-300 ring-1 ring-rose-500/20 transition hover:bg-rose-600/25" title="{{ __('Disable account') }}">
                                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.36 6.64a9 9 0 1 1-12.73 0" stroke-linecap="round"/><path d="M12 2v10" stroke-linecap="round"/></svg>
                                                </button>
                                            </form>
                                        @else
                                            <form id="enable-form-{{ $user->id }}" method="POST" action="{{ route('dashboard.users.enable', $user) }}">
                                                @csrf
                                                <button type="button" onclick="confirmAction('{{ __('Enable account') }}', '{{ __('Are you sure you want to enable :name?', ['name' => $user->name]) }}', () => document.getElementById('enable-form-{{ $user->id }}').submit(), '{{ __('Enable') }}')" class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-600/20 text-emerald-300 ring-1 ring-emerald-500/30 transition hover:bg-emerald-600/30" title="{{ __('Enable account') }}">
                                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.36 6.64a9 9 0 1 1-12.73 0" stroke-linecap="round"/><path d="M12 2v10" stroke-linecap="round"/></svg>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Force Logout --}}
                                        <form id="force-logout-form-{{ $user->id }}" method="POST" action="{{ route('dashboard.users.force-logout', $user) }}">
                                            @csrf
                                            <button type="button" onclick="confirmAction('{{ __('Force logout') }}', '{{ __('Are you sure you want to force logout :name?', ['name' => $user->name]) }}', () => document.getElementById('force-logout-form-{{ $user->id }}').submit(), '{{ __('Logout') }}')" class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-amber-600/15 text-amber-300 ring-1 ring-amber-500/20 transition hover:bg-amber-600/25" title="{{ __('Force logout') }}">
                                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 17l5-5-5-5" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 12H9" stroke-linecap="round"/></svg>
                                            </button>
                                        </form>

                                        {{-- Delete --}}
                                        @if (strtolower($user->email) !== 'admin@venecia-dev.com' && $user->id !== auth()->id())
                                            <form id="delete-form-{{ $user->id }}" method="POST" action="{{ route('dashboard.users.destroy', $user) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmAction('{{ __('Delete user') }}', '{{ __('Are you sure you want to delete :name? The user will be moved to the trash.', ['name' => $user->name]) }}', () => document.getElementById('delete-form-{{ $user->id }}').submit(), '{{ __('Delete') }}')" class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-rose-600/20 text-rose-300 ring-1 ring-rose-500/30 transition hover:bg-rose-600/35" title="{{ __('Delete user') }}">
                                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-linecap="round"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                                    <svg class="mx-auto h-10 w-10 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="1.5"/><path d="M12 8v4M12 16h.01" stroke-width="2" stroke-linecap="round"/></svg>
                                    <p class="mt-2 font-semibold text-white">{{ __('No users found.') }}</p>
                                    <p class="mt-0.5 text-xs">{{ __('There are no accounts to manage right now.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="pt-3">{{ $users->links() }}</div>
            @endif
        </section>

        @push('scripts')
            <style>
                /* Enhanced Responsive Mobile Layout */
                @media (max-width: 1023.5px) {
                    .users-table-wrap {
                        border: none !important;
                        background: transparent !important;
                        overflow: visible !important;
                    }
                    .users-table {
                        min-width: 0 !important;
                        display: block;
                    }
                    .users-table thead {
                        display: none;
                    }
                    .users-table tbody {
                        display: flex;
                        flex-direction: column;
                        gap: 1rem;
                    }
                    .users-table tr {
                        display: flex;
                        flex-direction: column;
                        width: 100%;
                        border: 1px solid rgb(255 255 255 / 0.1) !important;
                        border-radius: 1.25rem;
                        background: linear-gradient(135deg, rgb(255 255 255 / 0.05), rgb(255 255 255 / 0.02)) !important;
                        padding: 1.1rem;
                        box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.3);
                        backdrop-filter: blur(16px);
                        -webkit-backdrop-filter: blur(16px);
                        transition: all 0.2s ease;
                    }
                    .users-table tr:hover {
                        border-color: rgb(255 255 255 / 0.18) !important;
                        background: linear-gradient(135deg, rgb(255 255 255 / 0.08), rgb(255 255 255 / 0.03)) !important;
                    }
                    .users-table td {
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        gap: 0.75rem;
                        width: 100%;
                        border: none !important;
                        padding: 0.45rem 0 !important;
                    }
                    .users-table tbody > :not([hidden]) ~ :not([hidden]) {
                        border-top: none !important;
                    }
                    .users-table td::before {
                        content: attr(data-label);
                        font-size: 11px;
                        font-weight: 700;
                        text-transform: uppercase;
                        letter-spacing: 0.08em;
                        color: rgb(148 163 184 / 0.8);
                        flex-shrink: 0;
                    }
                    /* First TD (User info) is hero layout on card */
                    .users-table td[data-label="{{ __('User') }}"] {
                        padding-bottom: 0.85rem !important;
                        margin-bottom: 0.5rem;
                        border-bottom: 1px solid rgb(255 255 255 / 0.08) !important;
                        justify-content: flex-start;
                    }
                    .users-table td[data-label="{{ __('User') }}"]::before {
                        display: none;
                    }
                    /* Action buttons pinned at bottom of card */
                    .users-table td[data-label=""] {
                        margin-top: 0.75rem;
                        padding-top: 0.85rem !important;
                        border-top: 1px solid rgb(255 255 255 / 0.08) !important;
                        justify-content: flex-end;
                    }
                    .users-table td[data-label=""]::before {
                        display: none;
                    }
                    .users-table .user-actions-group {
                        width: 100%;
                        justify-content: space-between;
                    }
                    .users-table .user-actions-group form {
                        display: inline-flex;
                    }
                }
            </style>
        @endpush

        {{-- Permissions modal --}}
        <div x-show="permsOpen" x-cloak x-transition.opacity class="fixed inset-0 z-[80] flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-label="{{ __('Permissions') }}">
            <div class="absolute inset-0 bg-black/70 backdrop-blur-md" @click="closePermissions()"></div>

            <div class="relative flex max-h-[85vh] w-full max-w-3xl flex-col overflow-hidden rounded-3xl border border-white/10 bg-slate-950 shadow-2xl shadow-black/60 backdrop-blur-2xl" x-show="permsOpen" x-transition.scale.origin.center>
                {{-- Header --}}
                <div class="flex items-start justify-between gap-4 border-b border-white/10 bg-white/[0.02] p-5 sm:p-6">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-brand-500/20 text-brand-300">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z" stroke-width="1.8"/></svg>
                            </span>
                            <h3 class="truncate text-lg font-bold text-white">
                                {{ __('Permissions') }} — <span class="text-brand-300" x-text="permsName"></span>
                            </h3>
                        </div>
                        <p class="mt-1 truncate text-xs text-slate-400 font-mono">
                            <span x-text="permsEmail"></span> · <span class="text-white font-semibold" x-text="permsCount"></span> {{ __('direct permissions') }}
                        </p>
                    </div>
                    <button type="button" class="-me-2 -mt-2 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-slate-400 transition hover:bg-white/10 hover:text-white" @click="closePermissions()" aria-label="{{ __('Close') }}">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 6 6 18M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                </div>

                {{-- Body --}}
                <form :action="permsAction" method="POST" class="flex min-h-0 flex-1 flex-col">
                    @csrf
                    <div class="flex-1 overflow-y-auto p-5 sm:p-6 space-y-6">
                        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($permissionGroups as $group => $perms)
                                <div class="rounded-2xl border border-white/5 bg-white/[0.02] p-4 transition hover:border-white/10">
                                    <div class="mb-3 flex items-center justify-between border-b border-white/5 pb-2">
                                        <p class="text-xs font-bold uppercase tracking-wider text-brand-300">
                                            {{ $group }}
                                        </p>
                                        <span class="rounded-md bg-white/5 px-1.5 py-0.5 text-[10px] font-bold text-slate-400 tabular-nums">{{ count($perms) }}</span>
                                    </div>
                                    <div class="space-y-1.5">
                                        @foreach ($perms as $perm)
                                            <label class="flex cursor-pointer items-center gap-2.5 rounded-lg px-2 py-1.5 text-xs text-slate-300 transition hover:bg-white/5 hover:text-white">
                                                <input type="checkbox" name="permissions[]" value="{{ $perm }}" :checked="perms.includes('{{ $perm }}')" class="h-4 w-4 rounded border-white/20 bg-slate-900 text-brand-600 focus:ring-0 focus:ring-offset-0">
                                                <span class="truncate">{{ $perm }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-white/10 bg-white/[0.02] p-4 sm:p-5">
                        <p class="text-[11px] text-slate-400 max-w-md">{{ __('Direct permissions are added on top of the role permissions and can override them.') }}</p>
                        <div class="flex items-center gap-2">
                            <button type="button" class="app-button--ghost text-xs" @click="closePermissions()">{{ __('Cancel') }}</button>
                            <button type="submit" class="app-button px-5 py-2 text-xs font-bold text-white">{{ __('Save Permissions') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

