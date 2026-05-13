<?php

use App\Models\Property;
use App\Models\User;

test('anonymous visitor can view a shared property page with the conservative defaults', function () {
    $owner = User::factory()->create();
    $this->actingAs($owner);
    $property = Property::factory()->create([
        'name' => 'Lake Vista Apartment',
        'imputed_value_inr' => 12500000,
        'rent_yearly_inr' => 600000,
        'keys_location' => 'With the gate guard',
        'extra_notes' => 'Top-floor unit with balcony.',
        'title_holder' => 'Aarav Sharma',
    ]);
    auth()->logout();

    $response = $this->get(route('properties.share.show', $property->share_token));

    $response->assertOk()
        ->assertSee('Lake Vista Apartment')
        ->assertDontSee('Aarav Sharma')
        ->assertDontSee('With the gate guard')
        ->assertDontSee('Top-floor unit with balcony.');
});

test('financials are visible only when the owner toggles them on', function () {
    $owner = User::factory()->create();
    $this->actingAs($owner);
    $property = Property::factory()->create([
        'imputed_value_inr' => 12500000,
        'rent_yearly_inr' => 600000,
    ]);
    auth()->logout();

    $this->get(route('properties.share.show', $property->share_token))
        ->assertOk()
        ->assertDontSee('Imputed value');

    $property->update(['share_financials' => true]);

    $this->get(route('properties.share.show', $property->share_token))
        ->assertOk()
        ->assertSee('Imputed value')
        ->assertSee('Rent / year');
});

test('contacts are visible only when the owner toggles them on', function () {
    $owner = User::factory()->create();
    $this->actingAs($owner);
    $property = Property::factory()->create();
    $property->contacts()->create([
        'name' => 'Priya Caretaker',
        'phone' => '+919800000001',
        'role' => 'Caretaker',
        'position' => 0,
    ]);
    auth()->logout();

    $this->get(route('properties.share.show', $property->share_token))
        ->assertOk()
        ->assertDontSee('Priya Caretaker');

    $property->update(['share_contacts' => true]);

    $this->get(route('properties.share.show', $property->share_token))
        ->assertOk()
        ->assertSee('Priya Caretaker')
        ->assertSee('+919800000001');
});

test('keys location and extra notes only render when their toggles are on', function () {
    $owner = User::factory()->create();
    $this->actingAs($owner);
    $property = Property::factory()->create([
        'keys_location' => 'Locker at building reception',
        'extra_notes' => 'Boiler is in the utility cupboard.',
        'share_keys_location' => true,
        'share_extra_notes' => true,
    ]);
    auth()->logout();

    $this->get(route('properties.share.show', $property->share_token))
        ->assertOk()
        ->assertSee('Locker at building reception')
        ->assertSee('Boiler is in the utility cupboard.');
});

test('unknown token returns 404', function () {
    $this->get('/p/this-token-does-not-exist')->assertNotFound();
});

test('soft-deleted property returns 404 on its share URL', function () {
    $owner = User::factory()->create();
    $this->actingAs($owner);
    $property = Property::factory()->create();
    $token = $property->share_token;
    $property->delete();
    auth()->logout();

    $this->get(route('properties.share.show', $token))->assertNotFound();
});

test('rotating the share token invalidates the previous URL', function () {
    $owner = User::factory()->create();
    $this->actingAs($owner);
    $property = Property::factory()->create();
    $oldToken = $property->share_token;
    auth()->logout();

    $this->get(route('properties.share.show', $oldToken))->assertOk();

    $this->actingAs($owner)
        ->post(route('properties.share.rotate', $property))
        ->assertRedirect(route('properties.show', $property));

    $property->refresh();
    expect($property->share_token)->not->toBe($oldToken);

    auth()->logout();
    $this->get(route('properties.share.show', $oldToken))->assertNotFound();
    $this->get(route('properties.share.show', $property->share_token))->assertOk();
});

test('a disabled share returns 404 even for the right token at rest', function () {
    $owner = User::factory()->create();
    $this->actingAs($owner);
    $property = Property::factory()->create();
    $token = $property->share_token;

    $this->delete(route('properties.share.disable', $property))->assertRedirect();
    auth()->logout();

    $this->get(route('properties.share.show', $token))->assertNotFound();
});

test('every newly created property is auto-assigned a unique share token', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $a = Property::factory()->create();
    $b = Property::factory()->create();

    expect($a->share_token)->toBeString()->and(strlen($a->share_token))->toBe(32);
    expect($b->share_token)->toBeString();
    expect($a->share_token)->not->toBe($b->share_token);
});
