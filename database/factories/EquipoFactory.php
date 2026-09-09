<?php

namespace Database\Factories;

use App\Enums\EstadoEquipo;
use App\Models\Equipo;
use App\Models\Marca;
use App\Models\Responsable;
use App\Models\TipoEquipo;
use App\Models\Ubicacion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Equipo>
 */
class EquipoFactory extends Factory
{
    protected $model = Equipo::class;

    public function definition(): array
    {
        return [
            'codigo_interno' => strtoupper(fake()->unique()->bothify('EQ-###')),
            'tipo_equipo_id' => TipoEquipo::factory(),
            'marca_id' => Marca::factory(),
            'modelo' => fake()->optional()->word(),
            'numero_serie' => fake()->boolean(70) ? fake()->unique()->bothify('SN########') : null,
            'ubicacion_id' => Ubicacion::factory(),
            'responsable_id' => fake()->boolean(70) ? Responsable::factory() : null,
            'estado' => EstadoEquipo::Operativo,
            'fecha_adquisicion' => fake()->optional()->dateTimeBetween('-5 years', '-1 month'),
            'fecha_garantia' => fake()->optional()->dateTimeBetween('now', '+3 years'),
            'procesador' => fake()->optional()->word(),
            'memoria_ram' => fake()->optional()->randomElement(['4 GB', '8 GB', '16 GB']),
            'almacenamiento' => fake()->optional()->randomElement(['500 GB HDD', '256 GB SSD', '1 TB SSD']),
            'sistema_operativo' => fake()->optional()->randomElement(['Windows 10', 'Windows 11', 'Ubuntu 22.04']),
            'observaciones' => fake()->optional()->sentence(),
            'activo' => true,
        ];
    }

    public function inactivo(): static
    {
        return $this->state(fn () => ['activo' => false]);
    }

    public function estado(EstadoEquipo $estado): static
    {
        return $this->state(fn () => ['estado' => $estado]);
    }
}
