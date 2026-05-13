<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\PropertyPhoto;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PropertyPhoto>
 */
class PropertyPhotoFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $mime = fake()->randomElement(['image/jpeg', 'image/png', 'image/webp']);
        $extension = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        };

        return [
            'property_id' => Property::factory(),
            'file_path' => 'property-photos/'.Str::random(40).'.'.$extension,
            'original_name' => fake()->slug(2).'.'.$extension,
            'mime_type' => $mime,
            'size_bytes' => fake()->numberBetween(50_000, 4_000_000),
            'is_primary' => false,
            'sort_order' => 0,
        ];
    }

    public function primary(): static
    {
        return $this->state(fn () => ['is_primary' => true]);
    }
}
