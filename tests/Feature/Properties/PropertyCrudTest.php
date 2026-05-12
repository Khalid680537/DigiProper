<?php

use App\Models\Property;
use App\Models\User;

test('unauthenticated users are redirected from the properties index', function () {
    $this->get(route('properties.index'))->assertRedirect(route('login'));
});

test('the properties index renders and shows existing properties', function () {
    $user = User::factory()->create();
    Property::factory()->create(['name' => 'SGTN 1', 'city' => 'New Delhi']);

    $this->actingAs($user)
        ->get(route('properties.index'))
        ->assertOk()
        ->assertSeeText('SGTN 1')
        ->assertSeeText('New Delhi');
});

test('the create form renders', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('properties.create'))
        ->assertOk()
        ->assertSee('Add a new property');
});

test('storing a property persists it and redirects to show', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('properties.store'), [
        'name' => 'Kashmiri Gate A',
        'title_holder' => 'SSC',
        'address_line1' => '1413 A, Gurnanak Charitable Trust Market',
        'city' => 'New Delhi',
        'state' => 'DL',
        'pincode' => '110006',
        'tenure' => 'pagri',
        'occupancy_status' => 'rented_out',
        'imputed_value_inr' => 150000,
        'rent_yearly_inr' => 70000,
        'contacts' => [
            ['name' => 'ABC Malhotra', 'phone' => '+919999999999', 'role' => 'Field Officer'],
        ],
        'is_data_complete' => '1',
    ]);

    $property = Property::firstWhere('name', 'Kashmiri Gate A');

    expect($property)->not->toBeNull()
        ->and($property->pincode)->toBe('110006')
        ->and($property->tenure)->toBe('pagri')
        ->and($property->is_data_complete)->toBeTrue()
        ->and($property->contacts[0]['name'])->toBe('ABC Malhotra')
        ->and($property->created_by)->toBe($user->id);

    $response->assertRedirect(route('properties.show', $property))
        ->assertSessionHas('status', 'property-created');
});

test('store validation rejects missing name', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('properties.create'))
        ->post(route('properties.store'), ['name' => ''])
        ->assertRedirect(route('properties.create'))
        ->assertSessionHasErrors('name');
});

test('the show page renders the property summary', function () {
    $user = User::factory()->create();
    $property = Property::factory()->create([
        'name' => 'SGTN 2',
        'imputed_value_inr' => 15000000,
    ]);

    $this->actingAs($user)
        ->get(route('properties.show', $property))
        ->assertOk()
        ->assertSeeText('SGTN 2')
        ->assertSee('Financials');
});

test('the edit form prefills existing data', function () {
    $user = User::factory()->create();
    $property = Property::factory()->create(['name' => 'Original Name']);

    $this->actingAs($user)
        ->get(route('properties.edit', $property))
        ->assertOk()
        ->assertSee('value="Original Name"', false);
});

test('updating a property persists changes and redirects', function () {
    $user = User::factory()->create();
    $property = Property::factory()->create(['name' => 'Old Name']);

    $this->actingAs($user)
        ->patch(route('properties.update', $property), ['name' => 'New Name'])
        ->assertRedirect(route('properties.show', $property))
        ->assertSessionHas('status', 'property-updated');

    expect($property->fresh()->name)->toBe('New Name')
        ->and($property->fresh()->updated_by)->toBe($user->id);
});

test('destroying a property soft-deletes it', function () {
    $user = User::factory()->create();
    $property = Property::factory()->create();

    $this->actingAs($user)
        ->delete(route('properties.destroy', $property))
        ->assertRedirect(route('properties.index'))
        ->assertSessionHas('status', 'property-deleted');

    expect(Property::find($property->id))->toBeNull()
        ->and(Property::withTrashed()->find($property->id)->deleted_by)->toBe($user->id);
});
