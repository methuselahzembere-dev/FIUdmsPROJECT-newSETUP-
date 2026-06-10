<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ArchiveController extends Controller
{
    public function index(Request $request): View
    {
        $institutionId = $request->user()?->institution_id;

        $documents = DB::table('documents')
            ->leftJoin('folders', 'documents.folder_id', '=', 'folders.id')
            ->select('documents.*', 'folders.name as folder_name')
            ->where('documents.institution_id', $institutionId)
            ->whereIn('documents.status', ['approved', 'archived'])
            ->latest('documents.updated_at')
            ->paginate(25);

        return view('institution.archive.index', compact('documents'));
    }
}
