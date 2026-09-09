<?php

namespace Tests\Feature\Mantenimientos;

use App\Enums\EstadoEquipo;
use App\Enums\RolUsuario;
use App\Enums\TipoMantenimiento;
use App\Models\Equipo;
use App\Models\Mantenimiento;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MantenimientoCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        Storage::fake('local');
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
            'fecha' => now()->subDay()->format('Y-m-d'),
            'estado_antes' => EstadoEquipo::Regular->value,
            'estado_despues' => EstadoEquipo::Operativo->value,
            'descripcion' => 'Mantenimiento preventivo general.',
            'observaciones' => 'Sin novedades.',
            'actividades' => ['Limpieza interna', '', 'Cambio de pasta térmica'],
        ], $overrides);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('mantenimientos.index'))->assertRedirect(route('login'));
    }

    public function test_consulta_can_view_but_not_manage(): void
    {
        $mantenimiento = Mantenimiento::factory()->create();
        $this->actingAs($this->consulta());

        $this->get(route('mantenimientos.index'))->assertOk();
        $this->get(route('mantenimientos.show', $mantenimiento))->assertOk();
        $this->get(route('equipos.mantenimientos.create', $mantenimiento->equipo))->assertForbidden();
        $this->post(route('equipos.mantenimientos.store', $mantenimiento->equipo), $this->payload())->assertForbidden();
        $this->get(route('mantenimientos.edit', $mantenimiento))->assertForbidden();
        $this->delete(route('mantenimientos.destroy', $mantenimiento))->assertForbidden();
    }

    public function test_tecnico_registers_a_mantenimiento_with_activities_evidence_and_state_change(): void
    {
        $equipo = Equipo::factory()->create(['estado' => EstadoEquipo::Danado]);
        $tecnico = $this->tecnico();

        $response = $this->actingAs($tecnico)->post(
            route('equipos.mantenimientos.store', $equipo),
            $this->payload([
                'evidencias' => [
                    UploadedFile::fake()->image('antes.jpg'),
                    UploadedFile::fake()->create('informe.pdf', 200, 'application/pdf'),
                ],
            ]),
        );

        $mantenimiento = Mantenimiento::firstOrFail();
        $response->assertRedirect(route('mantenimientos.show', $mantenimiento));

        $this->assertSame($equipo->id, $mantenimiento->equipo_id);
        $this->assertSame($tecnico->id, $mantenimiento->registrado_por_id);
        $this->assertCount(2, $mantenimiento->actividades);          // la línea vacía se descarta
        $this->assertCount(2, $mantenimiento->evidencias);
        $this->assertSame(EstadoEquipo::Operativo, $equipo->refresh()->estado);

        foreach ($mantenimiento->evidencias as $evidencia) {
            Storage::disk('local')->assertExists($evidencia->ruta);
            $this->assertStringStartsWith("evidencias/{$mantenimiento->id}/", $evidencia->ruta);
        }
    }

    public function test_descripcion_is_required(): void
    {
        $equipo = Equipo::factory()->create();

        $this->actingAs($this->tecnico())
            ->post(route('equipos.mantenimientos.store', $equipo), $this->payload(['descripcion' => '']))
            ->assertSessionHasErrors('descripcion');
    }

    public function test_fecha_cannot_be_in_the_future(): void
    {
        $equipo = Equipo::factory()->create();

        $this->actingAs($this->tecnico())
            ->post(route('equipos.mantenimientos.store', $equipo), $this->payload(['fecha' => now()->addWeek()->format('Y-m-d')]))
            ->assertSessionHasErrors('fecha');
    }

    public function test_evidence_with_a_disallowed_type_is_rejected(): void
    {
        $equipo = Equipo::factory()->create();

        $this->actingAs($this->tecnico())
            ->post(route('equipos.mantenimientos.store', $equipo), $this->payload([
                'evidencias' => [UploadedFile::fake()->create('malicioso.exe', 10, 'application/octet-stream')],
            ]))
            ->assertSessionHasErrors('evidencias.0');

        $this->assertDatabaseCount('mantenimientos', 0);
    }

    public function test_oversized_evidence_is_rejected(): void
    {
        $equipo = Equipo::factory()->create();

        $this->actingAs($this->tecnico())
            ->post(route('equipos.mantenimientos.store', $equipo), $this->payload([
                'evidencias' => [UploadedFile::fake()->create('grande.pdf', 6000, 'application/pdf')],
            ]))
            ->assertSessionHasErrors('evidencias.0');
    }

    public function test_tecnico_can_update_a_mantenimiento(): void
    {
        $mantenimiento = Mantenimiento::factory()->create(['descripcion' => 'Original']);

        $this->actingAs($this->tecnico())
            ->put(route('mantenimientos.update', $mantenimiento), $this->payload(['descripcion' => 'Editado']))
            ->assertRedirect(route('mantenimientos.show', $mantenimiento));

        $this->assertSame('Editado', $mantenimiento->refresh()->descripcion);
    }

    public function test_tecnico_can_delete_a_mantenimiento_and_its_files(): void
    {
        $equipo = Equipo::factory()->create();
        $this->actingAs($this->tecnico())->post(route('equipos.mantenimientos.store', $equipo), $this->payload([
            'evidencias' => [UploadedFile::fake()->image('foto.png')],
        ]));

        $mantenimiento = Mantenimiento::firstOrFail();
        $rutaEvidencia = $mantenimiento->evidencias->first()->ruta;

        $this->actingAs($this->tecnico())
            ->delete(route('mantenimientos.destroy', $mantenimiento))
            ->assertRedirect(route('mantenimientos.index'));

        $this->assertDatabaseCount('mantenimientos', 0);
        $this->assertDatabaseCount('mantenimiento_evidencias', 0);
        Storage::disk('local')->assertMissing($rutaEvidencia);
    }

    public function test_index_can_be_filtered_by_tipo(): void
    {
        $prev = Mantenimiento::factory()->preventivo()->create();
        $corr = Mantenimiento::factory()->correctivo()->create();

        $this->actingAs($this->tecnico())
            ->get(route('mantenimientos.index', ['tipo' => TipoMantenimiento::Correctivo->value]))
            ->assertOk()
            ->assertSee($corr->equipo->codigo_interno)
            ->assertDontSee($prev->equipo->codigo_interno);
    }
}
