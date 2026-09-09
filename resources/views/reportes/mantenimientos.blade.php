<x-app-layout>
    <x-slot name="title">Reporte de mantenimientos</x-slot>
    <x-slot name="header">Reporte de mantenimientos</x-slot>
    <x-slot name="subheader">{{ $reporte['total'] }} mantenimiento(s) con los filtros aplicados.</x-slot>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('reportes.mantenimientos') }}"
          class="mb-4 grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-5 lg:items-end">
        <input type="text" name="equipo" value="{{ $filtros['equipo'] ?? '' }}" placeholder="Código de equipo"
               class="rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
        <select name="tipo" class="rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="">Tipo: todos</option>
            @foreach ($tipos as $t)
                <option value="{{ $t->value }}" @selected(($filtros['tipo'] ?? '') === $t->value)>{{ $t->label() }}</option>
            @endforeach
        </select>
        <select name="responsable_id" class="rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="">Técnico: todos</option>
            @foreach ($responsables as $r)
                <option value="{{ $r->id }}" @selected((int) ($filtros['responsable_id'] ?? 0) === $r->id)>{{ $r->nombre }}</option>
            @endforeach
        </select>
        <label class="flex items-center gap-1 text-sm text-gray-500">
            Desde
            <input type="date" name="desde" value="{{ $filtros['desde'] ?? '' }}"
                   class="rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
        </label>
        <label class="flex items-center gap-1 text-sm text-gray-500">
            Hasta
            <input type="date" name="hasta" value="{{ $filtros['hasta'] ?? '' }}"
                   class="rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
        </label>

        <div class="col-span-2 flex flex-wrap gap-2 sm:col-span-3 lg:col-span-5">
            <x-ui.button type="submit" variant="secondary">Aplicar filtros</x-ui.button>
            @if (! empty($filtros))
                <x-ui.button :href="route('reportes.mantenimientos')" variant="ghost">Limpiar</x-ui.button>
            @endif
            <div class="flex-1"></div>
            <x-ui.button :href="route('reportes.mantenimientos.export', array_merge($filtros, ['formato' => 'pdf']))" variant="secondary">
                Exportar PDF
            </x-ui.button>
            <x-ui.button :href="route('reportes.mantenimientos.export', array_merge($filtros, ['formato' => 'csv']))">
                Exportar CSV (Excel)
            </x-ui.button>
        </div>
    </form>

    {{-- Contexto de programación --}}
    <div class="mb-4 grid grid-cols-2 gap-4 sm:max-w-md">
        <x-ui.stat-card label="Programaciones próximas" :value="$reporte['programacion']['proximas']" icon="calendar" color="indigo" />
        <x-ui.stat-card label="Programaciones vencidas" :value="$reporte['programacion']['vencidas']" icon="clock" color="red" />
    </div>

    {{-- Resumen --}}
    <h3 class="mb-3 text-sm font-semibold text-gray-900">Resumen</h3>
    @include('reportes.partials.agrupaciones', ['agrupaciones' => $reporte['agrupaciones']])

    {{-- Detalle --}}
    <h3 class="mb-3 mt-6 text-sm font-semibold text-gray-900">Detalle</h3>
    @if ($reporte['filas']->isEmpty())
        <x-ui.card>
            <x-ui.empty-state title="Sin resultados" message="Ningún mantenimiento coincide con los filtros seleccionados." />
        </x-ui.card>
    @else
        <x-ui.table>
            <x-slot name="head">
                <th class="px-4 py-3">Fecha</th>
                <th class="px-4 py-3">Equipo</th>
                <th class="px-4 py-3">Tipo</th>
                <th class="px-4 py-3">Técnico</th>
                <th class="px-4 py-3">Descripción</th>
            </x-slot>
            @foreach ($reporte['filas'] as $mantenimiento)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-600">{{ $mantenimiento->fecha->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $mantenimiento->equipo->codigo_interno }}</td>
                    <td class="px-4 py-3">
                        <x-ui.badge :color="$mantenimiento->tipo->color()">{{ $mantenimiento->tipo->label() }}</x-ui.badge>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $mantenimiento->responsable?->nombre ?? 'Sin asignar' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ \Illuminate\Support\Str::limit($mantenimiento->descripcion, 80) }}</td>
                </tr>
            @endforeach
        </x-ui.table>
    @endif
</x-app-layout>
