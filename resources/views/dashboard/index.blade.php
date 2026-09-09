@php
    $t = $resumen['tarjetas'];
    $barColores = [
        'green' => 'bg-green-500', 'yellow' => 'bg-yellow-500', 'red' => 'bg-red-500',
        'blue' => 'bg-blue-500', 'indigo' => 'bg-indigo-500', 'brand' => 'bg-brand-500', 'gray' => 'bg-gray-400',
    ];
@endphp

<x-app-layout>
    <x-slot name="title">Panel principal</x-slot>
    <x-slot name="header">Panel principal</x-slot>
    <x-slot name="subheader">Resumen del inventario y los mantenimientos de la institución.</x-slot>

    {{-- Indicadores --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-ui.stat-card label="Equipos registrados" :value="$t['total']" icon="desktop" color="brand" />
        <x-ui.stat-card label="Operativos" :value="$t['operativos']" icon="check" color="green" />
        <x-ui.stat-card label="Con novedades" :value="$t['con_novedades']" icon="alert" color="yellow" />
        <x-ui.stat-card label="En mantenimiento" :value="$t['en_mantenimiento']" icon="wrench" color="blue" />
        <x-ui.stat-card label="Fuera de servicio" :value="$t['fuera_de_servicio']" icon="ban" color="red" />
        <x-ui.stat-card label="Mantenimientos próximos" :value="$t['mantenimientos_proximos']" icon="calendar" color="indigo" />
        <x-ui.stat-card label="Mantenimientos vencidos" :value="$t['mantenimientos_vencidos']" icon="clock" color="red" />
    </div>

    {{-- Gráficos --}}
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <x-ui.card title="Equipos por estado">
            @if (empty($resumen['porEstado']))
                <x-ui.empty-state title="Sin equipos registrados" />
            @else
                <div class="space-y-3">
                    @foreach ($resumen['porEstado'] as $row)
                        <div>
                            <div class="mb-1 flex items-center justify-between text-xs text-gray-600">
                                <span>{{ $row['label'] }}</span>
                                <span class="font-medium text-gray-900">{{ $row['total'] }} ({{ $row['pct'] }}%)</span>
                            </div>
                            <div class="h-2 w-full overflow-hidden rounded-full bg-gray-100">
                                <div class="h-full rounded-full {{ $barColores[$row['color']] ?? 'bg-brand-500' }}"
                                     style="width: {{ max($row['pct'], 2) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-ui.card>

        <x-ui.card title="Equipos por ubicación">
            @if (empty($resumen['porUbicacion']))
                <x-ui.empty-state title="Sin datos" />
            @else
                <div class="space-y-3">
                    @foreach ($resumen['porUbicacion'] as $row)
                        <div>
                            <div class="mb-1 flex items-center justify-between text-xs text-gray-600">
                                <span class="truncate">{{ $row['nombre'] }}</span>
                                <span class="font-medium text-gray-900">{{ $row['total'] }}</span>
                            </div>
                            <div class="h-2 w-full overflow-hidden rounded-full bg-gray-100">
                                <div class="h-full rounded-full bg-brand-500" style="width: {{ max($row['pct'], 2) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-ui.card>
    </div>

    {{-- Paneles --}}
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <x-ui.card title="Mantenimientos por atender" subtitle="Vencidos y próximos">
            @if ($resumen['porAtender']->isEmpty())
                <x-ui.empty-state icon="calendar" title="Todo al día"
                    message="No hay mantenimientos vencidos ni próximos a vencer." />
            @else
                <ul class="divide-y divide-gray-100 text-sm">
                    @foreach ($resumen['porAtender'] as $programacion)
                        @php $estado = $programacion->estadoActual(); @endphp
                        <li class="flex items-center justify-between gap-3 py-2">
                            <div class="min-w-0">
                                <a href="{{ route('equipos.show', $programacion->equipo) }}"
                                   class="font-medium text-brand-700 hover:underline">{{ $programacion->equipo->codigo_interno }}</a>
                                <p class="text-xs text-gray-500">
                                    {{ $programacion->tipo->label() }} · {{ $programacion->proxima_fecha->format('d/m/Y') }}
                                </p>
                            </div>
                            <x-ui.badge :color="$estado->color()">{{ $estado->label() }}</x-ui.badge>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('programaciones.index') }}" class="mt-3 inline-block text-xs font-medium text-brand-700 hover:underline">
                    Ver toda la programación
                </a>
            @endif
        </x-ui.card>

        <x-ui.card title="Equipos con novedades">
            @if ($resumen['conNovedades']->isEmpty())
                <x-ui.empty-state icon="check" title="Sin novedades" message="Todos los equipos están operativos." />
            @else
                <ul class="divide-y divide-gray-100 text-sm">
                    @foreach ($resumen['conNovedades'] as $equipo)
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

        <x-ui.card title="Actividad reciente">
            @if ($resumen['actividadReciente']->isEmpty())
                <x-ui.empty-state icon="clipboard" title="Sin actividad" message="Aún no se han registrado acciones." />
            @else
                <ul class="space-y-3 text-sm">
                    @foreach ($resumen['actividadReciente'] as $evento)
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 grid h-7 w-7 shrink-0 place-items-center rounded-full bg-gray-100 text-gray-400">
                                <x-ui.icon :name="$evento->icono" class="h-4 w-4" />
                            </span>
                            <div class="min-w-0">
                                <p class="text-gray-800">{{ ucfirst($evento->texto) }}</p>
                                <p class="text-xs text-gray-400">{{ $evento->fecha?->diffForHumans() }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-ui.card>
    </div>
</x-app-layout>
