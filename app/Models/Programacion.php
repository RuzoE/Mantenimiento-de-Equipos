<?php

namespace App\Models;

use App\Enums\EstadoProgramacion;
use App\Enums\FrecuenciaMantenimiento;
use App\Enums\TipoMantenimiento;
use App\Models\Concerns\Auditable;
use Carbon\CarbonInterface;
use Database\Factories\ProgramacionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Programacion extends Model
{
    /** @use HasFactory<ProgramacionFactory> */
    use Auditable, HasFactory;

    public function auditModulo(): string
    {
        return 'Programación';
    }

    public function auditEtiqueta(): string
    {
        return "la programación de {$this->equipo->codigo_interno}";
    }

    protected $table = 'programaciones_mantenimiento';

    protected $fillable = [
        'equipo_id',
        'tipo',
        'frecuencia',
        'fecha_ultimo_mantenimiento',
        'proxima_fecha',
        'estado',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'tipo' => TipoMantenimiento::class,
            'frecuencia' => FrecuenciaMantenimiento::class,
            'fecha_ultimo_mantenimiento' => 'date',
            'proxima_fecha' => 'date',
            'estado' => EstadoProgramacion::class,
        ];
    }

    protected static function booted(): void
    {
        // La próxima fecha siempre se deriva de la última + la frecuencia.
        static::saving(function (Programacion $programacion) {
            $programacion->proxima_fecha = $programacion->calcularProximaFecha();
        });
    }

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }

    /**
     * Próxima fecha calculada: última fecha (o hoy si no hay) + días de la frecuencia.
     */
    public function calcularProximaFecha(): CarbonInterface
    {
        $base = $this->fecha_ultimo_mantenimiento ?? today();

        return $base->copy()->addDays($this->frecuencia->dias());
    }

    /**
     * Estado efectivo: manual si está cancelada, derivado de la próxima fecha en otro caso.
     */
    public function estadoActual(): EstadoProgramacion
    {
        if ($this->estado === EstadoProgramacion::Cancelado) {
            return EstadoProgramacion::Cancelado;
        }

        $proxima = $this->proxima_fecha->startOfDay();
        $hoy = today();

        return match (true) {
            $proxima->lt($hoy) => EstadoProgramacion::Vencido,
            $proxima->isSameDay($hoy) => EstadoProgramacion::Pendiente,
            $proxima->lte($hoy->copy()->addDays(EstadoProgramacion::VENTANA_AVISO_DIAS)) => EstadoProgramacion::Proximo,
            default => $this->estado,
        };
    }

    /**
     * Días que faltan para la próxima fecha (negativo si ya venció).
     */
    public function diasParaProxima(): int
    {
        return (int) today()->diffInDays($this->proxima_fecha->startOfDay(), false);
    }

    /**
     * Registra que el mantenimiento programado se cumplió.
     */
    public function registrarCumplimiento(?CarbonInterface $fecha = null): void
    {
        $this->fecha_ultimo_mantenimiento = $fecha ?? today();
        $this->estado = EstadoProgramacion::Realizado;
        $this->save();
    }

    public function scopeVigentes(Builder $query): void
    {
        $query->where('estado', '!=', EstadoProgramacion::Cancelado->value);
    }

    public function scopeVencidas(Builder $query): void
    {
        $query->vigentes()->whereDate('proxima_fecha', '<', today());
    }

    public function scopeProximas(Builder $query): void
    {
        $query->vigentes()
            ->whereDate('proxima_fecha', '>=', today())
            ->whereDate('proxima_fecha', '<=', today()->copy()->addDays(EstadoProgramacion::VENTANA_AVISO_DIAS));
    }
}
