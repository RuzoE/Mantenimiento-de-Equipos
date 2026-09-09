<?php

namespace App\Models\Concerns;

use App\Models\Auditoria;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Marca un modelo como auditable. El observer AuditObserver se registra en
 * AppServiceProvider y aprovecha auditModulo() / auditEtiqueta() para redactar
 * cada entrada de auditoría con el diff de campos modificados.
 */
trait Auditable
{
    public function auditorias(): MorphMany
    {
        return $this->morphMany(Auditoria::class, 'auditable');
    }

    /**
     * Nombre del módulo al que pertenece el registro (p. ej. "Equipos").
     */
    abstract public function auditModulo(): string;

    /**
     * Cómo se nombra el registro en la descripción (p. ej. "el equipo PC-001").
     */
    abstract public function auditEtiqueta(): string;

    /**
     * Campos que no deben incluirse en el diff de cambios.
     *
     * @return array<int, string>
     */
    public function auditExcept(): array
    {
        return [];
    }
}
