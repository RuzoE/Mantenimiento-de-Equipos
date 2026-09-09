<?php

namespace App\Http\Requests\Mantenimientos;

use App\Enums\Permiso;

class UpdateMantenimientoRequest extends MantenimientoRequest
{
    protected function permiso(): string
    {
        return Permiso::MantenimientosEditar->value;
    }
}
