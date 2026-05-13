<?php

namespace App\Http\Requests;

use App\Models\Property;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateShareVisibilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        $property = $this->route('property');

        return $property instanceof Property
            && ($this->user()?->can('update', $property) ?? false);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'share_financials' => ['sometimes', 'boolean'],
            'share_contacts' => ['sometimes', 'boolean'],
            'share_keys_location' => ['sometimes', 'boolean'],
            'share_extra_notes' => ['sometimes', 'boolean'],
            'share_title_holder' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'share_financials' => $this->boolean('share_financials'),
            'share_contacts' => $this->boolean('share_contacts'),
            'share_keys_location' => $this->boolean('share_keys_location'),
            'share_extra_notes' => $this->boolean('share_extra_notes'),
            'share_title_holder' => $this->boolean('share_title_holder'),
        ]);
    }
}
