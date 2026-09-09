<?php

namespace App\Enums;

/**
 * Motivo de un traslado de equipo (prompt, sección 18).
 */
enum MotivoTraslado: string
{
    case Reorganizacion = 'reorganizacion';
    case Prestamo = 'prestamo';
    case Reparacion = 'reparacion';
    case Devolucion = 'devolucion';
    case BajaTemporal = 'baja_temporal';
    case Otro = 'otro';

    public function label(): string
    {
        return match ($this) {
            self::Reorganizacion => 'Reorganización',
            self::Prestamo => 'Préstamo',
            self::Reparacion => 'Reparación',
            self::Devolucion => 'Devolución',
            self::BajaTemporal => 'Baja temporal',
            self::Otro => 'Otro',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $m) => $m->value, self::cases());
    }
}
