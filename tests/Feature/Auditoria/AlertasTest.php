<?php

namespace Tests\Feature\Auditoria;

use App\Enums\EstadoEquipo;
use App\Enums\RolUsuario;
use App\Models\Equipo;
use App\Models\Programacion;
use App\Models\User;
use App\Services\Alertas\CentroDeAlertas;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlertasTest extends TestCase
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

    public function test_a_guest_is_redirected_to_login(): void
    {
        $this->get(route('alertas.index'))->assertRedirect(route('login'));
    }

    public function test_the_alerts_page_is_available_to_any_authenticated_user(): void
    {
        $this->actingAs($this->usuario())
            ->get(route('alertas.index'))
            ->assertOk()
            ->assertViewIs('alertas.index');
    }

    public function test_the_counters_reflect_overdue_upcoming_and_flagged_equipment(): void
    {
        Programacion::factory()->for(Equipo::factory())->venceEn(-3)->create();  // vencida
        Programacion::factory()->for(Equipo::factory())->venceEn(-9)->create();  // vencida
        Programacion::factory()->for(Equipo::factory())->venceEn(4)->create();   // próxima
        Equipo::factory()->create(['estado' => EstadoEquipo::Danado]);           // con novedad
        Equipo::factory()->create(['estado' => EstadoEquipo::Operativo]);        // no cuenta

        $contadores = app(CentroDeAlertas::class)->contadores();

        $this->assertSame(2, $contadores['vencidas']);
        $this->assertSame(1, $contadores['proximas']);
        $this->assertSame(1, $contadores['con_novedad']);
        $this->assertSame(4, $contadores['total']);
    }

    public function test_the_navbar_bell_shows_the_alert_count(): void
    {
        Programacion::factory()->for(Equipo::factory())->venceEn(-1)->create();

        $this->actingAs($this->usuario())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Ver todas las alertas');
    }
}
