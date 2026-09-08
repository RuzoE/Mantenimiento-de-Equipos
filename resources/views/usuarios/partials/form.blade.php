@php
    $esEdicion = isset($usuario);
    $asignados = old('roles', $rolesAsignados ?? []);
@endphp

<form method="POST"
      action="{{ $esEdicion ? route('usuarios.update', $usuario) : route('usuarios.store') }}"
      class="space-y-5">
    @csrf
    @if ($esEdicion)
        @method('PUT')
    @endif

    <x-ui.input name="name" label="Nombre completo" required
                :value="old('name', $usuario->name ?? '')" autofocus />

    <x-ui.input name="email" type="email" label="Correo electrónico" required
                :value="old('email', $usuario->email ?? '')" autocomplete="off" />

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <x-ui.input name="password" type="password" label="Contraseña"
                    :required="! $esEdicion" autocomplete="new-password"
                    :hint="$esEdicion ? 'Déjela en blanco para no cambiarla.' : 'Mínimo 8 caracteres.'" />
        <x-ui.input name="password_confirmation" type="password" label="Confirmar contraseña"
                    :required="! $esEdicion" autocomplete="new-password" />
    </div>

    <div>
        <x-ui.label required class="mb-1">Roles</x-ui.label>
        <div class="space-y-2 rounded-lg border border-gray-200 p-3">
            @foreach ($roles as $rol)
                <label class="flex items-start gap-3">
                    <input type="checkbox" name="roles[]" value="{{ $rol->value }}"
                           @checked(in_array($rol->value, $asignados, true))
                           class="mt-0.5 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                    <span>
                        <span class="block text-sm font-medium text-gray-900">{{ $rol->label() }}</span>
                        <span class="block text-xs text-gray-500">{{ $rol->descripcion() }}</span>
                    </span>
                </label>
            @endforeach
        </div>
        @error('roles')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
        @error('roles.*')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    @unless ($esEdicion)
        <label class="flex items-center gap-3">
            <input type="checkbox" name="activo" value="1" @checked(old('activo', true))
                   class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
            <span class="text-sm text-gray-700">Cuenta activa (puede iniciar sesión)</span>
        </label>
    @endunless

    <div class="flex items-center gap-3 border-t border-gray-100 pt-4">
        <x-ui.button type="submit">{{ $esEdicion ? 'Guardar cambios' : 'Crear usuario' }}</x-ui.button>
        <x-ui.button :href="route('usuarios.index')" variant="ghost">Cancelar</x-ui.button>
    </div>
</form>
