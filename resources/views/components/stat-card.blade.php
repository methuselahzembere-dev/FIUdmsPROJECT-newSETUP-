@props(['label', 'value', 'accent' => 'sky'])

<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
    <p class="mt-3 text-3xl font-bold text-{{ $accent }}-600">{{ $value }}</p>
</div>
