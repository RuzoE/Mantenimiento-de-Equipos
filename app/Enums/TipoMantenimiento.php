<?php

namespace App\Enums;

/**
 * Tipo de mantenimiento (prompt, sección 14).
 */
enum TipoMantenimiento: string
{
    case Preventivo = 'preventivo';
    case Correctivo = 'correctivo';

    public function label(): string
    {
        return match ($this) {
            self::Preventivo => 'Preventivo',
            self::Correctivo => 'Correctivo',
        };
    }

    /**
     * Color del badge en la interfaz (coincide con x-ui.badge).
     */
    public function color(): string
    {
        return match ($this) {
            self::Preventivo => 'blue',
            self::Correctivo => 'yellow',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $t) => $t->value, self::cases());
    }
}
