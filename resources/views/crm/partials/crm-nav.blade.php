@php
    $currentRoute = request()->route()?->getName() ?? '';

    $crmNavItems = [
        [
            'route' => 'dashboard.crm.index',
            'match' => 'dashboard.crm.index',
            'label' => __('Overview'),
            'icon' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="7" height="7" rx="1.5" stroke-width="1.8"/><rect x="14" y="3" width="7" height="7" rx="1.5" stroke-width="1.8"/><rect x="3" y="14" width="7" height="7" rx="1.5" stroke-width="1.8"/><rect x="14" y="14" width="7" height="7" rx="1.5" stroke-width="1.8"/></svg>',
        ],
        [
            'route' => 'dashboard.crm.leads.index',
            'match' => 'dashboard.crm.leads',
            'label' => __('Leads'),
            'icon' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="8.5" cy="7" r="4" stroke-width="1.8"/><path d="M20 8v6M23 11h-6" stroke-width="1.8" stroke-linecap="round"/></svg>',
        ],
        [
            'route' => 'dashboard.crm.customers.index',
            'match' => 'dashboard.crm.customers',
            'label' => __('Customers'),
            'icon' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="9" cy="7" r="4" stroke-width="1.8"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-width="1.8"/></svg>',
        ],
        [
            'route' => 'dashboard.crm.organizations.index',
            'match' => 'dashboard.crm.organizations',
            'label' => __('Organizations'),
            'icon' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 21h18M3 7v1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7M4 21V4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v17" stroke-width="1.8"/></svg>',
        ],
        [
            'route' => 'dashboard.crm.deals.index',
            'match' => 'dashboard.crm.deals',
            'label' => __('Deals'),
            'icon' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 4h11a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H9z" stroke-width="1.8"/><path d="M4 20a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2v16z" stroke-width="1.8"/></svg>',
        ],
        [
            'route' => 'dashboard.crm.offers.index',
            'match' => 'dashboard.crm.offers',
            'label' => __('Offers'),
            'icon' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-width="1.8"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" stroke-width="1.8" stroke-linecap="round"/></svg>',
        ],
        [
            'route' => 'dashboard.crm.reservations.index',
            'match' => 'dashboard.crm.reservations',
            'label' => __('Reservations'),
            'icon' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 21l-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z" stroke-width="1.8" stroke-linejoin="round"/></svg>',
        ],
        [
            'route' => 'dashboard.crm.plans.index',
            'match' => 'dashboard.crm.plans',
            'label' => __('Plans'),
            'icon' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 4h11a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H9z" stroke-width="1.8"/><path d="M4 20a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2v16z" stroke-width="1.8"/><path d="M12 8v4M12 16h.01" stroke-width="1.8" stroke-linecap="round"/></svg>',
        ],
        [
            'route' => 'dashboard.crm.documents.index',
            'match' => 'dashboard.crm.documents',
            'label' => __('Documents'),
            'icon' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" stroke-width="1.8" stroke-linejoin="round"/></svg>',
        ],
        [
            'route' => 'dashboard.crm.tasks.index',
            'match' => 'dashboard.crm.tasks',
            'label' => __('Tasks'),
            'icon' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 11l3 3L22 4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" stroke-width="1.8" stroke-linecap="round"/></svg>',
        ],
        [
            'route' => 'dashboard.crm.follow_ups.index',
            'match' => 'dashboard.crm.follow_ups',
            'label' => __('Follow-ups'),
            'icon' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="9" stroke-width="1.8"/><path d="M12 7v5l3 3" stroke-width="1.8" stroke-linecap="round"/></svg>',
        ],
        [
            'route' => 'dashboard.crm.reports.index',
            'match' => 'dashboard.crm.reports',
            'label' => __('Reports'),
            'icon' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21.21 15.89A10 10 0 1 1 8 2.83" stroke-width="1.8" stroke-linecap="round"/><path d="M22 12A10 10 0 0 0 12 2v10z" stroke-width="1.8"/></svg>',
        ],
        [
            'route' => 'dashboard.crm.search',
            'match' => 'dashboard.crm.search',
            'label' => __('Search'),
            'icon' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="8" stroke-width="1.8"/><path d="m21 21-4.3-4.3" stroke-width="1.8" stroke-linecap="round"/></svg>',
        ],
        [
            'route' => 'dashboard.crm.trash.index',
            'match' => 'dashboard.crm.trash',
            'label' => __('Trash'),
            'icon' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>',
        ],
    ];
@endphp

<nav class="overflow-hidden rounded-3xl border border-white/10 bg-white/5 p-2 shadow-lg lg:hidden" aria-label="{{ __('CRM sections') }}">
    <div class="flex gap-1.5 overflow-x-auto no-scrollbar">
        @foreach ($crmNavItems as $item)
            @php($active = $currentRoute === $item['match'] || str_starts_with($currentRoute, $item['match'] . '.'))
            <a
                href="{{ route($item['route']) }}"
                class="inline-flex shrink-0 items-center gap-2 whitespace-nowrap rounded-2xl px-4 py-2.5 text-sm font-semibold transition-all duration-300 {{ $active ? 'bg-gradient-to-r from-brand-600 to-violet-600 text-white shadow-lg shadow-brand-600/20' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}"
                @if ($active) aria-current="page" @endif
            >
                {!! $item['icon'] !!}
                {{ $item['label'] }}
            </a>
        @endforeach
    </div>
</nav>
