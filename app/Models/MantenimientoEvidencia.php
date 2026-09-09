<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Number;

class MantenimientoEvidencia extends Model
{
    protected $table = 'mantenimiento_evidencias';

    protected $fillable = ['mantenimiento_id', 'nombre_original', 'ruta', 'mime', 'tamano'];

    public function mantenimiento(): BelongsTo
    {
        return $this->belongsTo(Mantenimiento::class);
    }

    public function esImagen(): bool
    {
        return str_starts_with($this->mime, 'image/');
    }

    public function tamanoLegible(): string
    {
        return Number::fileSize($this->tamano);
    }
}
