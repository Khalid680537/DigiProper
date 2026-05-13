<?php

namespace App\Http\Requests;

use App\Models\PropertyPhoto;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePropertyPhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', [PropertyPhoto::class, $this->route('property')]) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp,heic,heif',
                'mimetypes:image/jpeg,image/png,image/webp,image/heic,image/heif',
                'max:5120',
            ],
            'is_primary' => ['nullable', 'boolean'],
        ];
    }
}
