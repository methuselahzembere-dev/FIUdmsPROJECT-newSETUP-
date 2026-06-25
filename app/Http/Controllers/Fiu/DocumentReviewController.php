<?php

namespace App\Http\Controllers\Fiu;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DocumentReviewController extends Controller
{
public function index(Request $request): View
    {
        // 🌟 Eloquent 'with()' eager loads the relationships, eliminating duplicates!
        $documents = \App\Models\Document::with(['folder', 'institutions'])
            // Filter by Status
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            // Filter by Search Query
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = $request->q;
                
                $query->where(function ($subQuery) use ($search) {
                    // 1. Search the document title
                    $subQuery->where('title', 'like', "%{$search}%")
                        
                        // 2. Search dynamically inside the attached institutions
                        ->orWhereHas('institutions', function ($instQuery) use ($search) {
                            $instQuery->where('name', 'like', "%{$search}%");
                        })
                        
                        // 3. Search dynamically inside the attached folder
                        ->orWhereHas('folder', function ($folderQuery) use ($search) {
                            $folderQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('updated_at')
            ->paginate(15);

        return view('fiu.documents.index', compact('documents'));
    }

public function create(): View
{
    // Fetch all required data
    $institutions = \App\Models\Institution::orderBy('name')->get();
    $technicalFolders = \App\Models\TechnicalComplianceFolder::orderBy('name')->get(); // Matching Blade's variable
    $immediateOutcomes = \App\Models\EffectivenessImmediateOutcome::all();
    $subOutcomes = \App\Models\EffectivenessSubImmediateOutcome::all();
    
    // Specifically fetch FIU users (assuming a 'role' column exists)
    $fiuUsers = \App\Models\User::where('role', 'fiu_reviewer')->get(); 

    return view('fiu.documents.create', compact(
        'institutions', 
        'technicalFolders', 
        'immediateOutcomes', 
        'subOutcomes', 
        'fiuUsers'
    ));
}

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'institution_id' => ['required', 'integer', 'exists:institutions,id'],
            'folder_id' => ['required', 'integer', 'exists:folders,id'],
            'title' => ['required', 'string', 'max:255'],
            'document' => ['required', 'file', 'max:51200'],
            'status' => ['nullable', Rule::in(['draft', 'submitted', 'under-review', 'changes-requested', 'approved', 'archived'])],
            'review_notes' => ['nullable', 'string', 'max:4000'],
        ]);

        $path = $request->file('document')->store('documents', 'private');

        DB::table('documents')->insert([
            'institution_id' => $validated['institution_id'],
            'folder_id' => $validated['folder_id'],
            'title' => $validated['title'],
            'file_path' => $path,
            'status' => $validated['status'] ?? 'submitted',
            'review_notes' => $validated['review_notes'] ?? null,
            'submitted_by' => $request->user()?->id,
            'submitted_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('fiu.documents.index')->with('success', 'Document uploaded successfully.');
    }

    public function show(int|string $document): View
    {
        $document = DB::table('documents')
            ->leftJoin('institutions', 'documents.institution_id', '=', 'institutions.id')
            ->leftJoin('folders', 'documents.folder_id', '=', 'folders.id')
            ->select('documents.*', 'institutions.name as institution_name', 'folders.name as folder_name')
            ->where('documents.id', $document)
            ->firstOrFail();

        return view('fiu.documents.show', compact('document'));
    }

    public function edit(int|string $document): View
    {
        $document = DB::table('documents')->where('id', $document)->firstOrFail();
        $institutions = DB::table('institutions')->orderBy('name')->get();
        $folders = DB::table('folders')->orderBy('name')->get();

        return view('fiu.documents.edit', compact('document', 'institutions', 'folders'));
    }

    public function update(Request $request, int|string $document): RedirectResponse
    {
        $validated = $request->validate([
            'institution_id' => ['required', 'integer', 'exists:institutions,id'],
            'folder_id' => ['required', 'integer', 'exists:folders,id'],
            'title' => ['required', 'string', 'max:255'],
            'document' => ['nullable', 'file', 'max:51200'],
            'status' => ['required', Rule::in(['draft', 'submitted', 'under-review', 'changes-requested', 'approved', 'archived'])],
            'review_notes' => ['nullable', 'string', 'max:4000'],
        ]);

        $payload = [
            'institution_id' => $validated['institution_id'],
            'folder_id' => $validated['folder_id'],
            'title' => $validated['title'],
            'status' => $validated['status'],
            'review_notes' => $validated['review_notes'] ?? null,
            'reviewed_by' => $request->user()?->id,
            'reviewed_at' => now(),
            'updated_at' => now(),
        ];

        if ($request->hasFile('document')) {
            $existing = DB::table('documents')->where('id', $document)->first();
            if ($existing?->file_path) {
                Storage::disk('private')->delete($existing->file_path);
            }
            $payload['file_path'] = $request->file('document')->store('documents', 'private');
        }

        DB::table('documents')->where('id', $document)->update($payload);

        return redirect()->route('fiu.documents.show', $document)->with('success', 'Document review updated successfully.');
    }

    public function destroy(int|string $document): RedirectResponse
    {
        DB::table('documents')->where('id', $document)->update([
            'status' => 'archived',
            'archived_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('fiu.documents.index')->with('success', 'Document archived successfully.');
    }

}
