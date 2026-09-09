<?php

namespace Tests\Feature\Mantenimientos;

use App\Enums\RolUsuario;
use App\Models\Mantenimiento;
use App\Models\MantenimientoEvidencia;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EvidenciaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        Storage::fake('local');
    }

    private function evidencia(): MantenimientoEvidencia
    {
        $mantenimiento = Mantenimiento::factory()->create();
        Storage::disk('local')->put("evidencias/{$mantenimiento->id}/archivo.pdf", 'contenido');

        return MantenimientoEvidencia::create([
            'mantenimiento_id' => $mantenimiento->id,
            'nombre_original' => 'informe.pdf',
            'ruta' => "evidencias/{$mantenimiento->id}/archivo.pdf",
            'mime' => 'application/pdf',
            'tamano' => 9,
        ]);
    }

    public function test_a_user_with_view_permission_can_download_an_evidence(): void
    {
        $user = User::factory()->create()->assignRole(RolUsuario::Consulta->value);

        $this->actingAs($user)
            ->get(route('evidencias.show', $this->evidencia()))
            ->assertOk()
            ->assertHeader('content-disposition', 'attachment; filename=informe.pdf');
    }

    public function test_a_user_without_maintenance_access_cannot_download(): void
    {
        $user = User::factory()->create(); // sin roles ni permisos

        $this->actingAs($user)
            ->get(route('evidencias.show', $this->evidencia()))
            ->assertForbidden();
    }

    public function test_missing_file_returns_404(): void
    {
        $user = User::factory()->create()->assignRole(RolUsuario::Consulta->value);
        $evidencia = $this->evidencia();
        Storage::disk('local')->delete($evidencia->ruta);

        $this->actingAs($user)
            ->get(route('evidencias.show', $evidencia))
            ->assertNotFound();
    }

    public function test_a_technician_can_delete_an_evidence(): void
    {
        $user = User::factory()->create()->assignRole(RolUsuario::Tecnico->value);
        $evidencia = $this->evidencia();

        $this->actingAs($user)
            ->delete(route('evidencias.destroy', $evidencia))
            ->assertRedirect();

        $this->assertModelMissing($evidencia);
        Storage::disk('local')->assertMissing($evidencia->ruta);
    }

    public function test_a_consulta_user_cannot_delete_an_evidence(): void
    {
        $user = User::factory()->create()->assignRole(RolUsuario::Consulta->value);

        $this->actingAs($user)
            ->delete(route('evidencias.destroy', $this->evidencia()))
            ->assertForbidden();
    }
}
