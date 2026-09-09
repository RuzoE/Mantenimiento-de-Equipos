<?php

namespace Tests\Feature\Reportes;

use App\Enums\EstadoEquipo;
use App\Enums\RolUsuario;
use App\Models\Equipo;
use App\Models\User;
use App\Services\Reportes\ReporteEquipos;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReporteEquiposTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function conAcceso(): User
    {
        return User::factory()->create()->assignRole(RolUsuario::Consulta->value);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('reportes.equipos'))->assertRedirect(route('login'));
    }

    public function test_a_user_without_the_reportes_permission_is_forbidden(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('reportes.equipos'))
            ->assertForbidden();
    }

    public function test_a_user_with_the_permission_sees_the_report(): void
    {
        Equipo::factory()->create(['codigo_interno' => 'PC-REP-1']);

        $this->actingAs($this->conAcceso())
            ->get(route('reportes.equipos'))
            ->assertOk()
            ->assertViewIs('reportes.equipos')
            ->assertSee('PC-REP-1');
    }

    public function test_the_service_aggregates_by_state(): void
    {
        Equipo::factory()->count(2)->create(['estado' => EstadoEquipo::Operativo]);
        Equipo::factory()->create(['estado' => EstadoEquipo::Danado]);

        $reporte = app(ReporteEquipos::class)->generar([]);

        $this->assertSame(3, $reporte['total']);
        $porEstado = collect($reporte['agrupaciones']['Por estado'])->pluck('total', 'label');
        $this->assertSame(2, $porEstado['Operativo']);
        $this->assertSame(1, $porEstado['Dañado']);
    }

    public function test_the_report_can_be_filtered_by_state(): void
    {
        Equipo::factory()->create(['codigo_interno' => 'OK-1', 'estado' => EstadoEquipo::Operativo]);
        Equipo::factory()->create(['codigo_interno' => 'BAD-1', 'estado' => EstadoEquipo::Danado]);

        $this->actingAs($this->conAcceso())
            ->get(route('reportes.equipos', ['estado' => EstadoEquipo::Danado->value]))
            ->assertOk()
            ->assertSee('BAD-1')
            ->assertDontSee('OK-1');
    }

    public function test_it_exports_to_pdf(): void
    {
        Equipo::factory()->create();

        $response = $this->actingAs($this->conAcceso())
            ->get(route('reportes.equipos.export', ['formato' => 'pdf']));

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('content-type'));
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_it_exports_to_csv_compatible_with_excel(): void
    {
        $equipo = Equipo::factory()->create(['codigo_interno' => 'CSV-1']);

        $response = $this->actingAs($this->conAcceso())
            ->get(route('reportes.equipos.export', ['formato' => 'csv']));

        $response->assertOk()->assertDownload();
        $contenido = $response->streamedContent();
        $this->assertStringContainsString('Código interno', $contenido);
        $this->assertStringContainsString('CSV-1', $contenido);
        $this->assertStringStartsWith("\xEF\xBB\xBF", $contenido); // BOM UTF-8
    }
}
