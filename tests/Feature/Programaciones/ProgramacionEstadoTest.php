<?php

namespace Tests\Feature\Programaciones;

use App\Enums\EstadoProgramacion;
use App\Enums\FrecuenciaMantenimiento;
use App\Models\Equipo;
use App\Models\Programacion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgramacionEstadoTest extends TestCase
{
    use RefreshDatabase;

    private function programacion(FrecuenciaMantenimiento $frecuencia, ?string $ultimo): Programacion
    {
        return Programacion::factory()->for(Equipo::factory())->create([
            'frecuencia' => $frecuencia,
            'fecha_ultimo_mantenimiento' => $ultimo,
        ]);
    }

    public function test_proxima_fecha_is_last_date_plus_frequency_days(): void
    {
        $p = $this->programacion(FrecuenciaMantenimiento::Trimestral, '2026-01-01');

        $this->assertSame('2026-04-01', $p->proxima_fecha->toDateString()); // +90 días
    }

    public function test_proxima_fecha_counts_from_today_when_no_last_date(): void
    {
        $p = $this->programacion(FrecuenciaMantenimiento::Mensual, null);

        $this->assertSame(today()->addDays(30)->toDateString(), $p->proxima_fecha->toDateString());
    }

    public function test_estado_actual_is_vencido_when_next_date_is_past(): void
    {
        $p = $this->programacion(FrecuenciaMantenimiento::Mensual, today()->subDays(40)->toDateString());

        $this->assertSame(EstadoProgramacion::Vencido, $p->estadoActual());
        $this->assertLessThan(0, $p->diasParaProxima());
    }

    public function test_estado_actual_is_pendiente_when_next_date_is_today(): void
    {
        $p = $this->programacion(FrecuenciaMantenimiento::Mensual, today()->subDays(30)->toDateString());

        $this->assertSame(EstadoProgramacion::Pendiente, $p->estadoActual());
    }

    public function test_estado_actual_is_proximo_inside_the_warning_window(): void
    {
        $p = $this->programacion(FrecuenciaMantenimiento::Mensual, today()->subDays(20)->toDateString());

        $this->assertSame(EstadoProgramacion::Proximo, $p->estadoActual()); // faltan 10 días
    }

    public function test_estado_actual_is_programado_when_far_away(): void
    {
        $p = $this->programacion(FrecuenciaMantenimiento::Semestral, today()->toDateString());

        $this->assertSame(EstadoProgramacion::Programado, $p->estadoActual());
    }

    public function test_cancelled_schedule_keeps_its_state(): void
    {
        $p = $this->programacion(FrecuenciaMantenimiento::Mensual, today()->subDays(60)->toDateString());
        $p->update(['estado' => EstadoProgramacion::Cancelado]);

        $this->assertSame(EstadoProgramacion::Cancelado, $p->estadoActual());
    }

    public function test_registrar_cumplimiento_advances_the_dates(): void
    {
        $p = $this->programacion(FrecuenciaMantenimiento::Mensual, today()->subDays(40)->toDateString());

        $p->registrarCumplimiento();

        $this->assertSame(today()->toDateString(), $p->fecha_ultimo_mantenimiento->toDateString());
        $this->assertSame(today()->addDays(30)->toDateString(), $p->proxima_fecha->toDateString());
        $this->assertSame(EstadoProgramacion::Realizado, $p->estado);
    }
}
