<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePropertyPhotoRequest;
use App\Models\Property;
use App\Models\PropertyPhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PropertyPhotoController extends Controller
{
    public function store(StorePropertyPhotoRequest $request, Property $property): RedirectResponse
    {
        $file = $request->file('file');
        $path = $file->store("property-photos/{$property->id}", 'local');

        DB::transaction(function () use ($property, $request, $file, $path) {
            $shouldBePrimary = $request->boolean('is_primary')
                || ! $property->photos()->where('is_primary', true)->exists();

            if ($shouldBePrimary) {
                $property->photos()->where('is_primary', true)->update(['is_primary' => false]);
            }

            $property->photos()->create([
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'size_bytes' => $file->getSize(),
                'is_primary' => $shouldBePrimary,
                'sort_order' => ($property->photos()->max('sort_order') ?? 0) + 1,
            ]);
        });

        return Redirect::route('properties.show', $property)->with('status', 'photo-uploaded');
    }

    public function show(Property $property, PropertyPhoto $photo): StreamedResponse
    {
        abort_unless($photo->property_id === $property->id, 404);

        $this->authorize('view', $photo);

        abort_unless(Storage::disk('local')->exists($photo->file_path), 404);

        return Storage::disk('local')->response(
            $photo->file_path,
            $photo->original_name,
            [
                'Content-Type' => $photo->mime_type,
                'Cache-Control' => 'private, max-age=3600',
            ],
        );
    }

    public function makePrimary(Property $property, PropertyPhoto $photo): RedirectResponse
    {
        abort_unless($photo->property_id === $property->id, 404);

        $this->authorize('update', $photo);

        DB::transaction(function () use ($property, $photo) {
            $property->photos()->where('id', '!=', $photo->id)->update(['is_primary' => false]);
            $photo->update(['is_primary' => true]);
        });

        return Redirect::route('properties.show', $property)->with('status', 'photo-primary-set');
    }

    public function destroy(Property $property, PropertyPhoto $photo): RedirectResponse
    {
        abort_unless($photo->property_id === $property->id, 404);

        $this->authorize('delete', $photo);

        $wasPrimary = $photo->is_primary;

        DB::transaction(function () use ($property, $photo, $wasPrimary) {
            Storage::disk('local')->delete($photo->file_path);
            $photo->delete();

            if ($wasPrimary) {
                $next = $property->photos()->orderBy('sort_order')->orderBy('id')->first();
                $next?->update(['is_primary' => true]);
            }
        });

        return Redirect::route('properties.show', $property)->with('status', 'photo-deleted');
    }
}
