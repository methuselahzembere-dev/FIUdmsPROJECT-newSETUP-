@php
    $stats = $stats ?? [
        'institutions' => 12,
        'pending_reviews' => 38,
        'changes_requested' => 9,
        'archived_documents' => 1240,
    ];

    $tracks = $tracks ?? [
        ['title' => 'Technical Compliance', 'description' => 'Policy, legal, supervisory, enforcement, and preventive-measure documentation.', 'count' => 18, 'href' => route('fiu.tracks.show', 'technical-compliance'), 'tone' => 'indigo'],
        ['title' => 'Effectiveness', 'description' => 'Immediate Outcome based review framework for effectiveness evidence.', 'count' => 11, 'href' => route('fiu.tracks.show', 'effectiveness'), 'tone' => 'cyan'],
    ];

    $immediateOutcomes = $immediateOutcomes ?? collect(range(1, 11))->map(fn ($number) => [
        'number' => $number,
        'title' => 'Immediate Outcome ' . $number,
        'assigned_count' => rand(2, 8),
        'pending_count' => rand(0, 5),
    ]);

    $recentSubmissions = $recentSubmissions ?? [
        ['institution' => 'ZIMRA', 'document' => 'Tax Intelligence Cooperation Evidence.pdf', 'track' => 'Effectiveness / IO 6', 'status' => 'submitted', 'submitted_at' => 'Today, 09:20'],
        ['institution' => 'ZRP', 'document' => 'Investigations Statistics Q2.xlsx', 'track' => 'Effectiveness / IO 7', 'status' => 'under-review', 'submitted_at' => 'Yesterday, 16:45'],
        ['institution' => 'Judicial Service Commission', 'document' => 'ML Case Disposal Register.docx', 'track' => 'Technical Compliance', 'status' => 'changes-requested', 'submitted_at' => '31 May 2026'],
    ];

    $activities = $activities ?? [
        ['title' => 'Revision requested from ZRP', 'description' => 'FIU reviewer requested supporting metadata for Immediate Outcome 7.', 'time' => '12 min', 'tone' => 'amber'],
        ['title' => 'Institution profile updated', 'description' => 'ZIMRA representative contact information was amended by FIU Admin.', 'time' => '1 hr', 'tone' => 'indigo'],
        ['title' => 'Document archived', 'description' => 'Approved Technical Compliance evidence moved into secure archive.', 'time' => '3 hrs', 'tone' => 'emerald'],
    ];

    $iconDashboard = '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 13h7V4H4v9Zm9 7h7V4h-7v16ZM4 20h7v-5H4v5Z" /></svg>';
    $iconUsers = '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11a4 4 0 1 0-8 0m8 0a4 4 0 1 1-8 0m8 0c2.5.5 4 2 4 4v2H4v-2c0-2 1.5-3.5 4-4" /></svg>';
    $iconFolder = '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z" /></svg>';
    $iconDoc = '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v13H7V3Zm7 0v5h5" /></svg>';
@endphp

<x-app-layout
    page-title="FIU Compliance Oversight Dashboard"
    page-subtitle="Centralized administration console for institution management, document review, Immediate Outcome assignment, revision tracking, and secure archiving."
    user-role="fiu"
    active-section="dashboard"
    :unread-notifications="$unreadNotifications ?? 6"
>
    <x-slot:leftAside>
        <div class="space-y-4">
            <div class="rounded-[2rem] border border-blue-100 bg-gradient-to-br from-blue-700 via-indigo-700 to-slate-900 p-5 text-white shadow-xl shadow-blue-900/10">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.24em] text-blue-100/80">FIUdms</p>
                        <h2 class="mt-2 text-lg font-black leading-tight">Oversight Navigation</h2>
                        <p class="mt-2 text-sm leading-6 text-blue-50/85">Manage institutions, users, tracks, outcomes, reviews, archive records, and FIU controls from one place.</p>
                    </div>
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-white/10 ring-1 ring-white/15">
                        {!! $iconDashboard !!}
                    </span>
                </div>
            </div>

            <div x-data="{ navOpen: true }" class="rounded-[2rem] border border-slate-200 bg-white p-3 shadow-sm">
                <button
                    type="button"
                    @click="navOpen = !navOpen"
                    class="flex w-full items-center justify-between rounded-2xl px-3 py-3 text-left transition hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500/30 lg:pointer-events-none lg:cursor-default lg:hover:bg-transparent"
                    :aria-expanded="navOpen.toString()"
                >
                    <div>
                        <h2 class="text-sm font-black uppercase tracking-[0.2em] text-slate-500">Navigation</h2>
                        <p class="mt-1 text-sm text-slate-600">Quick access to core FIU administration areas.</p>
                    </div>
                    <svg class="h-5 w-5 text-slate-400 transition lg:hidden" :class="navOpen ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                    </svg>
                </button>

                <div x-show="navOpen" x-collapse class="mt-2 space-y-1.5 lg:block">
                    <x-dashboard.nav-link :href="route('fiu.dashboard')" :active="request()->routeIs('fiu.dashboard')" :icon="$iconDashboard">Dashboard</x-dashboard.nav-link>
                    <x-dashboard.nav-link :href="route('fiu.institutions.index')" :active="request()->routeIs('fiu.institutions.*')" :icon="$iconUsers" :badge="$stats['institutions']">Institutions</x-dashboard.nav-link>
                    <x-dashboard.nav-link :href="route('fiu.users.index')" :active="request()->routeIs('fiu.users.*')" :icon="$iconUsers">User Accounts</x-dashboard.nav-link>
                    <x-dashboard.nav-link :href="route('fiu.tracks.index')" :active="request()->routeIs('fiu.tracks.*')" :icon="$iconFolder">Compliance Tracks</x-dashboard.nav-link>
                    <x-dashboard.nav-link :href="route('fiu.outcomes.index')" :active="request()->routeIs('fiu.outcomes.*')" :icon="$iconFolder" badge="11">Immediate Outcomes</x-dashboard.nav-link>
                    <x-dashboard.nav-link :href="route('fiu.documents.index')" :active="request()->routeIs('fiu.documents.*')" :icon="$iconDoc" :badge="$stats['pending_reviews']">Document Review</x-dashboard.nav-link>
                    <x-dashboard.nav-link :href="route('fiu.archive.index')" :active="request()->routeIs('fiu.archive.*')" :icon="$iconDoc">Archive & Audit</x-dashboard.nav-link>
                </div>
            </div>
        </div>
    </x-slot:leftAside>

    <x-slot:pageActions>
        <a href="{{ route('fiu.institutions.create') }}" class="inline-flex items-center justify-center rounded-2xl border border-blue-200 bg-white px-4 py-2.5 text-sm font-black text-slate-700 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-800">Add Institution</a>
        <a href="{{ route('fiu.folders.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-blue-700 px-4 py-2.5 text-sm font-black text-white shadow-lg shadow-blue-700/20 transition duration-200 hover:-translate-y-0.5 hover:bg-blue-800">Create Folder</a>
    </x-slot:pageActions>

    <div x-data="{ leftRailOpen: true, rightRailOpen: true }" class="space-y-6">
<section class="overflow-hidden rounded-2xl border border-blue-100 bg-gradient-to-r from-blue-700 via-indigo-700 to-slate-900 p-6 shadow-md">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <span class="inline-flex items-center rounded-full bg-white/10 px-2.5 py-0.5 text-[10px] font-black uppercase tracking-[0.2em] text-blue-50 ring-1 ring-white/15">FIU OVERSIGHT</span>
            <h1 class="mt-2 text-xl font-black tracking-tight text-white sm:text-2xl">FIU Administration</h1>
            <p class="mt-1 max-w-2xl text-xs text-blue-50/80">Monitor institutions, tracks, Immediate Outcomes, document reviews, and system actions.</p>
        </div>
        <div class="flex flex-wrap gap-2 sm:shrink-0">
    <a href="{{ route('fiu.documents.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-black text-slate-700 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-slate-50">
        Review Submissions
    </a>

<a href="{{ route('fiu.access.effectiveness.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-indigo-100 bg-indigo-50 px-3.5 py-2 text-xs font-black text-indigo-700 transition duration-200 hover:-translate-y-0.5 hover:bg-indigo-100/80">
    Manage IO Assignments
</a>

    <a href="{{ route('fiu.documents.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-emerald-100 bg-emerald-50 px-3.5 py-2 text-xs font-black text-emerald-700 transition duration-200 hover:-translate-y-0.5 hover:bg-emerald-100/80">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-3.5 w-3.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5v-15m0 0l-6.75 6.75M12 4.5l6.75 6.75" />
        </svg>
        Upload
    </a>

    <a href="{{ route('fiu.users.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-amber-100 bg-amber-50 px-3.5 py-2 text-xs font-black text-amber-700 transition duration-200 hover:-translate-y-0.5 hover:bg-amber-100/80">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="h-3.5 w-3.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
        </svg>
        Users
    </a>
</div>
    </div>
</section>

<section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="FIU dashboard metrics">
    {{-- Card 1: Reporting Institutions (Solid Crisp Slate) --}}
    <a href="{{ route('fiu.users.index') }}" class="block no-underline group">
        <x-dashboard.metric-card 
            label="Reporting Institutions" 
            :value="$stats['institutions']" 
            tone="indigo" 
            trend="Active institution profiles managed by FIU"
            bgClass="border-slate-300 bg-slate-100 text-slate-950 shadow transition duration-200 group-hover:border-slate-400 group-hover:bg-slate-200 group-hover:shadow-md"
        >
            <x-slot:icon>
                <svg class="h-6 w-6 text-slate-700 transition duration-200 group-hover:scale-105" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 21V5l8-3 8 3v16M9 21v-8h6v8" />
                </svg>
            </x-slot:icon>
        </x-dashboard.metric-card>
    </a>

    {{-- Card 2: Pending Reviews (Solid Vivid Blue) --}}
    <a href="{{ route('fiu.documents.index', ['status' => 'pending']) }}" class="block no-underline group">
        <x-dashboard.metric-card 
            label="Pending Reviews" 
            :value="$stats['pending_reviews']" 
            tone="blue" 
            trend="Submissions awaiting verification"
            bgClass="border-blue-300 bg-blue-100 text-slate-950 shadow transition duration-200 group-hover:border-blue-400 group-hover:bg-blue-200 group-hover:shadow-md"
        >
            <x-slot:icon>
                <svg class="h-6 w-6 text-blue-800 transition duration-200 group-hover:scale-105" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
            </x-slot:icon>
        </x-dashboard.metric-card>
    </a>

    {{-- Card 3: Approved Submissions (Solid Vivid Emerald) --}}
    <a href="{{ route('fiu.documents.index', ['status' => 'approved']) }}" class="block no-underline group">
        <x-dashboard.metric-card 
            label="Approved Submissions" 
            :value="$stats['approved_docs'] ?? 0" 
            tone="emerald" 
            trend="Successfully verified archives"
            bgClass="border-emerald-300 bg-emerald-100 text-slate-950 shadow transition duration-200 group-hover:border-emerald-400 group-hover:bg-emerald-200 group-hover:shadow-md"
        >
            <x-slot:icon>
                <svg class="h-6 w-6 text-emerald-800 transition duration-200 group-hover:scale-105" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </x-slot:icon>
        </x-dashboard.metric-card>
    </a>

    {{-- Card 4: Revision Requests (Solid Vivid Amber) --}}
    <a href="{{ route('fiu.documents.index', ['status' => 'revision']) }}" class="block no-underline group">
        <x-dashboard.metric-card 
            label="Revision Requests" 
            :value="$stats['changes_requested']" 
            tone="amber" 
            trend="Flagged files returned to institutions"
            bgClass="border-amber-300 bg-amber-100 text-slate-950 shadow transition duration-200 group-hover:border-amber-400 group-hover:bg-amber-200 group-hover:shadow-md"
        >
            <x-slot:icon>
                <svg class="h-6 w-6 text-amber-800 transition duration-200 group-hover:scale-105" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
            </x-slot:icon>
        </x-dashboard.metric-card>
    </a>
</section>

        <section class="space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3 rounded-[1.75rem] border border-slate-200 bg-white/80 px-4 py-3 shadow-sm backdrop-blur">
                <div>
                    <h2 class="text-sm font-black uppercase tracking-[0.18em] text-slate-500">Workspace layout</h2>
                    <p class="mt-1 text-sm text-slate-600"></p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button
                        type="button"
                        @click="leftRailOpen = !leftRailOpen"
                        class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm font-black text-slate-600 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-800"
                        :aria-expanded="leftRailOpen.toString()"
                    >
                        <span x-text="leftRailOpen ? 'Hide left aside' : 'Show left aside'"></span>
                    </button>
                    <button
                        type="button"
                        @click="rightRailOpen = !rightRailOpen"
                        class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm font-black text-slate-600 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-800"
                        :aria-expanded="rightRailOpen.toString()"
                    >
                        <span x-text="rightRailOpen ? 'Hide right aside' : 'Show right aside'"></span>
                    </button>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-[minmax(17rem,.8fr)_minmax(0,1.7fr)_minmax(18rem,.9fr)] 2xl:grid-cols-[minmax(18rem,.82fr)_minmax(0,1.85fr)_minmax(19rem,.95fr)]">
                <aside
                    x-show="leftRailOpen"
                    x-collapse.horizontal
                    class="space-y-6 xl:sticky xl:top-6 xl:self-start"
                >
                 <div class="rounded-[2rem] border border-sky-400 bg-sky-900 p-6 shadow-md">
    
    {{-- Header block --}}
    <div class="flex items-center justify-between gap-3 border-b border-sky-800 pb-4">
        <div>
            <h2 class="text-base font-black text-white tracking-tight">Quick Access</h2>
            <p class="mt-1 text-xs text-sky-200">Direct shortcuts to primary administrative nodes and operational controls.</p>
        </div>
        <span class="rounded-full bg-sky-800/60 border border-sky-600 px-3 py-1 text-[10px] font-black uppercase tracking-wider text-sky-100">
            4 Actions
        </span>
    </div>

    {{-- Shortcuts Grid List --}}
    <div class="mt-4 space-y-3">
        
        {{-- Link 1: Institutions --}}
        <a href="{{ route('fiu.users.index') }}" class="group flex items-center gap-4 rounded-2xl border border-sky-700 bg-sky-950/40 p-3 shadow-sm transition duration-200 hover:border-sky-300 hover:bg-sky-800/50">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-950 border border-sky-800 text-sky-300 transition duration-200 group-hover:border-sky-300 group-hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3" />
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <span class="block text-sm font-black text-white">Institutions</span>
                <span class="block text-[11px] text-sky-200 font-medium">Manage reporting external profiles and oversight records</span>
            </div>
        </a>

        {{-- Link 2: Users --}}
        <a href="{{ route('fiu.users.index') }}" class="group flex items-center gap-4 rounded-2xl border border-sky-700 bg-sky-950/40 p-3 shadow-sm transition duration-200 hover:border-sky-300 hover:bg-sky-800/50">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-950 border border-sky-800 text-sky-300 transition duration-200 group-hover:border-sky-300 group-hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-2.533-4.65l-1.482-.394a8.314 8.314 0 0 0-4.731 0l-1.482.394a4.125 4.125 0 0 0-2.533 4.65 9.366 9.366 0 0 0 4.121.952ZM7.5 13.625c.995 0 1.93.21 2.774.587A4.124 4.124 0 0 1 8.163 9.17l.394-1.482a8.312 8.312 0 0 0 0-4.731L8.16 1.474A4.125 4.125 0 0 0 3.51 4.007c-.551 1.486-.551 3.12 0 4.606l.395 1.481a4.124 4.124 0 0 0 3.595 3.531Z" />
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <span class="block text-sm font-black text-white">Users</span>
                <span class="block text-[11px] text-sky-200 font-medium">Configure credentials, security states, and access keys</span>
            </div>
        </a>

        {{-- Link 3: Documents --}}
        <a href="{{ route('fiu.documents.index') }}" class="group flex items-center gap-4 rounded-2xl border border-sky-700 bg-sky-950/40 p-3 shadow-sm transition duration-200 hover:border-sky-300 hover:bg-sky-800/50">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-950 border border-sky-800 text-sky-300 transition duration-200 group-hover:border-sky-300 group-hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <span class="block text-sm font-black text-white">Documents</span>
                <span class="block text-[11px] text-sky-200 font-medium">Review filing trails, track feedback loops, and audit archives</span>
            </div>
        </a>

        <a href="#" class="group flex items-center gap-4 rounded-2xl border border-sky-700 bg-sky-950/40 p-3 shadow-sm transition duration-200 hover:border-sky-300 hover:bg-sky-800/50">
    <div class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-950 border border-sky-800 text-sky-300 transition duration-200 group-hover:border-sky-300 group-hover:text-white">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="h-5 w-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
        </svg>
        
        {{-- Demo Red Count Badge --}}
        <span class="absolute -top-1.5 -right-1.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-rose-600 px-1 text-[10px] font-black text-white ring-2 ring-sky-900 shadow-sm shadow-rose-950/50">
            3
        </span>
    </div>
    <div class="min-w-0 flex-1">
        <span class="block text-sm font-black text-white">Notifications</span>
        <span class="block text-[11px] text-sky-200 font-medium">Track instant revision status alerts and system changes</span>
    </div>
</a>

        {{-- Link 4: Settings --}}
        <a href="#" class="group flex items-center gap-4 rounded-2xl border border-sky-700 bg-sky-950/40 p-3 shadow-sm transition duration-200 hover:border-sky-300 hover:bg-sky-800/50">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-950 border border-sky-800 text-sky-300 transition duration-200 group-hover:border-sky-300 group-hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.43l-1.003.767a1.123 1.123 0 0 0-.417 1.03c.004.074.006.148.006.222 0 .074-.002.148-.006.222a1.123 1.123 0 0 0 .417 1.03l1.003.767a1.125 1.125 0 0 1 .26 1.43l-1.296 2.247a1.125 1.125 0 0 1-1.37.49l-1.216-.456a1.125 1.125 0 0 0-1.07.124c-.073.044-.146.087-.22.128-.332.183-.582.495-.645.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281a1.125 1.125 0 0 0-.646-.869c-.074-.041-.147-.084-.22-.129a1.125 1.125 0 0 0-1.07-.124l-1.217.456a1.125 1.125 0 0 1-1.37-.49l-1.296-2.247a1.125 1.125 0 0 1 .26-1.43l1.003-.767a1.122 1.122 0 0 0 .417-1.03c-.004-.074-.006-.148-.006-.222 0-.074.002-.148.006-.222a1.122 1.122 0 0 0-.417-1.03l-1.003-.767a1.125 1.125 0 0 1-.26-1.43l1.296-2.247a1.125 1.125 0 0 1 1.37-.49l1.216.456c.356.133.751.072 1.076-.124.072-.041.146-.084.218-.128.333-.183.582-.495.646-.869l.214-1.28Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <span class="block text-sm font-black text-white">Settings</span>
                <span class="block text-[11px] text-sky-200 font-medium">Adjust FATF framework variables and submission thresholds</span>
            </div>
        </a>

    </div>
</div>

                    <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h2 class="text-base font-black text-slate-950">Recent Activity</h2>
                                <p class="mt-1 text-sm text-slate-600">Administrative changes and review trail highlights.</p>
                            </div>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-600">Live</span>
                        </div>
                        <div class="mt-4 space-y-1">
                            @foreach($activities as $activity)
                                <div class="rounded-2xl transition duration-200 hover:bg-slate-50">
                                    <x-dashboard.activity-item :title="$activity['title']" :description="$activity['description']" :time="$activity['time']" :tone="$activity['tone']" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                </aside>

                <div class="min-w-0 space-y-6">
                    <div class="grid gap-4 xl:grid-cols-2">
                        @foreach($tracks as $track)
                            <div class="rounded-[2rem] transition duration-200 hover:-translate-y-1 hover:shadow-lg hover:shadow-blue-100/60">
                                <x-dashboard.track-card :title="$track['title']" :description="$track['description']" :count="$track['count']" :href="$track['href']" :tone="$track['tone']" />
                            </div>
                        @endforeach
                    </div>

                    <div x-data="{ outcomesOpen: true }" class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex flex-col gap-3 border-b border-slate-200 pb-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h2 class="text-lg font-black text-slate-950">Immediate Outcome Assignment Matrix</h2>
                                <p class="mt-1 max-w-3xl text-sm text-slate-600">Assign the standard 11 Immediate Outcomes to institutions, understand workload quickly, and keep review pressure visible.</p>
                            </div>
                            <div class="flex items-center gap-2">
                            <a href="{{ route('fiu.access.effectiveness.index') }}" class="inline-flex items-center rounded-2xl px-3 py-2 text-sm font-black text-blue-700 transition hover:bg-blue-50 hover:text-blue-900">Manage assignments</a>
                                <button
                                    type="button"
                                    @click="outcomesOpen = !outcomesOpen"
                                    class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm font-black text-slate-600 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-800"
                                    :aria-expanded="outcomesOpen.toString()"
                                >
                                    <span x-text="outcomesOpen ? 'Collapse' : 'Expand'"></span>
                                </button>
                            </div>
                        </div>

                        <div x-show="outcomesOpen" x-collapse class="mt-4 grid gap-3 sm:grid-cols-2 2xl:grid-cols-3">
                            @foreach($immediateOutcomes as $outcome)
                                <a href="{{ route('fiu.outcomes.show', $outcome['number']) }}" class="group rounded-2xl border border-slate-200 bg-slate-50 p-4 transition duration-200 hover:-translate-y-0.5 hover:border-blue-200 hover:bg-blue-50 hover:shadow-sm">
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="text-sm font-black text-slate-950 transition group-hover:text-blue-900">{{ $outcome['title'] }}</p>
                                        <span class="rounded-full bg-white px-2.5 py-1 text-[11px] font-black text-slate-600 ring-1 ring-slate-200 transition group-hover:ring-blue-200">IO {{ $outcome['number'] }}</span>
                                    </div>
                                    <div class="mt-3 flex items-center gap-2 text-xs font-bold text-slate-500">
                                        <span>{{ $outcome['assigned_count'] }} assigned</span>
                                        <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                                        <span>{{ $outcome['pending_count'] }} pending</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div x-data="{ submissionsOpen: true }" class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm">
                        <div class="flex flex-col gap-3 border-b border-slate-200 p-5 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h2 class="text-lg font-black text-slate-950">Recent Institutional Submissions</h2>
                                <p class="mt-1 max-w-3xl text-sm text-slate-600">Review, request revisions, edit administratively, or archive processed evidence with less visual clutter.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('fiu.documents.index') }}" class="inline-flex items-center rounded-2xl px-3 py-2 text-sm font-black text-blue-700 transition hover:bg-blue-50 hover:text-blue-900">View all</a>
                                <button
                                    type="button"
                                    @click="submissionsOpen = !submissionsOpen"
                                    class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm font-black text-slate-600 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-800"
                                    :aria-expanded="submissionsOpen.toString()"
                                >
                                    <span x-text="submissionsOpen ? 'Hide list' : 'Show list'"></span>
                                </button>
                            </div>
                        </div>

                        <div x-show="submissionsOpen" x-collapse>
                            <div class="hidden overflow-x-auto xl:block">
                                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                                    <thead class="bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                                        <tr>
                                            <th scope="col" class="px-5 py-3">Institution</th>
                                            <th scope="col" class="px-5 py-3">Document</th>
                                            <th scope="col" class="px-5 py-3">Track / Folder</th>
                                            <th scope="col" class="px-5 py-3">Status</th>
                                            <th scope="col" class="px-5 py-3">Submitted</th>
                                            <th scope="col" class="px-5 py-3 text-right">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($recentSubmissions as $submission)
                                            <tr class="transition duration-200 hover:bg-blue-50/60">
                                                <td class="whitespace-nowrap px-5 py-4 font-bold text-slate-900">{{ $submission['institution'] }}</td>
                                                <td class="min-w-56 px-5 py-4 text-slate-700">{{ $submission['document'] }}</td>
                                                <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $submission['track'] }}</td>
                                                <td class="whitespace-nowrap px-5 py-4"><x-dashboard.status-badge :status="$submission['status']" /></td>
                                                <td class="whitespace-nowrap px-5 py-4 text-slate-500">{{ $submission['submitted_at'] }}</td>
                                                <td class="whitespace-nowrap px-5 py-4 text-right"><a href="{{ route('fiu.documents.show', $loop->iteration) }}" class="font-black text-blue-700 transition hover:text-blue-900">Review</a></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="space-y-3 p-4 xl:hidden">
                                @foreach($recentSubmissions as $submission)
                                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 transition duration-200 hover:border-blue-200 hover:bg-blue-50">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <p class="text-sm font-black text-slate-950">{{ $submission['institution'] }}</p>
                                                <p class="mt-1 text-sm text-slate-700">{{ $submission['document'] }}</p>
                                            </div>
                                            <x-dashboard.status-badge :status="$submission['status']" />
                                        </div>
                                        <div class="mt-3 space-y-1 text-sm text-slate-600">
                                            <p><span class="font-bold text-slate-700">Track:</span> {{ $submission['track'] }}</p>
                                            <p><span class="font-bold text-slate-700">Submitted:</span> {{ $submission['submitted_at'] }}</p>
                                        </div>
                                        <div class="mt-4">
                                            <a href="{{ route('fiu.documents.show', $loop->iteration) }}" class="inline-flex items-center rounded-2xl bg-white px-3 py-2 text-sm font-black text-blue-700 ring-1 ring-slate-200 transition hover:bg-blue-50 hover:text-blue-900 hover:ring-blue-200">Review submission</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <aside
                    x-show="rightRailOpen"
                    x-collapse.horizontal
                    class="space-y-6 xl:sticky xl:top-6 xl:self-start"
                >
 <div class="rounded-[2rem] border border-sky-400 bg-sky-900 p-6 shadow-md">
    
    {{-- Header block --}}
    <div class="flex items-center justify-between gap-3 border-b border-sky-800 pb-4">
        <div>
            <h2 class="text-base font-black text-white tracking-tight">FIU Control Panel</h2>
            <p class="mt-1 text-xs text-sky-200">Administrative shortcuts for system configuration and tracking setups.</p>
        </div>
        <span class="rounded-full bg-sky-800/60 border border-sky-600 px-3 py-1 text-[10px] font-black uppercase tracking-wider text-sky-100">
            Admin
        </span>
    </div>

    {{-- Actions Stack Grid --}}
    <div class="mt-4 grid gap-3">
        
        {{-- Button 1: Create User Account (Primary Action Highlight) --}}
        <a href="{{ route('fiu.users.create') }}" class="group inline-flex items-center justify-center gap-2 rounded-2xl bg-sky-400 px-4 py-3 text-center text-sm font-black text-sky-950 shadow-md transition duration-200 hover:-translate-y-0.5 hover:bg-sky-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
            </svg>
            Create User Account
        </a>

    {{-- Button 2: Manage IO Access Rights --}}
<a href="{{ route('fiu.access.effectiveness.index') }}" class="group inline-flex items-center justify-center gap-2 rounded-2xl border border-sky-700 bg-sky-950/40 px-4 py-3 text-center text-sm font-black text-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-sky-300 hover:bg-sky-800/50">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="h-4 w-4 text-sky-300 group-hover:text-white">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
    </svg>
    Manage IO Access
</a>

        {{-- Button 3: Upload Submission File --}}
        <a href="{{ route('fiu.documents.create') }}" class="group inline-flex items-center justify-center gap-2 rounded-2xl border border-sky-700 bg-sky-950/40 px-5 py-3 text-center text-sm font-black text-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-sky-300 hover:bg-sky-800/50">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="h-4 w-4 text-sky-300 group-hover:text-white">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
            </svg>
            Upload Submission File
        </a>

    </div>
</div>

     <div class="rounded-[2rem] border border-sky-400 bg-sky-900 p-6 shadow-md">
    
    {{-- Header block --}}
    <div class="flex items-center justify-between gap-3 border-b border-sky-800 pb-4">
        <div>
            <h2 class="text-base font-black text-white tracking-tight">Compliance Summary</h2>
            <p class="mt-1 text-xs text-sky-200">High-level progress across oversight mandates and auditing metrics.</p>
        </div>
        <span class="rounded-full bg-sky-800/60 border border-sky-600 px-3 py-1 text-[10px] font-black uppercase tracking-wider text-sky-100">
            Snapshot
        </span>
    </div>

    {{-- Interactive Progress Tracks Stack --}}
    <div class="mt-4 space-y-4">
        
        {{-- Track 1: Technical Compliance --}}
        <div class="rounded-2xl border border-sky-800/80 bg-sky-950/40 p-4 transition duration-200 hover:border-sky-600 hover:bg-sky-950/60">
            <div class="flex justify-between text-xs font-black uppercase tracking-wider text-sky-100">
                <span>Technical Compliance</span>
                <span class="text-sky-300 font-black">72%</span>
            </div>
            <div class="mt-2.5 h-2 rounded-full bg-sky-950 border border-sky-800/60 overflow-hidden">
                <div class="h-full rounded-full bg-sky-400 shadow-[0_0_10px_rgba(56,189,248,0.3)] transition-all duration-500" style="width: 72%"></div>
            </div>
        </div>

        {{-- Track 2: Effectiveness --}}
        <div class="rounded-2xl border border-sky-800/80 bg-sky-950/40 p-4 transition duration-200 hover:border-sky-600 hover:bg-sky-950/60">
            <div class="flex justify-between text-xs font-black uppercase tracking-wider text-sky-100">
                <span>Effectiveness</span>
                <span class="text-cyan-300 font-black">58%</span>
            </div>
            <div class="mt-2.5 h-2 rounded-full bg-sky-950 border border-sky-800/60 overflow-hidden">
                <div class="h-full rounded-full bg-cyan-400 shadow-[0_0_10px_rgba(34,211,238,0.3)] transition-all duration-500" style="width: 58%"></div>
            </div>
        </div>

        {{-- Track 3: Archive Coverage --}}
        <div class="rounded-2xl border border-sky-800/80 bg-sky-950/40 p-4 transition duration-200 hover:border-sky-600 hover:bg-sky-950/60">
            <div class="flex justify-between text-xs font-black uppercase tracking-wider text-sky-100">
                <span>Archive Coverage</span>
                <span class="text-emerald-400 font-black">91%</span>
            </div>
            <div class="mt-2.5 h-2 rounded-full bg-sky-950 border border-sky-800/60 overflow-hidden">
                <div class="h-full rounded-full bg-emerald-400 shadow-[0_0_10px_rgba(52,211,153,0.3)] transition-all duration-500" style="width: 91%"></div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>