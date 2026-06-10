<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-3">
                <a
                    href="{{ route('fiu.effectiveness.folders.index') }}"
                    class="inline-flex items-center text-sm font-medium text-violet-700 transition hover:text-violet-800"
                >
                    ← Back to Immediate Outcomes
                </a>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-violet-600">Effectiveness / Immediate Outcome</p>
                    <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{{ $immediateOutcome->code }}</h1>
                    <p class="mt-2 max-w-4xl text-sm leading-6 text-slate-600">
                        {{ $immediateOutcome->description ?: 'Browse the sub-IO categories under this Immediate Outcome. Documents in the Effectiveness workspace should be filed under one of the sub-IOs below.' }}
                    </p>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm font-medium text-slate-700">Structure</p>
                <div class="mt-3 space-y-1 text-sm text-slate-600">
                    <p>Main IO: <span class="font-semibold text-slate-900">{{ $immediateOutcome->code }}</span></p>
                    <p>Available sub-IOs: <span class="font-semibold text-slate-900">{{ $immediateOutcome->subOutcomes->count() }}</span></p>
                    <p>Documents must be grouped under sub-IO categories beneath this main IO.</p>
                </div>
            </div>
        </div>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Sub-Immediate Outcomes</h2>
                    <p class="mt-1 text-sm text-slate-500">Select the appropriate sub-IO when classifying Effectiveness documents.</p>
                </div>
            </div>

            <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @forelse($immediateOutcome->subOutcomes as $subOutcome)
                    <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">IO {{ $subOutcome->code }}</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">
                                    {{ $subOutcome->description ?: 'Sub-Immediate Outcome category for Effectiveness documents.' }}
                                </p>
                            </div>
                            <span class="inline-flex rounded-full bg-violet-50 px-3 py-1 text-xs font-semibold text-violet-700">
                                {{ $documentsBySubIo[$subOutcome->id] ?? 0 }} docs
                            </span>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-sm text-slate-500 md:col-span-2 xl:col-span-3">
                        No sub-Immediate Outcomes are configured for this main IO yet.
                    </div>
                @endforelse
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Documents under {{ $immediateOutcome->code }}</h2>
                <p class="mt-1 text-sm text-slate-500">This list shows documents assigned to the sub-IO categories under this main Immediate Outcome.</p>
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
                        @forelse($documents as $document)
                            <tr>
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $document->title ?? $document->name ?? 'Untitled document' }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $document->subImmediateOutcome->code ?? $document->io_sub_code ?? '—' }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $document->institution->name ?? $document->reporting_institution ?? '—' }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ optional($document->created_at ?? $document->date_logged)->format('d M Y') ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full bg-violet-50 px-3 py-1 text-xs font-semibold text-violet-700">
                                        {{ str($document->status ?? 'logged')->replace('_', ' ')->title() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-500">No documents have been assigned to the sub-IOs under this Immediate Outcome yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($documents instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="border-t border-slate-200 px-6 py-4">
                    {{ $documents->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app-layout>