<?php

namespace Database\Factories;

use App\Enums\EstadoEquipo;
use App\Enums\TipoMantenimiento;
use App\Models\Equipo;
use App\Models\Mantenimiento;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mantenimiento>
 */
class MantenimientoFactory extends Factory
{
    protected $model = Mantenimiento::class;

    public function definition(): array
    {
        return [
            'equipo_id' => Equipo::factory(),
            'tipo' => fake()->randomElement(TipoMantenimiento::cases()),
            'fecha' => fake()->dateTimeBetween('-1 year', 'now'),
            'responsable_id' => null,
            'registrado_por_id' => User::factory(),
            'estado_antes' => fake()->randomElement(EstadoEquipo::cases()),
            'estado_despues' => EstadoEquipo::Operativo,
            'descripcion' => fake()->sentence(),
            'observaciones' => fake()->optional()->paragraph(),
        ];
    }

    public function preventivo(): static
    {
        return $this->state(fn () => ['tipo' => TipoMantenimiento::Preventivo]);
    }

    public function correctivo(): static
    {
        return $this->state(fn () => ['tipo' => TipoMantenimiento::Correctivo]);
    }
}
