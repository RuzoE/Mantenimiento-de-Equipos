<x-app-layout>
    <x-slot name="title">Traslados</x-slot>
    <x-slot name="header">Traslados</x-slot>
    <x-slot name="subheader">Historial de cambios de ubicación de los equipos.</x-slot>

    <form method="GET" action="{{ route('traslados.index') }}"
          class="mb-4 grid grid-cols-2 gap-2 sm:grid-cols-3 lg:flex lg:flex-wrap lg:items-end">
        <input type="text" name="buscar" value="{{ $filtros['buscar'] ?? '' }}" placeholder="Código de equipo"
               class="col-span-2 rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 sm:col-span-1 lg:w-44">

        <select name="ubicacion_origen_id" class="rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="">Origen: todas</option>
            @foreach ($ubicaciones as $u)
                <option value="{{ $u->id }}" @selected((int) ($filtros['ubicacion_origen_id'] ?? 0) === $u->id)>{{ $u->nombre }}</option>
            @endforeach
        </select>

        <select name="ubicacion_destino_id" class="rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="">Destino: todas</option>
            @foreach ($ubicaciones as $u)
                <option value="{{ $u->id }}" @selected((int) ($filtros['ubicacion_destino_id'] ?? 0) === $u->id)>{{ $u->nombre }}</option>
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

        <x-ui.button type="submit" variant="secondary">Filtrar</x-ui.button>
        @if (collect($filtros)->filter()->isNotEmpty())
            <x-ui.button :href="route('traslados.index')" variant="ghost">Limpiar</x-ui.button>
        @endif
    </form>

    @if ($traslados->isEmpty())
        <x-ui.card>
            <x-ui.empty-state icon="map-pin" title="Sin traslados"
                message="No hay traslados que coincidan con los filtros. Regístralos desde la hoja de vida de cada equipo." />
        </x-ui.card>
    @else
        <x-ui.table>
            <x-slot name="head">
                <th class="px-4 py-3">Fecha</th>
                <th class="px-4 py-3">Equipo</th>
                <th class="px-4 py-3">Origen &rarr; Destino</th>
                <th class="px-4 py-3">Motivo</th>
                <th class="px-4 py-3">Registró</th>
            </x-slot>

            @foreach ($traslados as $traslado)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-600">{{ $traslado->fecha->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 font-medium text-gray-900">
                        <a href="{{ route('equipos.show', $traslado->equipo) }}" class="text-brand-700 hover:underline">
                            {{ $traslado->equipo->codigo_interno }}
                        </a>
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ $traslado->ubicacionOrigen->nombre }}
                        <span class="text-gray-400">&rarr;</span>
                        <span class="font-medium text-gray-900">{{ $traslado->ubicacionDestino->nombre }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $traslado->motivo->label() }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $traslado->registradoPor?->name ?? '—' }}</td>
                </tr>
            @endforeach
        </x-ui.table>

        <div class="mt-4">{{ $traslados->links() }}</div>
    @endif
</x-app-layout>
