<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordChangeController;
use App\Http\Controllers\CandidatoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EleccionController;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\PuestoController;
use App\Http\Controllers\ResultadoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VotanteEleccionController;
use App\Http\Controllers\VotoController;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.store');
});

Route::middleware(['auth', 'usuario.activo'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/cambiar-password', [PasswordChangeController::class, 'edit'])
        ->name('password.change');

    Route::put('/cambiar-password', [PasswordChangeController::class, 'update'])
        ->name('password.update');
});

Route::middleware(['auth', 'usuario.activo', 'password.force'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/super-admin/dashboard', [DashboardController::class, 'superAdmin'])
        ->middleware('role:super_admin')
        ->name('super-admin.dashboard');

    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
        ->middleware('role:admin,super_admin')
        ->name('admin.dashboard');

    Route::get('/votante/dashboard', [DashboardController::class, 'votante'])
        ->middleware('role:votante')
        ->name('votante.dashboard');

    Route::middleware('role:super_admin,admin')->group(function () {
        Route::get('/usuarios/exportar-pdf', [UsuarioController::class, 'exportPdf'])
            ->name('usuarios.export-pdf');
        Route::resource('usuarios', UsuarioController::class);
        Route::post('/usuarios/{usuario}/reset-password', [UsuarioController::class, 'resetPassword'])
            ->name('usuarios.reset-password');

        Route::resource('puestos', PuestoController::class)->except(['show']);

        Route::get('/elecciones/exportar-pdf', [EleccionController::class, 'exportPdf'])
            ->name('elecciones.export-pdf');
        Route::resource('elecciones', EleccionController::class)
            ->parameters(['elecciones' => 'eleccion']);
        Route::post('/elecciones/{eleccion}/abrir', [EleccionController::class, 'abrir'])
            ->name('elecciones.abrir');
        Route::post('/elecciones/{eleccion}/cerrar', [EleccionController::class, 'cerrar'])
            ->name('elecciones.cerrar');
        Route::post('/elecciones/{eleccion}/publicar', [EleccionController::class, 'publicar'])
            ->name('elecciones.publicar');

        Route::get('/elecciones/{eleccion}/candidatos/create', [CandidatoController::class, 'create'])
            ->name('candidatos.create');
        Route::post('/elecciones/{eleccion}/candidatos', [CandidatoController::class, 'store'])
            ->name('candidatos.store');
        Route::delete('/candidatos/{candidato}', [CandidatoController::class, 'destroy'])
            ->name('candidatos.destroy');

        Route::get('/elecciones/{eleccion}/votantes', [VotanteEleccionController::class, 'index'])
            ->name('votantes.index');
        Route::post('/elecciones/{eleccion}/votantes', [VotanteEleccionController::class, 'store'])
            ->name('votantes.store');
        Route::post('/votantes/{votanteEleccion}/habilitar', [VotanteEleccionController::class, 'habilitar'])
            ->name('votantes.habilitar');
        Route::post('/votantes/{votanteEleccion}/inhabilitar', [VotanteEleccionController::class, 'inhabilitar'])
            ->name('votantes.inhabilitar');

        Route::get('/monitor/{eleccion}', [MonitorController::class, 'show'])
            ->name('monitor.show');
        Route::get('/monitor/{eleccion}/data', [MonitorController::class, 'data'])
            ->name('monitor.data');
    });

    Route::middleware('role:votante')->group(function () {
        Route::get('/votar/{eleccion}', [VotoController::class, 'create'])
            ->name('votos.create');
        Route::post('/votar/{eleccion}', [VotoController::class, 'store'])
            ->name('votos.store');
    });

    Route::get('/resultados/{eleccion}', [ResultadoController::class, 'show'])
        ->name('resultados.show');
    Route::get('/resultados/{eleccion}/exportar-pdf', [ResultadoController::class, 'exportPdf'])
        ->name('resultados.export-pdf');
});
