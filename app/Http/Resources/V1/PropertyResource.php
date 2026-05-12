<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'title_holder' => $this->title_holder,
            'rera_number' => $this->rera_number,

            'address' => [
                'line1' => $this->address_line1,
                'line2' => $this->address_line2,
                'city' => $this->city,
                'state' => $this->state,
                'pincode' => $this->pincode,
            ],

            'tenure' => $this->tenure,
            'tenure_authority' => $this->tenure_authority,
            'occupancy_status' => $this->occupancy_status,
            'tenant_or_occupant' => $this->tenant_or_occupant,

            'construction' => $this->construction,
            'area' => [
                'value' => $this->area_value,
                'unit' => $this->area_unit,
            ],

            'financials' => [
                'imputed_value_inr' => $this->imputed_value_inr,
                'rent_yearly_inr' => $this->rent_yearly_inr,
                'yield_percent' => $this->yield_percent,
                'effective_yield_percent' => $this->effective_yield_percent,
            ],

            'keys_location' => $this->keys_location,
            'extra_notes' => $this->extra_notes,
            'is_data_complete' => (bool) $this->is_data_complete,

            'contacts' => PropertyContactResource::collection($this->whenLoaded('contacts')),
            'documents' => PropertyDocumentResource::collection($this->whenLoaded('documents')),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
