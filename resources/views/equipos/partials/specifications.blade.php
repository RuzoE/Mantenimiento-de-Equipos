@php
    $specs = array_filter([
        'Procesador' => $equipo->procesador,
        'Memoria RAM' => $equipo->memoria_ram,
        'Almacenamiento' => $equipo->almacenamiento,
        'Sistema operativo' => $equipo->sistema_operativo,
    ], 'filled');
@endphp

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <x-ui.card title="Especificaciones">
        @if ($specs === [])
            <x-ui.empty-state title="Sin especificaciones registradas"
                message="Edite el equipo para añadir procesador, memoria, almacenamiento o sistema operativo." />
        @else
            <dl class="divide-y divide-gray-100 text-sm">
                @foreach ($specs as $label => $valor)
                    <x-ui.field :label="$label">{{ $valor }}</x-ui.field>
                @endforeach
            </dl>
        @endif
    </x-ui.card>

    <x-ui.card title="Observaciones">
        @if ($equipo->observaciones)
            <p class="whitespace-pre-line text-sm text-gray-700">{{ $equipo->observaciones }}</p>
        @else
            <x-ui.empty-state title="Sin observaciones" message="No se han registrado observaciones para este equipo." />
        @endif
    </x-ui.card>
</div>
