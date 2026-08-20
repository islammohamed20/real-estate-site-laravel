@extends('layouts.dashboard')

@section('content')
    @php($isEdit = $template !== null)
    <div class="space-y-6">
        <section class="dashboard-hero-card p-6 sm:p-8"><div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"><div><p class="mobile-section-title">{{ __('Communication') }}</p><h1 class="mt-2 text-3xl font-bold text-white">{{ $isEdit ? __('Edit Email Template') : __('New Email Template') }}</h1></div><a href="{{ route('dashboard.email-templates.index') }}" class="app-button--ghost">{{ __('Back') }}</a></div></section>
        <form method="POST" action="{{ $isEdit ? route('dashboard.email-templates.update', $template) : route('dashboard.email-templates.store') }}" class="app-card space-y-5 p-5 sm:p-6">
            @csrf @if($isEdit) @method('PUT') @endif
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2"><div><label class="mb-2 block text-sm text-slate-300">{{ __('Key') }}</label><input name="key" class="app-input" value="{{ old('key', $template?->key) }}" placeholder="welcome_customer" required @disabled($isEdit)>@if($isEdit)<input type="hidden" name="key" value="{{ $template->key }}">@endif</div><div><label class="mb-2 block text-sm text-slate-300">{{ __('Name') }}</label><input name="name" class="app-input" value="{{ old('name', $template?->name) }}" required></div></div>
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/5 p-4">
                    <p class="mb-3 text-sm font-bold text-emerald-300">{{ __('English') }}</p>
                    <label class="mb-2 block text-xs text-slate-300">{{ __('Subject') }}</label><input name="subject_en" class="app-input" value="{{ old('subject_en', $template?->subject_en ?: $template?->subject) }}" required>
                    <label class="mb-2 mt-4 block text-xs text-slate-300">{{ __('Body') }}</label><textarea name="body_en" class="app-textarea min-h-56" rows="8" required>{{ old('body_en', $template?->body_en ?: $template?->body) }}</textarea>
                </div>
                <div dir="rtl" class="rounded-2xl border border-brand-500/20 bg-brand-500/5 p-4">
                    <p class="mb-3 text-sm font-bold text-brand-300">{{ __('العربية') }}</p>
                    <label class="mb-2 block text-xs text-slate-300">{{ __('الموضوع') }}</label><input dir="rtl" name="subject_ar" class="app-input" value="{{ old('subject_ar', $template?->subject_ar) }}" required>
                    <label class="mb-2 mt-4 block text-xs text-slate-300">{{ __('المحتوى') }}</label><textarea dir="rtl" name="body_ar" class="app-textarea min-h-56" rows="8" required>{{ old('body_ar', $template?->body_ar) }}</textarea>
                </div>
            </div>
            <div class="rounded-2xl border border-brand-500/20 bg-brand-500/5 p-4"><p class="text-xs font-bold text-brand-300">{{ __('Available variables') }}</p><div class="mt-2 flex flex-wrap gap-2">@foreach($variables as $key => $label)<code class="rounded-lg bg-slate-950/60 px-2 py-1 text-[11px] text-slate-300">{{ '{' }}{{ '{' }}{{ $key }}{{ '}' }}{{ '}' }}</code>@endforeach</div></div>
            <label class="inline-flex items-center gap-3 text-sm text-slate-300"><input type="checkbox" name="is_active" value="1" class="h-5 w-5 rounded border-white/20 bg-slate-900 text-brand-500" @checked(old('is_active', $template?->is_active ?? true))> {{ __('Active template') }}</label>
            <div class="flex flex-wrap justify-end gap-3"><a href="{{ route('dashboard.email-templates.index') }}" class="app-button--ghost">{{ __('Cancel') }}</a><button class="app-button">{{ __('Save template') }}</button></div>
        </form>
        @if($isEdit)
            <form method="POST" action="{{ route('dashboard.email-templates.test', $template) }}" class="app-card flex flex-wrap items-end gap-3 p-5">
                @csrf
                <div class="min-w-56 flex-1">
                    <label class="mb-2 block text-sm text-slate-300">{{ __('Send test to') }}</label>
                    <input type="email" name="email" value="{{ auth()->user()->email }}" class="app-input" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm text-slate-300">{{ __('Language') }}</label>
                    <select name="locale" class="app-input">
                        <option value="ar" @selected(app()->getLocale() === 'ar')>العربية</option>
                        <option value="en" @selected(app()->getLocale() === 'en')>English</option>
                    </select>
                </div>
                <button class="app-button">{{ __('Send test email') }}</button>
            </form>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('dashboard.email-templates.preview', ['template' => $template, 'locale' => 'ar']) }}" class="app-button--ghost px-3 py-2 text-xs">{{ __('Preview Arabic') }}</a>
                <a href="{{ route('dashboard.email-templates.preview', ['template' => $template, 'locale' => 'en']) }}" class="app-button--ghost px-3 py-2 text-xs">{{ __('Preview English') }}</a>
            </div>
        @endif
    </div>
@endsection
