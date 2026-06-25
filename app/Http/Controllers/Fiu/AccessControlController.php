<?php

namespace App\Http\Controllers\Fiu;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\EffectivenessImmediateOutcome;
use Illuminate\Http\Request;

class AccessControlController extends Controller
{
    /**
     * Display the Institution-Centric access control matrix.
     */
    public function index(Request $request, Institution $institution = null)
    {
        // 1. Sidebar: Fetch all Institutions
        $institutions = Institution::orderBy('name')->get();
        
        // 2. Active State: Default to the first institution if none selected
        $activeInstitution = $institution ?? $institutions->first();
        
        // 3. Main Grid: Fetch all Immediate Outcomes
        $outcomes = EffectivenessImmediateOutcome::orderBy('code')->get();
        
        // 4. The Bridge: Get the IDs of IOs assigned to this specific institution
        $assignedIds = $activeInstitution ? $activeInstitution->effectivenessImmediateOutcomes()->pluck('effectiveness_immediate_outcomes.id')->toArray() : [];

        return view('fiu.access-control.index', compact('institutions', 'activeInstitution', 'outcomes', 'assignedIds'));
    }

    /**
     * Sync the selected Immediate Outcomes to the Institution.
     */
    public function sync(Request $request, Institution $institution)
    {
        $validated = $request->validate([
            'outcome_ids' => 'nullable|array',
            'outcome_ids.*' => 'exists:effectiveness_immediate_outcomes,id',
        ]);

        // Perfectly syncs the pivot table!
        $institution->effectivenessImmediateOutcomes()->sync($validated['outcome_ids'] ?? []);

        return back()->with('status', "Access rights updated for {$institution->name}");
    }
}