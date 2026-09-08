<?php

namespace Database\Factories;

use App\Models\TipoEquipo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TipoEquipo>
 */
class TipoEquipoFactory extends Factory
{
    protected $model = TipoEquipo::class;

    public function definition(): array
    {
        return [
            'nombre' => ucfirst(fake()->unique()->words(2, true)),
            'descripcion' => fake()->optional()->sentence(),
            'activo' => true,
        ];
    }

    public function inactivo(): static
    {
        return $this->state(fn () => ['activo' => false]);
    }
}
