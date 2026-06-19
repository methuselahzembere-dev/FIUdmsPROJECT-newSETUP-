<?php

namespace App\Http\Controllers\Fiu;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\EffectivenessImmediateOutcome;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OutcomeAssignmentController extends Controller
{
    /**
     * Display a paginated listing of assigned framework outcomes across tenants.
     */
    public function index(): View
    {
        // Fetch all 11 core outcomes along with their mapped institutions eagerly loaded
        $outcomes = EffectivenessImmediateOutcome::with('institutions')
            ->orderBy('number')
            ->get();

        return view('fiu.outcomes.assignments.index', compact('outcomes'));
    }

    /**
     * Show the configuration form grid to assign outcomes.
     */
    public function create(): View
    {
        $institutions = Institution::where('is_active', true)->orderBy('name')->get();
        $outcomes = EffectivenessImmediateOutcome::orderBy('number')->get();

        return view('fiu.outcomes.assignments.create', compact('institutions', 'outcomes'));
    }

    /**
     * Persist the updated multi-tenant cross-reference mapping grid.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'assignments' => ['nullable', 'array'],
            'assignments.*' => ['nullable', 'array'],
            'assignments.*.*' => ['integer', 'exists:institutions,id'],
        ]);

        // Wrap execution inside a Database Transaction for atomic security
        DB::transaction(function () use ($validated) {
            $allOutcomes = EffectivenessImmediateOutcome::all();

            foreach ($allOutcomes as $outcome) {
                // Grab selected institutions for this specific IO, fallback to empty array if unassigned
                $assignedInstitutionIds = $validated['assignments'][$outcome->id] ?? [];
                
                // sync() cleanly wipes out old records and registers new pivot rows instantly
                $outcome->institutions()->sync($assignedInstitutionIds);
            }
        });

        return redirect()
            ->route('fiu.outcomes.assignments.index')
            ->with('success', 'Immediate Outcomes assigned and synchronized successfully.');
    }
}