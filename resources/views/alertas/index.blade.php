<x-app-layout>
    <x-slot name="title">Alertas</x-slot>
    <x-slot name="header">Alertas</x-slot>
    <x-slot name="subheader">Mantenimientos por vencer y equipos que requieren atención.</x-slot>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <x-ui.stat-card label="Mantenimientos vencidos" :value="$contadores['vencidas']" icon="clock" color="red" />
        <x-ui.stat-card label="Mantenimientos próximos" :value="$contadores['proximas']" icon="calendar" color="yellow" />
        <x-ui.stat-card label="Equipos con novedades" :value="$contadores['con_novedad']" icon="alert" color="indigo" />
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <x-ui.card title="Mantenimientos por atender">
            @if ($detalle['programaciones']->isEmpty())
                <x-ui.empty-state icon="calendar" title="Todo al día"
                    message="No hay mantenimientos vencidos ni próximos a vencer." />
            @else
                <ul class="divide-y divide-gray-100 text-sm">
                    @foreach ($detalle['programaciones'] as $programacion)
                        @php $estado = $programacion->estadoActual(); $dias = $programacion->diasParaProxima(); @endphp
                        <li class="flex items-center justify-between gap-3 py-2">
                            <div class="min-w-0">
                                <a href="{{ route('equipos.show', $programacion->equipo) }}"
                                   class="font-medium text-brand-700 hover:underline">{{ $programacion->equipo->codigo_interno }}</a>
                                <p class="text-xs text-gray-500">
                                    {{ $programacion->tipo->label() }} · {{ $programacion->proxima_fecha->format('d/m/Y') }}
                                    ({{ $dias < 0 ? abs($dias).' días de retraso' : ($dias === 0 ? 'hoy' : "en {$dias} días") }})
                                </p>
                            </div>
                            <x-ui.badge :color="$estado->color()">{{ $estado->label() }}</x-ui.badge>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-ui.card>

        <x-ui.card title="Equipos con novedades">
            @if ($detalle['equipos']->isEmpty())
                <x-ui.empty-state icon="check" title="Sin novedades" message="Todos los equipos están operativos." />
            @else
                <ul class="divide-y divide-gray-100 text-sm">
                    @foreach ($detalle['equipos'] as $equipo)
                        <li class="flex items-center justify-between gap-3 py-2">
                            <div class="min-w-0">
                                <a href="{{ route('equipos.show', $equipo) }}"
                                   class="font-medium text-brand-700 hover:underline">{{ $equipo->codigo_interno }}</a>
                                <p class="truncate text-xs text-gray-500">{{ $equipo->ubicacion->nombre }}</p>
                            </div>
                            <x-ui.badge :color="$equipo->estado->color()">{{ $equipo->estado->label() }}</x-ui.badge>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-ui.card>
    </div>
</x-app-layout>
