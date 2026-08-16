@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    @include('crm.partials.crm-nav')
    <section class="dashboard-hero-card p-6 sm:p-8">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('Search') }}</h1>
            <p class="mt-2 text-sm text-slate-300">{{ __('Results for:') }} <span class="font-semibold text-brand-300">"{{ $query }}"</span></p>
        </div>
    </section>

    @if ($query === '')
        <section class="rounded-3xl border border-white/10 bg-white/5 p-8 text-center text-slate-400">
            <p>{{ __('Enter a search term to find records across CRM and projects.') }}</p>
        </section>
    @else
        <section class="space-y-6">
            @foreach ($results as $type => $items)
                @if ($items->isNotEmpty())
                    <div class="overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-lg p-4">
                        <h2 class="mb-3 text-lg font-semibold text-white">{{ __(ucfirst($type)) }}</h2>
                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($items as $item)
                                <a href="{{ $item instanceof \App\Models\Lead ? route('dashboard.crm.leads.show', $item) : ($item instanceof \App\Models\Customer ? route('dashboard.crm.customers.show', $item) : ($item instanceof \App\Models\Offer ? route('dashboard.crm.offers.show', $item) : ($item instanceof \App\Models\Reservation ? route('dashboard.crm.reservations.show', $item) : ($item instanceof \App\Models\Project ? route('dashboard.projects.show', $item) : '#')))) }}" class="app-card app-card--gradient p-4 block transition hover:border-brand-500/30">
                                    <p class="truncate font-semibold text-white">
                                        {{ $item->name ?? $item->offer_number ?? $item->reservation_number ?? $item->unit_number ?? $item->title ?? '—' }}
                                    </p>
                                    <p class="mt-1 text-sm text-slate-400 truncate">
                                        {{ $item->email ?? $item->phone ?? ($item->project?->name ? $item->project->name . ' #' . $item->unit_number : '') ?? '—' }}
                                    </p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

            @if (collect($results)->every(fn ($items) => $items->isEmpty()))
                <section class="rounded-3xl border border-white/10 bg-white/5 p-8 text-center text-slate-400">
                    <p>{{ __('No results found for your search.') }}</p>
                </section>
            @endif
        </section>
    @endif
</div>
@endsection
