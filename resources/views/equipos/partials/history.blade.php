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
            <div class="flex flex-wrap gap-2">
                @can('create', \App\Models\Mantenimiento::class)
                    <x-ui.button :href="route('equipos.mantenimientos.create', $equipo)" size="sm">Registrar mantenimiento</x-ui.button>
                @endcan
                @can('create', \App\Models\Traslado::class)
                    <x-ui.button :href="route('equipos.traslados.create', $equipo)" size="sm" variant="secondary">Registrar traslado</x-ui.button>
                @endcan
            </div>
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
                @php $traslados = $equipo->traslados->sortByDesc('fecha')->sortByDesc('id')->values(); @endphp
                @if ($traslados->isEmpty())
                    <x-ui.empty-state icon="map-pin" title="Sin traslados registrados"
                        message="Este equipo no ha cambiado de ubicación." />
                @else
                    <ul class="divide-y divide-gray-100 text-sm">
                        @foreach ($traslados as $traslado)
                            <li class="flex items-start justify-between gap-3 py-2">
                                <div class="min-w-0">
                                    <span class="font-medium text-gray-900">{{ $traslado->fecha->format('d/m/Y') }}</span>
                                    <span class="text-gray-500">
                                        · {{ $traslado->ubicacionOrigen->nombre }}
                                        <span class="text-gray-400">&rarr;</span>
                                        {{ $traslado->ubicacionDestino->nombre }}
                                    </span>
                                    <p class="text-xs text-gray-500">
                                        {{ $traslado->motivo->label() }}@if ($traslado->observaciones) · {{ $traslado->observaciones }}@endif
                                    </p>
                                </div>
                                @can('delete', $traslado)
                                    <form method="POST" action="{{ route('traslados.destroy', $traslado) }}"
                                          onsubmit="return confirm('¿Deshacer este traslado? El equipo volverá a {{ $traslado->ubicacionOrigen->nombre }}.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="shrink-0 text-xs text-red-600 hover:underline">Deshacer</button>
                                    </form>
                                @endcan
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            <section>
                <h4 class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-400">Cambios y novedades</h4>
                @if ($equipo->auditorias->isEmpty())
                    <x-ui.empty-state icon="clipboard" title="Sin cambios registrados"
                        message="No se han registrado cambios sobre este equipo." />
                @else
                    <ul class="divide-y divide-gray-100 text-sm">
                        @foreach ($equipo->auditorias as $auditoria)
                            <li class="py-2">
                                <p class="text-gray-800">
                                    <span class="font-medium">{{ $auditoria->actor() }}</span>
                                    {{ $auditoria->descripcion }}
                                </p>
                                <p class="text-xs text-gray-400">{{ $auditoria->created_at->diffForHumans() }}</p>
                                @if (! empty($auditoria->cambios))
                                    <ul class="mt-1 space-y-0.5 text-xs text-gray-500">
                                        @foreach ($auditoria->cambios as $campo => $valor)
                                            <li>
                                                <span class="font-medium">{{ $campo }}:</span>
                                                <span class="line-through">{{ $valor['antes'] ?? '—' }}</span>
                                                <span class="text-gray-400">&rarr;</span>
                                                {{ $valor['despues'] ?? '—' }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>
    </x-ui.card>
</div>
