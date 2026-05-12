<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class PropertyDocumentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'property_id' => $this->property_id,
            'title' => $this->title,
            'category' => $this->category,
            'original_name' => $this->original_name,
            'mime_type' => $this->mime_type,
            'size_bytes' => $this->size_bytes,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toIso8601String(),
            'download_url' => URL::temporarySignedRoute(
                'api.v1.properties.documents.download',
                now()->addMinutes(15),
                ['property' => $this->property_id, 'document' => $this->id],
            ),
        ];
    }
}
