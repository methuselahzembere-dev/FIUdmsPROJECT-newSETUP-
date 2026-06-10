<?php

namespace App\Http\Controllers\Fiu;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ComplianceReportController extends Controller
{
    public function index(): View
    {
        $summary = [
            'institutions' => DB::table('institutions')->count(),
            'submitted' => DB::table('documents')->where('status', 'submitted')->count(),
            'under_review' => DB::table('documents')->where('status', 'under-review')->count(),
            'changes_requested' => DB::table('documents')->where('status', 'changes-requested')->count(),
            'approved' => DB::table('documents')->where('status', 'approved')->count(),
            'archived' => DB::table('documents')->where('status', 'archived')->count(),
        ];

        $institutionSummaries = DB::table('institutions')
            ->leftJoin('documents', 'institutions.id', '=', 'documents.institution_id')
            ->select('institutions.id', 'institutions.name')
            ->selectRaw('COUNT(documents.id) as documents_count')
            ->selectRaw("SUM(CASE WHEN documents.status = 'approved' THEN 1 ELSE 0 END) as approved_count")
            ->groupBy('institutions.id', 'institutions.name')
            ->orderBy('institutions.name')
            ->get();

        return view('fiu.reports.compliance', compact('summary', 'institutionSummaries'));
    }
}
