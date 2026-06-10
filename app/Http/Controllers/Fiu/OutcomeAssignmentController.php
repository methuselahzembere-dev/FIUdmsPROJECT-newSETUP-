<?php

namespace App\Http\Controllers\Fiu;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OutcomeAssignmentController extends Controller
{
    public function index(): View
    {
        $assignments = DB::table('institution_immediate_outcome')
            ->join('institutions', 'institution_immediate_outcome.institution_id', '=', 'institutions.id')
            ->join('immediate_outcomes', 'institution_immediate_outcome.immediate_outcome_id', '=', 'immediate_outcomes.id')
            ->select('institution_immediate_outcome.*', 'institutions.name as institution_name', 'immediate_outcomes.number', 'immediate_outcomes.title')
            ->orderBy('institutions.name')
            ->orderBy('immediate_outcomes.number')
            ->paginate(25);

        return view('fiu.outcomes.assignments.index', compact('assignments'));
    }

    public function create(): View
    {
        $institutions = DB::table('institutions')->where('is_active', true)->orderBy('name')->get();
        $outcomes = DB::table('immediate_outcomes')->orderBy('number')->get();

        return view('fiu.outcomes.assignments.create', compact('institutions', 'outcomes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'institution_id' => ['required', 'integer', 'exists:institutions,id'],
            'immediate_outcome_ids' => ['required', 'array', 'min:1'],
            'immediate_outcome_ids.*' => ['integer', 'exists:immediate_outcomes,id'],
            'due_date' => ['nullable', 'date'],
        ]);

        foreach ($validated['immediate_outcome_ids'] as $outcomeId) {
            DB::table('institution_immediate_outcome')->updateOrInsert(
                [
                    'institution_id' => $validated['institution_id'],
                    'immediate_outcome_id' => $outcomeId,
                ],
                [
                    'due_date' => $validated['due_date'] ?? null,
                    'assigned_by' => $request->user()?->id,
                    'assigned_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        return redirect()->route('fiu.outcomes.assignments.index')->with('success', 'Immediate Outcomes assigned successfully.');
    }
}
