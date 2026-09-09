<?php

namespace App\Actions\Mantenimientos;

use App\Http\Requests\Mantenimientos\MantenimientoRequest;
use App\Models\Equipo;
use App\Models\Mantenimiento;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Crea o actualiza un mantenimiento junto con sus actividades y evidencias,
 * y refleja el estado resultante en el equipo. Todo en una transacción.
 */
class GuardarMantenimiento
{
    public function crear(Equipo $equipo, MantenimientoRequest $request): Mantenimiento
    {
        return DB::transaction(function () use ($equipo, $request) {
            $mantenimiento = $equipo->mantenimientos()->create(
                $this->datos($request) + ['registrado_por_id' => $request->user()->id],
            );

            $this->sincronizarActividades($mantenimiento, $request->actividadesLimpias());
            $this->guardarEvidencias($mantenimiento, $request->file('evidencias', []));
            $this->aplicarEstadoAlEquipo($mantenimiento);

            return $mantenimiento;
        });
    }

    public function actualizar(Mantenimiento $mantenimiento, MantenimientoRequest $request): Mantenimiento
    {
        return DB::transaction(function () use ($mantenimiento, $request) {
            $mantenimiento->update($this->datos($request));

            $this->sincronizarActividades($mantenimiento, $request->actividadesLimpias());
            $this->guardarEvidencias($mantenimiento, $request->file('evidencias', []));
            $this->aplicarEstadoAlEquipo($mantenimiento);

            return $mantenimiento;
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function datos(MantenimientoRequest $request): array
    {
        return [
            'tipo' => $request->validated('tipo'),
            'fecha' => $request->validated('fecha'),
            'responsable_id' => $request->validated('responsable_id'),
            'estado_antes' => $request->validated('estado_antes'),
            'estado_despues' => $request->validated('estado_despues'),
            'descripcion' => $request->validated('descripcion'),
            'observaciones' => $request->validated('observaciones'),
        ];
    }

    /**
     * @param  array<int, string>  $descripciones
     */
    private function sincronizarActividades(Mantenimiento $mantenimiento, array $descripciones): void
    {
        $mantenimiento->actividades()->delete();

        foreach (array_values($descripciones) as $orden => $descripcion) {
            $mantenimiento->actividades()->create([
                'descripcion' => $descripcion,
                'orden' => $orden,
            ]);
        }
    }

    /**
     * @param  array<int, UploadedFile|null>  $archivos
     */
    private function guardarEvidencias(Mantenimiento $mantenimiento, array $archivos): void
    {
        foreach ($archivos as $archivo) {
            if (! $archivo instanceof UploadedFile || ! $archivo->isValid()) {
                continue;
            }

            $extension = strtolower($archivo->getClientOriginalExtension());
            $nombreSeguro = Str::random(40).($extension !== '' ? ".{$extension}" : '');
            $ruta = $archivo->storeAs("evidencias/{$mantenimiento->id}", $nombreSeguro, 'local');

            $mantenimiento->evidencias()->create([
                'nombre_original' => $archivo->getClientOriginalName(),
                'ruta' => $ruta,
                'mime' => $archivo->getMimeType() ?? 'application/octet-stream',
                'tamano' => (int) $archivo->getSize(),
            ]);
        }
    }

    private function aplicarEstadoAlEquipo(Mantenimiento $mantenimiento): void
    {
        if ($mantenimiento->estado_despues !== null) {
            $mantenimiento->equipo->update(['estado' => $mantenimiento->estado_despues]);
        }
    }
}
