<?php

namespace App\Providers;

use App\Models\Equipo;
use App\Models\Mantenimiento;
use App\Models\Marca;
use App\Models\Programacion;
use App\Models\Responsable;
use App\Models\TipoEquipo;
use App\Models\Traslado;
use App\Models\Ubicacion;
use App\Models\User;
use App\Observers\AuditObserver;
use App\Policies\CatalogoPolicy;
use App\Services\Alertas\CentroDeAlertas;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // El acceso total del rol Administrador se concede asignándole el
        // conjunto completo de permisos en RolePermissionSeeder, no con un
        // Gate::before global: así las policies (p. ej. no desactivarse a sí
        // mismo, no eliminar al último Administrador) también se aplican a él.

        // Todos los catálogos comparten la misma policy.
        foreach ([TipoEquipo::class, Marca::class, Ubicacion::class, Responsable::class] as $modelo) {
            Gate::policy($modelo, CatalogoPolicy::class);
        }

        // Auditoría automática de los modelos que usan el trait Auditable.
        foreach ([Equipo::class, Mantenimiento::class, Traslado::class, Programacion::class, User::class] as $modelo) {
            $modelo::observe(AuditObserver::class);
        }

        // Contadores de alertas para la campana del navbar.
        View::composer('layouts.partials.navbar', function ($view) {
            $view->with('alertas', auth()->check()
                ? app(CentroDeAlertas::class)->contadores()
                : ['total' => 0, 'vencidas' => 0, 'proximas' => 0, 'con_novedad' => 0]);
        });
    }
}
