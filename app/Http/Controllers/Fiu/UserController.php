<?php

namespace App\Http\Controllers\Fiu;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = DB::table('users')
            ->leftJoin('institutions', 'users.institution_id', '=', 'institutions.id')
            ->select('users.*', 'institutions.name as institution_name')
            ->when($request->string('q')->toString(), function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('users.name', 'like', "%{$search}%")
                        ->orWhere('users.email', 'like', "%{$search}%")
                        ->orWhere('institutions.name', 'like', "%{$search}%");
                });
            })
            ->latest('users.updated_at')
            ->paginate(15);

        return view('fiu.users.index', compact('users'));
    }


    public function create()
    {
        // Fetch institutions to populate the smart dropdown
        $institutions = \App\Models\Institution::orderBy('name')->get();
        
        return view('fiu.users.create', compact('institutions'));
    }

          public function store(Request $request)
    {
        // 1. Strict Validation 
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:fiu_reviewer,fiu_admin,institution_representative',
            'institution_id' => 'required_if:role,institution_representative|nullable|exists:institutions,id',
        ]);

        // 2. Create the User 
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'], 
            'institution_id' => $validated['role'] === 'institution_representative' ? $validated['institution_id'] : null,
        ]);

        // 3. Redirect with a Success Flash Message
        return redirect()->route('fiu.users.index')
            ->with('success', "Account for {$user->name} has been successfully provisioned.");
    }

    public function show(int|string $user): View
    {
        $user = DB::table('users')
            ->leftJoin('institutions', 'users.institution_id', '=', 'institutions.id')
            ->select('users.*', 'institutions.name as institution_name')
            ->where('users.id', $user)
            ->firstOrFail();

        return view('fiu.users.show', compact('user'));
    }

    public function edit(int|string $user): View
    {
        $user = DB::table('users')->where('id', $user)->firstOrFail();
        $institutions = DB::table('institutions')->where('is_active', true)->orderBy('name')->get();

        return view('fiu.users.edit', compact('user', 'institutions'));
    }

    public function update(Request $request, int|string $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'role' => ['required', Rule::in(['fiu_admin', 'fiu_reviewer', 'institution_representative'])],
            'institution_id' => ['nullable', 'integer', 'exists:institutions,id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['updated_at'] = now();

        DB::table('users')->where('id', $user)->update($validated);

        return redirect()->route('fiu.users.show', $user)->with('success', 'User account updated successfully.');
    }

    public function destroy(int|string $user): RedirectResponse
    {
        DB::table('users')->where('id', $user)->delete();

        return redirect()->route('fiu.users.index')->with('success', 'User account removed successfully.');
    }
}
