@extends('layouts.dashboard')

@section('content')
    @if (session('status'))
        <div x-data="{ show: true }" x-show="show" x-transition class="flex items-center gap-3 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m5 12 5 5L20 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>{{ session('status') }}</span>
            <button type="button" @click="show = false" class="ms-auto text-emerald-400 hover:text-emerald-200">×</button>
        </div>
    @endif

    <style id="project-print-report-style">
        /* ===== Print Report — Iframe-only rendering ===== */
        #project-print-report { display: none; }
        @media print {
            @page { size: A4 landscape; margin: 8mm; }
            @page portrait { size: A4 portrait; margin: 8mm; }
            @page landscape { size: A4 landscape; margin: 8mm; }

            * { margin: 0 !important; padding: 0 !important; box-shadow: none !important; }

            html, body { background: #fff !important; color: #111 !important; font-family: 'Tahoma', 'Arial', sans-serif !important; }

            #project-print-report {
                display: block !important;
                width: 100% !important;
                background: #fff !important;
                color: #111 !important;
                font-family: 'Tahoma', 'Arial', sans-serif !important;
                font-size: 7px !important;
                line-height: 1.2 !important;
            }
            #project-print-report * { visibility: visible !important; }
            #project-print-report.portrait { page: portrait; }
            #project-print-report.landscape { page: landscape; }

            /* ===== Header ===== */
            .print-report-header {
                display: flex !important;
                justify-content: space-between !important;
                align-items: flex-start !important;
                border-bottom: 2px solid #0f172a !important;
                padding-bottom: 4px !important;
                margin-bottom: 5px !important;
            }
            .print-report-kicker { font-size: 6px !important; font-weight: 700; letter-spacing: 0.5px; color: #0f766e !important; text-transform: uppercase; }
            .print-report-header h1 { font-size: 13px !important; color: #0f172a !important; font-weight: 800; margin: 1px 0 !important; }
            .print-report-header p { font-size: 6px !important; color: #64748b !important; }
            .print-report-meta { text-align: end; font-size: 6px !important; color: #334155 !important; }
            .print-report-meta strong { font-size: 8px !important; display: block; }

            /* ===== Summary KPIs ===== */
            .print-report-summary {
                display: grid !important;
                grid-template-columns: repeat(5, 1fr) !important;
                gap: 3px !important;
                margin-bottom: 5px !important;
            }
            .print-report-summary div {
                border: 1px solid #cbd5e1 !important;
                border-radius: 4px !important;
                padding: 3px !important;
                background: #f8fafc !important;
                text-align: center !important;
            }
            .print-report-summary span { display: block; font-size: 5px !important; color: #64748b !important; }
            .print-report-summary strong { display: block; font-size: 10px !important; color: #0f172a !important; margin-top: 1px !important; }
            .print-report-summary .available { color: #047857 !important; }
            .print-report-summary .reserved { color: #b45309 !important; }
            .print-report-summary .sold { color: #be123c !important; }

            /* ===== Buildings Grid ===== */
            .print-buildings-grid {
                display: grid !important;
                grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
                gap: 4px !important;
                align-items: start !important;
            }
            #project-print-report.portrait .print-buildings-grid { grid-template-columns: repeat(4, minmax(0, 1fr)) !important; }

            .print-building {
                border: 1px solid #94a3b8 !important;
                border-radius: 5px !important;
                overflow: hidden !important;
                break-inside: avoid !important;
                page-break-inside: avoid !important;
            }
            .print-building-heading {
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                gap: 4px !important;
                padding: 3px 5px !important;
                background: #e2e8f0 !important;
                color: #0f172a !important;
                font-size: 7px !important;
                border-bottom: 1px solid #cbd5e1 !important;
            }
            .print-building-title { display: flex !important; gap: 4px !important; align-items: center !important; }
            .print-building-title > div { display: flex !important; gap: 3px !important; align-items: center !important; }
            .print-building-title strong { font-size: 7px !important; }
            .print-building-title span:last-child { color: #64748b !important; font-size: 6px !important; }
            .print-building-stats { display: flex !important; gap: 4px !important; align-items: center !important; font-size: 6px !important; color: #64748b !important; }
            .print-building-stats b { color: #047857 !important; font-size: 6px !important; }
            .print-building-mark {
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                min-width: 15px !important;
                height: 15px !important;
                border-radius: 4px !important;
                background: #0f172a !important;
                color: #fff !important;
                font-weight: 800 !important;
                font-size: 7px !important;
            }

            /* ===== Floors ===== */
            .print-floors { display: flex !important; flex-direction: column !important; gap: 2px !important; padding: 3px !important; background: #f8fafc !important; }
            .print-floor {
                border: 1px solid #e2e8f0 !important;
                border-radius: 4px !important;
                overflow: hidden !important;
                background: #fff !important;
                break-inside: avoid !important;
                page-break-inside: avoid !important;
            }
            .print-floor-heading {
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                gap: 4px !important;
                padding: 2px 4px !important;
                background: #f1f5f9 !important;
                border-bottom: 1px solid #e2e8f0 !important;
                color: #334155 !important;
                font-size: 6px !important;
            }
            .print-floor-label { display: flex !important; align-items: center !important; gap: 3px !important; }
            .print-floor-label strong { font-size: 6px !important; }
            .print-floor-number {
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                width: 12px !important;
                height: 12px !important;
                border-radius: 3px !important;
                background: #dbeafe !important;
                color: #1d4ed8 !important;
                font-weight: 800 !important;
                font-size: 6px !important;
            }
            .print-floor-count { color: #64748b !important; font-size: 5px !important; }

            /* ===== Units Grid ===== */
            .print-units-grid {
                display: grid !important;
                grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
                gap: 2px !important;
                padding: 2px !important;
            }
            #project-print-report.portrait .print-units-grid { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; }

            .print-unit {
                min-width: 0 !important;
                border: 1px solid #cbd5e1 !important;
                border-top: 2px solid #10b981 !important;
                border-radius: 3px !important;
                padding: 2px !important;
                background: #ecfdf5 !important;
                color: #1e293b !important;
                break-inside: avoid !important;
                page-break-inside: avoid !important;
            }
            .print-unit.sold { border-top-color: #e11d48 !important; background: #fff1f2 !important; }
            .print-unit.reserved { border-top-color: #f59e0b !important; background: #fffbeb !important; }
            .print-unit.hidden { border-top-color: #64748b !important; background: #f1f5f9 !important; }

            .print-unit-top { display: flex !important; justify-content: space-between !important; align-items: center !important; font-size: 6px !important; }
            .print-unit-top strong { font-size: 6px !important; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .print-status-dot { width: 3px !important; height: 3px !important; border-radius: 50%; background: #10b981; flex: 0 0 auto; }
            .print-unit.sold .print-status-dot { background: #e11d48; }
            .print-unit.reserved .print-status-dot { background: #f59e0b; }
            .print-unit.hidden .print-status-dot { background: #64748b; }
            .print-unit-type { margin-top: 1px !important; font-size: 5px !important; color: #475569 !important; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .print-unit-details { display: flex !important; justify-content: space-between !important; margin-top: 1px !important; font-size: 4.5px !important; color: #64748b !important; }
            .print-unit-bottom { display: flex !important; justify-content: space-between !important; align-items: center !important; margin-top: 1px !important; padding-top: 1px !important; border-top: 1px solid #dbe4ea; }
            .print-unit-bottom b { font-size: 5px !important; overflow: hidden; white-space: nowrap; direction: ltr; unicode-bidi: embed; }
            .print-status { display: inline-block !important; border-radius: 8px !important; padding: 1px 2px !important; font-size: 4px !important; font-weight: 700; }
            .print-status.available { color: #047857; background: #d1fae5; }
            .print-status.reserved { color: #b45309; background: #fef3c7; }
            .print-status.sold { color: #be123c; background: #ffe4e6; }
            .print-status.hidden { color: #475569; background: #e2e8f0; }
            .print-empty-floor { grid-column: 1 / -1; padding: 4px; text-align: center; color: #64748b; font-size: 6px; }

            /* ===== Footer ===== */
            .print-report-footer {
                display: flex !important;
                justify-content: space-between !important;
                border-top: 1px solid #cbd5e1 !important;
                margin-top: 4px !important;
                padding-top: 2px !important;
                color: #64748b !important;
                font-size: 5px !important;
            }
        }
    </style>

    <script id="project-layout-data" type="application/json">{!! json_encode($layoutData, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
    <script id="project-all-projects-data" type="application/json">{!! json_encode($allProjects, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
    <script id="project-refresh-url" type="application/json">{!! json_encode(route('dashboard.projects.layout-data', $project)) !!}</script>

    <div
        class="space-y-6"
        x-data="{
            buildings: JSON.parse(document.getElementById('project-layout-data').textContent),
            allProjects: JSON.parse(document.getElementById('project-all-projects-data').textContent),
            refreshUrl: JSON.parse(document.getElementById('project-refresh-url').textContent),
            currentProjectId: {{ $project->id }},
            defaultDownPaymentPercent: {{ $defaultDownPaymentPercent }},
            selectedBuildingId: null,
            selectedStatus: 'all',
            selectedType: 'all',
            searchQuery: '',
            viewMode: 'elevation',
            selectedUnit: null,
            focusedBuildingId: null,
            focusedFloorId: null,
            focusedFloorLayoutMode: 'elevation',
            zoomFloorPlan: null,
            printDialogOpen: false,
            printOrientation: 'landscape',
            autoRefresh: true,
            lastUpdatedAt: new Date().toLocaleTimeString('ar-EG'),
            refreshTimer: null,
            canChangeStatus: {{ auth()->user()?->hasAnyRole(['Administrator', 'Owner']) ? 'true' : 'false' }},

            statusLabels: {
                available: @js(__('Available')),
                reserved: @js(__('Reserved')),
                sold: @js(__('Sold')),
                hidden: @js(__('Hidden')),
            },

            init() {
                if (this.buildings.length > 0) {
                    this.focusedBuildingId = this.buildings[0].id;
                }
                this.startAutoRefresh();
            },

            startAutoRefresh() {
                if (this.refreshTimer) clearInterval(this.refreshTimer);
                this.refreshTimer = setInterval(() => {
                    if (this.autoRefresh) this.refreshData();
                }, 5000);
            },

            stopAutoRefresh() {
                if (this.refreshTimer) {
                    clearInterval(this.refreshTimer);
                    this.refreshTimer = null;
                }
            },

            async refreshData() {
                try {
                    const response = await fetch(this.refreshUrl, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (! response.ok) throw new Error('Refresh failed');
                    const data = await response.json();
                    if (data.buildings) {
                        this.buildings = data.buildings;
                        this.lastUpdatedAt = new Date().toLocaleTimeString('ar-EG');
                    }
                } catch (e) {
                    console.error('[layout] Refresh error:', e);
                }
            },

            // Computed Units
            get allUnits() {
                const list = [];
                this.buildings.forEach(b => {
                    b.floors.forEach(f => {
                        f.units.forEach(u => {
                            list.push(u);
                        });
                    });
                });
                return list;
            },

            get totalUnits() {
                return this.allUnits.length;
            },
            get availableUnits() {
                return this.allUnits.filter(u => u.status === 'available');
            },
            get reservedUnits() {
                return this.allUnits.filter(u => u.status === 'reserved');
            },
            get soldUnits() {
                return this.allUnits.filter(u => u.status === 'sold');
            },
            get hiddenUnits() {
                return this.allUnits.filter(u => u.status === 'hidden');
            },

            get totalValue() {
                return this.allUnits.reduce((sum, u) => sum + (u.price || 0), 0);
            },
            get availableValue() {
                return this.availableUnits.reduce((sum, u) => sum + (u.price || 0), 0);
            },
            get soldValue() {
                return this.soldUnits.reduce((sum, u) => sum + (u.price || 0), 0);
            },
            get reservedValue() {
                return this.reservedUnits.reduce((sum, u) => sum + (u.price || 0), 0);
            },

            get availablePercent() {
                return this.totalUnits > 0 ? Math.round((this.availableUnits.length / this.totalUnits) * 100) : 0;
            },
            get soldPercent() {
                return this.totalUnits > 0 ? Math.round((this.soldUnits.length / this.totalUnits) * 100) : 0;
            },
            get reservedPercent() {
                return this.totalUnits > 0 ? Math.round((this.reservedUnits.length / this.totalUnits) * 100) : 0;
            },

            get unitTypes() {
                const types = new Set();
                this.allUnits.forEach(u => {
                    if (u.type && u.type.trim()) types.add(u.type.trim());
                });
                return Array.from(types).sort();
            },

            // Filtering Helpers
            isUnitMatching(unit) {
                if (this.selectedStatus !== 'all' && unit.status !== this.selectedStatus) return false;
                if (this.selectedType !== 'all' && unit.type !== this.selectedType) return false;
                if (this.selectedBuildingId !== null && unit.building_id !== this.selectedBuildingId) return false;
                if (this.searchQuery.trim() !== '') {
                    const q = this.searchQuery.trim().toLowerCase();
                    const numMatch = (unit.number || '').toLowerCase().includes(q);
                    const typeMatch = (unit.type || '').toLowerCase().includes(q);
                    const bldMatch = (unit.building_name || '').toLowerCase().includes(q);
                    const floorMatch = (unit.floor_name || '').toLowerCase().includes(q);
                    if (!numMatch && !typeMatch && !bldMatch && !floorMatch) return false;
                }
                return true;
            },

            get filteredUnits() {
                return this.allUnits.filter(u => this.isUnitMatching(u));
            },

            get filteredBuildings() {
                if (this.selectedBuildingId !== null) {
                    return this.buildings.filter(b => b.id === this.selectedBuildingId);
                }
                return this.buildings;
            },

            get focusedBuilding() {
                return this.buildings.find(b => b.id === this.focusedBuildingId) || this.buildings[0] || null;
            },

            get focusedBuildingFloors() {
                if (!this.focusedBuilding) return [];
                if (this.focusedFloorId !== null) {
                    return this.focusedBuilding.floors.filter(f => f.id === this.focusedFloorId);
                }
                return this.focusedBuilding.floors;
            },

            get focusedFloor() {
                if (!this.focusedBuilding || this.focusedFloorId === null) return null;
                return this.focusedBuilding.floors.find(f => f.id === this.focusedFloorId) || null;
            },

            // Actions
            selectUnit(unit) {
                this.selectedUnit = this.selectedUnit?.id === unit.id ? null : unit;
            },

            refreshBuildingStats(building) {
                const units = building.floors.flatMap(floor => floor.units);
                building.units_count = units.length;
                building.available_count = units.filter(unit => unit.status === 'available').length;
                building.reserved_count = units.filter(unit => unit.status === 'reserved').length;
                building.sold_count = units.filter(unit => unit.status === 'sold').length;
            },

            async submitStatusForm(event, scope, target) {
                if (!window.confirm('{{ __('Are you sure?') }}')) return;

                const form = event.currentTarget;
                const submitButton = form.querySelector('button[type=&quot;submit&quot;]');
                const status = form.elements.status.value;
                const hidden = status === 'sold' ? false : Boolean(form.elements.hidden_from_website?.checked);

                if (submitButton) submitButton.disabled = true;

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        credentials: 'same-origin',
                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
                        },
                    });

                    if (!response.ok) {
                        let msg = '{{ __('Failed to update status.') }}';
                        try {
                            const data = await response.json();
                            msg = data.message || msg;
                        } catch (_) {}
                        throw new Error(msg);
                    }

                    if (scope === 'building') {
                        target.floors.forEach(floor => floor.units.forEach(unit => this.applyUnitStatus(unit, status, hidden)));
                        this.refreshBuildingStats(target);
                    } else if (scope === 'floor') {
                        target.units.forEach(unit => this.applyUnitStatus(unit, status, hidden));
                        const building = this.buildings.find(item => Number(item.id) === Number(target.building_id));
                        if (building) this.refreshBuildingStats(building);
                    } else {
                        this.applyUnitStatus(target, status, hidden);
                        const building = this.buildings.find(item => Number(item.id) === Number(target.building_id));
                        if (building) this.refreshBuildingStats(building);
                    }
                } catch (error) {
                    window.alert(error.message || '{{ __('Failed to update status.') }}');
                } finally {
                    if (submitButton) submitButton.disabled = false;
                }
            },

            applyUnitStatus(unit, status, hidden) {
                unit.status = status;
                unit.hidden_from_website = hidden;
            },

            filterByStatus(status) {
                this.selectedStatus = this.selectedStatus === status ? 'all' : status;
            },

            filterByBuilding(buildingId) {
                if (this.selectedBuildingId === buildingId) {
                    this.selectedBuildingId = null;
                } else {
                    this.selectedBuildingId = buildingId;
                    if (buildingId !== null) {
                        this.focusedBuildingId = buildingId;
                    }
                }
            },
            openBuilding2D(buildingId) {
                this.focusedBuildingId = buildingId;
                this.selectedBuildingId = buildingId;
                this.viewMode = 'focused';
            },

            money(value) {
                return new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 }).format(value || 0);
            },
            moneyShort(value) {
                if (!value) return '0';
                if (value >= 1000000) return (value / 1000000).toFixed(2) + ' م';
                if (value >= 1000) return (value / 1000).toFixed(0) + ' ألف';
                return value.toString();
            },
            switchProject(event) {
                const targetId = event.target.value;
                if (targetId && targetId != this.currentProjectId) {
                    window.location.replace('/real-statement-control/projects/' + targetId + '/layout');
                }
            },
            printSummary() {
                this.printDialogOpen = true;
            },
            printReport() {
                this.printDialogOpen = false;
                const report = document.getElementById('project-print-report');
                if (report) {
                    window.printProjectReport(report, this.printOrientation);
                }
            }
        }"
    >
        {{-- 1. Master Header Section --}}
        <section class="dashboard-hero-card relative overflow-hidden p-6 sm:p-8">
            <div class="pointer-events-none absolute -end-24 -top-24 h-72 w-72 rounded-full bg-brand-500/20 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-24 -start-24 h-72 w-72 rounded-full bg-emerald-500/15 blur-3xl"></div>

            <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div class="mb-2.5 flex flex-wrap items-center gap-2">
                        <span class="badge badge-brand">{{ __('Site Plan') }}</span>
                        <span class="badge badge-success">{{ __('Desktop Workspace') }}</span>
                        @if ($project->code)
                            <span class="badge badge-muted">{{ $project->code }}</span>
                        @endif
                    </div>
                    <div class="sr-only">
                        @foreach ($project->buildings as $bld)
                            <span>{{ $bld->name }}</span>
                        @endforeach
                    </div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-black tracking-tight text-white sm:text-4xl">{{ $project->name }}</h1>
                        {{-- Project Switcher --}}
                        <div class="relative">
                            <select
                                @change="switchProject($event)"
                                class="rounded-xl border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-bold text-slate-200 backdrop-blur-md transition hover:bg-white/15 focus:border-brand-400 focus:outline-none"
                            >
                                <template x-for="p in allProjects" :key="p.id">
                                    <option :value="p.id" :selected="p.id === currentProjectId" x-text="p.name" class="bg-slate-900 text-white"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <p class="mt-2 text-sm text-slate-300">
                        {{ __('مخطط الحصر الشامل والواجهات المعمارية للمشروع: رؤية فورية لجميع العمارات والأدوار والشقق المتاحة والمباعة.') }}
                    </p>
                </div>

                {{-- Print Action --}}
                <div class="flex flex-wrap items-center gap-2.5">
                    <div class="flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-2.5 py-1.5">
                        <button
                            type="button"
                            @click="autoRefresh = !autoRefresh; autoRefresh ? startAutoRefresh() : stopAutoRefresh()"
                            class="inline-flex items-center gap-1.5 text-xs font-bold transition"
                            :class="autoRefresh ? 'text-emerald-400' : 'text-slate-400'"
                            :title="autoRefresh ? '{{ __('إيقاف التحديث التلقائي') }}' : '{{ __('تفعيل التحديث التلقائي كل 5 ثوانٍ') }}'"
                        >
                            <span class="relative flex h-2 w-2">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full opacity-75" :class="autoRefresh ? 'bg-emerald-400' : 'bg-slate-500'"></span>
                                <span class="relative inline-flex h-2 w-2 rounded-full" :class="autoRefresh ? 'bg-emerald-500' : 'bg-slate-500'"></span>
                            </span>
                            <span x-text="autoRefresh ? '{{ __('تحديث لحظي') }}' : '{{ __('التحديث متوقف') }}'"></span>
                        </button>
                        <span class="text-[10px] text-slate-500" x-text="'{{ __('آخر تحديث:') }} ' + lastUpdatedAt"></span>
                        <button
                            type="button"
                            @click="refreshData()"
                            class="rounded-lg border border-white/10 bg-white/5 p-1.5 text-slate-300 hover:bg-white/10 hover:text-white"
                            title="{{ __('تحديث الآن') }}"
                        >
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" stroke-width="1.8" stroke-linecap="round"/><path d="M3 3v5h5" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </button>
                    </div>

                    <button
                        type="button"
                        @click="printSummary()"
                        class="app-button gap-2 text-xs"
                        title="{{ __('طباعة كشف حصر المشروع') }}"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" stroke-width="1.8" stroke-linecap="round"/><path d="M6 14h12v8H6z" stroke-width="1.8" stroke-linecap="round"/></svg>
                        <span>{{ __('طباعة الحصر') }}</span>
                    </button>
                </div>
            </div>
        </section>

        {{-- 2. Executive KPI Dashboard Bar --}}
        <section class="space-y-3">
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                {{-- Total Units --}}
                <div
                    @click="filterByStatus('all')"
                    class="app-card cursor-pointer p-4 transition duration-300 hover:scale-[1.02]"
                    :class="selectedStatus === 'all' ? 'border-brand-400/80 ring-2 ring-brand-400/30' : ''"
                >
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('إجمالي الوحدات') }}</p>
                        <span class="rounded-full bg-brand-500/15 p-1.5 text-brand-300">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="7" height="7" rx="1.5" stroke-width="1.8"/><rect x="14" y="3" width="7" height="7" rx="1.5" stroke-width="1.8"/><rect x="14" y="14" width="7" height="7" rx="1.5" stroke-width="1.8"/><rect x="3" y="14" width="7" height="7" rx="1.5" stroke-width="1.8"/></svg>
                        </span>
                    </div>
                    <div class="mt-2 flex items-baseline justify-between">
                        <p class="text-3xl font-black text-white" x-text="totalUnits"></p>
                        <p class="text-xs font-bold text-brand-300" x-text="buildings.length + ' {{ __('عمارة') }}'"></p>
                    </div>
                    <p class="mt-1 text-[11px] text-slate-400">
                        {{ __('قيمة المحفظة:') }} <strong class="text-slate-200" x-text="moneyShort(totalValue) + ' ج.م'"></strong>
                    </p>
                </div>

                {{-- Available Units --}}
                <div
                    @click="filterByStatus('available')"
                    class="app-card cursor-pointer p-4 transition duration-300 hover:scale-[1.02]"
                    :class="selectedStatus === 'available' ? 'border-emerald-400/80 bg-emerald-500/10 ring-2 ring-emerald-400/30' : ''"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                            </span>
                            <p class="text-xs font-bold uppercase tracking-wider text-emerald-300">{{ __('المتاح للبيع') }}</p>
                        </div>
                        <span class="rounded-full bg-emerald-500/20 px-2 py-0.5 text-[11px] font-extrabold text-emerald-300" x-text="availablePercent + '%'"></span>
                    </div>
                    <div class="mt-2 flex items-baseline justify-between">
                        <p class="text-3xl font-black text-emerald-400" x-text="availableUnits.length"></p>
                        <span class="text-xs text-slate-400" x-text="'/ ' + totalUnits + ' وحدة'"></span>
                    </div>
                    <p class="mt-1 text-[11px] text-slate-400">
                        {{ __('مخزون متاح:') }} <strong class="text-emerald-300" x-text="moneyShort(availableValue) + ' ج.م'"></strong>
                    </p>
                </div>

                {{-- Sold Units --}}
                <div
                    @click="filterByStatus('sold')"
                    class="app-card cursor-pointer p-4 transition duration-300 hover:scale-[1.02]"
                    :class="selectedStatus === 'sold' ? 'border-rose-400/80 bg-rose-500/10 ring-2 ring-rose-400/30' : ''"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5">
                            <span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span>
                            <p class="text-xs font-bold uppercase tracking-wider text-rose-300">{{ __('تم البيع') }}</p>
                        </div>
                        <span class="rounded-full bg-rose-500/20 px-2 py-0.5 text-[11px] font-extrabold text-rose-300" x-text="soldPercent + '%'"></span>
                    </div>
                    <div class="mt-2 flex items-baseline justify-between">
                        <p class="text-3xl font-black text-rose-400" x-text="soldUnits.length"></p>
                        <span class="text-xs text-slate-400" x-text="'/ ' + totalUnits + ' وحدة'"></span>
                    </div>
                    <p class="mt-1 text-[11px] text-slate-400">
                        {{ __('إجمالي المبيعات:') }} <strong class="text-rose-300" x-text="moneyShort(soldValue) + ' ج.م'"></strong>
                    </p>
                </div>

                {{-- Reserved Units --}}
                <div
                    @click="filterByStatus('reserved')"
                    class="app-card cursor-pointer p-4 transition duration-300 hover:scale-[1.02]"
                    :class="selectedStatus === 'reserved' ? 'border-amber-400/80 bg-amber-500/10 ring-2 ring-amber-400/30' : ''"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5">
                            <span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span>
                            <p class="text-xs font-bold uppercase tracking-wider text-amber-300">{{ __('محجوز مؤقتاً') }}</p>
                        </div>
                        <span class="rounded-full bg-amber-500/20 px-2 py-0.5 text-[11px] font-extrabold text-amber-300" x-text="reservedPercent + '%'"></span>
                    </div>
                    <div class="mt-2 flex items-baseline justify-between">
                        <p class="text-3xl font-black text-amber-400" x-text="reservedUnits.length"></p>
                        <span class="text-xs text-slate-400" x-text="'/ ' + totalUnits + ' وحدة'"></span>
                    </div>
                    <p class="mt-1 text-[11px] text-slate-400">
                        {{ __('قيمة المحجوز:') }} <strong class="text-amber-300" x-text="moneyShort(reservedValue) + ' ج.م'"></strong>
                    </p>
                </div>
            </div>

            {{-- Segmented Progress Bar --}}
            <div class="app-card p-3">
                <div class="flex items-center justify-between text-xs font-bold">
                    <span class="text-slate-300">{{ __('معدل إشغال ومبيعات المشروع') }}</span>
                    <div class="flex items-center gap-4 text-[11px]">
                        <span class="text-emerald-400">● {{ __('متاح:') }} <span x-text="availablePercent + '% (' + availableUnits.length + ')'"></span></span>
                        <span class="text-rose-400">● {{ __('مباع:') }} <span x-text="soldPercent + '% (' + soldUnits.length + ')'"></span></span>
                        <span class="text-amber-400">● {{ __('محجوز:') }} <span x-text="reservedPercent + '% (' + reservedUnits.length + ')'"></span></span>
                    </div>
                </div>
                <div class="mt-2 flex h-3 w-full overflow-hidden rounded-full bg-slate-800 ring-1 ring-white/10">
                    <div class="bg-gradient-to-r from-emerald-600 to-emerald-400 transition-all duration-500" :style="'width: ' + availablePercent + '%'" title="{{ __('متاح') }}"></div>
                    <div class="bg-gradient-to-r from-rose-600 to-rose-400 transition-all duration-500" :style="'width: ' + soldPercent + '%'" title="{{ __('مباع') }}"></div>
                    <div class="bg-gradient-to-r from-amber-600 to-amber-400 transition-all duration-500" :style="'width: ' + reservedPercent + '%'" title="{{ __('محجوز') }}"></div>
                </div>
            </div>
        </section>

        {{-- 3. Master Control & Filter Toolbar --}}
        <section class="app-card space-y-4 p-4 sm:p-5">
            <div class="flex flex-wrap items-center justify-between gap-4">
                {{-- View Modes Switcher --}}
                <div class="flex flex-wrap items-center gap-1.5 rounded-2xl border border-white/10 bg-slate-950/60 p-1">
                    <button
                        type="button"
                        @click="viewMode = 'elevation'"
                        class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-xs font-bold transition"
                        :class="viewMode === 'elevation' ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30' : 'text-slate-300 hover:bg-white/5 hover:text-white'"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 21V7l8-4 8 4v14M9 21v-4h6v4M8 7h.01M12 7h.01M16 7h.01M8 11h.01M12 11h.01M16 11h.01" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>{{ __('مخطط الواجهات (Elevation)') }}</span>
                    </button>

                    <button
                        type="button"
                        @click="viewMode = 'focused'"
                        class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-xs font-bold transition"
                        :class="viewMode === 'focused' ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30' : 'text-slate-300 hover:bg-white/5 hover:text-white'"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 21h18M3 7l9-4 9 4M5 21V7M19 21V7M9 21v-4h6v4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>{{ __('عمارة واحدة (مكبر)') }}</span>
                    </button>

                    <button
                        type="button"
                        @click="viewMode = 'compact'"
                        class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-xs font-bold transition"
                        :class="viewMode === 'compact' ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30' : 'text-slate-300 hover:bg-white/5 hover:text-white'"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="7" height="7" rx="1.5" stroke-width="1.8"/><rect x="14" y="3" width="7" height="7" rx="1.5" stroke-width="1.8"/><rect x="14" y="14" width="7" height="7" rx="1.5" stroke-width="1.8"/><rect x="3" y="14" width="7" height="7" rx="1.5" stroke-width="1.8"/></svg>
                        <span>{{ __('المصفوفة المدمجة (Compact)') }}</span>
                    </button>

                    <button
                        type="button"
                        @click="viewMode = 'table'"
                        class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-xs font-bold transition"
                        :class="viewMode === 'table' ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30' : 'text-slate-300 hover:bg-white/5 hover:text-white'"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 3h18v18H3zM3 9h18M3 15h18M9 3v18" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>{{ __('جدول الحصر (Table)') }}</span>
                    </button>
                </div>

                {{-- Status Legend & Filter --}}
                <div class="flex flex-wrap items-center gap-2">
                    <button
                        type="button"
                        @click="filterByStatus('all')"
                        class="inline-flex items-center gap-1.5 rounded-xl border px-3 py-1.5 text-xs font-semibold transition"
                        :class="selectedStatus === 'all' ? 'border-brand-500 bg-brand-500/20 text-white' : 'border-white/10 bg-white/5 text-slate-300 hover:bg-white/10'"
                    >
                        <span>{{ __('الكل') }} (<span x-text="totalUnits"></span>)</span>
                    </button>

                    <button
                        type="button"
                        @click="filterByStatus('available')"
                        class="inline-flex items-center gap-1.5 rounded-xl border px-3 py-1.5 text-xs font-semibold transition"
                        :class="selectedStatus === 'available' ? 'border-emerald-500 bg-emerald-500/25 text-emerald-200' : 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300 hover:bg-emerald-500/20'"
                    >
                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                        <span>{{ __('متاح') }} (<span x-text="availableUnits.length"></span>)</span>
                    </button>

                    <button
                        type="button"
                        @click="filterByStatus('sold')"
                        class="inline-flex items-center gap-1.5 rounded-xl border px-3 py-1.5 text-xs font-semibold transition"
                        :class="selectedStatus === 'sold' ? 'border-rose-500 bg-rose-500/25 text-rose-200' : 'border-rose-500/30 bg-rose-500/10 text-rose-300 hover:bg-rose-500/20'"
                    >
                        <span class="h-2 w-2 rounded-full bg-rose-400"></span>
                        <span>{{ __('مباع') }} (<span x-text="soldUnits.length"></span>)</span>
                    </button>

                    <button
                        type="button"
                        @click="filterByStatus('reserved')"
                        class="inline-flex items-center gap-1.5 rounded-xl border px-3 py-1.5 text-xs font-semibold transition"
                        :class="selectedStatus === 'reserved' ? 'border-amber-500 bg-amber-500/25 text-amber-200' : 'border-amber-500/30 bg-amber-500/10 text-amber-300 hover:bg-amber-500/20'"
                    >
                        <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                        <span>{{ __('محجوز') }} (<span x-text="reservedUnits.length"></span>)</span>
                    </button>
                </div>
            </div>

            {{-- Secondary Row: Building Selector Chips + Search + Type --}}
            <div class="flex flex-col gap-3 border-t border-white/10 pt-3 lg:flex-row lg:items-center lg:justify-between">
                {{-- Building Filter Chips --}}
                <div class="flex flex-wrap items-center gap-1.5 overflow-x-auto no-scrollbar">
                    <button
                        type="button"
                        @click="filterByBuilding(null)"
                        class="rounded-xl border px-3 py-1.5 text-xs font-bold transition"
                        :class="selectedBuildingId === null ? 'border-brand-500 bg-brand-500/25 text-white shadow-sm' : 'border-white/10 bg-white/5 text-slate-300 hover:bg-white/10'"
                    >
                        {{ __('كل العمارات') }} (<span x-text="buildings.length"></span>)
                    </button>

                    <template x-for="b in buildings" :key="b.id">
                        <button
                            type="button"
                            @click="filterByBuilding(b.id)"
                            class="inline-flex items-center gap-1.5 rounded-xl border px-3 py-1.5 text-xs font-bold transition"
                            :class="selectedBuildingId === b.id ? 'border-brand-400 bg-brand-500/30 text-white shadow-sm' : 'border-white/10 bg-white/5 text-slate-300 hover:bg-white/10'"
                        >
                            <span x-text="b.name"></span>
                            <span
                                class="rounded-full px-1.5 py-0.2 text-[10px] font-extrabold"
                                :class="b.available_count > 0 ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300'"
                                x-text="b.available_count + '/' + b.units_count"
                            ></span>
                        </button>
                    </template>
                </div>

                {{-- Search & Unit Type Filter --}}
                <div class="flex items-center gap-2">
                    {{-- Search --}}
                    <div class="relative w-48 sm:w-64">
                        <svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400 rtl:right-3 rtl:left-auto ltr:left-3 ltr:right-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="7" stroke-width="1.8"/><path d="m20 20-3.5-3.5" stroke-width="1.8"/></svg>
                        <input
                            type="search"
                            x-model="searchQuery"
                            placeholder="{{ __('بحث برقم الشقة، العمارة...') }}"
                            class="app-input py-1.5 text-xs pr-9 pl-3 rtl:pr-9 rtl:pl-3 ltr:pl-9 ltr:pr-3"
                        >
                    </div>

                    {{-- Type Filter --}}
                    <select
                        x-model="selectedType"
                        class="rounded-xl border border-white/10 bg-slate-900/80 px-3 py-2 text-xs font-semibold text-slate-200 focus:border-brand-400 focus:outline-none"
                    >
                        <option value="all">{{ __('جميع الأنواع') }}</option>
                        <template x-for="t in unitTypes" :key="t">
                            <option :value="t" x-text="t"></option>
                        </template>
                    </select>

                    <button
                        type="button"
                        x-show="selectedStatus !== 'all' || selectedBuildingId !== null || selectedType !== 'all' || searchQuery !== ''"
                        @click="selectedStatus = 'all'; selectedBuildingId = null; selectedType = 'all'; searchQuery = ''"
                        class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-2.5 py-1.5 text-xs font-bold text-rose-300 hover:bg-rose-500/20 transition"
                        title="{{ __('إلغاء جميع الفلاتر') }}"
                    >
                        {{ __('إعادة ضبط') }}
                    </button>
                </div>
            </div>
        </section>

        {{-- 4. Main Views & Interactive Inspector Two-Column Layout --}}
        <div class="flex flex-col lg:flex-row items-start gap-6 w-full">
            {{-- Primary Work Area (Elevation / 2D / Compact / Table) --}}
            <div class="flex-1 min-w-0 w-full space-y-6">

                {{-- MODE A: Master Elevation View (All Buildings Side-by-Side Towers) --}}
                <div x-show="viewMode === 'elevation'" class="space-y-6">
                    <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-2">
                        <template x-for="building in filteredBuildings" :key="building.id">
                            <div class="app-card relative flex flex-col overflow-hidden p-0 border-white/15 bg-gradient-to-b from-slate-900 via-slate-900/95 to-slate-950 shadow-2xl">
                                {{-- Architectural Roof Header --}}
                                <div class="relative border-b border-white/10 bg-slate-950/80 p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2.5">
                                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-500/20 text-brand-300 ring-1 ring-brand-400/30 font-black text-sm" x-text="building.name"></span>
                                            <div>
                                                <h3 class="text-base font-black text-white" x-text="'عمارة ' + building.name"></h3>
                                                <p class="text-[10px] text-slate-400" x-text="(building.code || '') + ' · ' + building.floors.length + ' {{ __('أدوار') }}'"></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 text-end">
                                            <button
                                                type="button"
                                                @click="openBuilding2D(building.id)"
                                                class="inline-flex items-center gap-1 rounded-xl border border-brand-400/40 bg-brand-500/20 px-2.5 py-1 text-[10px] font-bold text-brand-300 hover:bg-brand-500/35 transition"
                                                title="{{ __('فتح الواجهة المعمارية 2D لهذه العمارة') }}"
                                            >
                                                <span>{{ __('مقطع 2D') }}</span>
                                                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M15 3h6v6M10 14L21 3" stroke-width="2"/></svg>
                                            </button>
                                            <span
                                                class="rounded-full px-2.5 py-1 text-xs font-black"
                                                :class="building.available_count > 0 ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30'"
                                                x-text="building.available_count + ' / ' + building.units_count + ' {{ __('متاح') }}'"
                                            ></span>
                                        </div>
                                    </div>

                                    {{-- Mini Availability Bar --}}
                                    <div class="mt-3 flex h-1.5 w-full overflow-hidden rounded-full bg-slate-800">
                                        <div class="bg-emerald-400" :style="'width: ' + (building.units_count > 0 ? (building.available_count / building.units_count) * 100 : 0) + '%'"></div>
                                        <div class="bg-rose-500" :style="'width: ' + (building.units_count > 0 ? (building.sold_count / building.units_count) * 100 : 0) + '%'"></div>
                                        <div class="bg-amber-400" :style="'width: ' + (building.units_count > 0 ? (building.reserved_count / building.units_count) * 100 : 0) + '%'"></div>
                                    </div>
                                </div>

                                {{-- Building Floors Stack (Top to Ground Floor) --}}
                                <div class="flex flex-1 flex-col divide-y divide-white/5 p-3 space-y-2">
                                    <template x-for="floor in building.floors" :key="floor.id">
                                        <div class="pt-2">
                                            <div class="mb-1.5 flex items-center justify-between text-[11px]">
                                                <button
                                                    type="button"
                                                    @click="openBuilding2D(building.id); focusedFloorId = floor.id"
                                                    class="inline-flex items-center gap-1 font-bold text-slate-400 hover:text-brand-300 transition"
                                                    title="{{ __('عرض هذا الدور في الواجهة 2D') }}"
                                                >
                                                    <svg class="h-3 w-3 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 3h18v18H3zM3 9h18M3 15h18" stroke-width="1.8"/></svg>
                                                    <span x-text="floor.name"></span>
                                                    <span class="text-[9px] text-brand-400 font-normal">({{ __('مقطع 2D') }} ↗)</span>
                                                </button>
                                                <span class="text-[10px] text-slate-500" x-text="floor.units.filter(u => isUnitMatching(u)).length + ' / ' + floor.units.length + ' {{ __('وحدات') }}'"></span>
                                            </div>

                                            <form
                                                x-show="canChangeStatus"
                                                :action="floor.bulk_status_url"
                                                method="POST"
                                                class="mt-2 flex flex-wrap items-center gap-2 border-t border-white/5 pt-2"
                                                @submit.prevent="submitStatusForm($event, 'floor', floor)"
                                                data-no-ajax
                                            >
                                                @csrf
                                                <span class="text-xs font-bold text-slate-400">{{ __('Apply to all floor units') }}</span>
                                                <select name="status" class="rounded-lg border border-white/10 bg-slate-900 px-2 py-1.5 text-xs font-semibold text-slate-200 focus:border-brand-400 focus:outline-none">
                                                    <option value="available">{{ __('Available') }}</option>
                                                    <option value="reserved">{{ __('Reserved') }}</option>
                                                    <option value="sold">{{ __('Sold') }}</option>
                                                    <option value="hidden">{{ __('Hidden') }}</option>
                                                </select>
                                                <label class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-400">
                                                    <input type="checkbox" name="hidden_from_website" value="1" class="h-4 w-4 rounded border-white/10 bg-slate-900 text-brand-600 focus:ring-brand-500/20">
                                                    {{ __('Hide from Website') }}
                                                </label>
                                                <button type="submit" class="rounded-lg bg-brand-600 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-brand-500">
                                                    {{ __('Save') }}
                                                </button>
                                            </form>

                                            {{-- Floor Units Grid --}}
                                            <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                                                <template x-for="unit in floor.units" :key="unit.id">
                                                    <button
                                                        type="button"
                                                        @click="selectUnit(unit)"
                                                        class="group relative flex flex-col items-center justify-center rounded-xl border p-2 text-center transition-all duration-200"
                                                        :class="{
                                                            'opacity-25 grayscale hover:opacity-80': !isUnitMatching(unit),
                                                            'ring-2 ring-white scale-105 shadow-xl z-10': selectedUnit?.id === unit.id,
                                                            'border-emerald-500/40 bg-emerald-500/15 text-emerald-200 hover:bg-emerald-500/25': unit.status === 'available',
                                                            'border-rose-500/40 bg-rose-500/15 text-rose-200 hover:bg-rose-500/25': unit.status === 'sold',
                                                            'border-amber-500/40 bg-amber-500/15 text-amber-200 hover:bg-amber-500/25': unit.status === 'reserved',
                                                            'border-slate-700 bg-slate-800/50 text-slate-400': unit.status === 'hidden'
                                                        }"
                                                    >
                                                        <span class="block text-xs font-black" x-text="unit.number"></span>
                                                        <span class="mt-0.5 block text-[9px] font-semibold opacity-90" x-text="unit.area ? unit.area + ' م²' : unit.type"></span>
                                                        <span
                                                            class="mt-1 rounded-full px-1.5 py-0.2 text-[8px] font-extrabold uppercase"
                                                            :class="{
                                                                'bg-emerald-500/30 text-emerald-300': unit.status === 'available',
                                                                'bg-rose-500/30 text-rose-300': unit.status === 'sold',
                                                                'bg-amber-500/30 text-amber-300': unit.status === 'reserved',
                                                                'bg-slate-700 text-slate-400': unit.status === 'hidden'
                                                            }"
                                                            x-text="statusLabels[unit.status] || unit.status"
                                                        ></span>
                                                    </button>
                                                </template>
                                                <template x-if="floor.units.length === 0">
                                                    <p class="col-span-full py-2 text-center text-[10px] text-slate-500">{{ __('لا توجد وحدات مسجلة في هذا الدور') }}</p>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- MODE B: Focused Single Building 2D Architectural Elevation & Section --}}
                <div x-show="viewMode === 'focused'" class="space-y-6">
                    <div class="app-card overflow-hidden p-0 border-white/20 bg-gradient-to-b from-slate-900 via-slate-900/95 to-slate-950 shadow-2xl">
                        {{-- 2D Building Top Header & Building Switcher Bar --}}
                        <div class="border-b border-white/10 bg-slate-950/90 p-5 backdrop-blur-md space-y-4">
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500/30 to-brand-700/30 text-brand-200 ring-1 ring-brand-400/40 text-xl font-black shadow-lg shadow-brand-500/20" x-text="focusedBuilding?.name || '—'"></div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h2 class="text-xl font-black text-white sm:text-2xl" x-text="'الواجهة والمقطع المعماري 2D — عمارة ' + (focusedBuilding?.name || '')"></h2>
                                            <span class="rounded-full bg-brand-500/20 px-2.5 py-0.5 text-[10px] font-bold text-brand-300" x-text="focusedBuilding?.code || ''"></span>
                                        </div>
                                        <p class="mt-0.5 text-xs text-slate-400">
                                            <span x-text="focusedBuilding?.floors?.length || 0"></span> {{ __('أدوار معمارية') }} ·
                                            <span x-text="focusedBuilding?.units_count || 0"></span> {{ __('إجمالي الشقق') }} ·
                                            <span class="font-bold text-emerald-400" x-text="focusedBuilding?.available_count || 0"></span> {{ __('متاح للبيع') }} ·
                                            <span class="font-bold text-rose-400" x-text="focusedBuilding?.sold_count || 0"></span> {{ __('تم البيع') }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Building Quick Switcher Buttons --}}
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="text-xs font-bold text-slate-400 me-1">{{ __('اختر العمارة:') }}</span>
                                    <template x-for="b in buildings" :key="b.id">
                                        <button
                                            type="button"
                                            @click="focusedBuildingId = b.id; focusedFloorId = null"
                                            class="flex h-9 min-w-9 items-center justify-center rounded-xl border px-2.5 text-xs font-black transition-all duration-200"
                                            :class="focusedBuildingId === b.id
                                                ? 'border-brand-400 bg-brand-500/30 text-white shadow-md shadow-brand-500/20 ring-1 ring-brand-400'
                                                : 'border-white/10 bg-white/5 text-slate-300 hover:border-white/20 hover:bg-white/10 hover:text-white'"
                                        >
                                            <span x-text="b.name"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            {{-- Floor Selection & 2D Mode Switcher Bar --}}
                            <div class="flex flex-col gap-3 border-t border-white/10 pt-3 lg:flex-row lg:items-center lg:justify-between">
                                {{-- Floor Selector Pills --}}
                                <div class="flex flex-wrap items-center gap-1.5 overflow-x-auto no-scrollbar">
                                    <span class="text-xs font-bold text-brand-300 me-1 inline-flex items-center gap-1">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 3h18v18H3zM3 9h18M3 15h18" stroke-width="1.8"/></svg>
                                        {{ __('اختيار الدور:') }}
                                    </span>

                                    {{-- All Floors Button --}}
                                    <button
                                        type="button"
                                        @click="focusedFloorId = null"
                                        class="inline-flex items-center gap-1.5 rounded-xl border px-3 py-1.5 text-xs font-bold transition"
                                        :class="focusedFloorId === null
                                            ? 'border-brand-400 bg-brand-500/25 text-white shadow-sm ring-1 ring-brand-400/40'
                                            : 'border-white/10 bg-white/5 text-slate-300 hover:bg-white/10 hover:text-white'"
                                    >
                                        <span>{{ __('كل الأدوار') }}</span>
                                        <span class="rounded-full bg-white/10 px-1.5 py-0.2 text-[10px]" x-text="focusedBuilding?.floors?.length || 0"></span>
                                    </button>

                                    {{-- Individual Floor Buttons --}}
                                    <template x-for="floor in focusedBuilding?.floors || []" :key="floor.id">
                                        <button
                                            type="button"
                                            @click="focusedFloorId = focusedFloorId === floor.id ? null : floor.id"
                                            class="inline-flex items-center gap-1.5 rounded-xl border px-3 py-1.5 text-xs font-bold transition"
                                            :class="focusedFloorId === floor.id
                                                ? 'border-emerald-400 bg-emerald-500/25 text-emerald-100 shadow-sm ring-1 ring-emerald-400'
                                                : 'border-white/10 bg-white/5 text-slate-300 hover:bg-white/10 hover:text-white'"
                                        >
                                            <span x-text="floor.name"></span>
                                            <span
                                                class="rounded-full px-1.5 py-0.2 text-[10px] font-extrabold"
                                                :class="floor.units.filter(u => u.status === 'available').length > 0 ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300'"
                                                x-text="floor.units.filter(u => u.status === 'available').length + '/' + floor.units.length"
                                            ></span>
                                        </button>
                                    </template>
                                </div>

                                {{-- 2D View Sub-mode Switcher (Elevation vs Floor Plan Blueprint) --}}
                                <div class="flex items-center gap-1.5 rounded-xl border border-white/10 bg-slate-900 p-1">
                                    <button
                                        type="button"
                                        @click="focusedFloorLayoutMode = 'elevation'"
                                        class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-bold transition"
                                        :class="focusedFloorLayoutMode === 'elevation' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-400 hover:text-white'"
                                    >
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 21V7l8-4 8 4v14M9 21v-4h6v4" stroke-width="1.8"/></svg>
                                        <span>{{ __('الواجهة الرأسية 2D') }}</span>
                                    </button>

                                    <button
                                        type="button"
                                        @click="focusedFloorLayoutMode = 'plan'"
                                        class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-bold transition"
                                        :class="focusedFloorLayoutMode === 'plan' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-400 hover:text-white'"
                                    >
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="1.8"/><path d="M3 9h18M9 21V9" stroke-width="1.8"/></svg>
                                        <span>{{ __('المسقط الأفقي 2D') }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- 2D Architectural Elevation & Floor Plan Canvas Container --}}
                        <div class="p-6 sm:p-8 bg-gradient-to-b from-slate-950 via-slate-900/60 to-slate-950/95 overflow-x-auto">
                            <template x-if="focusedBuilding">
                                <div class="mx-auto w-full max-w-4xl space-y-4 relative">

                                    {{-- OPTION 1: Vertical 2D Elevation View --}}
                                    <div x-show="focusedFloorLayoutMode === 'elevation'" class="space-y-0">
                                        {{-- 1. Architectural Rooftop / Penthouse Crown --}}
                                        <div x-show="focusedFloorId === null" class="relative rounded-t-3xl border-t-2 border-x-2 border-brand-500/30 bg-gradient-to-b from-slate-900 to-slate-950/90 p-4 text-center shadow-lg">
                                            {{-- Pergola / Mechanical Roof Top Cutaway --}}
                                            <div class="mx-auto flex max-w-md items-center justify-between px-6 py-2 border-b border-dashed border-brand-500/20">
                                                <div class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400">
                                                    <svg class="h-4 w-4 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 2v20M2 12h20M4.93 4.93l14.14 14.14M19.07 4.93 4.93 19.07" stroke-width="1.5"/></svg>
                                                    <span>{{ __('خدمات السطح وخزانات المياه') }}</span>
                                                </div>
                                                <div class="rounded-full bg-brand-500/15 px-3 py-0.5 text-[10px] font-black uppercase text-brand-300">
                                                    {{ __('السطح / الروف العام') }} (+21.00m)
                                                </div>
                                                <div class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400">
                                                    <svg class="h-4 w-4 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="1.5"/><path d="M7 7h10M7 12h10M7 17h10" stroke-width="1.5"/></svg>
                                                    <span>{{ __('غرفة محركات المصعد') }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 2. Architectural Floor Slices (Stacked Top-to-Bottom or Filtered) --}}
                                        <div
                                            class="border-x-2 border-brand-500/30 divide-y-2 divide-slate-800/80 bg-slate-950/70"
                                            :class="focusedFloorId !== null ? 'rounded-3xl border-2' : ''"
                                        >
                                            <template x-for="(floor, floorIdx) in focusedBuildingFloors" :key="floor.id">
                                                <div
                                                    class="relative transition-all duration-300 hover:bg-white/[0.02]"
                                                    :class="focusedFloorId === floor.id ? 'bg-brand-500/5' : ''"
                                                >
                                                    {{-- Floor Slab Beam Header --}}
                                                    <div class="flex items-center justify-between border-b border-white/5 bg-slate-900/80 px-4 py-2 text-[11px]">
                                                        <div class="flex items-center gap-2 font-black text-brand-300">
                                                            <span class="inline-flex items-center gap-1">
                                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 3h18v18H3zM3 9h18M3 15h18" stroke-width="1.8"/></svg>
                                                                <span x-text="floor.name"></span>
                                                            </span>
                                                            <span class="text-slate-500">|</span>
                                                            <span class="text-slate-400 font-mono text-[10px]" x-text="'+' + ((floor.number + 1) * 3) + '.00m'"></span>
                                                        </div>

                                                        <div class="flex items-center gap-2.5">
                                                            {{-- Quick Toggle Floor Focus / Plan --}}
                                                            <button
                                                                type="button"
                                                                @click="focusedFloorId = floor.id; focusedFloorLayoutMode = 'plan'"
                                                                class="inline-flex items-center gap-1 rounded-lg border border-white/10 bg-white/5 px-2 py-0.5 text-[10px] font-bold text-slate-300 hover:bg-brand-600 hover:text-white transition"
                                                                title="{{ __('عرض المسقط الأفقي 2D لهذا الدور') }}"
                                                            >
                                                                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="1.8"/><path d="M3 9h18M9 21V9" stroke-width="1.8"/></svg>
                                                                <span>{{ __('مسقط أفقي 2D') }}</span>
                                                            </button>

                                                            <button
                                                                type="button"
                                                                @click="focusedFloorId = focusedFloorId === floor.id ? null : floor.id"
                                                                class="inline-flex items-center gap-1 rounded-lg border px-2 py-0.5 text-[10px] font-bold transition"
                                                                :class="focusedFloorId === floor.id ? 'border-amber-400/50 bg-amber-500/20 text-amber-200' : 'border-white/10 bg-white/5 text-slate-400 hover:text-white'"
                                                            >
                                                                <span x-text="focusedFloorId === floor.id ? '{{ __('إلغاء عزل الدور') }}' : '{{ __('عزل الدور') }}'"></span>
                                                            </button>

                                                            <span class="rounded-full bg-emerald-500/20 px-2 py-0.5 text-[10px] font-extrabold text-emerald-300" x-text="floor.units.filter(u => u.status === 'available').length + ' / ' + floor.units.length + ' متاح'"></span>
                                                        </div>
                                                    </div>


                                                    {{-- Floor Cutaway 2D Layout --}}
                                                    <div class="grid grid-cols-[3.5rem_minmax(0,1fr)_3.5rem] items-stretch gap-2 p-3">
                                                        {{-- Left Core: Elevator Column --}}
                                                        <div class="flex flex-col items-center justify-center rounded-xl border border-white/5 bg-slate-900/60 p-2 text-center text-[10px] text-slate-400" title="{{ __('مصعد المبنى') }}">
                                                            <svg class="h-5 w-5 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="5" y="3" width="14" height="18" rx="2" stroke-width="1.8"/><path d="m9 10 3-3 3 3M9 14l3 3 3-3" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                            <span class="mt-1 text-[8px] font-bold text-slate-400">{{ __('مصعد') }}</span>
                                                        </div>

                                                        {{-- Middle: Apartments Grid (2D Blueprint Boxes) --}}
                                                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                                                            <template x-for="unit in floor.units" :key="unit.id">
                                                                <button
                                                                    type="button"
                                                                    @click="selectUnit(unit)"
                                                                    class="group relative flex flex-col justify-between rounded-2xl border-2 p-3.5 text-start transition-all duration-300 hover:scale-[1.03]"
                                                                    :class="{
                                                                        'opacity-25 grayscale': !isUnitMatching(unit),
                                                                        'ring-4 ring-white shadow-2xl scale-[1.04] z-20': selectedUnit?.id === unit.id,
                                                                        'border-emerald-500/50 bg-gradient-to-b from-emerald-950/40 via-slate-900 to-slate-950 text-emerald-100 shadow-lg shadow-emerald-500/10 hover:border-emerald-400': unit.status === 'available',
                                                                        'border-rose-500/50 bg-gradient-to-b from-rose-950/40 via-slate-900 to-slate-950 text-rose-100 shadow-lg shadow-rose-500/10 hover:border-rose-400': unit.status === 'sold',
                                                                        'border-amber-500/50 bg-gradient-to-b from-amber-950/40 via-slate-900 to-slate-950 text-amber-100 shadow-lg shadow-amber-500/10 hover:border-amber-400': unit.status === 'reserved',
                                                                        'border-slate-700 bg-slate-900 text-slate-400': unit.status === 'hidden'
                                                                    }"
                                                                >
                                                                    {{-- Architectural Window Rail Simulation --}}
                                                                    <div class="mb-2 flex items-center justify-between border-b border-white/10 pb-1.5">
                                                                        <div class="flex items-center gap-1">
                                                                            <span class="h-1.5 w-1.5 rounded-full" :class="{
                                                                                'bg-emerald-400 animate-pulse': unit.status === 'available',
                                                                                'bg-rose-400': unit.status === 'sold',
                                                                                'bg-amber-400': unit.status === 'reserved',
                                                                                'bg-slate-500': unit.status === 'hidden'
                                                                            }"></span>
                                                                            <span class="text-xs font-black text-white" x-text="'وحدة #' + unit.number"></span>
                                                                        </div>
                                                                        <span
                                                                            class="rounded-full px-2 py-0.5 text-[9px] font-black uppercase tracking-wider"
                                                                            :class="{
                                                                                'bg-emerald-500/30 text-emerald-300 border border-emerald-500/40': unit.status === 'available',
                                                                                'bg-rose-500/30 text-rose-300 border border-rose-500/40': unit.status === 'sold',
                                                                                'bg-amber-500/30 text-amber-300 border border-amber-500/40': unit.status === 'reserved',
                                                                                'bg-slate-700 text-slate-400': unit.status === 'hidden'
                                                                            }"
                                                                            x-text="statusLabels[unit.status] || unit.status"
                                                                        ></span>
                                                                    </div>

                                                                    {{-- Unit Blueprint Specs --}}
                                                                    <div class="space-y-1.5 text-[11px]">
                                                                        <div class="flex items-center justify-between">
                                                                        <span class="font-bold text-slate-300" x-text="unit.type || '{{ __('شقة') }}'"></span>
                                                                        <span class="font-extrabold text-white" x-text="unit.area ? unit.area + ' م²' : '—'"></span>
                                                                    </div>

                                                                    <div class="flex items-center gap-2 text-[10px] text-slate-400">
                                                                        <span class="inline-flex items-center gap-0.5" x-show="unit.bedrooms">
                                                                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M2 4v16M2 8h20v12M2 17h20M6 8v9" stroke-width="1.8"/></svg>
                                                                            <span x-text="unit.bedrooms + ' غ'"></span>
                                                                        </span>
                                                                        <span class="inline-flex items-center gap-0.5" x-show="unit.bathrooms">
                                                                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 12h16a1 1 0 0 1 1 1v3a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4v-3a1 1 0 0 1 1-1Z" stroke-width="1.8"/></svg>
                                                                            <span x-text="unit.bathrooms + ' ح'"></span>
                                                                        </span>
                                                                        <span class="text-amber-300 text-[9px] font-bold" x-show="unit.garden_area > 0">{{ __('+حديقة') }}</span>
                                                                        <span class="text-brand-300 text-[9px] font-bold" x-show="unit.roof_area > 0">{{ __('+روف') }}</span>
                                                                    </div>

                                                                    {{-- Price Tag --}}
                                                                    <div class="border-t border-white/10 pt-1.5">
                                                                        <p class="text-xs font-black text-white" x-text="unit.price ? money(unit.price) + ' ج.م' : 'السعر عند الطلب'"></p>
                                                                    </div>
                                                                </div>

                                                                {{-- Hover prompt --}}
                                                                <div class="mt-2 text-center text-[9px] font-bold text-brand-300 opacity-0 group-hover:opacity-100 transition">
                                                                    {{ __('اضغط للمعاينة') }} ←
                                                                </div>
                                                            </button>
                                                        </template>
                                                        <template x-if="floor.units.length === 0">
                                                            <div class="col-span-full py-4 text-center text-xs text-slate-500">{{ __('لا توجد وحدات مسجلة في هذا الدور') }}</div>
                                                        </template>
                                                    </div>

                                                    {{-- Right Core: Stairs Column --}}
                                                    <div class="flex flex-col items-center justify-center rounded-xl border border-white/5 bg-slate-900/60 p-2 text-center text-[10px] text-slate-400" title="{{ __('سلالم المبنى') }}">
                                                        <svg class="h-5 w-5 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 5v14M5 19h14M5 19v-4h4v-4h4V7h4" stroke-width="1.8"/></svg>
                                                        <span class="mt-1 text-[8px] font-bold text-slate-400">{{ __('سلم') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    {{-- 3. Architectural Ground Base & Entrance Canopy --}}
                                    <div x-show="focusedFloorId === null" class="rounded-b-3xl border-b-2 border-x-2 border-brand-500/30 bg-gradient-to-t from-slate-900 to-slate-950 p-5 shadow-2xl">
                                        <div class="flex flex-wrap items-center justify-between gap-4">
                                            {{-- Private Ground Gardens --}}
                                            <div class="flex items-center gap-2 text-xs font-bold text-emerald-400">
                                                <svg class="h-5 w-5 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 22v-9M9 13a4.5 4.5 0 0 1 6 0M6 10a7.5 7.5 0 0 1 12 0" stroke-width="1.8"/></svg>
                                                <span>{{ __('مساحات خضراء وحدائق خاصة بالأرضي') }}</span>
                                            </div>

                                            {{-- Main Entrance --}}
                                            <div class="rounded-2xl border border-brand-400/40 bg-brand-500/15 px-6 py-2.5 text-center shadow-lg shadow-brand-500/10">
                                                <p class="text-xs font-black text-white">{{ __('المدخل الرئيسي الفاخر للعمارة') }}</p>
                                                <p class="text-[10px] text-brand-300 font-mono">±0.00m {{ __('منسوب الشارع الرئيسي') }}</p>
                                            </div>

                                            {{-- Security & Parking --}}
                                            <div class="flex items-center gap-2 text-xs font-bold text-slate-400">
                                                <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="11" width="18" height="11" rx="2" stroke-width="1.8"/><path d="M7 11V7a5 5 0 0 1 10 0v4" stroke-width="1.8"/></svg>
                                                <span>{{ __('إنتركم وبوابة وأماكن انتظار') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- OPTION 2: 2D Horizontal Floor Plan Blueprint View --}}
                                <div x-show="focusedFloorLayoutMode === 'plan'" class="space-y-4">
                                    <template x-for="floor in (focusedFloor ? [focusedFloor] : focusedBuilding.floors)" :key="'plan-' + floor.id">
                                        <div class="rounded-3xl border-2 border-brand-500/30 bg-slate-950 p-6 shadow-2xl space-y-5">
                                            {{-- Plan Header --}}
                                            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/10 pb-4">
                                                <div class="flex items-center gap-2.5">
                                                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-500/20 text-brand-300 font-black text-sm ring-1 ring-brand-400/30" x-text="floor.number"></span>
                                                    <div>
                                                        <h3 class="text-base font-black text-white" x-text="'المسقط الأفقي الهندسي 2D — ' + floor.name"></h3>
                                                        <p class="text-xs text-slate-400">{{ __('توزيع الشقق حول الممر الداخلي والمصعد والسلالم') }}</p>
                                                    </div>
                                                </div>
                                                <span class="rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-bold text-emerald-300" x-text="floor.units.length + ' {{ __('شقق إجمالية') }}'"></span>
                                            </div>

                                            {{-- 2D Floor Architectural Core & Apartment Quadrants --}}
                                            <div class="relative rounded-2xl border border-white/10 bg-slate-900/90 p-6">
                                                {{-- Front Facade Tag --}}
                                                <div class="mb-4 text-center">
                                                    <span class="rounded-full border border-brand-400/30 bg-brand-500/10 px-4 py-1 text-[11px] font-black uppercase tracking-wider text-brand-300">
                                                        ↑ {{ __('الواجهة الأمامية للمبنى (إطلالة الشارع)') }} ↑
                                                    </span>
                                                </div>

                                                {{-- Apartment Grid in Plan (2x2 or 1x4) --}}
                                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                                    <template x-for="(unit, uIdx) in floor.units" :key="'plan-u-' + unit.id">
                                                        <button
                                                            type="button"
                                                            @click="selectUnit(unit)"
                                                            class="group relative flex flex-col justify-between rounded-2xl border-2 p-4 text-start transition-all duration-300 hover:scale-[1.02]"
                                                            :class="{
                                                                'opacity-30 grayscale': !isUnitMatching(unit),
                                                                'ring-4 ring-white shadow-2xl scale-[1.03] z-20': selectedUnit?.id === unit.id,
                                                                'border-emerald-500/50 bg-emerald-950/30 text-emerald-100 hover:border-emerald-400': unit.status === 'available',
                                                                'border-rose-500/50 bg-rose-950/30 text-rose-100 hover:border-rose-400': unit.status === 'sold',
                                                                'border-amber-500/50 bg-amber-950/30 text-amber-100 hover:border-amber-400': unit.status === 'reserved',
                                                                'border-slate-700 bg-slate-800 text-slate-400': unit.status === 'hidden'
                                                            }"
                                                        >
                                                            <div class="flex items-start justify-between border-b border-white/10 pb-2">
                                                                <div>
                                                                    <div class="flex items-center gap-1.5">
                                                                        <span class="h-2 w-2 rounded-full" :class="{
                                                                            'bg-emerald-400 animate-pulse': unit.status === 'available',
                                                                            'bg-rose-400': unit.status === 'sold',
                                                                            'bg-amber-400': unit.status === 'reserved',
                                                                            'bg-slate-500': unit.status === 'hidden'
                                                                        }"></span>
                                                                        <span class="text-sm font-black text-white" x-text="'وحدة #' + unit.number"></span>
                                                                    </div>
                                                                    <span class="text-[10px] text-slate-400 font-semibold" x-text="uIdx === 0 ? '{{ __('واجهة يمين') }}' : (uIdx === 1 ? '{{ __('واجهة يسار') }}' : (uIdx === 2 ? '{{ __('خلفي يمين') }}' : '{{ __('خلفي يسار') }}'))"></span>
                                                                </div>

                                                                <span
                                                                    class="rounded-full px-2.5 py-1 text-[10px] font-black uppercase"
                                                                    :class="{
                                                                        'bg-emerald-500/30 text-emerald-300 border border-emerald-500/40': unit.status === 'available',
                                                                        'bg-rose-500/30 text-rose-300 border border-rose-500/40': unit.status === 'sold',
                                                                        'bg-amber-500/30 text-amber-300 border border-amber-500/40': unit.status === 'reserved',
                                                                        'bg-slate-700 text-slate-400': unit.status === 'hidden'
                                                                    }"
                                                                    x-text="statusLabels[unit.status] || unit.status"
                                                                ></span>
                                                            </div>

                                                            <div class="mt-3 grid grid-cols-2 gap-2 text-xs">
                                                                <div><span class="text-slate-500">{{ __('النوع:') }}</span> <strong class="text-white" x-text="unit.type || '{{ __('شقة') }}'"></strong></div>
                                                                <div><span class="text-slate-500">{{ __('المساحة:') }}</span> <strong class="text-white" x-text="unit.area + ' م²'"></strong></div>
                                                                <div><span class="text-slate-500">{{ __('الغرف:') }}</span> <strong class="text-white" x-text="(unit.bedrooms || 0) + ' غ / ' + (unit.bathrooms || 0) + ' ح'"></strong></div>
                                                                <div><span class="text-slate-500">{{ __('السعر:') }}</span> <strong class="text-emerald-300 font-black" x-text="unit.price ? moneyShort(unit.price) + ' ج.م' : '—'"></strong></div>
                                                            </div>
                                                        </button>
                                                    </template>
                                                </div>

                                                {{-- Central Core (Corridor, Elevator, Stairs) --}}
                                                <div class="mt-6 flex items-center justify-between rounded-xl border border-dashed border-white/20 bg-slate-950/80 p-3 text-xs text-slate-400">
                                                    <div class="flex items-center gap-2">
                                                        <svg class="h-4 w-4 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="5" y="3" width="14" height="18" rx="2" stroke-width="1.8"/></svg>
                                                        <span>{{ __('المصعد الرئيسي') }}</span>
                                                    </div>
                                                    <div class="text-center font-bold text-slate-300">
                                                        [ {{ __('ممر التوزيع والمداخل') }} ]
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <svg class="h-4 w-4 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 5v14M5 19h14" stroke-width="1.8"/></svg>
                                                        <span>{{ __('سلم الهروب والطوارئ') }}</span>
                                                    </div>
                                                </div>

                                                {{-- Rear Facade Tag --}}
                                                <div class="mt-4 text-center">
                                                    <span class="rounded-full border border-white/10 bg-white/5 px-4 py-1 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                                        ↓ {{ __('الواجهة الخلفية (المناور والخدمات)') }} ↓
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- MODE C: Compact Project Matrix (All 292 Units on 1 Desktop Screen) --}}
                <div x-show="viewMode === 'compact'" class="app-card space-y-5 p-5">
                    <div class="flex items-center justify-between border-b border-white/10 pb-3">
                        <div>
                            <p class="mobile-section-title">{{ __('الشبكة المصغرة الشاملة') }}</p>
                            <h3 class="text-lg font-bold text-white">{{ __('خريطة حرارية فورية لجميع وحدات المشروع') }}</h3>
                        </div>
                        <span class="text-xs text-slate-400" x-text="filteredUnits.length + ' / ' + totalUnits + ' {{ __('وحدة معروضة') }}'"></span>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-2">
                        <template x-for="building in filteredBuildings" :key="building.id">
                            <div class="rounded-2xl border border-white/10 bg-slate-950/60 p-3.5 space-y-2">
                                <div class="flex items-center justify-between text-xs font-bold">
                                    <span class="text-white" x-text="'عمارة ' + building.name"></span>
                                    <span
                                        class="rounded-full px-2 py-0.5 text-[10px]"
                                        :class="building.available_count > 0 ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300'"
                                        x-text="building.available_count + ' متاح'"
                                    ></span>
                                </div>
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="floor in building.floors" :key="floor.id">
                                        <template x-for="unit in floor.units" :key="unit.id">
                                            <button
                                                type="button"
                                                @click="selectUnit(unit)"
                                                :title="unit.building_name + ' · ' + unit.floor_name + ' · #' + unit.number + ' · ' + (statusLabels[unit.status] || unit.status) + ' · ' + (unit.price ? money(unit.price) + ' EGP' : '')"
                                                class="flex h-7 min-w-7 items-center justify-center rounded-lg border px-1.5 text-[10px] font-black transition-all duration-150"
                                                :class="{
                                                    'opacity-20': !isUnitMatching(unit),
                                                    'ring-2 ring-white scale-125 z-10': selectedUnit?.id === unit.id,
                                                    'border-emerald-500/50 bg-emerald-500/20 text-emerald-300 hover:bg-emerald-400 hover:text-black': unit.status === 'available',
                                                    'border-rose-500/50 bg-rose-500/20 text-rose-300 hover:bg-rose-400 hover:text-black': unit.status === 'sold',
                                                    'border-amber-500/50 bg-amber-500/20 text-amber-300 hover:bg-amber-400 hover:text-black': unit.status === 'reserved',
                                                    'border-slate-700 bg-slate-800 text-slate-400': unit.status === 'hidden'
                                                }"
                                                x-text="unit.number"
                                            ></button>
                                        </template>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- MODE D: Master Data Table View --}}
                <div x-show="viewMode === 'table'" class="app-card overflow-hidden p-0">
                    <div class="flex items-center justify-between border-b border-white/10 p-4">
                        <div>
                            <p class="mobile-section-title">{{ __('جدول الحصر الشامل') }}</p>
                            <h3 class="text-lg font-bold text-white">{{ __('بيانات تفصيلية لجميع وحدات المشروع') }}</h3>
                        </div>
                        <span class="rounded-full bg-white/5 px-3 py-1 text-xs font-bold text-slate-300" x-text="filteredUnits.length + ' {{ __('وحدة') }}'"></span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-start text-xs">
                            <thead class="border-b border-white/10 bg-white/[0.03] text-[11px] font-bold text-slate-400">
                                <tr>
                                    <th class="p-3.5 text-start">{{ __('رقم الوحدة') }}</th>
                                    <th class="p-3.5 text-start">{{ __('العمارة') }}</th>
                                    <th class="p-3.5 text-start">{{ __('الدور') }}</th>
                                    <th class="p-3.5 text-start">{{ __('النوع') }}</th>
                                    <th class="p-3.5 text-start">{{ __('المساحة') }}</th>
                                    <th class="p-3.5 text-start">{{ __('السعر الإجمالي') }}</th>
                                    <th class="p-3.5 text-start">{{ __('سعر المتر') }}</th>
                                    <th class="p-3.5 text-start">{{ __('الغرف/الحمام') }}</th>
                                    <th class="p-3.5 text-start">{{ __('الحالة') }}</th>
                                    <th class="p-3.5 text-center">{{ __('الإجراءات') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5 text-slate-300">
                                <template x-for="unit in filteredUnits" :key="unit.id">
                                    <tr
                                        @click="selectUnit(unit)"
                                        class="cursor-pointer transition hover:bg-white/[0.04]"
                                        :class="selectedUnit?.id === unit.id ? 'bg-brand-500/15 text-white' : ''"
                                    >
                                        <td class="p-3.5 font-bold text-white" x-text="'#' + unit.number"></td>
                                        <td class="p-3.5" x-text="unit.building_name"></td>
                                        <td class="p-3.5" x-text="unit.floor_name"></td>
                                        <td class="p-3.5" x-text="unit.type || '—'"></td>
                                        <td class="p-3.5 font-semibold" x-text="unit.area ? unit.area + ' م²' : '—'"></td>
                                        <td class="p-3.5 font-bold text-emerald-300" x-text="unit.price ? money(unit.price) + ' ج.م' : '—'"></td>
                                        <td class="p-3.5 text-slate-400" x-text="unit.price_per_meter ? money(unit.price_per_meter) + ' ج.م' : '—'"></td>
                                        <td class="p-3.5" x-text="(unit.bedrooms || 0) + ' غ / ' + (unit.bathrooms || 0) + ' ح'"></td>
                                        <td class="p-3.5">
                                            <span
                                                class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase"
                                                :class="{
                                                    'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30': unit.status === 'available',
                                                    'bg-rose-500/20 text-rose-300 border border-rose-500/30': unit.status === 'sold',
                                                    'bg-amber-500/20 text-amber-300 border border-amber-500/30': unit.status === 'reserved',
                                                    'bg-slate-700 text-slate-400': unit.status === 'hidden'
                                                }"
                                                x-text="statusLabels[unit.status] || unit.status"
                                            ></span>
                                        </td>
                                        <td class="p-3.5 text-center">
                                            <div class="inline-flex items-center gap-1.5" @click.stop>
                                                <a :href="unit.edit_url" class="rounded-lg bg-white/5 p-1.5 text-slate-300 hover:bg-brand-600 hover:text-white transition" title="{{ __('تعديل') }}">
                                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" stroke-width="2"/></svg>
                                                </a>
                                                <a :href="unit.public_url" target="_blank" class="rounded-lg bg-white/5 p-1.5 text-slate-300 hover:bg-emerald-600 hover:text-white transition" title="{{ __('معاينة') }}">
                                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M15 3h6v6M10 14L21 3M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" stroke-width="2"/></svg>
                                                </a>
                                                <a :href="unit.calculator_url" target="_blank" class="rounded-lg bg-white/5 p-1.5 text-slate-300 hover:bg-amber-600 hover:text-white transition" title="{{ __('حاسبة الأقساط') }}">
                                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="5" y="3" width="14" height="18" rx="2" stroke-width="2"/><path d="M8 7h8M8 11h.01M12 11h.01M16 11h.01M8 15h.01M12 15h.01M16 15h.01M8 18.5h.01M12 18.5h.01M16 18.5h.01" stroke-width="2"/></svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- 5. Persistent Interactive Unit Inspector (Desktop Sidebar) --}}
            <aside class="w-full lg:w-72 xl:w-80 2xl:w-96 lg:shrink-0 lg:sticky lg:top-6 space-y-4">
                <div class="app-card flex flex-col p-4 max-h-[calc(100vh-4rem)] overflow-hidden">
                    <div class="flex items-center justify-between border-b border-white/10 pb-3 shrink-0">
                        <div>
                            <p class="mobile-section-title">{{ __('Unit Inspector') }}</p>
                            <h3 class="text-lg font-bold text-white">{{ __('تفاصيل الشقة') }}</h3>
                        </div>
                        <template x-if="selectedUnit">
                            <button
                                type="button"
                                @click="selectedUnit = null"
                                class="rounded-lg bg-white/5 p-1 text-slate-400 hover:bg-white/10 hover:text-white transition"
                                title="{{ __('إغلاق المعاينة') }}"
                            >
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 6 6 18M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg>
                            </button>
                        </template>
                    </div>

                    {{-- Scrollable card content --}}
                    <div class="-mx-4 flex-1 overflow-y-auto overscroll-contain px-4 py-3">
                        {{-- When Unit Selected --}}
                        <template x-if="selectedUnit">
                            <div class="space-y-3 text-xs">
                            {{-- Header Card --}}
                            <div class="rounded-2xl border p-3" :class="{
                                'border-emerald-500/40 bg-emerald-500/10': selectedUnit.status === 'available',
                                'border-rose-500/40 bg-rose-500/10': selectedUnit.status === 'sold',
                                'border-amber-500/40 bg-amber-500/10': selectedUnit.status === 'reserved',
                                'border-slate-700 bg-slate-800/40': selectedUnit.status === 'hidden'
                            }">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400" x-text="selectedUnit.building_name + ' · ' + selectedUnit.floor_name"></p>
                                        <p class="mt-1 text-2xl font-black text-white" x-text="'وحدة #' + selectedUnit.number"></p>
                                    </div>
                                    <span
                                        class="rounded-full px-2.5 py-1 text-[11px] font-extrabold uppercase"
                                        :class="{
                                            'bg-emerald-500/30 text-emerald-300': selectedUnit.status === 'available',
                                            'bg-rose-500/30 text-rose-300': selectedUnit.status === 'sold',
                                            'bg-amber-500/30 text-amber-300': selectedUnit.status === 'reserved',
                                            'bg-slate-700 text-slate-400': selectedUnit.status === 'hidden'
                                        }"
                                        x-text="statusLabels[selectedUnit.status] || selectedUnit.status"
                                    ></span>
                                </div>
                                <p class="mt-2 text-sm font-bold text-white" x-text="selectedUnit.type || '{{ __('شقة سكنية') }}'"></p>
                            </div>

                            <form
                                x-show="canChangeStatus"
                                :action="selectedUnit.status_url"
                                method="POST"
                                class="w-fit rounded-xl border border-white/10 bg-white/[0.04] p-2.5"
                                @submit.prevent="submitStatusForm($event, 'unit', selectedUnit)"
                                data-no-ajax
                            >
                                @csrf
                                @method('PATCH')
                                <p class="text-[11px] font-semibold text-slate-300 mb-1">{{ __('Unit status') }}</p>
                                <div class="flex items-center gap-1.5">
                                    <select name="status" class="app-select h-8 w-28 py-0 pl-2 pr-6 text-[11px]">
                                        <option value="available" :selected="selectedUnit.status === 'available'">{{ __('Available') }}</option>
                                        <option value="reserved" :selected="selectedUnit.status === 'reserved'">{{ __('Reserved') }}</option>
                                        <option value="sold" :selected="selectedUnit.status === 'sold'">{{ __('Sold') }}</option>
                                        <option value="hidden" :selected="selectedUnit.status === 'hidden'">{{ __('Hidden') }}</option>
                                    </select>
                                    <button type="submit" class="app-button flex h-8 w-8 items-center justify-center p-0" title="{{ __('Save') }}">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" stroke-width="2"/><path d="M17 21v-8H7v8" stroke-width="2" stroke-linecap="round"/><path d="M7 3v5h8" stroke-width="2" stroke-linecap="round"/></svg>
                                    </button>
                                </div>
                                <label class="mt-2 flex items-center gap-1.5 text-[11px] text-slate-400">
                                    <input type="checkbox" name="hidden_from_website" value="1" :checked="selectedUnit.hidden_from_website" class="h-3.5 w-3.5 rounded border-white/10 bg-slate-900 text-brand-600 focus:ring-brand-500/20">
                                    {{ __('Hide from Website') }}
                                </label>
                            </form>

                            {{-- Financials --}}
                            <div class="rounded-xl border border-white/10 bg-white/[0.04] p-3 space-y-1.5 text-[11px]">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">{{ __('السعر:') }}</span>
                                    <strong class="font-black text-emerald-400" x-text="selectedUnit.price ? money(selectedUnit.price) + ' ج.م' : '—'"></strong>
                                </div>
                                <div class="flex items-center justify-between border-t border-white/5 pt-1.5">
                                    <span class="text-slate-400">{{ __('Down Payment (:percent%)', ['percent' => number_format($defaultDownPaymentPercent, 0)]) }}</span>
                                    <strong class="text-slate-200" x-text="selectedUnit.price ? money(selectedUnit.price * defaultDownPaymentPercent / 100) + ' ج.م' : '—'"></strong>
                                </div>
                                <div class="flex items-center justify-between border-t border-white/5 pt-1.5">
                                    <span class="text-slate-400">{{ __('قسط ربع سنوي:') }}</span>
                                    <strong class="text-emerald-300" x-text="selectedUnit.price ? money((selectedUnit.price * (1 - defaultDownPaymentPercent / 100)) / 20) + ' ج.م' : '—'"></strong>
                                </div>
                            </div>

                            {{-- Technical Specs Grid --}}
                            <div class="grid grid-cols-2 gap-2 text-[11px]">
                                <div class="rounded-xl border border-white/5 bg-white/[0.03] p-2">
                                    <span class="text-slate-500">{{ __('المساحة') }}</span>
                                    <p class="mt-0.5 font-bold text-white" x-text="selectedUnit.area ? selectedUnit.area + ' م²' : '—'"></p>
                                </div>
                                <div class="rounded-xl border border-white/5 bg-white/[0.03] p-2">
                                    <span class="text-slate-500">{{ __('سعر المتر') }}</span>
                                    <p class="mt-0.5 font-bold text-white" x-text="selectedUnit.price_per_meter ? money(selectedUnit.price_per_meter) + ' ج.م' : '—'"></p>
                                </div>
                                <div class="rounded-xl border border-white/5 bg-white/[0.03] p-2">
                                    <span class="text-slate-500">{{ __('الغرف') }}</span>
                                    <p class="mt-0.5 font-bold text-white" x-text="(selectedUnit.bedrooms || 0) + ' غرف'"></p>
                                </div>
                                <div class="rounded-xl border border-white/5 bg-white/[0.03] p-2">
                                    <span class="text-slate-500">{{ __('الحمامات') }}</span>
                                    <p class="mt-0.5 font-bold text-white" x-text="(selectedUnit.bathrooms || 0) + ' حمام'"></p>
                                </div>
                                <template x-if="selectedUnit.garden_area > 0">
                                    <div class="col-span-2 rounded-xl border border-white/5 bg-white/[0.03] p-2 flex justify-between">
                                        <span class="text-slate-500">{{ __('مساحة الحديقة') }}</span>
                                        <span class="font-bold text-emerald-300" x-text="selectedUnit.garden_area + ' م²'"></span>
                                    </div>
                                </template>
                                <template x-if="selectedUnit.roof_area > 0">
                                    <div class="col-span-2 rounded-xl border border-white/5 bg-white/[0.03] p-2 flex justify-between">
                                        <span class="text-slate-500">{{ __('مساحة الروف') }}</span>
                                        <span class="font-bold text-amber-300" x-text="selectedUnit.roof_area + ' م²'"></span>
                                    </div>
                                </template>
                            </div>

                            {{-- Floor Plan Preview --}}
                            <template x-if="selectedUnit.floor_plan">
                                <div class="space-y-1">
                                    <p class="text-[10px] font-bold uppercase text-slate-400">{{ __('المسقط الأفقي / المخطط الهندسي') }}</p>
                                    <button
                                        type="button"
                                        @click="zoomFloorPlan = selectedUnit.floor_plan"
                                        class="group relative block w-full overflow-hidden rounded-xl border border-white/10 bg-slate-950 p-1 text-center transition hover:border-brand-400"
                                    >
                                        <img :src="selectedUnit.floor_plan" alt="" class="h-24 w-full object-contain rounded-lg transition group-hover:scale-105">
                                        <span class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 group-hover:opacity-100 transition rounded-lg text-white font-bold text-[10px] gap-1">
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="7" stroke-width="2"/><path d="m21 21-4.3-4.3M11 8v6M8 11h6" stroke-width="2"/></svg>
                                            {{ __('تكبير المخطط') }}
                                        </span>
                                    </button>
                                </div>
                            </template>

                            {{-- Actions --}}
                            <div class="grid grid-cols-2 gap-2 pt-1">
                                <a :href="selectedUnit.edit_url" class="app-button flex h-[34px] items-center justify-center py-2" title="{{ __('تعديل بيانات الوحدة') }}">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" stroke-width="2"/></svg>
                                </a>
                                <a :href="selectedUnit.calculator_url" target="_blank" class="app-button--ghost flex h-[34px] w-full items-center justify-center rounded-lg py-2" title="{{ __('حاسبة التقسيط') }}">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="4" y="3" width="16" height="18" rx="2" stroke-width="2"/><path d="M8 7h8M8 11h2M8 15h2M8 19h2M12 15h4M12 11h4M12 19h4" stroke-width="1.8" stroke-linecap="round"/></svg>
                                </a>
                            </div>
                        </div>
                    </template>

                    {{-- Empty State (No Unit Selected) --}}
                    <template x-if="!selectedUnit">
                        <div class="rounded-2xl border border-dashed border-white/10 bg-white/[0.02] p-8 text-center">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-500/10 text-brand-400 ring-1 ring-brand-500/20">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 4h16v16H4zM4 12h16M12 4v16" stroke-width="1.8"/></svg>
                            </div>
                            <h4 class="mt-3 text-sm font-bold text-white">{{ __('اختر أي وحدة لمعاينتها') }}</h4>
                            <p class="mt-1 text-xs leading-5 text-slate-400">
                                {{ __('اضغط على أي شقة في الواجهات أو المصفوفة لعرض كافة مواصفاتها وسعرها ومخططها الهندسي.') }}
                            </p>
                        </div>
                    </template>
                </div>
            </aside>
        </div>

        {{-- 6. Floor Plan Zoom Modal --}}
        <div
            x-show="zoomFloorPlan"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 backdrop-blur-md"
            @click.self="zoomFloorPlan = null"
            @keydown.escape.window="zoomFloorPlan = null"
        >
            <div class="relative max-h-[90vh] max-w-4xl overflow-hidden rounded-3xl border border-white/20 bg-slate-900 p-4 shadow-2xl">
                <button
                    type="button"
                    @click="zoomFloorPlan = null"
                    class="absolute top-4 right-4 z-10 flex h-9 w-9 items-center justify-center rounded-full bg-slate-950/80 text-white hover:bg-rose-600 transition"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 6 6 18M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg>
                </button>
                <img :src="zoomFloorPlan" alt="Floor plan zoom" class="max-h-[80vh] w-full object-contain rounded-2xl">
            </div>
        </div>

        {{-- Print Settings Modal --}}
        <div
            x-show="printDialogOpen"
            x-cloak
            class="fixed inset-0 z-[80] flex items-center justify-center bg-black/70 p-4 backdrop-blur-md"
            @click.self="printDialogOpen = false"
            @keydown.escape.window="printDialogOpen = false"
        >
            <div class="w-full max-w-lg rounded-3xl border border-white/15 bg-slate-900 p-6 shadow-2xl">
                <div class="flex items-start justify-between gap-4 border-b border-white/10 pb-4">
                    <div>
                        <p class="mobile-section-title">{{ __('Print Center') }}</p>
                        <h2 class="mt-1 text-xl font-black text-white">{{ __('طباعة حصر المشروع') }}</h2>
                        <p class="mt-1 text-xs text-slate-400">{{ __('اختر اتجاه الورق لطباعة تقرير A4 احترافي.') }}</p>
                    </div>
                    <button type="button" @click="printDialogOpen = false" class="rounded-xl bg-white/5 p-2 text-slate-400 hover:bg-white/10 hover:text-white">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 6 6 18M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                </div>

                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    <button type="button" @click="printOrientation = 'portrait'" class="rounded-2xl border p-4 text-start transition" :class="printOrientation === 'portrait' ? 'border-brand-400 bg-brand-500/15 ring-2 ring-brand-400/30' : 'border-white/10 bg-white/5 hover:bg-white/10'">
                        <div class="flex items-center gap-3">
                            <span class="flex h-12 w-9 items-center justify-center rounded border-2 border-brand-300/70 bg-brand-500/10"><span class="h-7 w-5 rounded-sm border border-brand-300/60"></span></span>
                            <span><strong class="block text-sm text-white">{{ __('A4 Portrait') }}</strong><small class="text-xs text-slate-400">{{ __('عمودي — مناسب للبيانات المختصرة') }}</small></span>
                        </div>
                    </button>
                    <button type="button" @click="printOrientation = 'landscape'" class="rounded-2xl border p-4 text-start transition" :class="printOrientation === 'landscape' ? 'border-brand-400 bg-brand-500/15 ring-2 ring-brand-400/30' : 'border-white/10 bg-white/5 hover:bg-white/10'">
                        <div class="flex items-center gap-3">
                            <span class="flex h-9 w-12 items-center justify-center rounded border-2 border-brand-300/70 bg-brand-500/10"><span class="h-5 w-8 rounded-sm border border-brand-300/60"></span></span>
                            <span><strong class="block text-sm text-white">{{ __('A4 Landscape') }}</strong><small class="text-xs text-slate-400">{{ __('أفقي — مناسب للحصر الكامل') }}</small></span>
                        </div>
                    </button>
                </div>

                <div class="mt-5 rounded-2xl border border-white/10 bg-white/[0.03] p-4 text-xs text-slate-400">
                    <div class="flex items-center justify-between"><span>{{ __('المشروع') }}</span><strong class="text-white">{{ $project->name }}</strong></div>
                    <div class="mt-2 flex items-center justify-between"><span>{{ __('عدد العمارات') }}</span><strong class="text-white" x-text="buildings.length"></strong></div>
                    <div class="mt-2 flex items-center justify-between"><span>{{ __('إجمالي الوحدات') }}</span><strong class="text-white" x-text="totalUnits"></strong></div>
                </div>

                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" @click="printDialogOpen = false" class="app-button--ghost text-xs">{{ __('إلغاء') }}</button>
                    <button type="button" @click="printReport()" class="app-button gap-2 text-xs">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" stroke-width="1.8" stroke-linecap="round"/><path d="M6 14h12v8H6z" stroke-width="1.8"/></svg>
                        {{ __('طباعة التقرير') }}
                    </button>
                </div>
            </div>
        </div>

        {{-- A4 Inventory Print Report --}}
        <section id="project-print-report" dir="rtl" :class="printOrientation">
            <header class="print-report-header">
                <div>
                    <p class="print-report-kicker">VENECIA DEVELOPMENTS · {{ __('Inventory Report') }}</p>
                    <h1>{{ $project->name }}</h1>
                    <p>{{ $project->location ?: __('Project inventory and unit availability statement') }}</p>
                </div>
                <div class="print-report-meta">
                    <strong>{{ now()->format('Y-m-d') }}</strong>
                    <span>{{ __('Generated from project control panel') }}</span>
                </div>
            </header>

            <div class="print-report-summary">
                <div><span>{{ __('Buildings') }}</span><strong x-text="buildings.length"></strong></div>
                <div><span>{{ __('Total Units') }}</span><strong x-text="totalUnits"></strong></div>
                <div><span>{{ __('Available') }}</span><strong class="available" x-text="availableUnits.length"></strong></div>
                <div><span>{{ __('Reserved') }}</span><strong class="reserved" x-text="reservedUnits.length"></strong></div>
                <div><span>{{ __('Sold') }}</span><strong class="sold" x-text="soldUnits.length"></strong></div>
            </div>

            <div class="print-buildings-grid">
                <template x-for="(building, buildingIndex) in buildings" :key="'print-building-' + building.id">
                <section class="print-building">
                    <div class="print-building-heading">
                        <div class="print-building-title">
                            <span class="print-building-mark" x-text="building.name"></span>
                            <div><strong x-text="'{{ __('Building') }} ' + building.name"></strong><span x-text="building.code || ''"></span></div>
                        </div>
                        <div class="print-building-stats"><span x-text="building.floors.length + ' {{ __('Floors') }}'"></span><b x-text="building.available_count + ' {{ __('Available') }}'"></b></div>
                    </div>

                    <div class="print-floors">
                        <template x-for="floor in building.floors" :key="'print-floor-' + floor.id">
                            <div class="print-floor print-avoid-break">
                                <div class="print-floor-heading">
                                    <span class="print-floor-label"><span class="print-floor-number" x-text="floor.number"></span><strong x-text="floor.name"></strong></span>
                                    <span class="print-floor-count" x-text="floor.units.length + ' {{ __('Units') }} · ' + floor.units.filter(u => u.status === 'available').length + ' {{ __('Available') }}'"></span>
                                </div>
                                <div class="print-units-grid">
                                    <template x-for="unit in floor.units" :key="'print-unit-' + unit.id">
                                        <div class="print-unit" :class="unit.status">
                                            <div class="print-unit-top"><strong x-text="unit.number"></strong><span class="print-status-dot"></span></div>
                                            <div class="print-unit-type" x-text="unit.type || '{{ __('شقة') }}'"></div>
                                            <div class="print-unit-details"><span x-text="unit.area ? unit.area + ' م²' : '—'"></span><span x-text="(unit.bedrooms || 0) + ' غ / ' + (unit.bathrooms || 0) + ' ح'"></span></div>
                                            <div class="print-unit-bottom"><b x-text="unit.price ? money(unit.price) + ' ج.م' : '—'"></b><span class="print-status" :class="unit.status" x-text="statusLabels[unit.status] || unit.status"></span></div>
                                        </div>
                                    </template>
                                    <template x-if="floor.units.length === 0">
                                        <div class="print-empty-floor">{{ __('لا توجد وحدات مسجلة في هذا الدور') }}</div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </section>
                </template>
            </div>

            <footer class="print-report-footer">
                <span>{{ __('Confidential — Internal inventory statement') }}</span>
                <span>{{ $project->name }} · {{ __('Page generated automatically') }}</span>
            </footer>
        </section>
    </div>

    <script>
        window.printProjectReport = function (report, orientation) {
            const reportHtml = report.outerHTML;
            const styleTag = document.getElementById('project-print-report-style');
            let cssText = styleTag ? styleTag.textContent : '';
            cssText = cssText
                .replace(/#project-print-report\s*\{\s*display:\s*none\s*;?\s*\}/g, '')
                .replace(/body\.printing-layout\s*>\s*\*\s*\{[^}]*\}/g, '')
                .replace(/body\.printing-layout\s*>\s*#project-print-report\s*\{[^}]*\}/g, '')
                .replace(/body\.printing-layout\s*\{\s*[^}]*\}/g, '');

            const iframe = document.createElement('iframe');
            iframe.setAttribute('title', 'Project inventory print preview');
            iframe.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0;visibility:hidden;';
            document.body.appendChild(iframe);

            const printDocument = '<!doctype html>' +
                '<html dir="rtl" lang="ar"><head><meta charset="utf-8">' +
                '<style>' +
                '@page { size: A4 ' + (orientation === 'portrait' ? 'portrait' : 'landscape') + '; margin: 8mm; }' +
                'html,body { margin:0; padding:0; background:#fff; color:#111; font-family:Tahoma,Arial,sans-serif; }' +
                '#project-print-report { display:block !important; }' + cssText +
                '</style></head><body>' + reportHtml + '</body></html>';

            const frame = iframe.contentWindow;
            const doc = frame.document;
            let printed = false;
            const cleanup = function () {
                if (iframe.parentNode) iframe.parentNode.removeChild(iframe);
            };
            const print = function () {
                if (printed) return;
                printed = true;
                frame.focus();
                frame.print();
                window.setTimeout(cleanup, 1500);
            };

            iframe.addEventListener('load', function () { window.setTimeout(print, 150); }, { once: true });
            doc.open();
            doc.write(printDocument);
            doc.close();
            window.setTimeout(print, 1000);
        };
    </script>
@endsection
