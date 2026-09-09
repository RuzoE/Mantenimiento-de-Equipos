<?php

namespace Database\Factories;

use App\Enums\EstadoProgramacion;
use App\Enums\FrecuenciaMantenimiento;
use App\Enums\TipoMantenimiento;
use App\Models\Equipo;
use App\Models\Programacion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Programacion>
 */
class ProgramacionFactory extends Factory
{
    protected $model = Programacion::class;

    public function definition(): array
    {
        return [
            'equipo_id' => Equipo::factory(),
            'tipo' => TipoMantenimiento::Preventivo,
            'frecuencia' => fake()->randomElement(FrecuenciaMantenimiento::cases()),
            'fecha_ultimo_mantenimiento' => fake()->optional()->dateTimeBetween('-6 months', '-1 week'),
            'proxima_fecha' => now()->addDays(30), // el evento saving lo recalcula
            'estado' => EstadoProgramacion::Programado,
            'observaciones' => fake()->optional()->sentence(),
        ];
    }

    public function venceEn(int $dias): static
    {
        return $this->state(fn () => [
            'frecuencia' => FrecuenciaMantenimiento::Mensual,
            'fecha_ultimo_mantenimiento' => today()->subDays(30 - $dias),
        ]);
    }

    public function cancelada(): static
    {
        return $this->state(fn () => ['estado' => EstadoProgramacion::Cancelado]);
    }
}
