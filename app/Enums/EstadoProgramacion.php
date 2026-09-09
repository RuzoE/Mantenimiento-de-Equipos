<?php

namespace App\Enums;

/**
 * Estados de una programación de mantenimiento (prompt, sección 15).
 *
 * Los estados "manuales" se guardan en la columna `estado`:
 *   - Programado: recién creada, sin cumplirse todavía.
 *   - Realizado: se registró al menos un cumplimiento; a la espera del próximo ciclo.
 *   - Cancelado: dada de baja.
 *
 * Los estados "derivados" se calculan a partir de la próxima fecha y NO se guardan:
 *   - Vencido: la próxima fecha ya pasó.
 *   - Pendiente: la próxima fecha es hoy.
 *   - Próximo: la próxima fecha está dentro de la ventana de aviso (15 días).
 */
enum EstadoProgramacion: string
{
    case Programado = 'programado';
    case Proximo = 'proximo';
    case Pendiente = 'pendiente';
    case Vencido = 'vencido';
    case Realizado = 'realizado';
    case Cancelado = 'cancelado';

    /** Días de antelación con los que una programación se considera "Próxima". */
    public const VENTANA_AVISO_DIAS = 15;

    public function label(): string
    {
        return match ($this) {
            self::Programado => 'Programado',
            self::Proximo => 'Próximo',
            self::Pendiente => 'Pendiente',
            self::Vencido => 'Vencido',
            self::Realizado => 'Realizado',
            self::Cancelado => 'Cancelado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Programado => 'blue',
            self::Proximo => 'yellow',
            self::Pendiente => 'yellow',
            self::Vencido => 'red',
            self::Realizado => 'green',
            self::Cancelado => 'gray',
        };
    }

    /**
     * ¿Es un estado que fija el usuario (no derivado de fechas)?
     */
    public function esManual(): bool
    {
        return in_array($this, [self::Programado, self::Realizado, self::Cancelado], true);
    }
}
