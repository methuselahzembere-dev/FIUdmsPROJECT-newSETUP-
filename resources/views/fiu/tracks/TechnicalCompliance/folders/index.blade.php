<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900">Technical Compliance folders</h1>
                <p class="mt-2 text-sm text-slate-600">FIU manages the default folder structure and can create new areas without mixing documents across folders.</p>
            </div>
            
            {{-- 🌟 CHANGED: Swapped from <a> tag link to a true click button for modal invocation --}}
            <button onclick="document.getElementById('createFolderModal').classList.remove('hidden')" class="rounded-2xl bg-blue-700 px-4 py-2.5 text-sm font-black text-white hover:bg-blue-800 transition">
                New folder
            </button>-
        </div>

        {{-- 🌟 ADDED: Clean modal popup component wrapper tied to your POST endpoint --}}
        <div id="createFolderModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="w-full max-w-md rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                    <h2 class="text-lg font-black text-slate-900">Create New Workspace Folder</h2>
                    <button type="button" onclick="document.getElementById('createFolderModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold text-sm">✕</button>
                </div>
                
                <form method="POST" action="{{ route('fiu.technical-compliance.folders.store-folder') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Folder Name</label>
                        <input type="text" name="name" required placeholder="e.g., Cross-Border Directives" class="w-full rounded-xl border border-slate-200 p-3 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Description (Optional)</label>
                        <textarea name="description" rows="3" placeholder="Define tracking scope limits..." class="w-full rounded-xl border border-slate-200 p-3 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none font-medium"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" onclick="document.getElementById('createFolderModal').classList.add('hidden')" class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-black uppercase text-slate-600 hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="rounded-xl bg-blue-700 px-4 py-2.5 text-xs font-black uppercase text-white hover:bg-blue-800">Generate Folder</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach($folders as $folder)
                <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-black text-slate-900">{{ $folder->name }}</h2>
                            <p class="mt-2 text-sm text-slate-600">{{ $folder->description ?: 'No description provided.' }}</p>
                        </div>
                      @if(isset($folder->is_default) && $folder->is_default)
    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-700">Default</span>
@endif
                    </div>

                   <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
        <div class="rounded-2xl bg-slate-50 p-3">
              <dt class="text-slate-500">Documents</dt>
              {{-- 🌟 FIXED: Safe fallback check if property doesn't exist on raw DB row --}}
            <dd class="mt-1 text-lg font-black text-slate-900">
            {{ $folder->documents_count ?? 0 }}
              </dd>
         </div>

          <div class="rounded-2xl bg-slate-50 p-3">
             <dt class="text-slate-500">Institutions</dt>
           {{-- 🌟 FIXED: Safely verify type before counting to prevent integer TypeErrors --}}
              <dd class="mt-1 text-lg font-black text-slate-900">
            {{ is_countable($folder->institutions ?? null) ? count($folder->institutions) : ($folder->institutions ?? 0) }}
              </dd>
           </div>
       </dl>

   <div class="mt-4 flex flex-wrap gap-2">
    {{-- 🌟 FIXED: Verify $folder->institutions is iterable before trying to pass it to a loop --}}
    @if(is_iterable($folder->institutions ?? null))
        @forelse($folder->institutions as $institution)
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">
                {{ $institution->code }}
            </span>
        @empty
            <span class="text-xs font-semibold text-amber-700">Visible after institution assignment.</span>
        @endforelse
    @else
        {{-- 🌟 FALLBACK: If it's an integer or missing, safely show the fallback text --}}
        <span class="text-xs font-semibold text-amber-700">Visible after institution assignment.</span>
    @endif
</div>
</article>
            @endforeach
            </div>   

        @if(method_exists($folders, 'links'))
            <div class="mt-6">
                {{ $folders->links() }}
            </div>
        @endif
    </div>
</x-app-layout>