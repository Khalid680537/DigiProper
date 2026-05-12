<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePropertyDocumentRequest;
use App\Models\Property;
use App\Models\PropertyDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PropertyDocumentController extends Controller
{
    public function store(StorePropertyDocumentRequest $request, Property $property): RedirectResponse
    {
        $file = $request->file('file');
        $path = $file->store("property-documents/{$property->id}", 'local');

        $property->documents()->create([
            'title' => $request->validated('title'),
            'category' => $request->validated('category'),
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size_bytes' => $file->getSize(),
            'notes' => $request->validated('notes'),
        ]);

        return Redirect::route('properties.show', $property)->with('status', 'document-uploaded');
    }

    public function show(Property $property, PropertyDocument $document): StreamedResponse
    {
        abort_unless($document->property_id === $property->id, 404);

        $this->authorize('view', $document);

        abort_unless(Storage::disk('local')->exists($document->file_path), 404);

        return Storage::disk('local')->download($document->file_path, $document->original_name);
    }

    public function destroy(Property $property, PropertyDocument $document): RedirectResponse
    {
        abort_unless($document->property_id === $property->id, 404);

        $this->authorize('delete', $document);

        $document->delete();

        return Redirect::route('properties.show', $property)->with('status', 'document-deleted');
    }
}
