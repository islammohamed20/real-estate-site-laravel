@extends('layouts.dashboard')

@section('content')
    @php
        $rolePermissions = $rolePermissions ?? [];
    @endphp
    <script>window.__rolePermissions = @json($rolePermissions);</script>
    <div class="space-y-6" x-data="{
        submitting: false,
        selectedRole: '',
        directPerms: [],
        rolePerms: window.__rolePermissions || {},
        get effectivePerms() {
            const set = new Set(this.rolePerms[this.selectedRole] || []);
            (this.directPerms || []).forEach(p => set.add(p));
            return set;
        },
        hasPerm(p) { return this.effectivePerms.has(p); },
        get adminGroupVisible() {
            return this.selectedRole === 'Administrator' || this.selectedRole === 'Sales Manager'
                || this.hasPerm('view reports') || this.hasPerm('manage users') || this.hasPerm('manage settings');
        },
        get trashVisible() { return this.selectedRole === 'Administrator' || this.selectedRole === 'Sales Manager'; },
        get visibleCount() {
            const P = this.effectivePerms;
            let n = 4; // Dashboard, CRM, Projects, Calculator
            if (this.adminGroupVisible) {
                if (P.has('view reports')) n += 2;
                if (this.trashVisible) n += 1;
                if (P.has('manage users')) n += 1;
                if (P.has('manage settings')) n += 2;
            }
            return n;
        }
    }">
        @if ($errors->any())
            <div class="flex items-start gap-3 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-300">
                <svg class="mt-0.5 h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke-width="1.8"/><path d="M12 9v4M12 17h.01" stroke-width="1.8" stroke-linecap="round"/></svg>
                <div>
                    <p class="font-semibold">{{ __('Please fix the errors below.') }}</p>
                    <ul class="mt-1 list-inside list-disc text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <section class="dashboard-hero-card p-6 sm:p-8">
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-brand-500/20 blur-3xl"></div>

            <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="mobile-section-title">{{ __('Access control') }}</p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('Create User') }}</h1>
                    <p class="mt-2 text-sm text-slate-400">
                        {{ __('Create a new account, assign a role, and grant direct permissions.') }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="submit" form="user-form" class="app-button" :disabled="submitting" :class="submitting ? 'pointer-events-none opacity-60' : ''">
                        <svg x-show="!submitting" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round"/></svg>
                        <svg x-show="submitting" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M21 12a9 9 0 1 1-6.219-8.56" stroke-width="2" stroke-linecap="round"/></svg>
                        <span x-text="submitting ? '{{ __('Saving...') }}' : '{{ __('Create User') }}'"></span>
                    </button>
                    <a href="{{ route('dashboard.users.index') }}" class="app-button--ghost">{{ __('Cancel') }}</a>
                </div>
            </div>
        </section>

        <form id="user-form" method="POST" action="{{ route('dashboard.users.store') }}" class="grid gap-6 lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)]" @submit="submitting = true">
            @csrf

            {{-- Identity & Login --}}
            <section class="app-card app-card--gradient space-y-5">
                <div class="flex items-center gap-3 border-b border-white/5 pb-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-500/15 text-brand-400">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="9" cy="7" r="4" stroke-width="1.8"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-width="1.8"/></svg>
                    </span>
                    <div>
                        <h2 class="text-lg font-semibold text-white">{{ __('Identity & Login') }}</h2>
                        <p class="text-xs text-slate-400">{{ __('Basic account information and credentials.') }}</p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="name" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Full Name') }} <span class="text-rose-400">*</span></label>
                        <input type="text" id="name" name="name" class="app-input" value="{{ old('name') }}" required placeholder="{{ __('e.g. Ahmed Hassan') }}" autocomplete="name">
                        @error('name') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="email" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Email') }} <span class="text-rose-400">*</span></label>
                        <input type="email" id="email" name="email" class="app-input" value="{{ old('email') }}" required placeholder="{{ __('name@company.com') }}" autocomplete="email">
                        @error('email') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Password') }} <span class="text-rose-400">*</span></label>
                        <input type="password" id="password" name="password" class="app-input" required minlength="8" placeholder="{{ __('Min 8 characters') }}" autocomplete="new-password">
                        @error('password') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Confirm Password') }} <span class="text-rose-400">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="app-input" required autocomplete="new-password">
                        @error('password_confirmation') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            {{-- Role & Status --}}
            <section class="app-card app-card--gradient space-y-5">
                <div class="flex items-center gap-3 border-b border-white/5 pb-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-500/15 text-violet-400">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <div>
                        <h2 class="text-lg font-semibold text-white">{{ __('Role & Status') }}</h2>
                        <p class="text-xs text-slate-400">{{ __('The role defines what the account can do.') }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="role" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Role') }} <span class="text-rose-400">*</span></label>
                        <select id="role" name="role" class="app-select" required x-model="selectedRole">
                            <option value="" disabled @selected(old('role') === null)>{{ __('Select a role...') }}</option>
                            @foreach ($roleOptions as $roleName)
                                <option value="{{ $roleName }}" @selected(old('role') === $roleName)>{{ $roleName }}</option>
                            @endforeach
                        </select>
                        @error('role') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div x-show="selectedRole === 'Administrator'" x-cloak x-transition class="flex items-start gap-3 rounded-xl border border-amber-500/30 bg-amber-500/10 px-4 py-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4M12 17h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/></svg>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-amber-300">{{ __('Administrator access warning') }}</p>
                            <p class="mt-1 text-xs leading-5 text-amber-200/80">{{ __('This role grants full control including: managing users, projects, units, CRM, settings and banners. The user will see the Settings section and can change global configuration.') }}</p>
                        </div>
                    </div>

                    <label class="flex items-center justify-between gap-3 rounded-xl border border-white/5 bg-white/5 p-4 transition hover:bg-white/10">
                        <div>
                            <p class="text-sm font-semibold text-white">{{ __('Active account') }}</p>
                            <p class="text-xs text-slate-400">{{ __('Account can log in immediately') }}</p>
                        </div>
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="h-5 w-5 shrink-0 rounded border-white/10 bg-slate-900 text-brand-600 focus:ring-brand-500/20">
                    </label>

                    <div class="rounded-xl border border-brand-500/20 bg-brand-500/10 p-3">
                        <p class="text-xs leading-relaxed text-slate-400">
                            {{ __('Direct permissions below are added on top of the role permissions and can be granted individually.') }}
                        </p>
                    </div>
                </div>
            </section>

            {{-- Live dashboard sidebar preview --}}
            <section class="app-card app-card--gradient space-y-4 lg:col-start-2" x-data>
                <div class="flex flex-wrap items-center gap-2 border-b border-white/5 pb-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500/15 text-amber-400">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke-width="1.8"/></svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <h2 class="text-lg font-semibold text-white">{{ __('Dashboard preview') }}</h2>
                        <p class="text-xs text-slate-400">{{ __('Live preview of the control panel sidebar for the selected role and permissions.') }}</p>
                    </div>
                    <span class="badge badge-brand" x-show="selectedRole !== ''" x-cloak x-text="selectedRole"></span>
                    <span class="badge badge-muted" x-show="selectedRole === ''" x-cloak>{{ __('No role selected') }}</span>
                </div>

                <p class="rounded-xl border border-white/5 bg-slate-950/40 px-3 py-2 text-xs leading-relaxed text-slate-400">
                    <span class="font-semibold text-white" x-text="visibleCount + ' / 10'"></span>
                    {{ __('sections will appear in this user dashboard.') }}
                    <span x-show="!adminGroupVisible && selectedRole !== ''" x-cloak>
                        — {{ __('The Administration section is fully hidden for this role.') }}
                    </span>
                </p>

                {{-- Mock sidebar --}}
                <div class="overflow-hidden rounded-2xl border border-white/10 bg-slate-950/60">
                    {{-- Menu group --}}
                    <div class="border-b border-white/5 px-4 py-2.5">
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">{{ __('Menu') }}</p>
                    </div>
                    <div class="space-y-1 p-2">
                        @php
                            $mainItems = [
                                ['label' => 'Dashboard', 'icon' => '<path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke-width="1.8"/><path d="M9 22V12h6v10" stroke-width="1.8"/>'],
                                ['label' => 'CRM', 'icon' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="9" cy="7" r="4" stroke-width="1.8"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-width="1.8"/>'],
                                ['label' => 'Projects', 'icon' => '<path d="M3 21h18M3 7v1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7M4 21V4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v17" stroke-width="1.8"/>'],
                            ];
                        @endphp
                        @foreach ($mainItems as $item)
                            <div class="flex items-center gap-2.5 rounded-xl bg-brand-600 px-3 py-2 text-[13px] font-semibold text-white">
                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">{!! $item['icon'] !!}</svg>
                                <span class="flex-1 truncate">{{ __($item['label']) }}</span>
                                <svg class="h-3.5 w-3.5 text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg>
                            </div>
                        @endforeach
                    </div>

                    {{-- Administration group --}}
                    <div x-show="adminGroupVisible" x-cloak>
                        <div class="border-y border-white/5 bg-white/[0.03] px-4 py-2.5">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">{{ __('Administration') }}</p>
                        </div>
                        <div class="space-y-1 p-2">
                            @php
                                $adminItems = [
                                    ['label' => 'Reports', 'perm' => 'view reports', 'icon' => '<path d="M21.21 15.89A10 10 0 1 1 8 2.83" stroke-width="1.8"/><path d="M22 12A10 10 0 0 0 12 2v10z" stroke-width="1.8"/>'],
                                    ['label' => 'Site Analytics', 'perm' => 'view reports', 'icon' => '<path d="M3 3v18h18" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M7 14l4-4 3 3 5-6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>'],
                                    ['label' => 'Trash', 'role' => true, 'icon' => '<path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/>'],
                                    ['label' => 'Users', 'perm' => 'manage users', 'icon' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="9" cy="7" r="4" stroke-width="1.8"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-width="1.8"/>'],
                                    ['label' => 'Banners', 'perm' => 'manage settings', 'icon' => '<rect x="3" y="3" width="18" height="18" rx="2" stroke-width="1.8"/><circle cx="8.5" cy="8.5" r="1.5" stroke-width="1.8"/><path d="m21 15-5-5L5 21" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>'],
                                    ['label' => 'Settings', 'perm' => 'manage settings', 'icon' => '<path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke-width="1.8"/>'],
                                ];
                            @endphp
                            @foreach ($adminItems as $item)
                                @php
                                    $show = $item['role'] ?? false
                                        ? "trashVisible"
                                        : "hasPerm('" . $item['perm'] . "')";
                                @endphp
                                <div class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-[13px] font-semibold transition" :class="{{ $show }} ? 'bg-brand-600 text-white' : 'bg-white/[0.02] text-slate-600'">
                                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">{!! $item['icon'] !!}</svg>
                                    <span class="flex-1 truncate">{{ __($item['label']) }}</span>
                                    <svg x-show="{{ $show }}" x-cloak class="h-3.5 w-3.5 text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg>
                                    <svg x-show="!{{ $show }}" x-cloak class="h-3.5 w-3.5 text-slate-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-10-8-10-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 10 8 10 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24M1 1l22 22"/></svg>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Administration hidden note --}}
                    <div x-show="!adminGroupVisible && selectedRole !== ''" x-cloak class="border-y border-white/5 bg-white/[0.02] px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-600">{{ __('Administration') }} — {{ __('Hidden') }}</p>
                        <p class="mt-1 text-xs leading-5 text-slate-500">{{ __('This role does not include reports, analytics, users, banners or settings.') }}</p>
                    </div>

                    {{-- Tools group --}}
                    <div class="border-t border-white/5 px-4 py-2.5">
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">{{ __('Tools') }}</p>
                    </div>
                    <div class="space-y-1 p-2">
                        <div class="flex items-center gap-2.5 rounded-xl bg-brand-600 px-3 py-2 text-[13px] font-semibold text-white">
                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="4" y="2" width="16" height="20" rx="2" stroke-width="1.8"/><path d="M8 6h8M8 10h8M8 14h8M8 18h8" stroke-width="1.8"/></svg>
                            <span class="flex-1 truncate">{{ __('Calculator') }}</span>
                            <svg class="h-3.5 w-3.5 text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg>
                        </div>
                    </div>
                </div>

                <p class="text-center text-[11px] text-slate-500">
                    {{ __('Tip: checking direct permissions below adds extra sections on top of the role.') }}
                </p>
            </section>

            {{-- Permissions --}}
            <section class="app-card app-card--gradient space-y-5 lg:col-span-2">
                <div class="flex items-center gap-3 border-b border-white/5 pb-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-400">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M9 11l3 3L22 4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </span>
                    <div>
                        <h2 class="text-lg font-semibold text-white">{{ __('Permissions') }}</h2>
                        <p class="text-xs text-slate-400">{{ __('Optional direct permissions — leave unchecked to rely on the role only.') }}</p>
                    </div>
                </div>

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
                                        <input type="checkbox" name="permissions[]" value="{{ $perm }}" x-model="directPerms" @checked(in_array($perm, old('permissions', []), true)) class="h-3.5 w-3.5 rounded border-white/10 bg-slate-900 text-brand-600 focus:ring-brand-500/20">
                                        {{ $perm }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </form>
    </div>
@endsection
