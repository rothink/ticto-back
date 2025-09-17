<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
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

    public function store(Request $request): JsonResponse
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado'
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Nome é obrigatório',
            'email.required' => 'E-mail é obrigatório',
            'email.email' => 'E-mail deve ser válido',
            'email.unique' => 'Este e-mail já está em uso',
            'password.required' => 'Senha é obrigatória',
            'password.min' => 'Senha deve ter pelo menos 6 caracteres',
            'password.confirmed' => 'Confirmação de senha não confere',
        ]);

        $funcionario = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'employer',
            'admin_id' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Funcionário criado com sucesso',
            'funcionario' => $funcionario
        ], 201);
    }

    public function update(Request $request, User $funcionario): JsonResponse
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
