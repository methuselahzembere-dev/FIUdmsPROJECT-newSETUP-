<x-app-layout>
<div class="space-y-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between gap-4 border-b border-slate-100 pb-5">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">Technical Compliance Folders</h1>
            <p class="mt-2 text-sm text-slate-600">FIU manages the default folder structure and can create new areas without mixing documents across folders.</p>
        </div>
        <a href="{{ route('fiu.technical-compliance.folders.create') }}" class="rounded-2xl bg-blue-700 px-5 py-2.5 text-sm font-black text-white shadow-sm hover:bg-blue-800 transition cursor-pointer whitespace-nowrap">
            New folder
        </a>
    </div>

    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        @foreach($folders as $folder)
            <article class="transform rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-blue-300 flex flex-col justify-between min-h-[15rem]">
                <div>
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-black text-slate-900">{{ $folder->name }}</h2>
                            <p class="mt-2 text-sm text-slate-600 line-clamp-3">{{ $folder->description ?: 'No description provided.' }}</p>
                        </div>
                        
                        @if(!empty($folder->is_default))
                            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-700 whitespace-nowrap">Default</span>
                        @endif
                    </div>
                </div>

                <div>
                    <dl class="mt-5 grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-2xl bg-slate-50 p-3 border border-slate-100">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Documents</dt>
                            <dd class="mt-1 text-xl font-black text-slate-900">{{ $folder->documents_count }}</dd>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-3 border border-slate-100">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Institutions</dt>
                            <dd class="mt-1 text-xl font-black text-slate-900">
                                {{ $folder->institutions ? 1 : 0 }}
                            </dd>
                        </div>
                    </dl>

                    <div class="mt-4 flex flex-wrap gap-2">
                        @if($folder->institutions)
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700 border border-slate-200/60">
                                {{ $folder->institutions->code }}
                            </span>
                        @else
                            <span class="rounded-full bg-violet-50 px-3 py-1 text-xs font-bold text-violet-700 border border-violet-100">
                                Global Master Folder
                            </span>
                        @endif
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <div class="mt-8 pt-4 border-t border-slate-100">
        {{ $folders->links() }}
    </div>
</div>
</x-app-layout>