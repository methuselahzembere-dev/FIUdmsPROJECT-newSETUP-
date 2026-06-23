<?php

namespace App\Http\Controllers\Fiu;

use App\Http\Controllers\Controller;
use App\Models\Scopes\TenantComplianceScope;
use App\Models\Institution;  
use App\Models\TechnicalComplianceFolder;
use App\Models\TechnicalComplianceDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ComplianceTrackController extends Controller
{
    /**
     * Route Alias for your web.php: [ComplianceTrackController::class, 'technicalIndex']
     */
    public function technicalIndex()
    {
        return $this->index();
    }

    /**
     * Display the main workspace directory folder index map
     */
  /**
     * Display the main workspace directory folder index map
     */
    public function index()
    {
        // 🌟 FORCE FIU STAFF STATE TO TRUE TO SEE BOTH PRIVATE AND SHARED
        // (Later you can dynamically bind this to your auth profiles role/column checks)
        $isFiuStaff = true; 
        $userInstitutionId = auth()->user()->institution_id ?? null;

        $folders = \App\Models\TechnicalComplianceFolder::query()
            ->where('compliance_track_id', 1)
            ->where(function($query) use ($isFiuStaff, $userInstitutionId) {
                if ($isFiuStaff) {
                    // 🔒 FIU staff bypass constraints: Fetch EVERYTHING (shared AND private)
                    return $query;
                }
                
                // External institutional users are strictly limited to shared folders matching their company
                return $query->where('visibility_scope', 'shared')
                             ->where('institution_id', $userInstitutionId);
            })
            ->with([
                'institution' => fn($query) => $query->select('id', 'name'),
                'documents' => fn($query) => $query->with([
                    'creator:id,name', 
                    'updater:id,name'
                ])
            ])
            ->withCount('documents as documents_count')
            ->orderBy('name')
            ->paginate(50); // Increased pagination so long lists scroll fluidly in the sidebar

        return view('fiu.tracks.TechnicalCompliance.folders.index', compact('folders'));
    }

    /**
     * 📁 FOLDER REGISTRATION: Show the form for creating a new technical compliance folder.
     */
    public function create()
    {
        $institutions = Institution::query()
            ->orderBy('name')
            ->get();

        return view('fiu.tracks.TechnicalCompliance.folders.create', compact('institutions'));
    }
public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'visibility_scope' => ['required', 'string', 'in:shared,fiu-private'], // 🔒 Validate scope choice
            'target_institutions' => ['nullable', 'array'],
            'target_institutions.*' => ['exists:institutions,id'],
        ]);

        // Wrap execution in a transaction block to prevent partial multi-tenant row replication states
        return \Illuminate\Support\Facades\DB::transaction(function () use ($validated) {
            
       $institutionIds = $validated['target_institutions'] ?? [];
            $scope = $validated['visibility_scope'];

            // 🔒 SECURITY GUARD: If marked confidential, wipe out multi-tenant assignment parameters
            if ($scope === 'fiu-private') {
                $institutionIds = [];
            }

            // 1. Create the Folder EXACTLY ONCE (No more Case A/Case B loops!)
            $folder = \App\Models\TechnicalComplianceFolder::create([
                'name' => $validated['name'],
                'slug' => \Illuminate\Support\Str::slug($validated['name']), 
                'description' => $validated['description'],
                'compliance_track_id' => 1, 
                'visibility_scope' => $scope, 
                'institution_id' => null, // 🌟 Legacy column: Leave null since we now use pivot tables!
                'created_by' => auth()->id(),
                'is_active' => true,
                'is_default' => false,
                'is_visible_to_institutions' => ($scope === 'shared'), 
                'sort_order' => 0,
            ]);

            // 2. Map the Multi-Tenant Access via the Pivot Table
            if (!empty($institutionIds)) {
                // sync() automatically creates the rows in folder_institution_visibility
                $folder->institutions()->sync($institutionIds);
            }

            return redirect()
                ->route('fiu.technical-compliance.folders.index')
                ->with('success', 'Technical compliance folder structure saved securely without redundancies.');
        });
    }

    /**
     * 📑 CENTRALIZED DOCUMENT UPLOAD: Render the dual-workspace asset log form screen.
     */
 public function createDocument()
    {
        $techTrackId = DB::table('compliance_tracks')->where('slug', 'technical-compliance')->value('id') ?? 1;

        $technicalFolders = TechnicalComplianceFolder::where('compliance_track_id', $techTrackId)->orderBy('name')->get();
        
        // 🌟 FIXED: Use Eloquent models so Global Tenant Scopes and Admin bypass triggers execute perfectly
        $immediateOutcomes = \App\Models\EffectivenessImmediateOutcome::orderBy('code')->get();
        $subOutcomes = \App\Models\EffectivenessSubImmediateOutcome::orderBy('code')->get();
        $institutions = Institution::orderBy('name')->get();
        
        // 👉 CHANGE HERE: Name it $fiuUsers to match your Blade template!
        $fiuUsers = User::whereIn('role', ['fiu_reviewer', 'fiu_admin'])->orderBy('name')->get();
        // (Or just User::orderBy('name')->get() if you really want everyone)

        $documentStatuses = [
            'submitted'         => 'Submitted',
            'under-review'      => 'Under Review',
            'changes-requested' => 'Changes Requested',
            'approved'          => 'Approved',
            'archived'          => 'Archived'
        ];

        return view('fiu.documents.create', compact(
            'technicalFolders',
            'immediateOutcomes',
            'subOutcomes',
            'institutions',
            'fiuUsers', 
            'documentStatuses'
        ));
    }

    /**
     * 📑 CENTRALIZED DOCUMENT UPLOAD: Parse parameters, ingest binary file flows, and dispatch records.
     */

public function storeDocument(Request $request)
{
    // 1. Validation (Unchanged - Matches your HTML perfectly)
    $validated = $request->validate([
        'workspace_track'            => ['required', 'string', 'in:technical,effectiveness'],
        'title'                      => ['required', 'string', 'max:255'],
        'reporting_institution'      => ['required', 'string', 'max:255'],
        'date_logged'                => ['required', 'date'],
        'status'                     => ['required', 'string', \Illuminate\Validation\Rule::in(['submitted', 'under-review', 'changes-requested', 'approved', 'archived'])],
        'remarks'                    => ['nullable', 'string', 'max:4000'],
        'document_file'              => ['required_without:external_file_path', 'nullable', 'file', 'max:51200'],
        'technical_folder_ids'       => ['required_if:workspace_track,technical', 'nullable', 'array'],
        'technical_folder_ids.*'     => ['integer'], 
        'effectiveness_sub_io_ids'   => ['required_if:workspace_track,effectiveness', 'nullable', 'array'],
        'effectiveness_sub_io_ids.*' => ['integer'],
        'external_file_name'         => ['nullable', 'string', 'max:255'],
        'external_file_path'         => ['nullable', 'string', 'max:255'],
        'target_institutions'        => ['nullable', 'array'],
        'target_institutions.*'      => ['integer'],
        'target_users'               => ['nullable', 'array'],
        'target_users.*'             => ['integer'],
    ]);

    // 2. Process File Upload (Unchanged)
    $finalPath = null;
    $originalFilename = null;
    $mimeType = 'application/octet-stream';

    if ($request->hasFile('document_file')) {
        $file = $request->file('document_file');
        $originalFilename = $file->getClientOriginalName();
        $mimeType = $file->getMimeType();
        
        $subPath = $validated['workspace_track'] === 'technical' ? 'technical-compliance' : 'effectiveness';
        $finalPath = $file->store("documents/{$subPath}", 'private');
    } else {
        $finalPath = $request->input('external_file_path');
        $originalFilename = $request->input('external_file_name') ?? basename($finalPath);
    }

    // 🌟 SENIOR REFACTOR: Wrap DB operations in a transaction for data safety
    return \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $request, $finalPath, $originalFilename, $mimeType) {
        
        // 3. Create the SINGLE Master Document
     $document = \App\Models\Document::create([
            'workspace_track'       => $validated['workspace_track'],
            'visibility_scope'      => $validated['visibility_scope'] ?? 'shared', 
            'title'                 => $validated['title'],
            'reporting_institution' => $validated['reporting_institution'],
            'date_logged'           => $validated['date_logged'],
            'status'                => $validated['status'],
            'remarks'               => $validated['remarks'] ?? null,
            'file_path'             => $finalPath,
            
            // 🌟 FIXED: Mapped to the exact column names from your migration!
            'external_file_name'    => $originalFilename, 
            'user_id'               => $request->user()?->id ?? auth()->id() ?? 1,
        ]);
        // 4. CLASSIFICATION PILLAR: Where is it filed? (No foreach loops needed!)
        if ($validated['workspace_track'] === 'technical' && !empty($validated['technical_folder_ids'])) {
            // Maps to `document_technical_folder` table
            $document->technicalFolders()->sync($validated['technical_folder_ids']);
        } elseif ($validated['workspace_track'] === 'effectiveness' && !empty($validated['effectiveness_sub_io_ids'])) {
            // Maps to `document_sub_io` table
            $document->subImmediateOutcomes()->sync($validated['effectiveness_sub_io_ids']);
        }

       // 5. PERMISSION PILLAR: Who is allowed to see it?
        if (!empty($validated['target_institutions'])) {
            //  Build an array mapping each Institution ID to the Workspace Track
            $institutionSyncData = [];
            foreach ($validated['target_institutions'] as $instId) {
                $institutionSyncData[$instId] = ['workspace_track' => $validated['workspace_track']];
            }
            // Sync with the extra pivot data!
            $document->institutions()->sync($institutionSyncData);
        }

      if (!empty($validated['target_users'])) {
            $document->users()->sync($validated['target_users']);
        }

        // 6. REDIRECT
        if ($validated['workspace_track'] === 'effectiveness') {
            
            // Optional: Figure out exactly which Parent IOs were just updated
            // so we can highlight them on the index page!
            $parentIoIds = \Illuminate\Support\Facades\DB::table('effectiveness_sub_immediate_outcomes')
                ->whereIn('id', $validated['effectiveness_sub_io_ids'] ?? [])
                ->pluck('immediate_outcome_id')
                ->unique()
                ->toArray();

            return redirect()
                ->route('fiu.effectiveness.folders.index')
                // We pass the parent IDs in the session so the UI knows what to highlight!
                ->with('success', 'Document successfully mapped to selected Outcomes.')
                ->with('recently_updated_ios', $parentIoIds); 
        }


       });
}

 
 public function show($code)
{
    $user = auth()->user();
    $isFiuStaff = $user->is_fiu_staff ?? false;
    $userInstitutionId = $user->institution_id ?? null;

    // 1. Fetch the Parent Immediate Outcome based on the route code (e.g., 'IO.1')
    $immediateOutcome = \App\Models\ImmediateOutcome::where('code', $code)->firstOrFail();

    // 2. 🌟 SMART FETCH: Load Sub-IOs AND their attached Master Documents instantly!
    $subOutcomes = \App\Models\EffectivenessSubImmediateOutcome::where('immediate_outcome_id', $immediateOutcome->id)
        ->with(['documents' => function ($query) use ($isFiuStaff, $userInstitutionId) {
            
            // 🛡️ SECURITY: If the user is NOT FIU Staff, enforce the Permissions Pivot Bridge
            if (!$isFiuStaff) {
                $query->whereHas('institutions', function ($permissionQuery) use ($userInstitutionId) {
                    $permissionQuery->where('institution_id', $userInstitutionId);
                });
            }

            // Order newest documents first
            $query->orderBy('created_at', 'desc');
            
        }])->get();

    // 3. Send it perfectly packaged to your split dashboard view
    return view('fiu.effectiveness.show', compact('immediateOutcome', 'subOutcomes'));
}

    public function edit($track)
    {
        $track = DB::table('compliance_tracks')->where('id', $track)->orWhere('slug', $track)->firstOrFail();
        return view('fiu.tracks.edit', compact('track'));
    }

    public function update(Request $request, $track)
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

    public function destroy($track)
    {
        DB::table('compliance_tracks')->where('id', $track)->orWhere('slug', $track)->delete();
        return redirect()->route('fiu.tracks.index')->with('success', 'Compliance track removed successfully.');
    }

    /**
     * Action hook to securely store dynamic workspace folders generated by FIU staff
     */
    public function storeTechnicalFolder(Request $request)
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
            'parent_id'           => null, 
            'name'                => $validated['name'],
            'slug'                => Str::slug($validated['name']),
            'description'         => $validated['description'],
            'created_by'          => auth()->id(),
            'is_default'          => false,
            'created_at'          => now(),
            'updated_at'          => now()
        ]);

        return redirect()->back()->with('success', 'Custom compliance area folder generated successfully.');
    }
}