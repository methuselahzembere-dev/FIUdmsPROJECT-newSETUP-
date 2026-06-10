<x-app-layout>
    <div class="space-y-8 p-6 lg:p-8">
        
        {{-- Page Header Block --}}
        <div class="flex flex-col gap-4 border-b border-slate-200 pb-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-950">Upload Submission File</h1>
                <p class="mt-1 text-sm text-slate-500">Initiate a new compliance tracking record by uploading localized institutional documentation directly to the FIU repository.</p>
            </div>
            <div class="sm:shrink-0">
                <a href="{{ route('fiu.dashboard') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-black text-slate-700 shadow-sm transition duration-200 hover:bg-slate-50">
                    Back to Dashboard
                </a>
            </div>
        </div>

        {{-- Upload Canvas Container --}}
        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm max-w-3xl mx-auto">
            <div class="border-2 border-dashed border-slate-200 rounded-2xl p-12 text-center hover:border-emerald-300 transition duration-200 group">
                <div class="mx-auto rounded-full bg-emerald-50 p-3 text-emerald-600 ring-1 ring-emerald-100 w-12 h-12 flex items-center justify-center group-hover:bg-emerald-100/70 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5v-15m0 0l-6.75 6.75M12 4.5l6.75 6.75" />
                    </svg>
                </div>
                
                <h3 class="mt-4 text-sm font-black text-slate-950">Drag and drop submission files here</h3>
                <p class="mt-1 text-xs text-slate-500">PDF, XML, or spreadsheet formats up to 25MB.</p>
                
                <div class="mt-6">
                    <button type="button" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-xs font-black text-white shadow transition duration-200 hover:bg-slate-800">
                        Select File from Local Storage
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>