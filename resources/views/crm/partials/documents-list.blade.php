<form action="{{ route('dashboard.crm.documents.store') }}" method="POST" enctype="multipart/form-data" class="app-card app-card--gradient p-4">
    @csrf
    <input type="hidden" name="documentable_type" value="{{ get_class($documentable) }}">
    <input type="hidden" name="documentable_id" value="{{ $documentable->id }}">
    <div class="grid gap-3 sm:grid-cols-2">
        <input type="file" name="file" class="block w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-slate-300" required>
        <input type="text" name="notes" placeholder="{{ __('Notes') }}" class="form-input w-full rounded-xl text-sm">
    </div>
    <div class="mt-3 flex justify-end">
        <button type="submit" class="app-button text-sm">{{ __('Upload document') }}</button>
    </div>
</form>

<div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
    @forelse ($documentable->documents as $document)
        <div class="app-card app-card--gradient p-4">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-white">{{ $document->name }}</p>
                    <p class="text-xs text-slate-500">{{ number_format($document->size / 1024, 1) }} KB</p>
                </div>
                <form action="{{ route('dashboard.crm.documents.destroy', $document) }}" method="POST" onsubmit="return confirm('{{ __('Delete this document?') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="app-button app-button--danger px-2 py-1 text-xs" style="min-height:0">{{ __('Delete') }}</button>
                </form>
            </div>
            <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="mt-2 inline-block text-sm text-brand-300 hover:text-brand-200">{{ __('Download') }}</a>
            @if ($document->notes)
                <p class="mt-1 text-xs text-slate-400">{{ $document->notes }}</p>
            @endif
        </div>
    @empty
        <div class="col-span-full rounded-2xl border border-white/10 bg-white/5 p-6 text-center text-slate-400">
            <p>{{ __('No documents attached yet.') }}</p>
        </div>
    @endforelse
</div>
