@extends('layouts.dashboard')

@section('content')
    @php($isEdit = $team !== null)
    <div class="space-y-6">
        <section class="dashboard-hero-card p-6 sm:p-8">
            <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="mobile-section-title">{{ __('Sales Management') }}</p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ $isEdit ? __('Edit Team') : __('New Sales Team') }}</h1>
                    <p class="mt-2 text-sm text-slate-400">{{ $isEdit ? __('Update the team details and its members.') : __('Create a team, assign a manager, and add its members.') }}</p>
                </div>
                <a href="{{ route('dashboard.sales-teams.index') }}" class="app-button--ghost shrink-0">{{ __('Back to teams') }}</a>
            </div>
        </section>

        <form method="POST" action="{{ $isEdit ? route('dashboard.sales-teams.update', $team) : route('dashboard.sales-teams.store') }}" class="app-card space-y-6 p-5 sm:p-6">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="name" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Team name') }} <span class="text-rose-400">*</span></label>
                    <input type="text" id="name" name="name" class="app-input" value="{{ old('name', $team?->name) }}" maxlength="255" placeholder="{{ __('e.g. Team North / Elite Sales Team') }}" required>
                    @error('name') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Description (optional)') }}</label>
                    <textarea id="description" name="description" class="app-textarea min-h-24" rows="3" maxlength="1000" placeholder="{{ __('Focus area, goals, notes...') }}">{{ old('description', $team?->description) }}</textarea>
                    @error('description') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="manager_id" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Team manager') }}</label>
                    @if ($isGlobal)
                        <select id="manager_id" name="manager_id" class="app-input">
                            <option value="">— {{ __('No manager') }} —</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" @selected(old('manager_id', $team?->manager_id) == $user->id)>
                                    {{ $user->name }}{{ $user->job_title ? ' — '.$user->job_title : '' }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        {{-- Isolation: a Sales Manager can only manage their own team --}}
                        <input type="hidden" name="manager_id" value="{{ auth()->id() }}">
                        <input type="text" class="app-input" value="{{ auth()->user()->name }}" disabled>
                        <p class="mt-1 text-[11px] text-slate-500">{{ __('You manage this team — only an Administrator or Owner can change the manager.') }}</p>
                    @endif
                    @error('manager_id') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-end">
                    <label class="inline-flex cursor-pointer items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                        <input type="checkbox" name="is_active" value="1" class="h-5 w-5 rounded border-white/20 bg-slate-900 text-brand-500 focus:ring-brand-500" @checked(old('is_active', $team?->is_active ?? true))>
                        <span class="text-sm font-semibold text-slate-200">{{ __('Active team') }}</span>
                    </label>
                </div>
            </div>

            <div>
                <div class="mb-2 flex items-center justify-between gap-3">
                    <label class="block text-sm font-medium text-slate-300">{{ __('Team members') }}</label>
                    <span class="text-xs text-slate-500">{{ __('Select all salespeople in this team') }}</span>
                </div>
                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($users as $user)
                        <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3 transition hover:border-brand-500/40 hover:bg-white/[0.08]">
                            <input type="checkbox" name="members[]" value="{{ $user->id }}" class="h-5 w-5 rounded border-white/20 bg-slate-900 text-brand-500 focus:ring-brand-500" @checked(in_array($user->id, old('members', $team?->members->pluck('id')->all() ?? []), true))>
                            <span class="min-w-0">
                                <span class="block truncate text-sm font-semibold text-white">{{ $user->name }}</span>
                                <span class="block truncate text-[11px] text-slate-500">
                                    {{ $user->roles->pluck('name')->implode(', ') ?: __('No role') }}
                                    {{ $user->job_title ? ' · '.$user->job_title : '' }}
                                </span>
                            </span>
                        </label>
                    @empty
                        <p class="col-span-full text-sm text-slate-500">{{ __('No active users available. Create users first.') }}</p>
                    @endforelse
                </div>
                @error('members') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
            </div>

            <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-xs text-slate-500">{{ __('Leads assigned to team members can be filtered by team.') }}</p>
                <div class="flex gap-3">
                    <a href="{{ route('dashboard.sales-teams.index') }}" class="app-button--ghost">{{ __('Cancel') }}</a>
                    <button type="submit" class="app-button">{{ $isEdit ? __('Save changes') : __('Create team') }}</button>
                </div>
            </div>
        </form>
    </div>
@endsection
