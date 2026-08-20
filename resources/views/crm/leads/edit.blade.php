@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    @include('crm.partials.crm-nav')

    <section class="dashboard-hero-card p-6 sm:p-8">
        <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-brand-500/20 blur-3xl"></div>
        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-3">
                <span class="badge badge-brand">{{ __('Edit') }} {{ __('Lead') }}</span>
                <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ $lead->name }}</h1>
                <p class="text-sm text-slate-400">{{ __('Update the lead profile. Changes are saved to the customer record.') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('dashboard.crm.leads.show', $lead) }}" class="app-button--ghost text-sm">{{ __('Cancel') }}</a>
            </div>
        </div>
    </section>

    <form method="POST" action="{{ route('dashboard.crm.leads.update', $lead) }}" class="app-card app-card--gradient space-y-5">
        @csrf
        @method('PUT')

        <div class="flex items-center gap-3 border-b border-white/5 pb-4">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-500/15 text-brand-400">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34" stroke-width="1.8" stroke-linecap="round"/><path d="M18 2l4 4-10 10-4 1 1-4z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <div>
                <h2 class="text-lg font-semibold text-white">{{ __('Lead details') }}</h2>
                <p class="text-sm text-slate-400">{{ __('Basic contact information') }}</p>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <label for="name" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Customer name') }} <span class="text-rose-400">*</span></label>
                <input id="name" name="name" class="app-input" value="{{ old('name', $lead->name) }}" required>
                @error('name')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="phone" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Phone number') }} <span class="text-rose-400">*</span></label>
                <input id="phone" name="phone" type="tel" inputmode="tel" class="app-input" value="{{ old('phone', $lead->phone) }}" required>
                @error('phone')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="whatsapp" class="mb-1 block text-sm font-medium text-slate-300">{{ __('WhatsApp') }}</label>
                <input id="whatsapp" name="whatsapp" type="tel" inputmode="tel" class="app-input" value="{{ old('whatsapp', $lead->whatsapp) }}">
                @error('whatsapp')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="email" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Email address') }}</label>
                <input id="email" name="email" type="email" inputmode="email" class="app-input" value="{{ old('email', $lead->email) }}">
                @error('email')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="occupation" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Occupation') }}</label>
                <input id="occupation" name="occupation" class="app-input" value="{{ old('occupation', $lead->occupation) }}">
                @error('occupation')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="address" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Address') }}</label>
                <input id="address" name="address" class="app-input" value="{{ old('address', $lead->address) }}">
                @error('address')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="budget" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Budget') }}</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-slate-500">EGP</span>
                    <input id="budget" name="budget" type="number" step="0.01" min="0" class="app-input pl-12" value="{{ old('budget', $lead->budget) }}">
                </div>
                @error('budget')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex items-center gap-3 border-b border-white/5 pb-4">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-500/15 text-violet-400">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 21h18M3 7v1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7M4 21V4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v17" stroke-width="1.8"/></svg>
            </span>
            <div>
                <h2 class="text-lg font-semibold text-white">{{ __('Sales pipeline') }}</h2>
                <p class="text-sm text-slate-400">{{ __('Stage, priority and assignment') }}</p>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <label for="stage" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Stage') }}</label>
                <select id="stage" name="stage" class="app-input">
                    @foreach ($stages as $value => $label)
                        <option value="{{ $value }}" @selected(old('stage', $lead->stage->value) === $value)>{{ __($label) }}</option>
                    @endforeach
                </select>
                @error('stage')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="priority" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Priority') }}</label>
                <select id="priority" name="priority" class="app-input">
                    @foreach ($priorities as $value => $label)
                        <option value="{{ $value }}" @selected(old('priority', $lead->priority ?? 'normal') === $value)>{{ __($label) }}</option>
                    @endforeach
                </select>
                @error('priority')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="lead_source_id" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Source') }}</label>
                <select id="lead_source_id" name="lead_source_id" class="app-input">
                    <option value="">{{ __('Select source') }}</option>
                    @foreach ($sources as $id => $name)
                        <option value="{{ $id }}" @selected(old('lead_source_id', $lead->lead_source_id) == $id)>{{ $name }}</option>
                    @endforeach
                </select>
                @error('lead_source_id')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="assigned_sales_id" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Assign to sales') }}</label>
                <select id="assigned_sales_id" name="assigned_sales_id" class="app-input">
                    <option value="">{{ __('Unassigned') }}</option>
                    @foreach ($users as $id => $name)
                        <option value="{{ $id }}" @selected(old('assigned_sales_id', $lead->assigned_sales_id) == $id)>{{ $name }}</option>
                    @endforeach
                </select>
                @error('assigned_sales_id')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="campaign" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Campaign') }}</label>
                <input id="campaign" name="campaign" class="app-input" value="{{ old('campaign', $lead->campaign) }}">
                @error('campaign')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="follow_up_at" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Follow up at') }}</label>
                <input id="follow_up_at" name="follow_up_at" type="datetime-local" class="app-input" value="{{ old('follow_up_at', $lead->follow_up_at?->format('Y-m-d\TH:i')) }}">
                @error('follow_up_at')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex items-center gap-3 border-b border-white/5 pb-4">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-400">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke-width="1.8"/><path d="M9 22V12h6v10" stroke-width="1.8"/></svg>
            </span>
            <div>
                <h2 class="text-lg font-semibold text-white">{{ __('Requirements') }}</h2>
                <p class="text-sm text-slate-400">{{ __('What the customer is looking for') }}</p>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <label for="unit_type" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Unit type') }}</label>
                <input id="unit_type" name="unit_type" class="app-input" list="unit-type-options" value="{{ old('unit_type', $lead->unit_type) }}" placeholder="{{ __('e.g. Apartment, Villa') }}">
                <datalist id="unit-type-options">
                    <option value="Apartment">
                    <option value="Villa">
                    <option value="Penthouse">
                    <option value="Duplex">
                    <option value="Studio">
                    <option value="Townhouse">
                    <option value="Twin House">
                    <option value="Chalet">
                    <option value="Office">
                    <option value="Shop">
                    <option value="Land">
                    <option value="Roof">
                    <option value="Other">
                </datalist>
                @error('unit_type')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="bedrooms" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Bedrooms') }}</label>
                <input id="bedrooms" name="bedrooms" type="number" min="0" class="app-input" value="{{ old('bedrooms', $lead->bedrooms) }}">
                @error('bedrooms')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="required_area" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Required area') }} ({{ __('m²') }})</label>
                <input id="required_area" name="required_area" type="number" step="0.01" min="0" class="app-input" value="{{ old('required_area', $lead->required_area) }}">
                @error('required_area')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="preferred_payment_plan" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Preferred payment plan') }}</label>
                <input id="preferred_payment_plan" name="preferred_payment_plan" class="app-input" value="{{ old('preferred_payment_plan', $lead->preferred_payment_plan) }}">
                @error('preferred_payment_plan')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2 lg:col-span-3">
                <label for="notes" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Notes') }}</label>
                <textarea id="notes" name="notes" rows="4" class="app-input min-h-24">{{ old('notes', $lead->notes) }}</textarea>
                @error('notes')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2 lg:col-span-3">
                <label for="preferred_locale" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Preferred email language') }}</label>
                <select id="preferred_locale" name="preferred_locale" class="app-input">
                    <option value="ar" @selected(old('preferred_locale', $lead->preferred_locale ?? 'ar'))>العربية</option>
                    <option value="en" @selected(old('preferred_locale', $lead->preferred_locale) === 'en')>English</option>
                </select>
                @error('preferred_locale')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex items-center gap-3 border-b border-white/5 pb-4">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-500/15 text-sky-400">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z" stroke-width="1.8"/><circle cx="7" cy="7" r="1.5" stroke-width="1.8"/></svg>
            </span>
            <div>
                <h2 class="text-lg font-semibold text-white">{{ __('Interests') }}</h2>
                <p class="text-sm text-slate-400">{{ __('Projects, units and tags') }}</p>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <label for="interested_project_ids" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Interested projects') }}</label>
                <select id="interested_project_ids" name="interested_project_ids[]" multiple class="app-input min-h-28">
                    @foreach ($projects as $id => $name)
                        <option value="{{ $id }}" @selected(in_array($id, old('interested_project_ids', $lead->interestedProjects->pluck('id')->all())))>{{ $name }}</option>
                    @endforeach
                </select>
                @error('interested_project_ids')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="tags" class="mb-1 block text-sm font-medium text-slate-300">{{ __('Tags') }}</label>
                <select id="tags" name="tags[]" multiple class="app-input min-h-28">
                    @foreach ($tags as $id => $name)
                        <option value="{{ $id }}" @selected(in_array($id, old('tags', $lead->tags->pluck('id')->all())))>{{ $name }}</option>
                    @endforeach
                </select>
                @error('tags')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="sticky bottom-3 z-10 flex flex-wrap items-center justify-end gap-2 rounded-2xl border border-white/10 bg-slate-950/70 px-3 py-3 backdrop-blur-xl">
            <a href="{{ route('dashboard.crm.leads.show', $lead) }}" class="app-button--ghost">{{ __('Cancel') }}</a>
            <button type="submit" class="app-button">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" stroke-width="1.8"/><path d="M17 21v-8H7v8M7 3v5h8" stroke-width="1.8" stroke-linecap="round"/></svg>
                {{ __('Save Changes') }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
    <script>
        (function () {
            const phoneInput = document.getElementById('phone');
            if (!phoneInput) return;

            const ignoreId = {{ isset($lead) ? (int) $lead->id : 0 }};
            let box = null;
            let timer = null;

            const createBox = () => {
                box = document.createElement('div');
                box.id = 'duplicate-warning';
                box.style.cssText = 'margin-top:0.9rem;border:1px solid #f59e0b;background:#fef3c7;border-radius:1rem;padding:0.85rem 1rem;color:#78350f;font-size:0.78rem;line-height:1.6;';
                phoneInput.closest('form').insertBefore(box, phoneInput.closest('form').firstChild);
            };

            const show = (data) => {
                const leadCount = (data.leads || []).length;
                const customerCount = (data.customers || []).length;
                if (leadCount === 0 && customerCount === 0) {
                    if (box) { box.remove(); box = null; }
                    return;
                }
                if (!box) createBox();

                let html = '<div style="display:flex;align-items:flex-start;gap:0.6rem;">';
                html += '<span style="font-size:1rem;">⚠️</span><div style="flex:1;min-width:0;">';
                html += '<p style="font-weight:700;margin-bottom:0.35rem;">{{ __('This phone number already exists in the CRM.') }}</p>';

                if (leadCount > 0) {
                    html += '<p style="margin-bottom:0.3rem;opacity:0.9;">{{ __('Existing leads') }}:</p><ul style="list-style:none;margin:0 0 0.5rem;padding:0;display:flex;flex-direction:column;gap:0.3rem;">';
                    data.leads.forEach(l => {
                        html += '<li><a href="' + l.url + '" target="_blank" style="color:#0369a1;text-decoration:underline;">👤 ' + l.name + ' — ' + l.phone + (l.stage ? ' (' + l.stage + ')' : '') + '</a></li>';
                    });
                    html += '</ul>';
                }

                if (customerCount > 0) {
                    html += '<p style="margin-bottom:0.3rem;opacity:0.9;">{{ __('Existing customers') }}:</p><ul style="list-style:none;margin:0 0 0.5rem;padding:0;display:flex;flex-direction:column;gap:0.3rem;">';
                    data.customers.forEach(c => {
                        html += '<li><a href="' + c.url + '" target="_blank" style="color:#047857;text-decoration:underline;">🏢 ' + c.name + ' — ' + c.phone + '</a></li>';
                    });
                    html += '</ul>';
                }

                html += '<p style="opacity:0.85;">{{ __('It is recommended to open the existing record instead of creating a duplicate.') }}</p>';
                html += '</div></div>';
                box.innerHTML = html;
            };

            const check = () => {
                const phone = (phoneInput.value || '').trim();
                if (phone.length < 6) {
                    if (box) { box.remove(); box = null; }
                    return;
                }
                fetch('/real-statement-control/crm/leads/check-duplicate?phone=' + encodeURIComponent(phone) + '&ignore=' + ignoreId, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(r => r.json())
                    .then(show)
                    .catch(() => {});
            };

            phoneInput.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(check, 600);
            });

            check();
        })();
    </script>
@endpush
