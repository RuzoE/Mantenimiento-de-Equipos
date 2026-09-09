<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    {{-- Próximo mantenimiento (se conecta en la FASE 7) --}}
    <x-ui.card title="Próximo mantenimiento">
        @isset($proximoMantenimiento)
            {{-- Reservado para la FASE 7 --}}
        @else
            <div class="flex flex-col items-center py-4 text-center">
                <span class="grid h-11 w-11 place-items-center rounded-full bg-gray-100 text-gray-400">
                    <x-ui.icon name="calendar" class="h-6 w-6" />
                </span>
                <p class="mt-3 text-sm font-medium text-gray-900">Sin programación</p>
                <p class="mt-1 text-xs text-gray-500">La programación de mantenimientos se habilita en la FASE 7.</p>
            </div>
        @endisset
    </x-ui.card>

    {{-- Historial --}}
    <x-ui.card title="Historial" class="lg:col-span-2">
        <div class="space-y-5">
            <section>
                <h4 class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-400">Mantenimientos</h4>
                <x-ui.empty-state icon="wrench" title="Sin mantenimientos registrados"
                    message="El registro de mantenimientos preventivos y correctivos se habilita en la FASE 6." />
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
