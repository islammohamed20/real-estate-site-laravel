@php
    $items = [
        ['label' => __('Dashboard'), 'route' => 'dashboard.home', 'icon' => 'home'],
        ['label' => __('CRM'), 'route' => 'dashboard.crm.index', 'icon' => 'users'],
        ['label' => __('WhatsApp'), 'route' => 'dashboard.whatsapp.index', 'icon' => 'whatsapp'],
        ['label' => __('Calculator'), 'route' => 'dashboard.installments.index', 'icon' => 'calculator'],
    ];

    if (auth()->user()?->can('manage settings')) {
        $items[] = ['label' => __('Settings'), 'route' => 'dashboard.settings.index', 'icon' => 'settings'];
    }

    $user = auth()->user();
    if ($user === null || (! $user->can('view whatsapp') && ! $user->can('view all whatsapp conversations') && ! $user->can('manage crm'))) {
        $items = array_values(array_filter($items, fn ($item) => ($item['route'] ?? '') !== 'dashboard.whatsapp.index'));
    }

    $columns = count($items);
@endphp

<nav class="mobile-bottom-nav" aria-label="Primary navigation">
    <div class="mobile-bottom-nav__grid" style="grid-template-columns: repeat({{ $columns }}, minmax(0, 1fr));">
        @foreach ($items as $item)
            @if (isset($item['action']))
                <button type="button" @click="{{ $item['action'] }}" class="mobile-bottom-nav__item" aria-label="{{ $item['label'] }}">
            @else
                <a href="{{ route($item['route']) }}" class="mobile-bottom-nav__item {{ request()->routeIs($item['route']) ? 'is-active' : '' }}">
            @endif
                <span class="mobile-bottom-nav__icon" aria-hidden="true">
                @switch($item['icon'])
                    @case('home')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 11.5 12 4l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-8.5Z" stroke-width="1.8" stroke-linejoin="round"/></svg>
                        @break
                    @case('users')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M17 20v-1a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v1" stroke-width="1.8" stroke-linecap="round"/><path d="M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke-width="1.8"/></svg>
                        @break
                    @case('calculator')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="5" y="3" width="14" height="18" rx="3" stroke-width="1.8"/><path d="M8 7h8M8 11h2M12 11h2M16 11h0M8 15h2M12 15h2M16 15h0" stroke-width="1.8" stroke-linecap="round"/></svg>
                        @break
                    @case('building')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16" stroke-width="1.8"/><path d="M9 7h2M13 7h2M9 11h2M13 11h2M9 15h2M13 15h2" stroke-width="1.8" stroke-linecap="round"/></svg>
                        @break
                    @case('search')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="8" stroke-width="1.8"/><path d="m21 21-4.3-4.3" stroke-width="1.8" stroke-linecap="round"/></svg>
                        @break
                    @case('whatsapp')
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                        @break
                    @case('settings')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M10 3h4l.6 2.1a7.8 7.8 0 0 1 1.6.9L18.3 5l2 3.5-1.5 1.4a7.8 7.8 0 0 1 0 1.8l1.5 1.4-2 3.5-2.1-.9a7.8 7.8 0 0 1-1.6.9L14 21h-4l-.6-2.1a7.8 7.8 0 0 1-1.6-.9l-2.1.9-2-3.5 1.5-1.4a7.8 7.8 0 0 1 0-1.8L3.7 8.5l2-3.5 2.1.9a7.8 7.8 0 0 1 1.6-.9L10 3Z" stroke-width="1.6" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.5" stroke-width="1.8"/></svg>
                        @break
                @endswitch
                </span>
                <span class="mobile-bottom-nav__label">{{ $item['label'] }}</span>
            @if (isset($item['action']))
                </button>
            @else
                </a>
            @endif
        @endforeach
    </div>
</nav>
