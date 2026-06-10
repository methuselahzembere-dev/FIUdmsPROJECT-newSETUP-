@props([
    'label',
    'value',
    'trend' => null,
    'tone' => 'indigo',
    'bgClass' => 'bg-white border-slate-200 text-slate-950 shadow', {{-- Default theme state --}}
])

@php
    $tones = [
        'indigo' => 'bg-indigo-50 text-indigo-700 ring-indigo-100',
        'emerald' => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
        'amber' => 'bg-amber-50 text-amber-700 ring-amber-100',
        'rose' => 'bg-rose-50 text-rose-700 ring-rose-100',
        'cyan' => 'bg-cyan-50 text-cyan-700 ring-cyan-100',
        'slate' => 'bg-slate-100 text-slate-700 ring-slate-200',
    ];
    $toneClass = $tones[$tone] ?? $tones['indigo'];
@endphp

{{-- 🌟 The incoming bgClass completely swaps out the default styles dynamically! --}}
<div {{ $attributes->merge(['class' => "rounded-[1.75rem] border p-5 transition hover:-translate-y-0.5 hover:shadow-md {$bgClass}"]) }}>
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-bold text-slate-500">{{ $label }}</p>
            <p class="mt-2 text-3xl font-black tracking-tight text-slate-950">{{ $value }}</p>
        </div>
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl ring-1 {{ $toneClass }}">
            {{ $icon ?? '' }}
        </div>
    </div>

    @if($trend)
        <p class="mt-4 text-xs font-semibold text-slate-500">{{ $trend }}</p>
    @endif
</div>