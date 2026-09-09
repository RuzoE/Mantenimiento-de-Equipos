<x-app-layout>
    <x-slot name="title">Mantenimientos</x-slot>
    <x-slot name="header">Mantenimientos</x-slot>
    <x-slot name="subheader">Historial de mantenimientos preventivos y correctivos.</x-slot>

    <form method="GET" action="{{ route('mantenimientos.index') }}"
          class="mb-4 grid grid-cols-2 gap-2 sm:grid-cols-3 lg:flex lg:flex-wrap lg:items-end">
        <input type="text" name="buscar" value="{{ $filtros['buscar'] ?? '' }}" placeholder="Código de equipo"
               class="col-span-2 rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 sm:col-span-1 lg:w-48">

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

        <x-ui.button type="submit" variant="secondary">Filtrar</x-ui.button>
        @if (collect($filtros)->filter()->isNotEmpty())
            <x-ui.button :href="route('mantenimientos.index')" variant="ghost">Limpiar</x-ui.button>
        @endif
    </form>

    @if ($mantenimientos->isEmpty())
        <x-ui.card>
            <x-ui.empty-state icon="wrench" title="Sin mantenimientos"
                message="No hay mantenimientos que coincidan con los filtros. Regístrelos desde la hoja de vida de cada equipo." />
        </x-ui.card>
    @else
        <x-ui.table>
            <x-slot name="head">
                <th class="px-4 py-3">Fecha</th>
                <th class="px-4 py-3">Equipo</th>
                <th class="px-4 py-3">Tipo</th>
                <th class="px-4 py-3">Técnico</th>
                <th class="px-4 py-3">Estado después</th>
                <th class="px-4 py-3 text-right">Acciones</th>
            </x-slot>

            @foreach ($mantenimientos as $mantenimiento)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-600">{{ $mantenimiento->fecha->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $mantenimiento->equipo->codigo_interno }}</td>
                    <td class="px-4 py-3">
                        <x-ui.badge :color="$mantenimiento->tipo->color()">{{ $mantenimiento->tipo->label() }}</x-ui.badge>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $mantenimiento->responsable?->nombre ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @if ($mantenimiento->estado_despues)
                            <x-ui.badge :color="$mantenimiento->estado_despues->color()">{{ $mantenimiento->estado_despues->label() }}</x-ui.badge>
                        @else
                            <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <x-ui.button :href="route('mantenimientos.show', $mantenimiento)" variant="secondary" size="sm">Ver</x-ui.button>
                    </td>
                </tr>
            @endforeach
        </x-ui.table>

        <div class="mt-4">{{ $mantenimientos->links() }}</div>
    @endif
</x-app-layout>
