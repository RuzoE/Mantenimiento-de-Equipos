@php
    $m = $mantenimiento ?? null;
    $actividadesIniciales = old('actividades', $m?->actividades->pluck('descripcion')->values()->all() ?? []);
    $estadoAntesDefault = old('estado_antes', $m?->estado_antes?->value ?? $equipo->estado->value);
    $estadoDespuesDefault = old('estado_despues', $m?->estado_despues?->value ?? '');
@endphp

<div class="space-y-6">
    <div class="rounded-lg bg-gray-50 px-4 py-3 text-sm">
        <span class="text-gray-500">Equipo:</span>
        <span class="font-medium text-gray-900">{{ $equipo->codigo_interno }}</span>
        <span class="text-gray-500"> · {{ $equipo->tipoEquipo->nombre }} · {{ $equipo->marca->nombre }}</span>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <x-ui.select name="tipo" label="Tipo de mantenimiento" required placeholder="Seleccione…">
            @foreach ($tipos as $t)
                <option value="{{ $t->value }}" @selected(old('tipo', $m?->tipo->value) === $t->value)>{{ $t->label() }}</option>
            @endforeach
        </x-ui.select>

        <x-ui.input name="fecha" type="date" label="Fecha" required
                    :value="old('fecha', optional($m?->fecha)->format('Y-m-d') ?: now()->format('Y-m-d'))" />

        <x-ui.select name="responsable_id" label="Técnico / responsable" placeholder="Sin asignar">
            @foreach ($responsables as $r)
                <option value="{{ $r->id }}" @selected((int) old('responsable_id', $m?->responsable_id ?? 0) === $r->id)>{{ $r->nombre }}</option>
            @endforeach
        </x-ui.select>

        <div class="hidden sm:block"></div>

        <x-ui.select name="estado_antes" label="Estado del equipo antes">
            @foreach ($estados as $e)
                <option value="{{ $e->value }}" @selected($estadoAntesDefault === $e->value)>{{ $e->label() }}</option>
            @endforeach
        </x-ui.select>

        <x-ui.select name="estado_despues" label="Estado del equipo después" placeholder="Sin cambio">
            @foreach ($estados as $e)
                <option value="{{ $e->value }}" @selected($estadoDespuesDefault === $e->value)>{{ $e->label() }}</option>
            @endforeach
        </x-ui.select>
    </div>

    <x-ui.textarea name="descripcion" label="Descripción del mantenimiento" required rows="3"
                   :value="old('descripcion', $m?->descripcion)" />

    <div>
        <x-ui.label class="mb-1">Actividades realizadas</x-ui.label>
        <div x-data="actividadesMantenimiento(@js($actividadesIniciales))" class="space-y-2">
            <template x-for="(item, i) in items" :key="i">
                <div class="flex gap-2">
                    <input type="text" :name="`actividades[${i}]`" x-model="items[i]"
                           placeholder="Ej.: Limpieza interna y cambio de pasta térmica"
                           class="block w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                    <button type="button" @click="remove(i)"
                            class="shrink-0 rounded-lg px-3 text-lg text-gray-400 hover:bg-gray-100 hover:text-red-600"
                            aria-label="Quitar actividad">&times;</button>
                </div>
            </template>
            <button type="button" @click="add()" class="text-sm font-medium text-brand-700 hover:underline">
                + Añadir actividad
            </button>
        </div>
        @error('actividades.*')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <x-ui.textarea name="observaciones" label="Observaciones" rows="2" :value="old('observaciones', $m?->observaciones)" />

    <div>
        <x-ui.label class="mb-1">Evidencias (opcional)</x-ui.label>
        <input type="file" name="evidencias[]" multiple
               accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx"
               class="block w-full text-sm text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100">
        <p class="mt-1 text-xs text-gray-500">Imágenes, PDF o documentos de Office. Máximo 5 MB por archivo.</p>
        @error('evidencias.*')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror

        @if ($m && $m->evidencias->isNotEmpty())
            <ul class="mt-3 divide-y divide-gray-100 rounded-lg border border-gray-200 text-sm">
                @foreach ($m->evidencias as $ev)
                    <li class="flex items-center justify-between gap-3 px-3 py-2">
                        <a href="{{ route('evidencias.show', $ev) }}" class="truncate text-brand-700 hover:underline">{{ $ev->nombre_original }}</a>
                        <span class="flex items-center gap-3">
                            <span class="text-xs text-gray-400">{{ $ev->tamanoLegible() }}</span>
                            <button type="submit" form="form-eliminar-evidencia-{{ $ev->id }}"
                                    class="text-xs text-red-600 hover:underline">Eliminar</button>
                        </span>
                    </li>
                @endforeach
            </ul>
            @foreach ($m->evidencias as $ev)
                <form id="form-eliminar-evidencia-{{ $ev->id }}" method="POST"
                      action="{{ route('evidencias.destroy', $ev) }}" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            @endforeach
        @endif
    </div>
</div>
