<?php

namespace App\Http\Requests;

use App\Services\CpfValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        $employeeId = $this->route('employee')->id;

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($employeeId)
            ],
            'cpf' => [
                'required',
                'string',
                Rule::unique('users')->ignore($employeeId),
                function ($attribute, $value, $fail) {
                    if (!CpfValidationService::isValid($value)) {
                        $fail('CPF inválido.');
                    }
                },
            ],
            'cargo' => 'required|string|max:255',
            'data_nascimento' => 'required|date|before:today',
            'cep' => 'required|string|size:9',
            'endereco' => 'required|string|max:255',
            'numero' => 'required|string|max:10',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|size:2',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nome é obrigatório',
            'email.required' => 'E-mail é obrigatório',
            'email.email' => 'E-mail deve ser válido',
            'email.unique' => 'Este e-mail já está em uso',
            'cpf.required' => 'CPF é obrigatório',
            'cpf.unique' => 'Este CPF já está em uso',
            'cargo.required' => 'Cargo é obrigatório',
            'data_nascimento.required' => 'Data de nascimento é obrigatória',
            'data_nascimento.before' => 'Data de nascimento deve ser anterior a hoje',
            'cep.required' => 'CEP é obrigatório',
            'cep.size' => 'CEP deve ter 9 caracteres',
            'endereco.required' => 'Endereço é obrigatório',
            'numero.required' => 'Número é obrigatório',
            'bairro.required' => 'Bairro é obrigatório',
            'cidade.required' => 'Cidade é obrigatória',
            'estado.required' => 'Estado é obrigatório',
            'estado.size' => 'Estado deve ter 2 caracteres',
        ];
    }
}
