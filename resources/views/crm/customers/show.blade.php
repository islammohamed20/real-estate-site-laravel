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

    <section class="dashboard-hero-card p-6 sm:p-8">
        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="badge badge-brand">{{ __('Customer 360') }}</span>
                    <span class="badge badge-success">{{ $customer->leads_count ?? $customer->leads()->count() }} {{ __('leads') }}</span>
                </div>
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ $customer->name }}</h1>
                    <p class="mt-2 flex flex-wrap items-center gap-2 text-sm text-slate-300">
                        <a href="tel:{{ $customer->phone }}" class="hover:text-white hover:underline">{{ $customer->phone }}</a>
                        @if (\App\Support\WhatsApp::number($customer->whatsapp ?? $customer->phone))
                            <span class="text-slate-500">•</span>
                            <a href="{{ route('dashboard.whatsapp.index', ['phone' => $customer->whatsapp ?? $customer->phone, 'name' => $customer->name]) }}" class="text-emerald-400 hover:underline">{{ __('WhatsApp') }}</a>
                        @endif
                        @if ($customer->email)
                            <span class="text-slate-500">•</span>
                            <a href="mailto:{{ $customer->email }}" class="hover:text-white hover:underline">{{ $customer->email }}</a>
                        @endif
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('dashboard.crm.customers.edit', $customer) }}" class="app-button text-sm">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34" stroke-width="1.8" stroke-linecap="round"/><path d="M18 2l4 4-10 10-4 1 1-4z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        {{ __('Edit') }}
                    </a>
                    <a href="tel:{{ $customer->phone }}" class="app-button text-sm">{{ __('Call') }}</a>
                    @if (\App\Support\WhatsApp::number($customer->whatsapp ?? $customer->phone))
                        <a href="{{ route('dashboard.whatsapp.index', ['phone' => $customer->whatsapp ?? $customer->phone, 'name' => $customer->name]) }}" class="app-button--ghost text-sm">{{ __('WhatsApp') }}</a>
                    @endif
                    <a href="{{ route('dashboard.crm.index') }}" class="app-button--ghost text-sm">{{ __('CRM Home') }}</a>
                    @can('delete', $customer)
                        <form id="delete-customer-form-{{ $customer->id }}" method="POST" action="{{ route('dashboard.crm.customers.destroy', $customer) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmAction('{{ __('Delete customer') }}', '{{ __('Are you sure you want to delete :name?', ['name' => $customer->name]) }}', () => document.getElementById('delete-customer-form-{{ $customer->id }}').submit(), '{{ __('Delete') }}')" class="app-button app-button--danger text-sm">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                                {{ __('Delete') }}
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-lg">
        <div class="flex gap-2 overflow-x-auto p-2 no-scrollbar border-b border-white/10">
            @foreach ([
                'overview' => __('Overview'),
                'leads' => __('Leads'),
                'offers' => __('Offers'),
                'deals' => __('Deals'),
                'reservations' => __('Reservations'),
                'plans' => __('Payment plans'),
                'activities' => __('Activities'),
                'timeline' => __('Timeline'),
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
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Budget range') }}</p>
                    <p class="mt-1 text-lg font-semibold text-white">
                        @if ($customer->budget_min || $customer->budget_max)
                            {{ 'EGP ' . number_format((float) ($customer->budget_min ?? 0)) }} — {{ 'EGP ' . number_format((float) ($customer->budget_max ?? $customer->budget ?? 0)) }}
                        @else
                            —
                        @endif
                    </p>
                </div>
                <div class="app-card app-card--gradient p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Occupation') }}</p>
                    <p class="mt-1 text-lg font-semibold text-white">{{ $customer->occupation ?? '—' }}</p>
                </div>
                <div class="app-card app-card--gradient p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Source') }}</p>
                    <p class="mt-1 text-lg font-semibold text-white">{{ $customer->source ?? '—' }}</p>
                </div>
                <div class="app-card app-card--gradient p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Address') }}</p>
                    <p class="mt-1 text-lg font-semibold text-white">{{ $customer->address ?? '—' }}</p>
                </div>
                <div class="app-card app-card--gradient p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Total leads') }}</p>
                    <p class="mt-1 text-lg font-semibold text-white">{{ $customer->leads->count() }}</p>
                </div>

                @if ($customer->interestedProjects->isNotEmpty())
                    <div class="app-card app-card--gradient p-4 sm:col-span-2 lg:col-span-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Interested projects') }}</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($customer->interestedProjects as $project)
                                <a href="{{ route('dashboard.projects.edit', $project) }}" class="flex items-center gap-1.5 rounded-full border border-brand-500/30 bg-brand-500/10 px-3 py-1 text-xs font-semibold text-brand-300 transition hover:bg-brand-500/20">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 21h18M5 21V7l7-4 7 4v14" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    {{ $project->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="app-card app-card--gradient p-4 sm:col-span-2 lg:col-span-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Notes') }}</p>
                    <p class="mt-2 whitespace-pre-line text-sm text-slate-300">{{ $customer->notes ?? '—' }}</p>
                </div>
            </div>

            <div x-show="tab === 'leads'" x-cloak class="space-y-3">
                @forelse ($customer->leads as $lead)
                    <a href="{{ route('dashboard.crm.leads.show', $lead) }}" class="app-card app-card--gradient p-4 block transition hover:border-brand-500/30">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-semibold text-white">{{ $lead->name }} <span class="text-slate-400">• {{ $lead->phone }}</span></p>
                                <p class="text-sm text-slate-400">{{ __($lead->stage->label()) }} • {{ __(ucfirst($lead->priority)) }}</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold" style="background-color: {{ $lead->leadSource?->color ?? '#64748b' }}20; color: {{ $lead->leadSource?->color ?? '#64748b' }}">{{ $lead->assignedSales?->name ?? __('Unassigned') }}</span>
                        </div>
                    </a>
                @empty
                    <p class="text-center text-slate-400">{{ __('No leads yet.') }}</p>
                @endforelse
            </div>

            <div x-show="tab === 'offers'" x-cloak class="space-y-3">
                @forelse ($customer->offers as $offer)
                    <div class="app-card app-card--gradient p-4">
                        <p class="font-semibold text-white">{{ $offer->offer_number }} <span class="text-slate-400">• {{ 'EGP ' . number_format((float) $offer->total_amount) }}</span></p>
                        <p class="text-sm text-slate-400">{{ __(ucfirst($offer->status)) }} • {{ $offer->issue_date?->format('Y-m-d') }}</p>
                    </div>
                @empty
                    <p class="text-center text-slate-400">{{ __('No offers yet.') }}</p>
                @endforelse
            </div>

            <div x-show="tab === 'deals'" x-cloak class="space-y-3">
                @forelse ($customer->deals as $deal)
                    <a href="{{ route('dashboard.crm.deals.show', $deal) }}" class="app-card app-card--gradient p-4 block transition hover:border-brand-500/30">
                        <p class="font-semibold text-white">{{ $deal->title }} <span class="text-slate-400">• {{ 'EGP ' . number_format((float) $deal->value) }}</span></p>
                        <p class="text-sm text-slate-400">{{ __(ucfirst($deal->status)) }} • {{ $deal->stage?->name }}</p>
                    </a>
                @empty
                    <p class="text-center text-slate-400">{{ __('No deals yet.') }}</p>
                @endforelse
            </div>

            <div x-show="tab === 'plans'" x-cloak class="space-y-3">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm text-slate-400">{{ __('Saved installment plans for this customer.') }}</p>
                    <a href="{{ route('dashboard.installments.index') }}" class="app-button text-sm">{{ __('Open calculator') }}</a>
                </div>
                @forelse ($customer->plans as $plan)
                    <a href="{{ route('dashboard.crm.plans.show', $plan) }}" class="app-card app-card--gradient p-4 block transition hover:border-brand-500/30">
                        <p class="font-semibold text-white">{{ $plan->name ?? ('#'.$plan->id) }} <span class="text-slate-400">• {{ 'EGP ' . number_format((float) $plan->final_price) }}</span></p>
                        <p class="text-sm text-slate-400">{{ $plan->installment_count }} {{ __('installments') }} • {{ $plan->unit?->unit_number ?: ($plan->project?->name ?? '—') }}</p>
                    </a>
                @empty
                    <p class="text-center text-slate-400">{{ __('No payment plans yet — create one from the calculator and save it to this customer.') }}</p>
                @endforelse
            </div>

            <div x-show="tab === 'reservations'" x-cloak class="space-y-3">
                @forelse ($customer->reservations as $reservation)
                    <div class="app-card app-card--gradient p-4">
                        <p class="font-semibold text-white">{{ $reservation->reservation_number }} <span class="text-slate-400">• {{ 'EGP ' . number_format((float) $reservation->deposit_amount) }}</span></p>
                        <p class="text-sm text-slate-400">{{ __(ucfirst($reservation->status)) }} • {{ $reservation->reserved_at?->format('Y-m-d') }}</p>
                    </div>
                @empty
                    <p class="text-center text-slate-400">{{ __('No reservations yet.') }}</p>
                @endforelse
            </div>

            <div x-show="tab === 'activities'" x-cloak class="space-y-3">
                <form action="{{ route('dashboard.crm.activities.store') }}" method="POST" class="app-card app-card--gradient p-4">
                    @csrf
                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">
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

                @forelse ($customer->activities as $activity)
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

            <div x-show="tab === 'timeline'" x-cloak class="space-y-4">
                <div class="relative border-l border-white/10 pl-6">
                    @forelse ($timeline as $event)
                        <div class="relative mb-6 last:mb-0">
                            <span class="absolute -left-[31px] top-1 flex h-5 w-5 items-center justify-center rounded-full border border-white/10" style="background-color: {{ $event['color'] }}20">
                                <svg class="h-3 w-3" style="color: {{ $event['color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            <div class="app-card app-card--gradient p-4">
                                <p class="text-sm font-semibold text-white">{{ $event['title'] }}</p>
                                <p class="mt-1 text-sm text-slate-300">{{ $event['body'] }}</p>
                                <div class="mt-2 flex items-center gap-2 text-xs text-slate-500">
                                    <span>{{ $event['user'] ?? __('System') }}</span>
                                    <span>•</span>
                                    <span>{{ $event['at']?->format('Y-m-d H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-slate-400">{{ __('No timeline events yet.') }}</p>
                    @endforelse
                </div>
            </div>

            <div x-show="tab === 'documents'" x-cloak class="space-y-3">
                @include('crm.partials.documents-list', ['documentable' => $customer])
            </div>
        </div>
    </section>
</div>
@endsection
