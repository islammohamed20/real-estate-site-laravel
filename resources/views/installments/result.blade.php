@extends($layout ?? 'layouts.public')

@section('content')
    <div class="animate-fade-up space-y-6 pt-6">
        <div class="mx-auto grid w-full items-start gap-4 lg:w-fit lg:grid-cols-[auto_auto] lg:gap-6">
            {{-- Your payment plan --}}
            <section class="app-card min-w-0 space-y-4">
                <div>
                    <p class="mobile-section-title">{{ __('Calculation result') }}</p>
                    <h1 class="mt-2 text-2xl font-bold tracking-tight text-white sm:text-3xl">{{ __('Your payment plan') }}</h1>
                    <p class="mt-1 text-sm text-slate-400">{{ __('Review the summary below, then generate a PDF or save to CRM.') }}</p>
                </div>

                @if ($unit)
                    <div class="flex flex-wrap items-center gap-3 rounded-2xl border border-brand-500/20 bg-brand-500/10 px-4 py-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-500/15 text-brand-300">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 11.5 12 4l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-8.5Z" stroke-width="1.8" stroke-linejoin="round"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-white">{{ $unit->unit_number }} <span class="font-normal text-slate-400">·</span> {{ $unit->unit_type }}</p>
                            <p class="text-xs text-slate-400">{{ $unit->project?->name }} — {{ number_format((float) $unit->area) }} {{ __('m²') }}</p>
                        </div>
                        <a href="{{ route('public.units.show', $unit->unit_number) }}" class="ms-auto inline-flex shrink-0 items-center gap-1 text-xs font-semibold text-brand-300 transition hover:text-brand-200">
                            {{ __('عرض الوحدة') }}
                            <svg class="h-3.5 w-3.5 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12h14M13 6l6 6-6 6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-2.5">
                    @foreach ([
                        __('Base Price') => $result['base_price'],
                        __('Excellence %') => $result['excellence_percent'] . '%',
                        __('Excellence Amount') => $result['excellence_amount'],
                        __('Base Price with Excellence') => $result['base_price_with_excellence'],
                        __('Discount') => $result['down_payment_bonus_percent'] . '% · ' . number_format((float) $result['discount_amount'], 2),
                        __('Final Price') => $result['final_price'],
                        __('Maintenance %') => $result['maintenance_percent'] . '%',
                        __('Maintenance Deposit') => $result['maintenance_deposit'],
                        __('Remaining') => $result['remaining'],
                        __('Installment Amount') => $result['installment_amount'],
                    ] as $label => $value)
                        <div class="touch-card min-w-0 p-3">
                            <p class="touch-card__label min-w-0">{{ $label }}</p>
                            <p class="mt-1.5 min-w-0 break-words text-sm font-bold text-white sm:text-base">{{ is_numeric($value) ? number_format((float) $value, 2) : $value }}</p>
                        </div>
                    @endforeach

                    @php($downPaymentPercent = (float) ($input['down_payment_percent'] ?? 0))
                    <div class="touch-card min-w-0 p-3 border-emerald-500/30 bg-emerald-500/10">
                        <p class="touch-card__label min-w-0">
                            {{ $result['is_cash'] ? __('Cash Payment') : __('Down Payment') }}
                            @if (! $result['is_cash'] && $downPaymentPercent > 0)
                                <span class="badge badge-success">{{ number_format($downPaymentPercent, 1) }}%</span>
                            @endif
                        </p>
                        <p class="mt-1.5 min-w-0 break-words text-sm font-bold text-emerald-300 sm:text-base">{{ number_format((float) $result['down_payment'], 2) }}</p>
                    </div>
                </div>

                <div class="rounded-xl bg-amber-500/10 p-4 text-sm text-amber-200/80 border border-amber-500/20">
                    {{ __('Maintenance amount is paid during the contract period before delivery.') }}
                </div>
            </section>

            {{-- Actions card --}}
            <aside class="min-w-0 space-y-4">
                <section class="app-card sticky top-24 min-w-0 space-y-4"
                         x-data="{
                             customers: {{ Js::from($customersInfo->map(fn ($c) => ['id' => (int) $c->id, 'name' => $c->name, 'phone' => $c->phone])->values()->all()) }},
                             offers: {{ Js::from($pdfOffers->map(fn ($o) => ['id' => (int) $o->id, 'number' => $o->offer_number, 'customer_id' => (int) $o->customer_id])->values()->all()) }},
                             pdfCustomerId: {{ (int) ($input['customer_id'] ?? 0) }},
                             saveCustomerId: {{ (int) ($input['customer_id'] ?? 0) }},
                             offerId: {{ (int) ($input['offer_id'] ?? 0) }},
                             leadModalOpen: false,
                             leadSaving: false,
                             leadMessage: '',
                             leadForm: { name: '', phone: '', whatsapp: '', email: '', notes: '' },
                             get selectedCustomer() { return this.customers.find(c => c.id === this.pdfCustomerId); },
                             get filteredOffers() { return this.offers.filter(o => o.customer_id === this.pdfCustomerId); },
                             async createLead() {
                                 this.leadSaving = true;
                                 this.leadMessage = '';
                                 try {
                                     const csrfToken = document.querySelector('meta[name=\'csrf-token\']')?.content;
                                     if (!csrfToken) {
                                         this.leadMessage = '{{ __('Unable to submit the lead right now. Please refresh the page and try again.') }}';
                                         return;
                                     }

                                     const fd = new FormData();
                                     fd.append('_token', csrfToken);
                                     Object.entries(this.leadForm).forEach(([k, v]) => fd.append(k, v));
                                     const res = await fetch('{{ route($calculatorRoutes['lead']) }}', {
                                         method: 'POST',
                                         body: fd,
                                         credentials: 'same-origin',
                                         headers: {
                                             'Accept': 'application/json',
                                             'X-CSRF-TOKEN': csrfToken,
                                             'X-Requested-With': 'XMLHttpRequest',
                                         },
                                     });

                                     if (res.status === 419) {
                                         this.leadMessage = '{{ __('Your session expired. Please refresh the page and try again.') }}';
                                         return;
                                     }

                                     const data = await res.json().catch(() => ({}));
                                     if (! res.ok || ! data.ok) {
                                         this.leadMessage = data.message || '{{ __('Failed to save the lead.') }}';
                                         return;
                                     }
                                     if (! this.customers.some(c => c.id === data.customer_id)) {
                                         this.customers.push({ id: data.customer_id, name: data.name, phone: data.phone });
                                     }
                                     this.saveCustomerId = data.customer_id;
                                     this.pdfCustomerId = data.customer_id;
                                     this.offerId = 0;
                                     this.leadModalOpen = false;
                                     this.leadForm = { name: '', phone: '', whatsapp: '', email: '', notes: '' };
                                     this.leadMessage = '{{ __('Lead saved — it now appears in CRM Leads and can be selected for the PDF.') }}';
                                 } catch (e) {
                                     this.leadMessage = '{{ __('Failed to save the lead.') }}';
                                 } finally {
                                     this.leadSaving = false;
                                 }
                             },
                         }">
                    <div>
                        <p class="mobile-section-title">{{ __('Actions') }}</p>
                        <h2 class="mt-1 text-lg font-semibold text-white">{{ __('Plan actions') }}</h2>
                    </div>

                    @if (auth('web')->check() || auth('customer')->check())
                        <form id="installment-pdf-form" method="POST" action="{{ route($calculatorRoutes['pdf']) }}" class="space-y-4">
                            @csrf
                            @foreach ($input as $key => $value)
                                @continue(in_array($key, ['offer_id', 'customer_id'], true))
                                <input type="hidden" name="{{ $key }}" value="{{ is_bool($value) ? (int) $value : $value }}">
                            @endforeach

                            @if ($customerPortal)
                                {{-- Customer portal: one account = one linked customer, shown automatically (no dropdown) --}}
                                <div class="space-y-4 rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <input type="hidden" name="customer_id" :value="pdfCustomerId">
                                    <template x-if="selectedCustomer">
                                        <div class="flex items-center gap-2 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-3 py-2 text-xs text-emerald-200">
                                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="9" cy="7" r="4" stroke-width="1.8"/><path d="M19 8v6M22 11h-6" stroke-width="1.8" stroke-linecap="round"/></svg>
                                            <span class="min-w-0"><strong x-text="selectedCustomer.name"></strong> <span class="text-emerald-400/70">·</span> <span class="ltr" x-text="selectedCustomer.phone"></span></span>
                                        </div>
                                    </template>
                                    <label class="block space-y-1.5">
                                        <span class="text-xs font-medium text-slate-300">{{ __('Related Offer') }}</span>
                                        <select name="offer_id" x-model="offerId" class="app-select">
                                            <option value="0">{{ __('No offer') }}</option>
                                            <template x-for="o in filteredOffers" :key="o.id">
                                                <option :value="o.id" x-text="o.number"></option>
                                            </template>
                                        </select>
                                    </label>
                                </div>
                            @else
                                <div class="space-y-4 rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <p class="text-sm font-semibold text-white">{{ __('PDF settings') }}</p>

                                    <label class="block space-y-1.5">
                                        <span class="text-xs font-medium text-slate-300">{{ __('PDF addressed to') }}</span>
                                        <select name="customer_id" x-model="pdfCustomerId" class="app-select" @change="offerId = 0">
                                            <option value="0">{{ __('No customer') }}</option>
                                            <template x-for="c in customers" :key="c.id">
                                                <option :value="c.id" x-text="c.name"></option>
                                            </template>
                                        </select>
                                        <template x-if="selectedCustomer">
                                            <div class="flex items-center gap-2 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-3 py-2 text-xs text-emerald-200">
                                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="9" cy="7" r="4" stroke-width="1.8"/><path d="M19 8v6M22 11h-6" stroke-width="1.8" stroke-linecap="round"/></svg>
                                                <span class="min-w-0"><strong x-text="selectedCustomer.name"></strong> <span class="text-emerald-400/70">·</span> <span class="ltr" x-text="selectedCustomer.phone"></span></span>
                                            </div>
                                        </template>
                                    </label>

                                    <label class="block space-y-1.5">
                                        <span class="text-xs font-medium text-slate-300">{{ __('Related Offer') }}</span>
                                        <select name="offer_id" x-model="offerId" class="app-select">
                                            <option value="0">{{ __('No offer') }}</option>
                                            <template x-for="o in filteredOffers" :key="o.id">
                                                <option :value="o.id" x-text="o.number"></option>
                                            </template>
                                        </select>
                                    </label>
                                </div>
                            @endif

                        </form>

                        @if (! $customerPortal)
                        <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/5 p-4">
                            <p class="mb-3 text-sm font-semibold text-white">{{ __('Save plan to CRM') }}</p>

                            <div x-show="leadMessage" x-cloak class="mb-3 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-3 py-2 text-xs text-emerald-200" x-text="leadMessage"></div>

                            <form id="installment-save-form" method="POST" action="{{ route($calculatorRoutes['save']) }}" class="space-y-4">
                                @csrf
                                @foreach ($input as $key => $value)
                                    @continue(in_array($key, ['customer_id', 'offer_id', 'save_to_crm'], true))
                                    <input type="hidden" name="{{ $key }}" value="{{ is_bool($value) ? (int) $value : $value }}">
                                @endforeach

                                <div class="space-y-4 rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <p class="text-sm font-semibold text-white">{{ __('CRM settings') }}</p>

                                    <label class="block space-y-1.5">
                                        <span class="text-xs font-medium text-slate-300">{{ __('Customer') }} <span class="text-rose-400">*</span></span>
                                        <select name="customer_id" x-model="saveCustomerId" required class="app-select" @error('customer_id') aria-invalid="true" @enderror>
                                            <option value="">{{ __('Select customer') }}</option>
                                            <template x-for="c in customers" :key="c.id">
                                                <option :value="c.id" x-text="c.name"></option>
                                            </template>
                                        </select>
                                        @error('customer_id')
                                            <p class="text-xs text-rose-400">{{ $message }}</p>
                                        @enderror
                                    </label>

                                    <label class="block space-y-1.5">
                                        <span class="text-xs font-medium text-slate-300">{{ __('Link to offer') }}</span>
                                        <select name="offer_id" class="app-select">
                                            <option value="">{{ __('None — save plan only') }}</option>
                                            @forelse ($offers as $oid => $oname)
                                                <option value="{{ $oid }}" @selected(old('offer_id') == $oid)>{{ $oname }}</option>
                                            @empty
                                                <option value="" disabled>{{ __('No offers for this unit yet') }}</option>
                                            @endforelse
                                        </select>
                                        <p class="text-[11px] text-slate-500">{{ __('Saving to an offer links the plan and its schedule to it.') }}</p>
                                    </label>
                                </div>

                            </form>

                            <div class="mt-4 space-y-3">
                                <button type="button" @click="leadModalOpen = true" class="flex w-full items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold text-slate-200 transition hover:bg-white/10">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke-width="1.8" stroke-linecap="round"/></svg>
                                    {{ __('Save new lead') }}
                                </button>
                            </div>

                            {{-- Quick lead modal --}}
                            <div x-show="leadModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="leadModalOpen = false">
                                <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm" @click="leadModalOpen = false"></div>
                                <div class="relative w-full max-w-md space-y-4 rounded-3xl border border-white/10 bg-slate-900 p-6 shadow-2xl">
                                    <div class="flex items-center justify-between gap-3">
                                        <h3 class="text-lg font-semibold text-white">{{ __('Quick Lead') }}</h3>
                                        <button type="button" @click="leadModalOpen = false" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/5 text-slate-300 transition hover:bg-white/10" aria-label="{{ __('Close') }}">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 6 6 18M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg>
                                        </button>
                                    </div>
                                    <form @submit.prevent="createLead()" class="space-y-3">
                                        <label class="block space-y-1.5">
                                            <span class="text-xs font-medium text-slate-300">{{ __('Name') }} <span class="text-rose-400">*</span></span>
                                            <input type="text" x-model="leadForm.name" required class="app-input" placeholder="{{ __('Name') }}">
                                        </label>
                                        <label class="block space-y-1.5">
                                            <span class="text-xs font-medium text-slate-300">{{ __('Phone') }} <span class="text-rose-400">*</span></span>
                                            <input type="tel" x-model="leadForm.phone" required class="app-input ltr" placeholder="01xxxxxxxxx">
                                        </label>
                                        <div class="grid grid-cols-2 gap-3">
                                            <label class="block space-y-1.5">
                                                <span class="text-xs font-medium text-slate-300">{{ __('WhatsApp') }}</span>
                                                <input type="tel" x-model="leadForm.whatsapp" class="app-input ltr">
                                            </label>
                                            <label class="block space-y-1.5">
                                                <span class="text-xs font-medium text-slate-300">{{ __('Email') }}</span>
                                                <input type="email" x-model="leadForm.email" class="app-input ltr">
                                            </label>
                                        </div>
                                        <label class="block space-y-1.5">
                                            <span class="text-xs font-medium text-slate-300">{{ __('Notes') }}</span>
                                            <textarea x-model="leadForm.notes" rows="3" class="app-input resize-none"></textarea>
                                        </label>
                                        <div x-show="leadMessage && ! leadSaving" x-cloak class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-3 py-2 text-xs text-rose-200" x-text="leadMessage"></div>
                                        <button type="submit" :disabled="leadSaving" class="app-button w-full">
                                            <span x-show="! leadSaving">{{ __('Save') }}</span>
                                            <span x-show="leadSaving">{{ __('Saving…') }}</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="space-y-3">
                            <button type="submit" form="installment-pdf-form" class="app-button w-full">{{ __('Generate PDF') }}</button>
                            @if (! $customerPortal)
                                <button type="submit" form="installment-save-form" class="app-button w-full">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z" stroke-width="1.8"/><path d="M17 21v-8H7v8M7 3v5h8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    {{ __('Save to CRM') }}
                                </button>
                            @endif
                            <a href="{{ route($calculatorRoutes['index'], $input) }}" class="app-button--ghost w-full">{{ __('Edit inputs') }}</a>
                        </div>
                    @else
                        <div class="space-y-3">
                            <p class="text-sm text-slate-400">{{ __('Save the plan to your customer account') }}</p>
                            <a href="{{ route('customer.login', ['redirect' => url()->current()]) }}" class="app-button w-full">{{ __('Log in') }}</a>
                            <a href="{{ route('customer.register', ['redirect' => url()->current()]) }}" class="app-button--ghost w-full">{{ __('Create new account') }}</a>
                        </div>
                    @endif
                </section>
            </aside>
        </div>

        <section class="app-card overflow-hidden p-0">
            <div class="border-b border-white/10 px-5 py-4">
                <p class="mobile-section-title">{{ __('Schedule') }}</p>
                <h2 class="mt-1 text-lg font-semibold text-white">{{ $result['is_cash'] ? __('Cash Payment') : count($result['schedule']).' '.__('installments') }}</h2>
            </div>
            @if ($result['is_cash'] || count($result['schedule']) === 0)
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
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result['schedule'] as $row)
                                <tr>
                                    <td class="font-semibold text-white">{{ $row['installment_number'] }}</td>
                                    <td>{{ $row['due_date'] }}</td>
                                    <td class="font-semibold text-white">{{ number_format((float) $row['amount'], 2) }}</td>
                                    <td class="text-slate-400">{{ number_format((float) $row['balance_after'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection
