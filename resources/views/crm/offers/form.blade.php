@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    @include('crm.partials.crm-nav')
    <section class="dashboard-hero-card p-6 sm:p-8">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ $offer ? __('Edit Offer') : __('New Offer') }}</h1>
        </div>
    </section>

    <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-lg p-4 sm:p-8">
        <form action="{{ $offer ? route('dashboard.crm.offers.update', $offer) : route('dashboard.crm.offers.store') }}" method="POST" class="space-y-4">
            @csrf
            @if ($offer) @method('PUT') @endif

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-300">{{ __('Customer') }}</label>
                    <select name="customer_id" class="form-select w-full rounded-xl text-sm">
                        <option value="">—</option>
                        @foreach ($customers as $id => $name)
                            <option value="{{ $id }}" {{ ($offer?->customer_id == $id) ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-300">{{ __('Lead') }}</label>
                    <select name="lead_id" class="form-select w-full rounded-xl text-sm">
                        <option value="">—</option>
                        @foreach ($leads as $id => $name)
                            <option value="{{ $id }}" {{ ($offer?->lead_id == $id) ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-300">{{ __('Unit') }}</label>
                    <select name="unit_id" class="form-select w-full rounded-xl text-sm" required>
                        @foreach ($units as $id => $label)
                            <option value="{{ $id }}" {{ ($offer?->unit_id == $id) ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-300">{{ __('Subtotal') }}</label>
                    <input type="number" step="0.01" name="subtotal" value="{{ $offer?->subtotal }}" class="form-input w-full rounded-xl text-sm" required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-300">{{ __('Discount') }}</label>
                    <input type="number" step="0.01" name="discount_amount" value="{{ $offer?->discount_amount }}" class="form-input w-full rounded-xl text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-300">{{ __('Total') }}</label>
                    <input type="number" step="0.01" name="total_amount" value="{{ $offer?->total_amount }}" class="form-input w-full rounded-xl text-sm" required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-300">{{ __('Issue date') }}</label>
                    <input type="date" name="issue_date" value="{{ $offer?->issue_date?->format('Y-m-d') }}" class="form-input w-full rounded-xl text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-300">{{ __('Valid until') }}</label>
                    <input type="date" name="valid_until" value="{{ $offer?->valid_until?->format('Y-m-d') }}" class="form-input w-full rounded-xl text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-300">{{ __('Status') }}</label>
                    <select name="status" class="form-select w-full rounded-xl text-sm">
                        @foreach (['draft', 'sent', 'accepted', 'rejected', 'expired'] as $status)
                            <option value="{{ $status }}" {{ ($offer?->status ?? 'draft') === $status ? 'selected' : '' }}>{{ __(ucfirst($status)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-300">{{ __('Notes') }}</label>
                <textarea name="notes" rows="4" class="form-textarea w-full rounded-xl text-sm">{{ $offer?->notes }}</textarea>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('dashboard.crm.offers.index') }}" class="app-button--ghost">{{ __('Cancel') }}</a>
                <button type="submit" class="app-button">{{ $offer ? __('Update') : __('Create') }}</button>
            </div>
        </form>
    </section>
</div>
@endsection
