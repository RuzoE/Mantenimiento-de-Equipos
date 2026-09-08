<?php

namespace Tests\Unit;

use App\Enums\EstadoEquipo;
use PHPUnit\Framework\TestCase;

class EstadoEquipoTest extends TestCase
{
    public function test_every_case_has_a_label_and_a_badge_color(): void
    {
        $coloresValidos = ['gray', 'green', 'yellow', 'red', 'blue', 'brand', 'indigo'];

        foreach (EstadoEquipo::cases() as $estado) {
            $this->assertNotSame('', $estado->label());
            $this->assertContains($estado->color(), $coloresValidos);
        }
    }

    public function test_novedad_grouping(): void
    {
        $this->assertFalse(EstadoEquipo::Operativo->esNovedad());
        $this->assertFalse(EstadoEquipo::Regular->esNovedad());
        $this->assertTrue(EstadoEquipo::EnMantenimiento->esNovedad());
        $this->assertTrue(EstadoEquipo::Danado->esNovedad());
        $this->assertTrue(EstadoEquipo::DadoDeBaja->esNovedad());
    }

    public function test_opciones_returns_value_label_map(): void
    {
        $opciones = EstadoEquipo::opciones();

        $this->assertSame('Operativo', $opciones['operativo']);
        $this->assertSame('En mantenimiento', $opciones['en_mantenimiento']);
        $this->assertCount(7, $opciones);
    }
}
