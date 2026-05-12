<?php

use App\Models\Property;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('listing properties returns the authenticated user\'s properties only', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();

    $this->actingAs($alice);
    Property::factory()->count(3)->create();

    $this->actingAs($bob);
    Property::factory()->create(['name' => 'Bob Property']);

    Sanctum::actingAs($alice);

    $response = $this->getJson(route('api.v1.properties.index'))
        ->assertOk()
        ->assertJsonStructure([
            'data' => [['id', 'name', 'address', 'financials']],
            'links',
            'meta' => ['current_page', 'last_page', 'per_page', 'total'],
        ]);

    expect($response->json('meta.total'))->toBe(3)
        ->and(collect($response->json('data'))->pluck('name'))
        ->not->toContain('Bob Property');
});

test('a property can be created via the API', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $payload = [
        'name' => 'Kashmiri Gate A',
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
        'is_data_complete' => true,
    ];

    $response = $this->postJson(route('api.v1.properties.store'), $payload)
        ->assertCreated()
        ->assertJsonPath('data.name', 'Kashmiri Gate A')
        ->assertJsonPath('data.address.pincode', '110006')
        ->assertJsonPath('data.contacts.0.name', 'ABC Malhotra')
        ->assertJsonPath('data.contacts.0.phone', '+919999999999');

    $property = Property::firstWhere('name', 'Kashmiri Gate A');
    expect($property->created_by)->toBe($user->id)
        ->and($property->contacts()->count())->toBe(1);
});

test('contact PII is stored encrypted at rest', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->postJson(route('api.v1.properties.store'), [
        'name' => 'Encrypted Property',
        'contacts' => [
            ['name' => 'Secret Person', 'phone' => '+919800000000', 'role' => 'Owner'],
        ],
    ])->assertCreated();

    $row = DB::table('property_contacts')->first();

    expect($row->name)->not->toContain('Secret Person')
        ->and($row->phone)->not->toContain('+919800000000')
        ->and(strlen($row->name))->toBeGreaterThan(20);
});

test('updating a property persists changes via the API', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->actingAs($user);
    $property = Property::factory()->create(['name' => 'Old Name']);

    $this->patchJson(route('api.v1.properties.update', $property), [
        'name' => 'New Name',
    ])->assertOk()
        ->assertJsonPath('data.name', 'New Name');

    expect($property->fresh()->name)->toBe('New Name');
});

test('a user cannot fetch or mutate another user\'s property', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();

    $this->actingAs($alice);
    $aliceProperty = Property::factory()->create();

    Sanctum::actingAs($bob);

    $this->getJson(route('api.v1.properties.show', $aliceProperty))->assertNotFound();
    $this->patchJson(route('api.v1.properties.update', $aliceProperty), ['name' => 'Hijack'])->assertNotFound();
    $this->deleteJson(route('api.v1.properties.destroy', $aliceProperty))->assertNotFound();

    expect($aliceProperty->fresh()->name)->not->toBe('Hijack');
});

test('deleting a property soft-deletes it', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->actingAs($user);
    $property = Property::factory()->create();

    $this->deleteJson(route('api.v1.properties.destroy', $property))
        ->assertNoContent();

    expect(Property::find($property->id))->toBeNull();
});

test('an unauthenticated request returns 401', function () {
    $this->getJson(route('api.v1.properties.index'))->assertUnauthorized();
});

test('store validation rejects missing name', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->postJson(route('api.v1.properties.store'), ['name' => ''])
        ->assertStatus(422)
        ->assertJsonValidationErrors('name');
});
