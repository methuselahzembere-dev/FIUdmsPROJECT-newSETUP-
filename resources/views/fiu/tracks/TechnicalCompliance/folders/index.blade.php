<x-app-layout>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 h-\[calc(100vh-7rem)] flex flex-col space-y-4">

    <div class="flex items-center justify-between gap-4 border-b border-slate-100 pb-4 shrink-0">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">Technical Compliance Folders</h1>
            <p class="mt-1 text-sm text-slate-600">FIU manages the default folder structure and can create new areas without mixing documents across folders.</p>
        </div>
        <a href="{{ route('fiu.technical-compliance.folders.create') }}" class="rounded-2xl bg-blue-700 px-5 py-2.5 text-sm font-black text-white shadow-sm hover:bg-blue-800 transition cursor-pointer whitespace-nowrap">
            New folder
        </a>
    </div>

    <div class="flex flex-1 gap-4 min-h-0 relative overflow-hidden items-stretch">
        
        <aside id="folderSidebar" class="w-80 bg-white border border-slate-200 rounded-3xl flex flex-col transition-all duration-300 shrink-0 shadow-sm overflow-hidden z-10">
            <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between shrink-0">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Navigation Directories</span>
                <button onclick="toggleSidebar()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200/50 transition cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m18.75 4.5-7.5 7.5 7.5 7.5m-6-15L5.25 12l7.5 7.5" />
                    </svg>
                </button>
            </div>

            <div class="p-2 border-b border-slate-100 bg-slate-50/50 shrink-0 grid grid-cols-2 gap-1.5">
    <button type="button" 
            onclick="switchSidebarScopeTab('shared')" 
            id="scope-tab-shared"
            class="flex items-center justify-center gap-1.5 py-2 px-2.5 text-[10px] font-black uppercase tracking-wider rounded-xl transition-all border cursor-pointer select-none border-blue-600 bg-blue-600 text-white shadow-xs">
        👥 Shared
    </button>
    
    <button type="button" 
            onclick="switchSidebarScopeTab('fiu-private')" 
            id="scope-tab-private"
            class="flex items-center justify-center gap-1.5 py-2 px-2.5 text-[10px] font-black uppercase tracking-wider rounded-xl transition-all border cursor-pointer select-none border-slate-200 bg-white text-slate-500 hover:text-slate-700">
        🔒 Private
    </button>
</div>

            <div class="p-3 border-b border-slate-100 bg-white shrink-0">
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.604 10.604Z" />
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        id="folderSearchInput" 
                        onkeyup="filterFolders()" 
                        placeholder="Search active scope folders..." 
                        class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 py-1.5 pl-9 pr-3 text-xs text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 transition-all outline-none"
                    />
                </div>
            </div>

            <div id="sidebarFolderList" class="flex-1 overflow-y-auto max-h-[calc(100vh-18rem)] p-2 space-y-1 bg-slate-50/10">
                @forelse($folders as $folder)
                    @php
                        $isPrivateFolder = ($folder->visibility_scope === 'fiu-private');
                    @endphp
                    <button 
                        id="btn-folder-{{ $folder->id }}"
                        data-folder-name="{{ strtolower($folder->name) }}"
                        data-folder-scope="{{ $folder->visibility_scope ?? 'shared' }}"
                        onclick="selectFolder('{{ $folder->id }}', '{{ addslashes($folder->name) }}', '{{ addslashes($folder->description ?: 'No description provided.') }}', '{{ $folder->is_active ? 'Active' : 'Archived' }}', '{{ $folder->creator?->name ?: 'System' }}', '{{ $folder->updater?->name ?: ($folder->creator?->name ?: 'System') }}', '{{ $folder->updated_at->format('M d, Y H:i') }}', '{{ $folder->visibility_scope }}')"
                        class="folder-nav-item w-full flex items-center justify-between p-3 rounded-2xl text-left hover:bg-slate-50 group transition-all duration-150 cursor-pointer border border-transparent"
                    >
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="h-5 w-5 shrink-0 group-hover:scale-105 transition-transform {{ $isPrivateFolder ? 'text-rose-600' : 'text-amber-500' }}" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                @if($isPrivateFolder)
                                    <path d="M12 2a5 5 0 0 0-5 5v3H6a3 3 0 0 0-3 3v6a3 3 0 0 0 3 3h12a3 3 0 0 0 3-3v-6a3 3 0 0 0-3-3h-1V7a5 5 0 0 0-5-5Zm3 8H9V7a3 3 0 0 1 6 0v3Z"/>
                                @else
                                    <path d="M19.5 21a3 3 0 0 0 3-3v-4.5a3 3 0 0 0-3-3h-1.5V9a3 3 0 0 0-3-3h-4.672a.75.75 0 0 1-.53-.22L8.53 4.53A2.25 2.25 0 0 0 6.94 3H4.5a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3h15Z" />
                                @endif
                            </svg>
                            <div class="truncate">
                                <span class="block text-sm font-bold text-slate-800 group-hover:text-blue-700 transition-colors truncate">
                                    {{ $folder->name }}
                                </span>
                                
                                @if($isPrivateFolder)
                                    <span class="inline-block mt-0.5 text-xxs font-black px-1.5 py-0.5 rounded-md bg-rose-50 text-rose-700 uppercase border border-rose-100">
                                        Confidential
                                    </span>
                                @elseif($folder->institution)
                                    <span class="inline-block mt-0.5 text-xxs font-extrabold px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-600 uppercase border border-slate-200/50">
                                        {{ $folder->institution->code }}
                                    </span>
                                @else
                                    <span class="inline-block mt-0.5 text-xxs font-extrabold px-1.5 py-0.5 rounded-md bg-violet-50 text-violet-700 uppercase border border-violet-100">
                                        Global
                                    </span>
                                @endif 
                            </div>
                        </div>

                        <span class="text-xs font-extrabold px-2 py-1 rounded-xl bg-slate-100 text-slate-500 group-hover:bg-blue-50 group-hover:text-blue-700 transition-colors shrink-0 ml-1">
                            {{ $folder->documents_count }}
                        </span>
                    </button>
                @empty
                    <div class="text-center py-8 text-xs text-slate-400 italic">No tracking directories initialized.</div>
                @endforelse
                
                <div id="zeroSearchResults" class="hidden text-center py-8 text-xs text-slate-400 italic">
                    No folders match your search query inside this tab context.
                </div>
            </div>

            <div class="p-3 bg-slate-50 border-t border-slate-100 shrink-0 text-xs">
                {{ $folders->links() }}
            </div>
        </aside>

        <button id="sidebarExpandTrigger" onclick="toggleSidebar()" class="hidden absolute left-0 top-4 p-2 bg-white hover:bg-slate-50 border-y border-r border-slate-200 rounded-r-xl text-slate-500 hover:text-blue-700 shadow-sm cursor-pointer transition-all z-20">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m5.25 4.5 7.5 7.5-7.5 7.5m6-15 7.5 7.5-7.5 7.5" />
            </svg>
        </button>

        <main id="mainExplorerPane" class="flex-1 bg-white border border-slate-200 rounded-3xl shadow-sm flex flex-col min-w-0 transition-all duration-300">
            
            <div id="emptyViewPlaceholder" class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-slate-50/30 rounded-3xl">
                <svg class="h-16 w-16 text-slate-300 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                </svg>
                <h3 class="mt-4 text-base font-bold text-slate-800">No folder targeted</h3>
                <p class="mt-1 text-sm text-slate-500 max-w-sm">Select a compliance tracking folder from the left directory column map to inspect, isolate, and audit institutional file assets.</p>
            </div>

            <div id="explorerWorkspace" class="hidden flex-1 flex flex-col min-w-0 transition-all duration-300">
       
                <div id="activeHeaderPanel" class="p-5 border-b border-slate-100 shrink-0 bg-slate-50/60 rounded-t-3xl flex flex-col md:flex-row md:items-center justify-between gap-4 transition-colors duration-300">
                    <div class="min-w-0">
                        <h2 id="activeFolderName" class="text-lg font-black text-slate-900 tracking-tight truncate flex items-center gap-2"></h2>
                        <p id="activeFolderDesc" class="mt-0.5 text-xs text-slate-500 max-w-2xl line-clamp-1"></p>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-4 text-xxs font-semibold text-slate-500 tracking-wide border-t md:border-t-0 pt-3 md:pt-0 border-slate-200">
                        <div class="px-2.5 py-1 bg-white border border-slate-200 rounded-xl flex items-center gap-1.5 shadow-xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500" id="statusIndicatorDot"></span>
                            <span>STATUS: <strong id="metaStatus" class="text-slate-800 font-bold uppercase"></strong></span>
                        </div>
                        <div class="px-2.5 py-1 bg-white border border-slate-200 rounded-xl">
                            <span>CREATED BY: <strong id="metaCreatedBy" class="text-slate-800 font-bold"></strong></span>
                        </div>
                        <div class="px-2.5 py-1 bg-white border border-slate-200 rounded-xl flex flex-col sm:flex-row sm:gap-1.5">
                            <span>UPDATED BY: <strong id="metaUpdatedBy" class="text-slate-800 font-bold"></strong></span>
                            <span class="text-slate-300 hidden sm:inline">|</span>
                            <span id="metaUpdatedAt" class="text-slate-500 font-medium"></span>
                        </div>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto min-h-0">
                    <table class="w-full text-left border-collapse min-w-\[750px]">
                        <thead class="bg-slate-50/80 sticky top-0 border-b border-slate-200/60 z-10 text-xxs font-bold text-slate-400 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3.5">Document Details</th>
                                <th class="px-4 py-3.5 text-center w-32">Status</th>
                                <th class="px-4 py-3.5 text-center w-40">Uploaded By</th>
                                <th class="px-4 py-3.5 text-center w-40">Updated By</th>
                                <th class="px-6 py-3.5 w-28 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                            @foreach($folders as $folder)
                                @forelse($folder->documents ?? [] as $document)
                                    <tr class="document-row folder-group-{{ $folder->id }} hidden hover:bg-slate-50/60 transition-colors duration-150">
                                        <td class="px-6 py-3.5 font-medium text-slate-800 flex items-center gap-3 min-w-0">
                                            <svg class="h-5 w-5 text-rose-500 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V7.5H14.25a2.25 2.25 0 0 1-2.25-2.25V1.5H5.625z"/>
                                                <path d="M12.75 1.5v4.5a.75.75 0 0 0 .75.75h4.5l-5.25-5.25z"/>
                                            </svg>
                                            <div class="truncate flex flex-col gap-0.5">
                                                <span class="block text-sm font-semibold text-slate-800 truncate">{{ $document->title }}</span>
                                                <span class="inline-block self-start px-1.5 py-0.5 rounded bg-slate-100 border border-slate-200/60 text-slate-500 font-mono text-\[10px] tracking-wide uppercase">
                                                    {{ $document->mime_type ? last(explode('/', $document->mime_type)) : 'FILE' }}
                                                </span>
                                            </div>
                                        </td>

                                        <td class="px-4 py-3.5 text-center">
                                            @if($document->status === 'approved' || $document->status === 'verified')
                                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xxs font-bold text-emerald-700 ring-1 ring-inset ring-emerald-600/20 uppercase tracking-wide">
                                                    {{ $document->status }}
                                                </span>
                                            @elseif($document->status === 'rejected' || $document->status === 'declined')
                                                <span class="inline-flex items-center rounded-full bg-rose-50 px-2 py-0.5 text-xxs font-bold text-rose-700 ring-1 ring-inset ring-rose-600/20 uppercase tracking-wide">
                                                    {{ $document->status }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xxs font-bold text-amber-700 ring-1 ring-inset ring-amber-600/20 uppercase tracking-wide">
                                                    {{ $document->status ?: 'submitted' }}
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-3.5 text-center text-xs font-semibold text-slate-700">
                                            {{ $document->creator?->name ?: 'System' }}
                                        </td>

                                        <td class="px-4 py-3.5 text-center text-xs text-slate-600">
                                            <div>{{ $document->updater?->name ?: '—' }}</div>
                                            @if($document->updated_at)
                                                <div class="text-\[10px] text-slate-400 font-medium mt-0.5">
                                                    {{ $document->updated_at->format('M d, Y H:i') }}
                                                </div>
                                            @endif
                                        </td>

                                        <td class="px-6 py-3.5 text-right font-black">
                                            <a href="#" class="text-xs text-blue-600 hover:text-blue-800 uppercase tracking-wider transition-colors">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="document-row folder-group-{{ $folder->id }} hidden empty-row">
                                        <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic bg-slate-50/20">
                                            No compliance records or reporting data nodes loaded inside this folder node yet.
                                        </td>
                                    </tr>
                                @endforelse
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>
</div>

<script>
  let activeFolderId = null;
    // Tracking current active scope view pane (Defaulting to shared track mapping)
    let currentActiveScope = 'shared';

    /**
     * Filters folders within the active sidebar scope tab cleanly
     */
    function filterFolders() {
        // Read the text filter query input string safely
        const query = document.getElementById('folderSearchInput').value.toLowerCase().trim();
        
        // Grab all rendered folder buttons inside the navigation pane
        const items = document.querySelectorAll('.folder-nav-item');
        let totalVisible = 0;

        items.forEach(item => {
            const folderName = item.getAttribute('data-folder-name') || '';
            
            // 🌟 STEP 1: Grab the raw database scope string from the DOM attribute safely
            let rawScope = (item.getAttribute('data-folder-scope') || 'shared').toLowerCase().trim();
            
            // 🌟 STEP 2: Normalize manual variations ('private') and seeds ('fiu-private') onto a clean single key
            let normalizedFolderScope = (rawScope === 'fiu-private' || rawScope === 'private') ? 'fiu-private' : 'shared';
            
            // 🔒 STEP 3: Check if the normalized folder matches the active tab scope window ('shared' or 'fiu-private')
            if (normalizedFolderScope === currentActiveScope) {
                // If the scope matches, apply your search query text input check
                if (query === '' || folderName.includes(query)) {
                    // Force the layout display with high specificity overrides
                    item.style.setProperty('display', 'flex', 'important');
                    totalVisible++;
                } else {
                    // Hide if the search text doesn't match the current query string
                    item.style.setProperty('display', 'none', 'important');
                }
            } else {
                // 🛑 Hard lock exclusion rule: Hide completely if it belongs to the opposite tab scope context
                item.style.setProperty('display', 'none', 'important');
            }
        });

        // Toggle zero search state fallback nodes gracefully
        const fallback = document.getElementById('zeroSearchResults');
        if (fallback) {
            if (totalVisible === 0) {
                fallback.classList.remove('hidden');
            } else {
                fallback.classList.add('hidden');
            }
        }
    }

    function switchSidebarScopeTab(targetScope) {
        // Ensure the global tracker accurately saves 'shared' or 'fiu-private'
        currentActiveScope = targetScope;
        const sharedTab = document.getElementById('scope-tab-shared');
        const privateTab = document.getElementById('scope-tab-private');

        // Swap tab navigation active highlight state classes cleanly
        if (targetScope === 'shared') {
            sharedTab.className = "flex items-center justify-center gap-1.5 py-2 px-2.5 text-[10px] font-black uppercase tracking-wider rounded-xl transition-all border cursor-pointer select-none border-blue-600 bg-blue-600 text-white shadow-xs";
            privateTab.className = "flex items-center justify-center gap-1.5 py-2 px-2.5 text-[10px] font-black uppercase tracking-wider rounded-xl transition-all border cursor-pointer select-none border-slate-200 bg-white text-slate-500 hover:text-slate-700";
        } else {
            // Hard lock the focus to match 'fiu-private' parameters
            sharedTab.className = "flex items-center justify-center gap-1.5 py-2 px-2.5 text-[10px] font-black uppercase tracking-wider rounded-xl transition-all border cursor-pointer select-none border-slate-200 bg-white text-slate-500 hover:text-slate-700";
            privateTab.className = "flex items-center justify-center gap-1.5 py-2 px-2.5 text-[10px] font-black uppercase tracking-wider rounded-xl transition-all border cursor-pointer select-none border-red-600 bg-red-600 text-white shadow-xs";
        }

        // Wipe old lookup search input texts clean on scope tab changes
        const searchInput = document.getElementById('folderSearchInput');
        if (searchInput) {
            searchInput.value = "";
        }
        
        // Instantly re-run your filter matching checks!
        filterFolders();
    }

    function toggleSidebar() {
        const sidebar = document.getElementById('folderSidebar');
        const trigger = document.getElementById('sidebarExpandTrigger');

        if (sidebar && trigger) {
            if (sidebar.classList.contains('w-80')) {
                sidebar.classList.remove('w-80', 'border');
                sidebar.classList.add('w-0');
                trigger.classList.remove('hidden');
            } else {
                sidebar.classList.remove('w-0');
                sidebar.classList.add('w-80', 'border');
                trigger.classList.add('hidden');
            }
        }
    }

    // 🌟 UPDATED WITH PARAMETER: Accepts scope value to apply red or blue formatting to active elements
    function selectFolder(id, name, description, status, createdBy, updatedBy, updatedAt, scope) {
        document.querySelectorAll('.folder-nav-item').forEach(item => {
            item.classList.remove('bg-blue-50/80', 'border-blue-200/80', 'bg-rose-50/80', 'border-rose-200/80', 'shadow-sm');
        });
        const currentBtn = document.getElementById('btn-folder-' + id);
        if (currentBtn) {
            if (scope === 'fiu-private') {
                currentBtn.classList.add('bg-rose-50/80', 'border-rose-200/80', 'shadow-sm');
            } else {
                currentBtn.classList.add('bg-blue-50/80', 'border-blue-200/80', 'shadow-sm');
            }
        }

        document.getElementById('emptyViewPlaceholder').classList.add('hidden');
        document.getElementById('explorerWorkspace').classList.remove('hidden');
        document.getElementById('activeFolderName').innerText = name;
        document.getElementById('activeFolderDesc').innerText = description;

        document.getElementById('metaStatus').innerText = status;
        document.getElementById('metaCreatedBy').innerText = createdBy;
        document.getElementById('metaUpdatedBy').innerText = updatedBy;
        document.getElementById('metaUpdatedAt').innerText = 'at ' + updatedAt;

        // Toggle active explorer workspace panel header styling dynamically 
        const headerPanel = document.getElementById('activeHeaderPanel');
        if (scope === 'fiu-private') {
            headerPanel.className = "p-5 border-b border-rose-100 shrink-0 bg-rose-50/60 rounded-t-3xl flex flex-col md:flex-row md:items-center justify-between gap-4 transition-colors duration-300";
        } else {
            headerPanel.className = "p-5 border-b border-slate-100 shrink-0 bg-slate-50/60 rounded-t-3xl flex flex-col md:flex-row md:items-center justify-between gap-4 transition-colors duration-300";
        }

        const dot = document.getElementById('statusIndicatorDot');
        if (status === 'Active') {
            dot.className = "w-1.5 h-1.5 rounded-full bg-emerald-500";
        } else {
            dot.className = "w-1.5 h-1.5 rounded-full bg-amber-500";
        }

        document.querySelectorAll('.document-row').forEach(row => {
            row.classList.add('hidden');
        });
        document.querySelectorAll('.folder-group-' + id).forEach(row => {
            row.classList.remove('hidden');
        });
        activeFolderId = id;
    }

    // INITIALIZATION NODE PASS: Filter items safely on load
    document.addEventListener('DOMContentLoaded', function() {
        // Force the layout state manager to mount the 'shared' scope by default on load
        switchSidebarScopeTab('shared');
    });
</script>

</x-app-layout>