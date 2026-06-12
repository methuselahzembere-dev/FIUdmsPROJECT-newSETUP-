<?php

namespace App\Http\Controllers\Fiu;

use App\Http\Controllers\Controller;
use App\Models\Institution;  //for finding the isntitutions//
use App\Models\TechnicalComplianceFolder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ComplianceTrackController extends Controller
{
    /**
     * Route Alias for your web.php: [ComplianceTrackController::class, 'technicalIndex']
     */
    public function technicalIndex(): View
    {
        return $this->index();
    }

    
public function index(): View
    {
        // Cleaned, isolated, and safe from MySQL 1054 Unknown column crashes
        $folders = TechnicalComplianceFolder::query()
            ->where('compliance_track_id', 1) 
            ->with(['institution' => fn($query) => $query->select('id', 'name')])
            ->withCount('documents as documents_count')
            ->orderBy('name')
            ->paginate(15);

        return view('fiu.tracks.TechnicalCompliance.folders.index', compact('folders'));
    }

    /**
     * Show the form for creating a new technical compliance folder.
     */
public function create(): \Illuminate\View\View
    {
        // 1. Fetch the multi-tenant institutions from the database
        $institutions = \App\Models\Institution::query()
            ->orderBy('name')
            ->get();

        // 🌟 FIXED: Return the compiled View layout object passing the collection payload
        return view('fiu.tracks.TechnicalCompliance.folders.create', compact('institutions'));
    }

    /**
     * Store a newly created technical compliance track in the database.
     */
/**
     * Store a newly created technical compliance folder.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validate incoming parameters cleanly (accepting an array of institution IDs)
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'institution_ids' => ['nullable', 'array'],
            'institution_ids.*' => ['exists:institutions,id'],
        ]);

        $institutionIds = $validated['institution_ids'] ?? [];

        if (empty($institutionIds)) {
            // Scenario A: No institutions selected -> Create a single Global Master Folder
            TechnicalComplianceFolder::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'compliance_track_id' => 1, 
                'institution_id' => null, // Global context marker
                'created_by' => auth()->id(),
                'is_active' => true,
                'is_default' => false,
                'is_visible_to_institutions' => true,
                'sort_order' => 0,
            ]);
        } else {
            // Scenario B: Checkboxes selected -> Create a dedicated folder row for each institution
            foreach ($institutionIds as $institutionId) {
                TechnicalComplianceFolder::create([
                    'name' => $validated['name'],
                    'description' => $validated['description'],
                    'compliance_track_id' => 1, 
                    'institution_id' => $institutionId, // Tied to specific tenant
                    'created_by' => auth()->id(),
                    'is_active' => true,
                    'is_default' => false,
                    'is_visible_to_institutions' => true,
                    'sort_order' => 0,
                ]);
            }
        }

        //  Added the missing return statement to satisfy the RedirectResponse typehint!
        return redirect()
            ->route('fiu.technical-compliance.folders.index')
            ->with('success', 'Technical compliance folder structure saved successfully.');
    }

public function show(int|string $track): View
    {
        // 1. Attempt to fetch track details using fallback bindings first
        $trackData = DB::table('compliance_tracks')
            ->where('id', $track)
            ->orWhere('slug', $track)
            ->first(); // 🌟 Changed from firstOrFail to first so it doesn't instantly crash if it's a folder slug instead!
        
        // 🔹 CONTEXT A: If a matching track was found, display the track's folder layout index grid
        if ($trackData) {
            $viewPath = match ($trackData->slug) {
                'technical-compliance' => 'fiu.tracks.TechnicalCompliance.folders.index',
                'effectiveness'         => 'fiu.tracks.Effectiveness.index',
                default                 => 'fiu.tracks.show',
            };

            // Fetch technical folders and group them cleanly
            $rawFolders = DB::table('folders')
                ->leftJoin('technical_compliance_documents', 'folders.id', '=', 'technical_compliance_documents.folder_id')
                ->select(
                    'folders.*',
                    DB::raw('COUNT(technical_compliance_documents.id) as documents_count')
                )
                ->where('folders.compliance_track_id', $trackData->id)
                ->whereNull('folders.parent_id') // Shows root level directories only
                ->groupBy('folders.id')
                ->orderBy('folders.name')
                ->get();

            // Map over the folders array to inject default properties
            $folders = $rawFolders->map(function ($folder) {
                $folder->institutions = collect([]); 
                $folder->institutions_count = 0;
                return $folder;
            });

            return view($viewPath, [
                'track'   => (array) $trackData, 
                'folders' => $folders
            ]);
        }

        // 🔹 CONTEXT B: DRILL-DOWN SINGLE FOLDER VIEW
        // If no track matched, it means a folder slug was passed! Look it up in the folders table.
        $folder = DB::table('folders')
            ->where('slug', $track)
            ->firstOrFail(); // This WILL trigger a true 404 if the folder slug is totally invalid

        // Pull any institution documents matching this folder's record container id
        $documents = DB::table('technical_compliance_documents')
            ->where('folder_id', $folder->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Safely map standard structural properties onto your isolated folder object
        $folder->institutions = collect([]);

        // Direct layout compilation to your subfolder path: views/fiu/tracks/TechnicalCompliance/folders/show.blade.php
        return view('fiu.tracks.TechnicalCompliance.folders.show', compact('folder', 'documents'));
    }

    public function edit(int|string $track): View
    {
        $track = DB::table('compliance_tracks')->where('id', $track)->orWhere('slug', $track)->firstOrFail();

        return view('fiu.tracks.edit', compact('track'));
    }

    public function update(Request $request, int|string $track): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['updated_at'] = now();

        DB::table('compliance_tracks')->where('id', $track)->orWhere('slug', $track)->update($validated);

        return redirect()->route('fiu.tracks.index')->with('success', 'Compliance track updated successfully.');
    }

    public function destroy(int|string $track): RedirectResponse
    {
        // Keep tracking dependencies in mind—folders referencing this compliance_track_id may fail database constraints if deleted raw
        DB::table('compliance_tracks')->where('id', $track)->orWhere('slug', $track)->delete();

        return redirect()->route('fiu.tracks.index')->with('success', 'Compliance track removed successfully.');
    }

    /**
     * Action hook to securely store dynamic workspace folders generated by FIU staff
     */
   public function storeTechnicalFolder(\Illuminate\Http\Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000']
        ]);

        $trackId = DB::table('compliance_tracks')
            ->where('slug', 'technical-compliance')
            ->value('id');

        DB::table('folders')->insert([
            'compliance_track_id' => $trackId,
            'parent_id'           => null, // 🌟 FORCES it into the root category list
            'name'                => $validated['name'],
            'slug'                => \Illuminate\Support\Str::slug($validated['name']),
            'description'         => $validated['description'],
            'created_by'          => auth()->id(),
            'is_default'          => false,
            'created_at'          => now(),
            'updated_at'          => now()
        ]);

        return redirect()->back()->with('success', 'Custom compliance area folder generated successfully.');
    }
}