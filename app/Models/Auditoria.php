<?php

namespace App\Models;

use App\Enums\EventoAuditoria;
use Database\Factories\AuditoriaFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Auditoria extends Model
{
    /** @use HasFactory<AuditoriaFactory> */
    use HasFactory;

    protected $table = 'auditorias';

    protected $fillable = [
        'user_id',
        'evento',
        'auditable_type',
        'auditable_id',
        'modulo',
        'descripcion',
        'cambios',
        'ip',
    ];

    protected function casts(): array
    {
        return [
            'evento' => EventoAuditoria::class,
            'cambios' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeRecientes(Builder $query): void
    {
        $query->orderByDesc('id');
    }

    public function actor(): string
    {
        return $this->user?->name ?? 'Sistema';
    }
}
