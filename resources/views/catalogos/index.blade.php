<x-app-layout>
    <x-slot name="title">{{ $titulo }}</x-slot>
    <x-slot name="header">{{ $titulo }}</x-slot>
    <x-slot name="subheader">Catálogo que alimenta los formularios de equipos.</x-slot>

    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <form method="GET" action="{{ route($rutaBase . '.index') }}" class="flex flex-wrap gap-3">
            <input type="text" name="buscar" value="{{ $filtros['buscar'] ?? '' }}" placeholder="Buscar"
                   class="rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 sm:w-56">
            <select name="estado" class="rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                <option value="">Todos los estados</option>
                <option value="activo" @selected(($filtros['estado'] ?? '') === 'activo')>Activos</option>
                <option value="inactivo" @selected(($filtros['estado'] ?? '') === 'inactivo')>Inactivos</option>
            </select>
            <x-ui.button type="submit" variant="secondary">Filtrar</x-ui.button>
        </form>

        @can('catalogos.gestionar')
            <x-ui.button :href="route($rutaBase . '.create')">{{ $etiquetaNueva }}</x-ui.button>
        @endcan
    </div>

    @if ($items->isEmpty())
        <x-ui.card>
            <x-ui.empty-state title="Sin registros" message="No hay elementos que coincidan con el filtro." />
        </x-ui.card>
    @else
        @php $tieneConteoEquipos = isset($items->first()->equipos_count); @endphp
        <x-ui.table>
            <x-slot name="head">
                @foreach ($campos as $campo)
                    <th class="px-4 py-3">{{ $campo['label'] }}</th>
                @endforeach
                @if ($tieneConteoEquipos)
                    <th class="px-4 py-3">Equipos</th>
                @endif
                <th class="px-4 py-3">Estado</th>
                <th class="px-4 py-3 text-right">Acciones</th>
            </x-slot>

            @foreach ($items as $item)
                <tr class="hover:bg-gray-50">
                    @foreach ($campos as $i => $campo)
                        <td class="px-4 py-3 {{ $i === 0 ? 'font-medium text-gray-900' : 'text-gray-600' }}">
                            {{ $item->{$campo['name']} ?: '—' }}
                        </td>
                    @endforeach
                    @if ($tieneConteoEquipos)
                        <td class="px-4 py-3">
                            @if ($item->equipos_count > 0)
                                <a href="{{ route('equipos.index', ['ubicacion_id' => $item->id]) }}"
                                   class="text-brand-700 hover:underline">{{ $item->equipos_count }} equipo(s)</a>
                            @else
                                <span class="text-gray-400">0</span>
                            @endif
                        </td>
                    @endif
                    <td class="px-4 py-3">
                        <x-ui.badge :color="$item->activo ? 'green' : 'gray'">
                            {{ $item->activo ? 'Activo' : 'Inactivo' }}
                        </x-ui.badge>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-2">
                            @can('catalogos.gestionar')
                                <x-ui.button :href="route($rutaBase . '.edit', $item->id)" variant="secondary" size="sm">Editar</x-ui.button>
                                @if ($item->activo)
                                    <form method="POST" action="{{ route($rutaBase . '.destroy', $item->id) }}"
                                          onsubmit="return confirm('¿Desactivar «{{ $item->nombre }}»?')">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button type="submit" variant="danger" size="sm">Desactivar</x-ui.button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route($rutaBase . '.reactivar', $item->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <x-ui.button type="submit" variant="secondary" size="sm">Reactivar</x-ui.button>
                                    </form>
                                @endif
                            @endcan
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-ui.table>

        <div class="mt-4">{{ $items->links() }}</div>
    @endif
</x-app-layout>
