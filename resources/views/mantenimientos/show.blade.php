<x-app-layout>
    <x-slot name="title">Mantenimiento · {{ $mantenimiento->equipo->codigo_interno }}</x-slot>
    <x-slot name="header">Mantenimiento {{ $mantenimiento->tipo->label() }}</x-slot>
    <x-slot name="subheader">
        Equipo {{ $mantenimiento->equipo->codigo_interno }} · {{ $mantenimiento->fecha->format('d/m/Y') }}
    </x-slot>

    <div class="mb-5 flex flex-wrap items-center gap-3">
        <x-ui.badge :color="$mantenimiento->tipo->color()">{{ $mantenimiento->tipo->label() }}</x-ui.badge>
        <div class="flex-1"></div>
        @can('update', $mantenimiento)
            <x-ui.button :href="route('mantenimientos.edit', $mantenimiento)" variant="secondary" size="sm">Editar</x-ui.button>
        @endcan
        @can('delete', $mantenimiento)
            <form method="POST" action="{{ route('mantenimientos.destroy', $mantenimiento) }}"
                  onsubmit="return confirm('¿Eliminar este mantenimiento? Esta acción no se puede deshacer.')">
                @csrf
                @method('DELETE')
                <x-ui.button type="submit" variant="danger" size="sm">Eliminar</x-ui.button>
            </form>
        @endcan
        <x-ui.button :href="route('equipos.show', $mantenimiento->equipo)" variant="ghost" size="sm">Ver equipo</x-ui.button>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <x-ui.card title="Datos del mantenimiento">
            <dl class="divide-y divide-gray-100 text-sm">
                <x-ui.field label="Equipo">
                    <a href="{{ route('equipos.show', $mantenimiento->equipo) }}" class="text-brand-700 hover:underline">
                        {{ $mantenimiento->equipo->codigo_interno }}
                    </a>
                </x-ui.field>
                <x-ui.field label="Tipo">{{ $mantenimiento->tipo->label() }}</x-ui.field>
                <x-ui.field label="Fecha">{{ $mantenimiento->fecha->format('d/m/Y') }}</x-ui.field>
                <x-ui.field label="Técnico / responsable">{{ $mantenimiento->responsable?->nombre }}</x-ui.field>
                <x-ui.field label="Registrado por">{{ $mantenimiento->registradoPor?->name }}</x-ui.field>
                <x-ui.field label="Estado antes">
                    @if ($mantenimiento->estado_antes)
                        <x-ui.badge :color="$mantenimiento->estado_antes->color()">{{ $mantenimiento->estado_antes->label() }}</x-ui.badge>
                    @endif
                </x-ui.field>
                <x-ui.field label="Estado después">
                    @if ($mantenimiento->estado_despues)
                        <x-ui.badge :color="$mantenimiento->estado_despues->color()">{{ $mantenimiento->estado_despues->label() }}</x-ui.badge>
                    @endif
                </x-ui.field>
            </dl>
        </x-ui.card>

        <x-ui.card title="Actividades realizadas">
            @if ($mantenimiento->actividades->isEmpty())
                <x-ui.empty-state title="Sin actividades" message="No se registraron actividades para este mantenimiento." />
            @else
                <ul class="list-disc space-y-1 pl-5 text-sm text-gray-700">
                    @foreach ($mantenimiento->actividades as $actividad)
                        <li>{{ $actividad->descripcion }}</li>
                    @endforeach
                </ul>
            @endif
        </x-ui.card>

        <x-ui.card title="Descripción" class="lg:col-span-2">
            <p class="whitespace-pre-line text-sm text-gray-700">{{ $mantenimiento->descripcion }}</p>
            @if ($mantenimiento->observaciones)
                <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-gray-400">Observaciones</p>
                <p class="mt-1 whitespace-pre-line text-sm text-gray-700">{{ $mantenimiento->observaciones }}</p>
            @endif
        </x-ui.card>

        <x-ui.card title="Evidencias" class="lg:col-span-2">
            @if ($mantenimiento->evidencias->isEmpty())
                <x-ui.empty-state title="Sin evidencias" message="No se adjuntaron archivos a este mantenimiento." />
            @else
                <ul class="divide-y divide-gray-100 text-sm">
                    @foreach ($mantenimiento->evidencias as $ev)
                        <li class="flex items-center justify-between gap-3 py-2">
                            <a href="{{ route('evidencias.show', $ev) }}" class="truncate text-brand-700 hover:underline">
                                {{ $ev->nombre_original }}
                            </a>
                            <span class="text-xs text-gray-400">{{ $ev->tamanoLegible() }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-ui.card>
    </div>
</x-app-layout>
