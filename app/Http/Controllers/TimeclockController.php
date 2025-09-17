<?php

namespace App\Http\Controllers;

use App\Models\PontoRegistro;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TimeclockController extends Controller
{
    public function registrar(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        $user = Auth::user();

        if ($user->role !== 'employer') {
            return response()->json([
                'success' => false,
                'message' => 'Apenas funcionários podem registrar ponto'
            ], 403);
        }

        $registro = PontoRegistro::create([
            'user_id' => $user->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ponto registrado com sucesso',
            'data' => [
                'id' => $registro->id,
                'horario' => $registro->created_at->format('H:i:s'),
                'data' => $registro->created_at->format('d/m/Y')
            ]
        ]);
    }

    public function registrosHoje(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        $user = Auth::user();

        if ($user->role !== 'employer') {
            return response()->json([
                'success' => false,
                'message' => 'Apenas funcionários podem visualizar registros de ponto'
            ], 403);
        }

        $hoje = Carbon::today();
        $amanha = Carbon::tomorrow();

        $registros = PontoRegistro::where('user_id', $user->id)
            ->whereBetween('created_at', [$hoje, $amanha])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'registros' => $registros->map(function ($registro) {
                return [
                    'id' => $registro->id,
                    'horario' => $registro->created_at->toISOString(),
                    'hora_formatada' => $registro->created_at->format('H:i')
                ];
            })
        ]);
    }
}
