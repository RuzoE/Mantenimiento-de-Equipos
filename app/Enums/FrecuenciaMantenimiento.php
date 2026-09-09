<?php

namespace App\Enums;

/**
 * Frecuencia con la que se repite un mantenimiento programado.
 */
enum FrecuenciaMantenimiento: string
{
    case Mensual = 'mensual';
    case Bimestral = 'bimestral';
    case Trimestral = 'trimestral';
    case Semestral = 'semestral';
    case Anual = 'anual';

    public function label(): string
    {
        return match ($this) {
            self::Mensual => 'Mensual',
            self::Bimestral => 'Bimestral',
            self::Trimestral => 'Trimestral',
            self::Semestral => 'Semestral',
            self::Anual => 'Anual',
        };
    }

    /**
     * Días que se suman a la última fecha para obtener la próxima.
     */
    public function dias(): int
    {
        return match ($this) {
            self::Mensual => 30,
            self::Bimestral => 60,
            self::Trimestral => 90,
            self::Semestral => 180,
            self::Anual => 365,
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $f) => $f->value, self::cases());
    }
}
