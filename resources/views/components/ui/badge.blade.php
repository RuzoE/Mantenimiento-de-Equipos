@props(['color' => 'gray'])

@php
    $colors = [
        'gray'   => 'bg-gray-100 text-gray-700 ring-gray-500/10',
        'green'  => 'bg-green-100 text-green-700 ring-green-600/20',
        'yellow' => 'bg-yellow-100 text-yellow-800 ring-yellow-600/20',
        'red'    => 'bg-red-100 text-red-700 ring-red-600/20',
        'blue'   => 'bg-blue-100 text-blue-700 ring-blue-700/10',
        'brand'  => 'bg-brand-100 text-brand-700 ring-brand-700/10',
        'indigo' => 'bg-indigo-100 text-indigo-700 ring-indigo-700/10',
    ];
    $c = $colors[$color] ?? $colors['gray'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset {$c}"]) }}>
    {{ $slot }}
</span>
