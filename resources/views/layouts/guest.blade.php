<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' · ' : '' }}{{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-100 font-sans text-gray-900 antialiased">
    <div class="flex min-h-full flex-col items-center justify-center px-4 py-12">
        <div class="flex flex-col items-center gap-3">
            <a href="/" class="grid h-16 w-16 place-items-center rounded-2xl bg-brand-600 shadow-sm">
                <x-application-logo class="h-9 w-9 fill-current text-white" />
            </a>
            <div class="text-center">
                <p class="text-base font-semibold text-gray-900">Mantenimiento de Equipos</p>
                <p class="text-xs text-gray-500">I.E. Policarpa Salavarrieta</p>
            </div>
        </div>

        <div class="mt-6 w-full overflow-hidden rounded-xl bg-white px-6 py-6 shadow-sm ring-1 ring-gray-200 sm:max-w-md">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
