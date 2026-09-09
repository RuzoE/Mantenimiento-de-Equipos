<?php

namespace Database\Seeders;

use App\Enums\MotivoTraslado;
use App\Models\Equipo;
use App\Models\Ubicacion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrasladoSeeder extends Seeder
{
    public function run(): void
    {
        $registrador = User::where('email', 'admin@policarpa.edu.co')->value('id');

        if (! $registrador) {
            return;
        }

        // [código de equipo => [nueva ubicación, motivo, meses atrás]]
        $plan = [
            'PC-002' => ['Sala de informática 2', MotivoTraslado::Reorganizacion, 3],
            'PORT-004' => ['Rectoría', MotivoTraslado::Prestamo, 1],
            'PROY-002' => ['Primaria', MotivoTraslado::Prestamo, 2],
        ];

        foreach ($plan as $codigo => [$destinoNombre, $motivo, $meses]) {
            $equipo = Equipo::where('codigo_interno', $codigo)->first();
            $destino = Ubicacion::where('nombre', $destinoNombre)->first();

            if (! $equipo || ! $destino || $equipo->ubicacion_id === $destino->id || $equipo->traslados()->exists()) {
                continue;
            }

            DB::transaction(function () use ($equipo, $destino, $motivo, $meses, $registrador) {
                $equipo->traslados()->create([
                    'ubicacion_origen_id' => $equipo->ubicacion_id,
                    'ubicacion_destino_id' => $destino->id,
                    'fecha' => now()->subMonths($meses)->toDateString(),
                    'motivo' => $motivo,
                    'registrado_por_id' => $registrador,
                ]);
                $equipo->update(['ubicacion_id' => $destino->id]);
            });
        }
    }
}
