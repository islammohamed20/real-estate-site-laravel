@extends('layouts.dashboard')

@section('content')
    <div class="space-y-6">
        <section class="dashboard-hero-card p-6 sm:p-8">
            <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div><p class="mobile-section-title">{{ __('Communication') }}</p><h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('Email Templates') }}</h1><p class="mt-2 text-sm text-slate-400">{{ __('Manage reusable emails for customers, leads, and sales notifications.') }}</p></div>
                <a href="{{ route('dashboard.email-templates.create') }}" class="app-button shrink-0">+ {{ __('New template') }}</a>
            </div>
        </section>
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            @forelse ($templates as $template)
                <article class="app-card app-card--gradient flex flex-col p-5 {{ !$template->is_active ? 'opacity-60' : '' }}">
                    <div class="flex items-start justify-between gap-3"><div class="min-w-0"><div class="flex flex-wrap gap-2"><span class="badge {{ $template->is_active ? 'badge-success' : 'badge-muted' }}">{{ $template->is_active ? __('Active') : __('Inactive') }}</span><span class="font-mono text-[10px] text-slate-500">{{ $template->key }}</span></div><h2 class="mt-2 truncate text-lg font-bold text-white">{{ $template->name }}</h2><p class="mt-1 truncate text-sm text-brand-300">{{ $template->subject }}</p></div></div>
                    <p class="mt-3 line-clamp-3 whitespace-pre-line text-xs leading-relaxed text-slate-400">{{ $template->body }}</p>
                    <div class="mt-4 flex flex-wrap gap-2 border-t border-white/5 pt-3"><a href="{{ route('dashboard.email-templates.edit', $template) }}" class="app-button--ghost px-3 py-2 text-xs">{{ __('Edit') }}</a><a href="{{ route('dashboard.email-templates.preview', $template) }}" class="app-button--ghost px-3 py-2 text-xs">{{ __('Preview') }}</a><form method="POST" action="{{ route('dashboard.email-templates.toggle', $template) }}" class="contents">@csrf<button class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold text-slate-300">{{ $template->is_active ? __('Disable') : __('Enable') }}</button></form></div>
                </article>
            @empty
                <div class="app-card col-span-full py-12 text-center text-slate-400">{{ __('No email templates yet.') }}</div>
            @endforelse
        </div>
    </div>
@endsection
