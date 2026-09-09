<?php

namespace Tests\Feature\Traslados;

use App\Enums\RolUsuario;
use App\Models\Equipo;
use App\Models\Ubicacion;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EquiposPorUbicacionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_the_ubicaciones_catalog_shows_the_equipment_count_with_a_link(): void
    {
        $admin = User::factory()->create()->assignRole(RolUsuario::Administrador->value);
        $ubicacion = Ubicacion::factory()->create(['nombre' => 'Sala QA']);
        Equipo::factory()->count(3)->create(['ubicacion_id' => $ubicacion->id]);

        $this->actingAs($admin)
            ->get(route('catalogos.ubicaciones.index'))
            ->assertOk()
            ->assertSee('3 equipo(s)')
            ->assertSee(route('equipos.index', ['ubicacion_id' => $ubicacion->id]), escape: false);
    }

    public function test_equipos_index_can_be_filtered_by_ubicacion(): void
    {
        $admin = User::factory()->create()->assignRole(RolUsuario::Administrador->value);
        $ubicacion = Ubicacion::factory()->create();
        $dentro = Equipo::factory()->create(['codigo_interno' => 'AQUI-1', 'ubicacion_id' => $ubicacion->id]);
        $fuera = Equipo::factory()->create(['codigo_interno' => 'ALLA-1']);

        $this->actingAs($admin)
            ->get(route('equipos.index', ['ubicacion_id' => $ubicacion->id]))
            ->assertOk()
            ->assertSee('AQUI-1')
            ->assertDontSee('ALLA-1');
    }
}
