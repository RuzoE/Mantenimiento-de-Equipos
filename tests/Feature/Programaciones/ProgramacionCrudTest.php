<?php

namespace Tests\Feature\Programaciones;

use App\Enums\EstadoProgramacion;
use App\Enums\FrecuenciaMantenimiento;
use App\Enums\RolUsuario;
use App\Enums\TipoMantenimiento;
use App\Models\Equipo;
use App\Models\Programacion;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgramacionCrudTest extends TestCase
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

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'tipo' => TipoMantenimiento::Preventivo->value,
            'frecuencia' => FrecuenciaMantenimiento::Trimestral->value,
            'fecha_ultimo_mantenimiento' => '2026-01-01',
            'observaciones' => 'Preventivo trimestral.',
        ], $overrides);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('programaciones.index'))->assertRedirect(route('login'));
    }

    public function test_consulta_can_view_but_not_manage(): void
    {
        $programacion = Programacion::factory()->for(Equipo::factory())->create();
        $this->actingAs($this->consulta());

        $this->get(route('programaciones.index'))->assertOk();
        $this->get(route('equipos.programaciones.create', $programacion->equipo))->assertForbidden();
        $this->post(route('equipos.programaciones.store', $programacion->equipo), $this->payload())->assertForbidden();
        $this->get(route('programaciones.edit', $programacion))->assertForbidden();
        $this->patch(route('programaciones.realizar', $programacion))->assertForbidden();
        $this->delete(route('programaciones.destroy', $programacion))->assertForbidden();
    }

    public function test_tecnico_creates_a_programacion_with_computed_next_date(): void
    {
        $equipo = Equipo::factory()->create();

        $this->actingAs($this->tecnico())
            ->post(route('equipos.programaciones.store', $equipo), $this->payload())
            ->assertRedirect(route('equipos.show', $equipo));

        $this->assertDatabaseHas('programaciones_mantenimiento', [
            'equipo_id' => $equipo->id,
            'proxima_fecha' => '2026-04-01', // 2026-01-01 + 90 días
            'estado' => EstadoProgramacion::Programado->value,
        ]);
    }

    public function test_frecuencia_and_tipo_are_required(): void
    {
        $equipo = Equipo::factory()->create();

        $this->actingAs($this->tecnico())
            ->post(route('equipos.programaciones.store', $equipo), $this->payload(['frecuencia' => '', 'tipo' => '']))
            ->assertSessionHasErrors(['frecuencia', 'tipo']);
    }

    public function test_last_date_cannot_be_in_the_future(): void
    {
        $equipo = Equipo::factory()->create();

        $this->actingAs($this->tecnico())
            ->post(route('equipos.programaciones.store', $equipo), $this->payload([
                'fecha_ultimo_mantenimiento' => now()->addMonth()->format('Y-m-d'),
            ]))
            ->assertSessionHasErrors('fecha_ultimo_mantenimiento');
    }

    public function test_tecnico_can_update_a_programacion(): void
    {
        $programacion = Programacion::factory()->for(Equipo::factory())->create();

        $this->actingAs($this->tecnico())
            ->put(route('programaciones.update', $programacion), $this->payload([
                'frecuencia' => FrecuenciaMantenimiento::Semestral->value,
                'fecha_ultimo_mantenimiento' => '2026-01-01',
            ]))
            ->assertRedirect(route('equipos.show', $programacion->equipo));

        $this->assertDatabaseHas('programaciones_mantenimiento', [
            'id' => $programacion->id,
            'frecuencia' => FrecuenciaMantenimiento::Semestral->value,
            'proxima_fecha' => '2026-06-30', // 2026-01-01 + 180 días
        ]);
    }

    public function test_realizar_registers_compliance_and_recalculates(): void
    {
        $programacion = Programacion::factory()->for(Equipo::factory())->create([
            'frecuencia' => FrecuenciaMantenimiento::Mensual,
            'fecha_ultimo_mantenimiento' => today()->subDays(40)->toDateString(),
        ]);

        $this->actingAs($this->tecnico())
            ->patch(route('programaciones.realizar', $programacion))
            ->assertRedirect();

        $programacion->refresh();
        $this->assertSame(today()->toDateString(), $programacion->fecha_ultimo_mantenimiento->toDateString());
        $this->assertSame(today()->addDays(30)->toDateString(), $programacion->proxima_fecha->toDateString());
        $this->assertSame(EstadoProgramacion::Realizado, $programacion->estado);
    }

    public function test_cancelar_and_reactivar(): void
    {
        $programacion = Programacion::factory()->for(Equipo::factory())->create();
        $tecnico = $this->tecnico();

        $this->actingAs($tecnico)->patch(route('programaciones.cancelar', $programacion))->assertRedirect();
        $this->assertSame(EstadoProgramacion::Cancelado, $programacion->refresh()->estado);

        $this->actingAs($tecnico)->patch(route('programaciones.reactivar', $programacion))->assertRedirect();
        $this->assertSame(EstadoProgramacion::Programado, $programacion->refresh()->estado);
    }

    public function test_tecnico_can_delete_a_programacion(): void
    {
        $programacion = Programacion::factory()->for(Equipo::factory())->create();

        $this->actingAs($this->tecnico())
            ->delete(route('programaciones.destroy', $programacion))
            ->assertRedirect(route('programaciones.index'));

        $this->assertModelMissing($programacion);
    }

    public function test_index_can_be_filtered_by_vencidas(): void
    {
        $vencida = Programacion::factory()->for(Equipo::factory())->venceEn(-5)->create();
        $proxima = Programacion::factory()->for(Equipo::factory())->venceEn(5)->create();

        $this->actingAs($this->tecnico())
            ->get(route('programaciones.index', ['estado' => 'vencidas']))
            ->assertOk()
            ->assertSee($vencida->equipo->codigo_interno)
            ->assertDontSee($proxima->equipo->codigo_interno);
    }
}
