<?php

namespace Database\Seeders;

use App\Enums\FrecuenciaMantenimiento;
use App\Enums\TipoMantenimiento;
use App\Models\Equipo;
use Illuminate\Database\Seeder;

class ProgramacionSeeder extends Seeder
{
    public function run(): void
    {
        // [código de equipo => [frecuencia, días desde el último mantenimiento]]
        $plan = [
            'PC-001' => [FrecuenciaMantenimiento::Trimestral, 100], // vencida (100 > 90)
            'PC-002' => [FrecuenciaMantenimiento::Mensual, 25],      // próxima (faltan ~5 días)
            'PC-015' => [FrecuenciaMantenimiento::Semestral, 30],    // programada
            'PORT-004' => [FrecuenciaMantenimiento::Trimestral, 10], // programada
            'IMP-003' => [FrecuenciaMantenimiento::Mensual, 40],     // vencida
        ];

        foreach ($plan as $codigo => [$frecuencia, $diasDesdeUltimo]) {
            $equipo = Equipo::where('codigo_interno', $codigo)->first();

            if (! $equipo || $equipo->programaciones()->exists()) {
                continue;
            }

            $equipo->programaciones()->create([
                'tipo' => TipoMantenimiento::Preventivo,
                'frecuencia' => $frecuencia,
                'fecha_ultimo_mantenimiento' => now()->subDays($diasDesdeUltimo)->toDateString(),
                'observaciones' => 'Programación de mantenimiento preventivo institucional.',
            ]);
        }
    }
}
