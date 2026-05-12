<?php

namespace App\Http\Requests;

use App\Support\IndianStates;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePropertyRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'title_holder' => ['nullable', 'string', 'max:255'],
            'rera_number' => ['nullable', 'string', 'max:50'],

            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', Rule::in(IndianStates::codes())],
            'pincode' => ['nullable', 'digits:6'],

            'tenure' => ['nullable', Rule::in(['freehold', 'leasehold', 'rented_in', 'pagri', 'other'])],
            'tenure_authority' => ['nullable', 'string', 'max:255'],
            'occupancy_status' => ['nullable', Rule::in(['rented_out', 'self_use', 'vacant_plot', 'vacant_built'])],
            'tenant_or_occupant' => ['nullable', 'string', 'max:255'],

            'construction' => ['nullable', 'string', 'max:255'],
            'area_value' => ['nullable', 'numeric', 'min:0'],
            'area_unit' => ['nullable', Rule::in(['sqm', 'sqft', 'sqyd'])],

            'imputed_value_inr' => ['nullable', 'numeric', 'min:0'],
            'rent_yearly_inr' => ['nullable', 'numeric'],
            'yield_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],

            'contacts' => ['nullable', 'array'],
            'contacts.*.name' => ['nullable', 'string', 'max:255'],
            'contacts.*.phone' => ['nullable', 'string', 'max:20'],
            'contacts.*.role' => ['nullable', 'string', 'max:255'],
            'contacts.*.notes' => ['nullable', 'string', 'max:500'],

            'keys_location' => ['nullable', 'string', 'max:255'],
            'extra_notes' => ['nullable', 'string', 'max:5000'],
            'is_data_complete' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_data_complete' => $this->boolean('is_data_complete'),
            'contacts' => array_values(array_filter(
                (array) $this->input('contacts', []),
                fn ($c) => is_array($c) && array_filter($c, fn ($v) => $v !== null && $v !== '') !== [],
            )),
        ]);
    }
}
