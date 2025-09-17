<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Address;
use App\Services\CpfValidationService;
use App\Services\CepService;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Employees",
 *     description="Operações de gerenciamento de funcionários"
 * )
 */
class EmployeeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/employees",
     *     summary="Listar funcionários",
     *     tags={"Employees"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de funcionários",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="funcionarios",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="João Silva"),
     *                     @OA\Property(property="email", type="string", example="joao@empresa.com"),
     *                     @OA\Property(property="cpf", type="string", example="123.456.789-00"),
     *                     @OA\Property(property="cargo", type="string", example="Desenvolvedor"),
     *                     @OA\Property(property="data_nascimento", type="string", format="date", example="1990-01-15"),
     *                     @OA\Property(property="role", type="string", example="employer"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-17T10:30:00.000000Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acesso negado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Acesso negado")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado'
            ], 403);
        }

        $funcionarios = User::where('role', 'employer')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'funcionarios' => $funcionarios
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/employees",
     *     summary="Criar novo funcionário",
     *     tags={"Employees"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation","cpf","cargo","data_nascimento","cep","endereco","numero","bairro","cidade","estado"},
     *             @OA\Property(property="name", type="string", example="João Silva"),
     *             @OA\Property(property="email", type="string", format="email", example="joao@empresa.com"),
     *             @OA\Property(property="password", type="string", format="password", example="123456"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="123456"),
     *             @OA\Property(property="cpf", type="string", example="123.456.789-00"),
     *             @OA\Property(property="cargo", type="string", example="Desenvolvedor"),
     *             @OA\Property(property="data_nascimento", type="string", format="date", example="1990-01-15"),
     *             @OA\Property(property="cep", type="string", example="01234-567"),
     *             @OA\Property(property="endereco", type="string", example="Rua das Flores, 123"),
     *             @OA\Property(property="numero", type="string", example="123"),
     *             @OA\Property(property="complemento", type="string", example="Apto 45"),
     *             @OA\Property(property="bairro", type="string", example="Centro"),
     *             @OA\Property(property="cidade", type="string", example="São Paulo"),
     *             @OA\Property(property="estado", type="string", example="SP")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Funcionário criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Funcionário criado com sucesso"),
     *             @OA\Property(
     *                 property="funcionario",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="João Silva"),
     *                 @OA\Property(property="email", type="string", example="joao@empresa.com"),
     *                 @OA\Property(property="cpf", type="string", example="123.456.789-00"),
     *                 @OA\Property(property="cargo", type="string", example="Desenvolvedor"),
     *                 @OA\Property(property="data_nascimento", type="string", format="date", example="1990-01-15"),
     *                 @OA\Property(property="role", type="string", example="employer"),
     *                 @OA\Property(
     *                     property="address",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="cep", type="string", example="01234-567"),
     *                     @OA\Property(property="endereco", type="string", example="Rua das Flores, 123"),
     *                     @OA\Property(property="numero", type="string", example="123"),
     *                     @OA\Property(property="complemento", type="string", example="Apto 45"),
     *                     @OA\Property(property="bairro", type="string", example="Centro"),
     *                     @OA\Property(property="cidade", type="string", example="São Paulo"),
     *                     @OA\Property(property="estado", type="string", example="SP")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acesso negado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Acesso negado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dados de validação inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="name", type="array", @OA\Items(type="string", example="Nome é obrigatório")),
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="E-mail deve ser válido")),
     *                 @OA\Property(property="cpf", type="array", @OA\Items(type="string", example="CPF inválido."))
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $funcionario = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'employer',
                'admin_id' => Auth::id(),
                'cpf' => CpfValidationService::format($request->cpf),
                'cargo' => $request->cargo,
                'data_nascimento' => $request->data_nascimento,
            ]);

            Address::create([
                'user_id' => $funcionario->id,
                'cep' => $request->cep,
                'endereco' => $request->endereco,
                'numero' => $request->numero,
                'complemento' => $request->complemento,
                'bairro' => $request->bairro,
                'cidade' => $request->cidade,
                'estado' => $request->estado,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Funcionário criado com sucesso',
                'funcionario' => $funcionario->load('address')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar funcionário: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/employees/{employee}",
     *     summary="Atualizar funcionário",
     *     tags={"Employees"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="employee",
     *         in="path",
     *         description="ID do funcionário",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","cpf","cargo","data_nascimento","cep","endereco","numero","bairro","cidade","estado"},
     *             @OA\Property(property="name", type="string", example="João Silva"),
     *             @OA\Property(property="email", type="string", format="email", example="joao@empresa.com"),
     *             @OA\Property(property="cpf", type="string", example="123.456.789-00"),
     *             @OA\Property(property="cargo", type="string", example="Desenvolvedor Senior"),
     *             @OA\Property(property="data_nascimento", type="string", format="date", example="1990-01-15"),
     *             @OA\Property(property="cep", type="string", example="01234-567"),
     *             @OA\Property(property="endereco", type="string", example="Rua das Flores, 123"),
     *             @OA\Property(property="numero", type="string", example="123"),
     *             @OA\Property(property="complemento", type="string", example="Apto 45"),
     *             @OA\Property(property="bairro", type="string", example="Centro"),
     *             @OA\Property(property="cidade", type="string", example="São Paulo"),
     *             @OA\Property(property="estado", type="string", example="SP")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Funcionário atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Funcionário atualizado com sucesso"),
     *             @OA\Property(
     *                 property="funcionario",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="João Silva"),
     *                 @OA\Property(property="email", type="string", example="joao@empresa.com"),
     *                 @OA\Property(property="cpf", type="string", example="123.456.789-00"),
     *                 @OA\Property(property="cargo", type="string", example="Desenvolvedor Senior"),
     *                 @OA\Property(property="data_nascimento", type="string", format="date", example="1990-01-15")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acesso negado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Acesso negado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Funcionário não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Funcionário não encontrado")
     *         )
     *     )
     * )
     */
    public function update(UpdateEmployeeRequest $request, User $funcionario): JsonResponse
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado'
            ], 403);
        }

        if ($funcionario->admin_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Funcionário não encontrado'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $funcionario->id,
        ], [
            'name.required' => 'Nome é obrigatório',
            'email.required' => 'E-mail é obrigatório',
            'email.email' => 'E-mail deve ser válido',
            'email.unique' => 'Este e-mail já está em uso',
        ]);

        $funcionario->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Funcionário atualizado com sucesso',
            'funcionario' => $funcionario
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/employees/{employee}",
     *     summary="Excluir funcionário",
     *     tags={"Employees"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="employee",
     *         in="path",
     *         description="ID do funcionário",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Funcionário excluído com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Funcionário excluído com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acesso negado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Acesso negado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Funcionário não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Funcionário não encontrado")
     *         )
     *     )
     * )
     */
    public function destroy(User $funcionario): JsonResponse
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado'
            ], 403);
        }

        if ($funcionario->admin_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Funcionário não encontrado'
            ], 404);
        }

        $funcionario->delete();

        return response()->json([
            'success' => true,
            'message' => 'Funcionário excluído com sucesso'
        ]);
    }
}
