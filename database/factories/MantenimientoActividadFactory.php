<?php

namespace Database\Factories;

use App\Models\Mantenimiento;
use App\Models\MantenimientoActividad;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MantenimientoActividad>
 */
class MantenimientoActividadFactory extends Factory
{
    protected $model = MantenimientoActividad::class;

    public function definition(): array
    {
        return [
            'mantenimiento_id' => Mantenimiento::factory(),
            'descripcion' => fake()->sentence(4),
            'orden' => 0,
        ];
    }
}
