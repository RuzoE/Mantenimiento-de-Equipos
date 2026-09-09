@props(['label'])

<div {{ $attributes->merge(['class' => 'flex items-start justify-between gap-4 py-2']) }}>
    <dt class="shrink-0 text-gray-500">{{ $label }}</dt>
    <dd class="min-w-0 text-right font-medium text-gray-900">
        {{ $slot->isEmpty() ? '—' : $slot }}
    </dd>
</div>
