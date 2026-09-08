<x-app-layout>
    <x-slot name="title">Editar {{ $singular }}</x-slot>
    <x-slot name="header">Editar {{ $singular }}</x-slot>
    <x-slot name="subheader">{{ $item->nombre }}</x-slot>

    <x-ui.card class="max-w-xl">
        <form method="POST" action="{{ route($rutaBase . '.update', $item->id) }}" class="space-y-5">
            @csrf
            @method('PUT')
            @include('catalogos._form')

            <div class="flex items-center gap-3 border-t border-gray-100 pt-4">
                <x-ui.button type="submit">Guardar cambios</x-ui.button>
                <x-ui.button :href="route($rutaBase . '.index')" variant="ghost">Cancelar</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-app-layout>
