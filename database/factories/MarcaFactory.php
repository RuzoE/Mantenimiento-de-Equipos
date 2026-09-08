<?php

namespace Database\Factories;

use App\Models\Marca;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Marca>
 */
class MarcaFactory extends Factory
{
    protected $model = Marca::class;

    public function definition(): array
    {
        return [
            'nombre' => ucfirst(fake()->unique()->word()),
            'activo' => true,
        ];
    }

    public function inactivo(): static
    {
        return $this->state(fn () => ['activo' => false]);
    }
}
