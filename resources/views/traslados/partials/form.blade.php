<div class="space-y-6">
    <div class="rounded-lg bg-gray-50 px-4 py-3 text-sm">
        <span class="text-gray-500">Equipo:</span>
        <span class="font-medium text-gray-900">{{ $equipo->codigo_interno }}</span>
        <span class="text-gray-500"> · Ubicación actual:</span>
        <span class="font-medium text-gray-900">{{ $equipo->ubicacion->nombre }}</span>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <x-ui.select name="ubicacion_destino_id" label="Nueva ubicación" required placeholder="Seleccione…">
            @foreach ($ubicaciones as $u)
                <option value="{{ $u->id }}" @selected((int) old('ubicacion_destino_id', 0) === $u->id)>{{ $u->nombre }}</option>
            @endforeach
        </x-ui.select>

        <x-ui.input name="fecha" type="date" label="Fecha del traslado" required
                    :value="old('fecha', now()->format('Y-m-d'))" />

        <x-ui.select name="motivo" label="Motivo" required placeholder="Seleccione…">
            @foreach ($motivos as $m)
                <option value="{{ $m->value }}" @selected(old('motivo') === $m->value)>{{ $m->label() }}</option>
            @endforeach
        </x-ui.select>
    </div>

    <x-ui.textarea name="observaciones" label="Observaciones" rows="2" :value="old('observaciones')" />
</div>
