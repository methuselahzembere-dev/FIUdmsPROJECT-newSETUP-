<x-app-layout>
@php
    $immediateOutcomes = $immediateOutcomes ?? collect();
    $immediateOutcome = $immediateOutcome ?? $immediateOutcomes->first();
    $subOutcomes = $subOutcomes ?? ($immediateOutcome?->subOutcomes?->values() ?? collect());
    
    // 1. Figure out which Sub-IO is selected based on the URL parameter (or default to the first one)
    $selectedSubIoCode = request('sub_io');
    $selectedSubOutcome = $selectedSubIoCode 
        ? $subOutcomes->firstWhere('code', $selectedSubIoCode) 
        : $subOutcomes->first();

    $documents = $selectedSubOutcome ? $selectedSubOutcome->documents : collect();
    
    // 3. Simple counts
    $currentDocumentTotal = $documents->count();
@endphp

<div 
    x-data="{ 
        sidebarOpen: true,  
        previewOpen: false, 
        previewUrl: '',  
        previewTitle: '', 
        isMaximized: false 
    }" 
    class="space-y-6"
>
    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div class="space-y-3">
            {{-- Added Native Browser Click Navigation to break past Alpine.js state hijacking --}}
            <a
                href="{{ route('fiu.effectiveness.folders.index') }}"
                onclick="window.location.href='{{ route('fiu.effectiveness.folders.index') }}';"
                class="inline-flex items-center text-sm font-black text-violet-700 transition hover:text-violet-900 group"
            >
                <span class="mr-1.5 transform group-hover:-translate-x-1 transition-transform inline-block">←</span> 
                Back to Immediate Outcomes
            </a>

            <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-violet-600">
                    Effectiveness / Main Immediate Outcome
                </p>

                <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">
                    {{ $immediateOutcome?->code ?? 'Effectiveness Dashboard' }}
                </h1>

                <p class="mt-2 max-w-4xl text-sm leading-6 text-slate-600">
                    {{ $immediateOutcome?->description ?: 'Browse this Immediate Outcome through a split dashboard. Keep sibling sub-IOs visible in the left panel while reviewing documents in the main workspace.' }}
                </p>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-slate-500">Selected sub-IO</p>
                <p class="mt-1 text-sm font-semibold text-slate-900">{{ $selectedSubOutcome?->code ?? 'None' }}</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-slate-500">Sibling sub-IOs</p>
                <p class="mt-1 text-sm font-semibold text-slate-900">{{ $subOutcomes->count() }}</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-slate-500">Current documents</p>
                <p class="mt-1 text-sm font-semibold text-slate-900">{{ number_format($currentDocumentTotal) }}</p>
            </div>
        </div>
    </div>

    @if (session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm">
            {{ session('status') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        
        <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-4 py-3 sm:px-6">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">
                    {{ $immediateOutcome?->code ?? 'IO' }} Split Dashboard
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Select a sub-IO from the sidebar to update the document panel without leaving the main IO workspace.
                </p>
            </div>

            <button
                type="button"
                @click="sidebarOpen = !sidebarOpen"
                class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                :aria-expanded="sidebarOpen.toString()"
            >
                <span x-show="sidebarOpen">Collapse sidebar</span>
                <span x-show="! sidebarOpen">Expand sidebar</span>
            </button>
        </div>

        <div class="flex min-h-[640px] flex-col xl:flex-row">
            
            <aside
                x-bind:class="sidebarOpen ? 'xl:w-80 xl:min-w-80' : 'xl:w-0 xl:min-w-0 xl:border-r-0 overflow-hidden'"
                class="border-b border-r border-slate-200 bg-slate-50 transition-all duration-300 xl:border-b-0"
            >
                <div x-show="sidebarOpen" x-transition class="h-full">
                    <div class="border-b border-slate-200 px-4 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Sub-Immediate Outcomes</p>
                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            File-explorer style navigation for all sub-IOs under {{ $immediateOutcome?->code ?? 'this IO' }}.
                        </p>
                    </div>

                    <nav class="max-h-[calc(100vh-18rem)] space-y-2 overflow-y-auto p-3">
                        @forelse ($subOutcomes as $subOutcome)
                            @php $isActive = $selectedSubOutcome?->id === $subOutcome->id; @endphp

                            <a
                                href="{{ route('fiu.effectiveness.folders.show', ['code' => $immediateOutcome->code, 'sub_io' => $subOutcome->code]) }}"
                                class="block rounded-2xl border px-4 py-3 transition {{ $isActive ? 'border-violet-200 bg-white shadow-sm ring-1 ring-violet-100' : 'border-transparent bg-transparent hover:border-slate-200 hover:bg-white' }}"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold {{ $isActive ? 'text-violet-700' : 'text-slate-900' }}">
                                            {{ $subOutcome->code }}
                                        </p>
                                        <p class="mt-1 line-clamp-3 text-sm leading-5 text-slate-600">
                                            {{ $subOutcome->description ?: 'Sub-Immediate Outcome category for grouped Effectiveness documents.' }}
                                        </p>
                                    </div>
                                    <span class="inline-flex shrink-0 rounded-full {{ $isActive ? 'bg-violet-50 text-violet-700' : 'bg-slate-200 text-slate-700' }} px-2.5 py-1 text-xs font-semibold">
                                        {{ $subOutcome->documents->count() }}
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-4 py-6 text-sm text-slate-500">
                                No sub-Immediate Outcomes are configured for this main IO yet.
                            </div>
                        @endforelse
                    </nav>
                </div>
            </aside>

            <section class="min-w-0 flex-1 bg-white">
                <div class="border-b border-slate-200 px-4 py-5 sm:px-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-violet-600">Document panel</p>
                            <h3 class="mt-2 text-xl font-bold text-slate-900">
                                {{ $selectedSubOutcome?->code ? 'Documents for '.$selectedSubOutcome->code : 'Documents' }}
                            </h3>
                            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                                {{ $selectedSubOutcome?->description ?: 'Select a sub-IO to browse the grouped Effectiveness documents assigned beneath this main Immediate Outcome.' }}
                            </p>
                        </div>

                        <div class="flex flex-col items-stretch gap-3 sm:items-end">
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">
                                <p>Main IO: <span class="font-semibold text-slate-900">{{ $immediateOutcome?->code ?? '—' }}</span></p>
                                <p class="mt-1">Sub-IO: <span class="font-semibold text-slate-900">{{ $selectedSubOutcome?->code ?? 'None selected' }}</span></p>
                            </div>

                            @if ($immediateOutcome)
                                <a
                                    href="{{ route('fiu.effectiveness.folders.documents.create', ['code' => $immediateOutcome->code, 'sub_io' => $selectedSubOutcome?->code]) }}"
                                    class="inline-flex items-center justify-center rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-violet-700"
                                >
                                    Add document to this sub-IO
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-6 py-3">Document</th>
                                <th class="px-6 py-3">Sub-IO</th>
                                <th class="px-6 py-3">Institution</th>
                                <th class="px-6 py-3">Date Logged</th>
                                <th class="px-6 py-3">Status</th>
                            </tr>
                        </thead>
                      <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($documents as $document)
                                <tr id="doc-{{ $document->id }}" class="transition duration-500 hover:bg-slate-50/80 target:bg-amber-100 target:ring-2 target:ring-amber-400">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-between gap-4">
                                            
                                            <div class="flex-1">
                                                <button 
                                                    type="button"
                                                    @click.prevent="
                                                        previewUrl = '{{ route('fiu.documents.download', ['document' => $document->id, 'mode' => 'view']) }}';
                                                        previewTitle = '{{ addslashes($document->title ?? $document->name ?? 'Untitled document') }}'; 
                                                        previewOpen = true;
                                                        isMaximized = false;
                                                    "
                                                    class="font-bold text-violet-600 hover:text-violet-900 hover:underline transition-colors cursor-pointer flex items-center gap-2 text-left"
                                                    title="Quick Preview"
                                                >
                                                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    <span class="line-clamp-1">{{ $document->title ?? $document->name ?? 'Untitled document' }}</span>
                                                </button>
                                                
                                                @if (!empty($document->external_file_name))
                                                    <p class="mt-1 text-xs text-slate-500 pl-6">{{ $document->external_file_name }}</p>
                                                @endif
                                            </div>

                                            <a 
                                                href="{{ route('fiu.documents.download', ['document' => $document->id, 'mode' => 'download']) }}" 
                                                class="p-2 text-slate-400 hover:text-violet-700 hover:bg-violet-50 rounded-lg transition-colors shrink-0"
                                                title="Download to computer"
                                            >
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600"> {{ $selectedSubOutcome?->code ?? '—' }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $document->institution->name ?? $document->reporting_institution ?? '—' }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ optional($document->date_logged ?? $document->created_at)->format('d M Y') ?? '—' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex rounded-full bg-violet-50 px-3 py-1 text-xs font-semibold text-violet-700">
                                            {{ str($document->status ?? 'logged')->replace('_', ' ')->title() }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="mx-auto max-w-xl space-y-3">
                                            <p class="text-sm font-medium text-slate-900">No documents found for this sub-IO.</p>
                                            <p class="text-sm text-slate-500">
                                                Choose another sub-IO from the sidebar or add the first Effectiveness document directly to {{ $selectedSubOutcome?->code ?? $immediateOutcome?->code ?? 'this workspace' }}.
                                            </p>
                                            @if ($immediateOutcome)
                                                <div>
                                                    <a
                                                        href="{{ route('fiu.effectiveness.folders.documents.create', ['code' => $immediateOutcome->code, 'sub_io' => $selectedSubOutcome?->code]) }}"
                                                        class="inline-flex items-center rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-violet-700"
                                                    >
                                                        Create first document
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($documents instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="border-t border-slate-200 px-6 py-4">
                        {{ $documents->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div> <div 
        x-show="previewOpen" 
        style="display: none;"
        class="relative z-50" 
        aria-labelledby="modal-title" 
        role="dialog" 
        aria-modal="true"
    >
        <div 
            x-show="previewOpen"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
            @click="previewOpen = false; previewUrl = ''"
        ></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-6">
                <div 
                    x-show="previewOpen"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    :class="isMaximized ? 'w-full h-[calc(100vh-2rem)]' : 'max-w-5xl h-[85vh]'"
                    class="relative flex flex-col transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full"
                >
                    
                    <div class="flex shrink-0 items-center justify-between border-b border-slate-200 bg-slate-50 px-4 py-3 sm:px-6">
                        <h3 class="text-base font-semibold leading-6 text-slate-900" x-text="previewTitle"></h3>
                        
                        <div class="flex items-center gap-2">
                            <button @click="isMaximized = !isMaximized" type="button" class="rounded-lg p-2 text-slate-400 hover:bg-slate-200 hover:text-slate-600 transition" title="Toggle Fullscreen">
                                <svg x-show="!isMaximized" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" /></svg>
                                <svg x-show="isMaximized" style="display: none;" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 11l-5-5m0 0l5 5m-5-5v4m0-4h4m6 10l5 5m0 0l-5-5m5 5v-4m0 4h-4" /></svg>
                            </button>

                            <button @click="previewOpen = false; previewUrl = ''" type="button" class="rounded-lg p-2 text-red-400 hover:bg-red-50 hover:text-red-600 transition" title="Close Preview">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex-1 bg-slate-200 overflow-hidden relative">
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <svg class="h-8 w-8 animate-spin text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </div>
                        
                        <template x-if="previewUrl">
                            <iframe :src="previewUrl" class="relative z-10 h-full w-full border-0 bg-white" title="Secure Document Preview"></iframe>
                        </template>
                    </div>

                </div>
            </div>
        </div>
    </div> </div> </x-app-layout>