<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TimeclockController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ReportController;

/**
 * @OA\Info(
 *     title="API Ticto",
 *     version="1.0.0",
 *     description="API para gerenciamento de usuários",
 *     @OA\Contact(
 *         email="contato@ticto.com"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost",
 *     description="Servidor de desenvolvimento"
 * )
 */

// Rotas de autenticação
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Rotas de ponto (apenas para funcionários autenticados)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/timeclock', [TimeclockController::class, 'registrar']);
    Route::get('/timeclock/today', [TimeclockController::class, 'registrosHoje']);
    Route::post('/change-password', [ChangePasswordController::class, 'trocar']);

    // Rotas de administrador
    Route::apiResource('employees', EmployeeController::class);
    Route::get('/reports/timeclock', [ReportController::class, 'ponto']);
    Route::get('/time-records', [ReportController::class, 'pontosRegistrados']);
});

// Rotas de usuários
Route::apiResource('users', UserController::class);
