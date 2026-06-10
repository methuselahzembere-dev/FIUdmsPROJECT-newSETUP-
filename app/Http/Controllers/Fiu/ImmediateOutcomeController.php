<?php

namespace App\Http\Controllers\Fiu;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ImmediateOutcomeController extends Controller
{
    public function index(): View
    {
        $outcomes = DB::table('immediate_outcomes')
            ->orderBy('number')
            ->paginate(11);

        return view('fiu.outcomes.index', compact('outcomes'));
    }

    public function create(): View
    {
        return view('fiu.outcomes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'number' => ['required', 'integer', 'min:1', 'max:11', 'unique:immediate_outcomes,number'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $validated['created_at'] = now();
        $validated['updated_at'] = now();

        DB::table('immediate_outcomes')->insert($validated);

        return redirect()->route('fiu.outcomes.index')->with('success', 'Immediate Outcome created successfully.');
    }

    public function show(int|string $outcome): View
    {
        $outcome = DB::table('immediate_outcomes')->where('id', $outcome)->orWhere('number', $outcome)->firstOrFail();
        $assignments = DB::table('institution_immediate_outcome')
            ->join('institutions', 'institution_immediate_outcome.institution_id', '=', 'institutions.id')
            ->select('institution_immediate_outcome.*', 'institutions.name as institution_name')
            ->where('institution_immediate_outcome.immediate_outcome_id', $outcome->id)
            ->orderBy('institutions.name')
            ->get();

        return view('fiu.outcomes.show', compact('outcome', 'assignments'));
    }

    public function edit(int|string $outcome): View
    {
        $outcome = DB::table('immediate_outcomes')->where('id', $outcome)->orWhere('number', $outcome)->firstOrFail();

        return view('fiu.outcomes.edit', compact('outcome'));
    }

    public function update(Request $request, int|string $outcome): RedirectResponse
    {
        $record = DB::table('immediate_outcomes')->where('id', $outcome)->orWhere('number', $outcome)->firstOrFail();

        $validated = $request->validate([
            'number' => ['required', 'integer', 'min:1', 'max:11'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $validated['updated_at'] = now();

        DB::table('immediate_outcomes')->where('id', $record->id)->update($validated);

        return redirect()->route('fiu.outcomes.show', $record->id)->with('success', 'Immediate Outcome updated successfully.');
    }

    public function destroy(int|string $outcome): RedirectResponse
    {
        $record = DB::table('immediate_outcomes')->where('id', $outcome)->orWhere('number', $outcome)->firstOrFail();
        DB::table('immediate_outcomes')->where('id', $record->id)->delete();

        return redirect()->route('fiu.outcomes.index')->with('success', 'Immediate Outcome removed successfully.');
    }
}
