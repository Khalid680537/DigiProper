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
    private const OCCUPANCY_VALUES = ['rented_out', 'self_use', 'vacant_plot', 'vacant_built'];

    private const TENURE_VALUES = ['freehold', 'leasehold', 'rented_in', 'pagri', 'other'];

    private const SORT_VALUES = ['name', 'value_desc', 'yield_desc', 'recent'];

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Property::class);

        $q = trim((string) $request->query('q', ''));
        $occupancy = in_array($request->query('occupancy'), self::OCCUPANCY_VALUES, true)
            ? $request->query('occupancy')
            : null;
        $tenure = in_array($request->query('tenure'), self::TENURE_VALUES, true)
            ? $request->query('tenure')
            : null;
        $hasDocuments = $request->query('has') === 'documents';
        $sort = in_array($request->query('sort'), self::SORT_VALUES, true)
            ? $request->query('sort')
            : 'name';

        $query = Property::query()->with('primaryPhoto');

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('city', 'like', "%{$q}%")
                    ->orWhere('address_line1', 'like', "%{$q}%");
            });
        }

        if ($occupancy !== null) {
            $query->where('occupancy_status', $occupancy);
        }

        if ($tenure !== null) {
            $query->where('tenure', $tenure);
        }

        if ($hasDocuments) {
            $query->whereHas('documents');
        }

        match ($sort) {
            'value_desc' => $query
                ->orderByRaw('imputed_value_inr IS NULL')
                ->orderByDesc('imputed_value_inr'),
            'yield_desc' => $query
                ->orderByRaw('COALESCE(yield_percent, CASE WHEN imputed_value_inr > 0 THEN rent_yearly_inr * 100.0 / imputed_value_inr ELSE NULL END) IS NULL')
                ->orderByRaw('COALESCE(yield_percent, CASE WHEN imputed_value_inr > 0 THEN rent_yearly_inr * 100.0 / imputed_value_inr ELSE NULL END) DESC'),
            'recent' => $query->orderByDesc('created_at'),
            default => $query->orderBy('name'),
        };

        /** @var LengthAwarePaginator $properties */
        $properties = $query->paginate(20)->withQueryString();

        return view('properties.index', [
            'properties' => $properties,
            'q' => $q,
            'filters' => [
                'occupancy' => $occupancy,
                'tenure' => $tenure,
                'has' => $hasDocuments ? 'documents' : null,
                'sort' => $sort,
            ],
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
