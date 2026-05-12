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
        $query = Property::query()->orderBy('name');

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
        return view('properties.create', [
            'property' => new Property,
        ]);
    }

    public function store(StorePropertyRequest $request): RedirectResponse
    {
        $property = Property::create($request->validated());

        return Redirect::route('properties.show', $property)->with('status', 'property-created');
    }

    public function show(Property $property): View
    {
        $property->load(['documents' => fn ($q) => $q->latest(), 'creator', 'updater']);

        return view('properties.show', [
            'property' => $property,
        ]);
    }

    public function edit(Property $property): View
    {
        return view('properties.edit', [
            'property' => $property,
        ]);
    }

    public function update(UpdatePropertyRequest $request, Property $property): RedirectResponse
    {
        $property->update($request->validated());

        return Redirect::route('properties.show', $property)->with('status', 'property-updated');
    }

    public function destroy(Property $property): RedirectResponse
    {
        $property->delete();

        return Redirect::route('properties.index')->with('status', 'property-deleted');
    }
}
