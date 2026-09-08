<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Muestra el panel principal.
     *
     * Los indicadores se conectarán a datos reales cuando existan los módulos
     * de equipos y mantenimientos (FASE 9). Por ahora se envían en cero.
     */
    public function __invoke(Request $request): View
    {
        $stats = [
            'equipos_total' => 0,
            'equipos_operativos' => 0,
            'equipos_novedad' => 0,
            'equipos_mantenimiento' => 0,
            'equipos_fuera_servicio' => 0,
            'mantenimientos_proximos' => 0,
            'mantenimientos_vencidos' => 0,
        ];

        return view('dashboard.index', compact('stats'));
    }
}
