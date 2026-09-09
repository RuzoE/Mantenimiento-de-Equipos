<?php

namespace App\Http\Controllers\Equipos;

use App\Enums\EstadoEquipo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Equipos\StoreEquipoRequest;
use App\Http\Requests\Equipos\UpdateEquipoRequest;
use App\Models\Equipo;
use App\Models\Marca;
use App\Models\Responsable;
use App\Models\TipoEquipo;
use App\Models\Ubicacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class EquipoController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Equipo::class);

        $equipos = Equipo::query()
            ->with(['tipoEquipo', 'marca', 'ubicacion', 'responsable'])
            ->buscar($request->string('buscar')->value())
            ->when($request->filled('tipo_equipo_id'), fn ($q) => $q->where('tipo_equipo_id', $request->integer('tipo_equipo_id')))
            ->when($request->filled('marca_id'), fn ($q) => $q->where('marca_id', $request->integer('marca_id')))
            ->when($request->filled('ubicacion_id'), fn ($q) => $q->where('ubicacion_id', $request->integer('ubicacion_id')))
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', $request->string('estado')->value()))
            ->when($request->filled('activo'), fn ($q) => $q->where('activo', $request->string('activo')->value() === 'activo'))
            ->orderBy('codigo_interno')
            ->paginate(15)
            ->withQueryString();

        return view('equipos.index', [
            'equipos' => $equipos,
            'estados' => EstadoEquipo::cases(),
            'filtros' => $request->only(['buscar', 'tipo_equipo_id', 'marca_id', 'ubicacion_id', 'estado', 'activo']),
            ...$this->opcionesCatalogos(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Equipo::class);

        return view('equipos.create', [
            'estados' => EstadoEquipo::cases(),
            ...$this->opcionesCatalogos(),
        ]);
    }

    public function store(StoreEquipoRequest $request): RedirectResponse
    {
        $equipo = Equipo::create($request->validated());

        return redirect()->route('equipos.show', $equipo)
            ->with('success', "Equipo «{$equipo->codigo_interno}» registrado correctamente.");
    }

    public function show(Equipo $equipo): View
    {
        $this->authorize('view', $equipo);

        $equipo->load([
            'tipoEquipo',
            'marca',
            'ubicacion',
            'responsable',
            'mantenimientos' => fn ($q) => $q->recientes()->with('responsable'),
        ]);

        return view('equipos.show', ['equipo' => $equipo]);
    }

    public function edit(Equipo $equipo): View
    {
        $this->authorize('update', $equipo);

        return view('equipos.edit', [
            'equipo' => $equipo,
            'estados' => EstadoEquipo::cases(),
            ...$this->opcionesCatalogos(),
        ]);
    }

    public function update(UpdateEquipoRequest $request, Equipo $equipo): RedirectResponse
    {
        $equipo->update($request->validated());

        return redirect()->route('equipos.show', $equipo)
            ->with('success', "Equipo «{$equipo->codigo_interno}» actualizado correctamente.");
    }

    public function destroy(Equipo $equipo): RedirectResponse
    {
        $this->authorize('delete', $equipo);

        $equipo->update(['activo' => false]);

        return redirect()->route('equipos.index')
            ->with('success', "Equipo «{$equipo->codigo_interno}» desactivado.");
    }

    public function reactivar(Equipo $equipo): RedirectResponse
    {
        $this->authorize('restore', $equipo);

        $equipo->update(['activo' => true]);

        return redirect()->route('equipos.index')
            ->with('success', "Equipo «{$equipo->codigo_interno}» reactivado.");
    }

    /**
     * Opciones activas de los catálogos para formularios y filtros.
     *
     * @return array<string, Collection>
     */
    private function opcionesCatalogos(): array
    {
        return [
            'tipos' => TipoEquipo::activos()->orderBy('nombre')->get(['id', 'nombre']),
            'marcas' => Marca::activos()->orderBy('nombre')->get(['id', 'nombre']),
            'ubicaciones' => Ubicacion::activos()->orderBy('nombre')->get(['id', 'nombre']),
            'responsables' => Responsable::activos()->orderBy('nombre')->get(['id', 'nombre']),
        ];
    }
}
