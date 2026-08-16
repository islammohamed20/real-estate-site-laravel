@extends('layouts.dashboard')

@section('content')
    <div class="space-y-6" x-data="{ activityTab: 'all', activityForm: false }">
    @include('crm.partials.crm-nav')
        <template x-if="activityForm">
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4 backdrop-blur-sm" @click.outside="activityForm = false" @keydown.escape.window="activityForm = false">
                <div class="w-full max-w-xl rounded-3xl border border-white/10 bg-slate-900 p-6 shadow-2xl">
                    <h2 class="text-xl font-semibold text-white">{{ __('New Activity') }}</h2>
                    <form method="POST" action="{{ route('dashboard.crm.deals.activities.store', $deal) }}" class="mt-4 grid gap-3 sm:grid-cols-2">
                        @csrf

                        <div>
                            <label class="text-sm text-slate-400">{{ __('Type') }}</label>
                            <select name="type" class="app-input w-full">
                                @foreach ($activityTypes as $type)
                                    <option value="{{ $type }}">{{ __(ucfirst($type)) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-sm text-slate-400">{{ __('Contact') }}</label>
                            <select name="contact_id" class="app-input w-full">
                                <option value="">{{ __('Select contact') }}</option>
                                @foreach ($deal->contacts as $contact)
                                    <option value="{{ $contact->id }}">{{ $contact->full_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="text-sm text-slate-400">{{ __('Subject') }}</label>
                            <input name="subject" class="app-input w-full">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="text-sm text-slate-400">{{ __('Body / Notes') }}</label>
                            <textarea name="body" class="app-input min-h-24 w-full"></textarea>
                        </div>

                        <div>
                            <label class="text-sm text-slate-400">{{ __('Due at') }}</label>
                            <input type="datetime-local" name="due_at" class="app-input w-full">
                        </div>

                        <div>
                            <label class="text-sm text-slate-400">{{ __('Duration') }}</label>
                            <input name="duration" placeholder="15m" class="app-input w-full">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="text-sm text-slate-400">{{ __('Outcome') }}</label>
                            <input name="outcome" class="app-input w-full">
                        </div>

                        <div class="flex gap-2 sm:col-span-2">
                            <button type="submit" class="app-button">{{ __('Add Activity') }}</button>
                            <button type="button" @click="activityForm = false" class="app-button--ghost">{{ __('Cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        <section class="app-card app-card--gradient p-6 sm:p-8">
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-brand-500/20 blur-3xl"></div>

            <div class="relative flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('dashboard.crm.deals.index', ['pipeline' => $deal->pipeline_id]) }}" class="text-sm text-slate-400 hover:text-white">{{ __('Deals') }}</a>
                        <span class="text-slate-600">/</span>
                        <span class="badge badge-brand">{{ $deal->pipeline->name }}</span>
                        <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase" style="background-color: {{ $deal->stage->color }}20; color: {{ $deal->stage->color }}">{{ $deal->stage->name }}</span>
                    </div>

                    <h1 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">{{ $deal->title }}</h1>

                    <div class="flex flex-wrap gap-3 text-sm text-slate-300">
                        @if ($deal->organization)
                            <span class="flex items-center gap-1"><svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 21h18M3 7v1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7M4 21V4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v17" stroke-width="1.8"/></svg> {{ $deal->organization->name }}</span>
                        @endif
                        @if ($deal->primaryContact())
                            <span class="flex items-center gap-1"><svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="9" cy="7" r="4" stroke-width="1.8"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-width="1.8"/></svg> {{ $deal->primaryContact()?->full_name }}</span>
                        @endif
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    @can('manage crm')
                        <button type="button" @click="activityForm = true" class="app-button">{{ __('+ Activity') }}</button>
                    @endcan
                    <a href="{{ route('dashboard.crm.deals.index', ['pipeline' => $deal->pipeline_id]) }}" class="app-button--ghost">{{ __('Board') }}</a>
                </div>
            </div>

            <div class="relative mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Value') }}</p>
                    <p class="mt-2 text-2xl font-bold text-white">{{ number_format($deal->value, 2) }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Priority') }}</p>
                    <p class="mt-2 text-lg font-semibold text-white">{{ __(ucfirst($deal->priority)) }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Expected close') }}</p>
                    <p class="mt-2 text-lg font-semibold text-white">{{ $deal->expected_close_date?->format('Y-m-d') ?? __('—') }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Owner') }}</p>
                    <p class="mt-2 text-lg font-semibold text-white">{{ $deal->assignedUser?->name ?? __('Unassigned') }}</p>
                </div>
            </div>
        </section>

        <div class="grid gap-6 lg:grid-cols-[1fr_22rem]">
            <section class="app-card app-card--gradient space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold">{{ __('Activity Timeline') }}</h2>
                    <div class="flex gap-1 rounded-xl border border-white/10 bg-slate-900 p-1">
                        @foreach (['all' => __('All'), 'call' => __('Calls'), 'email' => __('Emails'), 'meeting' => __('Meetings'), 'task' => __('Tasks')] as $key => $label)
                            <button type="button" @click="activityTab = '{{ $key }}'" class="rounded-lg px-3 py-1 text-xs font-semibold transition" :class="activityTab === '{{ $key }}' ? 'bg-brand-600 text-white' : 'text-slate-400 hover:text-white'">{{ $label }}</button>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-4">
                    @forelse ($deal->activities as $activity)
                        <div
                            x-show="activityTab === 'all' || activityTab === '{{ $activity->type }}'"
                            x-cloak
                            class="flex gap-3 border-b border-white/10 pb-4 last:border-0 last:pb-0"
                        >
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-brand-500/15 text-brand-400">
                                {{ __(ucfirst($activity->type)) }}
                            </span>
                            <div class="flex-1 space-y-1">
                                <div class="flex items-start justify-between gap-2">
                                    <p class="text-sm font-semibold text-white">{{ $activity->subject ?? __(ucfirst($activity->type)) }}</p>
                                    <div class="flex gap-2">
                                        @if (! $activity->isCompleted() && in_array($activity->type, ['task', 'call', 'meeting', 'follow_up']))
                                            <form method="POST" action="{{ route('dashboard.crm.deals.activities.update', [$deal, $activity]) }}" class="contents">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="completed_at" value="{{ now() }}">
                                                <button type="submit" class="text-xs text-emerald-400 hover:text-emerald-300">{{ __('Complete') }}</button>
                                            </form>
                                        @endif
                                        @can('manage crm')
                                            <form method="POST" action="{{ route('dashboard.crm.deals.activities.destroy', [$deal, $activity]) }}" class="contents" onsubmit="return confirm('{{ __('Delete this activity?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="app-button app-button--danger px-2 py-1 text-xs" style="min-height:0">{{ __('Delete') }}</button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                                @if ($activity->body)
                                    <p class="text-sm text-slate-300 whitespace-pre-line">{{ $activity->body }}</p>
                                @endif
                                @if ($activity->due_at)
                                    <p class="text-xs text-slate-500">{{ __('Due:') }} {{ $activity->due_at->format('Y-m-d H:i') }} @if ($activity->isCompleted()) <span class="text-emerald-400">({{ __('Completed') }})</span> @endif</p>
                                @endif
                                <p class="text-xs text-slate-500">{{ $activity->created_at->diffForHumans() }} · {{ $activity->creator?->name }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">{{ __('No activities yet. Add a call, email, meeting, or task.') }}</p>
                    @endforelse
                </div>
            </section>

            <aside class="space-y-6">
                <section class="app-card app-card--gradient space-y-4">
                    <h2 class="text-lg font-semibold">{{ __('Deal Details') }}</h2>

                    <form method="POST" action="{{ route('dashboard.crm.deals.update', $deal) }}" class="grid gap-3">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="text-sm text-slate-400">{{ __('Title') }}</label>
                            <input name="title" value="{{ $deal->title }}" class="app-input w-full" required>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <label class="text-sm text-slate-400">{{ __('Pipeline') }}</label>
                                <select name="pipeline_id" class="app-input w-full" onchange="this.form.submit()">
                                    @foreach ($pipelines as $p)
                                        <option value="{{ $p->id }}" {{ $p->id == $deal->pipeline_id ? 'selected' : '' }}>{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="text-sm text-slate-400">{{ __('Stage') }}</label>
                                <select name="stage_id" class="app-input w-full" onchange="this.form.submit()">
                                    @foreach ($deal->pipeline->stages as $stage)
                                        <option value="{{ $stage->id }}" {{ $stage->id == $deal->stage_id ? 'selected' : '' }}>{{ $stage->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="text-sm text-slate-400">{{ __('Lead') }}</label>
                                <select name="lead_id" class="app-input w-full" onchange="this.form.submit()">
                                    <option value="">{{ __('No lead') }}</option>
                                    @foreach ($leads ?? [] as $id => $name)
                                        <option value="{{ $id }}" {{ $deal->lead_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="text-sm text-slate-400">{{ __('Customer') }}</label>
                                <select name="customer_id" class="app-input w-full" onchange="this.form.submit()">
                                    <option value="">{{ __('No customer') }}</option>
                                    @foreach ($customers ?? [] as $id => $name)
                                        <option value="{{ $id }}" {{ $deal->customer_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="text-sm text-slate-400">{{ __('Project') }}</label>
                                <select name="project_id" class="app-input w-full" onchange="this.form.submit()">
                                    <option value="">{{ __('No project') }}</option>
                                    @foreach ($projects ?? [] as $id => $name)
                                        <option value="{{ $id }}" {{ $deal->project_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="text-sm text-slate-400">{{ __('Unit') }}</label>
                                <select name="unit_id" class="app-input w-full" onchange="this.form.submit()">
                                    <option value="">{{ __('No unit') }}</option>
                                    @foreach ($units ?? [] as $id => $label)
                                        <option value="{{ $id }}" {{ $deal->unit_id == $id ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="text-sm text-slate-400">{{ __('Status') }}</label>
                                <select name="status" class="app-input w-full" onchange="this.form.submit()">
                                    @foreach (['open' => __('Open'), 'won' => __('Won'), 'lost' => __('Lost')] as $k => $label)
                                        <option value="{{ $k }}" {{ $deal->status === $k ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="text-sm text-slate-400">{{ __('Priority') }}</label>
                                <select name="priority" class="app-input w-full" onchange="this.form.submit()">
                                    @foreach (['low' => __('Low'), 'medium' => __('Medium'), 'high' => __('High')] as $k => $label)
                                        <option value="{{ $k }}" {{ $deal->priority === $k ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="text-sm text-slate-400">{{ __('Assigned to') }}</label>
                                <select name="assigned_to" class="app-input w-full" onchange="this.form.submit()">
                                    <option value="">{{ __('Unassigned') }}</option>
                                    @foreach ($users as $id => $name)
                                        <option value="{{ $id }}" {{ $deal->assigned_to == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="text-sm text-slate-400">{{ __('Value') }}</label>
                                <input type="number" step="0.01" name="value" value="{{ $deal->value }}" class="app-input w-full" onchange="this.form.submit()">
                            </div>

                            <div>
                                <label class="text-sm text-slate-400">{{ __('Currency') }}</label>
                                <select name="currency_code" class="app-input w-full" onchange="this.form.submit()">
                                    @foreach ($currencies ?? ['USD' => 'USD'] as $code => $label)
                                        <option value="{{ $code }}" {{ $deal->currency_code === $code ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="text-sm text-slate-400">{{ __('Expected close') }}</label>
                                <input type="date" name="expected_close_date" value="{{ $deal->expected_close_date?->format('Y-m-d') }}" class="app-input w-full" onchange="this.form.submit()">
                            </div>

                            <div>
                                <label class="text-sm text-slate-400">{{ __('Source') }}</label>
                                <input name="source" value="{{ $deal->source }}" class="app-input w-full" onchange="this.form.submit()">
                            </div>
                        </div>

                        <div>
                            <label class="text-sm text-slate-400">{{ __('Description') }}</label>
                            <textarea name="description" class="app-input min-h-24 w-full" onchange="this.form.submit()">{{ $deal->description }}</textarea>
                        </div>

                        <button type="submit" class="app-button hidden">{{ __('Save') }}</button>
                    </form>
                </section>

                <section class="app-card app-card--gradient space-y-3">
                    <h2 class="text-lg font-semibold">{{ __('Related') }}</h2>
                    <div class="space-y-2 text-sm">
                        @if ($deal->project)
                            <div class="flex items-center justify-between"><span class="text-slate-400">{{ __('Project') }}</span> <a href="{{ route('dashboard.projects.index') }}" class="text-white hover:text-brand-400">{{ $deal->project->name }}</a></div>
                        @endif
                        @if ($deal->lead)
                            <div class="flex items-center justify-between"><span class="text-slate-400">{{ __('Lead') }}</span> <span class="text-white">{{ $deal->lead->name }}</span></div>
                        @endif
                        @if ($deal->customer)
                            <div class="flex items-center justify-between"><span class="text-slate-400">{{ __('Customer') }}</span> <span class="text-white">{{ $deal->customer->name }}</span></div>
                        @endif
                    </div>
                </section>

                <section class="app-card app-card--gradient space-y-3">
                    <h2 class="text-lg font-semibold">{{ __('Stage History') }}</h2>
                    <div class="space-y-3">
                        @forelse ($deal->stageHistory as $history)
                            <div class="flex gap-2 text-sm">
                                <span class="text-slate-500">{{ $history->changed_at?->format('Y-m-d') }}</span>
                                <span class="text-white">{{ $history->fromStage?->name ?? __('Created') }} → {{ $history->toStage?->name }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-slate-400">{{ __('No stage changes recorded.') }}</p>
                        @endforelse
                    </div>
                </section>
            </aside>
        </div>
    </div>

@endsection
