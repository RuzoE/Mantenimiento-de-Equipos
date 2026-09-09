<?php

namespace App\Services\Dashboard;

use App\Enums\EstadoEquipo;
use App\Enums\EstadoProgramacion;
use App\Models\Auditoria;
use App\Models\Equipo;
use App\Models\Programacion;
use App\Models\Ubicacion;
use Illuminate\Support\Collection;

/**
 * Reúne los indicadores y listados del panel principal a partir de datos reales.
 */
class ResumenDashboard
{
    /**
     * @return array<string, mixed>
     */
    public function obtener(): array
    {
        /** @var Collection<string, int> $porEstado */
        $porEstado = Equipo::activos()
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $totalEquipos = (int) $porEstado->sum();

        $contarGrupo = fn (array $grupo): int => (int) collect(EstadoEquipo::valoresDe($grupo))
            ->sum(fn (string $valor) => $porEstado[$valor] ?? 0);

        return [
            'tarjetas' => [
                'total' => $totalEquipos,
                'operativos' => $contarGrupo(EstadoEquipo::grupoOperativo()),
                'con_novedades' => $contarGrupo(EstadoEquipo::grupoConNovedad()),
                'en_mantenimiento' => $contarGrupo(EstadoEquipo::grupoEnMantenimiento()),
                'fuera_de_servicio' => $contarGrupo(EstadoEquipo::grupoFueraDeServicio()),
                'mantenimientos_proximos' => Programacion::proximas()->count(),
                'mantenimientos_vencidos' => Programacion::vencidas()->count(),
            ],
            'porEstado' => collect(EstadoEquipo::cases())
                ->map(fn (EstadoEquipo $e) => [
                    'label' => $e->label(),
                    'color' => $e->color(),
                    'total' => (int) ($porEstado[$e->value] ?? 0),
                    'pct' => $totalEquipos > 0 ? (int) round((($porEstado[$e->value] ?? 0) / $totalEquipos) * 100) : 0,
                ])
                ->filter(fn (array $row) => $row['total'] > 0)
                ->values()
                ->all(),
            'porUbicacion' => $this->porUbicacion($totalEquipos),
            'porAtender' => Programacion::query()
                ->vigentes()
                ->whereDate('proxima_fecha', '<=', today()->addDays(EstadoProgramacion::VENTANA_AVISO_DIAS))
                ->with('equipo')
                ->orderBy('proxima_fecha')
                ->limit(8)
                ->get(),
            'conNovedades' => Equipo::activos()
                ->whereIn('estado', EstadoEquipo::valoresDe([
                    ...EstadoEquipo::grupoConNovedad(),
                    ...EstadoEquipo::grupoEnMantenimiento(),
                    ...EstadoEquipo::grupoFueraDeServicio(),
                ]))
                ->with(['ubicacion', 'tipoEquipo'])
                ->orderBy('codigo_interno')
                ->limit(8)
                ->get(),
            'actividadReciente' => $this->actividadReciente(),
        ];
    }

    /**
     * @return array<int, array{nombre:string, total:int, pct:int}>
     */
    private function porUbicacion(int $total): array
    {
        return Ubicacion::query()
            ->withCount(['equipos' => fn ($q) => $q->where('activo', true)])
            ->having('equipos_count', '>', 0)
            ->orderByDesc('equipos_count')
            ->limit(6)
            ->get()
            ->map(fn ($u) => [
                'nombre' => $u->nombre,
                'total' => (int) $u->equipos_count,
                'pct' => $total > 0 ? (int) round(($u->equipos_count / $total) * 100) : 0,
            ])
            ->all();
    }

    /**
     * Actividad reciente a partir de la auditoría.
     *
     * @return Collection<int, object>
     */
    private function actividadReciente(): Collection
    {
        return Auditoria::with('user')->recientes()->limit(8)->get()
            ->map(fn (Auditoria $a) => (object) [
                'fecha' => $a->created_at,
                'icono' => match ($a->modulo) {
                    'Mantenimientos' => 'wrench',
                    'Traslados' => 'map-pin',
                    'Programación' => 'calendar',
                    'Usuarios' => 'users',
                    'Equipos' => 'desktop',
                    default => 'clipboard',
                },
                'texto' => $a->actor().' '.$a->descripcion,
            ]);
    }
}
