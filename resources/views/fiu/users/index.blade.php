<x-app-layout>
    <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
        
        <!-- Command Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-violet-600">Access Management</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">System Users</h1>
                <p class="mt-1 text-sm text-slate-500">Manage internal reviewers and external institution representatives.</p>
            </div>
            
            <a href="{{ route('fiu.users.create') }}" class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-violet-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                Create User
            </a>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Smart Filter Ribbon -->
        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">
            <form method="GET" action="{{ route('fiu.users.index') }}" class="flex flex-col gap-4 border-b border-slate-100 p-4 sm:flex-row sm:items-center">
                
                <!-- Search -->
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email..." class="w-full rounded-xl border-slate-200 py-2 pl-9 pr-4 text-sm focus:border-violet-500 focus:ring-violet-500">
                </div>

                <!-- Role Filter -->
                <select name="role" class="w-full rounded-xl border-slate-200 text-sm focus:border-violet-500 focus:ring-violet-500 sm:w-48">
                    <option value="">All Roles</option>
                    <option value="fiu_admin" @selected(request('role') == 'fiu_admin')>FIU Admin</option>
                    <option value="fiu_reviewer" @selected(request('role') == 'fiu_reviewer')>FIU Reviewer</option>
                    <option value="institution_representative" @selected(request('role') == 'institution_representative')>Institution Rep</option>
                </select>

                <!-- Institution Filter -->
                <select name="institution_id" class="w-full rounded-xl border-slate-200 text-sm focus:border-violet-500 focus:ring-violet-500 sm:w-64">
                    <option value="">All Institutions</option>
                    @foreach($institutions as $inst)
                        <option value="{{ $inst->id }}" @selected(request('institution_id') == $inst->id)>{{ $inst->name }}</option>
                    @endforeach
                </select>

                <!-- Actions -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">Filter</button>
                    @if(request()->hasAny(['search', 'role', 'institution_id']))
                        <a href="{{ route('fiu.users.index') }}" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-500 hover:bg-slate-100 hover:text-slate-700">Clear</a>
                    @endif
                </div>
            </form>

            <!-- Data Matrix -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold">User Identity</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Access & Tenant</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Status & Timeline</th>
                            <th scope="col" class="px-6 py-4 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($users as $user)
                            <tr class="transition hover:bg-slate-50">
                                <!-- Identity -->
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-violet-100 font-bold text-violet-700">
                                            {{ substr($user->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-slate-900">{{ $user->name }}</div>
                                            <div class="text-xs text-slate-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Access & Tenant -->
                                <td class="px-6 py-4">
                                    <div class="flex flex-col items-start gap-1">
                                        @if($user->role === 'fiu_admin')
                                            <span class="inline-flex items-center rounded-md bg-purple-50 px-2 py-1 text-xs font-medium text-purple-700 ring-1 ring-inset ring-purple-600/20">FIU Admin</span>
                                        @elseif($user->role === 'fiu_reviewer')
                                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20">FIU Reviewer</span>
                                        @elseif($user->role === 'institution_representative')
                                            <span class="inline-flex items-center rounded-md bg-teal-50 px-2 py-1 text-xs font-medium text-teal-700 ring-1 ring-inset ring-teal-600/20">Institution Rep</span>
                                            @if($user->institution)
                                                <span class="mt-1 flex items-center gap-1 text-[11px] font-semibold text-slate-500">
                                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                                    {{ $user->institution->name }}
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </td>

                                <!-- Timeline -->
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="relative flex h-2.5 w-2.5">
                                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                                            <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                                        </span>
                                        <span class="text-xs font-medium text-slate-700">Active</span>
                                    </div>
                                    <div class="mt-1 text-[11px] text-slate-500">Joined {{ $user->created_at->format('M j, Y') }}</div>
                                </td>

                                <!-- Actions -->
                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    <div x-data="{ open: false }" class="relative inline-block text-left">
                                        <button @click="open = !open" @click.away="open = false" type="button" class="flex h-8 w-8 items-center justify-center rounded-full hover:bg-slate-200 focus:outline-none">
                                            <svg class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" /></svg>
                                        </button>

                                        <div x-show="open" x-transition.opacity.duration.200ms style="display: none;" class="absolute right-0 z-10 mt-2 w-40 origin-top-right rounded-xl border border-slate-100 bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                            <div class="py-1">
                                                <a href="#" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-violet-600">Edit Profile</a>
                                                <a href="#" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-violet-600">Reset Password</a>
                                                <hr class="my-1 border-slate-100">
                                                <a href="#" class="block px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50">Suspend User</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                    <h3 class="mt-2 text-sm font-semibold text-slate-900">No users found</h3>
                                    <p class="mt-1 text-sm text-slate-500">We couldn't find any users matching your criteria.</p>
                                    <div class="mt-4">
                                        <a href="{{ route('fiu.users.index') }}" class="text-sm font-medium text-violet-600 hover:text-violet-500">Clear all filters</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Footer -->
            @if($users->hasPages())
                <div class="border-t border-slate-100 px-6 py-4">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>