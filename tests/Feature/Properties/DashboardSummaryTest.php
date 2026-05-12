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
    $this->actingAs($user);

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

    $response = $this->get(route('dashboard'))->assertOk();

    // 2 properties total
    $response->assertSeeText('2');

    // avg yield = 750000 / 15000000 = 5%
    $response->assertSeeText('5%');

    // both names appear in the "Recent properties" list
    $response->assertSeeText('A');
    $response->assertSeeText('B');
});

test('dashboard totals are scoped per user', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();

    $this->actingAs($alice);
    Property::factory()->create([
        'name' => 'Alice One',
        'imputed_value_inr' => 10_000_000,
        'rent_yearly_inr' => 500_000,
    ]);
    Property::factory()->create([
        'name' => 'Alice Two',
        'imputed_value_inr' => 6_000_000,
        'rent_yearly_inr' => 300_000,
    ]);

    $this->actingAs($bob);
    Property::factory()->create([
        'name' => 'Bob Only',
        'imputed_value_inr' => 999_999_999,
        'rent_yearly_inr' => 1_000_000,
    ]);

    $this->actingAs($alice)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSeeText('Alice One')
        ->assertSeeText('Alice Two')
        ->assertDontSeeText('Bob Only');

    $this->actingAs($bob)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSeeText('Bob Only')
        ->assertDontSeeText('Alice One')
        ->assertDontSeeText('Alice Two');
});
