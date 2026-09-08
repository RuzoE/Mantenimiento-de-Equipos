<?php

namespace App\Models;

use Database\Factories\TipoEquipoFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoEquipo extends Model
{
    /** @use HasFactory<TipoEquipoFactory> */
    use HasFactory;

    protected $table = 'tipos_equipos';

    protected $fillable = ['nombre', 'descripcion', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function scopeActivos(Builder $query): void
    {
        $query->where('activo', true);
    }

    public function equipos(): HasMany
    {
        return $this->hasMany(Equipo::class);
    }
}
