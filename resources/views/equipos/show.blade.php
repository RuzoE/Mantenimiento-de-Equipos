<x-app-layout>
    <x-slot name="title">{{ $equipo->codigo_interno }}</x-slot>
    <x-slot name="header">{{ $equipo->codigo_interno }}</x-slot>
    <x-slot name="subheader">{{ $equipo->tipoEquipo->nombre }} · {{ $equipo->marca->nombre }}{{ $equipo->modelo ? ' '.$equipo->modelo : '' }}</x-slot>

    <div class="mb-5 flex flex-wrap items-center gap-3">
        <x-ui.badge :color="$equipo->estado->color()">{{ $equipo->estado->label() }}</x-ui.badge>
        @unless ($equipo->activo)
            <x-ui.badge color="gray">Registro inactivo</x-ui.badge>
        @endunless
        <div class="flex-1"></div>
        @can('update', $equipo)
            <x-ui.button :href="route('equipos.edit', $equipo)" variant="secondary" size="sm">Editar</x-ui.button>
        @endcan
        <x-ui.button :href="route('equipos.index')" variant="ghost" size="sm">Volver al listado</x-ui.button>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <x-ui.card title="Información general">
            <dl class="divide-y divide-gray-100 text-sm">
                @foreach ([
                    'Código interno' => $equipo->codigo_interno,
                    'Tipo de equipo' => $equipo->tipoEquipo->nombre,
                    'Marca' => $equipo->marca->nombre,
                    'Modelo' => $equipo->modelo,
                    'Número de serie' => $equipo->numero_serie,
                    'Fecha de adquisición' => $equipo->fecha_adquisicion?->format('d/m/Y'),
                    'Fin de garantía' => $equipo->fecha_garantia?->format('d/m/Y'),
                ] as $label => $valor)
                    <div class="flex justify-between gap-4 py-2">
                        <dt class="text-gray-500">{{ $label }}</dt>
                        <dd class="text-right font-medium text-gray-900">{{ filled($valor) ? $valor : '—' }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-ui.card>

        <x-ui.card title="Estado y ubicación">
            <dl class="divide-y divide-gray-100 text-sm">
                <div class="flex items-center justify-between gap-4 py-2">
                    <dt class="text-gray-500">Estado</dt>
                    <dd><x-ui.badge :color="$equipo->estado->color()">{{ $equipo->estado->label() }}</x-ui.badge></dd>
                </div>
                <div class="flex justify-between gap-4 py-2">
                    <dt class="text-gray-500">Ubicación</dt>
                    <dd class="text-right font-medium text-gray-900">{{ $equipo->ubicacion->nombre }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-2">
                    <dt class="text-gray-500">Responsable</dt>
                    <dd class="text-right font-medium text-gray-900">{{ $equipo->responsable?->nombre ?? 'Sin asignar' }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-2">
                    <dt class="text-gray-500">Registro</dt>
                    <dd class="text-right font-medium text-gray-900">{{ $equipo->activo ? 'Activo' : 'Inactivo' }}</dd>
                </div>
            </dl>
        </x-ui.card>

        <x-ui.card title="Especificaciones" class="lg:col-span-2">
            @php
                $specs = array_filter([
                    'Procesador' => $equipo->procesador,
                    'Memoria RAM' => $equipo->memoria_ram,
                    'Almacenamiento' => $equipo->almacenamiento,
                    'Sistema operativo' => $equipo->sistema_operativo,
                ], 'filled');
            @endphp

            @if ($specs === [])
                <x-ui.empty-state title="Sin especificaciones registradas"
                    message="Edite el equipo para añadir procesador, memoria, almacenamiento o sistema operativo." />
            @else
                <dl class="grid grid-cols-1 gap-x-8 gap-y-2 text-sm sm:grid-cols-2">
                    @foreach ($specs as $label => $valor)
                        <div class="flex justify-between gap-4 border-b border-gray-100 py-2">
                            <dt class="text-gray-500">{{ $label }}</dt>
                            <dd class="text-right font-medium text-gray-900">{{ $valor }}</dd>
                        </div>
                    @endforeach
                </dl>
            @endif
        </x-ui.card>

        @if ($equipo->observaciones)
            <x-ui.card title="Observaciones" class="lg:col-span-2">
                <p class="whitespace-pre-line text-sm text-gray-700">{{ $equipo->observaciones }}</p>
            </x-ui.card>
        @endif
    </div>

    <p class="mt-6 text-xs text-gray-400">
        El historial de mantenimientos, traslados y el próximo mantenimiento programado se mostrarán aquí a partir de la FASE 5.
    </p>
</x-app-layout>
