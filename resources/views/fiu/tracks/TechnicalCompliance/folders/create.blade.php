<x-app-layout>
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-black text-slate-900">Create Technical Compliance Folder</h1>
        <p class="mt-2 text-sm text-slate-600">Only FIU users can introduce new Technical Compliance folders when additional areas of interest are needed.</p>

        <form action="{{ route('fiu.technical-compliance.folders.store') }}" method="POST" class="mt-6 space-y-5">
            @csrf
            
            <div>
                <label for="name" class="text-sm font-bold text-slate-700">Folder name</label>
                <input id="name" name="name" value="{{ old('name') }}" required class="mt-2 w-full rounded-2xl border-slate-300 focus:border-blue-500 focus:ring-blue-500" />
                @error('name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="text-sm font-bold text-slate-700">Description</label>
                <textarea id="description" name="description" rows="4" class="mt-2 w-full rounded-2xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
            </div>

            <div>
                <p class="text-sm font-bold text-slate-700">Assign institutions</p>
                <p class="mt-1 text-xs text-slate-500">Leave blank to create the folder first and bind institutions later.</p>
                
                <div class="mt-3 grid gap-3 sm:grid-cols-2 max-h-60 overflow-y-auto pr-1">
                    @foreach($institutions as $institution)
                        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 p-3 hover:border-blue-200 transition-all cursor-pointer bg-slate-50/50 hover:bg-white">
                            <input type="checkbox" name="institution_ids[]" value="{{ $institution->id }}" @checked(collect(old('institution_ids', []))->contains($institution->id)) class="rounded text-blue-600 focus:ring-blue-500 border-slate-300">
                            <span>
                                <span class="block text-sm font-bold text-slate-800">{{ $institution->name }}</span>
                                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ $institution->code }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('fiu.technical-compliance.folders.index') }}" class="rounded-2xl border border-slate-300 px-5 py-2.5 text-sm font-black text-slate-700 hover:bg-slate-50 transition cursor-pointer">
                    Cancel
                </a>
                <button type="submit" class="rounded-2xl bg-blue-700 px-5 py-2.5 text-sm font-black text-white shadow-sm hover:bg-blue-800 transition cursor-pointer">
                    Create folder
                </button>
            </div>
        </form>
    </div>
</div>
</x-app-layout>