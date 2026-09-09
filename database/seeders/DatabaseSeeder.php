<?php

namespace Database\Seeders;

use App\Enums\RolUsuario;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Puebla la base de datos con los datos iniciales.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@policarpa.edu.co'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'activo' => true,
            ],
        );

        $this->call([
            RolePermissionSeeder::class,
            CatalogoSeeder::class,
            EquipoSeeder::class,
            MantenimientoSeeder::class,
        ]);

        $admin->syncRoles([RolUsuario::Administrador->value]);
    }
}
