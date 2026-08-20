@extends('layouts.dashboard')
@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">{{ __('Email Preview') }}</h1>
            <p class="mt-1 text-xs text-slate-400">{{ $locale === 'ar' ? 'العربية' : 'English' }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('dashboard.email-templates.preview', ['template' => $template, 'locale' => 'ar']) }}" class="app-button--ghost px-3 py-2 text-xs {{ $locale === 'ar' ? 'bg-brand-600 text-white' : '' }}">العربية</a>
            <a href="{{ route('dashboard.email-templates.preview', ['template' => $template, 'locale' => 'en']) }}" class="app-button--ghost px-3 py-2 text-xs {{ $locale === 'en' ? 'bg-brand-600 text-white' : '' }}">English</a>
            <a href="{{ route('dashboard.email-templates.edit', $template) }}" class="app-button--ghost">{{ __('Back to edit') }}</a>
        </div>
    </div>
    <article class="app-card bg-white p-6 text-slate-900 sm:p-10" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">
        <h2 class="border-b border-slate-200 pb-4 text-xl font-bold">{{ $rendered['subject'] }}</h2>
        <div class="mt-6 whitespace-pre-line text-sm leading-7">{{ $rendered['body'] }}</div>
    </article>
</div>
@endsection
