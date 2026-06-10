<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\TechnicalComplianceFolder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TechnicalComplianceFolderController extends Controller
{
    public function index(Request $request): View
    {
        $institution = $request->user()->reportingInstitution;

        $folders = TechnicalComplianceFolder::query()
            ->active()
            ->whereHas('institutions', fn ($query) => $query->whereKey($institution->id))
            ->withCount(['documents' => fn ($query) => $query->where('reporting_institution_id', $institution->id)])
            ->orderBy('name')
            ->paginate(12);

        return view('institution.technical-compliance.folders.index', compact('folders', 'institution'));
    }

    public function show(Request $request, TechnicalComplianceFolder $folder): View
    {
        $this->authorize('view', $folder);

        $institution = $request->user()->reportingInstitution;
        $documents = $folder->documents()
            ->where('reporting_institution_id', $institution->id)
            ->with('uploader:id,name', 'reviewer:id,name')
            ->latest()
            ->paginate(12);

        return view('institution.technical-compliance.folders.show', compact('folder', 'documents', 'institution'));
    }
}
