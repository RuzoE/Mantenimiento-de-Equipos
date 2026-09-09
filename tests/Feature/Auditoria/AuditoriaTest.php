<?php

namespace Tests\Feature\Auditoria;

use App\Enums\EstadoEquipo;
use App\Enums\EventoAuditoria;
use App\Enums\RolUsuario;
use App\Models\Auditoria;
use App\Models\Equipo;
use App\Models\Marca;
use App\Models\TipoEquipo;
use App\Models\Ubicacion;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditoriaTest extends TestCase
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

    /**
     * @return Collection<int, Auditoria>
     */
    private function auditoriasDe(Equipo $equipo)
    {
        return Auditoria::where('auditable_type', $equipo->getMorphClass())
            ->where('auditable_id', $equipo->id)
            ->get();
    }

    public function test_the_module_is_admin_only(): void
    {
        $this->actingAs(User::factory()->create()->assignRole(RolUsuario::Consulta->value))
            ->get(route('auditoria.index'))
            ->assertForbidden();

        $this->actingAs($this->admin())
            ->get(route('auditoria.index'))
            ->assertOk()
            ->assertViewIs('auditoria.index');
    }

    public function test_creating_a_model_writes_an_audit_entry_with_the_actor(): void
    {
        $admin = $this->admin();
        $ubicacion = Ubicacion::factory()->create();

        $this->actingAs($admin)->post(route('equipos.store'), [
            'codigo_interno' => 'AUD-1',
            'tipo_equipo_id' => TipoEquipo::factory()->create()->id,
            'marca_id' => Marca::factory()->create()->id,
            'ubicacion_id' => $ubicacion->id,
            'estado' => EstadoEquipo::Operativo->value,
        ]);

        $equipo = Equipo::where('codigo_interno', 'AUD-1')->firstOrFail();
        $auditoria = $this->auditoriasDe($equipo)->firstWhere('evento', EventoAuditoria::Creado);

        $this->assertNotNull($auditoria);
        $this->assertSame('Equipos', $auditoria->modulo);
        $this->assertSame($admin->id, $auditoria->user_id);
        $this->assertStringContainsString('creó el equipo AUD-1', $auditoria->descripcion);
    }

    public function test_an_update_records_the_field_diff(): void
    {
        $equipo = Equipo::factory()->create(['modelo' => 'Viejo']);
        $equipo->update(['modelo' => 'Nuevo']);

        $auditoria = $this->auditoriasDe($equipo)->firstWhere('evento', EventoAuditoria::Actualizado);

        $this->assertNotNull($auditoria);
        $this->assertSame(['antes' => 'Viejo', 'despues' => 'Nuevo'], $auditoria->cambios['modelo']);
    }

    public function test_deactivating_and_reactivating_use_dedicated_events(): void
    {
        $equipo = Equipo::factory()->create(['activo' => true]);

        $equipo->update(['activo' => false]);
        $this->assertNotNull($this->auditoriasDe($equipo)->firstWhere('evento', EventoAuditoria::Desactivado));

        $equipo->update(['activo' => true]);
        $this->assertNotNull($this->auditoriasDe($equipo)->firstWhere('evento', EventoAuditoria::Reactivado));
    }

    public function test_a_model_change_without_an_authenticated_user_is_attributed_to_the_system(): void
    {
        $equipo = Equipo::factory()->create();

        $auditoria = $this->auditoriasDe($equipo)->first();

        $this->assertNull($auditoria->user_id);
        $this->assertSame('Sistema', $auditoria->actor());
    }

    public function test_the_index_can_be_filtered_by_module(): void
    {
        $equipo = Equipo::factory()->create(); // módulo Equipos
        User::factory()->create();              // módulo Usuarios

        $response = $this->actingAs($this->admin())
            ->get(route('auditoria.index', ['modulo' => 'Equipos']))
            ->assertOk();

        $listado = $response->viewData('auditorias');
        $this->assertTrue($listado->every(fn (Auditoria $a) => $a->modulo === 'Equipos'));
        $this->assertGreaterThan(0, $listado->total());
    }
}
