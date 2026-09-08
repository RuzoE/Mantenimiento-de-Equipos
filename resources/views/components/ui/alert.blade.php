@props(['type' => 'info'])

@php
    $styles = [
        'success' => ['wrap' => 'bg-green-50 text-green-800 ring-green-200', 'icon' => 'check',    'iconColor' => 'text-green-500'],
        'error'   => ['wrap' => 'bg-red-50 text-red-800 ring-red-200',       'icon' => 'ban',      'iconColor' => 'text-red-500'],
        'warning' => ['wrap' => 'bg-yellow-50 text-yellow-800 ring-yellow-200','icon' => 'alert',   'iconColor' => 'text-yellow-500'],
        'info'    => ['wrap' => 'bg-brand-50 text-brand-800 ring-brand-200',  'icon' => 'alert',    'iconColor' => 'text-brand-500'],
    ];
    $s = $styles[$type] ?? $styles['info'];
@endphp

<div data-flash
     x-data="{ show: true }" x-show="show"
     {{ $attributes->merge(['class' => "flex items-start gap-3 rounded-lg px-4 py-3 text-sm ring-1 ring-inset {$s['wrap']}"]) }}>
    <x-ui.icon :name="$s['icon']" class="mt-0.5 h-5 w-5 shrink-0 {{ $s['iconColor'] }}" />
    <div class="flex-1">{{ $slot }}</div>
    <button type="button" @click="show = false" class="shrink-0 opacity-60 hover:opacity-100">
        <span class="sr-only">Cerrar</span>
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
        </svg>
    </button>
</div>
