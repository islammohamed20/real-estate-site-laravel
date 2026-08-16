@extends('layouts.dashboard')

@section('content')
    <div class="space-y-6">
        <section class="dashboard-hero-card p-6 sm:p-8">
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-brand-500/20 blur-3xl"></div>

            <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="mobile-section-title">{{ __('Banners') }}</p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">
                        {{ $banner ? __('Edit banner') : __('Create banner') }}
                    </h1>
                    <p class="mt-2 text-sm text-slate-400">
                        {{ __('Banners appear in the slider under the home page hero with the chosen effect and slide duration.') }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('dashboard.banners.index') }}" class="app-button--ghost">{{ __('Cancel') }}</a>
                </div>
            </div>
        </section>

        <form method="POST" action="{{ $banner ? route('dashboard.banners.update', $banner) : route('dashboard.banners.store') }}" enctype="multipart/form-data" class="max-w-3xl">
            @csrf
            @if($banner) @method('PUT') @endif

            <section class="app-card app-card--gradient space-y-5">
                <div class="flex items-center gap-3 border-b border-white/5 pb-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-500/15 text-brand-400">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="1.8"/><circle cx="8.5" cy="8.5" r="1.5" stroke-width="1.8"/><path d="m21 15-5-5L5 21" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <h2 class="text-lg font-semibold text-white">{{ __('Banner content') }}</h2>
                </div>

                <div class="space-y-4">
                    <label class="space-y-2">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-300">{{ __('Banner Title') }}</span>
                        <input class="app-input" name="title" value="{{ old('title', $banner?->title) }}" placeholder="{{ __('e.g. Winter promotions up to 15% off') }}">
                        @error('title')<p class="text-xs text-rose-400">{{ $message }}</p>@enderror
                    </label>

                    <label class="space-y-2">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-300">{{ __('Banner Subtitle') }}</span>
                        <textarea class="app-textarea min-h-24" name="subtitle" placeholder="{{ __('Short supporting text shown under the title') }}">{{ old('subtitle', $banner?->subtitle) }}</textarea>
                        @error('subtitle')<p class="text-xs text-rose-400">{{ $message }}</p>@enderror
                    </label>

                    <label class="space-y-2">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-300">{{ __('Link URL') }}</span>
                        <input class="app-input" name="link_url" value="{{ old('link_url', $banner?->link_url) }}" placeholder="https://...">
                        @error('link_url')<p class="text-xs text-rose-400">{{ $message }}</p>@enderror
                    </label>

                    <label class="space-y-2">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-300">{{ __('Banner Image') }}</span>
                        <input class="app-input file:mr-3 file:rounded-lg file:border-0 file:bg-brand-600 file:px-3 file:py-1.5 file:text-xs file:text-white hover:file:bg-brand-500" type="file" name="image" accept="image/png,image/jpg,image/jpeg,image/webp,image/gif">
                        @error('image')<p class="text-xs text-rose-400">{{ $message }}</p>@enderror
                        @if ($banner?->image_path)
                            <img src="{{ asset('storage/'.$banner->image_path) }}" alt="{{ $banner->title }}" class="mt-2 h-32 w-auto rounded-lg border border-white/10 object-contain">
                        @endif
                    </label>
                </div>
            </section>

            <section class="app-card app-card--gradient space-y-5 mt-6">
                <div class="flex items-center gap-3 border-b border-white/5 pb-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-500/15 text-violet-400">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </span>
                    <h2 class="text-lg font-semibold text-white">{{ __('Slider behavior') }}</h2>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="space-y-2">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-300">{{ __('Position') }}</span>
                        <select class="app-select" name="position">
                            @foreach ($positions as $value => $label)
                                <option value="{{ $value }}" @selected(old('position', $banner?->position ?? 'hero') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="space-y-2">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-300">{{ __('Transition Effect') }}</span>
                        <select class="app-select" name="effect">
                            @foreach ($effects as $value => $label)
                                <option value="{{ $value }}" @selected(old('effect', $banner?->effect ?? 'fade') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="space-y-2">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-300">{{ __('Slide Duration (seconds)') }}</span>
                        <input class="app-input" type="number" name="slide_duration" min="2" max="30" value="{{ old('slide_duration', $banner?->slide_duration ?? 5) }}">
                        @error('slide_duration')<p class="text-xs text-rose-400">{{ $message }}</p>@enderror
                    </label>

                    <label class="space-y-2">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-300">{{ __('Sort Order') }}</span>
                        <input class="app-input" type="number" name="sort_order" min="0" value="{{ old('sort_order', $banner?->sort_order ?? 0) }}">
                    </label>
                </div>

                <label class="flex items-center justify-between rounded-xl border border-white/5 bg-white/5 p-4 transition hover:bg-white/10">
                    <div>
                        <p class="text-sm font-semibold text-white">{{ __('Active') }}</p>
                        <p class="text-xs text-slate-400">{{ __('Show this banner on the public website') }}</p>
                    </div>
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $banner?->is_active ?? true)) class="h-5 w-5 rounded border-white/10 bg-slate-900 text-brand-600 focus:ring-brand-500/20">
                </label>
            </section>

            <div class="mt-6 flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 p-4">
                <p class="text-sm text-slate-400">{{ __('The first active banner controls the home slider effect and duration.') }}</p>
                <button type="submit" class="app-button">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z" stroke-width="1.8"/><path d="M17 21v-8H7v8M7 3v5h8" stroke-width="1.8"/></svg>
                    {{ $banner ? __('Update Banner') : __('Create Banner') }}
                </button>
            </div>
        </form>
    </div>
@endsection
