@props([
    'title',
    'description' => null,
    'count' => 0,
    'href' => '#',
    'tone' => 'indigo',
])

@php
    $tones = [
        'indigo' => 'from-indigo-700 to-indigo-500 shadow-indigo-700/20',
        'cyan' => 'from-cyan-700 to-cyan-500 shadow-cyan-700/20',
        'emerald' => 'from-emerald-700 to-emerald-500 shadow-emerald-700/20',
        'amber' => 'from-amber-600 to-orange-500 shadow-amber-700/20',
    ];
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'group block overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md']) }}>
    <div class="bg-gradient-to-br {{ $tones[$tone] ?? $tones['indigo'] }} p-5 text-white shadow-lg">
        <div class="flex items-center justify-between gap-4">
            <h3 class="text-lg font-black">{{ $title }}</h3>
            <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-black">{{ $count }} items</span>
        </div>
        @if($description)
            <p class="mt-2 text-sm leading-6 text-white/80">{{ $description }}</p>
        @endif
    </div>
    <div class="flex items-center justify-between p-5 text-sm font-bold text-slate-700">
        <span>Open workspace</span>
        <span class="transition group-hover:translate-x-1">&rarr;</span>
    </div>
</a>

