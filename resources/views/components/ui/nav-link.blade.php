@props([
    'href' => null,
    'active' => false,
    'icon' => null,
    'disabled' => false,
])

@php
    $base = 'group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition';
    $state = $active
        ? 'bg-brand-50 text-brand-700'
        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900';
@endphp

@if ($disabled)
    <span class="{{ $base }} cursor-not-allowed text-gray-400" aria-disabled="true">
        @if ($icon)
            <x-ui.icon :name="$icon" class="h-5 w-5 shrink-0 text-gray-300" />
        @endif
        <span class="flex-1">{{ $slot }}</span>
        <span class="rounded bg-gray-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-gray-400">Pronto</span>
    </span>
@else
    <a href="{{ $href }}" @if ($active) aria-current="page" @endif {{ $attributes->merge(['class' => $base . ' ' . $state]) }}>
        @if ($icon)
            <x-ui.icon :name="$icon"
                class="h-5 w-5 shrink-0 {{ $active ? 'text-brand-600' : 'text-gray-400 group-hover:text-gray-500' }}" />
        @endif
        <span class="flex-1">{{ $slot }}</span>
    </a>
@endif
