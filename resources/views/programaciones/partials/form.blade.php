@php $p = $programacion ?? null; @endphp

<div class="space-y-6">
    <div class="rounded-lg bg-gray-50 px-4 py-3 text-sm">
        <span class="text-gray-500">Equipo:</span>
        <span class="font-medium text-gray-900">{{ $equipo->codigo_interno }}</span>
        <span class="text-gray-500"> · {{ $equipo->tipoEquipo->nombre }} · {{ $equipo->marca->nombre }}</span>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <x-ui.select name="tipo" label="Tipo de mantenimiento" required placeholder="Seleccione…">
            @foreach ($tipos as $t)
                <option value="{{ $t->value }}" @selected(old('tipo', $p?->tipo->value) === $t->value)>{{ $t->label() }}</option>
            @endforeach
        </x-ui.select>

        <x-ui.select name="frecuencia" label="Frecuencia" required placeholder="Seleccione…">
            @foreach ($frecuencias as $f)
                <option value="{{ $f->value }}" @selected(old('frecuencia', $p?->frecuencia->value) === $f->value)>
                    {{ $f->label() }} (cada {{ $f->dias() }} días)
                </option>
            @endforeach
        </x-ui.select>

        <x-ui.input name="fecha_ultimo_mantenimiento" type="date" label="Fecha del último mantenimiento"
                    :value="optional($p?->fecha_ultimo_mantenimiento)->format('Y-m-d')"
                    hint="Opcional. Si se deja vacía, la próxima fecha se cuenta desde hoy." />
    </div>

    <x-ui.textarea name="observaciones" label="Observaciones" rows="2" :value="old('observaciones', $p?->observaciones)" />

    <p class="rounded-lg bg-brand-50 px-3 py-2 text-xs text-brand-800">
        La <span class="font-semibold">próxima fecha</span> se calcula automáticamente:
        la fecha del último mantenimiento (o la de hoy) más los días de la frecuencia elegida.
    </p>
</div>
