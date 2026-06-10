<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ImmediateOutcomeController extends Controller
{
    public function index(Request $request): View
    {
        $institutionId = $request->user()?->institution_id;

        $outcomes = DB::table('institution_immediate_outcome')
            ->join('immediate_outcomes', 'institution_immediate_outcome.immediate_outcome_id', '=', 'immediate_outcomes.id')
            ->select('immediate_outcomes.*', 'institution_immediate_outcome.due_date', 'institution_immediate_outcome.assigned_at')
            ->where('institution_immediate_outcome.institution_id', $institutionId)
            ->orderBy('immediate_outcomes.number')
            ->paginate(11);

        return view('institution.outcomes.index', compact('outcomes'));
    }

    public function show(Request $request, int|string $outcome): View
    {
        $institutionId = $request->user()?->institution_id;

        $outcome = DB::table('institution_immediate_outcome')
            ->join('immediate_outcomes', 'institution_immediate_outcome.immediate_outcome_id', '=', 'immediate_outcomes.id')
            ->select('immediate_outcomes.*', 'institution_immediate_outcome.due_date', 'institution_immediate_outcome.assigned_at')
            ->where('institution_immediate_outcome.institution_id', $institutionId)
            ->where(function ($query) use ($outcome) {
                $query->where('immediate_outcomes.id', $outcome)->orWhere('immediate_outcomes.number', $outcome);
            })
            ->firstOrFail();

        $documents = DB::table('documents')
            ->where('institution_id', $institutionId)
            ->where('immediate_outcome_id', $outcome->id)
            ->latest('updated_at')
            ->paginate(15);

        return view('institution.outcomes.show', compact('outcome', 'documents'));
    }
}
