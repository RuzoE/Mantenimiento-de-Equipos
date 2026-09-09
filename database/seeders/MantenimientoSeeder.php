<?php

namespace Database\Seeders;

use App\Enums\EstadoEquipo;
use App\Enums\TipoMantenimiento;
use App\Models\Equipo;
use App\Models\Responsable;
use App\Models\User;
use Illuminate\Database\Seeder;

class MantenimientoSeeder extends Seeder
{
    public function run(): void
    {
        $registrador = User::where('email', 'admin@policarpa.edu.co')->value('id');
        $responsable = Responsable::where('nombre', 'Área de Sistemas')->value('id');

        if (! $registrador) {
            return;
        }

        $plan = [
            'PC-001' => [
                ['tipo' => TipoMantenimiento::Preventivo, 'meses' => 6, 'desc' => 'Limpieza general y actualización del sistema.', 'despues' => EstadoEquipo::Operativo,
                    'actividades' => ['Limpieza interna con aire comprimido', 'Cambio de pasta térmica', 'Actualización de Windows']],
                ['tipo' => TipoMantenimiento::Correctivo, 'meses' => 2, 'desc' => 'Reemplazo de disco duro por fallas de lectura.', 'despues' => EstadoEquipo::Operativo,
                    'actividades' => ['Diagnóstico de disco', 'Instalación de SSD', 'Restauración de imagen']],
            ],
            'PC-015' => [
                ['tipo' => TipoMantenimiento::Preventivo, 'meses' => 1, 'desc' => 'Mantenimiento preventivo trimestral.', 'despues' => EstadoEquipo::EnMantenimiento,
                    'actividades' => ['Limpieza de ventiladores', 'Revisión de conexiones']],
            ],
            'IMP-003' => [
                ['tipo' => TipoMantenimiento::Correctivo, 'meses' => 1, 'desc' => 'Atasco de papel recurrente; se cambió el rodillo de arrastre.', 'despues' => EstadoEquipo::EnReparacion,
                    'actividades' => ['Cambio de rodillo de arrastre', 'Limpieza de sensores']],
            ],
        ];

        foreach ($plan as $codigo => $mantenimientos) {
            $equipo = Equipo::where('codigo_interno', $codigo)->first();

            if (! $equipo) {
                continue;
            }

            foreach ($mantenimientos as $datos) {
                $existe = $equipo->mantenimientos()
                    ->whereDate('fecha', now()->subMonths($datos['meses'])->toDateString())
                    ->where('tipo', $datos['tipo']->value)
                    ->exists();

                if ($existe) {
                    continue;
                }

                $mantenimiento = $equipo->mantenimientos()->create([
                    'tipo' => $datos['tipo'],
                    'fecha' => now()->subMonths($datos['meses'])->toDateString(),
                    'responsable_id' => $responsable,
                    'registrado_por_id' => $registrador,
                    'estado_antes' => EstadoEquipo::Regular,
                    'estado_despues' => $datos['despues'],
                    'descripcion' => $datos['desc'],
                ]);

                foreach ($datos['actividades'] as $orden => $actividad) {
                    $mantenimiento->actividades()->create(['descripcion' => $actividad, 'orden' => $orden]);
                }
            }
        }
    }
}
