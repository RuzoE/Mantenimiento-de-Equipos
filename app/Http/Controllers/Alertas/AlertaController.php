<?php

namespace App\Http\Controllers\Alertas;

use App\Http\Controllers\Controller;
use App\Services\Alertas\CentroDeAlertas;
use Illuminate\View\View;

class AlertaController extends Controller
{
    public function index(CentroDeAlertas $centro): View
    {
        return view('alertas.index', [
            'contadores' => $centro->contadores(),
            'detalle' => $centro->detalle(),
        ]);
    }
}
