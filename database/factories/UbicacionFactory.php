<?php

namespace Database\Factories;

use App\Models\Ubicacion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ubicacion>
 */
class UbicacionFactory extends Factory
{
    protected $model = Ubicacion::class;

    public function definition(): array
    {
        return [
            'nombre' => 'Sala '.fake()->unique()->numberBetween(1, 9999),
            'descripcion' => fake()->optional()->sentence(),
            'activo' => true,
        ];
    }

    public function inactivo(): static
    {
        return $this->state(fn () => ['activo' => false]);
    }
}
