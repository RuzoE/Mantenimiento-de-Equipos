<x-app-layout>
    <x-slot name="title">Editar programación</x-slot>
    <x-slot name="header">Editar programación</x-slot>
    <x-slot name="subheader">Equipo {{ $equipo->codigo_interno }}</x-slot>

    <x-ui.card class="max-w-2xl">
        <form method="POST" action="{{ route('programaciones.update', $programacion) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('programaciones.partials.form')

            <div class="flex items-center gap-3 border-t border-gray-100 pt-5">
                <x-ui.button type="submit">Guardar cambios</x-ui.button>
                <x-ui.button :href="route('equipos.show', $equipo)" variant="ghost">Cancelar</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-app-layout>
