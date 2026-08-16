@php
    $items = [
        ['label' => __('Home'), 'route' => 'home', 'icon' => 'home'],
        ['label' => __('Projects'), 'route' => 'public.projects.index', 'icon' => 'building'],
        ['label' => __('Calculator'), 'route' => 'installments.index', 'icon' => 'calculator'],
    ];

    $items[] = ['label' => __('Menu'), 'action' => 'menuOpen = true', 'icon' => 'menu'];

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
                    @case('building')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16" stroke-width="1.8"/><path d="M9 7h2M13 7h2M9 11h2M13 11h2M9 15h2M13 15h2" stroke-width="1.8" stroke-linecap="round"/></svg>
                        @break
                    @case('calculator')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="5" y="3" width="14" height="18" rx="3" stroke-width="1.8"/><path d="M8 7h8M8 11h2M12 11h2M16 11h0M8 15h2M12 15h2M16 15h0" stroke-width="1.8" stroke-linecap="round"/></svg>
                        @break
                    @case('menu')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 6h16M4 12h16M4 18h16" stroke-width="1.8" stroke-linecap="round"/></svg>
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
