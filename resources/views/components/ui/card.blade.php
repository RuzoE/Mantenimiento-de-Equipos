@props([
    'title' => null,
    'subtitle' => null,
    'padding' => true,
])

<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200']) }}>
    @if ($title || isset($actions))
        <div class="flex items-center justify-between gap-4 border-b border-gray-100 px-5 py-4">
            <div class="min-w-0">
                @if ($title)
                    <h3 class="text-sm font-semibold text-gray-900">{{ $title }}</h3>
                @endif
                @if ($subtitle)
                    <p class="mt-0.5 text-xs text-gray-500">{{ $subtitle }}</p>
                @endif
            </div>
            @isset($actions)
                <div class="shrink-0">{{ $actions }}</div>
            @endisset
        </div>
    @endif

    <div class="{{ $padding ? 'p-5' : '' }}">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="border-t border-gray-100 bg-gray-50 px-5 py-3 text-sm">
            {{ $footer }}
        </div>
    @endisset
</div>
