@props([
    'name',
    'title' => null,
    'show' => false,
    'maxWidth' => 'lg',
])

{{-- Reutiliza el modal base de Breeze (trampa de foco, tecla Escape, evento open-modal). --}}
<x-modal :name="$name" :show="$show" :max-width="$maxWidth" focusable>
    <div class="px-6 py-5">
        @if ($title)
            <div class="mb-4 flex items-start justify-between gap-4">
                <h2 class="text-base font-semibold text-gray-900">{{ $title }}</h2>
                <button type="button" x-on:click="$dispatch('close-modal', '{{ $name }}')"
                        class="rounded-md p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                    <span class="sr-only">Cerrar</span>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        <div class="text-sm text-gray-600">
            {{ $slot }}
        </div>

        @isset($footer)
            <div class="mt-6 flex justify-end gap-3">
                {{ $footer }}
            </div>
        @endisset
    </div>
</x-modal>
