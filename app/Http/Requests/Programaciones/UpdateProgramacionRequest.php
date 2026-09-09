<?php

namespace App\Http\Requests\Programaciones;

use App\Enums\Permiso;

class UpdateProgramacionRequest extends ProgramacionRequest
{
    protected function permiso(): string
    {
        return Permiso::ProgramacionesGestionar->value;
    }
}
