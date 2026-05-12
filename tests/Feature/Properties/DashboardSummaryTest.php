<?php

use App\Models\Property;
use App\Models\User;

test('dashboard renders with empty totals when no properties exist', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSeeText('Properties')
        ->assertSeeText('No properties yet. Add your first one to get started.');
});

test('dashboard aggregates totals across properties', function () {
    $user = User::factory()->create();

    Property::factory()->create([
        'name' => 'A',
        'imputed_value_inr' => 10_000_000,
        'rent_yearly_inr' => 500_000,
    ]);
    Property::factory()->create([
        'name' => 'B',
        'imputed_value_inr' => 5_000_000,
        'rent_yearly_inr' => 250_000,
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'))->assertOk();

    // 2 properties total
    $response->assertSeeText('2');

    // avg yield = 750000 / 15000000 = 5%
    $response->assertSeeText('5%');

    // both names appear in the "Recent properties" list
    $response->assertSeeText('A');
    $response->assertSeeText('B');
});
