<?php

namespace Database\Seeders;

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
        User::updateOrCreate(
            ['email' => 'admin@policarpa.edu.co'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
            ],
        );
    }
}
