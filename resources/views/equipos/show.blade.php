<x-app-layout>
    <x-slot name="title">{{ $equipo->codigo_interno }}</x-slot>
    <x-slot name="header">Hoja de vida · {{ $equipo->codigo_interno }}</x-slot>
    <x-slot name="subheader">{{ $equipo->tipoEquipo->nombre }} · {{ $equipo->marca->nombre }}{{ $equipo->modelo ? ' '.$equipo->modelo : '' }}</x-slot>

    <div class="mb-5 flex flex-wrap items-center gap-3">
        <x-ui.badge :color="$equipo->estado->color()">{{ $equipo->estado->label() }}</x-ui.badge>
        @if ($equipo->fecha_garantia)
            <x-ui.badge :color="$equipo->garantia_vigente ? 'green' : 'red'">
                Garantía {{ $equipo->garantia_vigente ? 'vigente' : 'vencida' }}
            </x-ui.badge>
        @endif
        @unless ($equipo->activo)
            <x-ui.badge color="gray">Registro inactivo</x-ui.badge>
        @endunless

        <div class="flex-1"></div>

        @can('update', $equipo)
            <x-ui.button :href="route('equipos.edit', $equipo)" variant="secondary" size="sm">Editar</x-ui.button>
        @endcan
        <x-ui.button :href="route('equipos.index')" variant="ghost" size="sm">Volver al listado</x-ui.button>
    </div>

    <div class="space-y-6">
        @include('equipos.partials.information')
        @include('equipos.partials.specifications')
        @include('equipos.partials.history')
    </div>
</x-app-layout>
