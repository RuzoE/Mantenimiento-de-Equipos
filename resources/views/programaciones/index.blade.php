<x-app-layout>
    <x-slot name="title">Programación</x-slot>
    <x-slot name="header">Programación de mantenimientos</x-slot>
    <x-slot name="subheader">Mantenimientos preventivos programados por equipo.</x-slot>

    <form method="GET" action="{{ route('programaciones.index') }}"
          class="mb-4 grid grid-cols-2 gap-2 sm:grid-cols-4 lg:flex lg:flex-wrap lg:items-end">
        <input type="text" name="buscar" value="{{ $filtros['buscar'] ?? '' }}" placeholder="Código de equipo"
               class="col-span-2 rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 sm:col-span-1 lg:w-48">

        <select name="tipo" class="rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="">Tipo: todos</option>
            @foreach ($tipos as $t)
                <option value="{{ $t->value }}" @selected(($filtros['tipo'] ?? '') === $t->value)>{{ $t->label() }}</option>
            @endforeach
        </select>

        <select name="estado" class="rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="">Todas</option>
            <option value="vencidas" @selected(($filtros['estado'] ?? '') === 'vencidas')>Vencidas</option>
            <option value="proximas" @selected(($filtros['estado'] ?? '') === 'proximas')>Próximas</option>
            <option value="canceladas" @selected(($filtros['estado'] ?? '') === 'canceladas')>Canceladas</option>
        </select>

        <x-ui.button type="submit" variant="secondary">Filtrar</x-ui.button>
        @if (collect($filtros)->filter()->isNotEmpty())
            <x-ui.button :href="route('programaciones.index')" variant="ghost">Limpiar</x-ui.button>
        @endif
    </form>

    @if ($programaciones->isEmpty())
        <x-ui.card>
            <x-ui.empty-state icon="calendar" title="Sin programaciones"
                message="No hay programaciones que coincidan con los filtros. Créalas desde la hoja de vida de cada equipo." />
        </x-ui.card>
    @else
        <x-ui.table>
            <x-slot name="head">
                <th class="px-4 py-3">Equipo</th>
                <th class="px-4 py-3">Tipo</th>
                <th class="px-4 py-3">Frecuencia</th>
                <th class="px-4 py-3">Última</th>
                <th class="px-4 py-3">Próxima</th>
                <th class="px-4 py-3">Estado</th>
                <th class="px-4 py-3 text-right">Acciones</th>
            </x-slot>

            @foreach ($programaciones as $programacion)
                @php $estado = $programacion->estadoActual(); $dias = $programacion->diasParaProxima(); @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">
                        <a href="{{ route('equipos.show', $programacion->equipo) }}" class="text-brand-700 hover:underline">
                            {{ $programacion->equipo->codigo_interno }}
                        </a>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $programacion->tipo->label() }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $programacion->frecuencia->label() }}</td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ $programacion->fecha_ultimo_mantenimiento?->format('d/m/Y') ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ $programacion->proxima_fecha->format('d/m/Y') }}
                    </td>
                    <td class="px-4 py-3">
                        <x-ui.badge :color="$estado->color()">{{ $estado->label() }}</x-ui.badge>
                        @if ($estado !== \App\Enums\EstadoProgramacion::Cancelado)
                            <span class="ml-1 text-xs text-gray-400">
                                {{ $dias < 0 ? abs($dias).' días de retraso' : ($dias === 0 ? 'hoy' : "en {$dias} días") }}
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap items-center justify-end gap-2">
                            @can('update', $programacion)
                                <x-ui.button :href="route('programaciones.edit', $programacion)" variant="secondary" size="sm">Editar</x-ui.button>

                                @if ($programacion->estado !== \App\Enums\EstadoProgramacion::Cancelado)
                                    <form method="POST" action="{{ route('programaciones.realizar', $programacion) }}">
                                        @csrf @method('PATCH')
                                        <x-ui.button type="submit" size="sm">Registrar cumplimiento</x-ui.button>
                                    </form>
                                    <form method="POST" action="{{ route('programaciones.cancelar', $programacion) }}"
                                          onsubmit="return confirm('¿Cancelar esta programación?')">
                                        @csrf @method('PATCH')
                                        <x-ui.button type="submit" variant="ghost" size="sm">Cancelar</x-ui.button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('programaciones.reactivar', $programacion) }}">
                                        @csrf @method('PATCH')
                                        <x-ui.button type="submit" variant="secondary" size="sm">Reactivar</x-ui.button>
                                    </form>
                                @endif
                            @endcan
                            @can('delete', $programacion)
                                <form method="POST" action="{{ route('programaciones.destroy', $programacion) }}"
                                      onsubmit="return confirm('¿Eliminar esta programación?')">
                                    @csrf @method('DELETE')
                                    <x-ui.button type="submit" variant="danger" size="sm">Eliminar</x-ui.button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-ui.table>

        <div class="mt-4">{{ $programaciones->links() }}</div>
    @endif
</x-app-layout>
