<?php

namespace App\Services\Reportes;

use App\Models\Equipo;
use Illuminate\Database\Eloquent\Builder;

class ReporteEquipos extends Reporte
{
    /**
     * @param  array<string, mixed>  $filtros
     * @return array<string, mixed>
     */
    public function generar(array $filtros): array
    {
        $filas = Equipo::query()
            ->with(['tipoEquipo', 'marca', 'ubicacion', 'responsable'])
            ->when($filtros['tipo_equipo_id'] ?? null, fn (Builder $q, $v) => $q->where('tipo_equipo_id', $v))
            ->when($filtros['marca_id'] ?? null, fn (Builder $q, $v) => $q->where('marca_id', $v))
            ->when($filtros['ubicacion_id'] ?? null, fn (Builder $q, $v) => $q->where('ubicacion_id', $v))
            ->when($filtros['responsable_id'] ?? null, fn (Builder $q, $v) => $q->where('responsable_id', $v))
            ->when($filtros['estado'] ?? null, fn (Builder $q, $v) => $q->where('estado', $v))
            ->when(($filtros['activo'] ?? null) === 'activo', fn (Builder $q) => $q->where('activo', true))
            ->when(($filtros['activo'] ?? null) === 'inactivo', fn (Builder $q) => $q->where('activo', false))
            ->orderBy('codigo_interno')
            ->get();

        return [
            'filas' => $filas,
            'total' => $filas->count(),
            'agrupaciones' => [
                'Por tipo' => $this->agrupar($filas, fn (Equipo $e) => $e->tipoEquipo->nombre),
                'Por marca' => $this->agrupar($filas, fn (Equipo $e) => $e->marca->nombre),
                'Por ubicación' => $this->agrupar($filas, fn (Equipo $e) => $e->ubicacion->nombre),
                'Por estado' => $this->agrupar($filas, fn (Equipo $e) => $e->estado->label()),
                'Por responsable' => $this->agrupar($filas, fn (Equipo $e) => $e->responsable?->nombre ?? 'Sin asignar'),
            ],
        ];
    }
}
