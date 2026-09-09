<?php

namespace App\Services\Reportes;

use Illuminate\Support\Collection;

abstract class Reporte
{
    /**
     * Agrupa las filas por una clave y devuelve [['label' => , 'total' => ], ...]
     * ordenado de mayor a menor.
     *
     * @param  Collection<int, mixed>  $filas
     * @return array<int, array{label: string, total: int}>
     */
    protected function agrupar(Collection $filas, callable $clave): array
    {
        return $filas->groupBy($clave)
            ->map->count()
            ->sortDesc()
            ->map(fn (int $total, string $label) => ['label' => $label, 'total' => $total])
            ->values()
            ->all();
    }
}
