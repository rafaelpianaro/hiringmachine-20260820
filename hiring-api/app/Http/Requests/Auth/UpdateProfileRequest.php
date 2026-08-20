<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'company' => 'sometimes|string|max:255',
            'position' => 'sometimes|string|max:255',
            'avatar' => 'sometimes|string|max:255',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'name.string' => 'O nome deve ser um texto.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'phone.string' => 'O telefone deve ser um texto.',
            'phone.max' => 'O telefone não pode ter mais de 20 caracteres.',
            'company.string' => 'A empresa deve ser um texto.',
            'company.max' => 'A empresa não pode ter mais de 255 caracteres.',
            'position.string' => 'O cargo deve ser um texto.',
            'position.max' => 'O cargo não pode ter mais de 255 caracteres.',
            'avatar.string' => 'O avatar deve ser um texto.',
            'avatar.max' => 'O avatar não pode ter mais de 255 caracteres.',
        ];
    }
}
