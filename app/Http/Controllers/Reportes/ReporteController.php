<?php

namespace App\Http\Controllers\Reportes;

use App\Enums\EstadoEquipo;
use App\Enums\TipoMantenimiento;
use App\Http\Controllers\Controller;
use App\Models\Equipo;
use App\Models\Mantenimiento;
use App\Models\Marca;
use App\Models\Responsable;
use App\Models\TipoEquipo;
use App\Models\Ubicacion;
use App\Services\Reportes\ReporteEquipos;
use App\Services\Reportes\ReporteMantenimientos;
use App\Support\ExportadorCsv;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteController extends Controller
{
    private const FILTROS_EQUIPOS = ['tipo_equipo_id', 'marca_id', 'ubicacion_id', 'responsable_id', 'estado', 'activo'];

    private const FILTROS_MANTENIMIENTOS = ['equipo', 'tipo', 'responsable_id', 'desde', 'hasta'];

    public function index(): View
    {
        return view('reportes.index');
    }

    public function equipos(Request $request, ReporteEquipos $servicio): View
    {
        $filtros = $this->filtros($request, self::FILTROS_EQUIPOS);

        return view('reportes.equipos', [
            'reporte' => $servicio->generar($filtros),
            'filtros' => $filtros,
            'estados' => EstadoEquipo::cases(),
            'tipos' => TipoEquipo::activos()->orderBy('nombre')->get(['id', 'nombre']),
            'marcas' => Marca::activos()->orderBy('nombre')->get(['id', 'nombre']),
            'ubicaciones' => Ubicacion::activos()->orderBy('nombre')->get(['id', 'nombre']),
            'responsables' => Responsable::activos()->orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    public function equiposExport(Request $request, ReporteEquipos $servicio): StreamedResponse|Response
    {
        $filtros = $this->filtros($request, self::FILTROS_EQUIPOS);
        $reporte = $servicio->generar($filtros);
        $nombre = 'reporte-equipos-'.now()->format('Ymd-His');

        if ($request->string('formato')->value() === 'csv') {
            return ExportadorCsv::descargar(
                "{$nombre}.csv",
                ['Código interno', 'Tipo', 'Marca', 'Modelo', 'Número de serie', 'Ubicación', 'Responsable', 'Estado', 'Fecha de adquisición', 'Activo'],
                $reporte['filas']->map(fn (Equipo $e) => [
                    $e->codigo_interno,
                    $e->tipoEquipo->nombre,
                    $e->marca->nombre,
                    $e->modelo,
                    $e->numero_serie,
                    $e->ubicacion->nombre,
                    $e->responsable?->nombre ?? 'Sin asignar',
                    $e->estado->label(),
                    $e->fecha_adquisicion?->format('d/m/Y'),
                    $e->activo ? 'Sí' : 'No',
                ]),
            );
        }

        return Pdf::loadView('reportes.pdf.equipos', ['reporte' => $reporte, 'generado' => now()])
            ->download("{$nombre}.pdf");
    }

    public function mantenimientos(Request $request, ReporteMantenimientos $servicio): View
    {
        $filtros = $this->filtros($request, self::FILTROS_MANTENIMIENTOS);

        return view('reportes.mantenimientos', [
            'reporte' => $servicio->generar($filtros),
            'filtros' => $filtros,
            'tipos' => TipoMantenimiento::cases(),
            'responsables' => Responsable::activos()->orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    public function mantenimientosExport(Request $request, ReporteMantenimientos $servicio): StreamedResponse|Response
    {
        $filtros = $this->filtros($request, self::FILTROS_MANTENIMIENTOS);
        $reporte = $servicio->generar($filtros);
        $nombre = 'reporte-mantenimientos-'.now()->format('Ymd-His');

        if ($request->string('formato')->value() === 'csv') {
            return ExportadorCsv::descargar(
                "{$nombre}.csv",
                ['Fecha', 'Equipo', 'Tipo', 'Técnico', 'Estado antes', 'Estado después', 'Descripción'],
                $reporte['filas']->map(fn (Mantenimiento $m) => [
                    $m->fecha->format('d/m/Y'),
                    $m->equipo->codigo_interno,
                    $m->tipo->label(),
                    $m->responsable?->nombre ?? 'Sin asignar',
                    $m->estado_antes?->label(),
                    $m->estado_despues?->label(),
                    $m->descripcion,
                ]),
            );
        }

        return Pdf::loadView('reportes.pdf.mantenimientos', ['reporte' => $reporte, 'generado' => now()])
            ->download("{$nombre}.pdf");
    }

    /**
     * @param  array<int, string>  $permitidos
     * @return array<string, mixed>
     */
    private function filtros(Request $request, array $permitidos): array
    {
        return array_filter(
            $request->only($permitidos),
            fn ($valor) => $valor !== null && $valor !== '',
        );
    }
}
