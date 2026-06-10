@props([
    'href' => '#',
    'active' => false,
    'icon' => null,
    'badge' => null,
])

<a
    href="{{ $href }}"
    {{ $attributes->merge([
        'class' => ($active
            ? 'bg-indigo-700 text-white shadow-lg shadow-indigo-700/20'
            : 'text-slate-700 hover:bg-slate-100 hover:text-slate-950') . ' group flex items-center justify-between rounded-2xl px-3 py-2.5 text-sm font-bold transition'
    ]) }}
>
    <span class="flex min-w-0 items-center gap-3">
        @if($icon)
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $active ? 'bg-white/15 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-white group-hover:text-indigo-700' }}">
                {!! $icon !!}
            </span>
        @endif
        <span class="truncate">{{ $slot }}</span>
    </span>

    @if($badge !== null)
        <span class="ml-3 rounded-full {{ $active ? 'bg-white/15 text-white' : 'bg-slate-200 text-slate-700' }} px-2 py-0.5 text-[11px] font-black">{{ $badge }}</span>
    @endif
</a>

