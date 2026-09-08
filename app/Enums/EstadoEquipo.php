<?php

namespace App\Enums;

/**
 * Estados controlados de un equipo. Conjunto fijo: el usuario nunca escribe
 * el estado libremente (prompt, sección 16).
 */
enum EstadoEquipo: string
{
    case Operativo = 'operativo';
    case Regular = 'regular';
    case EnMantenimiento = 'en_mantenimiento';
    case EnReparacion = 'en_reparacion';
    case Danado = 'danado';
    case FueraDeServicio = 'fuera_de_servicio';
    case DadoDeBaja = 'dado_de_baja';

    public function label(): string
    {
        return match ($this) {
            self::Operativo => 'Operativo',
            self::Regular => 'Regular',
            self::EnMantenimiento => 'En mantenimiento',
            self::EnReparacion => 'En reparación',
            self::Danado => 'Dañado',
            self::FueraDeServicio => 'Fuera de servicio',
            self::DadoDeBaja => 'Dado de baja',
        };
    }

    /**
     * Color del badge en la interfaz (coincide con los colores de x-ui.badge).
     */
    public function color(): string
    {
        return match ($this) {
            self::Operativo => 'green',
            self::Regular => 'yellow',
            self::EnMantenimiento => 'blue',
            self::EnReparacion => 'indigo',
            self::Danado => 'red',
            self::FueraDeServicio => 'red',
            self::DadoDeBaja => 'gray',
        };
    }

    /**
     * ¿El equipo se considera con novedad (no plenamente operativo)?
     */
    public function esNovedad(): bool
    {
        return ! in_array($this, [self::Operativo, self::Regular], true);
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $e) => $e->value, self::cases());
    }

    /**
     * @return array<string, string> [valor => etiqueta]
     */
    public static function opciones(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $e) => [$e->value => $e->label()])
            ->all();
    }
}
