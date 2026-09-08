@props([
    'label',
    'value' => '0',
    'icon' => 'chart-bar',
    'color' => 'brand',
    'hint' => null,
])

@php
    $colors = [
        'brand'  => 'bg-brand-50 text-brand-600',
        'green'  => 'bg-green-50 text-green-600',
        'yellow' => 'bg-yellow-50 text-yellow-600',
        'red'    => 'bg-red-50 text-red-600',
        'blue'   => 'bg-blue-50 text-blue-600',
        'indigo' => 'bg-indigo-50 text-indigo-600',
        'gray'   => 'bg-gray-100 text-gray-600',
    ];
    $c = $colors[$color] ?? $colors['brand'];
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-4 rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200']) }}>
    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-lg {{ $c }}">
        <x-ui.icon :name="$icon" class="h-6 w-6" />
    </span>
    <div class="min-w-0">
        <p class="text-sm leading-tight text-gray-500">{{ $label }}</p>
        <p class="mt-0.5 text-2xl font-semibold text-gray-900">{{ $value }}</p>
        @if ($hint)
            <p class="text-xs text-gray-400">{{ $hint }}</p>
        @endif
    </div>
</div>
