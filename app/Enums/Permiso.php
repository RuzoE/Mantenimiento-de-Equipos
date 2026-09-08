<?php

namespace App\Enums;

/**
 * Catálogo de permisos del sistema. Fuente única de verdad: el seeder los
 * registra en spatie/laravel-permission y la aplicación los referencia
 * siempre a través de este enum (policies, controladores, vistas).
 */
enum Permiso: string
{
    // Usuarios
    case UsuariosVer = 'usuarios.ver';
    case UsuariosCrear = 'usuarios.crear';
    case UsuariosEditar = 'usuarios.editar';
    case UsuariosEliminar = 'usuarios.eliminar';

    // Catálogos (tipos de equipo, marcas, ubicaciones, responsables)
    case CatalogosVer = 'catalogos.ver';
    case CatalogosGestionar = 'catalogos.gestionar';

    // Equipos
    case EquiposVer = 'equipos.ver';
    case EquiposCrear = 'equipos.crear';
    case EquiposEditar = 'equipos.editar';
    case EquiposEliminar = 'equipos.eliminar';

    // Mantenimientos
    case MantenimientosVer = 'mantenimientos.ver';
    case MantenimientosCrear = 'mantenimientos.crear';
    case MantenimientosEditar = 'mantenimientos.editar';
    case MantenimientosEliminar = 'mantenimientos.eliminar';

    // Programación de mantenimientos
    case ProgramacionesVer = 'programaciones.ver';
    case ProgramacionesGestionar = 'programaciones.gestionar';

    // Traslados
    case TrasladosVer = 'traslados.ver';
    case TrasladosCrear = 'traslados.crear';

    // Reportes
    case ReportesVer = 'reportes.ver';

    // Auditoría
    case AuditoriaVer = 'auditoria.ver';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $p) => $p->value, self::cases());
    }
}
