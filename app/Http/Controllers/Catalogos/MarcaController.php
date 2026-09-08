<?php

namespace App\Http\Controllers\Catalogos;

use App\Models\Marca;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class MarcaController extends CatalogoController
{
    protected string $modelClass = Marca::class;

    protected string $rutaBase = 'catalogos.marcas';

    protected string $singular = 'marca';

    protected string $plural = 'Marcas';

    protected string $etiquetaNueva = 'Nueva marca';

    protected function campos(): array
    {
        return [
            ['name' => 'nombre', 'label' => 'Nombre', 'type' => 'text', 'required' => true],
        ];
    }

    protected function reglas(?Model $ignore = null): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255', Rule::unique('marcas', 'nombre')->ignore($ignore)],
        ];
    }
}
