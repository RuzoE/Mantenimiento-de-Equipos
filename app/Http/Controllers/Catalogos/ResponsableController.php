<?php

namespace App\Http\Controllers\Catalogos;

use App\Models\Responsable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class ResponsableController extends CatalogoController
{
    protected string $modelClass = Responsable::class;

    protected string $rutaBase = 'catalogos.responsables';

    protected string $singular = 'responsable';

    protected string $plural = 'Responsables';

    protected function columnasBusqueda(): array
    {
        return ['nombre', 'cargo', 'correo'];
    }

    protected function campos(): array
    {
        return [
            ['name' => 'nombre', 'label' => 'Nombre o área', 'type' => 'text', 'required' => true],
            ['name' => 'cargo', 'label' => 'Cargo', 'type' => 'text'],
            ['name' => 'correo', 'label' => 'Correo electrónico', 'type' => 'email'],
            ['name' => 'telefono', 'label' => 'Teléfono', 'type' => 'text'],
        ];
    }

    protected function reglas(?Model $ignore = null): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255', Rule::unique('responsables', 'nombre')->ignore($ignore)],
            'cargo' => ['nullable', 'string', 'max:255'],
            'correo' => ['nullable', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
        ];
    }
}
