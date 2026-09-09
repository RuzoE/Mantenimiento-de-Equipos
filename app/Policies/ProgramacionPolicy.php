<?php

namespace App\Policies;

use App\Enums\Permiso;
use App\Models\Programacion;
use App\Models\User;

class ProgramacionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permiso::ProgramacionesVer->value);
    }

    public function view(User $user, Programacion $programacion): bool
    {
        return $user->can(Permiso::ProgramacionesVer->value);
    }

    public function create(User $user): bool
    {
        return $user->can(Permiso::ProgramacionesGestionar->value);
    }

    public function update(User $user, Programacion $programacion): bool
    {
        return $user->can(Permiso::ProgramacionesGestionar->value);
    }

    public function delete(User $user, Programacion $programacion): bool
    {
        return $user->can(Permiso::ProgramacionesGestionar->value);
    }
}
