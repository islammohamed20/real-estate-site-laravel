<?php

declare(strict_types=1);

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\DocumentRequest;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Document::class);

        return view('crm.documents.index', [
            'documents' => Document::query()->with(['documentable', 'uploader'])->latest()->paginate(20),
        ]);
    }

    public function store(DocumentRequest $request): RedirectResponse
    {
        $this->authorize('create', Document::class);

        $file = $request->file('file');
        $path = $file->store('documents', 'public');

        Document::query()->create([
            'documentable_type' => $request->input('documentable_type'),
            'documentable_id' => $request->input('documentable_id'),
            'uploaded_by' => auth()->id(),
            'name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'notes' => $request->input('notes'),
        ]);

        return back()->with('status', __('Document uploaded successfully.'));
    }

    public function destroy(Document $document): RedirectResponse
    {
        $this->authorize('delete', $document);

        $document->delete();

        return back()->with('status', __('Document deleted successfully.'));
    }
}
