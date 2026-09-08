<x-app-layout>
    <x-slot name="title">Usuarios</x-slot>
    <x-slot name="header">Usuarios</x-slot>
    <x-slot name="subheader">Gestione las cuentas de acceso, sus roles y su estado.</x-slot>

    {{-- Barra de acciones y filtros --}}
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <form method="GET" action="{{ route('usuarios.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-[1fr_auto_auto_auto]">
            <x-ui.input name="buscar" :value="$filtros['buscar'] ?? ''" placeholder="Buscar por nombre o correo"
                        class="sm:w-64" aria-label="Buscar" />

            <select name="rol" class="rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                <option value="">Todos los roles</option>
                @foreach ($roles as $rol)
                    <option value="{{ $rol->value }}" @selected(($filtros['rol'] ?? '') === $rol->value)>{{ $rol->label() }}</option>
                @endforeach
            </select>

            <select name="estado" class="rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                <option value="">Todos los estados</option>
                <option value="activo" @selected(($filtros['estado'] ?? '') === 'activo')>Activos</option>
                <option value="inactivo" @selected(($filtros['estado'] ?? '') === 'inactivo')>Inactivos</option>
            </select>

            <x-ui.button type="submit" variant="secondary">Filtrar</x-ui.button>
        </form>

        @can('usuarios.crear')
            <x-ui.button :href="route('usuarios.create')">
                <x-ui.icon name="users" class="h-4 w-4" />
                Nuevo usuario
            </x-ui.button>
        @endcan
    </div>

    @if ($usuarios->isEmpty())
        <x-ui.card>
            <x-ui.empty-state icon="users" title="No hay usuarios"
                message="No se encontraron usuarios con los filtros aplicados." />
        </x-ui.card>
    @else
        <x-ui.table>
            <x-slot name="head">
                <th class="px-4 py-3">Nombre</th>
                <th class="px-4 py-3">Correo</th>
                <th class="px-4 py-3">Roles</th>
                <th class="px-4 py-3">Estado</th>
                <th class="px-4 py-3 text-right">Acciones</th>
            </x-slot>

            @foreach ($usuarios as $usuario)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $usuario->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $usuario->email }}</td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-1">
                            @forelse ($usuario->roles as $rol)
                                <x-ui.badge color="brand">{{ \App\Enums\RolUsuario::from($rol->name)->label() }}</x-ui.badge>
                            @empty
                                <span class="text-xs text-gray-400">Sin rol</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        @if ($usuario->activo)
                            <x-ui.badge color="green">Activo</x-ui.badge>
                        @else
                            <x-ui.badge color="gray">Inactivo</x-ui.badge>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-2">
                            @can('update', $usuario)
                                <x-ui.button :href="route('usuarios.edit', $usuario)" variant="secondary" size="sm">Editar</x-ui.button>
                            @endcan

                            @if ($usuario->activo)
                                @can('delete', $usuario)
                                    <form method="POST" action="{{ route('usuarios.destroy', $usuario) }}"
                                          onsubmit="return confirm('¿Desactivar a {{ $usuario->name }}?')">
                                        @csrf @method('DELETE')
                                        <x-ui.button type="submit" variant="danger" size="sm">Desactivar</x-ui.button>
                                    </form>
                                @endcan
                            @else
                                @can('restore', $usuario)
                                    <form method="POST" action="{{ route('usuarios.reactivar', $usuario) }}">
                                        @csrf @method('PATCH')
                                        <x-ui.button type="submit" variant="secondary" size="sm">Reactivar</x-ui.button>
                                    </form>
                                @endcan
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-ui.table>

        <div class="mt-4">
            {{ $usuarios->links() }}
        </div>
    @endif
</x-app-layout>
