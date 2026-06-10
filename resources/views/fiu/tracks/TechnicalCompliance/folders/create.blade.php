@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <h1 class="text-2xl font-black text-slate-900">Create Technical Compliance folder</h1>
    <p class="mt-2 text-sm text-slate-600">Only FIU users can introduce new Technical Compliance folders when additional areas of interest are needed.</p>

    <form action="{{ route('fiu.technical-compliance.folders.store') }}" method="POST" class="mt-6 space-y-5">
        @csrf
        <div>
            <label for="name" class="text-sm font-bold text-slate-700">Folder name</label>
            <input id="name" name="name" value="{{ old('name') }}" required class="mt-2 w-full rounded-2xl border-slate-300" />
            @error('name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="description" class="text-sm font-bold text-slate-700">Description</label>
            <textarea id="description" name="description" rows="4" class="mt-2 w-full rounded-2xl border-slate-300">{{ old('description') }}</textarea>
        </div>

        <div>
            <p class="text-sm font-bold text-slate-700">Assign institutions</p>
            <p class="mt-1 text-xs text-slate-500">Leave blank to create the folder first and bind institutions later.</p>
            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                @foreach($institutions as $institution)
                    <label class="flex items-center gap-3 rounded-2xl border border-slate-200 p-3">
                        <input type="checkbox" name="institution_ids[]" value="{{ $institution->id }}" @checked(collect(old('institution_ids', []))->contains($institution->id))>
                        <span>
                            <span class="block text-sm font-bold text-slate-800">{{ $institution->name }}</span>
                            <span class="text-xs text-slate-500">{{ $institution->code }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('fiu.technical-compliance.folders.index') }}" class="rounded-2xl border border-slate-300 px-4 py-2.5 text-sm font-black text-slate-700">Cancel</a>
            <button type="submit" class="rounded-2xl bg-blue-700 px-4 py-2.5 text-sm font-black text-white">Create folder</button>
        </div>
    </form>
</div>
@endsection
