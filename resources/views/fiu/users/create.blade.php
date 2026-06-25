<x-app-layout>
    <div class="space-y-6 max-w-4xl mx-auto">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-violet-600">User Management</p>
                <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Create System User</h1>
                <p class="mt-2 text-sm leading-6 text-slate-600">
                    Provision a new account. Assigning an Institution Representative role will require linking them to a specific reporting tenant.
                </p>
            </div>
            <a href="{{ route('fiu.users.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700 transition">
                &larr; Back to Users
            </a>
        </div>

        <form action="{{ route('fiu.users.store') }}" method="POST" class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden" x-data="{ role: 'fiu_reviewer' }">
            @csrf

            <div class="p-6 sm:p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-900">Full Name</label>
                        <input type="text" name="name" id="name" required value="{{ old('name') }}" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-violet-500 focus:ring-violet-500">
                        @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-900">Email Address</label>
                        <input type="email" name="email" id="email" required value="{{ old('email') }}" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-violet-500 focus:ring-violet-500">
                        @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <hr class="border-slate-100">

                <div>
                    <label for="role" class="block text-sm font-semibold text-slate-900">System Role</label>
                    <select name="role" id="role" required x-model="role" class="mt-2 w-full max-w-md rounded-xl border-slate-200 text-sm focus:border-violet-500 focus:ring-violet-500">
                        <option value="fiu_reviewer">FIU Reviewer (Internal)</option>
                        <option value="fiu_admin">FIU Administrator (Internal)</option>
                        <option value="institution_representative">Institution Representative (External)</option>
                    </select>
                    @error('role') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div x-show="role === 'institution_representative'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                    <label for="institution_id" class="block text-sm font-semibold text-slate-900">Assign to Institution</label>
                    <p class="mb-2 text-xs text-slate-500">This user will only see documents and workspaces assigned to this tenant.</p>
                    
                    <select name="institution_id" id="institution_id" class="w-full max-w-md rounded-xl border-slate-200 text-sm focus:border-violet-500 focus:ring-violet-500" :required="role === 'institution_representative'">
                        <option value="">-- Select an Institution --</option>
                        @foreach($institutions as $inst)
                            <option value="{{ $inst->id }}" @selected(old('institution_id') == $inst->id)>{{ $inst->name }}</option>
                        @endforeach
                    </select>
                    @error('institution_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <hr class="border-slate-100">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-900">Password</label>
                        <input type="password" name="password" id="password" required class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-violet-500 focus:ring-violet-500">
                        @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-slate-900">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-violet-500 focus:ring-violet-500">
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3 border-t border-slate-200">
                <a href="{{ route('fiu.users.index') }}" class="inline-flex items-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-violet-700">
                    Create User
                </button>
            </div>
        </form>
    </div>
</x-app-layout>