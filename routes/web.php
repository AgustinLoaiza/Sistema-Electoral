<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordChangeController;

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

    Route::middleware('password.force')->group(function () {
        Route::get('/dashboard', function () {
            $usuario = Auth::user();

            return match ($usuario->rol) {
                'super_admin' => redirect()->route('super-admin.dashboard'),
                'admin' => redirect()->route('admin.dashboard'),
                default => redirect()->route('votante.dashboard'),
            };
        })->name('dashboard');
    });
});

Route::middleware(['auth', 'usuario.activo', 'password.force'])->group(function () {
    Route::get('/super-admin/dashboard', fn () => 'Dashboard Super Admin')
        ->middleware('role:super_admin')
        ->name('super-admin.dashboard');

    Route::get('/admin/dashboard', fn () => 'Dashboard Admin')
        ->middleware('role:admin,super_admin')
        ->name('admin.dashboard');

    Route::get('/votante/dashboard', fn () => 'Dashboard Votante')
        ->middleware('role:votante')
        ->name('votante.dashboard');
});
