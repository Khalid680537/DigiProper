<?php

namespace App\Http\Requests;

use App\Models\PropertyDocument;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePropertyDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', [PropertyDocument::class, $this->route('property')]) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', Rule::in(['title', 'lease', 'rera', 'tax_receipt', 'id_proof', 'other'])],
            'file' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'mimetypes:application/pdf,image/jpeg,image/png',
                'max:10240',
            ],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
