<x-app-layout>
    <x-slot name="title">Reportes</x-slot>
    <x-slot name="header">Reportes</x-slot>
    <x-slot name="subheader">Consulte y exporte la información del inventario y los mantenimientos.</x-slot>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <a href="{{ route('reportes.equipos') }}"
           class="group rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 transition hover:ring-brand-300">
            <span class="grid h-11 w-11 place-items-center rounded-lg bg-brand-50 text-brand-600">
                <x-ui.icon name="desktop" class="h-6 w-6" />
            </span>
            <h3 class="mt-4 text-sm font-semibold text-gray-900 group-hover:text-brand-700">Reporte de equipos</h3>
            <p class="mt-1 text-sm text-gray-500">
                Inventario filtrable por tipo, marca, ubicación, estado y responsable, con totales por categoría.
            </p>
        </a>

        <a href="{{ route('reportes.mantenimientos') }}"
           class="group rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 transition hover:ring-brand-300">
            <span class="grid h-11 w-11 place-items-center rounded-lg bg-brand-50 text-brand-600">
                <x-ui.icon name="wrench" class="h-6 w-6" />
            </span>
            <h3 class="mt-4 text-sm font-semibold text-gray-900 group-hover:text-brand-700">Reporte de mantenimientos</h3>
            <p class="mt-1 text-sm text-gray-500">
                Mantenimientos preventivos y correctivos por equipo, técnico y rango de fechas, con totales.
            </p>
        </a>
    </div>
</x-app-layout>
