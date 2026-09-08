<?php

namespace App\Http\Requests\Equipos;

use App\Enums\Permiso;
use Illuminate\Validation\Rule;

class UpdateEquipoRequest extends EquipoRequest
{
    protected function permiso(): string
    {
        return Permiso::EquiposEditar->value;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $id = $this->equipoActual()?->id;

        return array_merge($this->reglasComunes(), [
            'codigo_interno' => ['required', 'string', 'max:50', Rule::unique('equipos', 'codigo_interno')->ignore($id)],
            'numero_serie' => ['nullable', 'string', 'max:100', Rule::unique('equipos', 'numero_serie')->ignore($id)],
        ]);
    }
}
