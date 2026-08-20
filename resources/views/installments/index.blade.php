@extends($layout ?? 'layouts.public')

@section('content')
    @php
        $template = $templates->first();
        $preselectedUnit = $units->firstWhere('id', request('unit_id'));
        $preselectedProjectId = request('project_id', $preselectedUnit?->project_id);
        $preselectedBuildingId = request('building_id', $preselectedUnit?->building_id);
        $defaultInstallmentYears = $template
            ? $template->installment_count * $template->installment_frequency->monthsPerInstallment() / 12
            : 4;

        // Always produce a valid number for the Alpine x-data initializers. A failed
        // validation redirect (withInput) can otherwise render "area: " and break the
        // whole Alpine component, leaving every field empty.
        $calcNum = fn (string $field, mixed $default): float => is_numeric(old($field)) ? (float) old($field) : (float) ($default ?? 0);
        $calcDefaults = [
            'area' => $calcNum('area', $preselectedUnit?->area ?? 0),
            'price_per_meter' => $calcNum('price_per_meter', $preselectedUnit?->price_per_meter ?? 0),
            'garden_price' => $calcNum('garden_price', $preselectedUnit?->garden_price ?? 0),
            'roof_price' => $calcNum('roof_price', $preselectedUnit?->roof_price ?? 0),
            'roof_area' => $calcNum('roof_area', $preselectedUnit?->roof_area ?? 0),
            'excellence_percent' => $calcNum('excellence_percent', $preselectedUnit?->excellence_percent ?? $template?->defaults()['excellence_percent'] ?? 0),
            'down_payment_percent' => $calcNum('down_payment_percent', $template?->down_payment_percent ?? 10),
            'down_payment' => $calcNum('down_payment', 0),
            'maintenance_percent' => $calcNum('maintenance_percent', $template?->maintenance_percent ?? $companyProfile?->maintenance_percent ?? 7),
            'installment_years' => $calcNum('installment_years', $defaultInstallmentYears),
            // Only monthly/quarterly are offered; normalize anything else (e.g. a
            // semi-annual template default) to quarterly so the dropdown stays valid.
            'installment_type' => (fn (): string => in_array(old('installment_type', $template?->installment_frequency?->value ?? 'quarterly'), ['monthly', 'quarterly'], true)
                ? (string) old('installment_type', $template?->installment_frequency?->value ?? 'quarterly')
                : 'quarterly')(),
        ];
    @endphp
    <div
        class="space-y-6 pt-4"
        x-data="{
            isDashboard: {{ $isDashboard ? 'true' : 'false' }},
            area: {{ $calcDefaults['area'] }},
            pricePerMeter: {{ $calcDefaults['price_per_meter'] }},
            gardenPrice: {{ $calcDefaults['garden_price'] }},
            roofPrice: {{ $calcDefaults['roof_price'] }},
            roofArea: {{ $calcDefaults['roof_area'] }},
            excellencePercent: {{ $calcDefaults['excellence_percent'] }},
            downPaymentPercent: {{ $calcDefaults['down_payment_percent'] }},
            downPayment: {{ $calcDefaults['down_payment'] }},
            maintenancePercent: {{ $calcDefaults['maintenance_percent'] }},
            installmentYears: {{ $calcDefaults['installment_years'] }},
            installmentType: '{{ $calcDefaults['installment_type'] }}',
            paymentMethod: '{{ old('payment_method', 'installments') }}',
            firstInstallmentDate: '{{ old('first_installment_date', now()->toDateString()) }}',
            unitId: '{{ old('unit_id', request('unit_id')) }}',
            projectId: '{{ old('project_id', $preselectedProjectId) }}',
            buildingId: '{{ old('building_id', $preselectedBuildingId) }}',
            floorId: '{{ old('floor_id') }}',
            templateId: '{{ old('installment_template_id', $template?->id) }}',
            installmentLabel: @js(__('Installment')),
            buildings: {{ Js::from($buildings->map(fn ($b) => [
                'id' => (int) $b->id,
                'name' => $b->name,
                'code' => $b->code,
                'project_id' => (int) $b->project_id,
                'project_name' => $b->project?->name ?? '',
            ])->values()->all()) }},
            floors: {{ Js::from($floors->map(fn ($f) => [
                'id' => (int) $f->id,
                'name' => $f->name ?: ($f->number !== null ? __('Floor :number', ['number' => $f->number]) : ''),
                'number' => $f->number,
                'project_id' => (int) $f->project_id,
                'building_id' => (int) ($f->building_id ?? 0),
                'building_name' => $f->building?->name ?? '',
            ])->values()->all()) }},
            units: {{ Js::from($units->map(fn ($u) => [
                'id' => (int) $u->id,
                'unit_number' => $u->unit_number,
                'project_id' => (int) $u->project_id,
                'building_id' => (int) ($u->building_id ?? 0),
                'floor_id' => (int) ($u->floor_id ?? 0),
                'project_name' => $u->project?->name ?? '',
                'building_name' => $u->building?->name ?? '',
                'floor_name' => $u->floor?->name ?? '',
                'area' => (float) $u->area,
                'price_per_meter' => (float) $u->price_per_meter,
                'garden_price' => (float) $u->garden_price,
                'roof_price' => (float) $u->roof_price,
                'roof_area' => (float) $u->roof_area,
                'excellence_percent' => (float) $u->excellence_percent,
                'status' => $u->status?->value ?? 'available',
            ])->values()->all()) }},
            statusLabels: { available: @js(__('Available')), reserved: @js(__('Reserved')), sold: @js(__('Sold')), hidden: @js(__('Hidden')) },
            unavailableMessage: @js(__('Unit :unit is not available for sale — it is currently :status.')),
            get selectedUnit() {
                return this.units.find(x => Number(x.id) === Number(this.unitId)) || null;
            },
            get selectedBuilding() {
                return this.buildings.find(x => Number(x.id) === Number(this.buildingId)) || null;
            },
            get unitAvailable() {
                return ! this.selectedUnit || this.selectedUnit.status === 'available';
            },
            get unitStatusLabel() {
                return this.selectedUnit ? (this.statusLabels[this.selectedUnit.status] || this.selectedUnit.status) : '';
            },
            get filteredBuildings() {
                if (! this.projectId) return this.buildings;
                return this.buildings.filter(b => Number(b.project_id) === Number(this.projectId));
            },
            get filteredFloors() {
                if (! this.projectId) return this.floors;
                return this.floors.filter(f => {
                    if (Number(f.project_id) !== Number(this.projectId)) {
                        return false;
                    }
                    if (! this.buildingId) {
                        return true;
                    }
                    return Number(f.building_id) === Number(this.buildingId);
                });
            },
            get unitUnavailableMessage() {
                if (! this.selectedUnit) return '';
                return this.unavailableMessage
                    .replace(':unit', this.selectedUnit.unit_number)
                    .replace(':status', this.unitStatusLabel);
            },
            get filteredUnits() {
                if (! this.projectId) return this.units;
                return this.units.filter(u => {
                    if (Number(u.project_id) !== Number(this.projectId)) {
                        return false;
                    }

                    if (this.buildingId && Number(u.building_id) !== Number(this.buildingId)) {
                        return false;
                    }

                    if (this.floorId && Number(u.floor_id) !== Number(this.floorId)) {
                        return false;
                    }

                    return true;
                });
            },
            selectUnit() {
                const u = this.units.find(x => Number(x.id) === Number(this.unitId));
                if (u) {
                    this.projectId = String(u.project_id);
                    this.buildingId = String(u.building_id || '');
                    this.floorId = String(u.floor_id || '');
                    this.area = u.area;
                    this.pricePerMeter = u.price_per_meter;
                    this.gardenPrice = u.garden_price;
                    this.roofPrice = u.roof_price;
                    this.roofArea = u.roof_area;
                    this.excellencePercent = u.excellence_percent;
                }
            },
            init() {
                // The unit/building options are rendered by x-for AFTER the x-model
                // initial sync runs, so a pre-selected unit (unit_id query param from
                // the unit page) leaves those selects visually blank and submits an
                // empty unit_id. Re-apply the model values once the options exist.
                this.$nextTick(() => {
                    if (this.unitId && this.units.some(u => Number(u.id) === Number(this.unitId))) {
                        this.selectUnit();
                    }
                    if (this.$refs.buildingSelect) {
                        this.$refs.buildingSelect.value = this.buildingId || '';
                    }
                    if (this.$refs.floorSelect) {
                        this.$refs.floorSelect.value = this.floorId || '';
                    }
                    if (this.$refs.unitSelect) {
                        this.$refs.unitSelect.value = this.unitId || '';
                    }
                });
            },
            onProjectChange() {
                const u = this.units.find(x => Number(x.id) === Number(this.unitId));
                const b = this.buildings.find(x => Number(x.id) === Number(this.buildingId));

                if (u && Number(u.project_id) !== Number(this.projectId)) {
                    this.unitId = '';
                }

                if (b && Number(b.project_id) !== Number(this.projectId)) {
                    this.buildingId = '';
                    this.floorId = '';
                }

                if (! u || Number(u.project_id) !== Number(this.projectId) || (this.buildingId && Number(u.building_id) !== Number(this.buildingId))) {
                    this.unitId = '';
                }
            },
            onBuildingChange() {
                const f = this.floors.find(x => Number(x.id) === Number(this.floorId));
                if (f && Number(f.building_id) !== Number(this.buildingId)) {
                    this.floorId = '';
                }
                const u = this.units.find(x => Number(x.id) === Number(this.unitId));
                if (! u || Number(u.building_id) !== Number(this.buildingId)) {
                    this.unitId = '';
                }
            },
            onFloorChange() {
                const u = this.units.find(x => Number(x.id) === Number(this.unitId));
                if (! u || Number(u.floor_id) !== Number(this.floorId)) {
                    this.unitId = '';
                }
            },
            get isCash() { return this.paymentMethod === 'cash'; },
            get basePrice() { return (Number(this.area) * Number(this.pricePerMeter)) + Number(this.gardenPrice) + Number(this.roofPrice); },
            get excellenceAmount() { return this.basePrice * Number(this.excellencePercent) / 100; },
            get basePriceWithExcellence() { return this.basePrice + this.excellenceAmount; },
            get discountPercent() {
                // Excel model: every point above the 10% down-payment baseline
                // earns a 30% discount point; cash = 100% down → 27%.
                const downPct = this.isCash ? 100 : (
                    Number(this.downPayment) > 0 && this.basePriceWithExcellence > 0
                        ? Number(this.downPayment) / this.basePriceWithExcellence * 100
                        : Number(this.downPaymentPercent)
                );
                return Math.max(0, downPct - 10) * 0.30;
            },
            get discountAmount() { return this.basePriceWithExcellence * this.discountPercent / 100; },
            get priceAfterTermDiscount() { return Math.max(0, this.basePriceWithExcellence - this.discountAmount); },
            get downPaymentBonusPercent() { return this.discountPercent; },
            get downPaymentDiscount() { return this.discountAmount; },
            get finalPrice() { return this.priceAfterTermDiscount; },
            get maintenanceAmount() { return this.finalPrice * Number(this.maintenancePercent) / 100; },
            get monthsPerInstallment() { return this.installmentType === 'monthly' ? 1 : (this.installmentType === 'quarterly' ? 3 : 6); },
            get installmentCount() { return this.isCash ? 0 : Math.max(1, Math.round(Number(this.installmentYears) * 12 / this.monthsPerInstallment)); },
            get computedDownPayment() { return this.isCash ? this.finalPrice : (Number(this.downPayment) > 0 ? Number(this.downPayment) : (this.basePriceWithExcellence * Number(this.downPaymentPercent) / 100)); },
            get remaining() { return Math.max(0, this.finalPrice - this.computedDownPayment); },
            get installmentAmount() { return Number(this.installmentCount) > 0 ? (this.remaining / Number(this.installmentCount)) : 0; },
            money(value) { return new Intl.NumberFormat(undefined, { maximumFractionDigits: 0, minimumFractionDigits: 0 }).format(value); },
            pct(value) { return new Intl.NumberFormat(undefined, { maximumFractionDigits: 1, minimumFractionDigits: 0 }).format(Number(value)) + '%'; },
        }"
    >
        @if ($errors->any())
            <div class="animate-fade-up rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Hero Display --}}
        <section class="animate-fade-up dashboard-hero-card p-6 sm:p-8">
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-brand-500/20 blur-3xl"></div>
            
            <div class="relative grid gap-6 lg:grid-cols-[1fr_auto] lg:items-center">
                <div>
                    <p class="mobile-section-title">{{ __('Financial Tool') }}</p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('Installment Calculator') }}</h1>
                    <p class="mt-3 max-w-xl text-sm leading-6 text-slate-300">
                        {{ __('Generate precise payment plans, adjust down payments, and explore financing options in real-time.') }}
                    </p>
                </div>
            </div>

            @if ($preselectedUnit)
                <div class="mt-6 flex items-center gap-3 rounded-2xl border border-brand-500/20 bg-brand-500/10 px-4 py-3 text-sm text-brand-200">
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16" stroke-width="1.8"/><path d="M9 7h2M13 7h2M9 11h2M13 11h2M9 15h2M13 15h2" stroke-width="1.8" stroke-linecap="round"/></svg>
                    <span>{{ __('Unit :unit in :project selected.', ['unit' => $preselectedUnit->unit_number, 'project' => $preselectedUnit->project?->name]) }}</span>
                </div>
            @endif
        </section>

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Form Column --}}
            <form method="POST" action="{{ route($calculatorRoutes['calculate']) }}" class="lg:col-span-2 space-y-6"
                  @submit="if (selectedUnit && ! unitAvailable) { $event.preventDefault(); alert('{{ __('This unit is not available for sale.') }}'); }">
                @csrf

                <section class="app-card space-y-6">
                    <div class="flex items-center gap-3 border-b border-white/5 pb-4">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-500/15 text-brand-400">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 2H2v10h10V2zM22 12H12v10h10V12zM12 12H2v10h10V12zM22 2H12v10h10V2z" stroke-width="2"/></svg>
                        </span>
                        <h2 class="text-lg font-semibold text-white">{{ __('1. Unit Selection') }}</h2>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-slate-300">{{ __('Project') }}</span>
                            <select class="app-select" x-model="projectId" name="project_id" @change="onProjectChange()">
                                <option value="">{{ __('Choose a project') }}</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-slate-300">{{ __('Building') }}</span>
                            <select x-ref="buildingSelect" class="app-select" x-model="buildingId" name="building_id" @change="onBuildingChange()">
                                <option value="">{{ __('Choose a building') }}</option>
                                <template x-for="building in filteredBuildings" :key="building.id">
                                    <option :value="building.id" x-text="building.code ? building.name + ' · ' + building.code : building.name"></option>
                                </template>
                            </select>
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-slate-300">{{ __('Floor') }}</span>
                            <select x-ref="floorSelect" class="app-select" x-model="floorId" name="floor_id" @change="onFloorChange()">
                                <option value="">{{ __('Choose a floor') }}</option>
                                <template x-for="floor in filteredFloors" :key="floor.id">
                                    <option :value="floor.id" x-text="floor.building_name ? floor.name + ' · ' + floor.building_name : floor.name"></option>
                                </template>
                            </select>
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-slate-300">{{ __('Unit') }}</span>
                            <select x-ref="unitSelect" class="app-select" x-model="unitId" name="unit_id" @change="selectUnit()">
                                <option value="">{{ __('Choose a unit') }}</option>
                                <template x-for="u in filteredUnits" :key="u.id">
                                    <option :value="u.id" x-text="u.unit_number + ' · ' + (u.floor_name || u.building_name || u.project_name)"></option>
                                </template>
                            </select>
                        </label>
                    </div>

                    <div x-show="selectedUnit && ! unitAvailable" x-cloak class="flex items-start gap-3 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                        <svg class="mt-0.5 h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="10" stroke-width="1.8"/><path d="M12 8v4M12 16h.01" stroke-width="1.8" stroke-linecap="round"/></svg>
                        <span x-text="unitUnavailableMessage"></span>
                    </div>
                </section>

                <section class="app-card space-y-6">
                    <div class="flex items-center gap-3 border-b border-white/5 pb-4">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-500/15 text-violet-400">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="2"/><path d="M14.5 9a3.5 3.5 0 0 0-5 0M14.5 15a3.5 3.5 0 0 1-5 0M12 7v10" stroke-width="2"/></svg>
                        </span>
                        <h2 class="text-lg font-semibold text-white">{{ __('2. Core Pricing') }}</h2>
                    </div>

                    <div x-show="isDashboard || selectedUnit" x-cloak>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <label class="space-y-2"><span class="text-sm font-medium text-slate-300">{{ __('Area (m²)') }}</span><input class="app-input" type="number" step="0.01" x-model="area" name="area" required :readonly="!isDashboard"></label>
                            <label class="space-y-2"><span class="text-sm font-medium text-slate-300">{{ __('Price per m²') }}</span><input class="app-input" type="number" step="0.01" x-model="pricePerMeter" name="price_per_meter" required :readonly="!isDashboard"></label>
                            <label class="space-y-2"><span class="text-sm font-medium text-slate-300">{{ __('Roof Area (m²)') }}</span><input class="app-input" type="number" step="0.01" x-model="roofArea" name="roof_area" :readonly="!isDashboard"></label>
                            <template x-if="isDashboard || gardenPrice > 0">
                                <label class="space-y-2"><span class="text-sm font-medium text-slate-300">{{ __('Garden Price') }}</span><input class="app-input" type="number" step="0.01" x-model="gardenPrice" name="garden_price" :readonly="!isDashboard"></label>
                            </template>
                            <template x-if="isDashboard || roofPrice > 0">
                                <label class="space-y-2"><span class="text-sm font-medium text-slate-300">{{ __('Roof Price') }}</span><input class="app-input" type="number" step="0.01" x-model="roofPrice" name="roof_price" :readonly="!isDashboard"></label>
                            </template>
                            <label class="space-y-2"><span class="text-sm font-medium text-slate-300">{{ __('Excellence %') }}</span><input class="app-input" type="number" step="0.1" min="0" max="100" x-model="excellencePercent" name="excellence_percent" :readonly="!isDashboard"></label>
                        </div>
                        <p x-show="!isDashboard" class="mt-3 text-[11px] leading-relaxed text-slate-500">{{ __('Pricing values are taken from the unit data and cannot be changed here.') }}</p>
                    </div>
                    <p x-show="!isDashboard && !selectedUnit" x-cloak class="rounded-xl border border-dashed border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-400">
                        {{ __('Choose a unit to view its pricing details.') }}
                    </p>
                </section>

                <section class="app-card space-y-6">
                    <div class="flex items-center gap-3 border-b border-white/5 pb-4">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500/15 text-amber-400">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-width="2"/></svg>
                        </span>
                        <h2 class="text-lg font-semibold text-white">{{ __('3. Payment Plan') }}</h2>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-2 sm:col-span-2">
                            <span class="text-sm font-medium text-slate-300">{{ __('Payment Method') }}</span>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 transition hover:bg-white/10" :class="paymentMethod === 'installments' ? 'border-brand-500/50 bg-brand-500/10' : ''">
                                    <input type="radio" name="payment_method" value="installments" x-model="paymentMethod" class="h-4 w-4 text-brand-600 focus:ring-brand-500/20">
                                    <span class="text-sm font-semibold text-white">{{ __('Installments') }}</span>
                                </label>
                                <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 transition hover:bg-white/10" :class="paymentMethod === 'cash' ? 'border-emerald-500/50 bg-emerald-500/10' : ''">
                                    <input type="radio" name="payment_method" value="cash" x-model="paymentMethod" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500/20">
                                    <span class="text-sm font-semibold text-white">{{ __('Cash') }}</span>
                                </label>
                            </div>
                        </div>

                        <template x-if="!isCash">
                            <label class="space-y-2">
                                <span class="text-sm font-medium text-slate-300">{{ __('Installment Template') }}</span>
                                <select class="app-select" x-model="templateId" name="installment_template_id">
                                    <option value="">{{ __('Custom plan') }}</option>
                                    @foreach ($templates as $planTemplate)
                                        <option value="{{ $planTemplate->id }}">{{ __($planTemplate->name) }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </template>
                        <label class="space-y-2" x-show="!isCash">
                            <span class="text-sm font-medium text-slate-300">{{ __('Frequency') }}</span>
                            <select class="app-select" x-model="installmentType" name="installment_type">
                                <option value="monthly">{{ __('Monthly') }}</option>
                                <option value="quarterly">{{ __('Quarterly') }}</option>
                            </select>
                        </label>
                        <label class="space-y-2" x-show="!isCash">
                            <span class="text-sm font-medium text-slate-300">{{ __('Down Payment Amount') }}</span>
                            <input class="app-input" type="number" step="0.01" min="0" x-model="downPayment" name="down_payment" placeholder="0"
                                   @input="downPaymentPercent = finalPrice > 0 ? Math.round(Number(downPayment) / finalPrice * 100 * 10) / 10 : 0">
                        </label>
                        <label class="space-y-2" x-show="!isCash">
                            <span class="text-sm font-medium text-slate-300">{{ __('Down Payment %') }} <span class="text-xs text-slate-500" x-show="downPayment > 0" x-text="'(' + downPaymentPercent + '%)'"></span></span>
                            <input class="app-input" type="number" step="0.1" min="0" max="100" x-model="downPaymentPercent" name="down_payment_percent"
                                   @input="downPayment = Math.round(finalPrice * Number(downPaymentPercent) / 100)">
                            <p class="text-[11px] text-slate-500" x-show="downPayment > 0">{{ __('Calculated automatically from the amount.') }}</p>
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-slate-300">{{ __('Maintenance %') }}</span>
                            <input class="app-input" type="number" step="0.1" min="0" max="100" x-model="maintenancePercent" name="maintenance_percent">
                        </label>
                        <label class="space-y-2" x-show="!isCash">
                            <span class="text-sm font-medium text-slate-300">{{ __('Installment years') }}</span>
                            <input class="app-input" type="number" step="0.5" min="1" x-model="installmentYears" name="installment_years">
                            <input type="hidden" name="installment_count" :value="installmentCount">
                            <p class="text-xs text-slate-400">
                                {{ __('Number of Installments') }}: <span x-text="installmentCount"></span>
                            </p>
                        </label>
                        <label class="space-y-2" x-show="!isCash">
                            <span class="text-sm font-medium text-slate-300">{{ __('First Installment Date') }}</span>
                            <input class="app-input" type="date" x-model="firstInstallmentDate" name="first_installment_date" required>
                        </label>
                    </div>
                </section>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <button type="submit" class="app-button flex-1 py-4 text-lg"
                            :class="selectedUnit && ! unitAvailable ? 'opacity-50 cursor-not-allowed' : ''"
                            :disabled="selectedUnit && ! unitAvailable">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-width="2"/><path d="M14 2v6h6" stroke-width="2"/><path d="M16 13H8M16 17H8M10 9H8" stroke-width="2"/></svg>
                        {{ __('Calculate & Preview Schedule') }}
                    </button>
                    @auth
                        <a href="{{ route('dashboard.home') }}" class="app-button--ghost py-4">{{ __('Back to Dashboard') }}</a>
                    @endauth
                </div>
            </form>

            {{-- Summary Column --}}
            <aside class="space-y-6">
                <section class="app-card sticky top-24 space-y-6 border-brand-500/20 shadow-2xl shadow-brand-500/5">
                    <h2 class="text-lg font-bold text-white">{{ __('Real-time Summary') }}</h2>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between border-b border-white/5 pb-3">
                            <span class="text-sm text-slate-400">{{ __('Unit Price') }}</span>
                            <span class="font-semibold text-white" x-text="money(basePrice)"></span>
                        </div>
                        {{-- For the customer, hide the Excellence row when the unit has no excellence % --}}
                        <template x-if="isDashboard || excellencePercent > 0">
                            <div class="flex justify-between border-b border-white/5 pb-3">
                                <span class="text-sm text-slate-400">{{ __('Excellence') }}</span>
                                <span class="font-semibold text-brand-400" x-text="'+' + money(excellenceAmount)"></span>
                            </div>
                        </template>
                        <div class="flex justify-between border-b border-white/5 pb-3" x-show="discountPercent > 0" x-cloak>
                            <span class="text-sm text-slate-400">{{ __('Discount') }} <span class="text-xs text-emerald-400/70" x-text="'(' + pct(discountPercent) + ')'"></span></span>
                            <span class="font-semibold text-emerald-400" x-text="'-' + money(discountAmount)"></span>
                        </div>
                        <div class="flex justify-between border-b border-white/5 pb-3">
                            <span class="text-sm text-slate-400">{{ __('Net Total') }}</span>
                            <span class="text-xl font-bold text-emerald-400" x-text="money(finalPrice)"></span>
                        </div>
                        <div class="flex justify-between border-b border-white/5 pb-3">
                            <span class="text-sm text-slate-400">{{ __('Maintenance') }}</span>
                            <span class="font-semibold text-amber-400" x-text="'+' + money(maintenanceAmount)"></span>
                        </div>
                        <p class="text-[11px] leading-relaxed text-amber-200/70">
                            {{ __('Maintenance amount is paid during the contract period before delivery.') }}
                        </p>
                        <template x-if="isCash">
                            <div class="bg-emerald-500/10 rounded-2xl p-4 space-y-3 mt-4 border border-emerald-500/30">
                                <div class="text-xs font-bold uppercase tracking-wider text-emerald-400">{{ __('Cash Payment') }}</div>
                                <p class="text-2xl font-black text-emerald-400" x-text="money(finalPrice)"></p>
                            </div>
                        </template>
                        <template x-if="!isCash">
                            <div class="bg-white/5 rounded-2xl p-4 space-y-3 mt-4">
                                <div class="flex justify-between text-xs font-bold uppercase tracking-wider text-slate-500">
                                    <span>{{ __('Down Payment') }}</span>
                                    <span class="badge badge-success" x-text="downPaymentPercent + '%'"></span>
                                </div>
                                <p class="text-2xl font-black text-emerald-400" x-text="money(computedDownPayment)"></p>
                            </div>
                        </template>
                        <template x-if="!isCash">
                            <div class="bg-brand-500/10 rounded-2xl p-4 space-y-3">
                                <div class="flex justify-between text-xs font-bold uppercase tracking-wider text-brand-400">
                                    <span x-text="installmentType + ' ' + installmentLabel"></span>
                                    <span x-text="installmentCount + 'x'"></span>
                                </div>
                                <p class="text-2xl font-black text-white" x-text="money(installmentAmount)"></p>
                            </div>
                        </template>
                    </div>

                    <div class="rounded-xl bg-amber-500/10 p-3 text-[11px] leading-relaxed text-amber-200/70 border border-amber-500/20">
                        <svg class="h-3.5 w-3.5 inline mb-0.5 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke-width="2"/></svg>
                        {{ __('Calculations are approximate and subject to terms & conditions.') }}
                    </div>
                </section>
            </aside>
        </div>
    </div>
@endsection
