<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $institutionId = $request->user()?->institution_id;

        $documents = DB::table('documents')
            ->leftJoin('folders', 'documents.folder_id', '=', 'folders.id')
            ->leftJoin('immediate_outcomes', 'documents.immediate_outcome_id', '=', 'immediate_outcomes.id')
            ->select('documents.*', 'folders.name as folder_name', 'immediate_outcomes.number as outcome_number')
            ->where('documents.institution_id', $institutionId)
            ->when($request->string('status')->toString(), fn ($query, $status) => $query->where('documents.status', $status))
            ->when($request->string('q')->toString(), fn ($query, $search) => $query->where('documents.title', 'like', "%{$search}%"))
            ->latest('documents.updated_at')
            ->paginate(15);

        return view('institution.documents.index', compact('documents'));
    }

    public function show(Request $request, int|string $document): View
    {
        $institutionId = $request->user()?->institution_id;

        $document = DB::table('documents')
            ->leftJoin('folders', 'documents.folder_id', '=', 'folders.id')
            ->leftJoin('immediate_outcomes', 'documents.immediate_outcome_id', '=', 'immediate_outcomes.id')
            ->select('documents.*', 'folders.name as folder_name', 'immediate_outcomes.number as outcome_number', 'immediate_outcomes.title as outcome_title')
            ->where('documents.institution_id', $institutionId)
            ->where('documents.id', $document)
            ->firstOrFail();

        return view('institution.documents.show', compact('document'));
    }
}
