<?php

use App\Http\Controllers\Catalogos\MarcaController;
use App\Http\Controllers\Catalogos\ResponsableController;
use App\Http\Controllers\Catalogos\TipoEquipoController;
use App\Http\Controllers\Catalogos\UbicacionController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Usuarios\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Gestión de usuarios
    Route::resource('usuarios', UsuarioController::class)
        ->parameters(['usuarios' => 'usuario'])
        ->except(['show']);
    Route::patch('usuarios/{usuario}/reactivar', [UsuarioController::class, 'reactivar'])
        ->name('usuarios.reactivar');

    // Catálogos
    Route::prefix('catalogos')->name('catalogos.')->group(function () {
        $catalogos = [
            'tipos-equipo' => TipoEquipoController::class,
            'marcas' => MarcaController::class,
            'ubicaciones' => UbicacionController::class,
            'responsables' => ResponsableController::class,
        ];

        foreach ($catalogos as $slug => $controller) {
            Route::resource($slug, $controller)->parameters([$slug => 'id'])->except(['show']);
            Route::patch("{$slug}/{id}/reactivar", [$controller, 'reactivar'])->name("{$slug}.reactivar");
        }
    });
});

require __DIR__.'/auth.php';
