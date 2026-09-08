<?php

namespace App\Models;

use Database\Factories\UbicacionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ubicacion extends Model
{
    /** @use HasFactory<UbicacionFactory> */
    use HasFactory;

    protected $table = 'ubicaciones';

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
