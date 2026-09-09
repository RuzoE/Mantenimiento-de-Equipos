<?php

namespace App\Http\Controllers\Traslados;

use App\Actions\Traslados\RegistrarTraslado;
use App\Enums\MotivoTraslado;
use App\Http\Controllers\Controller;
use App\Http\Requests\Traslados\StoreTrasladoRequest;
use App\Models\Equipo;
use App\Models\Traslado;
use App\Models\Ubicacion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrasladoController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Traslado::class);

        $termino = $request->string('buscar')->trim()->value();

        $traslados = Traslado::query()
            ->with(['equipo', 'ubicacionOrigen', 'ubicacionDestino', 'registradoPor'])
            ->when($termino !== '', fn (Builder $q) => $q->whereHas(
                'equipo',
                fn (Builder $e) => $e->where('codigo_interno', 'like', "%{$termino}%")
            ))
            ->when($request->filled('ubicacion_origen_id'), fn (Builder $q) => $q->where('ubicacion_origen_id', $request->integer('ubicacion_origen_id')))
            ->when($request->filled('ubicacion_destino_id'), fn (Builder $q) => $q->where('ubicacion_destino_id', $request->integer('ubicacion_destino_id')))
            ->when($request->filled('desde'), fn (Builder $q) => $q->whereDate('fecha', '>=', $request->date('desde')))
            ->when($request->filled('hasta'), fn (Builder $q) => $q->whereDate('fecha', '<=', $request->date('hasta')))
            ->recientes()
            ->paginate(15)
            ->withQueryString();

        return view('traslados.index', [
            'traslados' => $traslados,
            'ubicaciones' => Ubicacion::orderBy('nombre')->get(['id', 'nombre']),
            'filtros' => $request->only(['buscar', 'ubicacion_origen_id', 'ubicacion_destino_id', 'desde', 'hasta']),
        ]);
    }

    public function create(Equipo $equipo): View
    {
        $this->authorize('create', Traslado::class);

        return view('traslados.create', [
            'equipo' => $equipo->load('ubicacion'),
            'ubicaciones' => Ubicacion::activos()->where('id', '!=', $equipo->ubicacion_id)->orderBy('nombre')->get(['id', 'nombre']),
            'motivos' => MotivoTraslado::cases(),
        ]);
    }

    public function store(StoreTrasladoRequest $request, Equipo $equipo, RegistrarTraslado $accion): RedirectResponse
    {
        $traslado = $accion->ejecutar($equipo, $request);

        return redirect()->route('equipos.show', $equipo)
            ->with('success', "Traslado registrado. El equipo «{$equipo->codigo_interno}» ahora está en {$traslado->ubicacionDestino->nombre}.");
    }

    public function destroy(Traslado $traslado, RegistrarTraslado $accion): RedirectResponse
    {
        $this->authorize('delete', $traslado);

        $equipo = $traslado->equipo;
        $accion->deshacer($traslado);

        return redirect()->route('equipos.show', $equipo)
            ->with('success', 'Se deshizo el último traslado y el equipo volvió a su ubicación anterior.');
    }
}
