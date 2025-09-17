<?php

namespace App\Http\Controllers;

use App\Models\PontoRegistro;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function ponto(Request $request): JsonResponse
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado'
            ], 403);
        }

        $query = PontoRegistro::with('user')
            ->whereHas('user', function ($q) {
                $q->where('admin_id', Auth::id());
            });

        if ($request->has('funcionario_id') && $request->funcionario_id) {
            $query->where('user_id', $request->funcionario_id);
        }

        if ($request->has('data_inicio') && $request->data_inicio) {
            $dataInicio = Carbon::parse($request->data_inicio)->startOfDay();
            $query->where('created_at', '>=', $dataInicio);
        }

        if ($request->has('data_fim') && $request->data_fim) {
            $dataFim = Carbon::parse($request->data_fim)->endOfDay();
            $query->where('created_at', '<=', $dataFim);
        }

        $registros = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'registros' => $registros
        ]);
    }

    public function pontosRegistrados(Request $request): JsonResponse
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado'
            ], 403);
        }

        $query = PontoRegistro::with('user');

        if ($request->has('data_inicio') && $request->data_inicio) {
            $dataInicio = Carbon::parse($request->data_inicio)->startOfDay();
            $query->where('created_at', '>=', $dataInicio);
        }

        if ($request->has('data_fim') && $request->data_fim) {
            $dataFim = Carbon::parse($request->data_fim)->endOfDay();
            $query->where('created_at', '<=', $dataFim);
        }

        $registros = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'registros' => $registros
        ]);
    }
}
