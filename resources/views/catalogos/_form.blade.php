@foreach ($campos as $campo)
    @php
        $tipo = $campo['type'] ?? 'text';
        $valor = $item->{$campo['name']} ?? null;
    @endphp

    @if ($tipo === 'textarea')
        <x-ui.textarea :name="$campo['name']" :label="$campo['label']"
                       :required="$campo['required'] ?? false" :value="$valor"
                       :hint="$campo['hint'] ?? null" />
    @else
        <x-ui.input :name="$campo['name']" :type="$tipo" :label="$campo['label']"
                    :required="$campo['required'] ?? false" :value="$valor"
                    :hint="$campo['hint'] ?? null" />
    @endif
@endforeach

@unless (isset($item))
    <label class="flex items-center gap-3">
        <input type="checkbox" name="activo" value="1" @checked(old('activo', true))
               class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
        <span class="text-sm text-gray-700">Activo</span>
    </label>
@endunless
