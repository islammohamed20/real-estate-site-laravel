@extends('layouts.dashboard')

@section('content')
    <div class="space-y-6">
        <section class="dashboard-hero-card p-6 sm:p-8">
            <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="mobile-section-title">{{ __('Edit Section') }}</p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ $section->title ?: __(':key section', ['key' => $section->key]) }}</h1>
                    <p class="mt-2 text-sm text-slate-400">{{ __('Key:') }} <span class="font-mono text-slate-300">{{ $section->key }}</span></p>
                </div>
                <a href="{{ route('dashboard.home-sections.index') }}" class="app-button--ghost">{{ __('Back to sections') }}</a>
            </div>
        </section>

        <form method="POST" action="{{ route('dashboard.home-sections.update', $section) }}" enctype="multipart/form-data" class="app-card space-y-5 p-5 sm:p-6">
            @csrf
            @method('PUT')

            <div>
                <label for="title" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Title') }}</label>
                <input type="text" id="title" name="title" class="app-input" value="{{ old('title', $section->title) }}" maxlength="255">
                @error('title') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="subtitle" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Subtitle / Eyebrow') }}</label>
                <input type="text" id="subtitle" name="subtitle" class="app-input" value="{{ old('subtitle', $section->subtitle) }}" maxlength="255">
                @error('subtitle') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="content" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Content') }}</label>
                <textarea id="content" name="content" class="app-textarea min-h-32" rows="5">{{ old('content', $section->content) }}</textarea>
                @error('content') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="image" class="mb-2 block text-sm font-medium text-slate-300">{{ __('Section image (optional)') }}</label>
                <input type="file" id="image" name="image" accept="image/png,image/jpeg,image/webp" class="block w-full rounded-xl border border-white/10 bg-slate-950/40 px-4 py-3 text-sm text-slate-300 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-600 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-white hover:file:bg-brand-500">
                @error('image') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                @if ($section->image_path)
                    <div class="mt-3">
                        <img src="{{ asset('storage/'.$section->image_path) }}" alt="" class="h-32 w-auto rounded-2xl border border-white/10 object-cover">
                    </div>
                @endif
            </div>

            <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-xs text-slate-500">{{ __('Leave fields empty to use the default content.') }}</p>
                <div class="flex gap-3">
                    <a href="{{ route('dashboard.home-sections.index') }}" class="app-button--ghost">{{ __('Cancel') }}</a>
                    <button type="submit" class="app-button">{{ __('Save changes') }}</button>
                </div>
            </div>
        </form>
    </div>
@endsection
