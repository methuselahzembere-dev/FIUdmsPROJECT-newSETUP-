<?php

namespace App\Http\Controllers\Fiu;

use App\Http\Controllers\Controller;
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

/**
     * 📁 FOLDER REGISTRATION: Store a newly created technical compliance folder row.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'visibility_scope' => ['required', 'string', 'in:shared,fiu-private'], // 🔒 Validate scope choice
            'institution_ids' => ['nullable', 'array'],
            'institution_ids.*' => ['exists:institutions,id'],
        ]);

        $institutionIds = $validated['institution_ids'] ?? [];
        $scope = $validated['visibility_scope'];

        // If marked confidential, force multi-tenant linkage maps to stay completely empty
        if ($scope === 'fiu-private') {
            $institutionIds = [];
        }

        if (empty($institutionIds)) {
            \App\Models\TechnicalComplianceFolder::create([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'description' => $validated['description'],
                'compliance_track_id' => 1, 
                'visibility_scope' => $scope, // 🌟 Save isolation level flag
                'institution_id' => null, 
                'created_by' => auth()->id(),
                'is_active' => true,
                'is_default' => false,
                'is_visible_to_institutions' => ($scope === 'shared'), 
                'sort_order' => 0,
            ]);
        } else {
            foreach ($institutionIds as $institutionId) {
                \App\Models\TechnicalComplianceFolder::create([
                    'name' => $validated['name'],
                    'slug' => Str::slug($validated['name']),
                    'description' => $validated['description'],
                    'compliance_track_id' => 1, 
                    'visibility_scope' => 'shared', 
                    'institution_id' => $institutionId, 
                    'created_by' => auth()->id(),
                    'is_active' => true,
                    'is_default' => false,
                    'is_visible_to_institutions' => true,
                    'sort_order' => 0,
                ]);
            }
        }

        return redirect()
            ->route('fiu.technical-compliance.folders.index')
            ->with('success', 'Technical compliance folder structure saved successfully.');
    }

    /**
     * 📑 CENTRALIZED DOCUMENT UPLOAD: Render the dual-workspace asset log form screen.
     */
    public function createDocument()
    {
        $techTrackId = DB::table('compliance_tracks')->where('slug', 'technical-compliance')->value('id') ?? 1;

        $technicalFolders = TechnicalComplianceFolder::where('compliance_track_id', $techTrackId)->orderBy('name')->get();
        $immediateOutcomes = DB::table('effectiveness_immediate_outcomes')->orderBy('code')->get();
        $subOutcomes = DB::table('effectiveness_sub_immediate_outcomes')->orderBy('code')->get();
        $institutions = Institution::orderBy('name')->get();
        $users = User::orderBy('name')->get();

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
            'users',
            'documentStatuses'
        ));
    }

    /**
     * 📑 CENTRALIZED DOCUMENT UPLOAD: Parse parameters, ingest binary file flows, and dispatch records.
     */
    public function storeDocument(Request $request)
    {
        $validated = $request->validate([
            'workspace_track'            => ['required', 'string', 'in:technical,effectiveness'],
            'title'                      => ['required', 'string', 'max:255'],
            'reporting_institution'      => ['required', 'string', 'max:255'],
            'date_logged'                => ['required', 'date'],
            'status'                     => ['required', 'string', Rule::in(['submitted', 'under-review', 'changes-requested', 'approved', 'archived'])],
            'remarks'                    => ['nullable', 'string', 'max:4000'],
            'document_file'              => ['required_without:external_file_path', 'nullable', 'file', 'max:51200'],
            
            'technical_folder_id'        => ['required_if:workspace_track,technical', 'nullable', 'integer', 'exists:folders,id'],
            'immediate_outcome_id'       => ['required_if:workspace_track,effectiveness', 'nullable', 'integer', 'exists:immediate_outcomes,id'],
            'effectiveness_sub_io_id'    => ['required_if:workspace_track,effectiveness', 'nullable', 'integer', 'exists:effectiveness_sub_ios,id'],
            
            'external_file_name'         => ['nullable', 'string', 'max:255'],
            'external_file_path'         => ['nullable', 'string', 'max:255'],
            
            'target_institutions'        => ['nullable', 'array'],
            'target_institutions.*'      => ['integer', 'exists:institutions,id'],
            'target_users'               => ['nullable', 'array'],
            'target_users.*'             => ['integer', 'exists:users,id'],
        ]);

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

        if ($validated['workspace_track'] === 'technical') {
            $documentId = DB::table('technical_compliance_documents')->insertGetId([
                'folder_id'         => $validated['technical_folder_id'],
                'title'             => $validated['title'],
                'description'       => $validated['remarks'] ?? null,
                'stored_path'       => $finalPath,
                'original_filename' => $originalFilename,
                'mime_type'         => $mimeType,
                'status'            => $validated['status'],
                'uploaded_by'       => $request->user()?->id ?? auth()->id() ?? 1,
                'submitted_at'      => $validated['date_logged'],
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        } else {
            $documentId = DB::table('effectiveness_documents')->insertGetId([
                'immediate_outcome_id' => $validated['immediate_outcome_id'],
                'sub_io_id'            => $validated['effectiveness_sub_io_id'],
                'title'                => $validated['title'],
                'description'          => $validated['remarks'] ?? null,
                'stored_path'          => $finalPath,
                'original_filename'    => $originalFilename,
                'mime_type'            => $mimeType,
                'status'               => $validated['status'],
                'uploaded_by'          => $request->user()?->id ?? auth()->id() ?? 1,
                'submitted_at'         => $validated['date_logged'],
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);
        }

        if (!empty($request->input('target_institutions'))) {
            foreach ($request->input('target_institutions') as $instId) {
                DB::table('document_institution_visibility')->insertOrIgnore([
                    'workspace_track' => $validated['workspace_track'],
                    'document_id'     => $documentId,
                    'institution_id'  => $instId,
                    'created_at'      => now(),
                ]);
            }
        }

        if (!empty($request->input('target_users'))) {
            foreach ($request->input('target_users') as $usrId) {
                DB::table('document_user_visibility')->insertOrIgnore([
                    'workspace_track' => $validated['workspace_track'],
                    'document_id'     => $documentId,
                    'user_id'         => $usrId,
                    'created_at'      => now(),
                ]);
            }
        }

        return redirect()
            ->route('fiu.technical-compliance.folders.index')
            ->with('success', 'Document asset ingested, processed and dispatched to multi-tenant targets successfully.');
    }

 
  /**
     * Show detailed dashboards tracking individual folders or custom tracks dynamically
     */
    public function show($track)
    {
        $isFiuStaff = auth()->user()->is_fiu_staff ?? false;
        $userInstitutionId = auth()->user()->institution_id ?? null;

        $trackData = DB::table('compliance_tracks')
            ->where('id', $track)
            ->orWhere('slug', $track)
            ->first();

        if ($trackData) {
            $viewPath = match ($trackData->slug) {
                'technical-compliance' => 'fiu.tracks.TechnicalCompliance.folders.index',
                'effectiveness'         => 'fiu.tracks.Effectiveness.index',
                default                 => 'fiu.tracks.show',
            };

            $rawFolders = DB::table('folders')
                ->leftJoin('technical_compliance_documents', 'folders.id', '=', 'technical_compliance_documents.folder_id')
                ->select(
                    'folders.*',
                    DB::raw('COUNT(technical_compliance_documents.id) as documents_count')
                )
                ->where('folders.compliance_track_id', $trackData->id)
                ->whereNull('folders.parent_id') 
                ->where(function($query) use ($isFiuStaff, $userInstitutionId) {
                    if ($isFiuStaff) return $query;
                    return $query->where('visibility_scope', 'shared')
                                 ->where('institution_id', $userInstitutionId);
                })
                ->groupBy('folders.id')
                ->orderBy('folders.name')
                ->get();

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

        // 🛡️ SUB-SECTION GATEKEEPER: Deep-diving a singular folder row via its slug string
        $folder = \App\Models\TechnicalComplianceFolder::query()
            ->where('slug', $track)
            ->where(function($query) use ($isFiuStaff, $userInstitutionId) {
                if ($isFiuStaff) return $query;
                return $query->where('visibility_scope', 'shared')
                             ->where('institution_id', $userInstitutionId);
            })
            ->firstOrFail(); // 💥 Triggers standard clean 404 instantly if unauthorized!

        $documents = DB::table('technical_compliance_documents')
            ->where('folder_id', $folder->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $folder->institutions = collect([]);

        return view('fiu.tracks.TechnicalCompliance.folders.show', compact('folder', 'documents'));
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