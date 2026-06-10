<?php

namespace App\Http\Controllers\Fiu;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InstitutionController extends Controller
{
    public function index(Request $request): View
    {
        $institutions = DB::table('institutions')
            ->when($request->string('q')->toString(), fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->latest('updated_at')
            ->paginate(15);

        return view('fiu.institutions.index', compact('institutions'));
    }

    public function create(): View
    {
        return view('fiu.institutions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:institutions,name'],
            'short_name' => ['nullable', 'string', 'max:50'],
            'sector' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['created_at'] = now();
        $validated['updated_at'] = now();

        DB::table('institutions')->insert($validated);

        return redirect()->route('fiu.institutions.index')->with('success', 'Institution profile created successfully.');
    }

    public function show(int|string $institution): View
    {
        $institution = DB::table('institutions')->where('id', $institution)->firstOrFail();

        return view('fiu.institutions.show', compact('institution'));
    }

    public function edit(int|string $institution): View
    {
        $institution = DB::table('institutions')->where('id', $institution)->firstOrFail();

        return view('fiu.institutions.edit', compact('institution'));
    }

    public function update(Request $request, int|string $institution): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:50'],
            'sector' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', false);
        $validated['updated_at'] = now();

        DB::table('institutions')->where('id', $institution)->update($validated);

        return redirect()->route('fiu.institutions.show', $institution)->with('success', 'Institution profile updated successfully.');
    }

    public function destroy(int|string $institution): RedirectResponse
    {
        DB::table('institutions')->where('id', $institution)->update([
            'is_active' => false,
            'updated_at' => now(),
        ]);

        return redirect()->route('fiu.institutions.index')->with('success', 'Institution profile deactivated successfully.');
    }
}
