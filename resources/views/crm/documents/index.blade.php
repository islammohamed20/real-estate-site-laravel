@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    @include('crm.partials.crm-nav')
    <section class="dashboard-hero-card p-6 sm:p-8">
        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-4">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('Documents') }}</h1>
                    <p class="mt-2 text-sm text-slate-300">{{ __('Files attached to CRM records.') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-lg p-4 sm:p-8">
        <form action="{{ route('dashboard.crm.documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-300">{{ __('Related type') }}</label>
                    <select name="documentable_type" class="form-select w-full rounded-xl text-sm" required>
                        <option value="App\Models\Customer">{{ __('Customer') }}</option>
                        <option value="App\Models\Lead">{{ __('Lead') }}</option>
                        <option value="App\Models\Offer">{{ __('Offer') }}</option>
                        <option value="App\Models\Reservation">{{ __('Reservation') }}</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-300">{{ __('Related ID') }}</label>
                    <input type="number" name="documentable_id" class="form-input w-full rounded-xl text-sm" required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-300">{{ __('File') }}</label>
                    <input type="file" name="file" class="block w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-slate-300" required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-300">{{ __('Notes') }}</label>
                    <input type="text" name="notes" class="form-input w-full rounded-xl text-sm">
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="app-button">{{ __('Upload Document') }}</button>
            </div>
        </form>
    </section>

    <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-lg p-4">
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($documents as $document)
                <div class="app-card app-card--gradient p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="truncate text-sm font-semibold text-white">{{ $document->name }}</h3>
                            <p class="text-xs text-slate-500">{{ $document->documentable?->name ?? $document->documentable?->offer_number ?? $document->documentable?->reservation_number ?? '—' }}</p>
                        </div>
                        <form action="{{ route('dashboard.crm.documents.destroy', $document) }}" method="POST" onsubmit="return confirm('{{ __('Delete this document?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="app-button app-button--danger px-2 py-1 text-xs" style="min-height:0">{{ __('Delete') }}</button>
                        </form>
                    </div>
                    <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="mt-3 inline-block text-sm text-brand-300 hover:text-brand-200">{{ __('Download') }}</a>
                    <p class="mt-1 text-xs text-slate-500">{{ number_format($document->size / 1024, 1) }} KB</p>
                </div>
            @empty
                <div class="col-span-full rounded-2xl border border-white/10 bg-white/5 p-8 text-center text-slate-400">
                    <p>{{ __('No documents found.') }}</p>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $documents->links() }}
        </div>
    </section>
</div>
@endsection
