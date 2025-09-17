<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class TrocarSenhaController extends Controller
{
    public function trocar(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        $request->validate([
            'senha_atual' => 'required|string',
            'nova_senha' => 'required|string|min:6',
            'confirmar_senha' => 'required|string|same:nova_senha'
        ], [
            'senha_atual.required' => 'A senha atual é obrigatória',
            'nova_senha.required' => 'A nova senha é obrigatória',
            'nova_senha.min' => 'A nova senha deve ter pelo menos 6 caracteres',
            'confirmar_senha.required' => 'A confirmação de senha é obrigatória',
            'confirmar_senha.same' => 'A confirmação de senha não confere com a nova senha'
        ]);

        $user = Auth::user();

        if (!Hash::check($request->senha_atual, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'A senha atual está incorreta'
            ], 400);
        }

        $user->update([
            'password' => Hash::make($request->nova_senha)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Senha alterada com sucesso'
        ]);
    }
}
