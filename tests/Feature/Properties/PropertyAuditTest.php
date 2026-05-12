<?php

use App\Models\Property;
use App\Models\PropertyDocument;
use App\Models\User;

test('creating a property as an authenticated user fills created_by', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $property = Property::factory()->create();

    expect($property->created_by)->toBe($user->id)
        ->and($property->updated_by)->toBe($user->id)
        ->and($property->deleted_by)->toBeNull();
});

test('updating a property as a different user updates updated_by but preserves created_by', function () {
    $creator = User::factory()->create();
    $editor = User::factory()->create();

    $this->actingAs($creator);
    $property = Property::factory()->create();

    $this->actingAs($editor);
    $property->name = 'Renamed Property';
    $property->save();

    expect($property->fresh()->created_by)->toBe($creator->id)
        ->and($property->fresh()->updated_by)->toBe($editor->id)
        ->and($property->fresh()->deleted_by)->toBeNull();
});

test('soft deleting a property sets deleted_by and deleted_at together', function () {
    $creator = User::factory()->create();
    $deleter = User::factory()->create();

    $this->actingAs($creator);
    $property = Property::factory()->create();

    $this->actingAs($deleter);
    $property->delete();

    // Bypass the per-user global scope since the deleter isn't the owner —
    // the test is explicitly verifying cross-user audit attribution.
    $trashed = Property::withoutGlobalScope('owner')->withTrashed()->find($property->id);

    expect($trashed)->not->toBeNull()
        ->and($trashed->deleted_by)->toBe($deleter->id)
        ->and($trashed->deleted_at)->not->toBeNull()
        ->and($trashed->created_by)->toBe($creator->id);
});

test('hard delete of property cascades to property_documents but soft delete does not', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $property = Property::factory()->create();
    $document = PropertyDocument::factory()->create(['property_id' => $property->id]);

    $property->delete();
    expect(PropertyDocument::find($document->id))->not->toBeNull();

    $property->forceDelete();
    expect(PropertyDocument::find($document->id))->toBeNull();
});

test('creating a property without an authenticated user leaves audit columns null', function () {
    $property = Property::factory()->create();

    expect($property->created_by)->toBeNull()
        ->and($property->updated_by)->toBeNull()
        ->and($property->deleted_by)->toBeNull();
});
