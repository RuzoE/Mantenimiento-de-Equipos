<?php

namespace Database\Seeders;

use App\Enums\EstadoEquipo;
use App\Models\Equipo;
use App\Models\Marca;
use App\Models\Responsable;
use App\Models\TipoEquipo;
use App\Models\Ubicacion;
use Illuminate\Database\Seeder;

class EquipoSeeder extends Seeder
{
    /**
     * Equipos de ejemplo para poblar el dashboard y las pruebas manuales.
     */
    public function run(): void
    {
        $tipo = fn (string $n) => TipoEquipo::where('nombre', $n)->value('id');
        $marca = fn (string $n) => Marca::where('nombre', $n)->value('id');
        $ubi = fn (string $n) => Ubicacion::where('nombre', $n)->value('id');
        $responsable = Responsable::where('nombre', 'Área de Sistemas')->value('id');

        $equipos = [
            ['codigo_interno' => 'PC-001', 'tipo' => 'Computador de escritorio', 'marca' => 'Lenovo', 'modelo' => 'ThinkCentre M70', 'ubi' => 'Sala de informática 1', 'estado' => EstadoEquipo::Operativo, 'procesador' => 'Intel Core i5', 'memoria_ram' => '8 GB', 'almacenamiento' => '256 GB SSD', 'sistema_operativo' => 'Windows 11'],
            ['codigo_interno' => 'PC-002', 'tipo' => 'Computador de escritorio', 'marca' => 'HP', 'modelo' => 'ProDesk 400', 'ubi' => 'Sala de informática 1', 'estado' => EstadoEquipo::Regular, 'procesador' => 'Intel Core i3', 'memoria_ram' => '4 GB', 'almacenamiento' => '500 GB HDD', 'sistema_operativo' => 'Windows 10'],
            ['codigo_interno' => 'PC-015', 'tipo' => 'Computador de escritorio', 'marca' => 'Dell', 'modelo' => 'OptiPlex 3080', 'ubi' => 'Sala de informática 2', 'estado' => EstadoEquipo::EnMantenimiento],
            ['codigo_interno' => 'PORT-004', 'tipo' => 'Portátil', 'marca' => 'Asus', 'modelo' => 'X515', 'ubi' => 'Coordinación', 'estado' => EstadoEquipo::Operativo, 'memoria_ram' => '16 GB', 'almacenamiento' => '512 GB SSD'],
            ['codigo_interno' => 'IMP-003', 'tipo' => 'Impresora', 'marca' => 'Epson', 'modelo' => 'L3250', 'ubi' => 'Secretaría', 'estado' => EstadoEquipo::EnReparacion],
            ['codigo_interno' => 'PROY-002', 'tipo' => 'Proyector', 'marca' => 'Epson', 'modelo' => 'PowerLite E20', 'ubi' => 'Biblioteca', 'estado' => EstadoEquipo::Operativo],
            ['codigo_interno' => 'TV-001', 'tipo' => 'Televisor', 'marca' => 'Samsung', 'modelo' => 'AU7000 55"', 'ubi' => 'Primaria', 'estado' => EstadoEquipo::Danado],
            ['codigo_interno' => 'RED-001', 'tipo' => 'Equipo de red', 'marca' => 'Genérica', 'modelo' => 'Switch 24 puertos', 'ubi' => 'Bodega', 'estado' => EstadoEquipo::FueraDeServicio],
        ];

        foreach ($equipos as $datos) {
            Equipo::updateOrCreate(
                ['codigo_interno' => $datos['codigo_interno']],
                [
                    'tipo_equipo_id' => $tipo($datos['tipo']),
                    'marca_id' => $marca($datos['marca']),
                    'modelo' => $datos['modelo'] ?? null,
                    'ubicacion_id' => $ubi($datos['ubi']),
                    'responsable_id' => $responsable,
                    'estado' => $datos['estado'],
                    'fecha_adquisicion' => now()->subYears(2)->subMonths(rand(0, 11))->toDateString(),
                    'procesador' => $datos['procesador'] ?? null,
                    'memoria_ram' => $datos['memoria_ram'] ?? null,
                    'almacenamiento' => $datos['almacenamiento'] ?? null,
                    'sistema_operativo' => $datos['sistema_operativo'] ?? null,
                    'activo' => true,
                ],
            );
        }
    }
}
