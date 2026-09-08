<?php

namespace App\Http\Controllers\Usuarios;

use App\Enums\RolUsuario;
use App\Http\Controllers\Controller;
use App\Http\Requests\Usuarios\StoreUsuarioRequest;
use App\Http\Requests\Usuarios\UpdateUsuarioRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $usuarios = User::query()
            ->with('roles')
            ->when($request->string('buscar')->trim()->value(), function ($query, $termino) {
                $query->where(function ($q) use ($termino) {
                    $q->where('name', 'like', "%{$termino}%")
                        ->orWhere('email', 'like', "%{$termino}%");
                });
            })
            ->when($request->filled('rol'), fn ($q) => $q->role($request->string('rol')->value()))
            ->when($request->filled('estado'), fn ($q) => $q->where('activo', $request->string('estado')->value() === 'activo'))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('usuarios.index', [
            'usuarios' => $usuarios,
            'roles' => RolUsuario::cases(),
            'filtros' => $request->only(['buscar', 'rol', 'estado']),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('usuarios.create', [
            'roles' => RolUsuario::cases(),
        ]);
    }

    public function store(StoreUsuarioRequest $request): RedirectResponse
    {
        $usuario = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
            'activo' => $request->boolean('activo', true),
        ]);

        $usuario->syncRoles($request->validated('roles'));

        return redirect()->route('usuarios.index')
            ->with('success', "Usuario «{$usuario->name}» creado correctamente.");
    }

    public function edit(User $usuario): View
    {
        $this->authorize('update', $usuario);

        return view('usuarios.edit', [
            'usuario' => $usuario->load('roles'),
            'roles' => RolUsuario::cases(),
            'rolesAsignados' => $usuario->roles->pluck('name')->all(),
        ]);
    }

    public function update(UpdateUsuarioRequest $request, User $usuario): RedirectResponse
    {
        $usuario->fill([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
        ]);

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->validated('password'));
        }

        $usuario->save();
        $usuario->syncRoles($request->validated('roles'));

        return redirect()->route('usuarios.index')
            ->with('success', "Usuario «{$usuario->name}» actualizado correctamente.");
    }

    public function destroy(User $usuario): RedirectResponse
    {
        $this->authorize('delete', $usuario);

        $usuario->update(['activo' => false]);

        return redirect()->route('usuarios.index')
            ->with('success', "Usuario «{$usuario->name}» desactivado.");
    }

    public function reactivar(User $usuario): RedirectResponse
    {
        $this->authorize('restore', $usuario);

        $usuario->update(['activo' => true]);

        return redirect()->route('usuarios.index')
            ->with('success', "Usuario «{$usuario->name}» reactivado.");
    }
}
