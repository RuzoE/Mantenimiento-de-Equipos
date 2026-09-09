<x-app-layout>
    <x-slot name="title">Editar mantenimiento</x-slot>
    <x-slot name="header">Editar mantenimiento</x-slot>
    <x-slot name="subheader">Equipo {{ $equipo->codigo_interno }} · {{ $mantenimiento->fecha->format('d/m/Y') }}</x-slot>

    <x-ui.card class="max-w-3xl">
        <form method="POST" action="{{ route('mantenimientos.update', $mantenimiento) }}"
              enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            @include('mantenimientos.partials.form')

            <div class="flex items-center gap-3 border-t border-gray-100 pt-5">
                <x-ui.button type="submit">Guardar cambios</x-ui.button>
                <x-ui.button :href="route('mantenimientos.show', $mantenimiento)" variant="ghost">Cancelar</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-app-layout>
