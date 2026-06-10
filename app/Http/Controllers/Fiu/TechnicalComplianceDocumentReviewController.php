<?php

namespace App\Http\Controllers\Fiu;

use App\Http\Controllers\Controller;
use App\Models\TechnicalComplianceDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TechnicalComplianceDocumentReviewController extends Controller
{
    public function index(Request $request): View
    {
        $documents = TechnicalComplianceDocument::query()
            ->with(['folder:id,name', 'institution:id,name', 'uploader:id,name'])
            ->when($request->filled('folder'), fn ($query) => $query->where('technical_compliance_folder_id', $request->integer('folder')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('fiu.technical-compliance.documents.index', [
            'documents' => $documents,
        ]);
    }

    public function updateStatus(Request $request, TechnicalComplianceDocument $document): RedirectResponse
    {
        $this->authorize('review', $document);

        $validated = $request->validate([
            'status' => ['required', 'in:under_review,revision_requested,approved,archived'],
            'comment' => ['nullable', 'string', 'max:3000'],
        ]);

        $document->forceFill([
            'status' => $validated['status'],
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'archived_at' => $validated['status'] === TechnicalComplianceDocument::STATUS_ARCHIVED ? now() : null,
        ])->save();

        if (($validated['status'] ?? null) === TechnicalComplianceDocument::STATUS_REVISION_REQUESTED && ! empty($validated['comment'])) {
            $document->revisions()->create([
                'requested_by' => $request->user()->id,
                'comment' => $validated['comment'],
                'status' => TechnicalComplianceDocument::STATUS_REVISION_REQUESTED,
            ]);
        }

        return back()->with('status', 'Document workflow status updated.');
    }
}
