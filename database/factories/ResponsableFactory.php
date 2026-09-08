<?php

namespace Database\Factories;

use App\Models\Responsable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Responsable>
 */
class ResponsableFactory extends Factory
{
    protected $model = Responsable::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->name(),
            'cargo' => fake()->optional()->jobTitle(),
            'correo' => fake()->optional()->safeEmail(),
            'telefono' => fake()->optional()->numerify('3## ### ####'),
            'activo' => true,
        ];
    }

    public function inactivo(): static
    {
        return $this->state(fn () => ['activo' => false]);
    }
}
