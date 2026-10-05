@extends('layouts.dashboard')

@section('content')
<div class="space-y-6" x-data="offerForm()" x-init="init()">
    @include('crm.partials.crm-nav')

    {{-- Hero Header --}}
    <section class="dashboard-hero-card p-6 sm:p-8">
        <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-amber-500/20 blur-3xl"></div>
        <div class="absolute -bottom-20 left-1/3 h-56 w-56 rounded-full bg-brand-500/10 blur-3xl"></div>

        <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="badge badge-brand">{{ __('CRM') }}</span>
                    <span class="badge badge-amber">{{ $offer ? __('Edit') : __('New') }}</span>
                </div>
                <div>
                    <p class="mobile-section-title">{{ __('Offers') }}</p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ $offer ? __('Edit Offer') : __('New Offer') }}</h1>
                    <p class="mt-2 max-w-2xl text-sm text-slate-300">{{ __('Create a professional price offer linked to a lead or customer with automatic pricing and installment options.') }}</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('dashboard.crm.offers.index') }}" class="app-button--ghost">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 12H5M12 19l-7-7 7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    {{ __('Back') }}
                </a>
                <button type="submit" form="offer-form" class="app-button">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round"/></svg>
                    {{ $offer ? __('Update Offer') : __('Create Offer') }}
                </button>
            </div>
        </div>
    </section>

    @if ($errors->any())
        <div class="flex items-start gap-3 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-300">
            <svg class="mt-0.5 h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke-width="1.8"/><path d="M12 9v4M12 17h.01" stroke-width="1.8" stroke-linecap="round"/></svg>
            <div>
                <p class="font-semibold">{{ __('Please fix the errors below.') }}</p>
                <ul class="mt-1 list-inside list-disc text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form id="offer-form" action="{{ $offer ? route('dashboard.crm.offers.update', $offer) : route('dashboard.crm.offers.store') }}" method="POST" class="space-y-6">
        @csrf
        @if ($offer) @method('PUT') @endif

        {{-- Client Selection (full width) --}}
        <section class="app-card app-card--gradient space-y-5">
            <div class="flex items-center gap-3 border-b border-white/5 pb-4">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-500/15 text-brand-400">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="9" cy="7" r="4" stroke-width="1.8"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-width="1.8"/></svg>
                </span>
                <div>
                    <h2 class="text-lg font-semibold text-white">{{ __('Client') }}</h2>
                    <p class="text-xs text-slate-400">{{ __('Choose one client type for this offer.') }}</p>
                </div>
            </div>

            {{-- Client type toggle --}}
            <div class="flex gap-2 rounded-xl bg-slate-950/40 p-1.5">
                <button type="button"
                        @click="clientType = 'customer'; leadId = ''"
                        :class="clientType === 'customer' ? 'bg-brand-600 text-white shadow' : 'text-slate-400 hover:bg-white/5 hover:text-white'"
                        class="flex-1 rounded-lg px-4 py-2 text-sm font-medium transition">
                    {{ __('Customer') }}
                </button>
                <button type="button"
                        @click="clientType = 'lead'; customerId = ''"
                        :class="clientType === 'lead' ? 'bg-brand-600 text-white shadow' : 'text-slate-400 hover:bg-white/5 hover:text-white'"
                        class="flex-1 rounded-lg px-4 py-2 text-sm font-medium transition">
                    {{ __('Lead') }}
                </button>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div x-show="clientType === 'customer'" x-cloak x-transition>
                    <label class="mb-2 block text-sm font-medium text-slate-300">{{ __('Customer') }} <span class="text-rose-400">*</span></label>
                    <select name="customer_id" class="app-input w-full" :required="clientType === 'customer'" x-model="customerId">
                        <option value="">{{ __('Select customer') }}</option>
                        @foreach ($customers as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div x-show="clientType === 'lead'" x-cloak x-transition>
                    <label class="mb-2 block text-sm font-medium text-slate-300">{{ __('Lead') }} <span class="text-rose-400">*</span></label>
                    <select name="lead_id" class="app-input w-full" :required="clientType === 'lead'" x-model="leadId">
                        <option value="">{{ __('Select lead') }}</option>
                        @foreach ($leads as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </section>

        {{-- Unit Selection: Project → Building → Floor → Unit (full width) --}}
        <section class="app-card app-card--gradient space-y-5">
            <div class="flex items-center gap-3 border-b border-white/5 pb-4">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-500/15 text-brand-400">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 21h18M5 21V7a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v14" stroke-width="1.8"/></svg>
                </span>
                <div class="flex-1">
                    <h2 class="text-lg font-semibold text-white">{{ __('Select Unit') }}</h2>
                    <p class="text-xs text-slate-400">{{ __('Choose project, building, floor, then unit. Only available units are shown.') }}</p>
                </div>
                <span class="badge badge-success text-xs" x-show="filteredUnits.length > 0" x-cloak>
                    <span x-text="filteredUnits.length"></span> {{ __('available') }}
                </span>
            </div>

            {{-- Cascade selectors --}}
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Step 1: Project --}}
                <div>
                    <label class="mb-2 flex items-center gap-1.5 text-sm font-medium text-slate-300">
                        <span class="flex h-5 w-5 items-center justify-center rounded-md bg-brand-500/20 text-[10px] font-bold text-brand-300">1</span>
                        {{ __('Project') }}
                    </label>
                    <select class="app-input w-full" x-model="selProject" @change="onProjectChange()">
                        <option value="">{{ __('All projects') }}</option>
                        @foreach ($projects as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Step 2: Building --}}
                <div>
                    <label class="mb-2 flex items-center gap-1.5 text-sm font-medium text-slate-300">
                        <span class="flex h-5 w-5 items-center justify-center rounded-md bg-brand-500/20 text-[10px] font-bold text-brand-300">2</span>
                        {{ __('Building') }}
                    </label>
                    <select class="app-input w-full" x-model="selBuilding" @change="onBuildingChange()" :disabled="!selProject">
                        <option value="">{{ __('All buildings') }}</option>
                        <template x-for="b in filteredBuildings" :key="b.id">
                            <option :value="b.id" x-text="b.name"></option>
                        </template>
                    </select>
                </div>

                {{-- Step 3: Floor --}}
                <div>
                    <label class="mb-2 flex items-center gap-1.5 text-sm font-medium text-slate-300">
                        <span class="flex h-5 w-5 items-center justify-center rounded-md bg-brand-500/20 text-[10px] font-bold text-brand-300">3</span>
                        {{ __('Floor') }}
                    </label>
                    <select class="app-input w-full" x-model="selFloor" @change="onFloorChange()" :disabled="!selBuilding">
                        <option value="">{{ __('All floors') }}</option>
                        <template x-for="f in filteredFloors" :key="f.id">
                            <option :value="f.id" x-text="f.name"></option>
                        </template>
                    </select>
                </div>

                {{-- Step 4: Unit --}}
                <div>
                    <label class="mb-2 flex items-center gap-1.5 text-sm font-medium text-slate-300">
                        <span class="flex h-5 w-5 items-center justify-center rounded-md bg-brand-500/20 text-[10px] font-bold text-brand-300">4</span>
                        {{ __('Unit') }} <span class="text-rose-400">*</span>
                    </label>
                    <select name="unit_id" class="app-input w-full" required x-model="selectedUnit" @change="onUnitChange()">
                        <option value="">{{ __('Select unit') }}</option>
                        <template x-for="u in filteredUnits" :key="u.id">
                            <option :value="u.id" x-text="u.label"></option>
                        </template>
                    </select>
                </div>
            </div>

            {{-- Available units count hint --}}
            <div x-show="filteredUnits.length === 0 && (selProject || selBuilding || selFloor)" x-cloak
                 class="rounded-xl border border-amber-500/20 bg-amber-500/5 px-4 py-3 text-sm text-amber-300">
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 9v4M12 17h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke-width="1.8"/></svg>
                    {{ __('No available units match the selected filters.') }}
                </div>
            </div>

            {{-- Selected unit hidden fallback (for edit mode when unit is not available) --}}
            @if ($offer && $offer->unit_id)
                <div x-show="!selectedUnitData && selectedUnit" x-cloak
                     class="rounded-xl border border-amber-500/20 bg-amber-500/5 px-4 py-3 text-xs text-amber-300">
                    {{ __('This unit is no longer available for sale but is kept for this existing offer.') }}
                </div>
            @endif

            {{-- Unit Preview Card --}}
            <div x-show="selectedUnitData" x-cloak x-transition class="rounded-2xl border border-brand-500/20 bg-brand-500/5 p-4">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <div class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 21h18M5 21V7a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v14" stroke-width="1.8"/></svg>
                        <p class="text-sm font-semibold text-brand-300">{{ __('Selected Unit Details') }}</p>
                    </div>
                    <span class="badge badge-success text-[10px]" x-show="selectedUnitData?.status === 'available'">{{ __('Available') }}</span>
                    <span class="badge badge-amber text-[10px]" x-show="selectedUnitData?.status !== 'available'">{{ __('Reserved/Sold') }}</span>
                </div>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4" x-show="selectedUnitData">
                    <div>
                        <p class="text-[10px] uppercase tracking-wide text-slate-500">{{ __('Unit #') }}</p>
                        <p class="text-sm font-semibold text-white" x-text="selectedUnitData?.unit_number"></p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wide text-slate-500">{{ __('Type') }}</p>
                        <p class="text-sm font-semibold text-white" x-text="selectedUnitData?.unit_type"></p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wide text-slate-500">{{ __('Area') }}</p>
                        <p class="text-sm font-semibold text-white"><span x-text="selectedUnitData?.area"></span> m²</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wide text-slate-500">{{ __('Rooms') }}</p>
                        <p class="text-sm font-semibold text-white"><span x-text="selectedUnitData?.bedrooms"></span> BD / <span x-text="selectedUnitData?.bathrooms"></span> BA</p>
                    </div>
                    <div class="col-span-2 sm:col-span-4 rounded-xl bg-slate-950/40 px-3 py-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-400">{{ __('Unit Price') }}</span>
                            <span class="text-lg font-bold text-emerald-400">{{ $currency }} <span x-text="formatNumber(selectedUnitData?.current_price)"></span></span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Pricing + Dates & Status side by side --}}
        <div class="grid gap-6 md:grid-cols-2">
            {{-- Pricing --}}
            <section class="app-card app-card--gradient space-y-5">
                <div class="flex items-center gap-3 border-b border-white/5 pb-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-400">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="9" stroke-width="1.8"/><path d="M12 7v10M9.5 9.5c0-.83 1.12-1.5 2.5-1.5s2.5.67 2.5 1.5-1.12 1.5-2.5 1.5-2.5.67-2.5 1.5 1.12 1.5 2.5 1.5 2.5.67 2.5 1.5-1.12 1.5-2.5 1.5-2.5-.67-2.5-1.5" stroke-width="1.6"/></svg>
                    </span>
                    <div>
                        <h2 class="text-lg font-semibold text-white">{{ __('Pricing') }}</h2>
                        <p class="text-xs text-slate-400">{{ __('Subtotal and discount are calculated automatically. Override if needed.') }}</p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-300">{{ __('Subtotal') }} <span class="text-rose-400">*</span></label>
                        <div class="relative">
                            <span class="absolute start-3 top-1/2 -translate-y-1/2 text-xs text-slate-500">{{ $currency }}</span>
                            <input type="number" step="0.01" name="subtotal" x-model.number="subtotal" @input="recalculate()" class="app-input w-full ps-12" required>
                        </div>
                        <p class="mt-1 text-[11px] text-slate-500" x-show="selectedUnitData" x-cloak>{{ __('Auto-filled from unit price') }}</p>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-300">{{ __('Discount') }}</label>
                        <div class="relative">
                            <span class="absolute start-3 top-1/2 -translate-y-1/2 text-xs text-slate-500">{{ $currency }}</span>
                            <input type="number" step="0.01" name="discount_amount" x-model.number="discount" @input="recalculate()" class="app-input w-full ps-12">
                        </div>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-300">{{ __('Total') }} <span class="text-rose-400">*</span></label>
                        <div class="relative">
                            <span class="absolute start-3 top-1/2 -translate-y-1/2 text-xs text-slate-500">{{ $currency }}</span>
                            <input type="number" step="0.01" name="total_amount" x-model.number="total" class="app-input w-full ps-12 font-bold text-emerald-400" required>
                        </div>
                    </div>
                </div>

                {{-- Quick summary bar --}}
                <div class="flex items-center justify-between rounded-xl bg-slate-950/40 px-4 py-3">
                    <div class="flex items-center gap-2 text-xs text-slate-400">
                        <svg class="h-4 w-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 12l2 2 4-4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="9" stroke-width="1.8"/></svg>
                        {{ __('Total = Subtotal − Discount') }}
                    </div>
                    <div class="text-sm font-bold text-white">
                        {{ $currency }} <span x-text="formatNumber(total)"></span>
                    </div>
                </div>
            </section>

            {{-- Dates & Status --}}
            <section class="app-card app-card--gradient space-y-5">
                <div class="flex items-center gap-3 border-b border-white/5 pb-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-500/15 text-violet-400">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.8"/><path d="M16 2v4M8 2v4M3 10h18" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </span>
                    <div>
                        <h2 class="text-lg font-semibold text-white">{{ __('Dates & Status') }}</h2>
                        <p class="text-xs text-slate-400">{{ __('Offer validity period and current workflow status.') }}</p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-300">{{ __('Issue date') }}</label>
                        <input type="date" name="issue_date" value="{{ $offer?->issue_date?->format('Y-m-d') ?? now()->format('Y-m-d') }}" class="app-input w-full">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-300">{{ __('Valid until') }}</label>
                        <input type="date" name="valid_until" value="{{ $offer?->valid_until?->format('Y-m-d') ?? now()->addDays(7)->format('Y-m-d') }}" class="app-input w-full">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-300">{{ __('Status') }}</label>
                        <select name="status" class="app-input w-full">
                            @foreach (['draft', 'sent', 'accepted', 'rejected', 'expired'] as $status)
                                <option value="{{ $status }}" {{ ($offer?->status ?? 'draft') === $status ? 'selected' : '' }}>{{ __(ucfirst($status)) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if ($installmentTemplates->isNotEmpty())
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-300">{{ __('Installment Template') }}</label>
                    <select name="installment_template_id" class="app-input w-full">
                        <option value="">{{ __('No installment plan') }}</option>
                        @foreach ($installmentTemplates as $id => $name)
                            <option value="{{ $id }}" {{ ($offer?->installment_template_id == $id) ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
            </section>
        </div>

        {{-- Notes + Summary side by side --}}
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-[1.3fr_0.7fr]">
            {{-- Notes --}}
            <section class="app-card app-card--gradient space-y-5">
                <div class="flex items-center gap-3 border-b border-white/5 pb-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-500/15 text-sky-400">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-width="1.8"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </span>
                    <div>
                        <h2 class="text-lg font-semibold text-white">{{ __('Notes') }}</h2>
                        <p class="text-xs text-slate-400">{{ __('Internal notes about this offer (not shown to the customer).') }}</p>
                    </div>
                </div>
                <textarea name="notes" rows="6" class="app-input w-full" placeholder="{{ __('Add any internal notes about this offer...') }}">{{ $offer?->notes }}</textarea>
            </section>

            {{-- Summary Sidebar --}}
            <section class="app-card app-card--gradient space-y-4 lg:sticky lg:top-6">
                <div class="flex items-center gap-3 border-b border-white/5 pb-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500/15 text-amber-400">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-width="1.8"/><path d="M14 2v6h6" stroke-width="1.8"/></svg>
                    </span>
                    <div>
                        <h2 class="text-lg font-semibold text-white">{{ __('Summary') }}</h2>
                        <p class="text-xs text-slate-400">{{ __('Offer overview') }}</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="rounded-xl bg-slate-950/40 p-3">
                        <p class="text-[10px] uppercase tracking-wide text-slate-500">{{ __('Client') }}</p>
                        <p class="mt-1 text-sm font-semibold text-white" x-text="clientName || '—'"></p>
                    </div>
                    <div class="rounded-xl bg-slate-950/40 p-3">
                        <p class="text-[10px] uppercase tracking-wide text-slate-500">{{ __('Unit') }}</p>
                        <p class="mt-1 text-sm font-semibold text-white" x-text="selectedUnitData?.label || '—'"></p>
                    </div>
                    <div class="rounded-xl bg-slate-950/40 p-3">
                        <p class="text-[10px] uppercase tracking-wide text-slate-500">{{ __('Subtotal') }}</p>
                        <p class="mt-1 text-lg font-bold text-white">{{ $currency }} <span x-text="formatNumber(subtotal)"></span></p>
                    </div>
                    <div class="rounded-xl bg-slate-950/40 p-3">
                        <p class="text-[10px] uppercase tracking-wide text-slate-500">{{ __('Discount') }}</p>
                        <p class="mt-1 text-lg font-bold text-rose-400">− {{ $currency }} <span x-text="formatNumber(discount)"></span></p>
                    </div>
                    <div class="rounded-xl bg-emerald-500/10 border border-emerald-500/20 p-4">
                        <p class="text-[10px] uppercase tracking-wide text-emerald-400">{{ __('Total') }}</p>
                        <p class="mt-1 text-2xl font-black text-emerald-400">{{ $currency }} <span x-text="formatNumber(total)"></span></p>
                    </div>
                </div>
            </section>
        </div>
    </form>
</div>

@push('scripts')
<script>
function offerForm() {
    return {
        unitsData: {{ Js::from($unitsData) }},
        buildingsData: {{ Js::from($buildingsData) }},
        floorsData: {{ Js::from($floorsData) }},
        customersData: {{ Js::from($customers) }},
        leadsData: {{ Js::from($leads) }},

        // Cascade selection state
        selProject: '{{ $offer?->unit?->project_id ?? '' }}',
        selBuilding: '{{ $offer?->unit?->building_id ?? '' }}',
        selFloor: '{{ $offer?->unit?->floor_id ?? '' }}',
        selectedUnit: '{{ $offer?->unit_id ?? '' }}',

        clientType: '{{ $offer?->customer_id ? 'customer' : ($offer?->lead_id ? 'lead' : 'customer') }}',
        customerId: '{{ $offer?->customer_id ?? '' }}',
        leadId: '{{ $offer?->lead_id ?? '' }}',
        subtotal: {{ $offer?->subtotal ?? 0 }},
        discount: {{ $offer?->discount_amount ?? 0 }},
        total: {{ $offer?->total_amount ?? 0 }},

        init() {
            @if(!$offer)
            if (this.selectedUnitData && this.subtotal == 0) {
                this.subtotal = parseFloat(this.selectedUnitData.current_price) || 0;
                this.recalculate();
            }
            @endif
        },

        get selectedUnitData() {
            if (!this.selectedUnit) return null;
            return this.unitsData.find(u => String(u.id) === String(this.selectedUnit)) || null;
        },

        get clientName() {
            if (this.clientType === 'customer') return this.customersData[this.customerId] || '';
            return this.leadsData[this.leadId] || '';
        },

        //-- Cascade filtering getters --

        get filteredBuildings() {
            if (!this.selProject) return this.buildingsData;
            return this.buildingsData.filter(b => String(b.project_id) === String(this.selProject));
        },

        get filteredFloors() {
            let floors = this.floorsData;
            if (this.selProject) {
                floors = floors.filter(f => String(f.project_id) === String(this.selProject));
            }
            if (this.selBuilding) {
                floors = floors.filter(f => String(f.building_id) === String(this.selBuilding));
            }
            return floors;
        },

        get filteredUnits() {
            let units = this.unitsData;
            if (this.selProject) {
                units = units.filter(u => String(u.project_id) === String(this.selProject));
            }
            if (this.selBuilding) {
                units = units.filter(u => String(u.building_id) === String(this.selBuilding));
            }
            if (this.selFloor) {
                units = units.filter(u => String(u.floor_id) === String(this.selFloor));
            }
            return units;
        },

        //-- Cascade change handlers --

        onProjectChange() {
            if (this.selBuilding && !this.filteredBuildings.find(b => String(b.id) === String(this.selBuilding))) {
                this.selBuilding = '';
            }
            if (this.selFloor && !this.filteredFloors.find(f => String(f.id) === String(this.selFloor))) {
                this.selFloor = '';
            }
            if (this.selectedUnit && !this.filteredUnits.find(u => String(u.id) === String(this.selectedUnit))) {
                this.selectedUnit = '';
            }
        },

        onBuildingChange() {
            if (this.selFloor && !this.filteredFloors.find(f => String(f.id) === String(this.selFloor))) {
                this.selFloor = '';
            }
            if (this.selectedUnit && !this.filteredUnits.find(u => String(u.id) === String(this.selectedUnit))) {
                this.selectedUnit = '';
            }
        },

        onFloorChange() {
            if (this.selectedUnit && !this.filteredUnits.find(u => String(u.id) === String(this.selectedUnit))) {
                this.selectedUnit = '';
            }
        },

        onUnitChange() {
            if (this.selectedUnitData) {
                this.subtotal = parseFloat(this.selectedUnitData.current_price) || 0;
                this.recalculate();
            }
        },

        recalculate() {
            const sub = parseFloat(this.subtotal) || 0;
            const disc = parseFloat(this.discount) || 0;
            this.total = Math.max(0, sub - disc);
        },

        formatNumber(val) {
            const n = parseFloat(val) || 0;
            return n.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
        },
    };
}
</script>
@endpush
@endsection
