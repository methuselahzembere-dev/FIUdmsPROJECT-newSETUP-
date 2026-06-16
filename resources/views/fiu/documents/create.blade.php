<x-app-layout>
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
                        Dispatch institutional records into specific compliance paths, target structural folders, and select direct multi-tenant access lists.
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

        <form method="POST" action="{{ route('fiu.documents.store') }}" enctype="multipart/form-data" class="grid gap-6 xl:grid-cols-[minmax(0,2fr)_minmax(340px,1fr)]">
            @csrf

            <div class="space-y-6">
                
                <div id="trackSelectorCard" class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition-all duration-300">
                    <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                        <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">1. Select Target Compliance Workspace</h2>
                        <p class="text-xs text-slate-500">Route this document entry into its respective regulatory reporting track archetype.</p>
                    </div>
                    <div class="p-6 grid gap-4 sm:grid-cols-2">
                        
                        <label class="relative flex flex-col p-4 rounded-2xl border-2 bg-white cursor-pointer hover:bg-slate-50/50 transition-all select-none group border-slate-200" id="label-track-technical">
                            <input type="radio" name="workspace_track" value="technical" class="sr-only" @checked(old('workspace_track', 'technical') === 'technical') onchange="switchWorkspaceContext('technical')">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-xl bg-blue-50 text-blue-700 group-hover:scale-105 transition-transform" id="icon-container-technical">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="block text-sm font-black text-slate-900">Technical Compliance</span>
                                    <span class="block text-xxs font-medium text-slate-500 mt-0.5">Acts, Statutory Laws, Audit & Recommendations</span>
                                </div>
                            </div>
                        </label>

                        <label class="relative flex flex-col p-4 rounded-2xl border-2 bg-white cursor-pointer hover:bg-slate-50/50 transition-all select-none group border-slate-200" id="label-track-effectiveness">
                            <input type="radio" name="workspace_track" value="effectiveness" class="sr-only" @checked(old('workspace_track') === 'effectiveness') onchange="switchWorkspaceContext('effectiveness')">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-xl bg-slate-100 text-slate-600 group-hover:scale-105 transition-transform" id="icon-container-effectiveness">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="block text-sm font-black text-slate-900">Effectiveness Outcomes</span>
                                    <span class="block text-xxs font-medium text-slate-500 mt-0.5">Immediate Outcomes (IO 1 - 11) & Sub-IO Contexts</span>
                                </div>
                            </div>
                        </label>

                    </div>
                </div>

                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                        <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">2. Document Meta Attributes & Directories</h2>
                    </div>

                    <div class="space-y-6 p-6">
                        
                        <div id="section-technical-directories" class="grid gap-5 md:grid-cols-1">
                            <div>
                                <label for="technical_folder_id" class="block text-xs font-black uppercase tracking-wider text-slate-700">Target Technical Compliance Folder</label>
                                <select id="technical_folder_id" name="technical_folder_id" class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/40 p-2.5 text-sm font-medium outline-none focus:border-blue-600 focus:bg-white transition-all">
                                    <option value="" disabled @selected(!old('technical_folder_id'))>Select specific framework folder...</option>
                                    @foreach($technicalFolders ?? [] as $tFolder)
                                        <option value="{{ $tFolder->id }}" @selected(old('technical_folder_id') == $tFolder->id)>{{ $tFolder->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="section-effectiveness-directories" class="hidden grid gap-5 md:grid-cols-2">
                            <div>
                                <label for="immediate_outcome_id" class="block text-xs font-black uppercase tracking-wider text-slate-700">Main Immediate Outcome (IO)</label>
                                <select id="immediate_outcome_id" name="immediate_outcome_id" onchange="filterSubOutcomesByMainIO(this.value)" class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/40 p-2.5 text-sm font-medium outline-none focus:border-violet-600 focus:bg-white transition-all">
                                    <option value="" disabled @selected(!old('immediate_outcome_id'))>Select Core IO (1 to 11)...</option>
                                    @foreach($immediateOutcomes ?? [] as $io)
                                        <option value="{{ $io->id }}" @selected(old('immediate_outcome_id') == $io->id)>{{ $io->code }} - {{ $io->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="effectiveness_sub_io_id" class="block text-xs font-black uppercase tracking-wider text-slate-700">Specific Sub-IO Outcome Target</label>
                                <select id="effectiveness_sub_io_id" name="effectiveness_sub_io_id" class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/40 p-2.5 text-sm font-medium outline-none focus:border-violet-600 focus:bg-white transition-all">
                                    <option value="" data-io="all" disabled @selected(!old('effectiveness_sub_io_id'))>Select matching sub-indicator outcome...</option>
                                    @foreach($subOutcomes ?? [] as $subIo)
                                        <option value="{{ $subIo->id }}" data-io="{{ $subIo->immediate_outcome_id }}" class="sub-io-option" @selected(old('effectiveness_sub_io_id') == $subIo->id)>
                                            {{ $subIo->code }} - {{ $subIo->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <label for="title" class="block text-xs font-black uppercase tracking-wider text-slate-700">Document Title</label>
                                <input type="text" id="title" name="title" value="{{ old('title') }}" required class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/40 p-2.5 text-sm font-medium outline-none focus:border-slate-800 focus:bg-white transition-all" placeholder="Enter full systemic document title name node">
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

                            <div>
                                <label for="status" class="block text-xs font-black uppercase tracking-wider text-slate-700">Compliance Processing Status</label>
                                <select id="status" name="status" required class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/40 p-2.5 text-sm font-medium outline-none focus:border-slate-800 focus:bg-white transition-all">
                                    @foreach(($documentStatuses ?? ['submitted' => 'Submitted', 'verified' => 'Verified', 'rejected' => 'Rejected']) as $val => $lbl)
                                        <option value="{{ $val }}" @selected(old('status', 'submitted') === $val)>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label for="remarks" class="block text-xs font-black uppercase tracking-wider text-slate-700">Remarks & Audit Logs Summary</label>
                                <textarea id="remarks" name="remarks" rows="3" class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/40 p-2.5 text-sm font-medium outline-none focus:border-slate-800 focus:bg-white transition-all" placeholder="Optional implementation comments, evidence tracking notes, or legislative history context..."></textarea>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/40 p-4 space-y-4">
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Storage Assets Setup</span>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <label for="document_file" class="block text-xs font-bold text-slate-700">Upload New Binary File</label>
                                    <input type="file" id="document_file" name="document_file" class="mt-2 block w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-slate-900 file:text-white hover:file:bg-slate-800 transition-colors cursor-pointer bg-white border border-slate-200 rounded-xl p-1.5 shadow-xs">
                                    <p class="mt-1.5 text-[11px] text-slate-500">Accepted: PDF, Word, Excel, PowerPoint, CSV, JPG, PNG (Max 10MB).</p>
                                </div>
                                <div>
                                    <label for="external_file_name" class="block text-[11px] font-bold text-slate-600">Existing File Target Name</label>
                                    <input type="text" id="external_file_name" name="external_file_name" value="{{ old('external_file_name') }}" class="mt-1.5 block w-full rounded-lg border border-slate-200 p-2 text-xs" placeholder="Use if already persisted in system">
                                </div>
                                <div>
                                    <label for="external_file_path" class="block text-[11px] font-bold text-slate-600">Existing Storage Path / Remote URL</label>
                                    <input type="text" id="external_file_path" name="external_file_path" value="{{ old('external_file_path') }}" class="mt-1.5 block w-full rounded-lg border border-slate-200 p-2 text-xs" placeholder="storage/compliance-documents/...">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-t border-slate-100 pt-4">
                    <p class="text-xs text-slate-500 max-w-md">This record file links up immediately with the chosen tracking directories, updating analytics indices dynamically across the dashboard infrastructure.</p>
                    <div class="flex gap-2">
                        <a href="javascript:history.back()" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-300 px-4 text-xs font-black text-slate-700 transition hover:bg-slate-50 shadow-xs">Cancel</a>
                        <button type="submit" id="submitButtonWidget" class="inline-flex h-10 items-center justify-center rounded-xl bg-blue-700 px-5 text-xs font-black text-white transition hover:bg-blue-800 shadow-sm cursor-pointer whitespace-nowrap">Save Document Asset</button>
                    </div>
                </div>

            </div>

            <aside class="space-y-6">
                
                <div class="rounded-3xl border border-slate-200 bg-white shadow-sm flex flex-col h-[320px] overflow-hidden">
                    <div class="border-b border-slate-100 bg-slate-50/50 px-5 py-3 shrink-0 flex items-center justify-between">
                        <div>
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">3. Institution Access</h3>
                            <p class="text-[10px] text-slate-400 font-medium">Tick organizations permitted to audit this record</p>
                        </div>
                        <button type="button" onclick="toggleCheckboxGroup('institution-cb')" class="text-[10px] font-extrabold text-blue-600 hover:text-blue-800 uppercase tracking-wide cursor-pointer select-none">Toggle All</button>
                    </div>
                    
                    <div class="flex-1 overflow-y-auto p-4 space-y-2 bg-slate-50/30">
                        @forelse($institutions ?? [] as $inst)
                            <label class="flex items-start gap-3 p-2.5 rounded-xl bg-white border border-slate-200/60 hover:bg-slate-50/80 transition-all shadow-2xs cursor-pointer select-none">
                                <input type="checkbox" name="target_institutions[]" value="{{ $inst->id }}" class="institution-cb rounded border-slate-300 text-blue-600 focus:ring-blue-500 mt-0.5 h-4 w-4">
                                <div class="min-w-0">
                                    <span class="block text-xs font-bold text-slate-800 truncate">{{ $inst->name }}</span>
                                    <span class="block text-[10px] font-black text-slate-400 tracking-wide uppercase mt-0.5">ID: {{ $inst->id }}</span>
                                </div>
                            </label>
                        @empty
                            <div class="text-center py-12 text-xs text-slate-400 italic">No structural client tenant institutions loaded.</div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white shadow-sm flex flex-col h-[320px] overflow-hidden">
                    <div class="border-b border-slate-100 bg-slate-50/50 px-5 py-3 shrink-0 flex items-center justify-between">
                        <div>
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">4. User Account Visibility</h3>
                            <p class="text-[10px] text-slate-400 font-medium">Assign access privileges to specific user profiles</p>
                        </div>
                        <button type="button" onclick="toggleCheckboxGroup('user-cb')" class="text-[10px] font-extrabold text-blue-600 hover:text-blue-800 uppercase tracking-wide cursor-pointer select-none">Toggle All</button>
                    </div>
                    
                    <div class="flex-1 overflow-y-auto p-4 space-y-2 bg-slate-50/30">
                        @forelse($users ?? [] as $usr)
                            <label class="flex items-start gap-3 p-2.5 rounded-xl bg-white border border-slate-200/60 hover:bg-slate-50/80 transition-all shadow-2xs cursor-pointer select-none">
                                <input type="checkbox" name="target_users[]" value="{{ $usr->id }}" class="user-cb rounded border-slate-300 text-blue-600 focus:ring-blue-500 mt-0.5 h-4 w-4">
                                <div class="min-w-0">
                                    <span class="block text-xs font-bold text-slate-800 truncate">{{ $usr->name }}</span>
                                    <span class="block text-[10px] font-medium text-slate-500 truncate mt-0.5">{{ $usr->email }}</span>
                                </div>
                            </label>
                        @empty
                            <div class="text-center py-12 text-xs text-slate-400 italic">No individual accounts registered in workspace.</div>
                        @endforelse
                    </div>
                </div>

            </aside>
        </form>
    </div>

    <script>
        /**
         * Orchestrates immediate layout transitions and changes styles matching the active compliance track
         */
        function switchWorkspaceContext(track) {
            const techSection = document.getElementById('section-technical-directories');
            const effSection = document.getElementById('section-effectiveness-directories');
            const submitBtn = document.getElementById('submitButtonWidget');
            
            const techCardLabel = document.getElementById('label-track-technical');
            const effCardLabel = document.getElementById('label-track-effectiveness');
            const techIconBox = document.getElementById('icon-container-technical');
            const effIconBox = document.getElementById('icon-container-effectiveness');

            if (track === 'technical') {
                // 1. Swap Dropdown Sub-Sections
                techSection.classList.remove('hidden');
                effSection.classList.add('hidden');
                
                // 2. Clear out inputs in the hidden fields to prevent messy structural payloads
                document.getElementById('immediate_outcome_id').value = "";
                document.getElementById('effectiveness_sub_io_id').value = "";

                // 3. Re-theme Action UI Components (Blue for Technical Compliance)
                submitBtn.className = "inline-flex h-10 items-center justify-center rounded-xl bg-blue-700 px-5 text-xs font-black text-white transition hover:bg-blue-800 shadow-sm cursor-pointer whitespace-nowrap";
                techCardLabel.className = "relative flex flex-col p-4 rounded-2xl border-2 bg-white cursor-pointer hover:bg-slate-50/50 transition-all select-none group border-blue-600 ring-1 ring-blue-600/10";
                effCardLabel.className = "relative flex flex-col p-4 rounded-2xl border-2 bg-white cursor-pointer hover:bg-slate-50/50 transition-all select-none group border-slate-200";
                
                techIconBox.className = "p-2 rounded-xl bg-blue-50 text-blue-700 group-hover:scale-105 transition-transform";
                effIconBox.className = "p-2 rounded-xl bg-slate-100 text-slate-600 group-hover:scale-105 transition-transform";
            } else if (track === 'effectiveness') {
                // 1. Swap Dropdown Sub-Sections
                techSection.classList.add('hidden');
                effSection.classList.remove('hidden');

                // 2. Clear out inputs in the technical compliance fields
                document.getElementById('technical_folder_id').value = "";

                // 3. Re-theme Action UI Components (Violet for Effectiveness Assessment)
                submitBtn.className = "inline-flex h-10 items-center justify-center rounded-xl bg-violet-600 px-5 text-xs font-black text-white transition hover:bg-violet-700 shadow-sm cursor-pointer whitespace-nowrap";
                techCardLabel.className = "relative flex flex-col p-4 rounded-2xl border-2 bg-white cursor-pointer hover:bg-slate-50/50 transition-all select-none group border-slate-200";
                effCardLabel.className = "relative flex flex-col p-4 rounded-2xl border-2 bg-white cursor-pointer hover:bg-slate-50/50 transition-all select-none group border-violet-600 ring-1 ring-violet-600/10";
                
                techIconBox.className = "p-2 rounded-xl bg-slate-100 text-slate-600 group-hover:scale-105 transition-transform";
                effIconBox.className = "p-2 rounded-xl bg-violet-50 text-violet-700 group-hover:scale-105 transition-transform";
                
                // Initialize sub outcome views based on active old values if loaded
                filterSubOutcomesByMainIO(document.getElementById('immediate_outcome_id').value);
            }
        }

        /**
         * Cascade Interlock Filter Node: Matches Sub-IO dropdown selections explicitly to parent Immediate Outcomes
         */
        function filterSubOutcomesByMainIO(mainIoId) {
            const subIoSelect = document.getElementById('effectiveness_sub_io_id');
            const options = subIoSelect.querySelectorAll('.sub-io-option');
            let matchedItemsCount = 0;

            // Reset sub outcome choice completely if parent changes
            subIoSelect.value = "";

            options.forEach(opt => {
                const parentTrackId = opt.getAttribute('data-io');
                if (parentTrackId === mainIoId) {
                    opt.style.display = "block";
                    matchedItemsCount++;
                } else {
                    opt.style.display = "none";
                }
            });
        }

        /**
         * Utility Multi-Tenant Helper: Instantly toggles entire collections of matching check-nodes
         */
        function toggleCheckboxGroup(className) {
            const checkboxes = document.querySelectorAll('.' + className);
            if (checkboxes.length === 0) return;
            
            // Base state decision string matching the state of the first item
            const masterState = !checkboxes[0].checked;
            checkboxes.forEach(cb => {
                cb.checked = masterState;
            });
        }

        // Initialize Workspace view state logic on initial rendering passes
        document.addEventListener('DOMContentLoaded', function() {
            const selectedTrack = document.querySelector('input[name="workspace_track"]:checked')?.value || 'technical';
            switchWorkspaceContext(selectedTrack);
        });
    </script>
</x-app-layout>