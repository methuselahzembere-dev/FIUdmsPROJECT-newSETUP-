<?php

namespace App\Http\Controllers\Fiu;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTechnicalComplianceFolderRequest;
use App\Models\ReportingInstitution;
use App\Models\TechnicalComplianceFolder;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TechnicalComplianceFolderController extends Controller
{
    public function index(): View
    {
        $folders = TechnicalComplianceFolder::query()
            ->withCount('documents')
            ->with('institutions:id,name,code')
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->paginate(12);

        return view('fiu.technical-compliance.folders.index', compact('folders'));
    }

    public function create(): View
    {
        $institutions = ReportingInstitution::query()->orderBy('name')->get(['id', 'name', 'code']);

        return view('fiu.technical-compliance.folders.create', compact('institutions'));
    }

    public function store(StoreTechnicalComplianceFolderRequest $request): RedirectResponse
    {
        $folder = TechnicalComplianceFolder::query()->create([
            'name' => $request->string('name')->toString(),
            'description' => $request->input('description'),
            'is_default' => false,
            'is_active' => true,
            'created_by' => $request->user()->id,
        ]);

        $folder->institutions()->sync($request->input('institution_ids', []));

        return redirect()
            ->route('fiu.technical-compliance.folders.show', $folder)
            ->with('status', 'Technical Compliance folder created successfully.');
    }

public function show(TechnicalComplianceFolder $folder): View
    {
        // 1. Load the basic relationships for the folder 
        $folder->load([
            'creator:id,name',
            'institutions:id,name' 
        ]);

        // 2. Fetch the paginated documents cleanly
        $documents = $folder->documents()
            ->with(['institutions:id,name', 'uploader:id,name'])
            ->latest()
            ->paginate(15);

        return view('fiu.tracks.TechnicalCompliance.folders.show', compact('folder', 'documents'));
    }
}
