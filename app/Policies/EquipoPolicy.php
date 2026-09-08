<?php

namespace App\Policies;

use App\Enums\Permiso;
use App\Models\Equipo;
use App\Models\User;

class EquipoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permiso::EquiposVer->value);
    }

    public function view(User $user, Equipo $equipo): bool
    {
        return $user->can(Permiso::EquiposVer->value);
    }

    public function create(User $user): bool
    {
        return $user->can(Permiso::EquiposCrear->value);
    }

    public function update(User $user, Equipo $equipo): bool
    {
        return $user->can(Permiso::EquiposEditar->value);
    }

    public function delete(User $user, Equipo $equipo): bool
    {
        return $user->can(Permiso::EquiposEliminar->value);
    }

    public function restore(User $user, Equipo $equipo): bool
    {
        return $user->can(Permiso::EquiposEliminar->value);
    }
}
