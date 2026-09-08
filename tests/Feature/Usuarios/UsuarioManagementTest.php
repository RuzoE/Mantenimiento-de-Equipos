<?php

namespace Tests\Feature\Usuarios;

use App\Enums\Permiso;
use App\Enums\RolUsuario;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsuarioManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function admin(): User
    {
        return User::factory()->create()->assignRole(RolUsuario::Administrador->value);
    }

    private function consulta(): User
    {
        return User::factory()->create()->assignRole(RolUsuario::Consulta->value);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('usuarios.index'))->assertRedirect(route('login'));
    }

    public function test_consulta_user_cannot_access_user_management(): void
    {
        $this->actingAs($this->consulta());

        $this->get(route('usuarios.index'))->assertForbidden();
        $this->get(route('usuarios.create'))->assertForbidden();
    }

    public function test_admin_can_list_users(): void
    {
        $this->actingAs($this->admin());

        $this->get(route('usuarios.index'))->assertOk()->assertViewIs('usuarios.index');
    }

    public function test_admin_can_create_a_user_with_roles(): void
    {
        $this->actingAs($this->admin());

        $response = $this->post(route('usuarios.store'), [
            'name' => 'Nuevo Técnico',
            'email' => 'tecnico@policarpa.edu.co',
            'password' => 'clave-super-segura',
            'password_confirmation' => 'clave-super-segura',
            'roles' => [RolUsuario::Tecnico->value],
            'activo' => '1',
        ]);

        $response->assertRedirectToRoute('usuarios.index');
        $nuevo = User::where('email', 'tecnico@policarpa.edu.co')->first();
        $this->assertNotNull($nuevo);
        $this->assertTrue($nuevo->hasRole(RolUsuario::Tecnico->value));
        $this->assertTrue($nuevo->activo);
    }

    public function test_store_requires_at_least_one_role(): void
    {
        $this->actingAs($this->admin());

        $this->post(route('usuarios.store'), [
            'name' => 'Sin Rol',
            'email' => 'sinrol@policarpa.edu.co',
            'password' => 'clave-super-segura',
            'password_confirmation' => 'clave-super-segura',
        ])->assertSessionHasErrors('roles');
    }

    public function test_admin_can_update_a_user_and_change_roles(): void
    {
        $this->actingAs($this->admin());
        $user = User::factory()->create()->assignRole(RolUsuario::Consulta->value);

        $this->put(route('usuarios.update', $user), [
            'name' => 'Nombre Editado',
            'email' => $user->email,
            'roles' => [RolUsuario::Tecnico->value],
        ])->assertRedirectToRoute('usuarios.index');

        $user->refresh();
        $this->assertSame('Nombre Editado', $user->name);
        $this->assertTrue($user->hasRole(RolUsuario::Tecnico->value));
        $this->assertFalse($user->hasRole(RolUsuario::Consulta->value));
    }

    public function test_admin_can_deactivate_and_reactivate_a_user(): void
    {
        $this->actingAs($this->admin());
        $user = User::factory()->create()->assignRole(RolUsuario::Consulta->value);

        $this->delete(route('usuarios.destroy', $user))->assertRedirectToRoute('usuarios.index');
        $this->assertFalse($user->refresh()->activo);

        $this->patch(route('usuarios.reactivar', $user))->assertRedirectToRoute('usuarios.index');
        $this->assertTrue($user->refresh()->activo);
    }

    public function test_admin_cannot_deactivate_themselves(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        $this->delete(route('usuarios.destroy', $admin))->assertForbidden();
        $this->assertTrue($admin->refresh()->activo);
    }

    public function test_the_last_active_administrator_cannot_be_deactivated(): void
    {
        $onlyAdmin = User::factory()->create()->assignRole(RolUsuario::Administrador->value);

        // Un operador con permiso para eliminar, pero que no es Administrador.
        $operador = User::factory()->create()->assignRole(RolUsuario::Consulta->value);
        $operador->givePermissionTo(Permiso::UsuariosEliminar->value);
        $this->actingAs($operador);

        $this->delete(route('usuarios.destroy', $onlyAdmin))->assertForbidden();
        $this->assertTrue($onlyAdmin->refresh()->activo);

        // Con un segundo Administrador activo, ya se puede desactivar a uno de ellos.
        User::factory()->create()->assignRole(RolUsuario::Administrador->value);
        $this->delete(route('usuarios.destroy', $onlyAdmin))->assertRedirectToRoute('usuarios.index');
        $this->assertFalse($onlyAdmin->refresh()->activo);
    }
}
