<x-app-layout>
    <x-slot name="title">Registrar traslado</x-slot>
    <x-slot name="header">Registrar traslado</x-slot>
    <x-slot name="subheader">Equipo {{ $equipo->codigo_interno }}</x-slot>

    <x-ui.card class="max-w-2xl">
        <form method="POST" action="{{ route('equipos.traslados.store', $equipo) }}" class="space-y-6">
            @csrf
            @include('traslados.partials.form')

            <div class="flex items-center gap-3 border-t border-gray-100 pt-5">
                <x-ui.button type="submit">Registrar traslado</x-ui.button>
                <x-ui.button :href="route('equipos.show', $equipo)" variant="ghost">Cancelar</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-app-layout>
