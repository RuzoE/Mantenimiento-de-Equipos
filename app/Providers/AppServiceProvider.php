<?php

namespace App\Providers;

use App\Models\Marca;
use App\Models\Responsable;
use App\Models\TipoEquipo;
use App\Models\Ubicacion;
use App\Policies\CatalogoPolicy;
use Illuminate\Support\Facades\Gate;
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
    }
}
