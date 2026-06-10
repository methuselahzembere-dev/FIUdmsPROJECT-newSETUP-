<x-app-layout>
    <div class="space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Effectiveness Workspace</h1>
            <p class="mt-2 max-w-4xl text-sm leading-6 text-slate-600">
                The Effectiveness workspace is organized first by Immediate Outcomes (IOs) 1 through 11. Open a main IO to drill down into its sub-IO categories, where Effectiveness documents are classified and managed.
            </p>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse($immediateOutcomes as $immediateOutcome)
                @php
                    $subDocumentTotal = $immediateOutcome->subOutcomes->sum(fn ($subOutcome) => $documentCounts[$subOutcome->id] ?? 0);
                @endphp

                <article class="flex h-full flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-violet-600">Immediate Outcome</p>
                            <h2 class="mt-2 text-xl font-bold text-slate-900">{{ $immediateOutcome->code }}</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-600">
                                {{ $immediateOutcome->description ?: 'Main Immediate Outcome category in the Effectiveness workspace.' }}
                            </p>
                        </div>
                        <span class="inline-flex rounded-full bg-violet-50 px-3 py-1 text-xs font-semibold text-violet-700">
                            {{ $immediateOutcome->sub_outcomes_count }} sub-IOs
                        </span>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-2">
                        @foreach($immediateOutcome->subOutcomes as $subOutcome)
                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                                {{ $subOutcome->code }}
                            </span>
                        @endforeach
                    </div>

                    <div class="mt-6 flex items-center justify-between border-t border-slate-100 pt-4">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Documents in sub-IOs</p>
                            <p class="mt-1 text-lg font-semibold text-slate-900">{{ number_format($subDocumentTotal) }}</p>
                        </div>

                        <a
                            href="{{ route('fiu.effectiveness.folders.show', $immediateOutcome->code) }}"
                            class="inline-flex items-center text-sm font-semibold text-violet-700 transition hover:text-violet-800"
                        >
                            Open {{ $immediateOutcome->code }} →
                        </a>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-sm text-slate-500 md:col-span-2 xl:col-span-3">
                    No Immediate Outcomes are available yet. Run the Effectiveness IO seeder, then reload this page.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>