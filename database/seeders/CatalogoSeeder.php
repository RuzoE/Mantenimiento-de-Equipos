<?php

namespace Database\Seeders;

use App\Models\Marca;
use App\Models\Responsable;
use App\Models\TipoEquipo;
use App\Models\Ubicacion;
use Illuminate\Database\Seeder;

class CatalogoSeeder extends Seeder
{
    /**
     * Datos iniciales de los catálogos.
     */
    public function run(): void
    {
        $tipos = [
            ['nombre' => 'Computador de escritorio', 'descripcion' => 'Equipo de cómputo fijo.'],
            ['nombre' => 'Portátil', 'descripcion' => 'Equipo de cómputo portátil.'],
            ['nombre' => 'Impresora', 'descripcion' => 'Impresora o multifuncional.'],
            ['nombre' => 'Monitor', 'descripcion' => 'Pantalla de computador.'],
            ['nombre' => 'Proyector', 'descripcion' => 'Videobeam / proyector.'],
            ['nombre' => 'Televisor', 'descripcion' => 'Televisor o pantalla grande.'],
            ['nombre' => 'Tablet', 'descripcion' => 'Tableta.'],
            ['nombre' => 'Equipo de red', 'descripcion' => 'Switch, router, access point, etc.'],
            ['nombre' => 'Otro', 'descripcion' => 'Otro equipo tecnológico.'],
        ];
        foreach ($tipos as $tipo) {
            TipoEquipo::updateOrCreate(['nombre' => $tipo['nombre']], $tipo);
        }

        $marcas = ['Lenovo', 'HP', 'Dell', 'Acer', 'Asus', 'Epson', 'Canon', 'Samsung', 'LG', 'Genérica'];
        foreach ($marcas as $marca) {
            Marca::updateOrCreate(['nombre' => $marca]);
        }

        $ubicaciones = [
            'Sala de informática 1',
            'Sala de informática 2',
            'Primaria',
            'Administración',
            'Secretaría',
            'Coordinación',
            'Biblioteca',
            'Rectoría',
            'Bodega',
        ];
        foreach ($ubicaciones as $ubicacion) {
            Ubicacion::updateOrCreate(['nombre' => $ubicacion]);
        }

        Responsable::updateOrCreate(
            ['nombre' => 'Área de Sistemas'],
            ['cargo' => 'Soporte técnico', 'correo' => 'sistemas@policarpa.edu.co'],
        );
    }
}
