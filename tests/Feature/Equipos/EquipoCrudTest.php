<?php

namespace Tests\Feature\Equipos;

use App\Enums\EstadoEquipo;
use App\Enums\RolUsuario;
use App\Models\Equipo;
use App\Models\Marca;
use App\Models\TipoEquipo;
use App\Models\Ubicacion;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EquipoCrudTest extends TestCase
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

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'codigo_interno' => 'PC-100',
            'tipo_equipo_id' => TipoEquipo::factory()->create()->id,
            'marca_id' => Marca::factory()->create()->id,
            'modelo' => 'ThinkCentre',
            'numero_serie' => 'SN-ABC-1',
            'ubicacion_id' => Ubicacion::factory()->create()->id,
            'responsable_id' => null,
            'estado' => EstadoEquipo::Operativo->value,
            'fecha_adquisicion' => '2024-01-15',
            'fecha_garantia' => '2026-01-15',
        ], $overrides);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('equipos.index'))->assertRedirect(route('login'));
    }

    public function test_consulta_can_view_but_not_manage(): void
    {
        $equipo = Equipo::factory()->create();
        $this->actingAs($this->consulta());

        $this->get(route('equipos.index'))->assertOk();
        $this->get(route('equipos.show', $equipo))->assertOk();
        $this->get(route('equipos.create'))->assertForbidden();
        $this->post(route('equipos.store'), $this->payload())->assertForbidden();
        $this->get(route('equipos.edit', $equipo))->assertForbidden();
        $this->delete(route('equipos.destroy', $equipo))->assertForbidden();
    }

    public function test_admin_can_register_an_equipo(): void
    {
        $this->actingAs($this->admin());

        $this->post(route('equipos.store'), $this->payload())
            ->assertRedirect(route('equipos.show', Equipo::first()));

        $this->assertDatabaseHas('equipos', ['codigo_interno' => 'PC-100', 'activo' => true]);
    }

    public function test_codigo_interno_is_normalised_to_uppercase(): void
    {
        $this->actingAs($this->admin());

        $this->post(route('equipos.store'), $this->payload(['codigo_interno' => 'pc-xyz']));

        $this->assertDatabaseHas('equipos', ['codigo_interno' => 'PC-XYZ']);
    }

    public function test_codigo_interno_is_required(): void
    {
        $this->actingAs($this->admin());

        $this->post(route('equipos.store'), $this->payload(['codigo_interno' => '']))
            ->assertSessionHasErrors('codigo_interno');
    }

    public function test_codigo_interno_must_be_unique(): void
    {
        $this->actingAs($this->admin());
        Equipo::factory()->create(['codigo_interno' => 'PC-100']);

        $this->post(route('equipos.store'), $this->payload(['codigo_interno' => 'PC-100']))
            ->assertSessionHasErrors('codigo_interno');
    }

    public function test_numero_serie_must_be_unique_when_present(): void
    {
        $this->actingAs($this->admin());
        Equipo::factory()->create(['numero_serie' => 'SN-DUP']);

        $this->post(route('equipos.store'), $this->payload(['numero_serie' => 'SN-DUP']))
            ->assertSessionHasErrors('numero_serie');
    }

    public function test_warranty_date_cannot_predate_acquisition_date(): void
    {
        $this->actingAs($this->admin());

        $this->post(route('equipos.store'), $this->payload([
            'fecha_adquisicion' => '2024-06-01',
            'fecha_garantia' => '2024-01-01',
        ]))->assertSessionHasErrors('fecha_garantia');
    }

    public function test_admin_can_update_an_equipo_keeping_its_code(): void
    {
        $this->actingAs($this->admin());
        $equipo = Equipo::factory()->create(['codigo_interno' => 'PC-777', 'modelo' => 'Viejo']);

        $this->put(route('equipos.update', $equipo), $this->payload([
            'codigo_interno' => 'PC-777',
            'modelo' => 'Nuevo',
        ]))->assertRedirect(route('equipos.show', $equipo));

        $this->assertDatabaseHas('equipos', ['id' => $equipo->id, 'codigo_interno' => 'PC-777', 'modelo' => 'Nuevo']);
    }

    public function test_admin_can_deactivate_and_reactivate_an_equipo(): void
    {
        $this->actingAs($this->admin());
        $equipo = Equipo::factory()->create();

        $this->delete(route('equipos.destroy', $equipo))->assertRedirect(route('equipos.index'));
        $this->assertDatabaseHas('equipos', ['id' => $equipo->id, 'activo' => false]);

        $this->patch(route('equipos.reactivar', $equipo))->assertRedirect(route('equipos.index'));
        $this->assertDatabaseHas('equipos', ['id' => $equipo->id, 'activo' => true]);
    }

    public function test_index_can_be_filtered_by_estado(): void
    {
        $this->actingAs($this->admin());
        Equipo::factory()->create(['codigo_interno' => 'AAA-OK', 'estado' => EstadoEquipo::Operativo]);
        Equipo::factory()->create(['codigo_interno' => 'BBB-DAN', 'estado' => EstadoEquipo::Danado]);

        $this->get(route('equipos.index', ['estado' => EstadoEquipo::Danado->value]))
            ->assertOk()
            ->assertSee('BBB-DAN')
            ->assertDontSee('AAA-OK');
    }

    public function test_index_can_be_filtered_by_tipo(): void
    {
        $this->actingAs($this->admin());
        $tipo = TipoEquipo::factory()->create();
        Equipo::factory()->create(['codigo_interno' => 'DEL-TIPO', 'tipo_equipo_id' => $tipo->id]);
        Equipo::factory()->create(['codigo_interno' => 'OTRO-TIPO']);

        $this->get(route('equipos.index', ['tipo_equipo_id' => $tipo->id]))
            ->assertOk()
            ->assertSee('DEL-TIPO')
            ->assertDontSee('OTRO-TIPO');
    }
}
