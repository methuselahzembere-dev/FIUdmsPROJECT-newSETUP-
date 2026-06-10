<x-app-layout>

@section('content')
<div class="space-y-6">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-sm font-black uppercase tracking-[0.2em] text-blue-700">Technical Compliance Folder</p>
                <h1 class="mt-2 text-2xl font-black text-slate-900">{{ $folder->name }}</h1>
                <p class="mt-2 max-w-3xl text-sm text-slate-600">{{ $folder->description ?: 'Documents in this folder stay isolated from every other Technical Compliance folder.' }}</p>
            </div>
            <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">
                Created by <span class="font-black text-slate-900">{{ $folder->creator?->name ?? 'System Seeder' }}</span>
            </div>
        </div>

        <div class="mt-5 flex flex-wrap gap-2">
            @foreach($folder->institutions as $institution)
                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-700">{{ $institution->name }}</span>
            @endforeach
        </div>
    </div>

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-4">
            <h2 class="text-lg font-black text-slate-900">Bound documents</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Title</th>
                        <th class="px-6 py-3 text-left">Institution</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Uploaded by</th>
                        <th class="px-6 py-3 text-left">Submitted</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($documents as $document)
                        <tr>
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $document->title }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $document->institution->name }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ str($document->status)->replace('_', ' ')->title() }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $document->uploader->name }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ optional($document->submitted_at)->format('d M Y H:i') ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-slate-500">No documents have been uploaded into this folder yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
     {{-- After (Line 56) 🌟 FIXED: Safe structural wrapper check --}}
@if($documents instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
        {{ $documents->links() }}
    </div>
@endif
    </div>
</div>
</x-app-layout>
