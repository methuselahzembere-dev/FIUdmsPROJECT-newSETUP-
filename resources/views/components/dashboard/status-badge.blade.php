@props([
    'status' => 'draft',
])

@php
    $normalized = strtolower(str_replace([' ', '_'], '-', $status));
    $classes = match ($normalized) {
        'approved', 'accepted', 'complete', 'compliant' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'submitted', 'under-review', 'in-review' => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
        'changes-requested', 'revision-required', 'returned' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'rejected', 'overdue', 'non-compliant' => 'bg-rose-50 text-rose-700 ring-rose-200',
        default => 'bg-slate-100 text-slate-700 ring-slate-200',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-1 text-xs font-black capitalize ring-1 $classes"]) }}>
    {{ str_replace('-', ' ', $normalized) }}
</span>

