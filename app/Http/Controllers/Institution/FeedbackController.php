<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function index(Request $request): View
    {
        $institutionId = $request->user()?->institution_id;

        $documents = DB::table('documents')
            ->leftJoin('folders', 'documents.folder_id', '=', 'folders.id')
            ->select('documents.*', 'folders.name as folder_name')
            ->where('documents.institution_id', $institutionId)
            ->whereIn('documents.status', ['changes-requested', 'under-review'])
            ->latest('documents.updated_at')
            ->paginate(15);

        return view('institution.feedback.index', compact('documents'));
    }
}
