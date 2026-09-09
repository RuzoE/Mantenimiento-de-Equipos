<?php

namespace Tests\Feature\Equipos;

use App\Enums\RolUsuario;
use App\Models\Equipo;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EquipoHojaDeVidaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function consulta(): User
    {
        return User::factory()->create()->assignRole(RolUsuario::Consulta->value);
    }

    public function test_the_hoja_de_vida_shows_all_its_sections(): void
    {
        $equipo = Equipo::factory()->create([
            'fecha_adquisicion' => now()->subYears(2),
        ]);

        $this->actingAs($this->consulta())
            ->get(route('equipos.show', $equipo))
            ->assertOk()
            ->assertSee('Hoja de vida')
            ->assertSee('Información general')
            ->assertSee('Especificaciones')
            ->assertSee('Historial')
            ->assertSee('Próximo mantenimiento')
            ->assertSee('Antigüedad');
    }

    public function test_warranty_badge_reflects_a_valid_warranty(): void
    {
        $equipo = Equipo::factory()->create(['fecha_garantia' => now()->addYear()]);

        $this->actingAs($this->consulta())
            ->get(route('equipos.show', $equipo))
            ->assertOk()
            ->assertSee('Vigente');

        $this->assertTrue($equipo->garantia_vigente);
    }

    public function test_warranty_badge_reflects_an_expired_warranty(): void
    {
        $equipo = Equipo::factory()->create(['fecha_garantia' => now()->subMonth()]);

        $this->actingAs($this->consulta())
            ->get(route('equipos.show', $equipo))
            ->assertOk()
            ->assertSee('Vencida');

        $this->assertFalse($equipo->garantia_vigente);
    }

    public function test_no_warranty_badge_when_the_date_is_missing(): void
    {
        $equipo = Equipo::factory()->create(['fecha_garantia' => null]);

        $this->assertNull($equipo->garantia_vigente);

        $this->actingAs($this->consulta())
            ->get(route('equipos.show', $equipo))
            ->assertOk()
            ->assertDontSee('Garantía vigente')
            ->assertDontSee('Garantía vencida');
    }

    public function test_antiguedad_accessor_is_derived_from_the_acquisition_date(): void
    {
        $conFecha = Equipo::factory()->create(['fecha_adquisicion' => now()->subYears(3)]);
        $sinFecha = Equipo::factory()->create(['fecha_adquisicion' => null]);

        $this->assertStringContainsString('3', (string) $conFecha->antiguedad);
        $this->assertNull($sinFecha->antiguedad);
    }
}
