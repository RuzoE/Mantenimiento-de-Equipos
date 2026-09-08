<x-app-layout>
    <x-slot name="title">Editar usuario</x-slot>
    <x-slot name="header">Editar usuario</x-slot>
    <x-slot name="subheader">{{ $usuario->name }} &middot; {{ $usuario->email }}</x-slot>

    <x-ui.card class="max-w-2xl">
        @include('usuarios.partials.form')
    </x-ui.card>
</x-app-layout>
