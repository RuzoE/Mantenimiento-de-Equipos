<?php

namespace App\Services\Alertas;

use App\Enums\EstadoEquipo;
use App\Enums\EstadoProgramacion;
use App\Models\Equipo;
use App\Models\Programacion;
use Illuminate\Support\Collection;

/**
 * Alertas operativas: mantenimientos vencidos/próximos y equipos con novedades.
 * Se usa tanto en la campana del navbar como en la página de alertas.
 */
class CentroDeAlertas
{
    /**
     * @return array<int, string>
     */
    private function estadosConNovedad(): array
    {
        return EstadoEquipo::valoresDe([
            ...EstadoEquipo::grupoConNovedad(),
            ...EstadoEquipo::grupoEnMantenimiento(),
            ...EstadoEquipo::grupoFueraDeServicio(),
        ]);
    }

    /**
     * Contadores para la campana del navbar (consultas ligeras).
     *
     * @return array{total: int, vencidas: int, proximas: int, con_novedad: int}
     */
    public function contadores(): array
    {
        $vencidas = Programacion::vencidas()->count();
        $proximas = Programacion::proximas()->count();
        $conNovedad = Equipo::activos()->whereIn('estado', $this->estadosConNovedad())->count();

        return [
            'total' => $vencidas + $proximas + $conNovedad,
            'vencidas' => $vencidas,
            'proximas' => $proximas,
            'con_novedad' => $conNovedad,
        ];
    }

    /**
     * Listados para la página de alertas.
     *
     * @return array<string, Collection>
     */
    public function detalle(): array
    {
        return [
            'programaciones' => Programacion::query()
                ->vigentes()
                ->whereDate('proxima_fecha', '<=', today()->addDays(EstadoProgramacion::VENTANA_AVISO_DIAS))
                ->with('equipo')
                ->orderBy('proxima_fecha')
                ->get(),
            'equipos' => Equipo::activos()
                ->whereIn('estado', $this->estadosConNovedad())
                ->with('ubicacion')
                ->orderBy('codigo_interno')
                ->get(),
        ];
    }
}
