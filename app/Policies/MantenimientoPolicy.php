<?php

namespace App\Policies;

use App\Enums\Permiso;
use App\Models\Mantenimiento;
use App\Models\User;

class MantenimientoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permiso::MantenimientosVer->value);
    }

    public function view(User $user, Mantenimiento $mantenimiento): bool
    {
        return $user->can(Permiso::MantenimientosVer->value);
    }

    public function create(User $user): bool
    {
        return $user->can(Permiso::MantenimientosCrear->value);
    }

    public function update(User $user, Mantenimiento $mantenimiento): bool
    {
        return $user->can(Permiso::MantenimientosEditar->value);
    }

    public function delete(User $user, Mantenimiento $mantenimiento): bool
    {
        return $user->can(Permiso::MantenimientosEliminar->value);
    }
}
