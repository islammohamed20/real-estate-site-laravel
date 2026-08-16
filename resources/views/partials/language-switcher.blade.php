@php
    $locale = app()->getLocale();
    $nextLocale = $locale === 'ar' ? 'en' : 'ar';
    $nextLabel = $locale === 'ar' ? 'English' : 'العربية';
@endphp

<form method="POST" action="/locale" class="inline-flex items-center">
    @csrf
    <input type="hidden" name="locale" value="{{ $nextLocale }}">
    <button
        type="submit"
        class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-semibold text-slate-200 transition-all duration-300 hover:border-brand-500/40 hover:bg-white/10 hover:text-white active:scale-95 shadow-sm"
        aria-label="{{ __('Switch language to') }} {{ $nextLabel }}"
        title="{{ __('Switch language to') }} {{ $nextLabel }}"
    >
        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
            <circle cx="12" cy="12" r="9" stroke-width="1.8"/>
            <path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18" stroke-width="1.8"/>
        </svg>
        <span>{{ $nextLabel }}</span>
    </button>
</form>
