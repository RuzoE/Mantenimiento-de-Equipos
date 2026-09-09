<?php

namespace Tests\Feature;

use App\Enums\RolUsuario;
use App\Models\Equipo;
use App\Models\User;
use Database\Seeders\CatalogoSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Barrido de autorización sobre las rutas principales para cada rol.
 */
class AutorizacionRutasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolePermissionSeeder::class, CatalogoSeeder::class]);
    }

    private function usuario(RolUsuario $rol): User
    {
        return User::factory()->create()->assignRole($rol->value);
    }

    /**
     * Rutas GET de solo lectura que los tres roles pueden ver.
     *
     * @return array<int, string>
     */
    private function rutasDeConsulta(): array
    {
        return [
            route('dashboard'),
            route('equipos.index'),
            route('mantenimientos.index'),
            route('programaciones.index'),
            route('traslados.index'),
            route('alertas.index'),
            route('catalogos.tipos-equipo.index'),
            route('catalogos.marcas.index'),
            route('catalogos.ubicaciones.index'),
            route('catalogos.responsables.index'),
            route('reportes.index'),
            route('reportes.equipos'),
            route('reportes.mantenimientos'),
        ];
    }

    public function test_a_guest_is_redirected_from_every_main_route(): void
    {
        foreach ([...$this->rutasDeConsulta(), route('usuarios.index'), route('auditoria.index')] as $ruta) {
            $this->get($ruta)->assertRedirect(route('login'));
        }
    }

    public function test_the_three_roles_can_open_the_read_only_routes(): void
    {
        foreach ([RolUsuario::Administrador, RolUsuario::Tecnico, RolUsuario::Consulta] as $rol) {
            $usuario = $this->usuario($rol);

            foreach ($this->rutasDeConsulta() as $ruta) {
                $this->actingAs($usuario)->get($ruta)->assertOk();
            }
        }
    }

    public function test_only_the_administrator_reaches_users_and_audit(): void
    {
        $rutas = [route('usuarios.index'), route('usuarios.create'), route('auditoria.index')];

        foreach ([RolUsuario::Tecnico, RolUsuario::Consulta] as $rol) {
            foreach ($rutas as $ruta) {
                $this->actingAs($this->usuario($rol))->get($ruta)->assertForbidden();
            }
        }

        foreach ($rutas as $ruta) {
            $this->actingAs($this->usuario(RolUsuario::Administrador))->get($ruta)->assertOk();
        }
    }

    public function test_only_administrator_and_technician_manage_equipos_and_maintenance(): void
    {
        $equipo = Equipo::factory()->create();

        // Alta de equipo: solo Administrador
        $this->actingAs($this->usuario(RolUsuario::Consulta))->get(route('equipos.create'))->assertForbidden();
        $this->actingAs($this->usuario(RolUsuario::Tecnico))->get(route('equipos.create'))->assertForbidden();
        $this->actingAs($this->usuario(RolUsuario::Administrador))->get(route('equipos.create'))->assertOk();

        // Registrar mantenimiento: Administrador y Técnico
        $this->actingAs($this->usuario(RolUsuario::Consulta))
            ->get(route('equipos.mantenimientos.create', $equipo))->assertForbidden();
        $this->actingAs($this->usuario(RolUsuario::Tecnico))
            ->get(route('equipos.mantenimientos.create', $equipo))->assertOk();
        $this->actingAs($this->usuario(RolUsuario::Administrador))
            ->get(route('equipos.mantenimientos.create', $equipo))->assertOk();
    }

    public function test_public_registration_routes_no_longer_exist(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', [])->assertNotFound();
    }
}
