<?php

namespace Tests\Feature\Dashboard;

use App\Enums\EstadoEquipo;
use App\Enums\RolUsuario;
use App\Models\Equipo;
use App\Models\Mantenimiento;
use App\Models\Programacion;
use App\Models\User;
use App\Services\Dashboard\ResumenDashboard;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function usuario(): User
    {
        return User::factory()->create()->assignRole(RolUsuario::Consulta->value);
    }

    private function resumen(): array
    {
        return app(ResumenDashboard::class)->obtener();
    }

    public function test_dashboard_loads_for_an_authenticated_user(): void
    {
        $this->actingAs($this->usuario())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewIs('dashboard.index')
            ->assertSee('Panel principal');
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_the_cards_count_equipos_by_state_group_over_active_ones(): void
    {
        Equipo::factory()->count(3)->create(['estado' => EstadoEquipo::Operativo]);
        Equipo::factory()->create(['estado' => EstadoEquipo::Regular]);
        Equipo::factory()->create(['estado' => EstadoEquipo::EnMantenimiento]);
        Equipo::factory()->create(['estado' => EstadoEquipo::FueraDeServicio]);
        Equipo::factory()->create(['estado' => EstadoEquipo::Operativo, 'activo' => false]);

        $t = $this->resumen()['tarjetas'];

        $this->assertSame(6, $t['total']);
        $this->assertSame(3, $t['operativos']);
        $this->assertSame(1, $t['con_novedades']);
        $this->assertSame(1, $t['en_mantenimiento']);
        $this->assertSame(1, $t['fuera_de_servicio']);
    }

    public function test_it_counts_overdue_and_upcoming_programaciones(): void
    {
        Programacion::factory()->for(Equipo::factory())->venceEn(-3)->create();
        Programacion::factory()->for(Equipo::factory())->venceEn(-10)->create();
        Programacion::factory()->for(Equipo::factory())->venceEn(5)->create();
        Programacion::factory()->for(Equipo::factory())->venceEn(90)->create();

        $t = $this->resumen()['tarjetas'];

        $this->assertSame(2, $t['mantenimientos_vencidos']);
        $this->assertSame(1, $t['mantenimientos_proximos']);
    }

    public function test_por_atender_lists_overdue_and_upcoming_only(): void
    {
        $vencida = Programacion::factory()->for(Equipo::factory())->venceEn(-2)->create();
        $lejana = Programacion::factory()->for(Equipo::factory())->venceEn(120)->create();

        $porAtender = $this->resumen()['porAtender'];

        $this->assertTrue($porAtender->contains('id', $vencida->id));
        $this->assertFalse($porAtender->contains('id', $lejana->id));
    }

    public function test_equipos_con_novedades_excludes_operational_ones(): void
    {
        $ok = Equipo::factory()->create(['estado' => EstadoEquipo::Operativo]);
        $danado = Equipo::factory()->create(['estado' => EstadoEquipo::Danado]);

        $novedades = $this->resumen()['conNovedades'];

        $this->assertTrue($novedades->contains('id', $danado->id));
        $this->assertFalse($novedades->contains('id', $ok->id));
    }

    public function test_recent_activity_comes_from_the_audit_log(): void
    {
        Mantenimiento::factory()->create();

        $actividad = $this->resumen()['actividadReciente'];

        $this->assertNotEmpty($actividad);
        $this->assertLessThanOrEqual(8, $actividad->count());
        $this->assertTrue($actividad->contains(fn ($evento) => str_contains($evento->texto, 'mantenimiento')));
    }

    public function test_por_estado_percentages_add_up(): void
    {
        Equipo::factory()->count(2)->create(['estado' => EstadoEquipo::Operativo]);
        Equipo::factory()->count(2)->create(['estado' => EstadoEquipo::Danado]);

        $porEstado = collect($this->resumen()['porEstado']);

        $this->assertCount(2, $porEstado);
        $this->assertSame(100, $porEstado->sum('pct'));
    }
}
