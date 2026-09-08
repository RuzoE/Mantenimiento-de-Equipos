# Sistema Web para la Gestión y Control del Mantenimiento de Equipos Tecnológicos

**Institución Educativa Policarpa Salavarrieta**

Sistema web para administrar los equipos tecnológicos de la institución: características,
ubicación, responsables, estado, hoja de vida digital, historial y programación de
mantenimientos, traslados, alertas, reportes y auditoría.

## Stack tecnológico

| Componente | Versión |
|---|---|
| Laravel | 13.x |
| PHP | 8.4 |
| Base de datos | MySQL 8 |
| Frontend | Blade + Tailwind CSS 4 + Vite |
| Roles y permisos | Spatie Laravel Permission *(pendiente FASE 2)* |
| Autenticación | Laravel Breeze (Blade) *(pendiente FASE 1)* |

## Requisitos

- PHP >= 8.3
- Composer 2
- Node.js + npm
- MySQL 8 (Laragon)

## Instalación

```bash
git clone https://github.com/RuzoE/Mantenimiento-de-Equipos.git
cd Mantenimiento-de-Equipos

composer install
cp .env.example .env
php artisan key:generate

# Configurar credenciales de base de datos en .env
php artisan migrate

npm install
npm run build   # o: npm run dev
```

## Desarrollo

```bash
php artisan serve
npm run dev
```

## Estado del proyecto

El desarrollo es incremental, por fases. Cada fase queda funcional y probada antes de
comenzar la siguiente.

- [x] **FASE 0** — Análisis técnico inicial
- [ ] **FASE 1** — Arquitectura base (layout, sidebar, navbar, componentes UI, autenticación)
- [ ] **FASE 2** — Usuarios, roles y permisos
- [ ] **FASE 3** — Catálogos (tipos de equipo, marcas, ubicaciones, responsables, estados)
- [ ] **FASE 4** — Gestión de equipos
- [ ] **FASE 5** — Hoja de vida
- [ ] **FASE 6** — Mantenimientos
- [ ] **FASE 7** — Programación de mantenimientos
- [ ] **FASE 8** — Ubicaciones y traslados
- [ ] **FASE 9** — Dashboard avanzado
- [ ] **FASE 10** — Reportes (PDF / Excel)
- [ ] **FASE 11** — Auditoría y notificaciones
- [ ] **FASE 12** — Pruebas y preparación para producción
