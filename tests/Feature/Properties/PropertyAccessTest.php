<?php

use App\Models\Property;
use App\Models\User;

test('a user cannot view another user\'s property via the web routes', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();

    $this->actingAs($alice);
    $aliceProperty = Property::factory()->create();

    $this->actingAs($bob);

    $this->get(route('properties.show', $aliceProperty))->assertNotFound();
    $this->get(route('properties.edit', $aliceProperty))->assertNotFound();
    $this->from(route('properties.edit', $aliceProperty))
        ->put(route('properties.update', $aliceProperty), ['name' => 'Hijack'])
        ->assertNotFound();
    $this->delete(route('properties.destroy', $aliceProperty))->assertNotFound();

    expect($aliceProperty->fresh()->name)->not->toBe('Hijack');
});

test('the owner can view their own property via the web routes', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $property = Property::factory()->create();

    $this->get(route('properties.show', $property))->assertOk();
    $this->get(route('properties.edit', $property))->assertOk();
});
