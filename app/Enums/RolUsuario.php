<?php

namespace App\Enums;

/**
 * Roles del sistema. Fuente única de verdad para los nombres de rol
 * que se registran en spatie/laravel-permission.
 */
enum RolUsuario: string
{
    case Administrador = 'Administrador';
    case Tecnico = 'Tecnico';
    case Consulta = 'Consulta';

    /**
     * Nombre legible para mostrar en la interfaz.
     */
    public function label(): string
    {
        return match ($this) {
            self::Administrador => 'Administrador',
            self::Tecnico => 'Técnico',
            self::Consulta => 'Consulta',
        };
    }

    /**
     * Descripción corta del alcance del rol.
     */
    public function descripcion(): string
    {
        return match ($this) {
            self::Administrador => 'Acceso completo al sistema.',
            self::Tecnico => 'Consulta de equipos y gestión de mantenimientos.',
            self::Consulta => 'Solo consulta de la información autorizada.',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $r) => $r->value, self::cases());
    }
}
