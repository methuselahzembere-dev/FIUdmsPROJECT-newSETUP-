@props([
    'pageTitle' => 'FIUdmsPROJECT Dashboard',
    'pageSubtitle' => null,
    'userRole' => 'institution',
    'institutionName' => null,
    'activeSection' => 'dashboard',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $pageTitle }} | {{ config('app.name', 'FIUdmsPROJECT') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-900">
    <div
        x-data="{
            sidebarOpen: false,
            rightPanelOpen: false,
            searchOpen: false,
            activeTab: '{{ $activeSection }}',
        }"
        class="min-h-screen bg-slate-50"
    >
        <div
            x-cloak
            x-show="sidebarOpen || rightPanelOpen"
            x-transition.opacity
            class="fixed inset-0 z-30 bg-slate-950/50 lg:hidden"
            @click="sidebarOpen = false; rightPanelOpen = false"
        ></div>

        <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/90 backdrop-blur supports-[backdrop-filter]:bg-white/75">
            <div class="mx-auto flex h-16 max-w-[100rem] items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 p-2 text-slate-600 shadow-sm transition hover:bg-slate-100 hover:text-slate-900 lg:hidden"
                        @click="sidebarOpen = true"
                        aria-label="Open navigation"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
                        </svg>
                    </button>

                    <a href="{{ route($userRole === 'fiu' ? 'fiu.dashboard' : 'institution.dashboard') }}" class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-700 text-sm font-black tracking-tight text-white shadow-lg shadow-indigo-700/25">FIU</span>
                        <span class="hidden sm:block">
                            <span class="block text-sm font-black uppercase tracking-[0.18em] text-slate-900">FIUdmsPROJECT</span>
                            <span class="block text-xs font-medium text-slate-500">Institutional Document Management System</span>
                        </span>
                    </a>
                </div>

                <div class="hidden flex-1 justify-center px-8 md:flex">
                    <form action="{{ route($userRole === 'fiu' ? 'fiu.documents.index' : 'institution.documents.index') }}" method="GET" class="w-full max-w-xl">
                        <label for="global-search" class="sr-only">Search documents, institutions, outcomes, or folders</label>
                        <div class="relative">
                            <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                            </svg>
                            <input
                                id="global-search"
                                name="q"
                                type="search"
                                placeholder="Search submissions, folders, Immediate Outcomes..."
                                class="block w-full rounded-2xl border border-slate-200 bg-slate-50 py-2.5 pl-11 pr-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100"
                            >
                        </div>
                    </form>
                </div>

                <div class="flex items-center gap-2 sm:gap-3">
                    <button
                        type="button"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 p-2 text-slate-600 shadow-sm transition hover:bg-slate-100 hover:text-slate-900 md:hidden"
                        @click="searchOpen = ! searchOpen"
                        aria-label="Toggle search"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                        </svg>
                    </button>

                    <a href="{{ route($userRole === 'fiu' ? 'fiu.notifications.index' : 'institution.notifications.index') }}" class="relative inline-flex items-center justify-center rounded-xl border border-slate-200 p-2 text-slate-600 shadow-sm transition hover:bg-slate-100 hover:text-slate-900" aria-label="Notifications">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 0 1-6 0" />
                        </svg>
                        @if(($unreadNotifications ?? 0) > 0)
                            <span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-rose-600 px-1 text-[10px] font-bold text-white">{{ $unreadNotifications }}</span>
                        @endif
                    </a>

                    <button
                        type="button"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 p-2 text-slate-600 shadow-sm transition hover:bg-slate-100 hover:text-slate-900 xl:hidden"
                        @click="rightPanelOpen = true"
                        aria-label="Open context panel"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 5h16M4 12h16M4 19h10" />
                        </svg>
                    </button>

                    <div class="hidden items-center gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm sm:flex">
                        <div class="h-8 w-8 rounded-full bg-gradient-to-br from-indigo-600 to-cyan-500"></div>
                        <div class="leading-tight">
                            <p class="text-xs font-bold text-slate-900">{{ auth()->user()->name ?? 'Authenticated User' }}</p>
                            <p class="text-[11px] font-medium text-slate-500">{{ $userRole === 'fiu' ? 'FIU Governing Body' : ($institutionName ?? 'Reporting Institution') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div x-cloak x-show="searchOpen" x-transition class="border-t border-slate-200 bg-white px-4 py-3 md:hidden">
                <form action="{{ route($userRole === 'fiu' ? 'fiu.documents.index' : 'institution.documents.index') }}" method="GET">
                    <input
                        name="q"
                        type="search"
                        placeholder="Search submissions, folders, Immediate Outcomes..."
                        class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100"
                    >
                </form>
            </div>
        </header>

        <div class="mx-auto grid max-w-[100rem] grid-cols-1 lg:grid-cols-[18rem_minmax(0,1fr)] xl:grid-cols-[18rem_minmax(0,1fr)_22rem]">
            <aside
                class="fixed inset-y-0 left-0 z-40 w-72 -translate-x-full border-r border-slate-200 bg-white pt-16 shadow-xl transition-transform duration-300 lg:sticky lg:top-16 lg:z-20 lg:h-[calc(100vh-4rem)] lg:translate-x-0 lg:overflow-y-auto lg:pt-0 lg:shadow-none"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            >
                <div class="flex h-full flex-col p-4">
                    <div class="mb-4 rounded-3xl border border-indigo-100 bg-indigo-50 p-4">
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-indigo-700">{{ $userRole === 'fiu' ? 'Administrator Console' : 'Institution Portal' }}</p>
                        <h2 class="mt-2 text-lg font-black text-slate-950">{{ $userRole === 'fiu' ? 'FIU OVERSIGHT' : ($institutionName ? $institutionName . ' Submission Portal' : 'Submission Portal') }}</h2>
                        <p class="mt-1 text-sm text-slate-600">{{ $userRole === 'fiu' ? 'Manage compliance tracks, institutions, submissions, and evaluations.' : 'Submit, revise, and track assigned compliance documentation.' }}</p>
                    </div>

                    <nav class="space-y-1" aria-label="Primary navigation">
                        {{ $leftAside ?? '' }}
                    </nav>

                    <div class="mt-auto rounded-3xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-sm font-bold text-slate-900">Need audit evidence?</p>
                        <p class="mt-1 text-xs leading-5 text-slate-600">Use archived records and activity logs to retrieve historical submission decisions.</p>
                        <a href="{{ route($userRole === 'fiu' ? 'fiu.archive.index' : 'institution.archive.index') }}" class="mt-3 inline-flex w-full items-center justify-center rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-indigo-700">Open Archive</a>
                    </div>
                </div>
            </aside>

            <main class="min-w-0 px-4 py-6 sm:px-6 lg:px-8">
                <div class="mb-6 flex flex-col gap-4 rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm sm:p-6 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.22em] text-indigo-700">{{ $userRole === 'fiu' ? 'Financial Intelligence Unit' : ($institutionName ?? 'Reporting Institution') }}</p>
                        <h1 class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">{{ $pageTitle }}</h1>
                        @if($pageSubtitle)
                            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">{{ $pageSubtitle }}</p>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        {{ $pageActions ?? '' }}
                    </div>
                </div>

                {{ $slot }}
            </main>

            <aside
                class="fixed inset-y-0 right-0 z-40 w-80 translate-x-full border-l border-slate-200 bg-white pt-16 shadow-xl transition-transform duration-300 xl:sticky xl:top-16 xl:z-20 xl:h-[calc(100vh-4rem)] xl:translate-x-0 xl:overflow-y-auto xl:pt-0 xl:shadow-none"
                :class="rightPanelOpen ? 'translate-x-0' : 'translate-x-full xl:translate-x-0'"
            >
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 xl:hidden">
                    <p class="text-sm font-black text-slate-950">Context Panel</p>
                    <button type="button" class="rounded-xl p-2 text-slate-500 hover:bg-slate-100" @click="rightPanelOpen = false" aria-label="Close context panel">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" d="M6 6l12 12M18 6 6 18" />
                        </svg>
                    </button>
                </div>
                <div class="space-y-5 p-5">
                    {{ $rightAside ?? '' }}
                </div>
            </aside>
        </div>
    </div>
</body>
</html>
