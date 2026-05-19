<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeleteProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'confirmation' => ['required', 'string', Rule::in(['DELETE'])],
        ];
    }

    public function messages(): array
    {
        return [
            'confirmation.in' => 'Type DELETE to confirm account deletion.',
            'confirmation.required' => 'Type DELETE to confirm account deletion.',
        ];
    }
}
