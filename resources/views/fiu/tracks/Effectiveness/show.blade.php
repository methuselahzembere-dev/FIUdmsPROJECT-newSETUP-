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

<div x-data="{ sidebarOpen: true }" class="space-y-6">
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
                                    <tr class="transition hover:bg-slate-50/80">

                                    <td class="px-6 py-4">
                                           <div>
                                   <p class="font-medium text-slate-900">{{ $document->title ?? $document->name ?? 'Untitled document' }}</p>
        
                                   @if (!empty($document->external_file_name))
                                  <p class="mt-1 text-xs text-slate-500">{{ $document->external_file_name }}</p>
                                     @endif
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
        </div>
    </div>
</x-app-layout>