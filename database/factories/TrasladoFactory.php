<?php

namespace Database\Factories;

use App\Enums\MotivoTraslado;
use App\Models\Equipo;
use App\Models\Traslado;
use App\Models\Ubicacion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Traslado>
 */
class TrasladoFactory extends Factory
{
    protected $model = Traslado::class;

    public function definition(): array
    {
        return [
            'equipo_id' => Equipo::factory(),
            'ubicacion_origen_id' => Ubicacion::factory(),
            'ubicacion_destino_id' => Ubicacion::factory(),
            'fecha' => fake()->dateTimeBetween('-1 year', 'now'),
            'motivo' => fake()->randomElement(MotivoTraslado::cases()),
            'observaciones' => fake()->optional()->sentence(),
            'registrado_por_id' => User::factory(),
        ];
    }
}
