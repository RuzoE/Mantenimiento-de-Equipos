# Sistema Web para la Gestión y Control del Mantenimiento de Equipos Tecnológicos

**Institución Educativa Policarpa Salavarrieta**

Sistema web para administrar los equipos tecnológicos de la institución: características,
ubicación, responsables, estado, hoja de vida digital, historial y programación de
mantenimientos, traslados, alertas, reportes y auditoría.

## Stack tecnológico

| Componente | Versión / paquete |
|---|---|
| Laravel | 13.x |
| PHP | 8.4 |
| Base de datos | MySQL 8 |
| Frontend | Blade + Tailwind CSS 4 + Vite + Alpine.js |
| Autenticación | Laravel Breeze (Blade) — registro público deshabilitado |
| Roles y permisos | `spatie/laravel-permission` |
| PDF | `barryvdh/laravel-dompdf` |
| Excel | Exportación CSV (BOM UTF-8) compatible con Excel |

## Requisitos

- PHP >= 8.3 (probado en 8.4)
- Composer 2
- Node.js + npm
- MySQL 8

## Instalación (desarrollo)

```bash
git clone https://github.com/RuzoE/Mantenimiento-de-Equipos.git
cd Mantenimiento-de-Equipos

composer install
cp .env.example .env
php artisan key:generate

# Ajustar credenciales de base de datos en .env
php artisan migrate --seed

npm install
npm run build      # o: npm run dev
```

```bash
php artisan serve
npm run dev
```

Usuario administrador de ejemplo (creado por el seeder):

- **Correo:** `admin@policarpa.edu.co`
- **Contraseña:** `password`

## Roles

| Rol | Alcance |
|---|---|
| **Administrador** | Acceso completo (incluye usuarios y auditoría). |
| **Técnico** | Consulta de equipos y gestión de mantenimientos, programación y traslados. |
| **Consulta** | Solo lectura de la información autorizada. |

## Pruebas

```bash
php artisan test
```

Las pruebas usan una base de datos MySQL dedicada, `mantenimiento_equipos_test`
(configurada en `phpunit.xml`). Créala una vez:

```sql
CREATE DATABASE mantenimiento_equipos_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## Despliegue en producción

1. **Variables de entorno** (`.env`):
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_URL=https://…` (dominio real, con HTTPS)
   - `SESSION_SECURE_COOKIE=true`
   - `LOG_LEVEL=warning`
   - Credenciales reales de base de datos y correo (`MAIL_MAILER=smtp`).
   - Nunca versionar `.env` (ya está en `.gitignore`).
2. **Dependencias y assets**:
   ```bash
   composer install --no-dev --optimize-autoloader
   npm ci && npm run build
   ```
3. **Migraciones**:
   ```bash
   php artisan migrate --force
   php artisan db:seed --class=RolePermissionSeeder --force   # solo la primera vez
   ```
4. **Optimización** (repetir en cada despliegue):
   ```bash
   php artisan optimize          # config + routes + views + events
   ```
   (o `config:cache`, `route:cache`, `view:cache`, `event:cache` por separado).
5. **Permisos de carpetas**: `storage/` y `bootstrap/cache/` escribibles por el
   servidor web. Las evidencias de mantenimiento se guardan en
   `storage/app/private/evidencias/` (disco privado, no accesible por URL).
6. **Colas**: `QUEUE_CONNECTION=database`; ejecutar un worker
   (`php artisan queue:work`) supervisado.
7. **Programador**: añadir al cron `* * * * * php artisan schedule:run` (para
   futuras tareas programadas de notificaciones).
8. **Copias de seguridad** de la base de datos y de `storage/app/private/`.

## Estado del proyecto

Desarrollo incremental por fases. **Todas las fases completadas.**

- [x] **FASE 0** — Análisis técnico inicial
- [x] **FASE 1** — Arquitectura base (layout, sidebar, navbar, componentes UI, autenticación)
- [x] **FASE 2** — Usuarios, roles y permisos
- [x] **FASE 3** — Catálogos (tipos de equipo, marcas, ubicaciones, responsables, estados)
- [x] **FASE 4** — Gestión de equipos
- [x] **FASE 5** — Hoja de vida
- [x] **FASE 6** — Mantenimientos (con evidencias en disco privado)
- [x] **FASE 7** — Programación de mantenimientos (cálculo automático de la próxima fecha)
- [x] **FASE 8** — Ubicaciones y traslados
- [x] **FASE 9** — Dashboard con datos reales
- [x] **FASE 10** — Reportes (PDF y CSV/Excel)
- [x] **FASE 11** — Auditoría y notificaciones
- [x] **FASE 12** — Pruebas y preparación para producción

## Seguridad

- Validación de todas las entradas mediante Form Requests / reglas explícitas.
- CSRF en todos los formularios; escape automático de Blade.
- Autorización por permiso en rutas, controladores y policies (nunca solo ocultando botones).
- Asignación masiva controlada (`$fillable` explícito en todos los modelos).
- Archivos de evidencia: validación de tipo y tamaño (máx. 5 MB), nombres aleatorios,
  almacenamiento privado y descarga por ruta autorizada.
- Contraseñas con hash; límite de intentos de inicio de sesión.
- Auditoría de acciones importantes (quién, qué, cuándo, cambios).
