<?php

use App\Models\Property;
use App\Models\User;

test('the owner can rotate their property\'s share token', function () {
    $owner = User::factory()->create();
    $this->actingAs($owner);
    $property = Property::factory()->create();
    $oldToken = $property->share_token;

    $this->post(route('properties.share.rotate', $property))
        ->assertRedirect(route('properties.show', $property));

    expect($property->fresh()->share_token)->not->toBe($oldToken)->toHaveLength(32);
});

test('the owner can disable sharing then re-enable it with a fresh token', function () {
    $owner = User::factory()->create();
    $this->actingAs($owner);
    $property = Property::factory()->create();
    $oldToken = $property->share_token;

    $this->delete(route('properties.share.disable', $property))
        ->assertRedirect(route('properties.show', $property));

    expect($property->fresh()->share_token)->toBeNull();

    $this->post(route('properties.share.enable', $property))
        ->assertRedirect(route('properties.show', $property));

    $reenabled = $property->fresh();
    expect($reenabled->share_token)->toBeString()->toHaveLength(32);
    expect($reenabled->share_token)->not->toBe($oldToken);
});

test('updating share visibility persists the booleans', function () {
    $owner = User::factory()->create();
    $this->actingAs($owner);
    $property = Property::factory()->create();

    $this->patch(route('properties.share.visibility', $property), [
        'share_financials' => '1',
        'share_contacts' => '1',
    ])->assertRedirect(route('properties.show', $property));

    $fresh = $property->fresh();
    expect($fresh->share_financials)->toBeTrue();
    expect($fresh->share_contacts)->toBeTrue();
    expect($fresh->share_keys_location)->toBeFalse();
    expect($fresh->share_extra_notes)->toBeFalse();
    expect($fresh->share_title_holder)->toBeFalse();
});

test('a user cannot rotate or disable another user\'s share', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();

    $this->actingAs($alice);
    $aliceProperty = Property::factory()->create();
    $originalToken = $aliceProperty->share_token;

    $this->actingAs($bob);

    $this->post(route('properties.share.rotate', $aliceProperty))->assertNotFound();
    $this->delete(route('properties.share.disable', $aliceProperty))->assertNotFound();
    $this->patch(route('properties.share.visibility', $aliceProperty), [
        'share_financials' => '1',
    ])->assertNotFound();

    $fresh = $aliceProperty->fresh();
    expect($fresh->share_token)->toBe($originalToken);
    expect($fresh->share_financials)->toBeFalse();
});

test('the share modal flash messages render on the property show page', function () {
    $owner = User::factory()->create();
    $this->actingAs($owner);
    $property = Property::factory()->create();

    $this->post(route('properties.share.rotate', $property))->assertRedirect();

    $this->followingRedirects()
        ->post(route('properties.share.rotate', $property))
        ->assertOk()
        ->assertSee('Share link rotated');
});

test('guests are bounced from owner-only share endpoints', function () {
    $owner = User::factory()->create();
    $this->actingAs($owner);
    $property = Property::factory()->create();
    auth()->logout();

    $this->post(route('properties.share.rotate', $property))->assertRedirect(route('login'));
    $this->delete(route('properties.share.disable', $property))->assertRedirect(route('login'));
    $this->patch(route('properties.share.visibility', $property), [])->assertRedirect(route('login'));
});
