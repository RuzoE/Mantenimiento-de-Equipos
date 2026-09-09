<?php

namespace App\Models;

use App\Enums\EstadoEquipo;
use App\Enums\TipoMantenimiento;
use App\Models\Concerns\Auditable;
use Database\Factories\MantenimientoFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mantenimiento extends Model
{
    /** @use HasFactory<MantenimientoFactory> */
    use Auditable, HasFactory;

    public function auditModulo(): string
    {
        return 'Mantenimientos';
    }

    public function auditEtiqueta(): string
    {
        return "un mantenimiento {$this->tipo->label()} en {$this->equipo->codigo_interno}";
    }

    protected $table = 'mantenimientos';

    protected $fillable = [
        'equipo_id',
        'tipo',
        'fecha',
        'responsable_id',
        'registrado_por_id',
        'estado_antes',
        'estado_despues',
        'descripcion',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'tipo' => TipoMantenimiento::class,
            'fecha' => 'date',
            'estado_antes' => EstadoEquipo::class,
            'estado_despues' => EstadoEquipo::class,
        ];
    }

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(Responsable::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por_id');
    }

    public function actividades(): HasMany
    {
        return $this->hasMany(MantenimientoActividad::class)->orderBy('orden');
    }

    public function evidencias(): HasMany
    {
        return $this->hasMany(MantenimientoEvidencia::class)->latest('id');
    }

    /**
     * Más recientes primero.
     */
    public function scopeRecientes(Builder $query): void
    {
        $query->orderByDesc('fecha')->orderByDesc('id');
    }
}
