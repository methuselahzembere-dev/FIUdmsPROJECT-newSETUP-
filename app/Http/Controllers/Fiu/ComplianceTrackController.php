<?php

namespace App\Http\Controllers\Fiu;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ComplianceTrackController extends Controller
{
    public function index(): View
    {
        $tracks = DB::table('compliance_tracks')->orderBy('name')->paginate(15);

        return view('fiu.tracks.index', compact('tracks'));
    }

    public function create(): View
    {
        return view('fiu.tracks.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['created_at'] = now();
        $validated['updated_at'] = now();

        DB::table('compliance_tracks')->insert($validated);

        return redirect()->route('fiu.tracks.index')->with('success', 'Compliance track created successfully.');
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