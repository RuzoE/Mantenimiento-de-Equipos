<?php

namespace Database\Seeders;

use App\Enums\Permiso;
use App\Enums\RolUsuario;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Registra permisos, roles y sus asignaciones.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 1. Permisos
        foreach (Permiso::values() as $nombre) {
            Permission::findOrCreate($nombre, 'web');
        }

        // 2. Roles con sus permisos
        $matriz = [
            RolUsuario::Administrador->value => Permiso::values(),

            RolUsuario::Tecnico->value => [
                Permiso::EquiposVer->value,
                Permiso::MantenimientosVer->value,
                Permiso::MantenimientosCrear->value,
                Permiso::MantenimientosEditar->value,
                Permiso::MantenimientosEliminar->value,
                Permiso::ProgramacionesVer->value,
                Permiso::ProgramacionesGestionar->value,
                Permiso::TrasladosVer->value,
                Permiso::TrasladosCrear->value,
                Permiso::CatalogosVer->value,
                Permiso::ReportesVer->value,
            ],

            RolUsuario::Consulta->value => [
                Permiso::EquiposVer->value,
                Permiso::MantenimientosVer->value,
                Permiso::ProgramacionesVer->value,
                Permiso::TrasladosVer->value,
                Permiso::CatalogosVer->value,
                Permiso::ReportesVer->value,
            ],
        ];

        foreach ($matriz as $rol => $permisos) {
            $role = Role::findOrCreate($rol, 'web');
            $role->syncPermissions($permisos);
        }

        // 3. Asignar el rol Administrador al usuario administrador base
        $admin = DB::table('users')->where('email', 'admin@policarpa.edu.co')->first();

        if ($admin) {
            User::find($admin->id)?->syncRoles([RolUsuario::Administrador->value]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
