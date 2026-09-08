<?php

namespace App\Policies;

use App\Enums\Permiso;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Autorización compartida por todos los catálogos (tipos de equipo, marcas,
 * ubicaciones, responsables). Se registra para cada modelo en AppServiceProvider.
 */
class CatalogoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permiso::CatalogosVer->value);
    }

    public function view(User $user, Model $model): bool
    {
        return $user->can(Permiso::CatalogosVer->value);
    }

    public function create(User $user): bool
    {
        return $user->can(Permiso::CatalogosGestionar->value);
    }

    public function update(User $user, Model $model): bool
    {
        return $user->can(Permiso::CatalogosGestionar->value);
    }

    public function delete(User $user, Model $model): bool
    {
        return $user->can(Permiso::CatalogosGestionar->value);
    }

    public function restore(User $user, Model $model): bool
    {
        return $user->can(Permiso::CatalogosGestionar->value);
    }
}
