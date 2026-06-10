<x-app-layout>
    <div class="space-y-8 p-6 lg:p-8">
        
        {{-- Page Header Block --}}
        <div class="flex flex-col gap-4 border-b border-slate-200 pb-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-950">Manage Users</h1>
                <p class="mt-1 text-sm text-slate-500">Oversee official system access, assign roles, and monitor institutional representative profiles across all tenants.</p>
            </div>
            <div class="sm:shrink-0">
                <a href="{{ route('fiu.dashboard') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-black text-slate-700 shadow-sm transition duration-200 hover:bg-slate-50">
                    Back to Dashboard
                </a>
            </div>
        </div>

        {{-- Main User Directory Container --}}
        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex h-64 flex-col items-center justify-center text-center">
                <div class="rounded-full bg-amber-50 p-3 text-amber-600 ring-1 ring-amber-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-sm font-black text-slate-950">No users found</h3>
                <p class="mt-1 max-w-sm text-xs text-slate-500">Institutional representative user accounts and profiles will be populated here for administrative oversight.</p>
            </div>
        </div>

    </div>
</x-app-layout>