@extends('layouts.dashboard')

@section('content')
    <div x-data="crmKanban()" x-init="init()" class="space-y-6">
    @include('crm.partials.crm-nav')
        <template x-if="open">
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4 backdrop-blur-sm" @click.outside="open = false" @keydown.escape.window="open = false">
                <div class="w-full max-w-2xl rounded-3xl border border-white/10 bg-slate-900 p-6 shadow-2xl">
                    <h2 class="text-xl font-semibold text-white">{{ __('New Deal') }}</h2>
                    <form method="POST" action="{{ route('dashboard.crm.deals.store') }}" class="mt-4 grid gap-3 sm:grid-cols-2">
                        @csrf
                        <input type="hidden" name="pipeline_id" value="{{ $pipeline?->id }}">

                        <div class="sm:col-span-2">
                            <label class="text-sm text-slate-400">{{ __('Title') }}</label>
                            <input name="title" class="app-input w-full" required>
                        </div>

                        <div>
                            <label class="text-sm text-slate-400">{{ __('Stage') }}</label>
                            <select name="stage_id" class="app-input w-full">
                                @foreach ($stages as $stage)
                                    <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-sm text-slate-400">{{ __('Lead') }}</label>
                            <select name="lead_id" class="app-input w-full">
                                <option value="">{{ __('Select lead') }}</option>
                                @foreach ($leads ?? [] as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-sm text-slate-400">{{ __('Customer') }}</label>
                            <select name="customer_id" class="app-input w-full">
                                <option value="">{{ __('Select customer') }}</option>
                                @foreach ($customers ?? [] as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-sm text-slate-400">{{ __('Organization') }}</label>
                            <select name="organization_id" class="app-input w-full">
                                <option value="">{{ __('Select organization') }}</option>
                                @foreach ($organizations ?? [] as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-sm text-slate-400">{{ __('Contact') }}</label>
                            <select name="contact_id" class="app-input w-full">
                                <option value="">{{ __('Select contact') }}</option>
                                @foreach ($contacts ?? [] as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-sm text-slate-400">{{ __('Assigned to') }}</label>
                            <select name="assigned_to" class="app-input w-full">
                                <option value="">{{ __('Unassigned') }}</option>
                                @foreach ($users as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-sm text-slate-400">{{ __('Project') }}</label>
                            <select name="project_id" class="app-input w-full">
                                <option value="">{{ __('Select project') }}</option>
                                @foreach ($projects ?? [] as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-sm text-slate-400">{{ __('Unit') }}</label>
                            <select name="unit_id" class="app-input w-full">
                                <option value="">{{ __('Select unit') }}</option>
                                @foreach ($units ?? [] as $id => $label)
                                    <option value="{{ $id }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-sm text-slate-400">{{ __('Value') }}</label>
                            <input type="number" step="0.01" name="value" class="app-input w-full">
                        </div>

                        <div>
                            <label class="text-sm text-slate-400">{{ __('Currency') }}</label>
                            <select name="currency_code" class="app-input w-full">
                                @foreach ($currencies ?? ['USD' => 'USD'] as $code => $label)
                                    <option value="{{ $code }}" {{ $code === 'USD' ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-sm text-slate-400">{{ __('Expected close') }}</label>
                            <input type="date" name="expected_close_date" class="app-input w-full">
                        </div>

                        <div>
                            <label class="text-sm text-slate-400">{{ __('Source') }}</label>
                            <input type="text" name="source" class="app-input w-full" placeholder="{{ __('e.g. referral, website') }}">
                        </div>

                        <div>
                            <label class="text-sm text-slate-400">{{ __('Priority') }}</label>
                            <select name="priority" class="app-input w-full">
                                @foreach (['low' => __('Low'), 'medium' => __('Medium'), 'high' => __('High')] as $k => $label)
                                    <option value="{{ $k }}" {{ $k === 'medium' ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="text-sm text-slate-400">{{ __('Description') }}</label>
                            <textarea name="description" class="app-input min-h-24 w-full"></textarea>
                        </div>

                        <div class="flex gap-2 sm:col-span-2">
                            <button type="submit" class="app-button">{{ __('Create Deal') }}</button>
                            <button type="button" @click="open = false" class="app-button--ghost">{{ __('Cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        <section class="dashboard-hero-card p-6 sm:p-8">
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-brand-500/20 blur-3xl"></div>
            <div class="absolute -bottom-20 left-1/3 h-56 w-56 rounded-full bg-violet-500/10 blur-3xl"></div>

            <div class="relative grid gap-6 lg:grid-cols-[minmax(0,1.4fr)_minmax(18rem,0.9fr)] lg:items-end">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="badge badge-brand">{{ __('CRM') }}</span>
                        <span class="badge badge-success">{{ __('Frappe-style deals') }}</span>
                    </div>

                    <div>
                        <p class="mobile-section-title">{{ __('Sales pipeline') }}</p>
                        <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('Deals') }}</h1>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">{{ __('Drag deals across stages, add activities, and track opportunities from first contact to close.') }}</p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @can('manage crm')
                            <button type="button" @click="open = true" class="app-button">{{ __('+ New Deal') }}</button>
                        @endcan
                        <a href="{{ route('dashboard.crm.index') }}" class="app-button--ghost">{{ __('Leads & Customers') }}</a>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="app-card app-card--gradient p-4">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Open deals') }}</p>
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-sky-500/15 text-sky-400">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 4h11a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H9z" stroke-width="1.8"/><path d="M4 20a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2v16z" stroke-width="1.8"/></svg>
                            </span>
                        </div>
                        <p class="mt-2 text-3xl font-bold text-white">{{ number_format($stats['open_deals'] ?? 0) }}</p>
                    </div>
                    <div class="app-card app-card--gradient p-4">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Pipeline value') }}</p>
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-400">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="9" stroke-width="1.8"/><path d="M12 7v10M9.5 9.5c0-.83 1.12-1.5 2.5-1.5s2.5.67 2.5 1.5-1.12 1.5-2.5 1.5-2.5.67-2.5 1.5 1.12 1.5 2.5 1.5 2.5.67 2.5 1.5-1.12 1.5-2.5 1.5-2.5-.67-2.5-1.5" stroke-width="1.6"/></svg>
                            </span>
                        </div>
                        <p class="mt-2 text-3xl font-bold text-white">{{ number_format($stats['total_value'] ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="app-card app-card--gradient space-y-4">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-wrap items-center gap-2">
                    <label class="text-sm text-slate-400">{{ __('Pipeline') }}</label>
                    <select name="pipeline" form="filters" onchange="this.form.submit()" class="app-input h-9 w-auto text-sm">
                        @foreach ($pipelines as $p)
                            <option value="{{ $p->id }}" {{ $p->id == $pipeline?->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>

                    <form id="filters" method="GET" action="{{ route('dashboard.crm.deals.index') }}" class="contents">
                        <input type="hidden" name="pipeline" value="{{ $pipeline?->id }}">
                        <select name="status" onchange="this.form.submit()" class="app-input h-9 w-auto text-sm">
                            <option value="">{{ __('All statuses') }}</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" {{ ($filters['status'] ?? '') === $status ? 'selected' : '' }}>{{ __(ucfirst($status)) }}</option>
                            @endforeach
                        </select>
                        <select name="assigned" onchange="this.form.submit()" class="app-input h-9 w-auto text-sm">
                            <option value="">{{ __('All owners') }}</option>
                            @foreach ($users as $id => $name)
                                <option value="{{ $id }}" {{ ($filters['assigned'] ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="{{ __('Search deals...') }}" class="app-input h-9 w-full text-sm lg:w-64">
                    </form>
                </div>

                <div class="text-sm text-slate-400">
                    {{ $stages->sum(fn ($s) => $dealsByStage->get($s->id, collect())->count()) }} {{ __('deals shown') }}
                </div>
            </div>

            @error('pipeline_id')
                <p class="text-sm text-rose-400">{{ $message }}</p>
            @enderror

            <div class="-mx-4 -mb-4 flex gap-4 overflow-x-auto px-4 pb-4">
                @foreach ($stages as $stage)
                    @php($stageDeals = $dealsByStage->get($stage->id, collect()))
                    <div class="w-[18rem] shrink-0 flex-col rounded-2xl border border-white/10 bg-slate-900/50 p-3" data-stage-id="{{ $stage->id }}">
                        <div class="mb-3 space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="flex min-w-0 items-center gap-2">
                                    <span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background-color: {{ $stage->color }}"></span>
                                    <h3 class="truncate text-sm font-semibold text-white">{{ $stage->name }}</h3>
                                    <span class="rounded-full bg-white/10 px-2 py-0.5 text-xs text-slate-300">{{ $stageDeals->count() }}</span>
                                </div>
                                <span class="shrink-0 text-xs text-slate-500">{{ $stage->probability }}%</span>
                            </div>
                            @if ($stageDeals->isNotEmpty())
                                <div class="flex items-center justify-between text-[10px] text-slate-500">
                                    <span>{{ __('Stage value') }}</span>
                                    <span class="font-semibold text-emerald-400/80">{{ number_format($stageDeals->sum('value'), 0) }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="stage-column min-h-[10rem] space-y-3" data-stage-id="{{ $stage->id }}" x-ref="stage-{{ $stage->id }}">
                            @foreach ($stageDeals as $deal)
                                <a href="{{ route('dashboard.crm.deals.show', $deal) }}" class="deal-card block cursor-grab rounded-xl border border-white/10 bg-slate-800/60 p-3 transition hover:border-brand-500/40 hover:bg-slate-800" draggable="true" data-deal-id="{{ $deal->id }}" data-current-status="{{ $deal->status }}">
                                    <div class="flex items-start justify-between gap-2">
                                        <h4 class="text-sm font-semibold text-white line-clamp-2">{{ $deal->title }}</h4>
                                        <div class="flex shrink-0 flex-col items-end gap-1">
                                            @if ($deal->status !== 'open')
                                                @include('crm.partials.status-badge', ['status' => $deal->status, 'label' => __(ucfirst($deal->status))])
                                            @endif
                                            @include('crm.partials.status-badge', ['status' => $deal->priority, 'label' => __(ucfirst($deal->priority))])
                                        </div>
                                    </div>

                                    @if ($deal->organization)
                                        <p class="mt-1.5 text-xs text-slate-400">{{ $deal->organization->name }}</p>
                                    @endif

                                    <div class="mt-3 flex items-center justify-between gap-2">
                                        <p class="text-sm font-bold text-emerald-400">{{ number_format($deal->value, 2) }}</p>
                                        @if ($deal->assignedUser)
                                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-brand-600 text-[10px] font-bold text-white" title="{{ $deal->assignedUser->name }}">
                                                {{ mb_strtoupper(mb_substr($deal->assignedUser->name, 0, 1)) }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] text-slate-500">
                                        @if ($deal->expected_close_date)
                                            <span class="inline-flex items-center gap-1">
                                                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="9" stroke-width="1.8"/><path d="M12 7v5l3 3" stroke-width="1.8" stroke-linecap="round"/></svg>
                                                {{ $deal->expected_close_date->format('M d') }}
                                            </span>
                                        @endif
                                        @if ($deal->customer)
                                            <span class="inline-flex min-w-0 items-center gap-1">
                                                <svg class="h-3 w-3 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="9" cy="7" r="4" stroke-width="1.8"/></svg>
                                                <span class="truncate">{{ $deal->customer->name }}</span>
                                            </span>
                                        @endif
                                    </div>

                                    @if ($deal->activities->isNotEmpty())
                                        <div class="mt-2 flex flex-wrap gap-1">
                                            @foreach ($deal->activities->take(2) as $activity)
                                                <span class="inline-flex items-center gap-1 rounded-md bg-white/5 px-1.5 py-0.5 text-[10px] text-slate-400">
                                                    {{ __(ucfirst($activity->type)) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    @push('scripts')
        <script>
            function crmKanban() {
                return {
                    open: false,
                    dragSrc: null,

                    init() {
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                        document.querySelectorAll('.deal-card').forEach(card => {
                            card.addEventListener('dragstart', (e) => {
                                this.dragSrc = card;
                                card.classList.add('opacity-50');
                                e.dataTransfer.effectAllowed = 'move';
                            });

                            card.addEventListener('dragend', () => {
                                card.classList.remove('opacity-50');
                                this.dragSrc = null;
                            });
                        });

                        document.querySelectorAll('.stage-column').forEach(col => {
                            col.addEventListener('dragover', (e) => {
                                e.preventDefault();
                                e.dataTransfer.dropEffect = 'move';
                            });

                            col.addEventListener('drop', async (e) => {
                                e.preventDefault();
                                if (! this.dragSrc) return;

                                const dealId = this.dragSrc.getAttribute('data-deal-id');
                                const stageId = col.getAttribute('data-stage-id');
                                const url = `{{ route('dashboard.crm.deals.index') }}/${dealId}/stage`;

                                try {
                                    const response = await fetch(url, {
                                        method: 'PATCH',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': token,
                                            'Accept': 'application/json',
                                        },
                                        body: JSON.stringify({ stage_id: stageId }),
                                    });

                                    if (response.ok) {
                                        col.appendChild(this.dragSrc);
                                        window.location.reload();
                                    } else {
                                        const data = await response.json();
                                        alert(data.message || '{{ __('Could not move deal') }}');
                                    }
                                } catch (err) {
                                    console.error(err);
                                }
                            });
                        });
                    },

                    openDealModal() {
                        this.open = true;
                    },
                };
            }
        </script>
    @endpush
@endsection
