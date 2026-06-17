<x-app-layout>
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-black text-slate-900">Create Technical Compliance Folder</h1>
        <p class="mt-2 text-sm text-slate-600">Only FIU users can introduce new Technical Compliance folders when additional areas of interest are needed.</p>

     <form action="{{ route('fiu.technical-compliance.folders.store') }}" method="POST" class="space-y-6">
    @csrf

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xs">
        <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
            <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">📁 Create Compliance Folder Structure</h2>
        </div>

        <div class="space-y-6 p-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-1.5">
                    <label for="name" class="block text-xs font-black uppercase tracking-wider text-slate-700">Folder Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="e.g., AML/CFT Directives" required
                           class="block w-full rounded-xl border border-slate-200 bg-slate-50/40 p-2.5 text-sm font-medium outline-none focus:border-blue-600 focus:bg-white transition-all">
                    @error('name') <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="description" class="block text-xs font-black uppercase tracking-wider text-slate-700">Brief Description</label>
                    <input type="text" id="description" name="description" value="{{ old('description') }}" placeholder="Describe the purpose of this data node..."
                           class="block w-full rounded-xl border border-slate-200 bg-slate-50/40 p-2.5 text-sm font-medium outline-none focus:border-blue-600 focus:bg-white transition-all">
                    @error('description') <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <style>
    @keyframes subtle-breath {
        0%, 100% { transform: scale(1); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        50% { transform: scale(1.015); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08); }
    }
    .animate-attention-breath {
        animation: subtle-breath 2.5s infinite ease-in-out;
    }
</style>

    <div class="space-y-2">
    <label class="block text-xs font-black uppercase tracking-wider text-slate-700">Folder Security Isolation Level</label>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        
        <label id="label-scope-shared" class="relative flex items-start gap-3.5 p-5 rounded-2xl border-2 bg-white cursor-pointer transition-all duration-300 select-none group border-slate-200 shadow-2xs">
            <input type="radio" name="visibility_scope" value="shared" class="text-blue-600 focus:ring-blue-400 h-4 w-4 mt-0.5 transition-transform group-hover:scale-110" 
                   onchange="handleFolderScopeChange('shared')" @checked(old('visibility_scope', 'shared') === 'shared')>
            <div class="min-w-0 flex-1">
                <span id="title-scope-shared" class="block text-xs font-black text-slate-800 uppercase tracking-wide transition-colors duration-300">👥 Shared / Institutional Workspace</span>
                <span id="desc-scope-shared" class="block text-[11px] font-semibold text-slate-400 mt-1.5 leading-normal transition-colors duration-300">Visible to selected institutions. Tenant operators can submit, manage, and view assigned documents here.</span>
            </div>
        </label>

        <label id="label-scope-private" class="relative flex items-start gap-3.5 p-5 rounded-2xl border-2 bg-white cursor-pointer transition-all duration-300 select-none group border-slate-200 shadow-2xs">
            <input type="radio" name="visibility_scope" value="fiu-private" class="text-red-600 focus:ring-red-400 h-4 w-4 mt-0.5 transition-transform group-hover:scale-110" 
                   onchange="handleFolderScopeChange('fiu-private')" @checked(old('visibility_scope') === 'fiu-private')>
            <div class="min-w-0 flex-1">
                <span id="title-scope-private" class="block text-xs font-black text-slate-800 uppercase tracking-wide transition-colors duration-300">🔒 FIU Confidential Sandbox</span>
                <span id="desc-scope-private" class="block text-[11px] font-semibold text-slate-400 mt-1.5 leading-normal transition-colors duration-300">100% sandboxed. Hidden from all external tenant structures. Visible and accessible exclusively to FIU internal staff profiles.</span>
            </div>
        </label>
        
    </div>
    @error('visibility_scope') <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p> @enderror
</div>

            <div id="institution-checkboxes-wrapper" class="space-y-3 transition-all duration-300">
                <div class="flex items-center justify-between pb-1">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-slate-700">Assign Target Institutions</label>
                        <p class="text-xxs text-slate-500 mt-0.5">Check one or more institutions to build independent folder replicas for each chosen tenant. Leave blank for a Global Master Folder.</p>
                    </div>
                    <button type="button" onclick="toggleAllInstitutions()" 
                            class="text-[10px] font-extrabold text-blue-600 hover:text-blue-800 uppercase tracking-wide cursor-pointer select-none bg-blue-50 px-2.5 py-1 rounded-md transition-colors hover:bg-blue-100/70">
                        Toggle All
                    </button>
                </div>

                <div class="max-h-56 overflow-y-auto p-3 bg-slate-50/40 rounded-2xl border border-slate-200/60 grid gap-2 sm:grid-cols-2 md:grid-cols-3">
                    @foreach($institutions ?? [] as $inst)
                        <label class="institution-item-label flex items-start gap-3 p-3 rounded-xl bg-white border border-slate-200/60 shadow-2xs hover:bg-slate-50 transition-all cursor-pointer select-none group">
                            <input type="checkbox" name="institution_ids[]" value="{{ $inst->id }}" 
                                   class="institution-cb rounded border-slate-300 text-blue-600 focus:ring-blue-500 mt-0.5 h-4 w-4 transition-transform group-hover:scale-105"
                                   @checked(is_array(old('institution_ids')) && in_array($inst->id, old('institution_ids')))>
                            <span class="text-xs font-bold text-slate-700 group-hover:text-blue-900 transition-colors truncate">{{ $inst->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

        </div>

        <div class="flex items-center justify-end gap-3 border-t border-slate-100 bg-slate-50/50 px-6 py-4">
            <a href="{{ route('fiu.technical-compliance.folders.index') }}" 
               class="inline-flex h-9 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-xs font-black text-slate-700 transition hover:bg-slate-50">
                Cancel
            </a>
            <button type="submit" 
                    class="inline-flex h-9 items-center justify-center rounded-xl bg-blue-700 px-5 text-xs font-black text-white transition hover:bg-blue-800 shadow-sm cursor-pointer">
                Save Folder Config
            </button>
        </div>
    </div>


    </form>

    <script>
        /**
         * Intercepts scope changes and orchestrates front-end UI transitions
         */
    /**
     * Intercepts scope changes and orchestrates rich background color transitions and attention animations
     */
    function handleFolderScopeChange(scope) {
        const wrapper = document.getElementById('institution-checkboxes-wrapper');
        const sharedLabel = document.getElementById('label-scope-shared');
        const privateLabel = document.getElementById('label-scope-private');
        
        const sharedTitle = document.getElementById('title-scope-shared');
        const sharedDesc = document.getElementById('desc-scope-shared');
        
        const privateTitle = document.getElementById('title-scope-private');
        const privateDesc = document.getElementById('desc-scope-private');

        if (scope === 'shared') {
            // 1. Reveal institutional checklist elements
            wrapper.classList.remove('hidden');
            
            // 2. Activate Blue Flash Background Matrix on Shared Label
            sharedLabel.className = "relative flex items-start gap-3.5 p-5 rounded-2xl border-2 bg-blue-600 border-blue-700 text-white cursor-pointer transition-all duration-300 select-none group shadow-md scale-102 ring-4 ring-blue-500/20 animate-attention-breath";
            sharedTitle.className = "block text-xs font-black text-white uppercase tracking-wide";
            sharedDesc.className = "block text-[11px] font-medium text-blue-100 mt-1.5 leading-normal";

            // 3. Reset Private Label back to Clean Neutral Slate
            privateLabel.className = "relative flex items-start gap-3.5 p-5 rounded-2xl border-2 bg-white border-slate-200 text-slate-700 cursor-pointer hover:bg-slate-50 transition-all duration-300 select-none group shadow-2xs";
            privateTitle.className = "block text-xs font-black text-slate-800 uppercase tracking-wide group-hover:text-red-600 transition-colors";
            privateDesc.className = "block text-[11px] font-semibold text-slate-400 mt-1.5 leading-normal";
        } else {
            // 1. Tuck away multi-tenant checkboxes securely
            wrapper.classList.add('hidden');
            
            // 2. Flush selection data payload array values inside hidden context
            wrapper.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                cb.checked = false;
                updateInstitutionRowHighlight(cb);
            });

            // 3. Reset Shared Label back to Clean Neutral Slate
            sharedLabel.className = "relative flex items-start gap-3.5 p-5 rounded-2xl border-2 bg-white border-slate-200 text-slate-700 cursor-pointer hover:bg-slate-50 transition-all duration-300 select-none group shadow-2xs";
            sharedTitle.className = "block text-xs font-black text-slate-800 uppercase tracking-wide group-hover:text-blue-600 transition-colors";
            sharedDesc.className = "block text-[11px] font-semibold text-slate-400 mt-1.5 leading-normal";

            // 4. Activate Red Alert Background Matrix on Confidential Label
            privateLabel.className = "relative flex items-start gap-3.5 p-5 rounded-2xl border-2 bg-red-600 border-red-700 text-white cursor-pointer transition-all duration-300 select-none group shadow-md scale-102 ring-4 ring-red-500/20 animate-attention-breath";
            privateTitle.className = "block text-xs font-black text-white uppercase tracking-wide";
            privateDesc.className = "block text-[11px] font-medium text-red-100 mt-1.5 leading-normal";
        }
    }

        /**
         * Simple Toggle utility mapping all institution options concurrently
         */
        function toggleAllInstitutions() {
            const checkboxes = document.querySelectorAll('.institution-cb');
            if (checkboxes.length === 0) return;

            const targetState = !checkboxes[0].checked;
            checkboxes.forEach(cb => {
                cb.checked = targetState;
                updateInstitutionRowHighlight(cb);
            });
        }

        /**
         * Dynamically shifts Tailwind styles onto checkbox label wrappers when toggled
         */
        function updateInstitutionRowHighlight(checkbox) {
            const wrapperLabel = checkbox.closest('.institution-item-label');
            if (!wrapperLabel) return;

            if (checkbox.checked) {
                wrapperLabel.classList.remove('bg-white', 'border-slate-200/60');
                wrapperLabel.classList.add('bg-blue-50/70', 'border-blue-400', 'ring-1', 'ring-blue-100');
            } else {
                wrapperLabel.classList.remove('bg-blue-50/70', 'border-blue-400', 'ring-1', 'ring-blue-100');
                wrapperLabel.classList.add('bg-white', 'border-slate-200/60');
            }
        }

        // Process page-load configurations (Crucial for validation fallbacks tracking old values)
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
    </div>
</div>
</x-app-layout>