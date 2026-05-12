<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\PropertyDocument;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PropertyDocument>
 */
class PropertyDocumentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $mime = fake()->randomElement(['application/pdf', 'image/jpeg', 'image/png']);
        $extension = match ($mime) {
            'application/pdf' => 'pdf',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
        };

        $original = fake()->slug(3).'.'.$extension;

        return [
            'property_id' => Property::factory(),
            'title' => fake()->randomElement([
                'Title deed scan',
                'Lease agreement',
                'RERA certificate',
                'Property tax receipt',
                'Identity proof of tenant',
            ]),
            'category' => fake()->randomElement(['title', 'lease', 'rera', 'tax_receipt', 'id_proof', 'other']),
            'file_path' => 'property-documents/'.Str::random(40).'.'.$extension,
            'original_name' => $original,
            'mime_type' => $mime,
            'size_bytes' => fake()->numberBetween(50_000, 5_000_000),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
