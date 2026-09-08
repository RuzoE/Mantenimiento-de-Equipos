<?php

namespace Tests\Feature\Catalogos;

use App\Enums\RolUsuario;
use App\Models\Marca;
use App\Models\Responsable;
use App\Models\TipoEquipo;
use App\Models\Ubicacion;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CatalogoCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    /**
     * @return array<string, array{class-string, string, string, array<string,mixed>}>
     */
    public static function catalogos(): array
    {
        return [
            'tipos de equipo' => [TipoEquipo::class, 'catalogos.tipos-equipo', 'tipos_equipos', ['nombre' => 'Escáner', 'descripcion' => 'Escáner de documentos']],
            'marcas' => [Marca::class, 'catalogos.marcas', 'marcas', ['nombre' => 'Toshiba']],
            'ubicaciones' => [Ubicacion::class, 'catalogos.ubicaciones', 'ubicaciones', ['nombre' => 'Aula Máxima', 'descripcion' => 'Auditorio']],
            'responsables' => [Responsable::class, 'catalogos.responsables', 'responsables', ['nombre' => 'Juan Pérez', 'cargo' => 'Docente', 'correo' => 'juan@policarpa.edu.co', 'telefono' => '3001112233']],
        ];
    }

    private function admin(): User
    {
        return User::factory()->create()->assignRole(RolUsuario::Administrador->value);
    }

    private function consulta(): User
    {
        return User::factory()->create()->assignRole(RolUsuario::Consulta->value);
    }

    #[DataProvider('catalogos')]
    public function test_guest_is_redirected_to_login(string $modelClass, string $ruta): void
    {
        $this->get(route("{$ruta}.index"))->assertRedirect(route('login'));
    }

    #[DataProvider('catalogos')]
    public function test_consulta_can_view_but_not_manage(string $modelClass, string $ruta, string $tabla, array $payload): void
    {
        $this->actingAs($this->consulta());

        $this->get(route("{$ruta}.index"))->assertOk();
        $this->get(route("{$ruta}.create"))->assertForbidden();
        $this->post(route("{$ruta}.store"), $payload)->assertForbidden();
    }

    #[DataProvider('catalogos')]
    public function test_admin_can_create(string $modelClass, string $ruta, string $tabla, array $payload): void
    {
        $this->actingAs($this->admin());

        $this->post(route("{$ruta}.store"), $payload)->assertRedirectToRoute("{$ruta}.index");
        $this->assertDatabaseHas($tabla, ['nombre' => $payload['nombre'], 'activo' => true]);
    }

    #[DataProvider('catalogos')]
    public function test_nombre_is_required(string $modelClass, string $ruta): void
    {
        $this->actingAs($this->admin());

        $this->post(route("{$ruta}.store"), [])->assertSessionHasErrors('nombre');
    }

    #[DataProvider('catalogos')]
    public function test_nombre_must_be_unique(string $modelClass, string $ruta, string $tabla, array $payload): void
    {
        $this->actingAs($this->admin());
        $modelClass::factory()->create(['nombre' => $payload['nombre']]);

        $this->post(route("{$ruta}.store"), $payload)->assertSessionHasErrors('nombre');
    }

    #[DataProvider('catalogos')]
    public function test_admin_can_update(string $modelClass, string $ruta, string $tabla, array $payload): void
    {
        $this->actingAs($this->admin());
        $item = $modelClass::factory()->create();

        $this->put(route("{$ruta}.update", $item->id), ['nombre' => 'Nombre Editado'] + $payload)
            ->assertRedirectToRoute("{$ruta}.index");

        $this->assertDatabaseHas($tabla, ['id' => $item->id, 'nombre' => 'Nombre Editado']);
    }

    #[DataProvider('catalogos')]
    public function test_admin_can_deactivate_and_reactivate(string $modelClass, string $ruta, string $tabla): void
    {
        $this->actingAs($this->admin());
        $item = $modelClass::factory()->create();

        $this->delete(route("{$ruta}.destroy", $item->id))->assertRedirectToRoute("{$ruta}.index");
        $this->assertDatabaseHas($tabla, ['id' => $item->id, 'activo' => false]);

        $this->patch(route("{$ruta}.reactivar", $item->id))->assertRedirectToRoute("{$ruta}.index");
        $this->assertDatabaseHas($tabla, ['id' => $item->id, 'activo' => true]);
    }
}
