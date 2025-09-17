<?php

namespace App\Http\Controllers;

use App\Services\CepService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CepController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/cep/{cep}",
     *     summary="Consultar CEP",
     *     description="Consulta informações de endereço através do CEP utilizando o serviço ViaCEP. Esta rota é pública e não requer autenticação.",
     *     operationId="consultarCep",
     *     tags={"CEP"},
     *     @OA\Parameter(
     *         name="cep",
     *         in="path",
     *         required=true,
     *         description="CEP a ser consultado (formato: 00000000 ou 00000-000)",
     *         @OA\Schema(
     *             type="string", 
     *             pattern="^[0-9]{8}$|^[0-9]{5}-[0-9]{3}$",
     *             example="01310100"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="CEP encontrado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="cep", type="string", example="01310-100", description="CEP formatado"),
     *                 @OA\Property(property="endereco", type="string", example="Avenida Paulista", description="Nome da rua/avenida"),
     *                 @OA\Property(property="bairro", type="string", example="Bela Vista", description="Nome do bairro"),
     *                 @OA\Property(property="cidade", type="string", example="São Paulo", description="Nome da cidade"),
     *                 @OA\Property(property="estado", type="string", example="SP", description="Sigla do estado")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="CEP não encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="CEP não encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="CEP inválido",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="CEP deve ter 8 dígitos")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erro interno do servidor")
     *         )
     *     )
     * )
     */
    public function consultar(string $cep): JsonResponse
    {
        // Validação básica do CEP
        $cepLimpo = preg_replace('/[^0-9]/', '', $cep);

        if (strlen($cepLimpo) !== 8) {
            return response()->json([
                'success' => false,
                'message' => 'CEP deve ter 8 dígitos'
            ], 422);
        }

        try {
            $dadosCep = CepService::consultarCep($cep);

            if ($dadosCep === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'CEP não encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $dadosCep
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao consultar CEP: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }
}
