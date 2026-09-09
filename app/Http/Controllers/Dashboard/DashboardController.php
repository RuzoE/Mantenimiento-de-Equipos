<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\ResumenDashboard;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Muestra el panel principal con indicadores y listados reales.
     */
    public function __invoke(ResumenDashboard $resumen): View
    {
        return view('dashboard.index', ['resumen' => $resumen->obtener()]);
    }
}
