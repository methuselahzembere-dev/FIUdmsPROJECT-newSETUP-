@php
    $institution = $institution ?? (object) [
        'id' => 1,
        'name' => auth()->user()->institution->name ?? 'ZIMRA',
        'short_name' => auth()->user()->institution->short_name ?? 'ZIMRA',
    ];

    $stats = $stats ?? [
        'assigned_outcomes' => 5,
        'submitted_documents' => 24,
        'changes_requested' => 3,
        'approved_documents' => 17,
    ];

    $assignedOutcomes = $assignedOutcomes ?? [
        ['number' => 1, 'title' => 'Risk, Policy and Coordination', 'status' => 'submitted', 'progress' => 70],
        ['number' => 3, 'title' => 'Supervision', 'status' => 'changes-requested', 'progress' => 45],
        ['number' => 6, 'title' => 'Financial Intelligence', 'status' => 'under-review', 'progress' => 80],
        ['number' => 7, 'title' => 'ML Investigation and Prosecution', 'status' => 'draft', 'progress' => 25],
        ['number' => 11, 'title' => 'PF Financial Sanctions', 'status' => 'approved', 'progress' => 100],
    ];

    $requiredFolders = $requiredFolders ?? [
        ['name' => 'Technical Compliance', 'description' => 'Upload compliance documents requested by FIU under customized technical folders.', 'documents' => 9, 'href' => route('institution.folders.show', 'technical-compliance'), 'tone' => 'indigo'],
        ['name' => 'Effectiveness', 'description' => 'Submit evidence only for Immediate Outcomes assigned to your institution.', 'documents' => 15, 'href' => route('institution.folders.show', 'effectiveness'), 'tone' => 'cyan'],
    ];

    $submissions = $submissions ?? [
        ['document' => 'Tax Intelligence Cooperation Evidence.pdf', 'folder' => 'Effectiveness / Immediate Outcome 6', 'status' => 'under-review', 'updated_at' => 'Today, 09:20'],
        ['document' => 'Beneficial Ownership Data Sharing SOP.docx', 'folder' => 'Technical Compliance / Preventive Measures', 'status' => 'approved', 'updated_at' => 'Yesterday, 14:10'],
        ['document' => 'Risk Assessment Statistics.xlsx', 'folder' => 'Effectiveness / Immediate Outcome 1', 'status' => 'changes-requested', 'updated_at' => '31 May 2026'],
    ];

    $feedback = $feedback ?? [
        ['title' => 'Clarify methodology', 'description' => 'FIU requested additional methodology notes for IO 3 supervision evidence.', 'time' => '2 hrs', 'tone' => 'amber'],
        ['title' => 'Document approved', 'description' => 'Technical Compliance SOP accepted and moved to processed records.', 'time' => '1 day', 'tone' => 'emerald'],
        ['title' => 'New folder available', 'description' => 'FIU created a custom Technical Compliance folder for updated policy evidence.', 'time' => '2 days', 'tone' => 'indigo'],
    ];

    $iconDashboard = '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 13h7V4H4v9Zm9 7h7V4h-7v16ZM4 20h7v-5H4v5Z" /></svg>';
    $iconUpload = '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0 4 4m-4-4-4 4M4 16v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3" /></svg>';
    $iconFolder = '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z" /></svg>';
    $iconDoc = '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v13H7V3Zm7 0v5h5" /></svg>';

    $overallProgress = (int) round(collect($assignedOutcomes)->avg('progress') ?? 0);
    $approvalRate = $stats['submitted_documents'] > 0
        ? (int) round(($stats['approved_documents'] / $stats['submitted_documents']) * 100)
        : 0;
    $revisionExposure = $stats['submitted_documents'] > 0
        ? (int) round(($stats['changes_requested'] / $stats['submitted_documents']) * 100)
        : 0;
@endphp

<x-app-layout
    :page-title="$institution['name'] . ' Submission Portal'"
    page-subtitle="Reusable institutional dashboard for assigned compliance folders, document submissions, FIU feedback, revision status, and archived records."
    user-role="institution"
    :institution-name="$institution['name']"
    active-section="dashboard"
    :unread-notifications="$unreadNotifications ?? 4"
>
    <x-slot:leftAside>
        <x-dashboard.nav-link :href="route('institution.dashboard')" :active="request()->routeIs('institution.dashboard')" :icon="$iconDashboard">Dashboard</x-dashboard.nav-link>
        <x-dashboard.nav-link :href="route('institution.uploads.create')" :active="request()->routeIs('institution.uploads.*')" :icon="$iconUpload">Upload Document</x-dashboard.nav-link>
        <x-dashboard.nav-link :href="route('institution.folders.index')" :active="request()->routeIs('institution.folders.*')" :icon="$iconFolder">Assigned Folders</x-dashboard.nav-link>
        <x-dashboard.nav-link :href="route('institution.outcomes.index')" :active="request()->routeIs('institution.outcomes.*')" :icon="$iconFolder" :badge="$stats['assigned_outcomes']">Assigned IOs</x-dashboard.nav-link>
        <x-dashboard.nav-link :href="route('institution.documents.index')" :active="request()->routeIs('institution.documents.*')" :icon="$iconDoc" :badge="$stats['submitted_documents']">My Submissions</x-dashboard.nav-link>
        <x-dashboard.nav-link :href="route('institution.feedback.index')" :active="request()->routeIs('institution.feedback.*')" :icon="$iconDoc" :badge="$stats['changes_requested']">FIU Feedback</x-dashboard.nav-link>
        <x-dashboard.nav-link :href="route('institution.archive.index')" :active="request()->routeIs('institution.archive.*')" :icon="$iconDoc">Archive</x-dashboard.nav-link>
    </x-slot:leftAside>

    <x-slot:pageActions>
        <a href="{{ route('institution.documents.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-blue-200 bg-white px-4 py-2.5 text-sm font-black text-slate-700 shadow-sm transition hover:border-blue-300 hover:bg-blue-50">Track Submissions</a>
        <a href="{{ route('institution.uploads.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-blue-700 px-4 py-2.5 text-sm font-black text-white shadow-lg shadow-blue-700/20 transition hover:bg-blue-800">Upload Evidence</a>
    </x-slot:pageActions>

    <section class="space-y-6">
        <div class="overflow-hidden rounded-[2rem] border border-blue-100 bg-gradient-to-br from-blue-900 via-blue-800 to-sky-700 shadow-xl shadow-blue-900/10">
            <div class="grid gap-6 px-6 py-6 lg:grid-cols-[minmax(0,1.35fr)_minmax(18rem,0.85fr)] lg:px-8 lg:py-8">
                <div class="space-y-5 text-white">
                    <div class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-bold uppercase tracking-[0.22em] text-blue-100">
                        <span class="inline-block h-2 w-2 rounded-full bg-emerald-300"></span>
                        Institution Workspace
                    </div>

                    <div class="space-y-3">
                        <h1 class="text-2xl font-black tracking-tight sm:text-3xl">{{ $institution['name'] }} FIUdms Dashboard</h1>
                        <p class="max-w-3xl text-sm leading-7 text-blue-100 sm:text-base">
                            Manage assigned folders, submit Technical Compliance and Effectiveness evidence, respond to FIU review and revision requests, and keep institution-specific audit records visible in one clearer workspace.
                        </p>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                        <div class="rounded-[1.5rem] border border-white/10 bg-white/10 p-4 backdrop-blur-sm">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-blue-100">Assigned IOs</p>
                            <p class="mt-2 text-3xl font-black">{{ $stats['assigned_outcomes'] }}</p>
                            <p class="mt-1 text-xs text-blue-100">Visible only where FIU assigned access</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-white/10 bg-white/10 p-4 backdrop-blur-sm">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-blue-100">Submissions</p>
                            <p class="mt-2 text-3xl font-black">{{ $stats['submitted_documents'] }}</p>
                            <p class="mt-1 text-xs text-blue-100">Across compliance and effectiveness folders</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-white/10 bg-white/10 p-4 backdrop-blur-sm">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-blue-100">Revisions</p>
                            <p class="mt-2 text-3xl font-black">{{ $stats['changes_requested'] }}</p>
                            <p class="mt-1 text-xs text-blue-100">Needs institutional follow-up</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-white/10 bg-white/10 p-4 backdrop-blur-sm">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-blue-100">Approved</p>
                            <p class="mt-2 text-3xl font-black">{{ $stats['approved_documents'] }}</p>
                            <p class="mt-1 text-xs text-blue-100">Retained for archive and audit retrieval</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-[1.75rem] border border-white/15 bg-white/10 p-5 text-white backdrop-blur-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-blue-100">Workflow alignment</p>
                            <h2 class="mt-2 text-lg font-black">Review lifecycle at a glance</h2>
                        </div>
                        <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-bold text-blue-50">Responsive</span>
                    </div>

                    <div class="mt-5 space-y-3">
                        <div class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3">
                            <p class="text-sm font-black">1. Submit evidence</p>
                            <p class="mt-1 text-xs leading-6 text-blue-100">Upload to FIU folders or assigned Immediate Outcomes only.</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3">
                            <p class="text-sm font-black">2. Review and revision</p>
                            <p class="mt-1 text-xs leading-6 text-blue-100">Track under-review items and act quickly when changes are requested.</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3">
                            <p class="text-sm font-black">3. Archive and accountability</p>
                            <p class="mt-1 text-xs leading-6 text-blue-100">Approved materials remain traceable for retrieval, audit context, and institutional history.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1.15fr)_minmax(0,1.15fr)_minmax(20rem,0.9fr)]">
            <div class="space-y-6">
                <div class="rounded-[2rem] border border-blue-100 bg-white p-5 shadow-sm shadow-slate-200/50">
                    <div class="flex flex-col gap-3 border-b border-slate-100 pb-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h2 class="text-lg font-black text-slate-950">Assigned submission channels</h2>
                            <p class="mt-1 text-sm leading-6 text-slate-600">Separate Technical Compliance folders from Effectiveness evidence so teams can scan what to submit next.</p>
                        </div>
                        <a href="{{ route('institution.folders.index') }}" class="text-sm font-black text-blue-700 hover:text-blue-900">View all folders</a>
                    </div>

                    <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-1 2xl:grid-cols-2">
                        @foreach($requiredFolders as $folder)
                            <x-dashboard.track-card :title="$folder['name']" :description="$folder['description']" :count="$folder['documents']" :href="$folder['href']" :tone="$folder['tone']" />
                        @endforeach
                    </div>
                </div>

                <div x-data="{ open: true }" class="rounded-[2rem] border border-blue-100 bg-white p-5 shadow-sm shadow-slate-200/50">
                    <div class="flex flex-col gap-3 border-b border-slate-100 pb-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h2 class="text-lg font-black text-slate-950">Assigned Immediate Outcomes</h2>
                            <p class="mt-1 text-sm leading-6 text-slate-600">Only FIU-assigned Immediate Outcomes should be visible to {{ $institution['short_name'] }} within this institutional workspace.</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('institution.outcomes.index') }}" class="text-sm font-black text-blue-700 hover:text-blue-900">Open IO workspace</a>
                            <button
                                type="button"
                                @click="open = !open"
                                class="inline-flex items-center rounded-2xl border border-slate-200 px-3 py-2 text-xs font-black uppercase tracking-wide text-slate-600 transition hover:border-blue-200 hover:text-blue-700"
                                :aria-expanded="open.toString()"
                            >
                                <span x-text="open ? 'Collapse' : 'Expand'"></span>
                            </button>
                        </div>
                    </div>

                    <div x-show="open" x-collapse class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-1 2xl:grid-cols-2">
                        @foreach($assignedOutcomes as $outcome)
                            <article class="group rounded-[1.5rem] border border-slate-200 bg-gradient-to-br from-slate-50 to-blue-50/60 p-4 transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-xs font-black uppercase tracking-[0.18em] text-blue-700">Immediate Outcome {{ $outcome['number'] }}</p>
                                        <h3 class="mt-1 text-base font-black text-slate-950">{{ $outcome['title'] }}</h3>
                                    </div>
                                    <x-dashboard.status-badge :status="$outcome['status']" />
                                </div>

                                <div class="mt-4 space-y-2">
                                    <div class="flex items-center justify-between text-xs font-bold text-slate-500">
                                        <span>Submission readiness</span>
                                        <span>{{ $outcome['progress'] }}%</span>
                                    </div>
                                    <div class="h-2 rounded-full bg-white ring-1 ring-blue-100">
                                        <div class="h-2 rounded-full bg-gradient-to-r from-blue-600 to-sky-500" style="width: {{ $outcome['progress'] }}%"></div>
                                    </div>
                                </div>

                                <div class="mt-4 flex items-center justify-between gap-3 text-xs text-slate-500">
                                    <span>Effectiveness evidence visibility remains institution-scoped.</span>
                                    <a href="{{ route('institution.outcomes.show', $outcome['number']) }}" class="shrink-0 font-black text-blue-700 hover:text-blue-900">View requirements →</a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div x-data="{ showTable: true }" class="overflow-hidden rounded-[2rem] border border-blue-100 bg-white shadow-sm shadow-slate-200/50">
                    <div class="flex flex-col gap-3 border-b border-slate-100 p-5 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h2 class="text-lg font-black text-slate-950">Submission tracking</h2>
                            <p class="mt-1 text-sm leading-6 text-slate-600">Monitor uploads through review, revision, approval, and archive-aware processing.</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('institution.documents.index') }}" class="text-sm font-black text-blue-700 hover:text-blue-900">View all</a>
                            <button
                                type="button"
                                @click="showTable = !showTable"
                                class="inline-flex items-center rounded-2xl border border-slate-200 px-3 py-2 text-xs font-black uppercase tracking-wide text-slate-600 transition hover:border-blue-200 hover:text-blue-700"
                                :aria-expanded="showTable.toString()"
                            >
                                <span x-text="showTable ? 'Collapse' : 'Expand'"></span>
                            </button>
                        </div>
                    </div>

                    <div x-show="showTable" x-collapse>
                        <div class="hidden overflow-x-auto lg:block">
                            <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                                <thead class="bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                                    <tr>
                                        <th scope="col" class="px-5 py-3">Document</th>
                                        <th scope="col" class="px-5 py-3">Folder</th>
                                        <th scope="col" class="px-5 py-3">Status</th>
                                        <th scope="col" class="px-5 py-3">Updated</th>
                                        <th scope="col" class="px-5 py-3 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($submissions as $submission)
                                        <tr class="transition hover:bg-blue-50/50">
                                            <td class="min-w-64 px-5 py-4 font-bold text-slate-900">{{ $submission['document'] }}</td>
                                            <td class="min-w-64 px-5 py-4 text-slate-600">{{ $submission['folder'] }}</td>
                                            <td class="whitespace-nowrap px-5 py-4"><x-dashboard.status-badge :status="$submission['status']" /></td>
                                            <td class="whitespace-nowrap px-5 py-4 text-slate-500">{{ $submission['updated_at'] }}</td>
                                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                                <a href="{{ route('institution.documents.show', $loop->iteration) }}" class="font-black text-blue-700 hover:text-blue-900">Open</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="space-y-4 p-4 lg:hidden">
                            @foreach($submissions as $submission)
                                <article class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <h3 class="text-sm font-black text-slate-950">{{ $submission['document'] }}</h3>
                                        <x-dashboard.status-badge :status="$submission['status']" />
                                    </div>
                                    <dl class="mt-3 space-y-2 text-sm text-slate-600">
                                        <div>
                                            <dt class="font-bold text-slate-700">Folder</dt>
                                            <dd>{{ $submission['folder'] }}</dd>
                                        </div>
                                        <div>
                                            <dt class="font-bold text-slate-700">Updated</dt>
                                            <dd>{{ $submission['updated_at'] }}</dd>
                                        </div>
                                    </dl>
                                    <a href="{{ route('institution.documents.show', $loop->iteration) }}" class="mt-4 inline-flex text-sm font-black text-blue-700 hover:text-blue-900">Open submission →</a>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-blue-100 bg-white p-5 shadow-sm shadow-slate-200/50">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-black text-slate-950">FIU feedback and actions</h2>
                            <p class="mt-1 text-sm leading-6 text-slate-600">Keep review notes, approvals, and new folder instructions easy to scan.</p>
                        </div>
                        <a href="{{ route('institution.feedback.index') }}" class="text-sm font-black text-blue-700 hover:text-blue-900">Open feedback</a>
                    </div>

                    <div class="mt-4 space-y-3">
                        @foreach($feedback as $item)
                            <x-dashboard.activity-item :title="$item['title']" :description="$item['description']" :time="$item['time']" :tone="$item['tone']" />
                        @endforeach
                    </div>
                </div>
            </div>

            <aside class="space-y-6">
                <form action="{{ route('institution.uploads.store') }}" method="POST" enctype="multipart/form-data" class="rounded-[2rem] border border-blue-100 bg-white p-5 shadow-sm shadow-slate-200/50">
                    @csrf
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-black text-slate-950">Quick upload</h2>
                            <p class="mt-1 text-sm leading-6 text-slate-600">Submit evidence for FIU review while keeping institution-specific boundaries intact.</p>
                        </div>
                        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">Action</span>
                    </div>

                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="folder_id" class="text-sm font-bold text-slate-700">Destination folder</label>
                            <select id="folder_id" name="folder_id" class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100">
                                <option value="">Select folder</option>
                                <option value="technical">Technical Compliance</option>
                                @foreach($assignedOutcomes as $outcome)
                                    <option value="io-{{ $outcome['number'] }}">Immediate Outcome {{ $outcome['number'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="document" class="text-sm font-bold text-slate-700">Document file</label>
                            <input id="document" name="document" type="file" class="mt-2 block w-full rounded-2xl border border-dashed border-blue-200 bg-blue-50/40 px-4 py-6 text-sm text-slate-600 file:mr-4 file:rounded-xl file:border-0 file:bg-blue-700 file:px-4 file:py-2 file:text-sm file:font-black file:text-white hover:bg-blue-50" />
                        </div>

                        <div>
                            <label for="notes" class="text-sm font-bold text-slate-700">Submission notes</label>
                            <textarea id="notes" name="notes" rows="4" placeholder="Add context for FIU reviewers" class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100"></textarea>
                        </div>

                        <div class="rounded-[1.5rem] border border-blue-100 bg-blue-50/70 p-4 text-sm text-blue-900">
                            <p class="font-black">Submission rule</p>
                            <p class="mt-1 leading-6">Use Technical Compliance folders for compliance requirements and assigned Immediate Outcomes for Effectiveness evidence only when FIU has granted access.</p>
                        </div>

                        <button type="submit" class="w-full rounded-2xl bg-blue-700 px-4 py-3 text-sm font-black text-white shadow-lg shadow-blue-700/20 transition hover:bg-blue-800">Submit to FIU</button>
                    </div>
                </form>

                <section class="rounded-[2rem] border border-blue-100 bg-white p-5 shadow-sm shadow-slate-200/50">
                    <h2 class="text-base font-black text-slate-950">Institution context</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">This dashboard keeps reusable institutional branding while loading the title, short name, statistics, assigned folders, Immediate Outcomes, and records dynamically from the authenticated institution profile.</p>
                </section>

                <section class="rounded-[2rem] border border-blue-100 bg-white p-5 shadow-sm shadow-slate-200/50">
                    <h2 class="text-base font-black text-slate-950">Submission health</h2>
                    <div class="mt-4 space-y-4">
                        <div>
                            <div class="flex justify-between text-sm font-bold text-slate-700"><span>Overall progress</span><span>{{ $overallProgress }}%</span></div>
                            <div class="mt-2 h-2 rounded-full bg-slate-100"><div class="h-2 rounded-full bg-blue-700" style="width: {{ $overallProgress }}%"></div></div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm font-bold text-slate-700"><span>FIU approval rate</span><span>{{ $approvalRate }}%</span></div>
                            <div class="mt-2 h-2 rounded-full bg-slate-100"><div class="h-2 rounded-full bg-emerald-600" style="width: {{ $approvalRate }}%"></div></div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm font-bold text-slate-700"><span>Revision exposure</span><span>{{ $revisionExposure }}%</span></div>
                            <div class="mt-2 h-2 rounded-full bg-slate-100"><div class="h-2 rounded-full bg-amber-500" style="width: {{ $revisionExposure }}%"></div></div>
                        </div>
                    </div>
                </section>

                <section class="rounded-[2rem] border border-blue-200 bg-gradient-to-br from-blue-50 to-sky-50 p-5">
                    <h2 class="text-base font-black text-blue-950">Visibility and archive guardrails</h2>
                    <div class="mt-3 space-y-3 text-sm leading-6 text-blue-900">
                        <p><span class="font-black">Access boundaries:</span> external institution users should only query folders and Immediate Outcomes assigned to their institution.</p>
                        <p><span class="font-black">Review logic:</span> the interface should support document submission, FIU review, requested revisions, and approval without changing controller or policy behavior.</p>
                        <p><span class="font-black">Audit awareness:</span> approved and archived records should remain visible for retrieval and contextual accountability.</p>
                    </div>
                </section>
            </aside>
        </section>
    </section>
</x-app-layout>