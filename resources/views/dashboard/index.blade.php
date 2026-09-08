<x-app-layout>
    <x-slot name="title">Panel principal</x-slot>
    <x-slot name="header">Panel principal</x-slot>
    <x-slot name="subheader">Resumen general del inventario y los mantenimientos.</x-slot>

    {{-- Indicadores --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-ui.stat-card label="Equipos registrados" :value="$stats['equipos_total']" icon="desktop" color="brand" />
        <x-ui.stat-card label="Operativos" :value="$stats['equipos_operativos']" icon="check" color="green" />
        <x-ui.stat-card label="Con novedades" :value="$stats['equipos_novedad']" icon="alert" color="yellow" />
        <x-ui.stat-card label="En mantenimiento" :value="$stats['equipos_mantenimiento']" icon="wrench" color="blue" />
        <x-ui.stat-card label="Fuera de servicio" :value="$stats['equipos_fuera_servicio']" icon="ban" color="red" />
        <x-ui.stat-card label="Mantenimientos próximos" :value="$stats['mantenimientos_proximos']" icon="calendar" color="indigo" />
        <x-ui.stat-card label="Mantenimientos vencidos" :value="$stats['mantenimientos_vencidos']" icon="clock" color="red" />
    </div>

    {{-- Paneles --}}
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <x-ui.card title="Próximos mantenimientos" subtitle="Programaciones con fecha cercana">
            <x-ui.empty-state
                icon="calendar"
                title="Aún no hay programaciones"
                message="Cuando registres equipos y su programación de mantenimiento, aquí verás los próximos vencimientos." />
        </x-ui.card>

        <x-ui.card title="Actividad reciente" subtitle="Últimas acciones registradas">
            <x-ui.empty-state
                icon="clipboard"
                title="Sin actividad todavía"
                message="Las acciones importantes del sistema (altas, cambios, mantenimientos) aparecerán aquí." />
        </x-ui.card>
    </div>

    <p class="mt-6 text-xs text-gray-400">
        Los indicadores mostrarán datos reales a partir de la FASE 9, cuando existan los módulos de equipos y mantenimientos.
    </p>
</x-app-layout>
