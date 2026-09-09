<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    {{-- Próximo mantenimiento --}}
    @php
        $proxima = $equipo->programaciones
            ->reject(fn ($prog) => $prog->estado === \App\Enums\EstadoProgramacion::Cancelado)
            ->sortBy('proxima_fecha')
            ->first();
    @endphp
    <x-ui.card title="Próximo mantenimiento">
        @if ($proxima)
            @php $estadoProg = $proxima->estadoActual(); $diasProg = $proxima->diasParaProxima(); @endphp
            <div class="space-y-2 text-sm">
                <p class="text-2xl font-semibold text-gray-900">{{ $proxima->proxima_fecha->format('d/m/Y') }}</p>
                <p class="text-gray-500">
                    {{ $proxima->tipo->label() }} · {{ $proxima->frecuencia->label() }}
                </p>
                <div class="flex items-center gap-2">
                    <x-ui.badge :color="$estadoProg->color()">{{ $estadoProg->label() }}</x-ui.badge>
                    <span class="text-xs text-gray-400">
                        {{ $diasProg < 0 ? abs($diasProg).' días de retraso' : ($diasProg === 0 ? 'hoy' : "en {$diasProg} días") }}
                    </span>
                </div>
                @can('update', $proxima)
                    <a href="{{ route('programaciones.edit', $proxima) }}"
                       class="inline-block pt-1 text-xs font-medium text-brand-700 hover:underline">Editar programación</a>
                @endcan
            </div>
        @else
            <div class="flex flex-col items-center py-4 text-center">
                <span class="grid h-11 w-11 place-items-center rounded-full bg-gray-100 text-gray-400">
                    <x-ui.icon name="calendar" class="h-6 w-6" />
                </span>
                <p class="mt-3 text-sm font-medium text-gray-900">Sin programación</p>
                <p class="mt-1 text-xs text-gray-500">Este equipo no tiene mantenimientos programados.</p>
                @can('create', \App\Models\Programacion::class)
                    <x-ui.button :href="route('equipos.programaciones.create', $equipo)" size="sm" class="mt-3">
                        Programar mantenimiento
                    </x-ui.button>
                @endcan
            </div>
        @endif
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
