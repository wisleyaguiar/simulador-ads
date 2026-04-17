<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ParameterController;
use App\Http\Controllers\SimulationController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Middleware\IsAdmin;

Route::post('/register', RegisterController::class);
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    
    // Core parameters
    Route::get('/parameters', [ParameterController::class, 'index']);
    
    // Simulations
    Route::get('/simulations', [SimulationController::class, 'index']);
    Route::post('/simulations', [SimulationController::class, 'store']);
    Route::delete('/simulations/{id}', [SimulationController::class, 'destroy']);
    
    // Admin features
    Route::middleware([IsAdmin::class])->prefix('admin')->group(function () {
        Route::post('/import/segments', [ImportController::class, 'importSegments'])->middleware('throttle:import');
        Route::post('/import/regions', [ImportController::class, 'importRegions'])->middleware('throttle:import');
        Route::get('/data-health', [ImportController::class, 'dataHealth']);
    });
});


Route::middleware('auth:sanctum')->get('/user', function (\Illuminate\Http\Request $request) {
    return $request->user();
});

