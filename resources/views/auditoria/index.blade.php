<x-app-layout>
    <x-slot name="title">Auditoría</x-slot>
    <x-slot name="header">Auditoría</x-slot>
    <x-slot name="subheader">Registro de acciones importantes realizadas en el sistema.</x-slot>

    <form method="GET" action="{{ route('auditoria.index') }}"
          class="mb-4 grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-6 lg:items-end">
        <input type="text" name="buscar" value="{{ $filtros['buscar'] ?? '' }}" placeholder="Buscar en la descripción"
               class="col-span-2 rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">

        <select name="modulo" class="rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="">Módulo: todos</option>
            @foreach ($modulos as $modulo)
                <option value="{{ $modulo }}" @selected(($filtros['modulo'] ?? '') === $modulo)>{{ $modulo }}</option>
            @endforeach
        </select>

        <select name="evento" class="rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="">Evento: todos</option>
            @foreach ($eventos as $evento)
                <option value="{{ $evento->value }}" @selected(($filtros['evento'] ?? '') === $evento->value)>{{ $evento->label() }}</option>
            @endforeach
        </select>

        <select name="user_id" class="rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="">Usuario: todos</option>
            @foreach ($usuarios as $usuario)
                <option value="{{ $usuario->id }}" @selected((int) ($filtros['user_id'] ?? 0) === $usuario->id)>{{ $usuario->name }}</option>
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

        <div class="col-span-2 flex gap-2 sm:col-span-3 lg:col-span-6">
            <x-ui.button type="submit" variant="secondary">Filtrar</x-ui.button>
            @if (collect($filtros)->filter()->isNotEmpty())
                <x-ui.button :href="route('auditoria.index')" variant="ghost">Limpiar</x-ui.button>
            @endif
        </div>
    </form>

    @if ($auditorias->isEmpty())
        <x-ui.card>
            <x-ui.empty-state icon="clipboard" title="Sin registros"
                message="No hay acciones auditadas que coincidan con los filtros." />
        </x-ui.card>
    @else
        <x-ui.table>
            <x-slot name="head">
                <th class="px-4 py-3">Fecha y hora</th>
                <th class="px-4 py-3">Usuario</th>
                <th class="px-4 py-3">Módulo</th>
                <th class="px-4 py-3">Evento</th>
                <th class="px-4 py-3">Acción</th>
            </x-slot>

            @foreach ($auditorias as $auditoria)
                <tr class="align-top hover:bg-gray-50">
                    <td class="whitespace-nowrap px-4 py-3 text-gray-600">{{ $auditoria->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $auditoria->actor() }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $auditoria->modulo }}</td>
                    <td class="px-4 py-3">
                        <x-ui.badge :color="$auditoria->evento->color()">{{ $auditoria->evento->label() }}</x-ui.badge>
                    </td>
                    <td class="px-4 py-3 text-gray-700">
                        {{ ucfirst($auditoria->descripcion) }}
                        @if (! empty($auditoria->cambios))
                            <ul class="mt-1 space-y-0.5 text-xs text-gray-500">
                                @foreach ($auditoria->cambios as $campo => $valor)
                                    <li>
                                        <span class="font-medium">{{ $campo }}:</span>
                                        <span class="line-through">{{ $valor['antes'] ?? '—' }}</span>
                                        <span class="text-gray-400">&rarr;</span>
                                        {{ $valor['despues'] ?? '—' }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </td>
                </tr>
            @endforeach
        </x-ui.table>

        <div class="mt-4">{{ $auditorias->links() }}</div>
    @endif
</x-app-layout>
