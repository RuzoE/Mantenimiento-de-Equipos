<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <x-ui.card title="Información general">
        <dl class="divide-y divide-gray-100 text-sm">
            <x-ui.field label="Código interno">{{ $equipo->codigo_interno }}</x-ui.field>
            <x-ui.field label="Tipo de equipo">{{ $equipo->tipoEquipo->nombre }}</x-ui.field>
            <x-ui.field label="Marca">{{ $equipo->marca->nombre }}</x-ui.field>
            <x-ui.field label="Modelo">{{ $equipo->modelo }}</x-ui.field>
            <x-ui.field label="Número de serie">{{ $equipo->numero_serie }}</x-ui.field>
            <x-ui.field label="Fecha de adquisición">
                {{ $equipo->fecha_adquisicion?->format('d/m/Y') }}
            </x-ui.field>
            <x-ui.field label="Antigüedad">{{ $equipo->antiguedad }}</x-ui.field>
            <x-ui.field label="Fin de garantía">
                @if ($equipo->fecha_garantia)
                    <span class="inline-flex items-center gap-2">
                        {{ $equipo->fecha_garantia->format('d/m/Y') }}
                        <x-ui.badge :color="$equipo->garantia_vigente ? 'green' : 'red'">
                            {{ $equipo->garantia_vigente ? 'Vigente' : 'Vencida' }}
                        </x-ui.badge>
                    </span>
                @endif
            </x-ui.field>
        </dl>
    </x-ui.card>

    <x-ui.card title="Estado, ubicación y responsable">
        <dl class="divide-y divide-gray-100 text-sm">
            <x-ui.field label="Estado">
                <x-ui.badge :color="$equipo->estado->color()">{{ $equipo->estado->label() }}</x-ui.badge>
            </x-ui.field>
            <x-ui.field label="Ubicación">{{ $equipo->ubicacion->nombre }}</x-ui.field>
            <x-ui.field label="Responsable">{{ $equipo->responsable?->nombre ?? 'Sin asignar' }}</x-ui.field>
            <x-ui.field label="Registro">{{ $equipo->activo ? 'Activo' : 'Inactivo' }}</x-ui.field>
            <x-ui.field label="Última actualización">
                {{ $equipo->updated_at?->format('d/m/Y H:i') }}
            </x-ui.field>
        </dl>
    </x-ui.card>
</div>
