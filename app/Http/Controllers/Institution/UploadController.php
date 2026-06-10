<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UploadController extends Controller
{
    public function create(Request $request): View
    {
        $institutionId = $request->user()?->institution_id;

        $folders = DB::table('folders')
            ->where('is_visible_to_institutions', true)
            ->where(function ($query) use ($institutionId) {
                $query->whereNull('institution_id')->orWhere('institution_id', $institutionId);
            })
            ->orderBy('name')
            ->get();

        $outcomes = DB::table('institution_immediate_outcome')
            ->join('immediate_outcomes', 'institution_immediate_outcome.immediate_outcome_id', '=', 'immediate_outcomes.id')
            ->select('immediate_outcomes.*')
            ->where('institution_immediate_outcome.institution_id', $institutionId)
            ->orderBy('immediate_outcomes.number')
            ->get();

        return view('institution.uploads.create', compact('folders', 'outcomes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $institutionId = $request->user()?->institution_id;

        $validated = $request->validate([
            'folder_id' => ['required', 'integer', 'exists:folders,id'],
            'immediate_outcome_id' => ['nullable', 'integer', 'exists:immediate_outcomes,id'],
            'document' => ['required', 'file', 'max:51200'],
            'title' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $folderIsVisible = DB::table('folders')
            ->where('id', $validated['folder_id'])
            ->where('is_visible_to_institutions', true)
            ->where(function ($query) use ($institutionId) {
                $query->whereNull('institution_id')->orWhere('institution_id', $institutionId);
            })
            ->exists();

        if (! $folderIsVisible) {
            abort(403, 'This folder is not assigned to your institution.');
        }

        if (! empty($validated['immediate_outcome_id'])) {
            $assigned = DB::table('institution_immediate_outcome')
                ->where('institution_id', $institutionId)
                ->where('immediate_outcome_id', $validated['immediate_outcome_id'])
                ->exists();

            if (! $assigned) {
                abort(403, 'This Immediate Outcome is not assigned to your institution.');
            }
        }

        $path = $request->file('document')->store('documents/' . $institutionId, 'private');

        DB::table('documents')->insert([
            'institution_id' => $institutionId,
            'folder_id' => $validated['folder_id'],
            'immediate_outcome_id' => $validated['immediate_outcome_id'] ?? null,
            'title' => $validated['title'] ?: $request->file('document')->getClientOriginalName(),
            'file_path' => $path,
            'notes' => $validated['notes'] ?? null,
            'status' => 'submitted',
            'submitted_by' => $request->user()?->id,
            'submitted_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('institution.documents.index')->with('success', 'Document submitted to FIU successfully.');
    }
}
