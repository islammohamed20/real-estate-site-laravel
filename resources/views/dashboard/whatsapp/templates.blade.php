@extends('layouts.dashboard')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <section class="dashboard-hero-card p-5 sm:p-6">
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-brand-500/20 blur-3xl"></div>
            <div class="relative flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-500/15 text-brand-400">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 4h11a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H9z" stroke-linecap="round"/><path d="M4 20a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2v16z" stroke-linecap="round"/></svg>
                    </span>
                    <div>
                        <h1 class="text-lg font-bold text-white">{{ __('WhatsApp Templates') }}</h1>
                        <p class="text-xs text-slate-400">{{ __('Quick replies available in the chat composer.') }}</p>
                    </div>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('dashboard.whatsapp.index') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:bg-white/10">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        {{ __('Back to panel') }}
                    </a>
                </div>
            </div>
        </section>

        @if (session('status'))
            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="flex items-center gap-3 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12l5 5L20 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(18rem,0.45fr)]">
            {{-- Templates list --}}
            <section class="app-card app-card--gradient">
                <div class="flex items-center justify-between border-b border-white/10 pb-3">
                    <h2 class="text-sm font-bold text-white">{{ __('Saved templates') }} <span class="ms-1 text-xs font-normal text-slate-500">({{ $templates->count() }})</span></h2>
                </div>
                <div class="mt-4 space-y-3">
                    @forelse ($templates as $template)
                        <div class="flex items-start gap-3 rounded-2xl border border-white/10 bg-slate-950/40 p-3.5">
                            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-brand-500/15 text-xs font-bold text-brand-300">
                                {{ mb_substr($template->name, 0, 1) }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <p class="truncate text-sm font-bold text-white">{{ $template->name }}</p>
                                    @if ($template->is_active)
                                        <span class="shrink-0 rounded-md bg-emerald-500/15 px-1.5 py-0.5 text-[10px] font-bold text-emerald-300">{{ __('Active') }}</span>
                                    @else
                                        <span class="shrink-0 rounded-md bg-slate-600/30 px-1.5 py-0.5 text-[10px] font-bold text-slate-400">{{ __('Inactive') }}</span>
                                    @endif
                                </div>
                                <p class="mt-1 line-clamp-2 whitespace-pre-wrap break-words text-xs leading-relaxed text-slate-400">{{ $template->body }}</p>
                                <p class="mt-1.5 text-[10px] text-slate-600">{{ $template->createdBy?->name ?? '—' }} · {{ $template->created_at?->format('Y-m-d H:i') }}</p>
                            </div>
                            <form method="POST" action="{{ route('dashboard.whatsapp.templates.destroy', $template) }}" onsubmit="return confirm('{{ __('Delete this template?') }}')" class="shrink-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-lg p-1.5 text-slate-500 transition hover:bg-rose-500/10 hover:text-rose-400" title="{{ __('Delete') }}">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-linecap="round"/></svg>
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="flex flex-col items-center gap-2 py-12 text-center">
                            <svg class="h-10 w-10 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 4h11a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H9z" stroke-linecap="round"/><path d="M4 20a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2v16z" stroke-linecap="round"/></svg>
                            <p class="text-sm text-slate-500">{{ __('No templates yet — create your first quick reply.') }}</p>
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Create form --}}
            <section class="app-card app-card--gradient h-fit">
                <h2 class="text-sm font-bold text-white">{{ __('New template') }}</h2>
                <form method="POST" action="{{ route('dashboard.whatsapp.templates.store') }}" class="mt-4 space-y-4">
                    @csrf
                    <label class="block">
                        <span class="mb-1.5 block text-xs font-semibold text-slate-300">{{ __('Name') }}</span>
                        <input name="name" required maxlength="255" placeholder="{{ __('e.g. Welcome message') }}" class="app-input">
                    </label>
                    <label class="block">
                        <span class="mb-1.5 block text-xs font-semibold text-slate-300">{{ __('Message body') }}</span>
                        <textarea name="body" required maxlength="4000" rows="5" placeholder="{{ __('Hello [name], welcome to our projects...') }}" class="app-input resize-none"></textarea>
                    </label>
                    <label class="flex items-center gap-2 text-sm text-slate-300">
                        <input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded border-white/20 bg-slate-950/60 accent-emerald-500">
                        {{ __('Active (shown in the chat composer)') }}
                    </label>
                    <button type="submit" class="w-full rounded-xl bg-brand-600 py-2.5 text-sm font-bold text-white shadow-lg shadow-brand-600/20 transition hover:bg-brand-500">
                        {{ __('Save template') }}
                    </button>
                </form>
            </section>
        </div>
    </div>
@endsection
