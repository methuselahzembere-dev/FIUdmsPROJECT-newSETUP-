<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-3">
                <a
                    href="{{ route('fiu.effectiveness.folders.show', ['code' => $immediateOutcome->code, 'sub_io' => $selectedSubOutcome?->code]) }}"
                    class="inline-flex items-center text-sm font-medium text-violet-700 transition hover:text-violet-800"
                >
                    ← Back to split dashboard
                </a>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-violet-600">Effectiveness / Add document</p>
                    <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Upload or log a document</h1>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                        Add a document directly into the selected sub-IO while staying aligned with the current split-dashboard browsing flow.
                    </p>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-slate-500">Current workspace</p>
                <p class="mt-1 text-sm font-semibold text-slate-900">{{ $immediateOutcome->code }} / {{ $selectedSubOutcome?->code ?? 'Select a sub-IO' }}</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-4 text-sm text-red-700 shadow-sm">
                <p class="font-semibold">Please review the form and correct the highlighted fields.</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-[minmax(0,2fr)_minmax(320px,1fr)]">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-6 py-5">
                    <h2 class="text-lg font-semibold text-slate-900">Document details</h2>
                    <p class="mt-1 text-sm text-slate-500">Complete the record fields below, then upload a file or provide existing file details.</p>
                </div>

                <form method="POST" action="{{ route('fiu.effectiveness.folders.documents.store', ['code' => $immediateOutcome->code]) }}" enctype="multipart/form-data" class="space-y-6 px-6 py-6">
                    @csrf

                    <input type="hidden" name="immediate_outcome_id" value="{{ old('immediate_outcome_id', $immediateOutcome->id) }}">

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="immediate_outcome_display" class="block text-sm font-semibold text-slate-800">Main IO</label>
                            <input
                                id="immediate_outcome_display"
                                type="text"
                                value="{{ $immediateOutcome->code }}{{ $immediateOutcome->title ? ' - '.$immediateOutcome->title : '' }}"
                                disabled
                                class="mt-2 block w-full rounded-2xl border-slate-300 bg-slate-100 text-sm text-slate-700 shadow-sm"
                            >
                        </div>

                        <div>
                            <label for="effectiveness_sub_io_id" class="block text-sm font-semibold text-slate-800">Sub-IO</label>
                            <select
                                id="effectiveness_sub_io_id"
                                name="effectiveness_sub_io_id"
                                required
                                class="mt-2 block w-full rounded-2xl border-slate-300 text-sm shadow-sm focus:border-violet-500 focus:ring-violet-500"
                            >
                                @foreach($subOutcomes as $subOutcome)
                                    <option value="{{ $subOutcome->id }}" @selected((int) old('effectiveness_sub_io_id', $selectedSubOutcome?->id) === $subOutcome->id)>
                                        {{ $subOutcome->code }}{{ $subOutcome->title ? ' - '.$subOutcome->title : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-semibold text-slate-800">Document title</label>
                            <input
                                id="title"
                                name="title"
                                type="text"
                                value="{{ old('title') }}"
                                required
                                maxlength="255"
                                class="mt-2 block w-full rounded-2xl border-slate-300 text-sm shadow-sm focus:border-violet-500 focus:ring-violet-500"
                                placeholder="Enter the document title"
                            >
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-semibold text-slate-800">Document name (optional)</label>
                            <input
                                id="name"
                                name="name"
                                type="text"
                                value="{{ old('name') }}"
                                maxlength="255"
                                class="mt-2 block w-full rounded-2xl border-slate-300 text-sm shadow-sm focus:border-violet-500 focus:ring-violet-500"
                                placeholder="Defaults to the title if left blank"
                            >
                        </div>

                        <div>
                            <label for="reporting_institution" class="block text-sm font-semibold text-slate-800">Reporting institution</label>
                            <input
                                id="reporting_institution"
                                name="reporting_institution"
                                type="text"
                                value="{{ old('reporting_institution') }}"
                                required
                                maxlength="255"
                                class="mt-2 block w-full rounded-2xl border-slate-300 text-sm shadow-sm focus:border-violet-500 focus:ring-violet-500"
                                placeholder="e.g. College of Education"
                            >
                        </div>

                        <div>
                            <label for="date_logged" class="block text-sm font-semibold text-slate-800">Date logged</label>
                            <input
                                id="date_logged"
                                name="date_logged"
                                type="date"
                                value="{{ old('date_logged', now()->toDateString()) }}"
                                required
                                class="mt-2 block w-full rounded-2xl border-slate-300 text-sm shadow-sm focus:border-violet-500 focus:ring-violet-500"
                            >
                        </div>

                        <div>
                            <label for="document_date" class="block text-sm font-semibold text-slate-800">Document date</label>
                            <input
                                id="document_date"
                                name="document_date"
                                type="date"
                                value="{{ old('document_date') }}"
                                class="mt-2 block w-full rounded-2xl border-slate-300 text-sm shadow-sm focus:border-violet-500 focus:ring-violet-500"
                            >
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-semibold text-slate-800">Status</label>
                            <select
                                id="status"
                                name="status"
                                required
                                class="mt-2 block w-full rounded-2xl border-slate-300 text-sm shadow-sm focus:border-violet-500 focus:ring-violet-500"
                            >
                                @foreach($documentStatuses as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', 'logged') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label for="remarks" class="block text-sm font-semibold text-slate-800">Remarks</label>
                            <textarea
                                id="remarks"
                                name="remarks"
                                rows="4"
                                class="mt-2 block w-full rounded-2xl border-slate-300 text-sm shadow-sm focus:border-violet-500 focus:ring-violet-500"
                                placeholder="Optional implementation notes, evidence summary, or review context"
                            >{{ old('remarks') }}</textarea>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-5 py-5">
                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label for="document_file" class="block text-sm font-semibold text-slate-800">Upload file</label>
                                <input
                                    id="document_file"
                                    name="document_file"
                                    type="file"
                                    class="mt-2 block w-full rounded-2xl border border-slate-300 bg-white text-sm text-slate-700 file:mr-4 file:rounded-xl file:border-0 file:bg-violet-600 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-white hover:file:bg-violet-700"
                                >
                                <p class="mt-2 text-xs leading-5 text-slate-500">Accepted: PDF, Word, Excel, PowerPoint, JPG, PNG. Max 10 MB.</p>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label for="external_file_name" class="block text-sm font-semibold text-slate-800">Existing file name</label>
                                    <input
                                        id="external_file_name"
                                        name="external_file_name"
                                        type="text"
                                        value="{{ old('external_file_name') }}"
                                        maxlength="255"
                                        class="mt-2 block w-full rounded-2xl border-slate-300 text-sm shadow-sm focus:border-violet-500 focus:ring-violet-500"
                                        placeholder="Use if the file already exists in storage"
                                    >
                                </div>

                                <div>
                                    <label for="external_file_path" class="block text-sm font-semibold text-slate-800">Existing file path / URL</label>
                                    <input
                                        id="external_file_path"
                                        name="external_file_path"
                                        type="text"
                                        value="{{ old('external_file_path') }}"
                                        maxlength="2048"
                                        class="mt-2 block w-full rounded-2xl border-slate-300 text-sm shadow-sm focus:border-violet-500 focus:ring-violet-500"
                                        placeholder="storage/effectiveness-documents/... or external link"
                                    >
                                </div>

                                <div>
                                    <label for="disk" class="block text-sm font-semibold text-slate-800">Storage disk</label>
                                    <input
                                        id="disk"
                                        name="disk"
                                        type="text"
                                        value="{{ old('disk', 'public') }}"
                                        maxlength="50"
                                        class="mt-2 block w-full rounded-2xl border-slate-300 text-sm shadow-sm focus:border-violet-500 focus:ring-violet-500"
                                        placeholder="public"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-slate-500">The document will be saved directly under the selected sub-IO and then shown in the split dashboard list.</p>
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <a
                                href="{{ route('fiu.effectiveness.folders.show', ['code' => $immediateOutcome->code, 'sub_io' => $selectedSubOutcome?->code]) }}"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                            >
                                Cancel
                            </a>
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-violet-700"
                            >
                                Save document
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">Implementation notes</h2>
                    <ul class="mt-4 space-y-3 text-sm leading-6 text-slate-600">
                        <li>• The selected main IO is locked to the current workspace so users stay inside the split-dashboard context.</li>
                        <li>• Users can either upload a new file or register an already-stored file path.</li>
                        <li>• Validation ensures that the chosen sub-IO belongs to the current main IO.</li>
                        <li>• Stored metadata supports later file preview, download, or auditing enhancements.</li>
                    </ul>
                </div>

                <div class="rounded-3xl border border-violet-200 bg-violet-50 p-6 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-violet-700">Quick guidance</p>
                    <h3 class="mt-2 text-base font-semibold text-slate-900">Recommended first test</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        Seed sample documents first, open a main IO in the split dashboard, select a sub-IO, then use this form to add one uploaded file and verify the new record appears immediately in the document table.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>