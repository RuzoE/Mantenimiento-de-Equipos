@php $a = $alertas ?? ['total' => 0, 'vencidas' => 0, 'proximas' => 0, 'con_novedad' => 0]; @endphp

<x-dropdown align="right" width="64">
    <x-slot name="trigger">
        <button class="relative rounded-full p-2 text-gray-500 transition hover:bg-gray-100 hover:text-gray-700">
            <span class="sr-only">Alertas</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
            </svg>
            @if ($a['total'] > 0)
                <span class="absolute -right-0.5 -top-0.5 grid h-4 min-w-4 place-items-center rounded-full bg-red-500 px-1 text-[10px] font-semibold text-white">
                    {{ $a['total'] > 99 ? '99+' : $a['total'] }}
                </span>
            @endif
        </button>
    </x-slot>

    <x-slot name="content">
        <div class="border-b border-gray-100 px-4 py-3">
            <p class="text-sm font-semibold text-gray-900">Alertas</p>
        </div>
        <div class="px-4 py-2 text-sm">
            @if ($a['total'] === 0)
                <p class="py-2 text-gray-500">No hay alertas pendientes.</p>
            @else
                <ul class="space-y-1">
                    <li class="flex items-center justify-between">
                        <span class="text-gray-600">Mantenimientos vencidos</span>
                        <span class="font-semibold text-red-600">{{ $a['vencidas'] }}</span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="text-gray-600">Mantenimientos próximos</span>
                        <span class="font-semibold text-yellow-600">{{ $a['proximas'] }}</span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="text-gray-600">Equipos con novedades</span>
                        <span class="font-semibold text-gray-900">{{ $a['con_novedad'] }}</span>
                    </li>
                </ul>
            @endif
        </div>
        <a href="{{ route('alertas.index') }}"
           class="block border-t border-gray-100 px-4 py-2 text-sm font-medium text-brand-700 hover:bg-gray-50">
            Ver todas las alertas
        </a>
    </x-slot>
</x-dropdown>
