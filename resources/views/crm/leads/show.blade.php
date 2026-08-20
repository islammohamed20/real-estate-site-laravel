@extends('layouts.dashboard')

@section('content')
<div class="space-y-6" x-data="{ tab: 'overview' }">
    @include('crm.partials.crm-nav')
    @if (session('status'))
        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="flex items-center gap-3 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12l5 5L20 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>{{ session('status') }}</span>
            <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-200">×</button>
        </div>
    @endif

    @php
        $stageKeys = ($stages ?? collect()) instanceof \Illuminate\Support\Collection
            ? array_keys($stages->toArray())
            : array_keys((array) ($stages ?? []));
        $currentStage = $lead->stage->value;
        $currentIndex = array_search($currentStage, $stageKeys);
        $progressPct = $currentIndex !== false && count($stageKeys) > 0
            ? round((($currentIndex + 1) / count($stageKeys)) * 100)
            : 0;
    @endphp

    <section class="dashboard-hero-card p-6 sm:p-8">
        <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-brand-500/20 blur-3xl"></div>
        <div class="absolute -bottom-20 left-1/3 h-56 w-56 rounded-full bg-violet-500/10 blur-3xl"></div>

        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-start">
                <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500/25 to-violet-600/25 text-2xl font-bold text-brand-200 ring-1 ring-brand-500/20">
                    {{ mb_strtoupper(mb_substr($lead->name, 0, 1)) }}
                </span>

                <div class="space-y-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="badge badge-brand">{{ __('Lead') }}</span>
                        @include('crm.partials.status-badge', ['status' => $lead->stage->value, 'label' => __($lead->stage->label())])
                        @include('crm.partials.status-badge', ['status' => $lead->priority ?? 'normal', 'label' => __(ucfirst($lead->priority ?? 'normal'))])
                        @if ($lead->leadSource)
                            <span class="inline-flex items-center gap-1 rounded-full border border-white/10 bg-slate-900/60 px-2.5 py-0.5 text-xs font-semibold text-slate-300">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" stroke-width="1.8" stroke-linecap="round"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" stroke-width="1.8"/></svg>
                                {{ $lead->leadSource->name }}
                            </span>
                        @endif
                        @if ($lead->customer)
                            <a href="{{ route('dashboard.crm.customers.show', $lead->customer) }}" class="badge badge-success">
                                {{ __('Customer') }}: {{ $lead->customer->name }}
                            </a>
                        @else
                            <span class="badge badge-warning">{{ __('Potential customer') }}</span>
                        @endif
                    </div>

                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ $lead->name }}</h1>
                        <p class="mt-2 flex flex-wrap items-center gap-3 text-sm text-slate-300">
                            <a href="tel:{{ $lead->phone }}" class="flex items-center gap-1.5 hover:text-white hover:underline">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" stroke-width="1.8"/></svg>
                                {{ $lead->phone }}
                            </a>
                            @if ($lead->email)
                                <a href="mailto:{{ $lead->email }}" class="flex items-center gap-1.5 hover:text-white hover:underline">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="2" y="4" width="20" height="16" rx="2" stroke-width="1.8"/><path d="m22 7-10 6L2 7" stroke-width="1.8" stroke-linecap="round"/></svg>
                                    {{ $lead->email }}
                                </a>
                            @endif
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('dashboard.crm.leads.edit', $lead) }}" class="app-button text-sm">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34" stroke-width="1.8" stroke-linecap="round"/><path d="M18 2l4 4-10 10-4 1 1-4z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            {{ __('Edit') }}
                        </a>
                        <a href="tel:{{ $lead->phone }}" class="app-button text-sm">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" stroke-width="1.8"/></svg>
                            {{ __('Call') }}
                        </a>
                        @if (\App\Support\WhatsApp::number($lead->whatsapp ?? $lead->phone))
                            <a href="{{ route('dashboard.whatsapp.index', ['phone' => $lead->whatsapp ?? $lead->phone, 'name' => $lead->name]) }}" class="app-button--ghost text-sm">{{ __('WhatsApp') }}</a>
                        @endif
                        @if (! $lead->customer)
                            <form method="POST" action="{{ route('dashboard.crm.leads.convert', $lead) }}" class="inline" onsubmit="return confirm('{{ __('Convert this lead to a customer?') }}')">
                                @csrf
                                <button type="submit" class="app-button--success text-sm">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 13l4 4L19 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    {{ __('Convert to Customer') }}
                                </button>
                            </form>
                        @endif
                        @can('delete', $lead)
                            <form id="delete-lead-form-{{ $lead->id }}" method="POST" action="{{ route('dashboard.crm.leads.destroy', $lead) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmAction('{{ __('Delete lead') }}', '{{ __('Are you sure you want to delete :name?', ['name' => $lead->name]) }}', () => document.getElementById('delete-lead-form-{{ $lead->id }}').submit(), '{{ __('Delete') }}')" class="app-button app-button--danger text-sm">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                                    {{ __('Delete') }}
                                </button>
                            </form>
                        @endcan
                        <a href="{{ route('dashboard.crm.leads.index') }}" class="app-button--ghost text-sm">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 12H5M12 19l-7-7 7-7" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            {{ __('Back to leads') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="w-full max-w-sm space-y-3 lg:w-80">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-400">{{ __('Pipeline progress') }}</span>
                    <span class="font-semibold text-white">{{ $currentIndex !== false ? $currentIndex + 1 : '—' }} / {{ count($stageKeys) }} · {{ $progressPct }}%</span>
                </div>
                <div class="h-2 overflow-hidden rounded-full bg-white/10">
                    <div class="h-full rounded-full bg-gradient-to-r from-brand-500 to-violet-500 transition-all duration-500" style="width: {{ $progressPct }}%"></div>
                </div>
                <div class="flex flex-wrap items-center gap-1.5">
                    @foreach ($stageKeys as $idx => $key)
                        @php($isDone = $currentIndex !== false && $idx < $currentIndex)
                        @php($isCurrent = $idx === $currentIndex)
                        <span class="flex h-7 w-7 items-center justify-center rounded-full text-[10px] font-bold transition-all duration-300 {{ $isDone ? 'bg-emerald-500/25 text-emerald-300' : ($isCurrent ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 ring-2 ring-brand-500/30' : 'bg-white/10 text-slate-500') }}" title="{{ __(ucfirst(str_replace('_', ' ', $key))) }}">
                            {{ $idx + 1 }}
                        </span>
                        @if (!$loop->last)
                            <span class="h-px w-3 flex-1 {{ $isDone ? 'bg-emerald-500/40' : 'bg-white/10' }}"></span>
                        @endif
                    @endforeach
                </div>
                <p class="text-xs text-slate-500">{{ __('Current stage') }}: <span class="font-semibold text-slate-300">{{ __($lead->stage->label()) }}</span></p>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-lg">
        <div class="flex gap-2 overflow-x-auto p-2 no-scrollbar border-b border-white/10">
            @foreach ([
                'overview' => __('Overview'),
                'interests' => __('Interests'),
                'history' => __('History'),
                'activities' => __('Activities'),
                'tasks' => __('Tasks'),
                'notes' => __('Notes'),
                'documents' => __('Documents'),
            ] as $key => $label)
                <button type="button" @click="tab='{{ $key }}'" class="whitespace-nowrap rounded-2xl px-5 py-2.5 text-sm font-semibold transition" :class="tab === '{{ $key }}' ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/20' : 'text-slate-400 hover:bg-white/5 hover:text-white'">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div class="p-4">
            <div x-show="tab === 'overview'" x-cloak class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="app-card app-card--gradient p-4">
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-500/15 text-violet-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 5l7 7-7 7" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Stage') }}</p>
                    </div>
                    <p class="mt-2 text-lg font-semibold text-white">{{ __($lead->stage->label()) }}</p>
                </div>
                <div class="app-card app-card--gradient p-4">
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-500/15 text-brand-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" stroke-width="1.8" stroke-linecap="round"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" stroke-width="1.8"/></svg>
                        </span>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Source') }}</p>
                    </div>
                    <p class="mt-2 text-lg font-semibold text-white">{{ $lead->leadSource?->name ?? $lead->source ?? '—' }}</p>
                </div>
                <div class="app-card app-card--gradient p-4">
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-500/15 text-sky-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 21h18M3 7v1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7M4 21V4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v17" stroke-width="1.8"/></svg>
                        </span>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Campaign') }}</p>
                    </div>
                    <p class="mt-2 text-lg font-semibold text-white">{{ $lead->campaign ?? '—' }}</p>
                </div>
                <div class="app-card app-card--gradient p-4">
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="9" stroke-width="1.8"/><path d="M12 7v10M9.5 9.5c0-.83 1.12-1.5 2.5-1.5s2.5.67 2.5 1.5-1.12 1.5-2.5 1.5-2.5.67-2.5 1.5 1.12 1.5 2.5 1.5 2.5.67 2.5 1.5-1.12 1.5-2.5 1.5-2.5-.67-2.5-1.5" stroke-width="1.6"/></svg>
                        </span>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Budget') }}</p>
                    </div>
                    <p class="mt-2 text-lg font-semibold text-white">{{ $lead->budget ? 'EGP ' . number_format((float) $lead->budget) : '—' }}</p>
                </div>
                <div class="app-card app-card--gradient p-4">
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-500/15 text-amber-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="9" cy="7" r="4" stroke-width="1.8"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-width="1.8"/></svg>
                        </span>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Assigned to') }}</p>
                    </div>
                    <p class="mt-2 text-lg font-semibold text-white">{{ $lead->assignedSales?->name ?? __('Unassigned') }}</p>
                </div>
                <div class="app-card app-card--gradient p-4">
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-500/15 text-rose-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="9" stroke-width="1.8"/><path d="M12 7v5l3 3" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </span>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Follow-up') }}</p>
                    </div>
                    <p class="mt-2 text-lg font-semibold text-white">
                        {{ $lead->follow_up_at?->format('Y-m-d H:i') ?? '—' }}
                        @if ($lead->follow_up_at && $lead->follow_up_at->isPast())
                            <span class="text-xs font-semibold text-rose-400">({{ __('Overdue') }})</span>
                        @endif
                    </p>
                </div>
                <div class="app-card app-card--gradient p-4">
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-500/15 text-sky-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke-width="1.8"/><path d="M9 22V12h6v10" stroke-width="1.8"/></svg>
                        </span>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Unit type') }}</p>
                    </div>
                    <p class="mt-2 text-lg font-semibold text-white">{{ $lead->unit_type ?? '—' }}</p>
                </div>
                <div class="app-card app-card--gradient p-4">
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-500/15 text-violet-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.8"/><path d="M16 2v4M8 2v4M3 10h18" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </span>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Bedrooms') }}</p>
                    </div>
                    <p class="mt-2 text-lg font-semibold text-white">{{ $lead->bedrooms ?? '—' }}</p>
                </div>
                <div class="app-card app-card--gradient p-4">
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 3v18h18" stroke-width="1.8" stroke-linecap="round"/><path d="m7 14 4-4 4 3 5-6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Required area') }}</p>
                    </div>
                    <p class="mt-2 text-lg font-semibold text-white">{{ $lead->required_area ? $lead->required_area . ' ' . __('m²') : '—' }}</p>
                </div>

                @if ($lead->tags->isNotEmpty())
                    <div class="app-card app-card--gradient p-4 sm:col-span-2 lg:col-span-3">
                        <div class="flex items-center gap-2">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-500/15 text-brand-400">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z" stroke-width="1.8"/><circle cx="7" cy="7" r="1.5" stroke-width="1.8"/></svg>
                            </span>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Tags') }}</p>
                        </div>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($lead->tags as $tag)
                                <span class="rounded-full px-3 py-1 text-xs font-semibold" style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($lead->notes)
                    <div class="app-card app-card--gradient p-4 sm:col-span-2 lg:col-span-3">
                        <div class="flex items-center gap-2">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-500/15 text-slate-400">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-width="1.8"/><polyline points="14 2 14 8 20 8" stroke-width="1.8"/></svg>
                            </span>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Notes') }}</p>
                        </div>
                        <p class="mt-2 whitespace-pre-line text-sm text-slate-300">{{ $lead->notes }}</p>
                    </div>
                @endif

                <div class="app-card app-card--gradient p-4 sm:col-span-2 lg:col-span-3">
                    <h3 class="flex items-center gap-2 text-sm font-semibold text-white">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 5l7 7-7 7" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        {{ __('Move stage') }}
                    </h3>
                    <form action="{{ route('dashboard.crm.leads.stage.update', $lead) }}" method="POST" class="mt-3 flex flex-wrap gap-2" x-data="{ stage: '{{ $lead->stage->value }}' }" @submit.prevent="if(stage !== '{{ $lead->stage->value }}') { fetch($el.action, { method: 'PATCH', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify({ stage, notes: '' }) }).then(r => r.json()).then(() => window.location.reload()) }">
                        @csrf
                        @method('PATCH')
                        <select x-model="stage" class="form-select rounded-xl text-sm">
                            @foreach ($stages as $value => $label)
                                <option value="{{ $value }}">{{ __($label) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="app-button text-sm">{{ __('Update') }}</button>
                    </form>
                </div>
            </div>

            <div x-show="tab === 'interests'" x-cloak class="space-y-3">
                @forelse ($lead->interestedUnits as $interest)
                    <div class="app-card app-card--gradient p-4">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-semibold text-white">{{ $interest->unit->project?->name ?? __('Unknown project') }} — {{ __('Unit') }} #{{ $interest->unit->unit_number }}</p>
                                <p class="text-sm text-slate-400">{{ $interest->unit->unit_type }} • {{ __(ucfirst($interest->status)) }}</p>
                            </div>
                            @include('crm.partials.status-badge', ['status' => $interest->priority, 'label' => __(ucfirst($interest->priority))])
                        </div>
                    </div>
                @empty
                    <p class="text-center text-slate-400">{{ __('No unit interests recorded.') }}</p>
                @endforelse
            </div>

            <div x-show="tab === 'history'" x-cloak class="space-y-3">
                @forelse ($lead->stageHistory as $history)
                    <div class="app-card app-card--gradient p-4">
                        <p class="text-sm font-semibold text-white">{{ __($history->stage_from?->label() ?? '—') }} → {{ __($history->stage_to?->label()) }}</p>
                        <p class="mt-1 text-xs text-slate-400">{{ $history->user?->name ?? 'System' }} • {{ $history->changed_at?->format('Y-m-d H:i') }}</p>
                        @if ($history->notes)
                            <p class="mt-2 text-sm text-slate-300">{{ $history->notes }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-center text-slate-400">{{ __('No stage history yet.') }}</p>
                @endforelse

                @forelse ($lead->assignmentHistory as $history)
                    <div class="app-card app-card--gradient p-4">
                        <p class="text-sm font-semibold text-white">{{ __('Assigned') }}: {{ $history->fromUser?->name ?? '—' }} → {{ $history->toUser?->name ?? '—' }}</p>
                        <p class="mt-1 text-xs text-slate-400">{{ $history->assignedBy?->name ?? 'System' }} • {{ $history->assigned_at?->format('Y-m-d H:i') }}</p>
                    </div>
                @empty
                @endforelse
            </div>

            <div x-show="tab === 'activities'" x-cloak class="space-y-3">
                <form action="{{ route('dashboard.crm.activities.store') }}" method="POST" class="app-card app-card--gradient p-4">
                    @csrf
                    <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <select name="type" class="form-select rounded-xl text-sm">
                            @foreach ($activityTypes as $activityType)
                                <option value="{{ $activityType }}">{{ __(ucfirst($activityType)) }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="subject" placeholder="{{ __('Subject') }}" class="form-input rounded-xl text-sm">
                        <input type="datetime-local" name="due_at" class="form-input rounded-xl text-sm">
                        <input type="text" name="duration" placeholder="{{ __('Duration (e.g. 30m)') }}" class="form-input rounded-xl text-sm">
                    </div>
                    <textarea name="body" rows="2" placeholder="{{ __('Notes / Outcome') }}" class="form-textarea mt-3 w-full rounded-xl text-sm"></textarea>
                    <div class="mt-3 flex justify-end">
                        <button type="submit" class="app-button text-sm">{{ __('Log activity') }}</button>
                    </div>
                </form>

                @forelse ($lead->activities as $activity)
                    <div class="app-card app-card--gradient p-4">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-semibold text-white">{{ $activity->subject ?? __(ucfirst($activity->type)) }}</p>
                                <p class="text-sm text-slate-400">{{ __(ucfirst($activity->type)) }} • {{ $activity->duration }} • {{ $activity->creator?->name }}</p>
                            </div>
                            @include('crm.partials.status-badge', ['status' => $activity->completed_at ? 'completed' : 'open', 'label' => $activity->completed_at ? __('Completed') : __('Open')])
                        </div>
                        <p class="mt-2 text-sm text-slate-300">{{ $activity->body }}</p>
                        @if ($activity->outcome)
                            <p class="mt-1 text-xs text-slate-400">{{ __('Outcome') }}: {{ $activity->outcome }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-center text-slate-400">{{ __('No activities logged yet.') }}</p>
                @endforelse
            </div>

            <div x-show="tab === 'tasks'" x-cloak class="space-y-3">
                @forelse ($lead->tasks as $task)
                    <div class="app-card app-card--gradient p-4">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-semibold text-white">{{ $task->title }}</p>
                                <p class="text-sm text-slate-400">{{ $task->due_at?->format('Y-m-d H:i') ?? __('No due date') }}</p>
                            </div>
                            @include('crm.partials.status-badge', ['status' => $task->status === 'completed' ? 'completed' : $task->priority, 'label' => $task->status === 'completed' ? __('Completed') : __(ucfirst($task->priority))])
                        </div>
                        @if ($task->description)
                            <p class="mt-2 text-sm text-slate-300">{{ $task->description }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-center text-slate-400">{{ __('No tasks yet.') }}</p>
                @endforelse
            </div>

            <div x-show="tab === 'notes'" x-cloak class="space-y-3">
                @forelse ($lead->recordedNotes as $note)
                    <div class="app-card app-card--gradient p-4">
                        <p class="text-sm text-slate-300">{{ $note->body }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $note->user?->name }} • {{ $note->created_at?->format('Y-m-d H:i') }}</p>
                    </div>
                @empty
                    <p class="text-center text-slate-400">{{ __('No notes yet.') }}</p>
                @endforelse
            </div>

            <div x-show="tab === 'documents'" x-cloak class="space-y-3">
                @include('crm.partials.documents-list', ['documentable' => $lead])
            </div>
        </div>
    </section>
</div>
@endsection
