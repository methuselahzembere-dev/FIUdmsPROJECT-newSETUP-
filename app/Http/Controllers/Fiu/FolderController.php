<?php

namespace App\Http\Controllers\Fiu;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FolderController extends Controller
{
    public function index(Request $request): View
    {
        $folders = DB::table('folders')
            ->leftJoin('compliance_tracks', 'folders.compliance_track_id', '=', 'compliance_tracks.id')
            ->select('folders.*', 'compliance_tracks.name as track_name')
            ->when($request->string('track')->toString(), fn ($query, $track) => $query->where('compliance_tracks.slug', $track))
            ->orderBy('compliance_tracks.name')
            ->orderBy('folders.name')
            ->paginate(15);

        return view('fiu.folders.index', compact('folders'));
    }

    public function create(): View
    {
        $tracks = DB::table('compliance_tracks')->orderBy('name')->get();

        return view('fiu.folders.create', compact('tracks'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'compliance_track_id' => ['required', 'integer', 'exists:compliance_tracks,id'],
            'parent_id' => ['nullable', 'integer', 'exists:folders,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_visible_to_institutions' => ['sometimes', 'boolean'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_visible_to_institutions'] = $request->boolean('is_visible_to_institutions', true);
        $validated['created_at'] = now();
        $validated['updated_at'] = now();

        DB::table('folders')->insert($validated);

        return redirect()->route('fiu.folders.index')->with('success', 'Folder created successfully.');
    }

    public function show(int|string $folder): View
    {
        $folder = DB::table('folders')->where('id', $folder)->orWhere('slug', $folder)->firstOrFail();
        $documents = DB::table('documents')->where('folder_id', $folder->id)->latest('updated_at')->paginate(15);

        return view('fiu.folders.show', compact('folder', 'documents'));
    }

    public function edit(int|string $folder): View
    {
        $folder = DB::table('folders')->where('id', $folder)->orWhere('slug', $folder)->firstOrFail();
        $tracks = DB::table('compliance_tracks')->orderBy('name')->get();

        return view('fiu.folders.edit', compact('folder', 'tracks'));
    }

    public function update(Request $request, int|string $folder): RedirectResponse
    {
        $validated = $request->validate([
            'compliance_track_id' => ['required', 'integer', 'exists:compliance_tracks,id'],
            'parent_id' => ['nullable', 'integer', 'exists:folders,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_visible_to_institutions' => ['sometimes', 'boolean'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_visible_to_institutions'] = $request->boolean('is_visible_to_institutions', true);
        $validated['updated_at'] = now();

        DB::table('folders')->where('id', $folder)->orWhere('slug', $folder)->update($validated);

        return redirect()->route('fiu.folders.index')->with('success', 'Folder updated successfully.');
    }

    public function destroy(int|string $folder): RedirectResponse
    {
        DB::table('folders')->where('id', $folder)->orWhere('slug', $folder)->delete();

        return redirect()->route('fiu.folders.index')->with('success', 'Folder removed successfully.');
    }
}
