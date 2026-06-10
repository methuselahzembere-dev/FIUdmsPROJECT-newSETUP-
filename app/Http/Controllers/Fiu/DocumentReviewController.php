<?php

namespace App\Http\Controllers\Fiu;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DocumentReviewController extends Controller
{
    public function index(Request $request): View
    {
        $documents = DB::table('documents')
            ->leftJoin('institutions', 'documents.institution_id', '=', 'institutions.id')
            ->leftJoin('folders', 'documents.folder_id', '=', 'folders.id')
            ->select('documents.*', 'institutions.name as institution_name', 'folders.name as folder_name')
            ->when($request->string('status')->toString(), fn ($query, $status) => $query->where('documents.status', $status))
            ->when($request->string('q')->toString(), function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('documents.title', 'like', "%{$search}%")
                        ->orWhere('institutions.name', 'like', "%{$search}%")
                        ->orWhere('folders.name', 'like', "%{$search}%");
                });
            })
            ->latest('documents.updated_at')
            ->paginate(15);

        return view('fiu.documents.index', compact('documents'));
    }

    public function create(): View
    {
        $institutions = DB::table('institutions')->orderBy('name')->get();
        $folders = DB::table('folders')->orderBy('name')->get();

        return view('fiu.documents.create', compact('institutions', 'folders'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'institution_id' => ['required', 'integer', 'exists:institutions,id'],
            'folder_id' => ['required', 'integer', 'exists:folders,id'],
            'title' => ['required', 'string', 'max:255'],
            'document' => ['required', 'file', 'max:51200'],
            'status' => ['nullable', Rule::in(['draft', 'submitted', 'under-review', 'changes-requested', 'approved', 'archived'])],
            'review_notes' => ['nullable', 'string', 'max:4000'],
        ]);

        $path = $request->file('document')->store('documents', 'private');

        DB::table('documents')->insert([
            'institution_id' => $validated['institution_id'],
            'folder_id' => $validated['folder_id'],
            'title' => $validated['title'],
            'file_path' => $path,
            'status' => $validated['status'] ?? 'submitted',
            'review_notes' => $validated['review_notes'] ?? null,
            'submitted_by' => $request->user()?->id,
            'submitted_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('fiu.documents.index')->with('success', 'Document uploaded successfully.');
    }

    public function show(int|string $document): View
    {
        $document = DB::table('documents')
            ->leftJoin('institutions', 'documents.institution_id', '=', 'institutions.id')
            ->leftJoin('folders', 'documents.folder_id', '=', 'folders.id')
            ->select('documents.*', 'institutions.name as institution_name', 'folders.name as folder_name')
            ->where('documents.id', $document)
            ->firstOrFail();

        return view('fiu.documents.show', compact('document'));
    }

    public function edit(int|string $document): View
    {
        $document = DB::table('documents')->where('id', $document)->firstOrFail();
        $institutions = DB::table('institutions')->orderBy('name')->get();
        $folders = DB::table('folders')->orderBy('name')->get();

        return view('fiu.documents.edit', compact('document', 'institutions', 'folders'));
    }

    public function update(Request $request, int|string $document): RedirectResponse
    {
        $validated = $request->validate([
            'institution_id' => ['required', 'integer', 'exists:institutions,id'],
            'folder_id' => ['required', 'integer', 'exists:folders,id'],
            'title' => ['required', 'string', 'max:255'],
            'document' => ['nullable', 'file', 'max:51200'],
            'status' => ['required', Rule::in(['draft', 'submitted', 'under-review', 'changes-requested', 'approved', 'archived'])],
            'review_notes' => ['nullable', 'string', 'max:4000'],
        ]);

        $payload = [
            'institution_id' => $validated['institution_id'],
            'folder_id' => $validated['folder_id'],
            'title' => $validated['title'],
            'status' => $validated['status'],
            'review_notes' => $validated['review_notes'] ?? null,
            'reviewed_by' => $request->user()?->id,
            'reviewed_at' => now(),
            'updated_at' => now(),
        ];

        if ($request->hasFile('document')) {
            $existing = DB::table('documents')->where('id', $document)->first();
            if ($existing?->file_path) {
                Storage::disk('private')->delete($existing->file_path);
            }
            $payload['file_path'] = $request->file('document')->store('documents', 'private');
        }

        DB::table('documents')->where('id', $document)->update($payload);

        return redirect()->route('fiu.documents.show', $document)->with('success', 'Document review updated successfully.');
    }

    public function destroy(int|string $document): RedirectResponse
    {
        DB::table('documents')->where('id', $document)->update([
            'status' => 'archived',
            'archived_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('fiu.documents.index')->with('success', 'Document archived successfully.');
    }
}
