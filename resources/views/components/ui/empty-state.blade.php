@props([
    'title' => 'Sin registros',
    'message' => null,
    'icon' => 'clipboard',
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center rounded-lg border border-dashed border-gray-200 px-6 py-10 text-center']) }}>
    <span class="grid h-12 w-12 place-items-center rounded-full bg-gray-100 text-gray-400">
        <x-ui.icon :name="$icon" class="h-6 w-6" />
    </span>
    <p class="mt-3 text-sm font-medium text-gray-900">{{ $title }}</p>
    @if ($message)
        <p class="mt-1 max-w-sm text-sm text-gray-500">{{ $message }}</p>
    @endif
    @isset($action)
        <div class="mt-4">{{ $action }}</div>
    @endisset
</div>
