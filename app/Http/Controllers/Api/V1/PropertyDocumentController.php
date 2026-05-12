<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StorePropertyDocumentRequest;
use App\Http\Resources\V1\PropertyDocumentResource;
use App\Models\Property;
use App\Models\PropertyDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PropertyDocumentController extends Controller
{
    public function index(Property $property): AnonymousResourceCollection
    {
        $this->authorize('view', $property);

        return PropertyDocumentResource::collection(
            $property->documents()->latest()->paginate(20),
        );
    }

    public function store(StorePropertyDocumentRequest $request, Property $property): JsonResponse
    {
        $file = $request->file('file');
        $path = $file->store("property-documents/{$property->id}", 'local');

        $document = $property->documents()->create([
            'title' => $request->validated('title'),
            'category' => $request->validated('category'),
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size_bytes' => $file->getSize(),
            'notes' => $request->validated('notes'),
        ]);

        return PropertyDocumentResource::make($document)
            ->response()
            ->setStatusCode(201);
    }

    public function show(Property $property, PropertyDocument $document): PropertyDocumentResource
    {
        abort_unless($document->property_id === $property->id, 404);

        $this->authorize('view', $document);

        return PropertyDocumentResource::make($document);
    }

    public function download(Property $property, PropertyDocument $document): StreamedResponse
    {
        // Reached via a signed URL embedded in PropertyDocumentResource.
        // The signature itself authorizes the download — no auth:sanctum gate.
        abort_unless($document->property_id === $property->id, 404);
        abort_unless(Storage::disk('local')->exists($document->file_path), 404);

        return Storage::disk('local')->download($document->file_path, $document->original_name);
    }

    public function destroy(Property $property, PropertyDocument $document): JsonResponse
    {
        abort_unless($document->property_id === $property->id, 404);

        $this->authorize('delete', $document);

        $document->delete();

        return response()->json(null, 204);
    }
}
