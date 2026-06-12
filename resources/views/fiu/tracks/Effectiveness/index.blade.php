<x-app-layout>
    <div class="space-y-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-violet-600">Effectiveness Workspace</p>
                    <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Immediate Outcomes Dashboard</h1>
                    <p class="mt-2 max-w-4xl text-sm leading-6 text-slate-600">
                        Browse the Effectiveness workspace by main Immediate Outcome. Select any IO to open its dedicated dashboard, then move between sub-IOs from a persistent left-hand navigation panel.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Main IOs</p>
                        <p class="mt-1 text-xl font-semibold text-slate-900">{{ $immediateOutcomes->count() }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Sub-IOs</p>
                        <p class="mt-1 text-xl font-semibold text-slate-900">{{ number_format($immediateOutcomes->sum('sub_outcomes_count')) }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 col-span-2 sm:col-span-1">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Navigation</p>
                        <p class="mt-1 text-sm font-medium text-slate-700">Grid → IO dashboard → sub-IO documents</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse($immediateOutcomes as $immediateOutcome)
                @php
                    $subDocumentTotal = $immediateOutcome->subOutcomes->sum(fn ($subOutcome) => $documentCounts[$subOutcome->id] ?? 0);
                @endphp

                <article class="group flex h-full flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-violet-200 hover:shadow-md">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-violet-600">Main Immediate Outcome</p>
                            <h2 class="mt-2 text-xl font-bold text-slate-900">{{ $immediateOutcome->code }}</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-600 line-clamp-3">
                                {{ $immediateOutcome->description ?: 'Main Immediate Outcome category in the Effectiveness workspace.' }}
                            </p>
                        </div>
                        <span class="inline-flex rounded-full bg-violet-50 px-3 py-1 text-xs font-semibold text-violet-700 h-fit whitespace-nowrap">
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

                    <div class="mt-6 grid grid-cols-2 gap-3 border-t border-slate-100 pt-4 text-sm">
                        <div class="rounded-xl bg-slate-50 px-3 py-3">
                            <p class="text-xs uppercase tracking-wide text-slate-500">Documents</p>
                            <p class="mt-1 text-lg font-semibold text-slate-900">{{ number_format($subDocumentTotal) }}</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 px-3 py-3">
                            <p class="text-xs uppercase tracking-wide text-slate-500">View</p>
                            <p class="mt-1 font-medium text-slate-700">Split dashboard</p>
                        </div>
                    </div>

                    <div class="mt-6 pt-2">
                        {{-- 🌟 FIXED: Added window.location.href execution payload here --}}
                        <a
                            href="{{ route('fiu.effectiveness.folders.show', $immediateOutcome->code) }}"
                            onclick="window.location.href='{{ route('fiu.effectiveness.folders.show', $immediateOutcome->code) }}'; return false;"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-violet-700 cursor-pointer"
                        >
                            Open {{ $immediateOutcome->code }} Dashboard
                        </a>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-sm text-slate-500 md:col-span-2 xl:col-span-3 text-center">
                    No Immediate Outcomes are available yet. Run the Effectiveness IO seeder, then reload this page.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>