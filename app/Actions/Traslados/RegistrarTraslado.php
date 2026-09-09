<?php

namespace App\Actions\Traslados;

use App\Http\Requests\Traslados\StoreTrasladoRequest;
use App\Models\Equipo;
use App\Models\Traslado;
use Illuminate\Support\Facades\DB;

class RegistrarTraslado
{
    /**
     * Registra el traslado (origen = ubicación actual) y mueve el equipo al destino.
     */
    public function ejecutar(Equipo $equipo, StoreTrasladoRequest $request): Traslado
    {
        return DB::transaction(function () use ($equipo, $request) {
            $traslado = $equipo->traslados()->create([
                'ubicacion_origen_id' => $equipo->ubicacion_id,
                'ubicacion_destino_id' => $request->integer('ubicacion_destino_id'),
                'fecha' => $request->validated('fecha'),
                'motivo' => $request->validated('motivo'),
                'observaciones' => $request->validated('observaciones'),
                'registrado_por_id' => $request->user()->id,
            ]);

            $equipo->update(['ubicacion_id' => $traslado->ubicacion_destino_id]);

            return $traslado;
        });
    }

    /**
     * Deshace un traslado: devuelve el equipo a la ubicación de origen y borra el registro.
     */
    public function deshacer(Traslado $traslado): void
    {
        DB::transaction(function () use ($traslado) {
            $traslado->equipo->update(['ubicacion_id' => $traslado->ubicacion_origen_id]);
            $traslado->delete();
        });
    }
}
