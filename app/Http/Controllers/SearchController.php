<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    private const RESULT_LIMIT = 8;

    public function index(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        if ($q === '') {
            return response()->json(['properties' => []]);
        }

        $properties = Property::query()
            ->with('primaryPhoto')
            ->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('city', 'like', "%{$q}%")
                    ->orWhere('address_line1', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->limit(self::RESULT_LIMIT)
            ->get(['id', 'name', 'city'])
            ->map(fn (Property $property): array => [
                'id' => $property->id,
                'name' => $property->name,
                'city' => $property->city,
                'url' => route('properties.show', $property),
                'thumbnail_url' => $property->thumbnail_url,
            ]);

        return response()->json(['properties' => $properties]);
    }
}
