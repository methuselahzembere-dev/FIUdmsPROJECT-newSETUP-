<x-app-layout>
    <div class="space-y-8 p-6 lg:p-8">
        
        {{-- Modern Page Header --}}
        <div class="flex flex-col gap-4 border-b border-slate-200 pb-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-950">Review Submissions</h1>
                <p class="mt-1 text-sm text-slate-500">Manage incoming compliance documents, check audit trails, and request revisions from reporting institutions.</p>
            </div>
            <div class="sm:shrink-0">
                <a href="{{ route('fiu.dashboard') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-black text-slate-700 shadow-sm transition duration-200 hover:bg-slate-50">
                    Back to Dashboard
                </a>
            </div>
        </div>

        {{-- Submission Table / Grid Canvas Container --}}
        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex h-64 flex-col items-center justify-center text-center">
                <div class="rounded-full bg-indigo-50 p-3 text-indigo-600 ring-1 ring-indigo-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-sm font-black text-slate-950">No files under active review</h3>
                <p class="mt-1 max-w-sm text-xs text-slate-500">Incoming documents from your 14–16 reporting institutions will automatically appear here for verification workflows.</p>
            </div>
        </div>

    </div>
</x-app-layout>