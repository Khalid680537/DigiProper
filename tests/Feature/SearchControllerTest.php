<?php

use App\Models\Property;
use App\Models\User;

test('unauthenticated requests to /search are rejected', function () {
    $this->getJson(route('search', ['q' => 'foo']))->assertStatus(401);
});

test('empty query returns no properties', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Property::factory()->create(['name' => 'Visible Asset']);

    $this->getJson(route('search', ['q' => '']))
        ->assertOk()
        ->assertExactJson(['properties' => []]);
});

test('matches by name, city, or address_line1', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $byName = Property::factory()->create(['name' => 'Connaught Tower', 'city' => 'Mumbai', 'address_line1' => 'Marine Drive']);
    $byCity = Property::factory()->create(['name' => 'Random A', 'city' => 'Hyderabad', 'address_line1' => 'Banjara Hills']);
    $byAddress = Property::factory()->create(['name' => 'Random B', 'city' => 'Pune', 'address_line1' => 'Koregaon Park Road']);
    Property::factory()->create(['name' => 'Untouched', 'city' => 'Chennai', 'address_line1' => 'Anna Salai']);

    $nameHit = $this->getJson(route('search', ['q' => 'Connaught']))->json('properties');
    expect($nameHit)->toHaveCount(1)
        ->and($nameHit[0]['id'])->toBe($byName->id);

    $cityHit = $this->getJson(route('search', ['q' => 'Hyderabad']))->json('properties');
    expect($cityHit)->toHaveCount(1)
        ->and($cityHit[0]['id'])->toBe($byCity->id);

    $addressHit = $this->getJson(route('search', ['q' => 'Koregaon']))->json('properties');
    expect($addressHit)->toHaveCount(1)
        ->and($addressHit[0]['id'])->toBe($byAddress->id);
});

test('returned rows expose id, name, city, and a show url', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $property = Property::factory()->create(['name' => 'Suncrest', 'city' => 'Goa']);

    $row = $this->getJson(route('search', ['q' => 'Suncrest']))->json('properties.0');

    expect($row)->toMatchArray([
        'id' => $property->id,
        'name' => 'Suncrest',
        'city' => 'Goa',
        'url' => route('properties.show', $property),
    ]);
});

test("a user cannot search another user's properties", function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();

    $this->actingAs($alice);
    Property::factory()->create(['name' => 'Alice Mansion', 'city' => 'Jaipur']);

    $this->actingAs($bob);
    Property::factory()->create(['name' => 'Bob Bungalow', 'city' => 'Jaipur']);

    $aliceResults = $this->actingAs($alice)->getJson(route('search', ['q' => 'Jaipur']))->json('properties');
    expect($aliceResults)->toHaveCount(1)
        ->and($aliceResults[0]['name'])->toBe('Alice Mansion');

    $bobResults = $this->actingAs($bob)->getJson(route('search', ['q' => 'Jaipur']))->json('properties');
    expect($bobResults)->toHaveCount(1)
        ->and($bobResults[0]['name'])->toBe('Bob Bungalow');
});

test('results are capped at eight rows', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Property::factory()->count(12)->create(['city' => 'Bengaluru']);

    $this->getJson(route('search', ['q' => 'Bengaluru']))
        ->assertOk()
        ->assertJsonCount(8, 'properties');
});

test('authenticated pages mount the command palette', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('x-on:keydown.window.meta.k.prevent', false)
        ->assertSee('open-command-palette', false)
        ->assertSee('x-data="commandPalette({', false);
});

test('the command palette renders a close button so mobile users are not trapped', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('aria-label="Close search"', false)
        ->assertSee('x-on:click.self="close()"', false);
});

test('the topbar search button dispatches the open event', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee("\$dispatch('open-command-palette')", false);
});

test('the command palette is not rendered for guests', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertDontSee('x-data="commandPalette({', false);
});
