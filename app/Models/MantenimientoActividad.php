<?php

namespace App\Models;

use Database\Factories\MantenimientoActividadFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MantenimientoActividad extends Model
{
    /** @use HasFactory<MantenimientoActividadFactory> */
    use HasFactory;

    protected $table = 'mantenimiento_actividades';

    protected $fillable = ['mantenimiento_id', 'descripcion', 'orden'];

    public function mantenimiento(): BelongsTo
    {
        return $this->belongsTo(Mantenimiento::class);
    }
}
