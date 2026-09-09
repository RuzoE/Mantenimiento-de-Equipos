<?php

namespace Database\Factories;

use App\Enums\EventoAuditoria;
use App\Models\Auditoria;
use App\Models\Equipo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Auditoria>
 */
class AuditoriaFactory extends Factory
{
    protected $model = Auditoria::class;

    public function definition(): array
    {
        $equipo = Equipo::factory()->create();

        return [
            'user_id' => User::factory(),
            'evento' => fake()->randomElement(EventoAuditoria::cases()),
            'auditable_type' => $equipo->getMorphClass(),
            'auditable_id' => $equipo->id,
            'modulo' => 'Equipos',
            'descripcion' => 'actualizó el equipo '.$equipo->codigo_interno,
            'cambios' => ['estado' => ['antes' => 'operativo', 'despues' => 'danado']],
            'ip' => fake()->ipv4(),
        ];
    }

    public function delSistema(): static
    {
        return $this->state(fn () => ['user_id' => null]);
    }
}
