<?php

use App\Models\Property;
use App\Models\PropertyDocument;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
});

test('a user can upload a document to a property', function () {
    $user = User::factory()->create();
    $property = Property::factory()->create();

    $file = UploadedFile::fake()->create('title-deed.pdf', 200, 'application/pdf');

    $this->actingAs($user)
        ->post(route('properties.documents.store', $property), [
            'title' => 'Title deed',
            'category' => 'title',
            'file' => $file,
            'notes' => 'Original at 24 Motia khan',
        ])
        ->assertRedirect(route('properties.show', $property))
        ->assertSessionHas('status', 'document-uploaded');

    $document = PropertyDocument::firstWhere('property_id', $property->id);

    expect($document)->not->toBeNull()
        ->and($document->title)->toBe('Title deed')
        ->and($document->category)->toBe('title')
        ->and($document->original_name)->toBe('title-deed.pdf')
        ->and($document->mime_type)->toBe('application/pdf')
        ->and($document->size_bytes)->toBeGreaterThan(0)
        ->and($document->created_by)->toBe($user->id);

    Storage::disk('local')->assertExists($document->file_path);
});

test('upload validation rejects unsupported file types', function () {
    $user = User::factory()->create();
    $property = Property::factory()->create();

    $this->actingAs($user)
        ->from(route('properties.show', $property))
        ->post(route('properties.documents.store', $property), [
            'title' => 'Bad upload',
            'file' => UploadedFile::fake()->create('script.exe', 10, 'application/octet-stream'),
        ])
        ->assertSessionHasErrors('file');
});

test('a user can download an uploaded document', function () {
    $user = User::factory()->create();
    $property = Property::factory()->create();

    $this->actingAs($user)->post(route('properties.documents.store', $property), [
        'title' => 'Lease agreement',
        'category' => 'lease',
        'file' => UploadedFile::fake()->create('lease.pdf', 100, 'application/pdf'),
    ]);

    $document = PropertyDocument::firstWhere('property_id', $property->id);

    $response = $this->actingAs($user)->get(route('properties.documents.show', [$property, $document]));

    $response->assertOk();
    expect($response->headers->get('content-disposition'))->toContain('lease.pdf');
});

test('downloading a document that belongs to another property returns 404', function () {
    $user = User::factory()->create();
    $propertyA = Property::factory()->create();
    $propertyB = Property::factory()->create();

    $this->actingAs($user)->post(route('properties.documents.store', $propertyA), [
        'title' => 'Lease',
        'file' => UploadedFile::fake()->create('a.pdf', 50, 'application/pdf'),
    ]);

    $document = PropertyDocument::firstWhere('property_id', $propertyA->id);

    $this->actingAs($user)
        ->get(route('properties.documents.show', [$propertyB, $document]))
        ->assertNotFound();
});

test('a user can soft-delete an uploaded document', function () {
    $user = User::factory()->create();
    $property = Property::factory()->create();

    $this->actingAs($user)->post(route('properties.documents.store', $property), [
        'title' => 'Receipt',
        'file' => UploadedFile::fake()->create('receipt.pdf', 30, 'application/pdf'),
    ]);

    $document = PropertyDocument::firstWhere('property_id', $property->id);

    $this->actingAs($user)
        ->delete(route('properties.documents.destroy', [$property, $document]))
        ->assertRedirect(route('properties.show', $property))
        ->assertSessionHas('status', 'document-deleted');

    expect(PropertyDocument::find($document->id))->toBeNull()
        ->and(PropertyDocument::withTrashed()->find($document->id)->deleted_by)->toBe($user->id);
});
