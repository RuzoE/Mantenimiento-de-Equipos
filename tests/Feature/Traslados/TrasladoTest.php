<?php

namespace Tests\Feature\Traslados;

use App\Enums\MotivoTraslado;
use App\Enums\RolUsuario;
use App\Models\Equipo;
use App\Models\Traslado;
use App\Models\Ubicacion;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrasladoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function tecnico(): User
    {
        return User::factory()->create()->assignRole(RolUsuario::Tecnico->value);
    }

    private function consulta(): User
    {
        return User::factory()->create()->assignRole(RolUsuario::Consulta->value);
    }

    private function admin(): User
    {
        return User::factory()->create()->assignRole(RolUsuario::Administrador->value);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'ubicacion_destino_id' => Ubicacion::factory()->create()->id,
            'fecha' => now()->subDay()->format('Y-m-d'),
            'motivo' => MotivoTraslado::Reorganizacion->value,
            'observaciones' => 'Cambio de aula.',
        ], $overrides);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('traslados.index'))->assertRedirect(route('login'));
    }

    public function test_consulta_can_view_but_not_register(): void
    {
        $equipo = Equipo::factory()->create();
        $this->actingAs($this->consulta());

        $this->get(route('traslados.index'))->assertOk();
        $this->get(route('equipos.traslados.create', $equipo))->assertForbidden();
        $this->post(route('equipos.traslados.store', $equipo), $this->payload())->assertForbidden();
    }

    public function test_registering_a_traslado_moves_the_equipo_and_records_the_origin(): void
    {
        $origen = Ubicacion::factory()->create();
        $destino = Ubicacion::factory()->create();
        $equipo = Equipo::factory()->create(['ubicacion_id' => $origen->id]);
        $tecnico = $this->tecnico();

        $this->actingAs($tecnico)
            ->post(route('equipos.traslados.store', $equipo), $this->payload(['ubicacion_destino_id' => $destino->id]))
            ->assertRedirect(route('equipos.show', $equipo));

        $this->assertSame($destino->id, $equipo->refresh()->ubicacion_id);
        $this->assertDatabaseHas('traslados', [
            'equipo_id' => $equipo->id,
            'ubicacion_origen_id' => $origen->id,
            'ubicacion_destino_id' => $destino->id,
            'registrado_por_id' => $tecnico->id,
        ]);
    }

    public function test_destination_must_differ_from_the_current_location(): void
    {
        $ubicacion = Ubicacion::factory()->create();
        $equipo = Equipo::factory()->create(['ubicacion_id' => $ubicacion->id]);

        $this->actingAs($this->tecnico())
            ->post(route('equipos.traslados.store', $equipo), $this->payload(['ubicacion_destino_id' => $ubicacion->id]))
            ->assertSessionHasErrors('ubicacion_destino_id');
    }

    public function test_fecha_cannot_be_in_the_future_and_motivo_is_required(): void
    {
        $equipo = Equipo::factory()->create();

        $this->actingAs($this->tecnico())
            ->post(route('equipos.traslados.store', $equipo), $this->payload([
                'fecha' => now()->addWeek()->format('Y-m-d'),
                'motivo' => '',
            ]))
            ->assertSessionHasErrors(['fecha', 'motivo']);
    }

    public function test_history_is_preserved_across_moves(): void
    {
        $a = Ubicacion::factory()->create();
        $b = Ubicacion::factory()->create();
        $c = Ubicacion::factory()->create();
        $equipo = Equipo::factory()->create(['ubicacion_id' => $a->id]);
        $tecnico = $this->tecnico();

        $this->actingAs($tecnico)->post(route('equipos.traslados.store', $equipo), $this->payload(['ubicacion_destino_id' => $b->id]));
        $this->actingAs($tecnico)->post(route('equipos.traslados.store', $equipo), $this->payload(['ubicacion_destino_id' => $c->id]));

        $this->assertDatabaseCount('traslados', 2);
        $this->assertSame($c->id, $equipo->refresh()->ubicacion_id);
    }

    public function test_admin_can_undo_the_latest_traslado(): void
    {
        $origen = Ubicacion::factory()->create();
        $destino = Ubicacion::factory()->create();
        $equipo = Equipo::factory()->create(['ubicacion_id' => $origen->id]);

        $this->actingAs($this->tecnico())
            ->post(route('equipos.traslados.store', $equipo), $this->payload(['ubicacion_destino_id' => $destino->id]));

        $traslado = Traslado::firstOrFail();

        $this->actingAs($this->admin())
            ->delete(route('traslados.destroy', $traslado))
            ->assertRedirect(route('equipos.show', $equipo));

        $this->assertModelMissing($traslado);
        $this->assertSame($origen->id, $equipo->refresh()->ubicacion_id);
    }

    public function test_non_admin_cannot_undo_a_traslado(): void
    {
        $traslado = Traslado::factory()->create();

        $this->actingAs($this->tecnico())
            ->delete(route('traslados.destroy', $traslado))
            ->assertForbidden();
    }

    public function test_only_the_latest_traslado_can_be_undone(): void
    {
        $equipo = Equipo::factory()->create();
        $viejo = Traslado::factory()->for($equipo)->create();
        $nuevo = Traslado::factory()->for($equipo)->create();

        $this->actingAs($this->admin())
            ->delete(route('traslados.destroy', $viejo))
            ->assertForbidden();

        $this->assertModelExists($viejo);
    }

    public function test_index_can_be_filtered_by_destination(): void
    {
        $destino = Ubicacion::factory()->create();
        $conDestino = Traslado::factory()->create(['ubicacion_destino_id' => $destino->id]);
        $otro = Traslado::factory()->create();

        $this->actingAs($this->tecnico())
            ->get(route('traslados.index', ['ubicacion_destino_id' => $destino->id]))
            ->assertOk()
            ->assertSee($conDestino->equipo->codigo_interno)
            ->assertDontSee($otro->equipo->codigo_interno);
    }
}
