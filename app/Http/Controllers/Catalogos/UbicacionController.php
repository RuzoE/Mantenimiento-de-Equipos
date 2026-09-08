<?php

namespace App\Http\Controllers\Catalogos;

use App\Models\Ubicacion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class UbicacionController extends CatalogoController
{
    protected string $modelClass = Ubicacion::class;

    protected string $rutaBase = 'catalogos.ubicaciones';

    protected string $singular = 'ubicación';

    protected string $plural = 'Ubicaciones';

    protected string $etiquetaNueva = 'Nueva ubicación';

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
            'nombre' => ['required', 'string', 'max:255', Rule::unique('ubicaciones', 'nombre')->ignore($ignore)],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ];
    }
}
