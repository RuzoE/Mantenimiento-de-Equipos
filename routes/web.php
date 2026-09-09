<?php

use App\Http\Controllers\Alertas\AlertaController;
use App\Http\Controllers\Auditoria\AuditoriaController;
use App\Http\Controllers\Catalogos\MarcaController;
use App\Http\Controllers\Catalogos\ResponsableController;
use App\Http\Controllers\Catalogos\TipoEquipoController;
use App\Http\Controllers\Catalogos\UbicacionController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Equipos\EquipoController;
use App\Http\Controllers\Mantenimientos\EvidenciaController;
use App\Http\Controllers\Mantenimientos\MantenimientoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Programaciones\ProgramacionController;
use App\Http\Controllers\Reportes\ReporteController;
use App\Http\Controllers\Traslados\TrasladoController;
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

    // Equipos
    Route::resource('equipos', EquipoController::class);
    Route::patch('equipos/{equipo}/reactivar', [EquipoController::class, 'reactivar'])
        ->name('equipos.reactivar');

    // Mantenimientos
    Route::get('equipos/{equipo}/mantenimientos/create', [MantenimientoController::class, 'create'])
        ->name('equipos.mantenimientos.create');
    Route::post('equipos/{equipo}/mantenimientos', [MantenimientoController::class, 'store'])
        ->name('equipos.mantenimientos.store');
    Route::resource('mantenimientos', MantenimientoController::class)
        ->only(['index', 'show', 'edit', 'update', 'destroy']);
    Route::get('evidencias/{evidencia}', [EvidenciaController::class, 'show'])->name('evidencias.show');
    Route::delete('evidencias/{evidencia}', [EvidenciaController::class, 'destroy'])->name('evidencias.destroy');

    // Programación de mantenimientos
    Route::get('equipos/{equipo}/programaciones/create', [ProgramacionController::class, 'create'])
        ->name('equipos.programaciones.create');
    Route::post('equipos/{equipo}/programaciones', [ProgramacionController::class, 'store'])
        ->name('equipos.programaciones.store');
    Route::resource('programaciones', ProgramacionController::class)
        ->parameters(['programaciones' => 'programacion'])
        ->only(['index', 'edit', 'update', 'destroy']);
    Route::patch('programaciones/{programacion}/realizar', [ProgramacionController::class, 'realizar'])->name('programaciones.realizar');
    Route::patch('programaciones/{programacion}/cancelar', [ProgramacionController::class, 'cancelar'])->name('programaciones.cancelar');
    Route::patch('programaciones/{programacion}/reactivar', [ProgramacionController::class, 'reactivar'])->name('programaciones.reactivar');

    // Traslados
    Route::get('equipos/{equipo}/traslados/create', [TrasladoController::class, 'create'])
        ->name('equipos.traslados.create');
    Route::post('equipos/{equipo}/traslados', [TrasladoController::class, 'store'])
        ->name('equipos.traslados.store');
    Route::get('traslados', [TrasladoController::class, 'index'])->name('traslados.index');
    Route::delete('traslados/{traslado}', [TrasladoController::class, 'destroy'])->name('traslados.destroy');

    // Alertas y auditoría
    Route::get('alertas', [AlertaController::class, 'index'])->name('alertas.index');
    Route::get('auditoria', [AuditoriaController::class, 'index'])
        ->middleware('can:auditoria.ver')->name('auditoria.index');

    // Reportes
    Route::middleware('can:reportes.ver')->prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/', [ReporteController::class, 'index'])->name('index');
        Route::get('equipos/export', [ReporteController::class, 'equiposExport'])->name('equipos.export');
        Route::get('equipos', [ReporteController::class, 'equipos'])->name('equipos');
        Route::get('mantenimientos/export', [ReporteController::class, 'mantenimientosExport'])->name('mantenimientos.export');
        Route::get('mantenimientos', [ReporteController::class, 'mantenimientos'])->name('mantenimientos');
    });

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
