@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <section class="dashboard-hero-card p-6 sm:p-8">
        <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="mobile-section-title">{{ __('Access control') }}</p>
                <h1 class="mt-2 text-3xl font-bold text-white">{{ __('Departments') }}</h1>
                <p class="mt-2 text-sm text-slate-400">{{ __('Create departments and assign users to them.') }}</p>
            </div>
            <a href="{{ route('dashboard.users.index') }}" class="app-button--ghost">{{ __('Back to Users') }}</a>
        </div>
    </section>

    @if ($errors->any())
        <div class="rounded-2xl border border-rose-500/30 bg-rose-500/10 p-4 text-sm text-rose-300">{{ $errors->first() }}</div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[minmax(18rem,0.7fr)_minmax(0,1.3fr)] lg:items-start">
        <form method="POST" action="{{ route('dashboard.departments.store') }}" class="app-card app-card--gradient space-y-4">
            @csrf
            <h2 class="text-lg font-semibold text-white">{{ __('Add Department') }}</h2>
            <div>
                <label for="name" class="mb-2 block text-sm text-slate-300">{{ __('Department name') }}</label>
                <input id="name" name="name" value="{{ old('name') }}" class="app-input" required maxlength="255">
            </div>
            <div>
                <label for="description" class="mb-2 block text-sm text-slate-300">{{ __('Description') }}</label>
                <input id="description" name="description" value="{{ old('description') }}" class="app-input" maxlength="255">
            </div>
            <label class="flex items-center gap-3 text-sm text-slate-300">
                <input type="checkbox" name="is_active" value="1" checked class="h-5 w-5 rounded border-white/10 bg-slate-900 text-brand-600">
                {{ __('Active') }}
            </label>
            <button class="app-button w-full justify-center" type="submit">{{ __('Add Department') }}</button>
        </form>

        <section class="app-card app-card--gradient space-y-4">
            <div class="flex items-center justify-between border-b border-white/5 pb-4">
                <h2 class="text-lg font-semibold text-white">{{ __('Saved Departments') }}</h2>
                <span class="badge badge-brand">{{ $departments->count() }}</span>
            </div>
            <div class="space-y-3">
                @forelse ($departments as $department)
                    <div class="rounded-2xl border border-white/5 bg-white/[0.03] p-4">
                        <form method="POST" action="{{ route('dashboard.departments.update', $department) }}" class="grid gap-3 sm:grid-cols-[1fr_1.4fr_auto] sm:items-end">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="mb-1 block text-xs text-slate-400">{{ __('Department name') }}</label>
                                <input name="name" value="{{ $department->name }}" class="app-input" required maxlength="255">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-slate-400">{{ __('Description') }}</label>
                                <input name="description" value="{{ $department->description }}" class="app-input" maxlength="255">
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="flex items-center gap-2 text-xs text-slate-300">
                                    <input type="checkbox" name="is_active" value="1" @checked($department->is_active) class="h-5 w-5 rounded border-white/10 bg-slate-900 text-brand-600">
                                    {{ __('Active') }}
                                </label>
                                <button class="app-button--ghost" type="submit">{{ __('Save') }}</button>
                            </div>
                        </form>
                        <div class="mt-3 flex items-center justify-between border-t border-white/5 pt-3">
                            <span class="text-xs text-slate-400">{{ trans_choice(':count user|:count users', $department->users_count, ['count' => $department->users_count]) }}</span>
                            <form method="POST" action="{{ route('dashboard.departments.destroy', $department) }}" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-rose-400 hover:text-rose-300">{{ __('Delete') }}</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="py-10 text-center text-sm text-slate-400">{{ __('No departments yet.') }}</p>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
