<div class="mobile-fab" x-data="{ open: false }">
    <button type="button" class="mobile-fab__button" @click="open = !open" aria-label="{{ __('Quick Actions') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="2.2" stroke-linecap="round"/></svg>
    </button>

    <div x-show="open" x-cloak class="mobile-fab__sheet" @click.outside="open = false">
        <a href="{{ route('dashboard.crm.quick') }}" class="mobile-fab__action">{{ __('+ New Lead') }}</a>
        <a href="{{ route('dashboard.installments.index') }}" class="mobile-fab__action">{{ __('New Offer') }}</a>
        <a href="{{ route('dashboard.installments.index') }}" class="mobile-fab__action">{{ __('Calculator') }}</a>
    </div>
</div>
