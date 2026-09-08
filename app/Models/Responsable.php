<?php

namespace App\Models;

use Database\Factories\ResponsableFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Responsable extends Model
{
    /** @use HasFactory<ResponsableFactory> */
    use HasFactory;

    protected $fillable = ['nombre', 'cargo', 'correo', 'telefono', 'activo'];

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
