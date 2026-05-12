<?php

namespace Database\Factories;

use App\Models\Property;
use App\Support\IndianStates;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->streetName().' Property',
            'title_holder' => fake()->company(),
            'rera_number' => null,

            'address_line1' => fake()->streetAddress(),
            'address_line2' => null,
            'city' => fake()->city(),
            'state' => fake()->randomElement(IndianStates::codes()),
            'pincode' => (string) fake()->numberBetween(110001, 999999),

            'tenure' => fake()->randomElement(['freehold', 'leasehold', 'rented_in', 'pagri', 'other']),
            'tenure_authority' => fake()->randomElement(['DDA', 'MCD', null, null]),
            'occupancy_status' => fake()->randomElement(['rented_out', 'self_use', 'vacant_plot', 'vacant_built']),
            'tenant_or_occupant' => fake()->optional()->company(),

            'construction' => fake()->randomElement([
                'Plot',
                'Ground Floor',
                'Ground Floor + First',
                'Basement + Ground Floor + First + Second',
                'Ground Floor Godown',
            ]),
            'area_value' => fake()->numberBetween(100, 1000),
            'area_unit' => fake()->randomElement(['sqm', 'sqft', 'sqyd']),

            'imputed_value_inr' => fake()->numberBetween(50, 10_000) * 100_000,
            'rent_yearly_inr' => fake()->numberBetween(0, 50) * 100_000,
            'yield_percent' => null,

            'contacts' => [
                [
                    'name' => fake()->name(),
                    'phone' => '+91'.fake()->numerify('##########'),
                    'role' => fake()->randomElement(['Consultant', 'Manager', 'Field Officer']),
                    'notes' => null,
                ],
            ],
            'keys_location' => fake()->optional()->streetAddress(),
            'extra_notes' => fake()->optional()->sentence(),
            'is_data_complete' => false,
        ];
    }
}
