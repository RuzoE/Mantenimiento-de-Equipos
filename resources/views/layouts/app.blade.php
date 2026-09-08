<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' · ' : '' }}{{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-50 font-sans text-gray-900 antialiased">
    <div x-data="{ sidebarOpen: false }" class="min-h-full">
        {{-- Barra lateral --}}
        @include('layouts.partials.sidebar')

        {{-- Capa oscura para móvil --}}
        <div x-show="sidebarOpen"
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 z-30 bg-gray-900/50 lg:hidden"
             style="display: none;"></div>

        {{-- Columna de contenido --}}
        <div class="flex min-h-full flex-col lg:pl-64">
            @include('layouts.partials.navbar')

            <main class="flex-1 py-8">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    @isset($header)
                        <div class="mb-6">
                            <h1 class="text-2xl font-semibold tracking-tight text-gray-900">{{ $header }}</h1>
                            @isset($subheader)
                                <p class="mt-1 text-sm text-gray-500">{{ $subheader }}</p>
                            @endisset
                        </div>
                    @endisset

                    @include('layouts.partials.alerts')

                    {{ $slot }}
                </div>
            </main>

            @include('layouts.partials.footer')
        </div>
    </div>
</body>
</html>
