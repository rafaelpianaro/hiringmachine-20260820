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
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'phone.string' => 'The phone must be a string.',
            'phone.max' => 'The phone must not exceed 20 characters.',
            'company.string' => 'The company must be a string.',
            'company.max' => 'The company must not exceed 255 characters.',
            'position.string' => 'The position must be a string.',
            'position.max' => 'The position must not exceed 255 characters.',
            'avatar.string' => 'The avatar must be a string.',
            'avatar.max' => 'The avatar must not exceed 255 characters.',
        ];
    }
}
