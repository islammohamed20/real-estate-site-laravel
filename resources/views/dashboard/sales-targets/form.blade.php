@extends('layouts.dashboard')

@section('content')
    @php($isEdit = $target !== null)
    <div class="space-y-6">
        <section class="dashboard-hero-card p-6 sm:p-8">
            <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="mobile-section-title">{{ __('Sales Management') }}</p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ $isEdit ? __('Edit Target') : __('New Sales Target') }}</h1>
                    <p class="mt-2 text-sm text-slate-400">{{ $isEdit ? __('Update the targets for this entity.') : __('Set monthly targets for a salesperson or a team.') }}</p>
                </div>
                <a href="{{ route('dashboard.sales-targets.index', ['period' => $period]) }}" class="app-button--ghost shrink-0">{{ __('Back to targets') }}</a>
            </div>
        </section>

        @if ($errors->has('target'))
            <div class="rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">{{ $errors->first('target') }}</div>
        @endif

        <form method="POST" action="{{ $isEdit ? route('dashboard.sales-targets.update', $target) : route('dashboard.sales-targets.store') }}" class="app-card space-y-6 p-5 sm:p-6">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                <div>
                    <label for="period" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Month') }} <span class="text-rose-400">*</span></label>
                    <input type="month" id="period" name="period" class="app-input" value="{{ old('period', $period) }}" required>
                    @error('period') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="sales_team_id" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Team (optional)') }}</label>
                    <select id="sales_team_id" name="sales_team_id" class="app-input">
                        <option value="">— {{ __('No team — individual target') }} —</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}" @selected(old('sales_team_id', $target?->sales_team_id) == $team->id)>{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="user_id" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Salesperson (optional)') }}</label>
                    <select id="user_id" name="user_id" class="app-input">
                        <option value="">— {{ __('No salesperson — team target') }} —</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected(old('user_id', $target?->user_id) == $user->id)>
                                {{ $user->name }}{{ $user->job_title ? ' — '.$user->job_title : '' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-[11px] text-slate-500">{{ __('Select exactly one: a salesperson OR a team.') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div>
                    <label for="leads_target" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Leads target') }}</label>
                    <input type="number" id="leads_target" name="leads_target" min="0" step="1" class="app-input" value="{{ old('leads_target', $target?->leads_target) }}">
                    @error('leads_target') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="offers_target" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Offers target') }}</label>
                    <input type="number" id="offers_target" name="offers_target" min="0" step="1" class="app-input" value="{{ old('offers_target', $target?->offers_target) }}">
                    @error('offers_target') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="reservations_target" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Reservations target') }}</label>
                    <input type="number" id="reservations_target" name="reservations_target" min="0" step="1" class="app-input" value="{{ old('reservations_target', $target?->reservations_target) }}">
                    @error('reservations_target') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="deals_target" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Deals won target') }}</label>
                    <input type="number" id="deals_target" name="deals_target" min="0" step="1" class="app-input" value="{{ old('deals_target', $target?->deals_target) }}">
                    @error('deals_target') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="sm:max-w-xs">
                <label for="deal_value_target" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Deal value target (ج.م)') }}</label>
                <input type="number" id="deal_value_target" name="deal_value_target" min="0" step="0.01" class="app-input" value="{{ old('deal_value_target', $target?->deal_value_target) }}" placeholder="0">
                @error('deal_value_target') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
            </div>

            <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-xs text-slate-500">{{ __('Achievement % is computed automatically from real activity.') }}</p>
                <div class="flex gap-3">
                    <a href="{{ route('dashboard.sales-targets.index', ['period' => $period]) }}" class="app-button--ghost">{{ __('Cancel') }}</a>
                    <button type="submit" class="app-button">{{ $isEdit ? __('Save changes') : __('Create target') }}</button>
                </div>
            </div>
        </form>
    </div>
@endsection
