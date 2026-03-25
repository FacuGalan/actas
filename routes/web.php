<?php

use App\Livewire\ControlOperativo;
use App\Livewire\CrearActa;
use Illuminate\Support\Facades\Route;
use App\Livewire\DashboardActas;
use App\Livewire\EditarActa;
use App\Livewire\ListarActas;

Route::get('/', function () {
    return redirect('/login');
});

// Rutas del sistema de actas (requieren autenticación con guard inspector)
Route::middleware(['auth:inspector'])->group(function () {
    
    // Dashboard principal de actas
    Route::get('/actas', DashboardActas::class)->name('actas.dashboard');

    // Crear acta simple (sin operativo)
    Route::get('/actas/crear-simple', CrearActa::class)->name('actas.crear-simple');
    
    // Control de operativo (vista intermedia)
    Route::get('/actas/operativo/{operativo_id}', ControlOperativo::class)->name('actas.crear-operativo');

    // Crear acta en operativo (desde el control)
    Route::get('/actas/operativo/{operativo_id}/acta', CrearActa::class)->name('actas.crear-operativo-acta');

    // Listar mis actas
    Route::get('/actas/listar', ListarActas::class)->name('actas.listar');
    
    // Editar acta
    Route::get('/actas/{acta}/editar', EditarActa::class)->name('actas.editar');
    
});

// Incluir las rutas de autenticación de Breeze
require __DIR__.'/auth.php';