<?php

namespace App\Http\Controllers;

use App\Models\PontoRegistro;
use App\Repositories\TimeRecordRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected TimeRecordRepository $timeRecordRepository;

    public function __construct(TimeRecordRepository $timeRecordRepository)
    {
        $this->timeRecordRepository = $timeRecordRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/reports/timeclock",
     *     summary="Lista registros de ponto com informações completas",
     *     description="Retorna uma listagem de registros de ponto contendo ID do Registro, Nome do Funcionário, Cargo, Idade, Nome do Gestor e Data/Hora Completa do Registro. Utiliza SQL puro com JOINs para performance otimizada.",
     *     operationId="listarRegistrosPonto",
     *     tags={"Reports"},
     *     @OA\Parameter(
     *         name="data_inicio",
     *         in="query",
     *         required=false,
     *         description="Data de início para filtro (formato: Y-m-d)",
     *         @OA\Schema(
     *             type="string",
     *             format="date",
     *             example="2024-01-01"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="data_fim",
     *         in="query",
     *         required=false,
     *         description="Data de fim para filtro (formato: Y-m-d)",
     *         @OA\Schema(
     *             type="string",
     *             format="date",
     *             example="2024-12-31"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Registros de ponto retornados com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="registros", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="registro_id", type="integer", example=1, description="ID do registro de ponto"),
     *                     @OA\Property(property="nome_funcionario", type="string", example="João Silva", description="Nome do funcionário"),
     *                     @OA\Property(property="cargo", type="string", example="Desenvolvedor", description="Cargo do funcionário"),
     *                     @OA\Property(property="idade", type="integer", example=30, description="Idade do funcionário"),
     *                     @OA\Property(property="nome_gestor", type="string", example="Maria Santos", description="Nome do gestor"),
     *                     @OA\Property(property="data_hora_completa", type="string", example="15/01/2024 14:30:25", description="Data e hora completa do registro"),
     *                     @OA\Property(property="timestamp_original", type="string", format="date-time", example="2024-01-15T14:30:25.000000Z", description="Timestamp original do registro")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acesso negado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Acesso negado")
     *         )
     *     )
     * )
     */
    public function ponto(Request $request): JsonResponse
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado'
            ], 403);
        }

        try {
            $dataInicio = $request->get('data_inicio');
            $dataFim = $request->get('data_fim');

            // Validação das datas
            if ($dataInicio && !Carbon::createFromFormat('Y-m-d', $dataInicio)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Formato de data de início inválido. Use Y-m-d'
                ], 422);
            }

            if ($dataFim && !Carbon::createFromFormat('Y-m-d', $dataFim)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Formato de data de fim inválido. Use Y-m-d'
                ], 422);
            }

            // Buscar registros usando repository com SQL puro
            $registros = $this->timeRecordRepository->buscarRegistrosCompletos($dataInicio, $dataFim);

            return response()->json([
                'success' => true,
                'registros' => $registros
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar registros de ponto: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
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
