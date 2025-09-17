<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

// Rotas de usuários
Route::apiResource('users', UserController::class);
