@props([
    'title',
    'description' => null,
    'time' => null,
    'tone' => 'indigo',
])

@php
    $dot = [
        'indigo' => 'bg-indigo-600 ring-indigo-100',
        'emerald' => 'bg-emerald-600 ring-emerald-100',
        'amber' => 'bg-amber-500 ring-amber-100',
        'rose' => 'bg-rose-600 ring-rose-100',
        'slate' => 'bg-slate-500 ring-slate-100',
    ][$tone] ?? 'bg-indigo-600 ring-indigo-100';
@endphp

<div {{ $attributes->merge(['class' => 'relative flex gap-3']) }}>
    <span class="mt-1.5 flex h-3 w-3 shrink-0 rounded-full ring-4 {{ $dot }}"></span>
    <div class="min-w-0 flex-1 pb-5">
        <div class="flex items-start justify-between gap-3">
            <p class="text-sm font-black text-slate-900">{{ $title }}</p>
            @if($time)
                <time class="shrink-0 text-xs font-semibold text-slate-400">{{ $time }}</time>
            @endif
        </div>
        @if($description)
            <p class="mt-1 text-sm leading-6 text-slate-600">{{ $description }}</p>
        @endif
    </div>
</div>

