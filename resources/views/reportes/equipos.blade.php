<x-app-layout>
    <x-slot name="title">Reporte de equipos</x-slot>
    <x-slot name="header">Reporte de equipos</x-slot>
    <x-slot name="subheader">{{ $reporte['total'] }} equipo(s) con los filtros aplicados.</x-slot>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('reportes.equipos') }}"
          class="mb-4 grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-6 lg:items-end">
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
            @foreach ($estados as $e)
                <option value="{{ $e->value }}" @selected(($filtros['estado'] ?? '') === $e->value)>{{ $e->label() }}</option>
            @endforeach
        </select>
        <select name="responsable_id" class="rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="">Responsable: todos</option>
            @foreach ($responsables as $r)
                <option value="{{ $r->id }}" @selected((int) ($filtros['responsable_id'] ?? 0) === $r->id)>{{ $r->nombre }}</option>
            @endforeach
        </select>
        <select name="activo" class="rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="">Activos e inactivos</option>
            <option value="activo" @selected(($filtros['activo'] ?? '') === 'activo')>Solo activos</option>
            <option value="inactivo" @selected(($filtros['activo'] ?? '') === 'inactivo')>Solo inactivos</option>
        </select>

        <div class="col-span-2 flex flex-wrap gap-2 sm:col-span-3 lg:col-span-6">
            <x-ui.button type="submit" variant="secondary">Aplicar filtros</x-ui.button>
            @if (! empty($filtros))
                <x-ui.button :href="route('reportes.equipos')" variant="ghost">Limpiar</x-ui.button>
            @endif
            <div class="flex-1"></div>
            <x-ui.button :href="route('reportes.equipos.export', array_merge($filtros, ['formato' => 'pdf']))" variant="secondary">
                Exportar PDF
            </x-ui.button>
            <x-ui.button :href="route('reportes.equipos.export', array_merge($filtros, ['formato' => 'csv']))">
                Exportar CSV (Excel)
            </x-ui.button>
        </div>
    </form>

    {{-- Resumen --}}
    <h3 class="mb-3 text-sm font-semibold text-gray-900">Resumen</h3>
    @include('reportes.partials.agrupaciones', ['agrupaciones' => $reporte['agrupaciones']])

    {{-- Detalle --}}
    <h3 class="mb-3 mt-6 text-sm font-semibold text-gray-900">Detalle</h3>
    @if ($reporte['filas']->isEmpty())
        <x-ui.card>
            <x-ui.empty-state title="Sin resultados" message="Ningún equipo coincide con los filtros seleccionados." />
        </x-ui.card>
    @else
        <x-ui.table>
            <x-slot name="head">
                <th class="px-4 py-3">Código</th>
                <th class="px-4 py-3">Tipo</th>
                <th class="px-4 py-3">Marca / Modelo</th>
                <th class="px-4 py-3">Ubicación</th>
                <th class="px-4 py-3">Responsable</th>
                <th class="px-4 py-3">Estado</th>
            </x-slot>
            @foreach ($reporte['filas'] as $equipo)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $equipo->codigo_interno }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $equipo->tipoEquipo->nombre }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $equipo->marca->nombre }}@if ($equipo->modelo) · {{ $equipo->modelo }}@endif</td>
                    <td class="px-4 py-3 text-gray-600">{{ $equipo->ubicacion->nombre }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $equipo->responsable?->nombre ?? 'Sin asignar' }}</td>
                    <td class="px-4 py-3">
                        <x-ui.badge :color="$equipo->estado->color()">{{ $equipo->estado->label() }}</x-ui.badge>
                    </td>
                </tr>
            @endforeach
        </x-ui.table>
    @endif
</x-app-layout>
