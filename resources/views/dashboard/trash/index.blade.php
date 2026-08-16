@extends('layouts.dashboard')

@section('content')
    @php
        $projectsCount = $trashedProjects->count();
        $unitsCount = $trashedUnits->count();
        $buildingsCount = $trashedBuildings->count();
        $customersCount = $trashedCustomers->count();
        $leadsCount = $trashedLeads->count();
        $offersCount = $trashedOffers->count();
        $plansCount = $trashedPlans->count();
        $organizationsCount = $trashedOrganizations->count();
        $dealsCount = $trashedDeals->count();
        $contactsCount = $trashedContacts->count();
        $documentsCount = $trashedDocuments->count();
        $conversationsCount = $trashedConversations->count();
        $usersCount = $trashedUsers->count();
        $totalCount = $projectsCount + $unitsCount + $buildingsCount + $customersCount + $leadsCount
            + $offersCount + $plansCount + $organizationsCount + $dealsCount + $contactsCount
            + $documentsCount + $conversationsCount + $usersCount;
    @endphp

    <div class="space-y-6">
        <section class="dashboard-hero-card p-6 sm:p-8">
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-rose-500/10 blur-3xl"></div>

            <div class="relative grid gap-6 lg:grid-cols-[minmax(0,1.4fr)_minmax(16rem,0.9fr)] lg:items-end">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="badge badge-danger">{{ $projectsCount }} {{ __('projects') }}</span>
                        <span class="badge badge-warning">{{ $unitsCount }} {{ __('units') }}</span>
                        <span class="badge badge-muted">{{ $buildingsCount }} {{ __('buildings') }}</span>
                        <span class="badge badge-danger">{{ $customersCount }} {{ __('customers') }}</span>
                        <span class="badge badge-warning">{{ $leadsCount }} {{ __('leads') }}</span>
                        <span class="badge badge-brand">{{ $offersCount }} {{ __('offers') }}</span>
                        <span class="badge badge-brand">{{ $plansCount }} {{ __('plans') }}</span>
                        <span class="badge badge-muted">{{ $organizationsCount }} {{ __('organizations') }}</span>
                        <span class="badge badge-brand">{{ $dealsCount }} {{ __('deals') }}</span>
                        <span class="badge badge-muted">{{ $contactsCount }} {{ __('contacts') }}</span>
                        <span class="badge badge-muted">{{ $documentsCount }} {{ __('documents') }}</span>
                        <span class="badge badge-muted">{{ $conversationsCount }} {{ __('conversations') }}</span>
                        <span class="badge badge-danger">{{ $usersCount }} {{ __('users') }}</span>
                    </div>

                    <div>
                        <p class="mobile-section-title">{{ __('Recycle bin') }}</p>
                        <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('Trash') }}</h1>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">
                            {{ __('Soft-deleted projects, units, buildings, CRM records, and users. Restore them or permanently delete them.') }}
                        </p>
                        <p class="mt-2 text-xs text-rose-300/80">
                            {{ __('Permanently deleting an item cannot be undone.') }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('dashboard.projects.index') }}" class="app-button--ghost">{{ __('Back to Projects') }}</a>
                        <a href="{{ route('dashboard.home') }}" class="app-button--ghost">{{ __('Dashboard') }}</a>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2 sm:grid-cols-6 lg:grid-cols-3 xl:grid-cols-2">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-3 shadow-lg shadow-black/10 backdrop-blur-xl">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Total') }}</p>
                        <p class="mt-1 text-2xl font-bold text-white">{{ number_format($totalCount) }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-3 shadow-lg shadow-black/10 backdrop-blur-xl">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Projects') }}</p>
                        <p class="mt-1 text-2xl font-bold text-rose-400">{{ number_format($projectsCount) }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-3 shadow-lg shadow-black/10 backdrop-blur-xl">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Units') }}</p>
                        <p class="mt-1 text-2xl font-bold text-amber-400">{{ number_format($unitsCount) }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-3 shadow-lg shadow-black/10 backdrop-blur-xl">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Buildings') }}</p>
                        <p class="mt-1 text-2xl font-bold text-white">{{ number_format($buildingsCount) }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-3 shadow-lg shadow-black/10 backdrop-blur-xl">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Customers') }}</p>
                        <p class="mt-1 text-2xl font-bold text-rose-400">{{ number_format($customersCount) }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-3 shadow-lg shadow-black/10 backdrop-blur-xl">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Leads') }}</p>
                        <p class="mt-1 text-2xl font-bold text-amber-400">{{ number_format($leadsCount) }}</p>
                    </div>
                </div>
            </div>
        </section>

        <div x-data="{ tab: 'projects' }" class="space-y-4">
            {{-- Tabs --}}
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" @click="tab = 'projects'" class="app-button--ghost px-4 py-2 text-sm" :class="tab === 'projects' ? '!bg-brand-600 !text-white' : ''">
                    {{ __('Projects') }} ({{ $projectsCount }})
                </button>
                <button type="button" @click="tab = 'units'" class="app-button--ghost px-4 py-2 text-sm" :class="tab === 'units' ? '!bg-brand-600 !text-white' : ''">
                    {{ __('Units') }} ({{ $unitsCount }})
                </button>
                <button type="button" @click="tab = 'buildings'" class="app-button--ghost px-4 py-2 text-sm" :class="tab === 'buildings' ? '!bg-brand-600 !text-white' : ''">
                    {{ __('Buildings') }} ({{ $buildingsCount }})
                </button>
                <button type="button" @click="tab = 'customers'" class="app-button--ghost px-4 py-2 text-sm" :class="tab === 'customers' ? '!bg-brand-600 !text-white' : ''">
                    {{ __('Customers') }} ({{ $customersCount }})
                </button>
                <button type="button" @click="tab = 'leads'" class="app-button--ghost px-4 py-2 text-sm" :class="tab === 'leads' ? '!bg-brand-600 !text-white' : ''">
                    {{ __('Leads') }} ({{ $leadsCount }})
                </button>
                <button type="button" @click="tab = 'offers'" class="app-button--ghost px-4 py-2 text-sm" :class="tab === 'offers' ? '!bg-brand-600 !text-white' : ''">
                    {{ __('Offers') }} ({{ $offersCount }})
                </button>
                <button type="button" @click="tab = 'plans'" class="app-button--ghost px-4 py-2 text-sm" :class="tab === 'plans' ? '!bg-brand-600 !text-white' : ''">
                    {{ __('Plans') }} ({{ $plansCount }})
                </button>
                <button type="button" @click="tab = 'organizations'" class="app-button--ghost px-4 py-2 text-sm" :class="tab === 'organizations' ? '!bg-brand-600 !text-white' : ''">
                    {{ __('Organizations') }} ({{ $organizationsCount }})
                </button>
                <button type="button" @click="tab = 'deals'" class="app-button--ghost px-4 py-2 text-sm" :class="tab === 'deals' ? '!bg-brand-600 !text-white' : ''">
                    {{ __('Deals') }} ({{ $dealsCount }})
                </button>
                <button type="button" @click="tab = 'contacts'" class="app-button--ghost px-4 py-2 text-sm" :class="tab === 'contacts' ? '!bg-brand-600 !text-white' : ''">
                    {{ __('Contacts') }} ({{ $contactsCount }})
                </button>
                <button type="button" @click="tab = 'documents'" class="app-button--ghost px-4 py-2 text-sm" :class="tab === 'documents' ? '!bg-brand-600 !text-white' : ''">
                    {{ __('Documents') }} ({{ $documentsCount }})
                </button>
                <button type="button" @click="tab = 'conversations'" class="app-button--ghost px-4 py-2 text-sm" :class="tab === 'conversations' ? '!bg-brand-600 !text-white' : ''">
                    {{ __('Conversations') }} ({{ $conversationsCount }})
                </button>
                <button type="button" @click="tab = 'users'" class="app-button--ghost px-4 py-2 text-sm" :class="tab === 'users' ? '!bg-brand-600 !text-white' : ''">
                    {{ __('Users') }} ({{ $usersCount }})
                </button>
            </div>

            {{-- Projects tab --}}
            <section x-show="tab === 'projects'" x-cloak class="app-card app-card--gradient space-y-3 p-5 sm:p-6">
                @forelse ($trashedProjects as $project)
                    @php
                        $coverImg = $project->cover_image_path
                            ?: collect($project->images ?? [])->filter(fn ($img) => is_string($img))->first();
                    @endphp
                    <div class="flex flex-col gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-center gap-3">
                            @if ($coverImg)
                                <img src="{{ asset('storage/'.$coverImg) }}" alt="" class="h-12 w-16 shrink-0 rounded-lg border border-white/10 object-cover">
                            @else
                                <div class="flex h-12 w-16 shrink-0 items-center justify-center rounded-lg border border-dashed border-white/10 bg-white/5">
                                    <svg class="h-5 w-5 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2" stroke-width="1.8"/><circle cx="9" cy="10" r="1.5" stroke-width="1.8"/><path d="M3 16l4.5-4.5 4 4 3.5-3.5 6 6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-white">{{ $project->name }}</p>
                                <p class="text-xs text-slate-400">
                                    {{ __('Deleted :date', ['date' => $project->deleted_at?->diffForHumans() ?? '—']) }}
                                    @if ($project->units_count > 0) · {{ $project->units_count }} {{ __('units') }} @endif
                                    @if ($project->buildings_count > 0) · {{ $project->buildings_count }} {{ __('buildings') }} @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex shrink-0 flex-wrap items-center gap-2">
                            <form method="POST" action="{{ route('dashboard.trash.projects.restore', $project) }}">
                                @csrf
                                <button type="submit" class="app-button--ghost px-4 py-2 text-xs text-emerald-300 hover:text-emerald-200">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 3v5h5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    {{ __('Restore') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('dashboard.trash.projects.force-delete', $project) }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmAction('{{ __('Delete forever') }}', '{{ __('Permanently delete :name? This cannot be undone and will also remove its buildings and floors.', ['name' => $project->name]) }}', () => this.closest('form').submit(), '{{ __('Delete forever') }}')" class="app-button app-button--danger px-4 py-2 text-xs">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                                    {{ __('Delete forever') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/5 py-14 text-center">
                        <svg class="h-10 w-10 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                        <p class="mt-3 font-semibold text-white">{{ __('No deleted projects') }}</p>
                        <p class="mt-1 text-sm text-slate-400">{{ __('The trash is empty.') }}</p>
                    </div>
                @endforelse
            </section>

            {{-- Units tab --}}
            <section x-show="tab === 'units'" x-cloak class="app-card app-card--gradient space-y-3 p-5 sm:p-6">
                @forelse ($trashedUnits as $unit)
                    @php
                        $unitImg = (is_string($unit->thumbnail) && $unit->thumbnail !== '')
                            ? $unit->thumbnail
                            : collect($unit->images ?? [])->filter(fn ($img) => is_string($img))->first();
                    @endphp
                    <div class="flex flex-col gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-center gap-3">
                            @if ($unitImg)
                                <img src="{{ asset('storage/'.$unitImg) }}" alt="" class="h-12 w-16 shrink-0 rounded-lg border border-white/10 object-cover">
                            @else
                                <div class="flex h-12 w-16 shrink-0 items-center justify-center rounded-lg border border-dashed border-white/10 bg-white/5">
                                    <svg class="h-5 w-5 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2" stroke-width="1.8"/><circle cx="9" cy="10" r="1.5" stroke-width="1.8"/><path d="M3 16l4.5-4.5 4 4 3.5-3.5 6 6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-white">#{{ $unit->unit_number }} <span class="text-xs font-normal text-slate-400">{{ $unit->unit_type }}</span></p>
                                <p class="text-xs text-slate-400">
                                    {{ $unit->project?->name ?? __('Unknown project') }}
                                    @if ($unit->building?->name) · {{ $unit->building->name }} @endif
                                    · {{ __('Deleted :date', ['date' => $unit->deleted_at?->diffForHumans() ?? '—']) }}
                                </p>
                            </div>
                        </div>
                        <div class="flex shrink-0 flex-wrap items-center gap-2">
                            <form method="POST" action="{{ route('dashboard.trash.units.restore', $unit) }}">
                                @csrf
                                <button type="submit" class="app-button--ghost px-4 py-2 text-xs text-emerald-300 hover:text-emerald-200">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 3v5h5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    {{ __('Restore') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('dashboard.trash.units.force-delete', $unit) }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmAction('{{ __('Delete forever') }}', '{{ __('Permanently delete unit :num? This cannot be undone.', ['num' => $unit->unit_number]) }}', () => this.closest('form').submit(), '{{ __('Delete forever') }}')" class="app-button app-button--danger px-4 py-2 text-xs">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                                    {{ __('Delete forever') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/5 py-14 text-center">
                        <svg class="h-10 w-10 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                        <p class="mt-3 font-semibold text-white">{{ __('No deleted units') }}</p>
                        <p class="mt-1 text-sm text-slate-400">{{ __('The trash is empty.') }}</p>
                    </div>
                @endforelse
            </section>

            {{-- Buildings tab --}}
            <section x-show="tab === 'buildings'" x-cloak class="app-card app-card--gradient space-y-3 p-5 sm:p-6">
                @forelse ($trashedBuildings as $building)
                    <div class="flex flex-col gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-12 w-16 shrink-0 items-center justify-center rounded-lg border border-white/10 bg-white/5">
                                <svg class="h-5 w-5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M4 21V7l8-4 8 4v14M9 21v-4h6v4M8 7h.01M12 7h.01M16 7h.01M8 11h.01M12 11h.01M16 11h.01" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-white">{{ $building->name }} <span class="text-xs font-normal text-slate-400">{{ $building->code }}</span></p>
                                <p class="text-xs text-slate-400">
                                    {{ $building->project?->name ?? __('Unknown project') }}
                                    · {{ __('Deleted :date', ['date' => $building->deleted_at?->diffForHumans() ?? '—']) }}
                                </p>
                            </div>
                        </div>
                        <div class="flex shrink-0 flex-wrap items-center gap-2">
                            <form method="POST" action="{{ route('dashboard.trash.buildings.restore', $building) }}">
                                @csrf
                                <button type="submit" class="app-button--ghost px-4 py-2 text-xs text-emerald-300 hover:text-emerald-200">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 3v5h5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    {{ __('Restore') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('dashboard.trash.buildings.force-delete', $building) }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmAction('{{ __('Delete forever') }}', '{{ __('Permanently delete building :name? This cannot be undone and will also remove its floors.', ['name' => $building->name]) }}', () => this.closest('form').submit(), '{{ __('Delete forever') }}')" class="app-button app-button--danger px-4 py-2 text-xs">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                                    {{ __('Delete forever') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/5 py-14 text-center">
                        <svg class="h-10 w-10 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M4 21V7l8-4 8 4v14M9 21v-4h6v4M8 7h.01M12 7h.01M16 7h.01M8 11h.01M12 11h.01M16 11h.01" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <p class="mt-3 font-semibold text-white">{{ __('No deleted buildings') }}</p>
                        <p class="mt-1 text-sm text-slate-400">{{ __('The trash is empty.') }}</p>
                    </div>
                @endforelse
            </section>

            {{-- Customers tab --}}
            <section x-show="tab === 'customers'" x-cloak class="app-card app-card--gradient space-y-3 p-5 sm:p-6">
                @forelse ($trashedCustomers as $customer)
                    <div class="flex flex-col gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/5">
                                <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="9" cy="7" r="4" stroke-width="1.8"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-width="1.8"/></svg>
                            </span>
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-white">{{ $customer->name }} <span class="text-xs font-normal text-slate-400 ltr">{{ $customer->phone }}</span></p>
                                <p class="text-xs text-slate-400">
                                    @if ($customer->email) {{ $customer->email }} · @endif
                                    {{ __('Deleted :date', ['date' => $customer->deleted_at?->diffForHumans() ?? '—']) }}
                                    @if ($customer->offers_count > 0) · {{ $customer->offers_count }} {{ __('offers') }} @endif
                                    @if ($customer->leads_count > 0) · {{ $customer->leads_count }} {{ __('leads') }} @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex shrink-0 flex-wrap items-center gap-2">
                            <form method="POST" action="{{ route('dashboard.trash.restore', ['type' => 'customers', 'id' => $customer->id]) }}">
                                @csrf
                                <button type="submit" class="app-button--ghost px-4 py-2 text-xs text-emerald-300 hover:text-emerald-200">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 3v5h5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    {{ __('Restore') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('dashboard.trash.force-delete', ['type' => 'customers', 'id' => $customer->id]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmAction('{{ __('Delete forever') }}', '{{ __('Permanently delete customer :name? This cannot be undone.', ['name' => $customer->name]) }}', () => this.closest('form').submit(), '{{ __('Delete forever') }}')" class="app-button app-button--danger px-4 py-2 text-xs">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                                    {{ __('Delete forever') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/5 py-14 text-center">
                        <svg class="h-10 w-10 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                        <p class="mt-3 font-semibold text-white">{{ __('No deleted customers') }}</p>
                        <p class="mt-1 text-sm text-slate-400">{{ __('The trash is empty.') }}</p>
                    </div>
                @endforelse
            </section>

            {{-- Leads tab --}}
            <section x-show="tab === 'leads'" x-cloak class="app-card app-card--gradient space-y-3 p-5 sm:p-6">
                @forelse ($trashedLeads as $lead)
                    <div class="flex flex-col gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/5">
                                <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="8.5" cy="7" r="4" stroke-width="1.8"/><path d="M20 8v6M23 11h-6" stroke-width="1.8" stroke-linecap="round"/></svg>
                            </span>
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-white">{{ $lead->name }} <span class="text-xs font-normal text-slate-400 ltr">{{ $lead->phone }}</span></p>
                                <p class="text-xs text-slate-400">
                                    {{ $lead->customer?->name ?? __('No customer') }}
                                    @if ($lead->offers_count > 0) · {{ $lead->offers_count }} {{ __('offers') }} @endif
                                    · {{ __('Deleted :date', ['date' => $lead->deleted_at?->diffForHumans() ?? '—']) }}
                                </p>
                            </div>
                        </div>
                        <div class="flex shrink-0 flex-wrap items-center gap-2">
                            <form method="POST" action="{{ route('dashboard.trash.restore', ['type' => 'leads', 'id' => $lead->id]) }}">
                                @csrf
                                <button type="submit" class="app-button--ghost px-4 py-2 text-xs text-emerald-300 hover:text-emerald-200">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 3v5h5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    {{ __('Restore') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('dashboard.trash.force-delete', ['type' => 'leads', 'id' => $lead->id]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmAction('{{ __('Delete forever') }}', '{{ __('Permanently delete lead :name? This cannot be undone.', ['name' => $lead->name]) }}', () => this.closest('form').submit(), '{{ __('Delete forever') }}')" class="app-button app-button--danger px-4 py-2 text-xs">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                                    {{ __('Delete forever') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/5 py-14 text-center">
                        <svg class="h-10 w-10 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                        <p class="mt-3 font-semibold text-white">{{ __('No deleted leads') }}</p>
                        <p class="mt-1 text-sm text-slate-400">{{ __('The trash is empty.') }}</p>
                    </div>
                @endforelse
            </section>

            {{-- Offers tab --}}
            <section x-show="tab === 'offers'" x-cloak class="app-card app-card--gradient space-y-3 p-5 sm:p-6">
                @forelse ($trashedOffers as $offer)
                    <div class="flex flex-col gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/5">
                                <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-width="1.8"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" stroke-width="1.8" stroke-linecap="round"/></svg>
                            </span>
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-white">#{{ $offer->offer_number }}</p>
                                <p class="text-xs text-slate-400">
                                    {{ $offer->customer?->name ?? $offer->lead?->name ?? __('No customer') }}
                                    @if ($offer->project?->name) · {{ $offer->project->name }} @endif
                                    @if ($offer->unit?->unit_number) · #{{ $offer->unit->unit_number }} @endif
                                    · {{ number_format((float) $offer->total_amount, 0) }} {{ __('ج.م') }}
                                    · {{ __('Deleted :date', ['date' => $offer->deleted_at?->diffForHumans() ?? '—']) }}
                                </p>
                            </div>
                        </div>
                        <div class="flex shrink-0 flex-wrap items-center gap-2">
                            <form method="POST" action="{{ route('dashboard.trash.restore', ['type' => 'offers', 'id' => $offer->id]) }}">
                                @csrf
                                <button type="submit" class="app-button--ghost px-4 py-2 text-xs text-emerald-300 hover:text-emerald-200">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 3v5h5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    {{ __('Restore') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('dashboard.trash.force-delete', ['type' => 'offers', 'id' => $offer->id]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmAction('{{ __('Delete forever') }}', '{{ __('Permanently delete offer :num? This cannot be undone.', ['num' => $offer->offer_number]) }}', () => this.closest('form').submit(), '{{ __('Delete forever') }}')" class="app-button app-button--danger px-4 py-2 text-xs">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                                    {{ __('Delete forever') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/5 py-14 text-center">
                        <svg class="h-10 w-10 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                        <p class="mt-3 font-semibold text-white">{{ __('No deleted offers') }}</p>
                        <p class="mt-1 text-sm text-slate-400">{{ __('The trash is empty.') }}</p>
                    </div>
                @endforelse
            </section>

            {{-- Plans tab --}}
            <section x-show="tab === 'plans'" x-cloak class="app-card app-card--gradient space-y-3 p-5 sm:p-6">
                @forelse ($trashedPlans as $plan)
                    <div class="flex flex-col gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/5">
                                <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M9 3h6M10 3v4m4-4v4M5 7h14v13a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 12h.01M12 12h.01M16 12h.01M8 16h.01M12 16h.01M16 16h.01" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-white">{{ $plan->customer?->name ?? __('No customer') }}</p>
                                <p class="text-xs text-slate-400">
                                    @if ($plan->unit?->unit_number) #{{ $plan->unit->unit_number }} · {{ $plan->unit->project?->name ?? '' }} · @endif
                                    {{ number_format((float) $plan->final_price, 0) }} {{ __('ج.م') }}
                                    @if ($plan->items_count > 0) · {{ $plan->items_count }} {{ __('items') }} @endif
                                    · {{ __('Deleted :date', ['date' => $plan->deleted_at?->diffForHumans() ?? '—']) }}
                                </p>
                            </div>
                        </div>
                        <div class="flex shrink-0 flex-wrap items-center gap-2">
                            <form method="POST" action="{{ route('dashboard.trash.restore', ['type' => 'plans', 'id' => $plan->id]) }}">
                                @csrf
                                <button type="submit" class="app-button--ghost px-4 py-2 text-xs text-emerald-300 hover:text-emerald-200">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 3v5h5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    {{ __('Restore') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('dashboard.trash.force-delete', ['type' => 'plans', 'id' => $plan->id]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmAction('{{ __('Delete forever') }}', '{{ __('Permanently delete this plan :customer? This cannot be undone and will also remove its installment items.', ['customer' => $plan->customer?->name ?? $plan->name ?? '']) }}', () => this.closest('form').submit(), '{{ __('Delete forever') }}')" class="app-button app-button--danger px-4 py-2 text-xs">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                                    {{ __('Delete forever') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/5 py-14 text-center">
                        <svg class="h-10 w-10 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                        <p class="mt-3 font-semibold text-white">{{ __('No deleted plans') }}</p>
                        <p class="mt-1 text-sm text-slate-400">{{ __('The trash is empty.') }}</p>
                    </div>
                @endforelse
            </section>

            {{-- Organizations tab --}}
            <section x-show="tab === 'organizations'" x-cloak class="app-card app-card--gradient space-y-3 p-5 sm:p-6">
                @forelse ($trashedOrganizations as $organization)
                    <div class="flex flex-col gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/5">
                                <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 21h18M3 7v1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7M4 21V4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v17" stroke-width="1.8"/></svg>
                            </span>
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-white">{{ $organization->name }}</p>
                                <p class="text-xs text-slate-400">
                                    @if ($organization->email) {{ $organization->email }} · @endif
                                    {{ __('Deleted :date', ['date' => $organization->deleted_at?->diffForHumans() ?? '—']) }}
                                </p>
                            </div>
                        </div>
                        <div class="flex shrink-0 flex-wrap items-center gap-2">
                            <form method="POST" action="{{ route('dashboard.trash.restore', ['type' => 'organizations', 'id' => $organization->id]) }}">
                                @csrf
                                <button type="submit" class="app-button--ghost px-4 py-2 text-xs text-emerald-300 hover:text-emerald-200">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 3v5h5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    {{ __('Restore') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('dashboard.trash.force-delete', ['type' => 'organizations', 'id' => $organization->id]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmAction('{{ __('Delete forever') }}', '{{ __('Permanently delete organization :name? This cannot be undone.', ['name' => $organization->name]) }}', () => this.closest('form').submit(), '{{ __('Delete forever') }}')" class="app-button app-button--danger px-4 py-2 text-xs">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                                    {{ __('Delete forever') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/5 py-14 text-center">
                        <svg class="h-10 w-10 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                        <p class="mt-3 font-semibold text-white">{{ __('No deleted organizations') }}</p>
                        <p class="mt-1 text-sm text-slate-400">{{ __('The trash is empty.') }}</p>
                    </div>
                @endforelse
            </section>

            {{-- Deals tab --}}
            <section x-show="tab === 'deals'" x-cloak class="app-card app-card--gradient space-y-3 p-5 sm:p-6">
                @forelse ($trashedDeals as $deal)
                    <div class="flex flex-col gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/5">
                                <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M9 4h11a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H9z" stroke-width="1.8"/><path d="M4 20a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2v16z" stroke-width="1.8"/></svg>
                            </span>
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-white">{{ $deal->title }}</p>
                                <p class="text-xs text-slate-400">
                                    {{ $deal->customer?->name ?? $deal->lead?->name ?? __('No customer') }}
                                    · {{ __('Deleted :date', ['date' => $deal->deleted_at?->diffForHumans() ?? '—']) }}
                                </p>
                            </div>
                        </div>
                        <div class="flex shrink-0 flex-wrap items-center gap-2">
                            <form method="POST" action="{{ route('dashboard.trash.restore', ['type' => 'deals', 'id' => $deal->id]) }}">
                                @csrf
                                <button type="submit" class="app-button--ghost px-4 py-2 text-xs text-emerald-300 hover:text-emerald-200">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 3v5h5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    {{ __('Restore') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('dashboard.trash.force-delete', ['type' => 'deals', 'id' => $deal->id]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmAction('{{ __('Delete forever') }}', '{{ __('Permanently delete deal :title? This cannot be undone.', ['title' => $deal->title]) }}', () => this.closest('form').submit(), '{{ __('Delete forever') }}')" class="app-button app-button--danger px-4 py-2 text-xs">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                                    {{ __('Delete forever') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/5 py-14 text-center">
                        <svg class="h-10 w-10 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                        <p class="mt-3 font-semibold text-white">{{ __('No deleted deals') }}</p>
                        <p class="mt-1 text-sm text-slate-400">{{ __('The trash is empty.') }}</p>
                    </div>
                @endforelse
            </section>

            {{-- Contacts tab --}}
            <section x-show="tab === 'contacts'" x-cloak class="app-card app-card--gradient space-y-3 p-5 sm:p-6">
                @forelse ($trashedContacts as $contact)
                    <div class="flex flex-col gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/5">
                                <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="9" cy="7" r="4" stroke-width="1.8"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-width="1.8"/></svg>
                            </span>
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-white">{{ $contact->full_name }}</p>
                                <p class="text-xs text-slate-400">
                                    {{ $contact->organization?->name ?? __('No organization') }}
                                    @if ($contact->phone ?? $contact->mobile) · <span class="ltr">{{ $contact->phone ?? $contact->mobile }}</span> @endif
                                    · {{ __('Deleted :date', ['date' => $contact->deleted_at?->diffForHumans() ?? '—']) }}
                                </p>
                            </div>
                        </div>
                        <div class="flex shrink-0 flex-wrap items-center gap-2">
                            <form method="POST" action="{{ route('dashboard.trash.restore', ['type' => 'contacts', 'id' => $contact->id]) }}">
                                @csrf
                                <button type="submit" class="app-button--ghost px-4 py-2 text-xs text-emerald-300 hover:text-emerald-200">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 3v5h5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    {{ __('Restore') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('dashboard.trash.force-delete', ['type' => 'contacts', 'id' => $contact->id]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmAction('{{ __('Delete forever') }}', '{{ __('Permanently delete contact :name? This cannot be undone.', ['name' => $contact->full_name]) }}', () => this.closest('form').submit(), '{{ __('Delete forever') }}')" class="app-button app-button--danger px-4 py-2 text-xs">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                                    {{ __('Delete forever') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/5 py-14 text-center">
                        <svg class="h-10 w-10 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                        <p class="mt-3 font-semibold text-white">{{ __('No deleted contacts') }}</p>
                        <p class="mt-1 text-sm text-slate-400">{{ __('The trash is empty.') }}</p>
                    </div>
                @endforelse
            </section>

            {{-- Documents tab --}}
            <section x-show="tab === 'documents'" x-cloak class="app-card app-card--gradient space-y-3 p-5 sm:p-6">
                @forelse ($trashedDocuments as $document)
                    <div class="flex flex-col gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/5">
                                <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-width="1.8"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" stroke-width="1.8" stroke-linecap="round"/></svg>
                            </span>
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-white">{{ $document->name }}</p>
                                <p class="text-xs text-slate-400">
                                    {{ $document->documentable?->name ?? $document->documentable?->offer_number ?? $document->documentable?->title ?? '—' }}
                                    · {{ __('Deleted :date', ['date' => $document->deleted_at?->diffForHumans() ?? '—']) }}
                                </p>
                            </div>
                        </div>
                        <div class="flex shrink-0 flex-wrap items-center gap-2">
                            <form method="POST" action="{{ route('dashboard.trash.restore', ['type' => 'documents', 'id' => $document->id]) }}">
                                @csrf
                                <button type="submit" class="app-button--ghost px-4 py-2 text-xs text-emerald-300 hover:text-emerald-200">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 3v5h5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    {{ __('Restore') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('dashboard.trash.force-delete', ['type' => 'documents', 'id' => $document->id]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmAction('{{ __('Delete forever') }}', '{{ __('Permanently delete document :name? This cannot be undone.', ['name' => $document->name]) }}', () => this.closest('form').submit(), '{{ __('Delete forever') }}')" class="app-button app-button--danger px-4 py-2 text-xs">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                                    {{ __('Delete forever') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/5 py-14 text-center">
                        <svg class="h-10 w-10 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                        <p class="mt-3 font-semibold text-white">{{ __('No deleted documents') }}</p>
                        <p class="mt-1 text-sm text-slate-400">{{ __('The trash is empty.') }}</p>
                    </div>
                @endforelse
            </section>

            {{-- Conversations tab --}}
            <section x-show="tab === 'conversations'" x-cloak class="app-card app-card--gradient space-y-3 p-5 sm:p-6">
                @forelse ($trashedConversations as $conversation)
                    <div class="flex flex-col gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/5">
                                <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" stroke-width="1.8"/></svg>
                            </span>
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-white">{{ $conversation->customer_name }}</p>
                                <p class="text-xs text-slate-400">
                                    <span class="ltr">{{ $conversation->customer_phone }}</span>
                                    · {{ __('Deleted :date', ['date' => $conversation->deleted_at?->diffForHumans() ?? '—']) }}
                                </p>
                            </div>
                        </div>
                        <div class="flex shrink-0 flex-wrap items-center gap-2">
                            <form method="POST" action="{{ route('dashboard.trash.restore', ['type' => 'conversations', 'id' => $conversation->id]) }}">
                                @csrf
                                <button type="submit" class="app-button--ghost px-4 py-2 text-xs text-emerald-300 hover:text-emerald-200">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 3v5h5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    {{ __('Restore') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('dashboard.trash.force-delete', ['type' => 'conversations', 'id' => $conversation->id]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmAction('{{ __('Delete forever') }}', '{{ __('Permanently delete conversation :name? This cannot be undone.', ['name' => $conversation->customer_name]) }}', () => this.closest('form').submit(), '{{ __('Delete forever') }}')" class="app-button app-button--danger px-4 py-2 text-xs">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                                    {{ __('Delete forever') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/5 py-14 text-center">
                        <svg class="h-10 w-10 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                        <p class="mt-3 font-semibold text-white">{{ __('No deleted conversations') }}</p>
                        <p class="mt-1 text-sm text-slate-400">{{ __('The trash is empty.') }}</p>
                    </div>
                @endforelse
            </section>

            {{-- Users tab --}}
            <section x-show="tab === 'users'" x-cloak class="app-card app-card--gradient space-y-3 p-5 sm:p-6">
                @forelse ($trashedUsers as $user)
                    <div class="flex flex-col gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/5">
                                <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="9" cy="7" r="4" stroke-width="1.8"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-width="1.8"/></svg>
                            </span>
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-white">{{ $user->name }}</p>
                                <p class="text-xs text-slate-400">
                                    <span class="ltr">{{ $user->email }}</span>
                                    · {{ __('Deleted :date', ['date' => $user->deleted_at?->diffForHumans() ?? '—']) }}
                                </p>
                            </div>
                        </div>
                        <div class="flex shrink-0 flex-wrap items-center gap-2">
                            <form method="POST" action="{{ route('dashboard.trash.restore', ['type' => 'users', 'id' => $user->id]) }}">
                                @csrf
                                <button type="submit" class="app-button--ghost px-4 py-2 text-xs text-emerald-300 hover:text-emerald-200">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 3v5h5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    {{ __('Restore') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('dashboard.trash.force-delete', ['type' => 'users', 'id' => $user->id]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmAction('{{ __('Delete forever') }}', '{{ __('Permanently delete user :name? This cannot be undone.', ['name' => $user->name]) }}', () => this.closest('form').submit(), '{{ __('Delete forever') }}')" class="app-button app-button--danger px-4 py-2 text-xs">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                                    {{ __('Delete forever') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/5 py-14 text-center">
                        <svg class="h-10 w-10 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                        <p class="mt-3 font-semibold text-white">{{ __('No deleted users') }}</p>
                        <p class="mt-1 text-sm text-slate-400">{{ __('The trash is empty.') }}</p>
                    </div>
                @endforelse
            </section>
        </div>
    </div>
@endsection
