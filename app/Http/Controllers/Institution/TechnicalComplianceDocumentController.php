<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTechnicalComplianceDocumentRequest;
use App\Models\TechnicalComplianceDocument;
use App\Models\TechnicalComplianceFolder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TechnicalComplianceDocumentController extends Controller
{
    public function create(Request $request): View
    {
        $institution = $request->user()->reportingInstitution;
        $folders = TechnicalComplianceFolder::query()
            ->active()
            ->whereHas('institutions', fn ($query) => $query->whereKey($institution->id))
            ->orderBy('name')
            ->get();

        return view('institution.technical-compliance.documents.create', compact('folders', 'institution'));
    }

    public function store(StoreTechnicalComplianceDocumentRequest $request): RedirectResponse
    {
        $folder = $request->folder()->loadMissing('institutions:id');
        $institution = $request->user()->reportingInstitution;

        abort_unless($folder->institutions->contains('id', $institution->id), 403, 'You are not assigned to this Technical Compliance folder.');

        $file = $request->file('document');
        $storedPath = $file->store('technical-compliance/'.$folder->slug, 'public');

        $document = TechnicalComplianceDocument::query()->create([
            'technical_compliance_folder_id' => $folder->id,
            'reporting_institution_id' => $institution->id,
            'uploaded_by' => $request->user()->id,
            'title' => $request->string('title')->toString(),
            'description' => $request->input('description'),
            'stored_path' => $storedPath,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'status' => TechnicalComplianceDocument::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);

        return redirect()
            ->route('institution.technical-compliance.folders.show', $folder)
            ->with('status', 'Document uploaded and bound to the selected Technical Compliance folder.');
    }
}
