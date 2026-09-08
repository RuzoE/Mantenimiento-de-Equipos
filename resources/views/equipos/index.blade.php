<x-app-layout>
    <x-slot name="title">Equipos</x-slot>
    <x-slot name="header">Equipos</x-slot>
    <x-slot name="subheader">Inventario de equipos tecnológicos de la institución.</x-slot>

    <div class="mb-4 flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
        <form method="GET" action="{{ route('equipos.index') }}" class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:flex lg:flex-wrap">
            <input type="text" name="buscar" value="{{ $filtros['buscar'] ?? '' }}" placeholder="Código, serie o modelo"
                   class="col-span-2 rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 sm:col-span-1 lg:w-52">

            <select name="tipo_equipo_id" class="rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                <option value="">Tipo: todos</option>
                @foreach ($tipos as $t)
                    <option value="{{ $t->id }}" @selected((int) ($filtros['tipo_equipo_id'] ?? 0) === $t->id)>{{ $t->nombre }}</option>
                @endforeach
            </select>

            <select name="marca_id" class="rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                <option value="">Marca: todas</option>
                @foreach ($marcas as $m)
                    <option value="{{ $m->id }}" @selected((int) ($filtros['marca_id'] ?? 0) === $m->id)>{{ $m->nombre }}</option>
                @endforeach
            </select>

            <select name="ubicacion_id" class="rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                <option value="">Ubicación: todas</option>
                @foreach ($ubicaciones as $u)
                    <option value="{{ $u->id }}" @selected((int) ($filtros['ubicacion_id'] ?? 0) === $u->id)>{{ $u->nombre }}</option>
                @endforeach
            </select>

            <select name="estado" class="rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                <option value="">Estado: todos</option>
                @foreach ($estados as $est)
                    <option value="{{ $est->value }}" @selected(($filtros['estado'] ?? '') === $est->value)>{{ $est->label() }}</option>
                @endforeach
            </select>

            <select name="activo" class="rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                <option value="">Activos e inactivos</option>
                <option value="activo" @selected(($filtros['activo'] ?? '') === 'activo')>Solo activos</option>
                <option value="inactivo" @selected(($filtros['activo'] ?? '') === 'inactivo')>Solo inactivos</option>
            </select>

            <x-ui.button type="submit" variant="secondary">Filtrar</x-ui.button>
            @if (collect($filtros)->filter()->isNotEmpty())
                <x-ui.button :href="route('equipos.index')" variant="ghost">Limpiar</x-ui.button>
            @endif
        </form>

        @can('equipos.crear')
            <x-ui.button :href="route('equipos.create')" class="shrink-0">
                <x-ui.icon name="desktop" class="h-4 w-4" />
                Nuevo equipo
            </x-ui.button>
        @endcan
    </div>

    @if ($equipos->isEmpty())
        <x-ui.card>
            <x-ui.empty-state icon="desktop" title="No hay equipos"
                message="No se encontraron equipos con los filtros aplicados." />
        </x-ui.card>
    @else
        <x-ui.table>
            <x-slot name="head">
                <th class="px-4 py-3">Código</th>
                <th class="px-4 py-3">Tipo</th>
                <th class="px-4 py-3">Marca / Modelo</th>
                <th class="px-4 py-3">Ubicación</th>
                <th class="px-4 py-3">Estado</th>
                <th class="px-4 py-3 text-right">Acciones</th>
            </x-slot>

            @foreach ($equipos as $equipo)
                <tr class="hover:bg-gray-50 {{ $equipo->activo ? '' : 'opacity-60' }}">
                    <td class="px-4 py-3">
                        <a href="{{ route('equipos.show', $equipo) }}" class="font-medium text-brand-700 hover:underline">
                            {{ $equipo->codigo_interno }}
                        </a>
                        @unless ($equipo->activo)
                            <span class="ml-1 text-xs text-gray-400">(inactivo)</span>
                        @endunless
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $equipo->tipoEquipo->nombre }}</td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ $equipo->marca->nombre }}@if ($equipo->modelo) · {{ $equipo->modelo }}@endif
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $equipo->ubicacion->nombre }}</td>
                    <td class="px-4 py-3">
                        <x-ui.badge :color="$equipo->estado->color()">{{ $equipo->estado->label() }}</x-ui.badge>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-2">
                            <x-ui.button :href="route('equipos.show', $equipo)" variant="secondary" size="sm">Ver</x-ui.button>
                            @can('update', $equipo)
                                <x-ui.button :href="route('equipos.edit', $equipo)" variant="secondary" size="sm">Editar</x-ui.button>
                            @endcan
                            @can('delete', $equipo)
                                @if ($equipo->activo)
                                    <form method="POST" action="{{ route('equipos.destroy', $equipo) }}"
                                          onsubmit="return confirm('¿Desactivar el equipo {{ $equipo->codigo_interno }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button type="submit" variant="danger" size="sm">Desactivar</x-ui.button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('equipos.reactivar', $equipo) }}">
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

        <div class="mt-4">{{ $equipos->links() }}</div>
    @endif
</x-app-layout>
