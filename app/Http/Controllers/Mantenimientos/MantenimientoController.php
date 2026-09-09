<?php

namespace App\Http\Controllers\Mantenimientos;

use App\Actions\Mantenimientos\GuardarMantenimiento;
use App\Enums\EstadoEquipo;
use App\Enums\TipoMantenimiento;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mantenimientos\StoreMantenimientoRequest;
use App\Http\Requests\Mantenimientos\UpdateMantenimientoRequest;
use App\Models\Equipo;
use App\Models\Mantenimiento;
use App\Models\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MantenimientoController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Mantenimiento::class);

        $termino = $request->string('buscar')->trim()->value();

        $mantenimientos = Mantenimiento::query()
            ->with(['equipo', 'responsable'])
            ->when($termino !== '', fn (Builder $q) => $q->whereHas(
                'equipo',
                fn (Builder $e) => $e->where('codigo_interno', 'like', "%{$termino}%")
            ))
            ->when($request->filled('tipo'), fn (Builder $q) => $q->where('tipo', $request->string('tipo')->value()))
            ->when($request->filled('responsable_id'), fn (Builder $q) => $q->where('responsable_id', $request->integer('responsable_id')))
            ->when($request->filled('desde'), fn (Builder $q) => $q->whereDate('fecha', '>=', $request->date('desde')))
            ->when($request->filled('hasta'), fn (Builder $q) => $q->whereDate('fecha', '<=', $request->date('hasta')))
            ->recientes()
            ->paginate(15)
            ->withQueryString();

        return view('mantenimientos.index', [
            'mantenimientos' => $mantenimientos,
            'tipos' => TipoMantenimiento::cases(),
            'responsables' => Responsable::activos()->orderBy('nombre')->get(['id', 'nombre']),
            'filtros' => $request->only(['buscar', 'tipo', 'responsable_id', 'desde', 'hasta']),
        ]);
    }

    public function create(Equipo $equipo): View
    {
        $this->authorize('create', Mantenimiento::class);

        return view('mantenimientos.create', $this->datosFormulario() + ['equipo' => $equipo]);
    }

    public function store(StoreMantenimientoRequest $request, Equipo $equipo, GuardarMantenimiento $accion): RedirectResponse
    {
        $mantenimiento = $accion->crear($equipo, $request);

        return redirect()->route('mantenimientos.show', $mantenimiento)
            ->with('success', "Mantenimiento del equipo «{$equipo->codigo_interno}» registrado correctamente.");
    }

    public function show(Mantenimiento $mantenimiento): View
    {
        $this->authorize('view', $mantenimiento);

        $mantenimiento->load(['equipo', 'responsable', 'registradoPor', 'actividades', 'evidencias']);

        return view('mantenimientos.show', ['mantenimiento' => $mantenimiento]);
    }

    public function edit(Mantenimiento $mantenimiento): View
    {
        $this->authorize('update', $mantenimiento);

        $mantenimiento->load(['equipo', 'actividades', 'evidencias']);

        return view('mantenimientos.edit', $this->datosFormulario() + [
            'equipo' => $mantenimiento->equipo,
            'mantenimiento' => $mantenimiento,
        ]);
    }

    public function update(UpdateMantenimientoRequest $request, Mantenimiento $mantenimiento, GuardarMantenimiento $accion): RedirectResponse
    {
        $accion->actualizar($mantenimiento, $request);

        return redirect()->route('mantenimientos.show', $mantenimiento)
            ->with('success', 'Mantenimiento actualizado correctamente.');
    }

    public function destroy(Mantenimiento $mantenimiento): RedirectResponse
    {
        $this->authorize('delete', $mantenimiento);

        Storage::disk('local')->deleteDirectory("evidencias/{$mantenimiento->id}");
        $mantenimiento->delete();

        return redirect()->route('mantenimientos.index')
            ->with('success', 'Mantenimiento eliminado.');
    }

    /**
     * @return array<string, mixed>
     */
    private function datosFormulario(): array
    {
        return [
            'tipos' => TipoMantenimiento::cases(),
            'estados' => EstadoEquipo::cases(),
            'responsables' => Responsable::activos()->orderBy('nombre')->get(['id', 'nombre']),
        ];
    }
}
