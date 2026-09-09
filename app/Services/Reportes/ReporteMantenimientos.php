<?php

namespace App\Services\Reportes;

use App\Models\Mantenimiento;
use App\Models\Programacion;
use Illuminate\Database\Eloquent\Builder;

class ReporteMantenimientos extends Reporte
{
    /**
     * @param  array<string, mixed>  $filtros
     * @return array<string, mixed>
     */
    public function generar(array $filtros): array
    {
        $filas = Mantenimiento::query()
            ->with(['equipo', 'responsable'])
            ->when($filtros['equipo'] ?? null, fn (Builder $q, $v) => $q->whereHas(
                'equipo',
                fn (Builder $e) => $e->where('codigo_interno', 'like', "%{$v}%")
            ))
            ->when($filtros['tipo'] ?? null, fn (Builder $q, $v) => $q->where('tipo', $v))
            ->when($filtros['responsable_id'] ?? null, fn (Builder $q, $v) => $q->where('responsable_id', $v))
            ->when($filtros['desde'] ?? null, fn (Builder $q, $v) => $q->whereDate('fecha', '>=', $v))
            ->when($filtros['hasta'] ?? null, fn (Builder $q, $v) => $q->whereDate('fecha', '<=', $v))
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->get();

        return [
            'filas' => $filas,
            'total' => $filas->count(),
            'agrupaciones' => [
                'Por tipo' => $this->agrupar($filas, fn (Mantenimiento $m) => $m->tipo->label()),
                'Por técnico' => $this->agrupar($filas, fn (Mantenimiento $m) => $m->responsable?->nombre ?? 'Sin asignar'),
                'Por equipo' => $this->agrupar($filas, fn (Mantenimiento $m) => $m->equipo->codigo_interno),
            ],
            'programacion' => [
                'proximas' => Programacion::proximas()->count(),
                'vencidas' => Programacion::vencidas()->count(),
            ],
        ];
    }
}
