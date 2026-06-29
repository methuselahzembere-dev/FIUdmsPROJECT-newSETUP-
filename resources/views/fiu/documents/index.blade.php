<x-app-layout>
    <div x-data="{ 
            drawerOpen: false, 
            docId: null, 
            docTitle: '', 
            docStatus: '', 
            docFolder: '',
            docDate: '',
            docUrl: '',

            // 🌟 FIXED: Added 'url' to the function arguments here!
            openDrawer(id, title, status, folder, date, url) {
                this.docId = id;
                this.docTitle = title;
                this.docStatus = status;
                this.docFolder = folder;
                this.docDate = date;
                this.docUrl = url;
                this.drawerOpen = true;
            }
        }" 
        class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8 pb-12">
        
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-violet-600">IDMS Command Center</p>
                <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Central Documents</h1>
                <p class="mt-2 text-sm leading-6 text-slate-600">
                    Review submissions, track compliance, and manage the effectiveness matrix.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <a href="{{ route('fiu.documents.index', array_merge(request()->query(), ['status' => 'pending'])) }}" 
               class="relative overflow-hidden rounded-2xl border bg-white p-6 shadow-sm transition hover:shadow-md border-amber-200 {{ request('status') === 'pending' ? 'ring-2 ring-amber-500 ring-offset-2' : '' }}">
                <dt class="truncate text-sm font-medium text-amber-600">Pending Review</dt>
                <dd class="mt-2 text-3xl font-bold tracking-tight text-slate-900">{{ $metrics['pending'] ?? 0 }}</dd>
            </a>

            <a href="{{ route('fiu.documents.index', array_merge(request()->query(), ['status' => 'in_progress'])) }}" 
               class="relative overflow-hidden rounded-2xl border bg-white p-6 shadow-sm transition hover:shadow-md border-blue-200 {{ request('status') === 'in_progress' ? 'ring-2 ring-blue-500 ring-offset-2' : '' }}">
                <dt class="truncate text-sm font-medium text-blue-600">In Progress</dt>
                <dd class="mt-2 text-3xl font-bold tracking-tight text-slate-900">{{ $metrics['in_progress'] ?? 0 }}</dd>
            </a>

            <a href="{{ route('fiu.documents.index', array_merge(request()->query(), ['status' => 'approved'])) }}" 
               class="relative overflow-hidden rounded-2xl border bg-white p-6 shadow-sm transition hover:shadow-md border-emerald-200 {{ request('status') === 'approved' ? 'ring-2 ring-emerald-500 ring-offset-2' : '' }}">
                <dt class="truncate text-sm font-medium text-emerald-600">Approved</dt>
                <dd class="mt-2 text-3xl font-bold tracking-tight text-slate-900">{{ $metrics['approved'] ?? 0 }}</dd>
            </a>

            <a href="{{ route('fiu.documents.index', array_merge(request()->query(), ['status' => 'returned'])) }}" 
               class="relative overflow-hidden rounded-2xl border bg-white p-6 shadow-sm transition hover:shadow-md border-rose-200 {{ request('status') === 'returned' ? 'ring-2 ring-rose-500 ring-offset-2' : '' }}">
                <dt class="truncate text-sm font-medium text-rose-600">Returned / Revisions</dt>
                <dd class="mt-2 text-3xl font-bold tracking-tight text-slate-900">{{ $metrics['returned'] ?? 0 }}</dd>
            </a>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            
            <form method="GET" action="{{ route('fiu.documents.index') }}" class="flex flex-col gap-4 border-b border-slate-100 p-4 sm:flex-row sm:items-center bg-slate-50/50">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search documents, institutions, or folders..." class="w-full rounded-xl border-slate-200 py-2 pl-9 pr-4 text-sm focus:border-violet-500 focus:ring-violet-500">
                </div>

                @if(isset($filterInstitutions) && $filterInstitutions->isNotEmpty())
                    <select name="institution_id" class="w-full rounded-xl border-slate-200 text-sm focus:border-violet-500 focus:ring-violet-500 sm:w-64">
                        <option value="">All Institutions</option>
                        @foreach($filterInstitutions as $inst)
                            <option value="{{ $inst->id }}" @selected(request('institution_id') == $inst->id)>{{ $inst->name }}</option>
                        @endforeach
                    </select>
                @endif

                <div class="flex items-center gap-2">
                    <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">Search</button>
                    @if(request()->hasAny(['q', 'institution_id', 'status']))
                        <a href="{{ route('fiu.documents.index') }}" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-500 hover:bg-slate-200 transition">Clear</a>
                    @endif
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="border-b border-slate-200 bg-white text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold">Document Info</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Source</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Status</th>
                            <th scope="col" class="px-6 py-4 text-right font-semibold">Last Updated</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($documents as $doc)

                        <tr @click="openDrawer({{ $doc->id }}, '{{ addslashes($doc->title) }}', '{{ $doc->status }}', '{{ addslashes($doc->folder->name ?? 'Uncategorized') }}', '{{ $doc->updated_at->diffForHumans() }}', '{{ route('fiu.documents.show', $doc->id) }}')" 
                           class="transition hover:bg-slate-50 cursor-pointer group">
     
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500 group-hover:bg-violet-100 group-hover:text-violet-600 transition">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-slate-900">{{ $doc->title }}</div>
                                            <div class="text-xs text-slate-500">{{ $doc->folder->name ?? 'Uncategorized' }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($doc->institutions as $inst)
                                            <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">
                                                {{ $inst->name }}
                                            </span>
                                        @empty
                                            <span class="text-slate-400 text-xs">No Institution</span>
                                        @endforelse
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    @if($doc->status === 'pending')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Pending
                                        </span>
                                    @elseif($doc->status === 'in_progress')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20">
                                            <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span> In Progress
                                        </span>
                                    @elseif($doc->status === 'approved')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Approved
                                        </span>
                                    @elseif($doc->status === 'returned')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-medium text-rose-700 ring-1 ring-inset ring-rose-600/20">
                                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span> Returned
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-md bg-slate-50 px-2 py-1 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-500/20">
                                            {{ ucfirst($doc->status) }}
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right text-sm text-slate-500">
                                    {{ $doc->updated_at->diffForHumans() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                    No documents found matching your criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(isset($documents) && $documents->hasPages())
                <div class="border-t border-slate-100 px-6 py-4">
                    {{ $documents->links() }}
                </div>
            @endif
        </div>

        <div x-show="drawerOpen" class="relative z-50" aria-labelledby="slide-over-title" role="dialog" aria-modal="true" style="display: none;">
            <div x-show="drawerOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 overflow-hidden">
                    <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
                        <div x-show="drawerOpen" 
                             x-transition:enter="transform transition ease-in-out duration-300" 
                             x-transition:enter-start="translate-x-full" 
                             x-transition:enter-end="translate-x-0" 
                             x-transition:leave="transform transition ease-in-out duration-300" 
                             x-transition:leave-start="translate-x-0" 
                             x-transition:leave-end="translate-x-full" 
                             class="pointer-events-auto w-screen max-w-md">
                            
                            <div class="flex h-full flex-col bg-white shadow-2xl">
                                <div class="px-6 py-6 sm:px-8 border-b border-slate-100">
                                    <div class="flex items-start justify-between">
                                        <h2 class="text-lg font-semibold leading-6 text-slate-900" id="slide-over-title">Document Details</h2>
                                        <button @click="drawerOpen = false" type="button" class="relative rounded-md text-slate-400 hover:text-slate-600 focus:outline-none">
                                            <span class="sr-only">Close panel</span>
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="relative flex-1 px-6 py-6 sm:px-8 overflow-y-auto">
                                    <div class="space-y-6">
                                        <div>
                                            <h3 class="text-xl font-bold text-slate-900" x-text="docTitle"></h3>
                                            <p class="text-sm text-violet-600 font-medium mt-1" x-text="docFolder"></p>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4 border-y border-slate-100 py-4">
                                            <div>
                                                <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Status</p>
                                                <p class="mt-1 text-sm font-medium text-slate-900 capitalize" x-text="docStatus.replace('_', ' ')"></p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Last Activity</p>
                                                <p class="mt-1 text-sm font-medium text-slate-900" x-text="docDate"></p>
                                            </div>
                                        </div>

                                        <div class="rounded-xl bg-slate-50 p-4 border border-slate-100">
                                            <div class="flex gap-3">
                                                <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                <p class="text-sm text-slate-600">Review capabilities, comment threads, and file downloads will be accessible from the main review interface.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-shrink-0 justify-end px-4 py-4 bg-slate-50 border-t border-slate-100 gap-3">
                                    <button @click="drawerOpen = false" type="button" class="rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50">Cancel</button>
                                    
                                    <a :href="docUrl" class="inline-flex justify-center rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-violet-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-violet-600">
                                        Open Review Workspace &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>