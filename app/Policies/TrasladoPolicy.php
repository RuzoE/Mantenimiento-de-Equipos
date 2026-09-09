<?php

namespace App\Policies;

use App\Enums\Permiso;
use App\Models\Traslado;
use App\Models\User;

class TrasladoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permiso::TrasladosVer->value);
    }

    public function view(User $user, Traslado $traslado): bool
    {
        return $user->can(Permiso::TrasladosVer->value);
    }

    public function create(User $user): bool
    {
        return $user->can(Permiso::TrasladosCrear->value);
    }

    /**
     * Solo el Administrador puede deshacer, y únicamente el traslado más reciente
     * del equipo (para no romper la cadena del historial).
     */
    public function delete(User $user, Traslado $traslado): bool
    {
        return $user->esAdministrador() && $traslado->esUltimo();
    }
}
