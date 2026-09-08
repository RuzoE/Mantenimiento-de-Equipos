<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * CRUD compartido por todos los catálogos. Cada subclase declara su modelo,
 * su nombre de ruta, sus etiquetas, sus campos y sus reglas de validación.
 */
abstract class CatalogoController extends Controller
{
    /** @var class-string<Model> */
    protected string $modelClass;

    /** Prefijo de ruta, p. ej. "catalogos.marcas". */
    protected string $rutaBase;

    /** Etiqueta en singular, p. ej. "marca". */
    protected string $singular;

    /** Etiqueta en plural para el título, p. ej. "Marcas". */
    protected string $plural;

    /** Texto del botón / título de alta, con el género correcto. */
    protected string $etiquetaNueva = '';

    protected function etiquetaNueva(): string
    {
        return $this->etiquetaNueva !== '' ? $this->etiquetaNueva : 'Nuevo '.$this->singular;
    }

    /**
     * Definición declarativa de los campos del formulario y del listado.
     *
     * @return array<int, array{name:string, label:string, type?:string, required?:bool, hint?:string, listable?:bool}>
     */
    abstract protected function campos(): array;

    /**
     * Reglas de validación. $ignore es el modelo a excluir en las reglas unique.
     *
     * @return array<string, mixed>
     */
    abstract protected function reglas(?Model $ignore = null): array;

    /**
     * @return array<int, string>
     */
    protected function columnasBusqueda(): array
    {
        return ['nombre'];
    }

    protected function query(): Builder
    {
        return $this->modelClass::query();
    }

    protected function resolver(int|string $id): Model
    {
        return $this->query()->findOrFail($id);
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', $this->modelClass);

        $termino = $request->string('buscar')->trim()->value();

        $items = $this->query()
            ->when($termino !== '', function (Builder $q) use ($termino) {
                $q->where(function (Builder $sub) use ($termino) {
                    foreach ($this->columnasBusqueda() as $col) {
                        $sub->orWhere($col, 'like', "%{$termino}%");
                    }
                });
            })
            ->when($request->filled('estado'), fn (Builder $q) => $q->where('activo', $request->string('estado')->value() === 'activo'))
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('catalogos.index', [
            'items' => $items,
            'campos' => $this->campos(),
            'rutaBase' => $this->rutaBase,
            'titulo' => $this->plural,
            'etiquetaNueva' => $this->etiquetaNueva(),
            'filtros' => $request->only(['buscar', 'estado']),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', $this->modelClass);

        return view('catalogos.create', [
            'campos' => $this->campos(),
            'rutaBase' => $this->rutaBase,
            'etiquetaNueva' => $this->etiquetaNueva(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', $this->modelClass);

        $datos = $request->validate($this->reglas());
        $datos['activo'] = $request->boolean('activo', true);

        $item = $this->modelClass::create($datos);

        return redirect()->route("{$this->rutaBase}.index")
            ->with('success', ucfirst($this->singular)." «{$item->nombre}» creado correctamente.");
    }

    public function edit(int $id): View
    {
        $item = $this->resolver($id);
        $this->authorize('update', $item);

        return view('catalogos.edit', [
            'item' => $item,
            'campos' => $this->campos(),
            'rutaBase' => $this->rutaBase,
            'singular' => $this->singular,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $item = $this->resolver($id);
        $this->authorize('update', $item);

        $item->update($request->validate($this->reglas($item)));

        return redirect()->route("{$this->rutaBase}.index")
            ->with('success', ucfirst($this->singular)." «{$item->nombre}» actualizado correctamente.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $item = $this->resolver($id);
        $this->authorize('delete', $item);

        $item->update(['activo' => false]);

        return redirect()->route("{$this->rutaBase}.index")
            ->with('success', ucfirst($this->singular)." «{$item->nombre}» desactivado.");
    }

    public function reactivar(int $id): RedirectResponse
    {
        $item = $this->resolver($id);
        $this->authorize('restore', $item);

        $item->update(['activo' => true]);

        return redirect()->route("{$this->rutaBase}.index")
            ->with('success', ucfirst($this->singular)." «{$item->nombre}» reactivado.");
    }
}
