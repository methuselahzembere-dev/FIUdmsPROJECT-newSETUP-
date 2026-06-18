<x-app-layout>
    <style>
        @keyframes subtle-breath {
            0%, 100% { transform: scale(1); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
            50% { transform: scale(1.012); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08); }
        }
        .animate-attention-breath {
            animation: subtle-breath 2.5s infinite ease-in-out;
        }
    </style>

    <div class="space-y-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between border-b border-slate-100 pb-5">
            <div class="space-y-2">
                <a href="javascript:history.back()" class="inline-flex items-center text-sm font-semibold text-slate-600 transition hover:text-slate-900 gap-1">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    Back to dashboard workspace
                </a>
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">FIU Centralized Document Repository</p>
                    <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-900">Upload or Log a Track Document</h1>
                    <p class="mt-1 max-w-3xl text-sm leading-6 text-slate-600">
                        Dispatch records into compliance paths, target structural folders, and map user access directories.
                    </p>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-700 shadow-sm">
                <p class="font-bold flex items-center gap-2">
                    <svg class="h-4 w-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    Please review the form and correct the highlighted fields.
                </p>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-xs font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('fiu.documents.store') }}" enctype="multipart/form-data" class="space-y-6 w-full">
            @csrf

            <div id="trackSelectorCard" class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xs transition-all duration-300">
                <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                    <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">1. Select Target Compliance Workspace</h2>
                    <p class="text-xs text-slate-500">Route this document entry into its respective regulatory reporting track archetype.</p>
                </div>
                <div class="p-6 grid gap-4 sm:grid-cols-2">
                    
                    <label class="relative flex flex-col p-5 rounded-2xl border-2 bg-white cursor-pointer hover:bg-slate-50/50 transition-all select-none group border-slate-200" id="label-track-technical">
                        <input type="radio" name="workspace_track" value="technical" class="sr-only" @checked(old('workspace_track', 'technical') === 'technical') onchange="switchWorkspaceContext('technical')">
                        <div class="flex items-center gap-4">
                            <div class="p-3 rounded-xl bg-blue-50 text-blue-700 group-hover:scale-105 transition-transform" id="icon-container-technical">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                                </svg>
                            </div>
                            <div>
                                <span class="block text-sm font-black text-slate-900">Technical Compliance</span>
                                <span class="block text-xs font-medium text-slate-500 mt-0.5">Acts, Statutory Laws, Audit & Recommendations</span>
                            </div>
                        </div>
                    </label>

                    <label class="relative flex flex-col p-5 rounded-2xl border-2 bg-white cursor-pointer hover:bg-slate-50/50 transition-all select-none group border-slate-200" id="label-track-effectiveness">
                        <input type="radio" name="workspace_track" value="effectiveness" class="sr-only" @checked(old('workspace_track') === 'effectiveness') onchange="switchWorkspaceContext('effectiveness')">
                        <div class="flex items-center gap-4">
                            <div class="p-3 rounded-xl bg-slate-100 text-slate-600 group-hover:scale-105 transition-transform" id="icon-container-effectiveness">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z" />
                                </svg>
                            </div>
                            <div>
                                <span class="block text-sm font-black text-slate-900">Effectiveness Outcomes</span>
                                <span class="block text-xs font-medium text-slate-500 mt-0.5">Immediate Outcomes (IO 1 - 11) & Sub-IO Contexts</span>
                            </div>
                        </div>
                    </label>

                </div>
            </div>

            <div id="documentAttributesCard" class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xs transition-all duration-500">
                <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                    <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">2. Document Meta Attributes & Directories</h2>
                </div>

                <div class="space-y-6 p-6">
                    
                    <div id="section-technical-directories" class="space-y-3">
                        <div class="flex items-center justify-between pb-1">
                            <div>
                                <label class="block text-xs font-black uppercase tracking-wider text-slate-700">Select Target Technical Compliance Folders</label>
                                <p class="text-xxs text-slate-500 mt-0.5">Select multiple structural folders across the unified workspace framework.</p>
                            </div>
                            <button type="button" onclick="toggleCheckboxGroup('tech-folder-cb')" class="text-[10px] font-extrabold text-blue-600 hover:text-blue-800 uppercase tracking-wide cursor-pointer select-none bg-blue-50 px-2.5 py-1 rounded-md transition-colors hover:bg-blue-100/70">
                                Toggle All Folders
                            </button>
                        </div>
        <div class="max-h-64 overflow-y-auto p-3 bg-slate-50/40 rounded-2xl border border-slate-200/60 grid gap-2.5 sm:grid-cols-2 lg:grid-cols-3">
    @foreach($technicalFolders ?? [] as $tFolder)
        @php
            // 🌟 Normalizing manual inputs ('private') and seeds ('fiu-private') safely for the document creator scope matching
            $rawScope = strtolower(trim($tFolder->visibility_scope ?? 'shared'));
            $folderScope = ($rawScope === 'fiu-private' || $rawScope === 'private') ? 'internal' : 'shared';
        @endphp
        
        <label data-folder-scope="{{ $folderScope }}" class="tech-folder-wrapper form-folder-item flex items-start gap-3 p-3 rounded-xl bg-white border border-slate-200/60 shadow-2xs hover:bg-slate-50 hover:border-slate-300 transition-all cursor-pointer select-none group">
            <input type="checkbox" name="technical_folder_ids[]" value="{{ $tFolder->id }}" class="tech-folder-cb rounded border-slate-300 text-blue-600 focus:ring-blue-500 mt-0.5 h-4 w-4 transition-transform group-hover:scale-105" @checked(is_array(old('technical_folder_ids')) && in_array($tFolder->id, old('technical_folder_ids')))>
            <div class="min-w-0 flex-1">
                <span class="block text-xs font-bold text-slate-800 truncate group-hover:text-blue-900 transition-colors">{{ $tFolder->name }}</span>
                @if($tFolder->description)
                    <span class="block text-[10px] font-medium text-slate-400 line-clamp-1 mt-0.5 leading-tight">{{ $tFolder->description }}</span>
                @endif
            </div>
        </label>
    @endforeach
</div>

                    <div id="section-effectiveness-directories" class="hidden space-y-4">
                        <div class="space-y-2">
                            <label class="block text-xs font-black uppercase tracking-wider text-slate-700">Choose an Immediate Outcome (IO) to manage its sub-IOs</label>
                            <div class="grid grid-cols-3 gap-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-11">
                                @foreach($immediateOutcomes ?? [] as $io)
                                    <button type="button" id="btn-io-{{ $io->id }}" onclick="setActiveIOContext({{ $io->id }})" class="io-tab-btn flex flex-col items-center justify-center p-2.5 rounded-xl border-2 text-center transition-all cursor-pointer select-none bg-white border-slate-200 hover:bg-slate-50">
                                        <span class="text-xs font-black text-slate-900">{{ $io->code }}</span>
                                        <span id="badge-io-{{ $io->id }}" class="hidden mt-1 px-1.5 py-0.5 text-[9px] font-black bg-violet-600 text-white rounded-md">0 selected</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-black uppercase tracking-wider text-slate-700">Select related sub-IO targets for the active IO</label>
                                <button type="button" onclick="toggleActiveSubIOCbGroup()" class="text-[10px] font-extrabold text-violet-600 hover:text-violet-800 uppercase tracking-wide cursor-pointer select-none">Toggle All in Active IO</button>
                            </div>
                            <div class="max-h-60 overflow-y-auto p-3 bg-slate-50/40 rounded-2xl border border-slate-200/60 grid gap-2.5 sm:grid-cols-2 md:grid-cols-3">
                                @foreach($subOutcomes ?? [] as $subIo)
                                    <div class="sub-io-wrapper hidden" data-io="{{ $subIo->immediate_outcome_id }}">
                                        <label class="flex items-start gap-3 p-3 w-full rounded-xl bg-white border border-slate-200/60 shadow-2xs hover:bg-slate-50 transition-all cursor-pointer select-none">
                                            <input type="checkbox" name="effectiveness_sub_io_ids[]" value="{{ $subIo->id }}" data-parent-io="{{ $subIo->immediate_outcome_id }}" class="sub-io-cb rounded border-slate-300 text-violet-600 focus:ring-violet-500 mt-0.5 h-4 w-4" @checked(is_array(old('effectiveness_sub_io_ids')) && in_array($subIo->id, old('effectiveness_sub_io_ids')))>
                                            <div class="min-w-0">
                                                <span class="block text-xs font-bold text-slate-800">{{ $subIo->code }}</span>
                                                <span class="block text-[11px] font-medium text-slate-500 mt-0.5 leading-normal">{{ $subIo->title }}</span>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label for="title" class="block text-xs font-black uppercase tracking-wider text-slate-700">Document Title</label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}" required class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/40 p-2.5 text-sm font-medium outline-none focus:border-slate-800 focus:bg-white transition-all" placeholder="Enter full systemic document title name node">
                        </div>

                        <div class="md:col-span-2 space-y-2">
                            <label class="block text-xs font-black uppercase tracking-wider text-slate-700">Document Security Isolation Scope</label>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mt-2">
                                
                                <label id="doc-label-scope-shared" class="relative flex items-start gap-4 p-5 rounded-2xl border-2 cursor-pointer transition-all duration-300 select-none group border-slate-200 bg-white text-slate-700 shadow-2xs">
                                    <input type="radio" name="visibility_scope" value="shared" class="text-blue-600 focus:ring-blue-400 h-4 w-4 mt-0.5" onchange="handleDocumentScopeChange('shared')" @checked(old('visibility_scope', 'shared') === 'shared')>
                                    <div class="min-w-0 flex-1">
                                        <span id="doc-title-scope-shared" class="block text-sm font-black text-slate-800 uppercase tracking-wide group-hover:text-blue-900 transition-colors">👥 Shared / Institutional Asset</span>
                                        <span id="doc-desc-scope-shared" class="block text-xs font-medium text-slate-400 mt-1 leading-relaxed transition-colors">Visible to target institutions assigned down in the directory map panel matrix.</span>
                                    </div>
                                </label>

                                <label id="doc-label-scope-internal" class="relative flex items-start gap-4 p-5 rounded-2xl border-2 cursor-pointer transition-all duration-300 select-none group border-slate-200 bg-white text-slate-700 shadow-2xs">
                                    <input type="radio" name="visibility_scope" value="internal" class="text-red-600 focus:ring-red-400 h-4 w-4 mt-0.5" onchange="handleDocumentScopeChange('internal')" @checked(old('visibility_scope') === 'internal')>
                                    <div class="min-w-0 flex-1">
                                        <span id="doc-title-scope-internal" class="block text-sm font-black text-slate-800 uppercase tracking-wide group-hover:text-red-600 transition-colors">🔒 Internal Audit Only (FIU Private)</span>
                                        <span id="doc-desc-scope-internal" class="block text-xs font-semibold text-slate-400 mt-1 leading-relaxed transition-colors">100% hidden from external visibility maps, regardless of destination directory targets.</span>
                                    </div>
                                </label>
                                
                            </div>
                        </div>

                        <div>
                            <label for="name" class="block text-xs font-black uppercase tracking-wider text-slate-700">Internal Document Code / Tag (Optional)</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/40 p-2.5 text-sm font-medium outline-none focus:border-slate-800 focus:bg-white transition-all" placeholder="e.g. FIU-TC-ACTS-2026">
                        </div>

                        <div>
                            <label for="reporting_institution" class="block text-xs font-black uppercase tracking-wider text-slate-700">Originating Source Authority</label>
                            <input type="text" id="reporting_institution" name="reporting_institution" value="{{ old('reporting_institution') }}" required class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/40 p-2.5 text-sm font-medium outline-none focus:border-slate-800 focus:bg-white transition-all" placeholder="e.g. Reserve Bank of Zimbabwe / FIU Core Engine">
                        </div>

                        <div>
                            <label for="date_logged" class="block text-xs font-black uppercase tracking-wider text-slate-700">Date Logged</label>
                            <input type="date" id="date_logged" name="date_logged" value="{{ old('date_logged', now()->toDateString()) }}" required class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/40 p-2.5 text-sm font-medium outline-none focus:border-slate-800 focus:bg-white transition-all">
                        </div>

                        <div>
                            <label for="document_date" class="block text-xs font-black uppercase tracking-wider text-slate-700">Official Document Date</label>
                            <input type="date" id="document_date" name="document_date" value="{{ old('document_date') }}" class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/40 p-2.5 text-sm font-medium outline-none focus:border-slate-800 focus:bg-white transition-all">
                        </div>

                        <div class="md:col-span-2">
                            <label for="status" class="block text-xs font-black uppercase tracking-wider text-slate-700">Compliance Processing Status</label>
                            <select id="status" name="status" required class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/40 p-2.5 text-sm font-medium outline-none focus:border-slate-800 focus:bg-white transition-all">
                                @foreach(($documentStatuses ?? ['submitted' => 'Submitted', 'verified' => 'Verified', 'rejected' => 'Rejected']) as $val => $lbl)
                                    <option value="{{ $val }}" @selected(old('status', 'submitted') === $val)>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label for="remarks" class="block text-xs font-black uppercase tracking-wider text-slate-700">Remarks & Audit Logs Summary</label>
                            <textarea id="remarks" name="remarks" rows="3" class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/40 p-2.5 text-sm font-medium outline-none focus:border-slate-800 focus:bg-white transition-all" placeholder="Optional comments..."></textarea>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/40 p-5 m-6 mt-0 space-y-4">
                        <span class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Storage Assets Setup</span>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label for="document_file" class="block text-xs font-bold text-slate-700">Upload New Binary File</label>
                                <input type="file" id="document_file" name="document_file" class="mt-2 block w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-slate-900 file:text-white hover:file:bg-slate-800 transition-colors cursor-pointer bg-white border border-slate-200 rounded-xl p-1.5 shadow-xs">
                                <p class="mt-1.5 text-[11px] text-slate-500">Accepted: PDF, Word, Excel, PowerPoint, CSV (Max 50MB).</p>
                            </div>
                            <div>
                                <label for="external_file_name" class="block text-[11px] font-bold text-slate-600">Existing File Target Name</label>
                                <input type="text" id="external_file_name" name="external_file_name" value="{{ old('external_file_name') }}" class="mt-1.5 block w-full rounded-lg border border-slate-200 p-2 text-xs" placeholder="Use if already persisted">
                            </div>
                            <div>
                                <label for="external_file_path" class="block text-[11px] font-bold text-slate-600">Existing Storage Path / Remote URL</label>
                                <input type="text" id="external_file_path" name="external_file_path" value="{{ old('external_file_path') }}" class="mt-1.5 block w-full rounded-lg border border-slate-200 p-2 text-xs" placeholder="storage/compliance-documents/...">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xs flex flex-col">
                    <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 id="aside-panel-title" class="text-sm font-black text-slate-900 uppercase tracking-wider">3. Assign Access Mappings</h3>
                                <p id="aside-panel-desc" class="text-xs text-slate-500 mt-0.5">Determine visibility maps for this record execution node.</p>
                            </div>
                            <button type="button" onclick="toggleActiveAsideCheckboxGroup()" class="text-[10px] font-extrabold text-blue-600 hover:text-blue-800 uppercase tracking-wide cursor-pointer select-none bg-blue-50 px-3 py-1.5 rounded-md transition-colors hover:bg-blue-100/70">
                                Toggle All Listings
                            </button>
                        </div>
                    </div>

                    <div id="wrapper-institutions-checkboxes" class="p-6 overflow-y-auto max-h-96 space-y-2 bg-slate-50/10 grid gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                        @foreach($institutions ?? [] as $inst)
                            <label class="aside-cb-label flex items-start gap-3 p-3.5 rounded-xl bg-white border border-slate-200/60 shadow-2xs hover:bg-slate-50 transition-all cursor-pointer select-none group">
                                <input type="checkbox" name="target_institutions[]" value="{{ $inst->id }}" class="institution-cb aside-target-cb rounded border-slate-300 text-blue-600 focus:ring-blue-500 mt-0.5 h-4 w-4">
                                <div class="min-w-0 flex-1">
                                    <span class="block text-xs font-bold text-slate-700 group-hover:text-blue-900 transition-colors truncate">{{ $inst->name }}</span>
                                    <span class="block text-[10px] font-medium text-slate-400 mt-0.5 tracking-wider uppercase">Tenant Code: {{ $inst->code }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <div id="wrapper-fiu-users-checkboxes" class="hidden p-6 overflow-y-auto max-h-96 space-y-2 bg-slate-50/10 grid gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                        @foreach($fiuUsers ?? [] as $fUser)
                            <label class="aside-cb-label flex items-start gap-3 p-3.5 rounded-xl bg-white border border-slate-200/60 shadow-2xs hover:bg-slate-50 transition-all cursor-pointer select-none group">
                                <input type="checkbox" name="target_users[]" value="{{ $fUser->id }}" class="fiu-user-cb aside-target-cb rounded border-slate-300 text-rose-600 focus:ring-rose-500 mt-0.5 h-4 w-4">
                                <div class="min-w-0 flex-1">
                                    <span class="block text-xs font-bold text-slate-700 group-hover:text-rose-900 transition-colors truncate">{{ $fUser->name }}</span>
                                    <span class="block text-[10px] font-medium text-slate-400 mt-0.5 truncate">{{ $fUser->email }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-t border-slate-100 pt-5">
                    <p class="text-xs text-slate-500 max-w-xl">This record links up with selected directories, updating analytics indices dynamically across the infrastructure.</p>
                    <div class="flex gap-2">
                        <a href="javascript:history.back()" class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-300 px-5 text-xs font-black text-slate-700 transition hover:bg-slate-50 shadow-xs">Cancel</a>
                        <button type="submit" id="submitButtonWidget" class="inline-flex h-11 items-center justify-center rounded-xl bg-blue-700 px-6 text-xs font-black text-white transition hover:bg-blue-800 shadow-sm cursor-pointer whitespace-nowrap">Save Document Asset</button>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script>
        let currentActiveScope = 'shared';

        function handleDocumentScopeChange(scope) {

       // 🌟 STEP 1: INTERCEPT & ISOLATE THE TARGET COMPLIANCE FOLDERS IMMEDIATELY
            const formFolders = document.querySelectorAll('.form-folder-item');
            formFolders.forEach(folderCard => {
                const fScope = folderCard.getAttribute('data-folder-scope');
                
                if (fScope === scope) {
                    // Show matching folder options natively
                    folderCard.style.setProperty('display', 'flex', 'important');
                } else {
                    // Hide opposite options completely and reset their inputs to avoid dirty state submission mutations
                    folderCard.style.setProperty('display', 'none', 'important');
                    const cb = folderCard.querySelector('input[type="checkbox"]');
                    if (cb) {
                        cb.checked = false;
                        updateCheckboxLabelHighlight(cb);
                    }
                }
            });




            const sharedLabel = document.getElementById('doc-label-scope-shared');
            const internalLabel = document.getElementById('doc-label-scope-internal');
            const sharedTitle = document.getElementById('doc-title-scope-shared');
            const sharedDesc = document.getElementById('doc-desc-scope-shared');
            const internalTitle = document.getElementById('doc-title-scope-internal');
            const internalDesc = document.getElementById('doc-desc-scope-internal');

            const attributesCard = document.getElementById('documentAttributesCard');
            const submitBtn = document.getElementById('submitButtonWidget');

            const instWrapper = document.getElementById('wrapper-institutions-checkboxes');
            const userWrapper = document.getElementById('wrapper-fiu-users-checkboxes');
            const panelTitle = document.getElementById('aside-panel-title');
            const panelDesc = document.getElementById('aside-panel-desc');

            if (scope === 'shared') {
                currentActiveScope = 'shared';
                
                sharedLabel.className = "relative flex items-start gap-4 p-5 rounded-2xl border-2 bg-blue-600/30 border-blue-600 text-blue-900 cursor-pointer transition-all duration-300 select-none group shadow-xs ring-4 ring-blue-500/10 animate-attention-breath";
                sharedTitle.className = "block text-sm font-black text-blue-900 uppercase tracking-wide";
                sharedDesc.className = "block text-xs font-semibold text-blue-700/80 mt-1 leading-relaxed";

                internalLabel.className = "relative flex items-start gap-4 p-5 rounded-2xl border-2 bg-white border-slate-200 text-slate-700 cursor-pointer hover:bg-slate-50/50 transition-all duration-300 select-none group shadow-2xs";
                internalTitle.className = "block text-sm font-black text-slate-800 uppercase tracking-wide group-hover:text-rose-700 transition-colors";
                internalDesc.className = "block text-xs font-medium text-slate-400 mt-1 leading-relaxed";

                attributesCard.className = "overflow-hidden rounded-3xl border border-blue-300 bg-blue-100 shadow-xs transition-all duration-500";
                submitBtn.className = "inline-flex h-11 items-center justify-center rounded-xl bg-blue-700 px-6 text-xs font-black text-white transition hover:bg-blue-800 shadow-sm cursor-pointer whitespace-nowrap";

                instWrapper.style.display = 'grid';
                userWrapper.style.display = 'none';
                userWrapper.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                    cb.checked = false;
                    updateCheckboxLabelHighlight(cb);
                });

                panelTitle.innerText = "3. Target Institutions Access Mappings";
                panelDesc.innerText = "Check one or more institutions to append this asset into their multi-tenant workspace matrix grids.";
            } else {
                currentActiveScope = 'internal';

                sharedLabel.className = "relative flex items-start gap-4 p-5 rounded-2xl border-2 bg-white border-slate-200 text-slate-700 cursor-pointer hover:bg-slate-50/50 transition-all duration-300 select-none group shadow-2xs";
                sharedTitle.className = "block text-sm font-black text-slate-800 uppercase tracking-wide group-hover:text-blue-900 transition-colors";
                sharedDesc.className = "block text-xs font-medium text-slate-400 mt-1 leading-relaxed";

                internalLabel.className = "relative flex items-start gap-4 p-5 rounded-2xl border-2 bg-rose-600/30 border-rose-600 text-rose-900 cursor-pointer transition-all duration-300 select-none group shadow-xs ring-4 ring-rose-500/10 animate-attention-breath";
                internalTitle.className = "block text-sm font-black text-rose-900 uppercase tracking-wide";
                internalDesc.className = "block text-xs font-semibold text-rose-700/80 mt-1 leading-relaxed";

                attributesCard.className = "overflow-hidden rounded-3xl border border-rose-300 bg-rose-100 shadow-xs transition-all duration-500";
                submitBtn.className = "inline-flex h-11 items-center justify-center rounded-xl bg-rose-600 px-6 text-xs font-black text-white transition hover:bg-rose-700 shadow-sm cursor-pointer whitespace-nowrap";

                instWrapper.style.display = 'none';
                userWrapper.style.display = 'grid';
                instWrapper.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                    cb.checked = false;
                    updateCheckboxLabelHighlight(cb);
                });

                panelTitle.innerText = "3. Target FIU Internal Auditor Access";
                panelDesc.innerText = "Check internal protection officers authorized to inspect this sandboxed private execution node.";
            }
        }

        function toggleCheckboxGroup(checkboxClass) {
            const checkboxes = document.querySelectorAll('.' + checkboxClass);
            if (checkboxes.length === 0) return;
            const targetState = !checkboxes[0].checked;
            checkboxes.forEach(cb => {
                cb.checked = targetState;
                updateCheckboxLabelHighlight(cb);
            });
        }

        function toggleActiveAsideCheckboxGroup() {
            const activeSelector = (currentActiveScope === 'shared') ? '.institution-cb' : '.fiu-user-cb';
            const checkboxes = document.querySelectorAll(activeSelector);
            if (checkboxes.length === 0) return;
            const targetState = !checkboxes[0].checked;
            checkboxes.forEach(cb => {
                cb.checked = targetState;
                updateCheckboxLabelHighlight(cb);
            });
        }

        function updateCheckboxLabelHighlight(cb) {
            const labelWrapper = cb.closest('.tech-folder-wrapper, .sub-io-wrapper label, .aside-cb-label');
            if (!labelWrapper) return;

            const isUser = cb.classList.contains('fiu-user-cb');
            const highlightThemeClass = isUser ? 'bg-rose-600/10' : 'bg-blue-600/10';
            const borderThemeClass = isUser ? 'border-rose-500' : 'border-blue-500';
            const ringThemeClass = isUser ? 'ring-rose-100' : 'ring-blue-100';

            if (cb.checked) {
                labelWrapper.classList.remove('bg-white', 'border-slate-200/60');
                labelWrapper.classList.add(highlightThemeClass, borderThemeClass, 'ring-1', ringThemeClass);
            } else {
                labelWrapper.classList.remove(highlightThemeClass, borderThemeClass, 'ring-1', ringThemeClass);
                labelWrapper.classList.add('bg-white', 'border-slate-200/60');
            }
        }

        function switchWorkspaceContext(track) {
            const techLabel = document.getElementById('label-track-technical');
            const effectLabel = document.getElementById('label-track-effectiveness');
            const techIcon = document.getElementById('icon-container-technical');
            const effectIcon = document.getElementById('icon-container-effectiveness');

            const techSection = document.getElementById('section-technical-directories');
            const effectSection = document.getElementById('section-effectiveness-directories');

            if (track === 'technical') {
                techLabel.className = "relative flex flex-col p-5 rounded-2xl border-2 bg-blue-50/40 cursor-pointer transition-all select-none group border-blue-600 ring-1 ring-blue-600/10 scale-101 shadow-xs";
                techIcon.className = "p-3 rounded-xl bg-blue-600 text-white transition-transform";
                effectLabel.className = "relative flex flex-col p-5 rounded-2xl border-2 bg-white cursor-pointer hover:bg-slate-50/50 transition-all select-none group border-slate-200";
                effectIcon.className = "p-3 rounded-xl bg-slate-100 text-slate-600 transition-transform";

                techSection.classList.remove('hidden');
                effectSection.classList.add('hidden');
            } else {
                effectLabel.className = "relative flex flex-col p-5 rounded-2xl border-2 bg-violet-50/40 cursor-pointer transition-all select-none group border-violet-600 ring-1 ring-violet-600/10 scale-101 shadow-xs";
                effectIcon.className = "p-3 rounded-xl bg-violet-600 text-white transition-transform";
                techLabel.className = "relative flex flex-col p-5 rounded-2xl border-2 bg-white cursor-pointer hover:bg-slate-50/50 transition-all select-none group border-slate-200";
                techIcon.className = "p-3 rounded-xl bg-blue-50 text-blue-700 transition-transform";

                techSection.classList.add('hidden');
                effectSection.classList.remove('hidden');
                
                const firstActiveIoButton = document.querySelector('.io-tab-btn');
                if (firstActiveIoButton) {
                    const match = firstActiveIoButton.id.match(/\d+/);
                    if (match) setActiveIOContext(parseInt(match[0]));
                }
            }
        }

        function setActiveIOContext(ioId) {
            document.querySelectorAll('.io-tab-btn').forEach(btn => {
                btn.className = "io-tab-btn flex flex-col items-center justify-center p-2.5 rounded-xl border-2 text-center transition-all cursor-pointer select-none bg-white border-slate-200 hover:bg-slate-50";
            });
            
            const currentBtn = document.getElementById('btn-io-' + ioId);
            if (currentBtn) {
                currentBtn.className = "io-tab-btn flex flex-col items-center justify-center p-2.5 rounded-xl border-2 text-center transition-all cursor-pointer select-none bg-violet-600 border-violet-700 text-white shadow-xs scale-102";
                currentBtn.querySelector('span').className = "text-xs font-black text-white";
            }

            document.querySelectorAll('.sub-io-wrapper').forEach(wrapper => {
                if (parseInt(wrapper.getAttribute('data-io')) === ioId) {
                    wrapper.style.display = 'block';
                } else {
                    wrapper.style.display = 'none';
                }
            });
        }

        function toggleActiveSubIOCbGroup() {
            const currentActiveIoBtn = document.querySelector('.io-tab-btn.bg-violet-600');
            if (!currentActiveIoBtn) return;
            const ioId = currentActiveIoBtn.id.match(/\d+/)[0];
            const activeCheckboxes = document.querySelectorAll(`.sub-io-cb[data-parent-io="${ioId}"]`);
            if (activeCheckboxes.length === 0) return;
            const targetState = !activeCheckboxes[0].checked;
            activeCheckboxes.forEach(cb => {
                cb.checked = targetState;
                updateCheckboxLabelHighlight(cb);
            });
            refreshIOTabSelectionCounters();
        }

        function refreshIOTabSelectionCounters() {
            document.querySelectorAll('.io-tab-btn').forEach(btn => {
                const ioId = btn.id.match(/\d+/)[0];
                const checkedCount = document.querySelectorAll(`.sub-io-cb[data-parent-io="${ioId}"]:checked`).length;
                const badge = document.getElementById('badge-io-' + ioId);
                if (checkedCount > 0) {
                    badge.innerText = `${checkedCount} chosen`;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const selectedTrack = document.querySelector('input[name="workspace_track"]:checked')?.value || 'technical';
            switchWorkspaceContext(selectedTrack);

            const selectedScope = document.querySelector('input[name="visibility_scope"]:checked')?.value || 'shared';
            handleDocumentScopeChange(selectedScope);

            document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                updateCheckboxLabelHighlight(cb);
                cb.addEventListener('change', function() {
                    updateCheckboxLabelHighlight(this);
                    if (this.classList.contains('sub-io-cb')) {
                        refreshIOTabSelectionCounters();
                    }
                });
            });
        });
    </script>
</x-app-layout>