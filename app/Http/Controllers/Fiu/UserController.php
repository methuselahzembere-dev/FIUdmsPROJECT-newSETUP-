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
   public function index(Request $request)
    {
        // 1. Start the query and Eager Load the institution to optimize database calls
        $query = User::with('institution')->latest();

        // 2. Apply Smart Search (Name or Email)
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        // 3. Apply Role Filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // 4. Apply Institution Filter
        if ($request->filled('institution_id')) {
            $query->where('institution_id', $request->institution_id);
        }

        // 5. Execute query with Pagination (15 per page) and preserve filter URLs
        $users = $query->paginate(15)->withQueryString();

        // 6. Fetch institutions to populate the filter dropdown
        $institutions = \App\Models\Institution::orderBy('name')->get();

        return view('fiu.users.index', compact('users', 'institutions'));
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
