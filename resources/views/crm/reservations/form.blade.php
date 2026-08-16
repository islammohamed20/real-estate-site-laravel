@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    @include('crm.partials.crm-nav')
    <section class="dashboard-hero-card p-6 sm:p-8">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ $reservation ? __('Edit Reservation') : __('New Reservation') }}</h1>
        </div>
    </section>

    <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-lg p-4 sm:p-8">
        <form action="{{ $reservation ? route('dashboard.crm.reservations.update', $reservation) : route('dashboard.crm.reservations.store') }}" method="POST" class="space-y-4">
            @csrf
            @if ($reservation) @method('PUT') @endif

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-300">{{ __('Customer') }}</label>
                    <select name="customer_id" class="form-select w-full rounded-xl text-sm">
                        <option value="">—</option>
                        @foreach ($customers as $id => $name)
                            <option value="{{ $id }}" {{ ($reservation?->customer_id == $id) ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-300">{{ __('Lead') }}</label>
                    <select name="lead_id" class="form-select w-full rounded-xl text-sm">
                        <option value="">—</option>
                        @foreach ($leads as $id => $name)
                            <option value="{{ $id }}" {{ ($reservation?->lead_id == $id) ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-300">{{ __('Unit') }}</label>
                    <select name="unit_id" class="form-select w-full rounded-xl text-sm" required>
                        @foreach ($units as $id => $label)
                            <option value="{{ $id }}" {{ ($reservation?->unit_id == $id) ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-300">{{ __('Deposit amount') }}</label>
                    <input type="number" step="0.01" name="deposit_amount" value="{{ $reservation?->deposit_amount }}" class="form-input w-full rounded-xl text-sm" required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-300">{{ __('Reserved at') }}</label>
                    <input type="datetime-local" name="reserved_at" value="{{ $reservation?->reserved_at?->format('Y-m-d\TH:i') }}" class="form-input w-full rounded-xl text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-300">{{ __('Expires at') }}</label>
                    <input type="datetime-local" name="expires_at" value="{{ $reservation?->expires_at?->format('Y-m-d\TH:i') }}" class="form-input w-full rounded-xl text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-300">{{ __('Status') }}</label>
                    <select name="status" class="form-select w-full rounded-xl text-sm">
                        @foreach (['pending', 'paid', 'converted', 'cancelled', 'expired'] as $status)
                            <option value="{{ $status }}" {{ ($reservation?->status ?? 'pending') === $status ? 'selected' : '' }}>{{ __(ucfirst($status)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-300">{{ __('Notes') }}</label>
                <textarea name="notes" rows="4" class="form-textarea w-full rounded-xl text-sm">{{ $reservation?->notes }}</textarea>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('dashboard.crm.reservations.index') }}" class="app-button--ghost">{{ __('Cancel') }}</a>
                <button type="submit" class="app-button">{{ $reservation ? __('Update') : __('Create') }}</button>
            </div>
        </form>
    </section>
</div>
@endsection
