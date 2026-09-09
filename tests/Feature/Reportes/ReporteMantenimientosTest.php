<?php

namespace Tests\Feature\Reportes;

use App\Enums\RolUsuario;
use App\Enums\TipoMantenimiento;
use App\Models\Equipo;
use App\Models\Mantenimiento;
use App\Models\User;
use App\Services\Reportes\ReporteMantenimientos;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReporteMantenimientosTest extends TestCase
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

    public function test_a_user_without_the_permission_is_forbidden(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('reportes.mantenimientos'))
            ->assertForbidden();
    }

    public function test_the_service_aggregates_by_type(): void
    {
        Mantenimiento::factory()->count(2)->preventivo()->create();
        Mantenimiento::factory()->correctivo()->create();

        $reporte = app(ReporteMantenimientos::class)->generar([]);

        $this->assertSame(3, $reporte['total']);
        $porTipo = collect($reporte['agrupaciones']['Por tipo'])->pluck('total', 'label');
        $this->assertSame(2, $porTipo['Preventivo']);
        $this->assertSame(1, $porTipo['Correctivo']);
        $this->assertArrayHasKey('proximas', $reporte['programacion']);
    }

    public function test_the_report_can_be_filtered_by_date_range(): void
    {
        $equipoDentro = Equipo::factory()->create(['codigo_interno' => 'IN-1']);
        $equipoFuera = Equipo::factory()->create(['codigo_interno' => 'OUT-1']);
        Mantenimiento::factory()->for($equipoDentro)->create(['fecha' => '2026-05-15']);
        Mantenimiento::factory()->for($equipoFuera)->create(['fecha' => '2026-01-10']);

        $this->actingAs($this->conAcceso())
            ->get(route('reportes.mantenimientos', ['desde' => '2026-05-01', 'hasta' => '2026-05-31']))
            ->assertOk()
            ->assertSee('IN-1')
            ->assertDontSee('OUT-1');
    }

    public function test_the_report_can_be_filtered_by_type(): void
    {
        $prev = Mantenimiento::factory()->preventivo()->create();
        $corr = Mantenimiento::factory()->correctivo()->create();

        $this->actingAs($this->conAcceso())
            ->get(route('reportes.mantenimientos', ['tipo' => TipoMantenimiento::Correctivo->value]))
            ->assertOk()
            ->assertSee($corr->equipo->codigo_interno)
            ->assertDontSee($prev->equipo->codigo_interno);
    }

    public function test_it_exports_to_pdf_and_csv(): void
    {
        Mantenimiento::factory()->create();
        $user = $this->conAcceso();

        $pdf = $this->actingAs($user)->get(route('reportes.mantenimientos.export', ['formato' => 'pdf']));
        $pdf->assertOk();
        $this->assertSame('application/pdf', $pdf->headers->get('content-type'));

        $csv = $this->actingAs($user)->get(route('reportes.mantenimientos.export', ['formato' => 'csv']));
        $csv->assertOk()->assertDownload();
        $this->assertStringContainsString('Descripción', $csv->streamedContent());
    }
}
