<?php

declare(strict_types=1);

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\DocumentRequest;
use App\Models\Customer;
use App\Models\Document;
use App\Models\Lead;
use App\Models\Offer;
use App\Models\Reservation;
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

        $documentable = match ($request->string('documentable_type')->toString()) {
            Lead::class => Lead::query()->find($request->integer('documentable_id')),
            Customer::class => Customer::query()->find($request->integer('documentable_id')),
            Offer::class => Offer::query()->find($request->integer('documentable_id')),
            Reservation::class => Reservation::query()->find($request->integer('documentable_id')),
            default => null,
        };

        abort_unless($documentable, 404);
        $this->authorize('view', $documentable);

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
