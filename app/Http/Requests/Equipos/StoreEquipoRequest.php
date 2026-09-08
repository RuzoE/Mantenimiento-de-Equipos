<?php

namespace App\Http\Requests\Equipos;

use App\Enums\Permiso;
use Illuminate\Validation\Rule;

class StoreEquipoRequest extends EquipoRequest
{
    protected function permiso(): string
    {
        return Permiso::EquiposCrear->value;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return array_merge($this->reglasComunes(), [
            'codigo_interno' => ['required', 'string', 'max:50', Rule::unique('equipos', 'codigo_interno')],
            'numero_serie' => ['nullable', 'string', 'max:100', Rule::unique('equipos', 'numero_serie')],
        ]);
    }
}
