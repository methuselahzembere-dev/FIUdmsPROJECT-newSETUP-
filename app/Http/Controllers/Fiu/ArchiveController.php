<?php

namespace App\Http\Controllers\Fiu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ArchiveController extends Controller
{
    public function index(Request $request): View
    {
        $documents = DB::table('documents')
            ->leftJoin('institutions', 'documents.institution_id', '=', 'institutions.id')
            ->leftJoin('folders', 'documents.folder_id', '=', 'folders.id')
            ->select('documents.*', 'institutions.name as institution_name', 'folders.name as folder_name')
            ->whereIn('documents.status', ['approved', 'archived'])
            ->when($request->string('q')->toString(), function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('documents.title', 'like', "%{$search}%")
                        ->orWhere('institutions.name', 'like', "%{$search}%")
                        ->orWhere('folders.name', 'like', "%{$search}%");
                });
            })
            ->latest('documents.updated_at')
            ->paginate(25);

        return view('fiu.archive.index', compact('documents'));
    }
}
