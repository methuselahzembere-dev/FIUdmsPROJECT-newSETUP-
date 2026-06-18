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

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h1 class="text-2xl font-black text-slate-900">Create Technical Compliance Folder</h1>
            <p class="mt-2 text-sm text-slate-600">Only FIU users can introduce new Technical Compliance folders when additional areas of interest are needed.</p>

            <form action="{{ route('fiu.technical-compliance.folders.store') }}" method="POST" class="space-y-6 mt-6">
                @csrf

                <div id="folderAttributesCard" class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xs transition-all duration-500">
                    <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                        <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">📁 Create Compliance Folder Structure</h2>
                    </div>

                    <div class="space-y-6 p-6">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-1.5 sm:col-span-2">
                                <label for="name" class="block text-xs font-black uppercase tracking-wider text-slate-700">Folder Name</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/40 p-2.5 text-sm font-medium outline-none focus:border-slate-800 focus:bg-white transition-all" placeholder="Enter folder name e.g Acts, Statutory Laws">
                                @error('name') <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-2 space-y-2">
                                <label class="block text-xs font-black uppercase tracking-wider text-slate-700">Folder Security Isolation Scope</label>
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mt-2">
                                    
                                    <label id="folder-label-scope-shared" class="relative flex items-start gap-4 p-5 rounded-2xl border-2 cursor-pointer transition-all duration-300 select-none group border-slate-200 bg-white text-slate-700 shadow-2xs">
                                        <input type="radio" name="visibility_scope" value="shared" class="text-blue-600 focus:ring-blue-400 h-4 w-4 mt-0.5" onchange="handleFolderScopeChange('shared')" @checked(old('visibility_scope', 'shared') === 'shared')>
                                        <div class="min-w-0 flex-1">
                                            <span id="folder-title-scope-shared" class="block text-sm font-black text-slate-800 uppercase tracking-wide group-hover:text-blue-900 transition-colors">👥 Shared / Institutional Asset</span>
                                            <span id="folder-desc-scope-shared" class="block text-xs font-medium text-slate-400 mt-1 leading-relaxed transition-colors">Visible to target institutions assigned down in the directory map matrix.</span>
                                        </div>
                                    </label>

                                    <label id="folder-label-scope-internal" class="relative flex items-start gap-4 p-5 rounded-2xl border-2 cursor-pointer transition-all duration-300 select-none group border-slate-200 bg-white text-slate-700 shadow-2xs">
                                        <input type="radio" name="visibility_scope" value="fiu-private" class="text-red-600 focus:ring-red-400 h-4 w-4 mt-0.5" onchange="handleFolderScopeChange('fiu-private')" @checked(old('visibility_scope') === 'fiu-private')>
                                        <div class="min-w-0 flex-1">
                                            <span id="folder-title-scope-internal" class="block text-sm font-black text-slate-800 uppercase tracking-wide group-hover:text-rose-700 transition-colors">🔒 Internal Audit Only (FIU Private)</span>
                                            <span id="folder-desc-scope-internal" class="block text-xs font-semibold text-slate-400 mt-1 leading-relaxed transition-colors">100% hidden from external visibility maps, visible to internal FIU users only.</span>
                                        </div>
                                    </label>
                                    
                                </div>
                                @error('visibility_scope') <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1.5 sm:col-span-2">
                                <label for="description" class="block text-xs font-black uppercase tracking-wider text-slate-700">Description</label>
                                <textarea id="description" name="description" rows="3" class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/40 p-2.5 text-sm font-medium outline-none focus:border-slate-800 focus:bg-white transition-all" placeholder="Enter folder description Context">{{ old('description') }}</textarea>
                                @error('description') <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div id="section-institution-mapping" class="space-y-3 pt-4 border-t border-slate-100 transition-all duration-300">
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="block text-xs font-black uppercase tracking-wider text-slate-700">Assign To Specific Institution Access (Optional)</label>
                                    <p class="text-xxs text-slate-500 mt-0.5">Leave blank to make this compliance directory a global asset across all institutions.</p>
                                </div>
                                <button type="button" onclick="toggleAllInstitutions()" class="text-[10px] font-extrabold text-blue-600 hover:text-blue-800 uppercase tracking-wide cursor-pointer select-none bg-blue-50 px-2.5 py-1 rounded-md transition-colors hover:bg-blue-100/70">
                                    Toggle All Listings
                                </button>
                            </div>

                            <div class="max-h-60 overflow-y-auto p-3 bg-slate-50/40 rounded-2xl border border-slate-200/60 grid gap-2.5 sm:grid-cols-2">
                                @foreach($institutions as $inst)
                                    <label class="institution-item-label flex items-start gap-3 p-3 rounded-xl bg-white border border-slate-200/60 shadow-2xs hover:bg-slate-50 transition-all cursor-pointer select-none group">
                                        <input type="checkbox" name="institution_ids[]" value="{{ $inst->id }}" class="institution-cb rounded border-slate-300 text-blue-600 focus:ring-blue-500 mt-0.5 h-4 w-4" @checked(is_array(old('institution_ids')) && in_array($inst->id, old('institution_ids')))>
                                        <div class="min-w-0 flex-1">
                                            <span class="block text-xs font-bold text-slate-700 group-hover:text-blue-900 transition-colors truncate">{{ $inst->name }}</span>
                                            <span class="block text-[10px] font-medium text-slate-400 mt-0.5 tracking-wider uppercase">Tenant Code: {{ $inst->code }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>

                <div class="flex items-center justify-between border-t border-slate-100 pt-5">
                    <p class="text-xs text-slate-500 max-w-sm">This newly generated folder structure registers instantly into your chosen data stream scopes.</p>
                    <div class="flex gap-2">
                        <a href="{{ route('fiu.technical-compliance.folders.index') }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-300 px-5 text-xs font-black text-slate-700 transition hover:bg-slate-50 shadow-xs">Cancel</a>
                        <button type="submit" id="submitButtonWidget" class="inline-flex h-11 items-center justify-center rounded-xl bg-blue-700 px-6 text-xs font-black text-white transition hover:bg-blue-800 shadow-sm cursor-pointer whitespace-nowrap">Save Folder Asset</button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <script>
        let currentActiveScope = 'shared';

        function handleFolderScopeChange(scope) {
            currentActiveScope = scope;

            const sharedLabel = document.getElementById('folder-label-scope-shared');
            const internalLabel = document.getElementById('folder-label-scope-internal');
            const sharedTitle = document.getElementById('folder-title-scope-shared');
            const sharedDesc = document.getElementById('folder-desc-scope-shared');
            const internalTitle = document.getElementById('folder-title-scope-internal');
            const internalDesc = document.getElementById('folder-desc-scope-internal');

            const attributesCard = document.getElementById('folderAttributesCard');
            const submitBtn = document.getElementById('submitButtonWidget');
            const instSection = document.getElementById('section-institution-mapping');

            if (!sharedLabel || !internalLabel) return;

            if (scope === 'shared') {
                // High Density Blue Form Theme classes
                sharedLabel.className = "relative flex items-start gap-4 p-5 rounded-2xl border-2 bg-blue-600/10 border-blue-600 text-blue-900 cursor-pointer transition-all duration-300 select-none group shadow-xs ring-4 ring-blue-500/10 animate-attention-breath";
                sharedTitle.className = "block text-sm font-black text-blue-900 uppercase tracking-wide";
                sharedDesc.className = "block text-xs font-semibold text-blue-700/80 mt-1 leading-relaxed";

                internalLabel.className = "relative flex items-start gap-4 p-5 rounded-2xl border-2 bg-white border-slate-200 text-slate-700 cursor-pointer hover:bg-slate-50/50 transition-all duration-300 select-none group shadow-2xs";
                internalTitle.className = "block text-sm font-black text-slate-800 uppercase tracking-wide group-hover:text-rose-700 transition-colors";
                internalDesc.className = "block text-xs font-medium text-slate-400 mt-1 leading-relaxed";

                if (attributesCard) attributesCard.className = "overflow-hidden rounded-3xl border border-blue-300 bg-blue-50/50 shadow-xs transition-all duration-500";
                if (submitBtn) submitBtn.className = "inline-flex h-11 items-center justify-center rounded-xl bg-blue-700 px-6 text-xs font-black text-white transition hover:bg-blue-800 shadow-sm cursor-pointer whitespace-nowrap";

                if (instSection) instSection.style.setProperty('display', 'block', 'important');
            } else {
                // High Density Red Form Theme classes
                sharedLabel.className = "relative flex items-start gap-4 p-5 rounded-2xl border-2 bg-white border-slate-200 text-slate-700 cursor-pointer hover:bg-slate-50/50 transition-all duration-300 select-none group shadow-2xs";
                sharedTitle.className = "block text-sm font-black text-slate-800 uppercase tracking-wide group-hover:text-blue-900 transition-colors";
                sharedDesc.className = "block text-xs font-medium text-slate-400 mt-1 leading-relaxed";

                internalLabel.className = "relative flex items-start gap-4 p-5 rounded-2xl border-2 bg-rose-600/10 border-rose-600 text-rose-900 cursor-pointer transition-all duration-300 select-none group shadow-xs ring-4 ring-rose-500/10 animate-attention-breath";
                internalTitle.className = "block text-sm font-black text-rose-900 uppercase tracking-wide";
                internalDesc.className = "block text-xs font-semibold text-rose-700/80 mt-1 leading-relaxed";

                if (attributesCard) attributesCard.className = "overflow-hidden rounded-3xl border border-rose-300 bg-rose-50/50 shadow-xs transition-all duration-500";
                if (submitBtn) submitBtn.className = "inline-flex h-11 items-center justify-center rounded-xl bg-rose-600 px-6 text-xs font-black text-white transition hover:bg-rose-700 shadow-sm cursor-pointer whitespace-nowrap";

                if (instSection) instSection.style.setProperty('display', 'none', 'important');
                
                document.querySelectorAll('.institution-cb').forEach(cb => {
                    cb.checked = false;
                    updateInstitutionRowHighlight(cb);
                });
            }
        }

        function toggleAllInstitutions() {
            const checkboxes = document.querySelectorAll('.institution-cb');
            if (checkboxes.length === 0) return;
            const targetState = !checkboxes[0].checked;
            checkboxes.forEach(cb => {
                cb.checked = targetState;
                updateInstitutionRowHighlight(cb);
            });
        }

        function updateInstitutionRowHighlight(checkbox) {
            const wrapperLabel = checkbox.closest('.institution-item-label');
            if (!wrapperLabel) return;

            if (checkbox.checked) {
                wrapperLabel.classList.remove('bg-white', 'border-slate-200/60');
                wrapperLabel.classList.add('bg-blue-600/10', 'border-blue-500', 'ring-1', 'ring-blue-100');
            } else {
                wrapperLabel.classList.remove('bg-blue-600/10', 'border-blue-500', 'ring-1', 'ring-blue-100');
                wrapperLabel.classList.add('bg-white', 'border-slate-200/60');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const selectedScope = document.querySelector('input[name="visibility_scope"]:checked')?.value || 'shared';
            handleFolderScopeChange(selectedScope);

            document.querySelectorAll('.institution-cb').forEach(cb => {
                updateInstitutionRowHighlight(cb);
                cb.addEventListener('change', function() {
                    updateInstitutionRowHighlight(this);
                });
            });
        });
    </script>
</x-app-layout>