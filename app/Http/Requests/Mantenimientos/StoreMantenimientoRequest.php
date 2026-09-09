<?php

namespace App\Http\Requests\Mantenimientos;

use App\Enums\Permiso;

class StoreMantenimientoRequest extends MantenimientoRequest
{
    protected function permiso(): string
    {
        return Permiso::MantenimientosCrear->value;
    }
}
