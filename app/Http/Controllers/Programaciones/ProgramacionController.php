<?php

namespace App\Http\Controllers\Programaciones;

use App\Enums\EstadoProgramacion;
use App\Enums\FrecuenciaMantenimiento;
use App\Enums\TipoMantenimiento;
use App\Http\Controllers\Controller;
use App\Http\Requests\Programaciones\StoreProgramacionRequest;
use App\Http\Requests\Programaciones\UpdateProgramacionRequest;
use App\Models\Equipo;
use App\Models\Programacion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgramacionController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Programacion::class);

        $termino = $request->string('buscar')->trim()->value();

        $programaciones = Programacion::query()
            ->with('equipo')
            ->when($termino !== '', fn (Builder $q) => $q->whereHas(
                'equipo',
                fn (Builder $e) => $e->where('codigo_interno', 'like', "%{$termino}%")
            ))
            ->when($request->filled('tipo'), fn (Builder $q) => $q->where('tipo', $request->string('tipo')->value()))
            ->when($request->string('estado')->value() === 'vencidas', fn (Builder $q) => $q->vencidas())
            ->when($request->string('estado')->value() === 'proximas', fn (Builder $q) => $q->proximas())
            ->when($request->string('estado')->value() === 'canceladas', fn (Builder $q) => $q->where('estado', EstadoProgramacion::Cancelado->value))
            ->orderBy('proxima_fecha')
            ->paginate(15)
            ->withQueryString();

        return view('programaciones.index', [
            'programaciones' => $programaciones,
            'tipos' => TipoMantenimiento::cases(),
            'filtros' => $request->only(['buscar', 'tipo', 'estado']),
        ]);
    }

    public function create(Equipo $equipo): View
    {
        $this->authorize('create', Programacion::class);

        return view('programaciones.create', $this->datosFormulario() + ['equipo' => $equipo]);
    }

    public function store(StoreProgramacionRequest $request, Equipo $equipo): RedirectResponse
    {
        $equipo->programaciones()->create($request->validated());

        return redirect()->route('equipos.show', $equipo)
            ->with('success', "Programación de mantenimiento creada para el equipo «{$equipo->codigo_interno}».");
    }

    public function edit(Programacion $programacion): View
    {
        $this->authorize('update', $programacion);

        $programacion->load('equipo');

        return view('programaciones.edit', $this->datosFormulario() + [
            'equipo' => $programacion->equipo,
            'programacion' => $programacion,
        ]);
    }

    public function update(UpdateProgramacionRequest $request, Programacion $programacion): RedirectResponse
    {
        $programacion->update($request->validated());

        return redirect()->route('equipos.show', $programacion->equipo)
            ->with('success', 'Programación actualizada correctamente.');
    }

    public function destroy(Programacion $programacion): RedirectResponse
    {
        $this->authorize('delete', $programacion);

        $programacion->delete();

        return redirect()->route('programaciones.index')
            ->with('success', 'Programación eliminada.');
    }

    public function realizar(Request $request, Programacion $programacion): RedirectResponse
    {
        $this->authorize('update', $programacion);

        $fecha = $request->date('fecha');
        $programacion->registrarCumplimiento($fecha && $fecha->lte(now()) ? $fecha : null);

        return back()->with('success', 'Cumplimiento registrado. La próxima fecha se recalculó automáticamente.');
    }

    public function cancelar(Programacion $programacion): RedirectResponse
    {
        $this->authorize('update', $programacion);

        $programacion->update(['estado' => EstadoProgramacion::Cancelado]);

        return back()->with('success', 'Programación cancelada.');
    }

    public function reactivar(Programacion $programacion): RedirectResponse
    {
        $this->authorize('update', $programacion);

        $programacion->update(['estado' => EstadoProgramacion::Programado]);

        return back()->with('success', 'Programación reactivada.');
    }

    /**
     * @return array<string, mixed>
     */
    private function datosFormulario(): array
    {
        return [
            'tipos' => TipoMantenimiento::cases(),
            'frecuencias' => FrecuenciaMantenimiento::cases(),
        ];
    }
}
