@php
    $e = $equipo ?? null;
    $estadoActual = old('estado', $e?->estado->value ?? \App\Enums\EstadoEquipo::Operativo->value);
@endphp

<div class="space-y-8">
    {{-- Identificación --}}
    <section>
        <h3 class="mb-4 text-sm font-semibold text-gray-900">Identificación</h3>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <x-ui.input name="codigo_interno" label="Código interno" required
                        :value="$e->codigo_interno ?? null" hint="Ej.: PC-001" />

            <x-ui.select name="tipo_equipo_id" label="Tipo de equipo" required placeholder="Seleccione…">
                @foreach ($tipos as $t)
                    <option value="{{ $t->id }}" @selected((int) old('tipo_equipo_id', $e->tipo_equipo_id ?? 0) === $t->id)>{{ $t->nombre }}</option>
                @endforeach
            </x-ui.select>

            <x-ui.select name="marca_id" label="Marca" required placeholder="Seleccione…">
                @foreach ($marcas as $m)
                    <option value="{{ $m->id }}" @selected((int) old('marca_id', $e->marca_id ?? 0) === $m->id)>{{ $m->nombre }}</option>
                @endforeach
            </x-ui.select>

            <x-ui.input name="modelo" label="Modelo" :value="$e->modelo ?? null" />

            <x-ui.input name="numero_serie" label="Número de serie" :value="$e->numero_serie ?? null" />
        </div>
    </section>

    {{-- Ubicación, responsable y estado --}}
    <section>
        <h3 class="mb-4 text-sm font-semibold text-gray-900">Ubicación, responsable y estado</h3>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <x-ui.select name="ubicacion_id" label="Ubicación" required placeholder="Seleccione…">
                @foreach ($ubicaciones as $u)
                    <option value="{{ $u->id }}" @selected((int) old('ubicacion_id', $e->ubicacion_id ?? 0) === $u->id)>{{ $u->nombre }}</option>
                @endforeach
            </x-ui.select>

            <x-ui.select name="responsable_id" label="Responsable" placeholder="Sin asignar">
                @foreach ($responsables as $r)
                    <option value="{{ $r->id }}" @selected((int) old('responsable_id', $e->responsable_id ?? 0) === $r->id)>{{ $r->nombre }}</option>
                @endforeach
            </x-ui.select>

            <x-ui.select name="estado" label="Estado" required>
                @foreach ($estados as $est)
                    <option value="{{ $est->value }}" @selected($estadoActual === $est->value)>{{ $est->label() }}</option>
                @endforeach
            </x-ui.select>

            <div class="hidden sm:block"></div>

            <x-ui.input name="fecha_adquisicion" type="date" label="Fecha de adquisición"
                        :value="optional($e?->fecha_adquisicion)->format('Y-m-d')" />

            <x-ui.input name="fecha_garantia" type="date" label="Fin de garantía"
                        :value="optional($e?->fecha_garantia)->format('Y-m-d')" />
        </div>
    </section>

    {{-- Especificaciones --}}
    <section>
        <h3 class="mb-4 text-sm font-semibold text-gray-900">Especificaciones</h3>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <x-ui.input name="procesador" label="Procesador" :value="$e->procesador ?? null" />
            <x-ui.input name="memoria_ram" label="Memoria RAM" :value="$e->memoria_ram ?? null" hint="Ej.: 8 GB" />
            <x-ui.input name="almacenamiento" label="Almacenamiento" :value="$e->almacenamiento ?? null" hint="Ej.: 256 GB SSD" />
            <x-ui.input name="sistema_operativo" label="Sistema operativo" :value="$e->sistema_operativo ?? null" />
        </div>
    </section>

    {{-- Observaciones --}}
    <section>
        <x-ui.textarea name="observaciones" label="Observaciones" :value="$e->observaciones ?? null" rows="3" />
    </section>
</div>
