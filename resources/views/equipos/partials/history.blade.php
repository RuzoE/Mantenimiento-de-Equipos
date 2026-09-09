<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    {{-- Próximo mantenimiento (se conecta en la FASE 7) --}}
    <x-ui.card title="Próximo mantenimiento">
        <div class="flex flex-col items-center py-4 text-center">
            <span class="grid h-11 w-11 place-items-center rounded-full bg-gray-100 text-gray-400">
                <x-ui.icon name="calendar" class="h-6 w-6" />
            </span>
            <p class="mt-3 text-sm font-medium text-gray-900">Sin programación</p>
            <p class="mt-1 text-xs text-gray-500">La programación de mantenimientos se habilita en la FASE 7.</p>
        </div>
    </x-ui.card>

    {{-- Historial --}}
    <x-ui.card class="lg:col-span-2">
        <x-slot name="actions">
            @can('create', \App\Models\Mantenimiento::class)
                <x-ui.button :href="route('equipos.mantenimientos.create', $equipo)" size="sm">Registrar mantenimiento</x-ui.button>
            @endcan
        </x-slot>
        <x-slot name="title">Historial</x-slot>

        <div class="space-y-6">
            <section>
                <h4 class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-400">Mantenimientos</h4>
                @if ($equipo->mantenimientos->isEmpty())
                    <x-ui.empty-state icon="wrench" title="Sin mantenimientos registrados"
                        message="Aún no se ha registrado ningún mantenimiento para este equipo." />
                @else
                    <ul class="divide-y divide-gray-100 text-sm">
                        @foreach ($equipo->mantenimientos as $mantenimiento)
                            <li class="flex items-center justify-between gap-3 py-2">
                                <div class="min-w-0">
                                    <a href="{{ route('mantenimientos.show', $mantenimiento) }}"
                                       class="font-medium text-brand-700 hover:underline">
                                        {{ $mantenimiento->fecha->format('d/m/Y') }}
                                    </a>
                                    <span class="text-gray-500">
                                        · {{ $mantenimiento->responsable?->nombre ?? 'Sin técnico' }}
                                    </span>
                                    @if ($mantenimiento->descripcion)
                                        <p class="truncate text-xs text-gray-500">{{ $mantenimiento->descripcion }}</p>
                                    @endif
                                </div>
                                <x-ui.badge :color="$mantenimiento->tipo->color()">{{ $mantenimiento->tipo->label() }}</x-ui.badge>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            <section>
                <h4 class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-400">Traslados</h4>
                <x-ui.empty-state icon="map-pin" title="Sin traslados registrados"
                    message="El historial de cambios de ubicación se habilita en la FASE 8." />
            </section>

            <section>
                <h4 class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-400">Cambios y novedades</h4>
                <x-ui.empty-state icon="clipboard" title="Sin cambios registrados"
                    message="La auditoría de cambios sobre el equipo se habilita en la FASE 11." />
            </section>
        </div>
    </x-ui.card>
</div>
