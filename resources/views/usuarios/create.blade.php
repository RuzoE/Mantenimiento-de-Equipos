<x-app-layout>
    <x-slot name="title">Nuevo usuario</x-slot>
    <x-slot name="header">Nuevo usuario</x-slot>
    <x-slot name="subheader">Cree una cuenta de acceso y asígnele uno o más roles.</x-slot>

    <x-ui.card class="max-w-2xl">
        @include('usuarios.partials.form')
    </x-ui.card>
</x-app-layout>
