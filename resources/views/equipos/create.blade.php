<x-app-layout>
    <x-slot name="title">Nuevo equipo</x-slot>
    <x-slot name="header">Nuevo equipo</x-slot>
    <x-slot name="subheader">Registre un equipo tecnológico en el inventario.</x-slot>

    <x-ui.card class="max-w-3xl">
        <form method="POST" action="{{ route('equipos.store') }}" class="space-y-8">
            @csrf
            @include('equipos.partials.form')

            <div class="flex items-center gap-3 border-t border-gray-100 pt-5">
                <x-ui.button type="submit">Registrar equipo</x-ui.button>
                <x-ui.button :href="route('equipos.index')" variant="ghost">Cancelar</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-app-layout>
