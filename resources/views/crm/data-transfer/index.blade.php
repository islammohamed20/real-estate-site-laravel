@extends('layouts.dashboard')

@section('content')
    <div class="space-y-6" x-data="{ type: '{{ $type }}' }">
        <section class="dashboard-hero-card p-5 sm:p-6">
            <h1 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">{{ __('Import & Export CRM Data') }}</h1>
            <p class="mt-1 text-sm text-slate-400">{{ __('Choose the record type and columns. Files use UTF-8 CSV format and phone is the matching key.') }}</p>
        </section>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('dashboard.crm.data-transfer.index', ['type' => 'leads']) }}" class="rounded-xl px-4 py-2 text-sm font-semibold {{ $type === 'leads' ? 'bg-brand-600 text-white' : 'bg-white/5 text-slate-400' }}">{{ __('Potential customers (Leads)') }}</a>
            <a href="{{ route('dashboard.crm.data-transfer.index', ['type' => 'customers']) }}" class="rounded-xl px-4 py-2 text-sm font-semibold {{ $type === 'customers' ? 'bg-brand-600 text-white' : 'bg-white/5 text-slate-400' }}">{{ __('Customers') }}</a>
        </div>

        <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">
            <section class="app-card app-card--gradient p-5 sm:p-6">
                <h2 class="text-base font-bold text-white">{{ __('Export') }}</h2>
                <p class="mt-1 text-xs text-slate-400">{{ __('Select the columns to include in the CSV file.') }}</p>
                <div class="mt-5 flex flex-wrap gap-2">
                    <a href="{{ route('dashboard.crm.data-transfer.template', ['type' => $type]) }}" class="app-button--ghost inline-flex text-xs">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M8 13h8M8 17h6" stroke-linecap="round"/></svg>
                        {{ __('Download Excel template') }}
                    </a>
                </div>
                <form method="GET" action="{{ route('dashboard.crm.data-transfer.export') }}" class="mt-4">
                    <input type="hidden" name="type" value="{{ $type }}">
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                        @foreach ($columns as $key => $label)
                            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-xs text-slate-300 hover:bg-white/10">
                                <input type="checkbox" name="columns[]" value="{{ $key }}" checked class="h-4 w-4 rounded border-white/20 bg-slate-900 text-brand-500 focus:ring-brand-500">
                                <span class="truncate">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    <button type="submit" class="app-button mt-5">{{ __('Download CSV') }}</button>
                </form>
            </section>

            <section class="app-card app-card--gradient p-5 sm:p-6">
                <h2 class="text-base font-bold text-white">{{ __('Import') }}</h2>
                <p class="mt-1 text-xs leading-relaxed text-slate-400">{{ __('Upload an Excel file (.xlsx/.xls) or CSV with a header row. Existing phone numbers are updated; new numbers are created.') }}</p>
                <a href="{{ route('dashboard.crm.data-transfer.template', ['type' => $type]) }}" class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-brand-300 hover:text-brand-200">{{ __('Download the correct template first') }} ←</a>
                <form method="POST" action="{{ route('dashboard.crm.data-transfer.import') }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">
                    <input type="file" name="file" accept=".xlsx,.xls,.csv,.txt,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,text/csv" required class="block w-full rounded-xl border border-white/10 bg-slate-950/40 px-4 py-3 text-sm text-slate-300 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-600 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-white">
                    <div>
                        <p class="mb-2 text-xs font-semibold text-slate-300">{{ __('Columns to import') }}</p>
                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                            @foreach ($columns as $key => $label)
                                <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-xs text-slate-300 hover:bg-white/10">
                                    <input type="checkbox" name="columns[]" value="{{ $key }}" checked class="h-4 w-4 rounded border-white/20 bg-slate-900 text-brand-500 focus:ring-brand-500">
                                    <span class="truncate">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @error('file') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                    <button type="submit" class="app-button">{{ __('Import CSV') }}</button>
                </form>
            </section>
        </div>

        <div class="rounded-2xl border border-amber-500/20 bg-amber-500/5 p-4 text-xs leading-relaxed text-amber-200/80">
            <strong class="text-amber-300">{{ __('Important:') }}</strong>
            {{ __('Phone is required and is used to detect duplicates. Importing a row with an existing phone updates only the selected columns. Use the exported CSV as a template.') }}
        </div>
    </div>
@endsection
