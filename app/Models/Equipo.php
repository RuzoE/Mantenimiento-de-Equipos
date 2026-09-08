<?php

namespace App\Models;

use App\Enums\EstadoEquipo;
use Database\Factories\EquipoFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Equipo extends Model
{
    /** @use HasFactory<EquipoFactory> */
    use HasFactory;

    protected $fillable = [
        'codigo_interno',
        'tipo_equipo_id',
        'marca_id',
        'modelo',
        'numero_serie',
        'ubicacion_id',
        'responsable_id',
        'estado',
        'fecha_adquisicion',
        'fecha_garantia',
        'procesador',
        'memoria_ram',
        'almacenamiento',
        'sistema_operativo',
        'observaciones',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'estado' => EstadoEquipo::class,
            'fecha_adquisicion' => 'date',
            'fecha_garantia' => 'date',
            'activo' => 'boolean',
        ];
    }

    public function tipoEquipo(): BelongsTo
    {
        return $this->belongsTo(TipoEquipo::class);
    }

    public function marca(): BelongsTo
    {
        return $this->belongsTo(Marca::class);
    }

    public function ubicacion(): BelongsTo
    {
        return $this->belongsTo(Ubicacion::class);
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(Responsable::class);
    }

    public function scopeActivos(Builder $query): void
    {
        $query->where('activo', true);
    }

    /**
     * Búsqueda por código interno, número de serie o modelo.
     */
    public function scopeBuscar(Builder $query, ?string $termino): void
    {
        $termino = trim((string) $termino);

        $query->when($termino !== '', function (Builder $q) use ($termino) {
            $q->where(function (Builder $sub) use ($termino) {
                $sub->where('codigo_interno', 'like', "%{$termino}%")
                    ->orWhere('numero_serie', 'like', "%{$termino}%")
                    ->orWhere('modelo', 'like', "%{$termino}%");
            });
        });
    }

    /**
     * Nombre corto para listados: marca + modelo.
     */
    public function getDescripcionAttribute(): string
    {
        return trim(($this->marca?->nombre ?? '').' '.($this->modelo ?? '')) ?: '—';
    }
}
