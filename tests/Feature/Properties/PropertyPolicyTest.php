<?php

use App\Models\Property;
use App\Models\PropertyDocument;
use App\Models\User;

test('a user can act on their own property but not on another user\'s', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();

    $this->actingAs($alice);
    $aliceProperty = Property::factory()->create();

    // The global scope hides Alice's property from Bob, so we bypass it
    // to exercise the Policy directly as defense-in-depth.
    $aliceFromBobView = Property::withoutGlobalScope('owner')->find($aliceProperty->id);

    expect($alice->can('view', $aliceFromBobView))->toBeTrue()
        ->and($alice->can('update', $aliceFromBobView))->toBeTrue()
        ->and($alice->can('delete', $aliceFromBobView))->toBeTrue();

    expect($bob->can('view', $aliceFromBobView))->toBeFalse()
        ->and($bob->can('update', $aliceFromBobView))->toBeFalse()
        ->and($bob->can('delete', $aliceFromBobView))->toBeFalse();
});

test('any authenticated user can create a property', function () {
    $user = User::factory()->create();

    expect($user->can('create', Property::class))->toBeTrue();
});

test('a user can act on documents belonging to their own property only', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();

    $this->actingAs($alice);
    $aliceProperty = Property::factory()->create();
    $aliceDocument = PropertyDocument::factory()->for($aliceProperty)->create();

    expect($alice->can('view', $aliceDocument))->toBeTrue()
        ->and($alice->can('update', $aliceDocument))->toBeTrue()
        ->and($alice->can('delete', $aliceDocument))->toBeTrue()
        ->and($alice->can('create', [PropertyDocument::class, $aliceProperty]))->toBeTrue();

    expect($bob->can('view', $aliceDocument))->toBeFalse()
        ->and($bob->can('update', $aliceDocument))->toBeFalse()
        ->and($bob->can('delete', $aliceDocument))->toBeFalse()
        ->and($bob->can('create', [PropertyDocument::class, $aliceProperty]))->toBeFalse();
});
