@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    @include('crm.partials.crm-nav')

    @if (session('status'))
        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="flex items-center gap-3 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12l5 5L20 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>{{ session('status') }}</span>
            <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-200">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 6L6 18M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    @endif

    {{-- Hero Section --}}
    <section class="dashboard-hero-card p-6 sm:p-8">
        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="badge badge-brand">{{ __('Installment Plans') }}</span>
                    <span class="badge badge-success">{{ __('Plan #:id', ['id' => $plan->id]) }}</span>
                    <span class="badge badge-muted">{{ $plan->status }}</span>
                </div>
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ $plan->customer?->name ?? __('No customer') }}</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">
                        {{ $plan->unit?->unit_number ? $plan->unit->unit_number.' · '.($plan->unit->project?->name ?? '') : __('Custom plan') }}
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('dashboard.crm.plans.index') }}" class="app-button--ghost">{{ __('All plans') }}</a>
                <a href="{{ route('dashboard.crm.plans.pdf', $plan) }}" class="app-button">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-width="1.8"/><path d="M14 2v6h6" stroke-width="1.8"/><path d="M12 18v-6M9 15l3 3 3-3" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    {{ __('Download PDF') }}
                </a>
                @if($items->isNotEmpty() && $items->where('paid_amount', '<', $items->sum('amount'))->count() > 0)
                <form action="{{ route('dashboard.crm.plans.full-pay-all', $plan) }}" method="POST" onsubmit="return confirm('{{ __('Pay all remaining installments?') }}')">
                    @csrf
                    <button type="submit" class="app-button bg-emerald-600 hover:bg-emerald-500 text-white text-xs">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        {{ __('Pay All Remaining') }}
                    </button>
                </form>
                @endif
                <form action="{{ route('dashboard.crm.plans.destroy', $plan) }}" method="POST" onsubmit="return confirm('{{ __('Delete this plan?') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="app-button app-button--danger text-xs">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        {{ __('Delete') }}
                    </button>
                </form>
            </div>
        </div>
    </section>

    {{-- Progress Bar --}}
    @php
        $totalAmount = (float) $plan->final_price;
        $totalPaid = (float) $items->sum('paid_amount');
        $progressPercent = $totalAmount > 0 ? min(100, round(($totalPaid / $totalAmount) * 100, 1)) : 0;
        $isComplete = $progressPercent >= 100;
    @endphp
    <section class="app-card p-4 sm:p-6">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg {{ $isComplete ? 'bg-emerald-500/20 text-emerald-400' : 'bg-brand-500/20 text-brand-400' }}">
                    @if($isComplete)
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" stroke-width="2"/><path d="M22 4 12 14.01l-3-3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    @else
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    @endif
                </div>
                <span class="text-sm font-semibold text-white">{{ __('Payment Progress') }}</span>
            </div>
            <span class="text-xs font-bold {{ $isComplete ? 'text-emerald-400' : 'text-brand-400' }}">{{ $progressPercent }}%</span>
        </div>
        <div class="h-2.5 w-full overflow-hidden rounded-full bg-slate-800">
            <div class="h-full rounded-full transition-all duration-700 {{ $isComplete ? 'bg-emerald-500' : 'bg-brand-500' }}" style="width: {{ $progressPercent }}%"></div>
        </div>
        <div class="mt-2 flex items-center justify-between text-xs text-slate-400">
            <span>{{ __('Paid') }}: <span class="font-semibold text-emerald-300">{{ number_format($totalPaid, 0) }} {{ __('EGP') }}</span></span>
            <span>{{ __('Total') }}: <span class="font-semibold text-white">{{ number_format($totalAmount, 0) }} {{ __('EGP') }}</span></span>
        </div>
    </section>

    {{-- Plan Details --}}
    <section class="app-card app-card--gradient p-6 sm:p-8">
        <h2 class="mb-4 text-lg font-semibold text-white">{{ __('Plan details') }}</h2>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
            <div class="rounded-xl border border-white/10 bg-slate-950/40 p-3">
                <p class="text-xs text-slate-500">{{ __('Customer') }}</p>
                <p class="mt-1 font-semibold text-white">{{ $plan->customer?->name ?? '—' }}</p>
            </div>
            <div class="rounded-xl border border-white/10 bg-slate-950/40 p-3">
                <p class="text-xs text-slate-500">{{ __('Phone') }}</p>
                <p class="mt-1 font-semibold text-white ltr">{{ $plan->customer?->phone ?? '—' }}</p>
            </div>
            <div class="rounded-xl border border-white/10 bg-slate-950/40 p-3">
                <p class="text-xs text-slate-500">{{ __('Project') }}</p>
                <p class="mt-1 font-semibold text-white">{{ $plan->unit?->project?->name ?? $plan->project?->name ?? '—' }}</p>
            </div>
            <div class="rounded-xl border border-white/10 bg-slate-950/40 p-3">
                <p class="text-xs text-slate-500">{{ __('Unit') }}</p>
                <p class="mt-1 font-semibold text-white">{{ $plan->unit?->unit_number ?? '—' }}</p>
            </div>
            <div class="rounded-xl border border-white/10 bg-slate-950/40 p-3">
                <p class="text-xs text-slate-500">{{ __('Unit Type') }}</p>
                <p class="mt-1 font-semibold text-white">{{ $plan->unit?->unit_type ? __($plan->unit->unit_type) : '—' }}</p>
            </div>
            <div class="rounded-xl border border-white/10 bg-slate-950/40 p-3">
                <p class="text-xs text-slate-500">{{ __('Building') }}</p>
                <p class="mt-1 font-semibold text-white">{{ $plan->unit?->building?->name ?? '—' }}</p>
            </div>
            <div class="rounded-xl border border-white/10 bg-slate-950/40 p-3">
                <p class="text-xs text-slate-500">{{ __('Floor') }}</p>
                <p class="mt-1 font-semibold text-white">{{ $plan->unit?->floor ? (($plan->unit->floor->number === 0 || $plan->unit->floor->number === null) ? __('Ground Floor') : __('Floor :number', ['number' => $plan->unit->floor->number])) : '—' }}</p>
            </div>
            <div class="rounded-xl border border-white/10 bg-slate-950/40 p-3">
                <p class="text-xs text-slate-500">{{ __('Offer') }}</p>
                <p class="mt-1 font-semibold text-white">{{ $plan->offer?->offer_number ?? '—' }}</p>
            </div>
            <div class="rounded-xl border border-white/10 bg-slate-950/40 p-3">
                <p class="text-xs text-slate-500">{{ __('Lead') }}</p>
                <p class="mt-1 font-semibold text-white">{{ $plan->lead?->name ?? '—' }}</p>
            </div>
            <div class="rounded-xl border border-white/10 bg-slate-950/40 p-3">
                <p class="text-xs text-slate-500">{{ __('Saved by') }}</p>
                <p class="mt-1 font-semibold text-white">{{ $plan->creator?->name ?? '—' }}</p>
            </div>
            <div class="rounded-xl border border-white/10 bg-slate-950/40 p-3">
                <p class="text-xs text-slate-500">{{ __('Saved') }}</p>
                <p class="mt-1 font-semibold text-white">{{ $plan->created_at?->format('Y-m-d H:i') }}</p>
            </div>
            <div class="rounded-xl border border-white/10 bg-slate-950/40 p-3">
                <p class="text-xs text-slate-500">{{ __('First installment') }}</p>
                <p class="mt-1 font-semibold text-white">{{ $plan->starts_at?->format('Y-m-d') ?? '—' }}</p>
            </div>
        </div>
    </section>

    {{-- Financial Summary --}}
    <section class="app-card app-card--gradient p-6 sm:p-8">
        <h2 class="mb-4 text-lg font-semibold text-white">{{ __('Financial summary') }}</h2>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
            <div class="rounded-xl border border-white/10 bg-slate-950/40 p-3">
                <p class="text-xs text-slate-500">{{ __('Base Price') }}</p>
                <p class="mt-1 font-semibold text-white">{{ number_format((float) $plan->base_price, 0) }} {{ __('EGP') }}</p>
            </div>
            <div class="rounded-xl border border-white/10 bg-slate-950/40 p-3">
                <p class="text-xs text-slate-500">{{ __('Discount') }}</p>
                <p class="mt-1 font-semibold text-emerald-300">-{{ number_format((float) $plan->discount_amount, 0) }} {{ __('EGP') }}</p>
            </div>
            <div class="rounded-xl border border-emerald-500/20 bg-emerald-500/10 p-3">
                <p class="text-xs text-slate-500">{{ __('Final Price') }}</p>
                <p class="mt-1 font-semibold text-emerald-300">{{ number_format((float) $plan->final_price, 0) }} {{ __('EGP') }}</p>
            </div>
            <div class="rounded-xl border border-white/10 bg-slate-950/40 p-3">
                <p class="text-xs text-slate-500">{{ __('Maintenance Deposit') }}</p>
                <p class="mt-1 font-semibold text-amber-300">{{ number_format((float) $plan->maintenance_deposit, 0) }} {{ __('EGP') }}</p>
            </div>
            <div class="rounded-xl border border-white/10 bg-slate-950/40 p-3">
                <p class="text-xs text-slate-500">{{ __('Down Payment') }}</p>
                <p class="mt-1 font-semibold text-white">{{ number_format((float) $plan->down_payment, 0) }} {{ __('EGP') }}</p>
            </div>
            <div class="rounded-xl border border-white/10 bg-slate-950/40 p-3">
                <p class="text-xs text-slate-500">{{ __('Remaining') }}</p>
                <p class="mt-1 font-semibold text-white">{{ number_format((float) $plan->remaining_amount, 0) }} {{ __('EGP') }}</p>
            </div>
            <div class="rounded-xl border border-white/10 bg-slate-950/40 p-3">
                <p class="text-xs text-slate-500">{{ __('Installment Amount') }}</p>
                <p class="mt-1 font-semibold text-white">{{ number_format((float) $plan->installment_amount, 0) }} {{ __('EGP') }}</p>
            </div>
            <div class="rounded-xl border border-white/10 bg-slate-950/40 p-3">
                <p class="text-xs text-slate-500">{{ __('Installments') }}</p>
                <p class="mt-1 font-semibold text-white">{{ $plan->installment_count }}</p>
            </div>
        </div>
    </section>

    {{-- Schedule Table --}}
    <section class="app-card overflow-hidden p-0">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/10 px-5 py-4">
            <div>
                <p class="mobile-section-title">{{ __('Schedule') }}</p>
                <h2 class="mt-1 text-lg font-semibold text-white">{{ $items->isNotEmpty() ? $items->count() : count($schedule) }} {{ __('installments') }}</h2>
            </div>
            <a href="{{ route('dashboard.crm.plans.pdf', $plan) }}" class="app-button !py-2 text-xs">{{ __('Download PDF') }}</a>
        </div>

        @if (count($schedule) === 0 && $items->isEmpty())
            <div class="p-5">
                <div class="rounded-xl bg-emerald-500/10 p-4 text-sm text-emerald-200 border border-emerald-500/30">
                    {{ __('Cash payment — the full amount is paid upfront.') }}
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Due Date') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Balance After') }}</th>
                            <th>{{ __('Paid Amount') }}</th>
                            <th class="hidden sm:table-cell">{{ __('Date Paid') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="hidden sm:table-cell">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalAmt = 0;
                            $totalPd = 0;
                        @endphp
                        @forelse ($items as $item)
                            @php
                                $amount = (float) $item->amount;
                                $paid = (float) $item->paid_amount;
                                $totalAmt += $amount;
                                $totalPd += $paid;
                                $isFullyPaid = $paid >= $amount && $amount > 0;
                                $isPartial = $paid > 0 && !$isFullyPaid;
                                $isOverdue = !$isFullyPaid && $item->due_date && $item->due_date->isPast();
                                $remaining = max(0, $amount - $paid);
                                $itemProgress = $amount > 0 ? min(100, round(($paid / $amount) * 100)) : 0;
                            @endphp
                            <tr class="{{ $isFullyPaid ? 'bg-emerald-500/5' : ($isOverdue ? 'bg-red-500/5' : '') }}">
                                <td class="font-semibold text-white">{{ $item->installment_number }}</td>
                                <td>
                                    <div>{{ $item->due_date?->format('Y-m-d') }}</div>
                                    @if($isOverdue)
                                        <span class="text-[10px] text-red-400">{{ __('Overdue') }}</span>
                                    @endif
                                </td>
                                <td class="font-semibold text-white">{{ number_format($amount, 2) }} {{ __('EGP') }}</td>
                                <td class="text-slate-400">{{ number_format($remaining, 2) }} {{ __('EGP') }}</td>
                                <td>
                                    @if($isFullyPaid)
                                        <span class="font-semibold text-emerald-300">{{ number_format($paid, 2) }}</span>
                                    @else
                                        <form method="POST" action="{{ route('dashboard.crm.plans.items.update', [$plan, $item]) }}" class="flex items-center gap-1.5" x-data="{ saving: false }">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="paid_amount" step="0.01" min="0" max="{{ $amount }}" value="{{ number_format($paid, 2, '.', '') }}" class="form-input w-24 rounded-lg py-1 text-xs" aria-label="{{ __('Paid Amount') }}">
                                            <button type="submit" @click="saving = true" class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-300 transition hover:bg-emerald-500/25" title="{{ __('Save') }}">
                                                <svg x-show="!saving" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 6 9 17l-5-5" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                <svg x-show="saving" class="h-3.5 w-3.5 animate-spin" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" opacity="0.25"/><path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                                <td class="hidden sm:table-cell text-xs text-slate-400">
                                    @if($item->paid_at)
                                        {{ $item->paid_at->format('Y-m-d H:i') }}
                                        @if($item->paid_by)
                                            <br><span class="text-slate-500">{{ $item->paid_by }}</span>
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if($isFullyPaid)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/15 px-2.5 py-0.5 text-xs font-semibold text-emerald-300">
                                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" stroke-width="2"/><path d="M22 4 12 14.01l-3-3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            {{ __('Paid') }}
                                        </span>
                                    @elseif($isPartial)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-500/15 px-2.5 py-0.5 text-xs font-semibold text-amber-300">
                                            {{ __('Partial') }} ({{ $itemProgress }}%)
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-slate-500/15 px-2.5 py-0.5 text-xs font-semibold text-slate-400">{{ __('Unpaid') }}</span>
                                    @endif
                                </td>
                                <td class="hidden sm:table-cell">
                                    <div class="flex items-center gap-1">
                                        @if($isFullyPaid)
                                            <a href="{{ route('dashboard.crm.plans.items.receipt', [$plan, $item]) }}" class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-300 transition hover:bg-emerald-500/25" title="{{ __('Receipt') }}">
                                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-width="1.8"/><path d="M14 2v6h6" stroke-width="1.8"/><path d="M9 15l2 2 4-4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </a>
                                        @else
                                            <form action="{{ route('dashboard.crm.plans.items.full-pay', [$plan, $item]) }}" method="POST" onsubmit="return confirm('{{ __('Pay remaining :amount EGP for this installment?', ['amount' => number_format($remaining, 2)]) }}')">
                                                @csrf
                                                <button type="submit" class="inline-flex h-7 items-center gap-1 rounded-lg bg-brand-500/15 px-2 text-[10px] font-semibold text-brand-300 transition hover:bg-brand-500/25" title="{{ __('Full pay') }}">
                                                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    {{ __('Pay') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            @foreach ($schedule as $row)
                                @php($totalAmt += (float) $row['amount'])
                                <tr>
                                    <td class="font-semibold text-white">{{ $row['installment_number'] ?? $loop->iteration }}</td>
                                    <td>{{ is_string($row['due_date'] ?? '') ? $row['due_date'] : optional($row['due_date'] ?? null)?->format('Y-m-d') }}</td>
                                    <td class="font-semibold text-white">{{ number_format((float) $row['amount'], 2) }} {{ __('EGP') }}</td>
                                    <td class="text-slate-400">{{ number_format((float) $row['balance_after'], 2) }} {{ __('EGP') }}</td>
                                    <td class="text-slate-500">0.00</td>
                                    <td class="hidden sm:table-cell text-slate-500">—</td>
                                    <td><span class="inline-flex rounded-full bg-slate-500/15 px-2.5 py-0.5 text-xs font-semibold text-slate-400">{{ __('Unpaid') }}</span></td>
                                    <td class="hidden sm:table-cell"></td>
                                </tr>
                            @endforeach
                        @endforelse
                        <tr class="bg-emerald-500/10 font-semibold">
                            <td class="text-white" colspan="2">{{ __('Total') }}</td>
                            <td class="font-semibold text-emerald-300">{{ number_format($totalAmt, 2) }} {{ __('EGP') }}</td>
                            <td></td>
                            <td class="font-semibold text-emerald-300">{{ number_format($totalPd, 2) }} {{ __('EGP') }}</td>
                            <td class="hidden sm:table-cell"></td>
                            <td></td>
                            <td class="hidden sm:table-cell"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
@endsection
