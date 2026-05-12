<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\PropertyContact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PropertyContact>
 */
class PropertyContactFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'name' => fake()->name(),
            'phone' => '+91'.fake()->numerify('##########'),
            'role' => fake()->randomElement(['Consultant', 'Manager', 'Field Officer']),
            'notes' => null,
            'position' => 0,
        ];
    }
}
