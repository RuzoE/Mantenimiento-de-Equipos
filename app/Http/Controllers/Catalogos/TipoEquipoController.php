<?php

namespace App\Http\Controllers\Catalogos;

use App\Models\TipoEquipo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class TipoEquipoController extends CatalogoController
{
    protected string $modelClass = TipoEquipo::class;

    protected string $rutaBase = 'catalogos.tipos-equipo';

    protected string $singular = 'tipo de equipo';

    protected string $plural = 'Tipos de equipo';

    protected function columnasBusqueda(): array
    {
        return ['nombre', 'descripcion'];
    }

    protected function campos(): array
    {
        return [
            ['name' => 'nombre', 'label' => 'Nombre', 'type' => 'text', 'required' => true],
            ['name' => 'descripcion', 'label' => 'Descripción', 'type' => 'text'],
        ];
    }

    protected function reglas(?Model $ignore = null): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255', Rule::unique('tipos_equipos', 'nombre')->ignore($ignore)],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ];
    }
}
