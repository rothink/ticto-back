<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PontoController;
use App\Http\Controllers\TrocarSenhaController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\RelatorioController;

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
    Route::post('/ponto', [PontoController::class, 'registrar']);
    Route::get('/ponto/hoje', [PontoController::class, 'registrosHoje']);
    Route::post('/trocar-senha', [TrocarSenhaController::class, 'trocar']);

    // Rotas de administrador
    Route::apiResource('funcionarios', FuncionarioController::class);
    Route::get('/relatorios/ponto', [RelatorioController::class, 'ponto']);
    Route::get('/pontos-registrados', [RelatorioController::class, 'pontosRegistrados']);
});

// Rotas de usuários
Route::apiResource('users', UserController::class);
