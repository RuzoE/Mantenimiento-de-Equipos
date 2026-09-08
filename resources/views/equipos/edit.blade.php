<x-app-layout>
    <x-slot name="title">Editar {{ $equipo->codigo_interno }}</x-slot>
    <x-slot name="header">Editar equipo</x-slot>
    <x-slot name="subheader">{{ $equipo->codigo_interno }}</x-slot>

    <x-ui.card class="max-w-3xl">
        <form method="POST" action="{{ route('equipos.update', $equipo) }}" class="space-y-8">
            @csrf
            @method('PUT')
            @include('equipos.partials.form')

            <div class="flex items-center gap-3 border-t border-gray-100 pt-5">
                <x-ui.button type="submit">Guardar cambios</x-ui.button>
                <x-ui.button :href="route('equipos.show', $equipo)" variant="ghost">Cancelar</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-app-layout>
