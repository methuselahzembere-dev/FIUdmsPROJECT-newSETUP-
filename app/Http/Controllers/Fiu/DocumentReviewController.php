<?php

namespace App\Http\Controllers\Fiu;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Document; 
use App\Models\Institution;
use Illuminate\View\View;

class DocumentReviewController extends Controller
{
public function index(Request $request): View
    {
        $user = Auth::user();

        // 1. Base Query & Strict Tenant Isolation
        $baseQuery = Document::query();
        
        // If the user is an external representative, lock their view to their institution ONLY
        if ($user->role === 'institution_representative' && $user->institution_id) {
            $baseQuery->whereHas('institutions', function ($query) use ($user) {
                $query->where('id', $user->institution_id);
            });
        }

        // 2. Intelligent Aggregations (Powering the Top Widgets)
        // We calculate these using the base query *before* search filters are applied, 
        // so the widgets always show the true total of the user's workspace.
        $metrics = [
            'total'       => (clone $baseQuery)->count(),
            'pending'     => (clone $baseQuery)->where('status', 'pending')->count(),
            'in_progress' => (clone $baseQuery)->where('status', 'in_progress')->count(),
            'approved'    => (clone $baseQuery)->where('status', 'approved')->count(),
            'returned'    => (clone $baseQuery)->where('status', 'returned')->count(),
        ];

      // 3. Apply Eager Loading & Custom Search Logic
  $documents = (clone $baseQuery)->with(['technicalFolders', 'subImmediateOutcomes', 'institutions'])
    
    // Filter by Status (Clicking the top widgets will trigger this)
    ->when($request->filled('status'), function ($query) use ($request) {
        $query->where('status', $request->status);
    })
    
    // Dynamic Search Logic
    ->when($request->filled('q'), function ($query) use ($request) {
        $search = $request->q;
        $query->where(function ($subQuery) use ($search) {
            
            // Search document title
            $subQuery->where('title', 'like', "%{$search}%")
                
                // Search dynamically inside attached institutions
                ->orWhereHas('institutions', function ($instQuery) use ($search) {
                    $instQuery->where('name', 'like', "%{$search}%");
                })
                
                // technicalFolders' to match your Pivot Table!
                ->orWhereHas('technicalFolders', function ($folderQuery) use ($search) {
                    $folderQuery->where('name', 'like', "%{$search}%");
                })
                
                //  Now it searches inside Effectiveness Sub-IOs as well!
                ->orWhereHas('subImmediateOutcomes', function ($subIoQuery) use ($search) {
                    $subIoQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('code', 'like', "%{$search}%");
                });
        });
    })

            // Admin Dropdown Filter
            ->when($request->filled('institution_id') && $user->role !== 'institution_representative', function ($query) use ($request) {
                $query->whereHas('institutions', function ($instQuery) use ($request) {
                    $instQuery->where('institutions.id', $request->institution_id);
                });
            })
            
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString(); 

        // 4. Fetch Institutions for the filter dropdown (Only for FIU internal staff)
        $filterInstitutions = ($user->role !== 'institution_representative') 
            ? Institution::orderBy('name')->get() 
            : collect();

        return view('fiu.documents.index', compact('documents', 'metrics', 'filterInstitutions'));
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

public function show(int|string $id)
    {
        // 1. Fetch the document and its pivot relationships
        $document = \App\Models\Document::with([
            'technicalFolders', 
            'subImmediateOutcomes'
        ])->findOrFail($id);

        // 🚦 2. TRAFFIC CONTROL LOGIC 🚦

        // Route to Technical Compliance Workspace
        if ($document->workspace_track === 'technical' && $document->technicalFolders->isNotEmpty()) {
            $folder = $document->technicalFolders->first(); 

            return redirect()
                ->route('fiu.technical-compliance.folders.index', ['active_folder' => $folder->id])
                ->withFragment('doc-' . $document->id);
        }
  

  // Route to Effectiveness Workspace
        if ($document->workspace_track === 'effectiveness' && $document->subImmediateOutcomes->isNotEmpty()) {
            
            // 1. Get the specific Sub-IO the document belongs to
            $subOutcome = $document->subImmediateOutcomes->first();
            
            // 2. Fetch the Parent IO so we know which main workspace to load
            // (Assuming your Sub-IO model has the 'immediate_outcome_id' column)
            $parentIo = \App\Models\EffectivenessImmediateOutcome::find($subOutcome->immediate_outcome_id);

            // 3. Route to the exact Split Dashboard tab and highlight the doc!
            return redirect()->route('fiu.effectiveness.folders.show', [
                'code' => $parentIo->code,      // e.g., 'IO.1'
                'sub_io' => $subOutcome->code   // e.g., 'IO.1.2'
            ])->withFragment('doc-' . $document->id);
        }

        
        // If we get here, the document has no folders assigned in the database.
        // We throw a 404 error instead of loading an ugly, broken view.
        abort(404, 'This document has not been assigned to a specific workspace track yet.');
    }

    public function edit(int|string $id): View
    {
        //  Upgraded to Eloquent: Safely fetches the document and its pivot data
        $document = \App\Models\Document::with(['institutions', 'technicalFolders'])->findOrFail($id);
        
        $institutions = \App\Models\Institution::orderBy('name')->get();
        $folders = \App\Models\TechnicalComplianceFolder::orderBy('name')->get(); 

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
