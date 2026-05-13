<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Models\Property;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Property::class);

        $query = Property::query()->with('primaryPhoto')->orderBy('name');

        $q = trim((string) $request->query('q', ''));
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('city', 'like', "%{$q}%")
                    ->orWhere('address_line1', 'like', "%{$q}%");
            });
        }

        /** @var LengthAwarePaginator $properties */
        $properties = $query->paginate(20)->withQueryString();

        return view('properties.index', [
            'properties' => $properties,
            'q' => $q,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Property::class);

        return view('properties.create', [
            'property' => new Property,
        ]);
    }

    public function store(StorePropertyRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $contacts = $validated['contacts'] ?? [];
        unset($validated['contacts']);

        $property = Property::create($validated);
        $this->syncContacts($property, $contacts);

        return Redirect::route('properties.show', $property)->with('status', 'property-created');
    }

    public function show(Property $property): View
    {
        $this->authorize('view', $property);

        $property->load([
            'documents' => fn ($q) => $q->latest(),
            'photos',
            'primaryPhoto',
            'contacts',
            'creator',
            'updater',
        ]);

        return view('properties.show', [
            'property' => $property,
        ]);
    }

    public function edit(Property $property): View
    {
        $this->authorize('update', $property);

        $property->load('contacts');

        return view('properties.edit', [
            'property' => $property,
        ]);
    }

    public function update(UpdatePropertyRequest $request, Property $property): RedirectResponse
    {
        $validated = $request->validated();
        $contacts = $validated['contacts'] ?? [];
        unset($validated['contacts']);

        $property->update($validated);
        $this->syncContacts($property, $contacts);

        return Redirect::route('properties.show', $property)->with('status', 'property-updated');
    }

    public function destroy(Property $property): RedirectResponse
    {
        $this->authorize('delete', $property);

        $property->delete();

        return Redirect::route('properties.index')->with('status', 'property-deleted');
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
