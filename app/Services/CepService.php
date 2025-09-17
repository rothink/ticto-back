<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CepService
{
    public static function consultarCep(string $cep): ?array
    {
        $cep = preg_replace('/[^0-9]/', '', $cep);

        if (strlen($cep) !== 8) {
            return null;
        }

        try {
            $response = Http::timeout(10)->get("https://viacep.com.br/ws/{$cep}/json/");

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['erro'])) {
                    return null;
                }

                return [
                    'cep' => $data['cep'] ?? null,
                    'endereco' => $data['logradouro'] ?? null,
                    'bairro' => $data['bairro'] ?? null,
                    'cidade' => $data['localidade'] ?? null,
                    'estado' => $data['uf'] ?? null,
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Erro ao consultar CEP: ' . $e->getMessage());
        }

        return null;
    }
}
