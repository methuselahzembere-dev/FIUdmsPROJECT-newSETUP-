<x-app-layout>
    <div class="space-y-8 p-6 lg:p-8">
        
        {{-- Page Header Block --}}
        <div class="flex flex-col gap-4 border-b border-slate-200 pb-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-950">Manage Immediate Outcome Assignments</h1>
                <p class="mt-1 text-sm text-slate-500">Map national strategic compliance goals, distribute Immediate Outcome (IO) targets, and monitor tracking metrics among active institutions.</p>
            </div>
            <div class="sm:shrink-0">
                <a href="{{ route('fiu.dashboard') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-black text-slate-700 shadow-sm transition duration-200 hover:bg-slate-50">
                    Back to Dashboard
                </a>
            </div>
        </div>

        {{-- Main IO Management Board Canvas --}}
        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex h-64 flex-col items-center justify-center text-center">
                <div class="rounded-full bg-purple-50 p-3 text-purple-600 ring-1 ring-purple-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-sm font-black text-slate-950">No strategic IO targets assigned</h3>
                <p class="mt-1 max-w-sm text-xs text-slate-500">Strategic tracking lines and FATF Immediate Outcome parameters can be linked directly to compliance entities here.</p>
            </div>
        </div>

    </div>
</x-app-layout>