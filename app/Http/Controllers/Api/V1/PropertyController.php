<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StorePropertyRequest;
use App\Http\Resources\V1\PropertyResource;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PropertyController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Property::class);

        $query = Property::query()->orderBy('name');

        $q = trim((string) $request->query('q', ''));
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('city', 'like', "%{$q}%")
                    ->orWhere('address_line1', 'like', "%{$q}%");
            });
        }

        return PropertyResource::collection($query->paginate(20));
    }

    public function store(StorePropertyRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $contacts = $validated['contacts'] ?? [];
        unset($validated['contacts']);

        $property = Property::create($validated);
        $this->syncContacts($property, $contacts);
        $property->load(['contacts', 'documents']);

        return PropertyResource::make($property)
            ->response()
            ->setStatusCode(201);
    }

    public function show(Property $property): PropertyResource
    {
        $this->authorize('view', $property);

        $property->load(['contacts', 'documents']);

        return PropertyResource::make($property);
    }

    public function update(StorePropertyRequest $request, Property $property): PropertyResource
    {
        $this->authorize('update', $property);

        $validated = $request->validated();
        $contacts = $validated['contacts'] ?? [];
        unset($validated['contacts']);

        $property->update($validated);
        $this->syncContacts($property, $contacts);
        $property->load(['contacts', 'documents']);

        return PropertyResource::make($property);
    }

    public function destroy(Property $property): JsonResponse
    {
        $this->authorize('delete', $property);

        $property->delete();

        return response()->json(null, 204);
    }

    /**
     * @param  array<int, array<string, mixed>>  $contacts
     */
    protected function syncContacts(Property $property, array $contacts): void
    {
        $property->contacts()->delete();

        foreach (array_values($contacts) as $position => $contact) {
            $property->contacts()->create([
                'name' => $contact['name'] ?? null,
                'phone' => $contact['phone'] ?? null,
                'role' => $contact['role'] ?? null,
                'notes' => $contact['notes'] ?? null,
                'position' => $position,
            ]);
        }
    }
}
