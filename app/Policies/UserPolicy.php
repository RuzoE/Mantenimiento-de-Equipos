<?php

namespace App\Policies;

use App\Enums\Permiso;
use App\Enums\RolUsuario;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permiso::UsuariosVer->value);
    }

    public function view(User $user, User $model): bool
    {
        return $user->can(Permiso::UsuariosVer->value);
    }

    public function create(User $user): bool
    {
        return $user->can(Permiso::UsuariosCrear->value);
    }

    public function update(User $user, User $model): bool
    {
        return $user->can(Permiso::UsuariosEditar->value);
    }

    /**
     * Desactivar. No se permite desactivarse a sí mismo ni desactivar
     * al último Administrador activo del sistema.
     */
    public function delete(User $user, User $model): bool
    {
        if (! $user->can(Permiso::UsuariosEliminar->value)) {
            return false;
        }

        if ($user->is($model)) {
            return false;
        }

        if ($this->esUltimoAdministrador($model)) {
            return false;
        }

        return true;
    }

    /**
     * Reactivar un usuario desactivado.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->can(Permiso::UsuariosEliminar->value);
    }

    private function esUltimoAdministrador(User $model): bool
    {
        if (! $model->hasRole(RolUsuario::Administrador->value)) {
            return false;
        }

        return User::query()
            ->activos()
            ->role(RolUsuario::Administrador->value)
            ->where('id', '!=', $model->id)
            ->doesntExist();
    }
}
