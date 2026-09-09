<?php

namespace App\Enums;

/**
 * Tipo de evento registrado en la auditoría.
 */
enum EventoAuditoria: string
{
    case Creado = 'creado';
    case Actualizado = 'actualizado';
    case Eliminado = 'eliminado';
    case Desactivado = 'desactivado';
    case Reactivado = 'reactivado';

    public function label(): string
    {
        return match ($this) {
            self::Creado => 'Creación',
            self::Actualizado => 'Actualización',
            self::Eliminado => 'Eliminación',
            self::Desactivado => 'Desactivación',
            self::Reactivado => 'Reactivación',
        };
    }

    /**
     * Verbo en pasado para redactar la descripción.
     */
    public function verbo(): string
    {
        return match ($this) {
            self::Creado => 'creó',
            self::Actualizado => 'actualizó',
            self::Eliminado => 'eliminó',
            self::Desactivado => 'desactivó',
            self::Reactivado => 'reactivó',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Creado => 'green',
            self::Actualizado => 'blue',
            self::Eliminado => 'red',
            self::Desactivado => 'gray',
            self::Reactivado => 'green',
        };
    }
}
