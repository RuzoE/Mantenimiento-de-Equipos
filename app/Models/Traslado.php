<?php

namespace App\Models;

use App\Enums\MotivoTraslado;
use Database\Factories\TrasladoFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Traslado extends Model
{
    /** @use HasFactory<TrasladoFactory> */
    use HasFactory;

    protected $fillable = [
        'equipo_id',
        'ubicacion_origen_id',
        'ubicacion_destino_id',
        'fecha',
        'motivo',
        'observaciones',
        'registrado_por_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'motivo' => MotivoTraslado::class,
        ];
    }

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }

    public function ubicacionOrigen(): BelongsTo
    {
        return $this->belongsTo(Ubicacion::class, 'ubicacion_origen_id');
    }

    public function ubicacionDestino(): BelongsTo
    {
        return $this->belongsTo(Ubicacion::class, 'ubicacion_destino_id');
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por_id');
    }

    public function scopeRecientes(Builder $query): void
    {
        $query->orderByDesc('fecha')->orderByDesc('id');
    }

    /**
     * ¿Es el traslado más reciente de su equipo? (el único que puede deshacerse)
     */
    public function esUltimo(): bool
    {
        return ! static::query()
            ->where('equipo_id', $this->equipo_id)
            ->where('id', '>', $this->id)
            ->exists();
    }
}
