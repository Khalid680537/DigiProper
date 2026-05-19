<?php

use App\Models\Property;
use App\Models\PropertyDocument;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('q filter narrows properties by name', function () {
    Property::factory()->create(['name' => 'Sunrise Apartments']);
    Property::factory()->create(['name' => 'Hilltop Villa']);

    $properties = $this->get(route('properties.index', ['q' => 'sunrise']))
        ->assertOk()
        ->viewData('properties');

    expect($properties->total())->toBe(1)
        ->and($properties->first()->name)->toBe('Sunrise Apartments');
});

test('occupancy filter narrows properties', function () {
    Property::factory()->create(['occupancy_status' => 'rented_out', 'name' => 'A']);
    Property::factory()->create(['occupancy_status' => 'self_use', 'name' => 'B']);

    $properties = $this->get(route('properties.index', ['occupancy' => 'rented_out']))
        ->assertOk()
        ->viewData('properties');

    expect($properties->total())->toBe(1)
        ->and($properties->first()->occupancy_status)->toBe('rented_out');
});

test('tenure filter narrows properties', function () {
    Property::factory()->create(['tenure' => 'freehold', 'name' => 'A']);
    Property::factory()->create(['tenure' => 'leasehold', 'name' => 'B']);

    $properties = $this->get(route('properties.index', ['tenure' => 'freehold']))
        ->assertOk()
        ->viewData('properties');

    expect($properties->total())->toBe(1)
        ->and($properties->first()->tenure)->toBe('freehold');
});

test('has=documents filter returns only properties with at least one document', function () {
    $withDoc = Property::factory()->create(['name' => 'With doc']);
    PropertyDocument::factory()->create(['property_id' => $withDoc->id]);
    Property::factory()->create(['name' => 'No doc']);

    $properties = $this->get(route('properties.index', ['has' => 'documents']))
        ->assertOk()
        ->viewData('properties');

    expect($properties->total())->toBe(1)
        ->and($properties->first()->name)->toBe('With doc');
});

test('sort=value_desc orders by imputed value descending', function () {
    Property::factory()->create(['name' => 'Small', 'imputed_value_inr' => 100_000]);
    Property::factory()->create(['name' => 'Big', 'imputed_value_inr' => 50_000_000]);
    Property::factory()->create(['name' => 'Medium', 'imputed_value_inr' => 1_000_000]);

    $properties = $this->get(route('properties.index', ['sort' => 'value_desc']))
        ->assertOk()
        ->viewData('properties');

    expect($properties->pluck('name')->all())->toBe(['Big', 'Medium', 'Small']);
});

test('sort=yield_desc orders by effective yield descending', function () {
    // 4% explicit yield
    Property::factory()->create(['name' => 'Fixed4', 'yield_percent' => '4.00', 'imputed_value_inr' => 1_000_000, 'rent_yearly_inr' => 0]);
    // computed 10% via rent/value
    Property::factory()->create(['name' => 'Computed10', 'yield_percent' => null, 'imputed_value_inr' => 1_000_000, 'rent_yearly_inr' => 100_000]);
    // computed 1% via rent/value
    Property::factory()->create(['name' => 'Computed1', 'yield_percent' => null, 'imputed_value_inr' => 1_000_000, 'rent_yearly_inr' => 10_000]);

    $properties = $this->get(route('properties.index', ['sort' => 'yield_desc']))
        ->assertOk()
        ->viewData('properties');

    expect($properties->pluck('name')->all())->toBe(['Computed10', 'Fixed4', 'Computed1']);
});

test('sort=recent orders by created_at descending', function () {
    $older = Property::factory()->create(['name' => 'Older']);
    $older->forceFill(['created_at' => now()->subDays(5)])->saveQuietly();
    $newer = Property::factory()->create(['name' => 'Newer']);
    $newer->forceFill(['created_at' => now()->subMinute()])->saveQuietly();

    $properties = $this->get(route('properties.index', ['sort' => 'recent']))
        ->assertOk()
        ->viewData('properties');

    expect($properties->pluck('name')->all())->toBe(['Newer', 'Older']);
});

test('invalid filter values are ignored', function () {
    Property::factory()->create(['occupancy_status' => 'rented_out']);
    Property::factory()->create(['occupancy_status' => 'self_use']);

    $properties = $this->get(route('properties.index', [
        'occupancy' => 'not-a-thing',
        'tenure' => 'made-up',
        'sort' => 'random-sql',
    ]))
        ->assertOk()
        ->viewData('properties');

    expect($properties->total())->toBe(2);
});

test('filters honour the owner scope', function () {
    $other = User::factory()->create();
    $this->actingAs($other);
    Property::factory()->create(['name' => 'Other user property', 'occupancy_status' => 'rented_out']);

    $this->actingAs($this->user);
    Property::factory()->create(['name' => 'Mine', 'occupancy_status' => 'rented_out']);

    $properties = $this->get(route('properties.index', ['occupancy' => 'rented_out']))
        ->assertOk()
        ->viewData('properties');

    expect($properties->total())->toBe(1)
        ->and($properties->first()->name)->toBe('Mine');
});

test('filters are passed back to the view', function () {
    Property::factory()->create();

    $filters = $this->get(route('properties.index', [
        'occupancy' => 'rented_out',
        'sort' => 'value_desc',
        'has' => 'documents',
    ]))
        ->assertOk()
        ->viewData('filters');

    expect($filters)->toBe([
        'occupancy' => 'rented_out',
        'tenure' => null,
        'has' => 'documents',
        'sort' => 'value_desc',
    ]);
});
