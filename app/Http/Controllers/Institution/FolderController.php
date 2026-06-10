<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FolderController extends Controller
{
    public function index(Request $request): View
    {
        $institutionId = $request->user()?->institution_id;

        $folders = DB::table('folders')
            ->leftJoin('compliance_tracks', 'folders.compliance_track_id', '=', 'compliance_tracks.id')
            ->select('folders.*', 'compliance_tracks.name as track_name')
            ->where('folders.is_visible_to_institutions', true)
            ->where(function ($query) use ($institutionId) {
                $query->whereNull('folders.institution_id')
                    ->orWhere('folders.institution_id', $institutionId);
            })
            ->orderBy('compliance_tracks.name')
            ->orderBy('folders.name')
            ->paginate(15);

        return view('institution.folders.index', compact('folders'));
    }

    public function show(Request $request, int|string $folder): View
    {
        $institutionId = $request->user()?->institution_id;

        $folder = DB::table('folders')
            ->where(function ($query) use ($folder) {
                $query->where('id', $folder)->orWhere('slug', $folder);
            })
            ->where('is_visible_to_institutions', true)
            ->where(function ($query) use ($institutionId) {
                $query->whereNull('institution_id')->orWhere('institution_id', $institutionId);
            })
            ->firstOrFail();

        $documents = DB::table('documents')
            ->where('folder_id', $folder->id)
            ->where('institution_id', $institutionId)
            ->latest('updated_at')
            ->paginate(15);

        return view('institution.folders.show', compact('folder', 'documents'));
    }
}
