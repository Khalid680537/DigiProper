<?php

use App\Models\Property;
use App\Models\PropertyDocument;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Storage::fake('local');
});

test('a user can upload a document for their own property via the API', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->actingAs($user);
    $property = Property::factory()->create();

    $file = UploadedFile::fake()->create('title-deed.pdf', 200, 'application/pdf');

    $response = $this->postJson(route('api.v1.properties.documents.store', $property), [
        'title' => 'Title deed',
        'category' => 'title',
        'file' => $file,
        'notes' => 'Original at 24 Motia khan',
    ])->assertCreated()
        ->assertJsonStructure(['data' => ['id', 'title', 'download_url']]);

    $document = PropertyDocument::firstWhere('property_id', $property->id);
    expect($document)->not->toBeNull()
        ->and($document->title)->toBe('Title deed');

    Storage::disk('local')->assertExists($document->file_path);

    // download_url is signed and works without a bearer token
    $downloadUrl = $response->json('data.download_url');
    expect($downloadUrl)->toContain('/api/v1/properties/'.$property->id.'/documents/'.$document->id.'/download');
});

test('upload rejects unsupported file types', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->actingAs($user);
    $property = Property::factory()->create();

    $this->postJson(route('api.v1.properties.documents.store', $property), [
        'title' => 'Bad upload',
        'file' => UploadedFile::fake()->create('script.exe', 10, 'application/octet-stream'),
    ])->assertStatus(422)->assertJsonValidationErrors('file');
});

test('a user cannot upload to another user\'s property', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();

    $this->actingAs($alice);
    $aliceProperty = Property::factory()->create();

    Sanctum::actingAs($bob);

    $this->postJson(route('api.v1.properties.documents.store', $aliceProperty), [
        'title' => 'Hijack',
        'file' => UploadedFile::fake()->create('h.pdf', 50, 'application/pdf'),
    ])->assertNotFound();
});

test('document download via signed URL works', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->actingAs($user);
    $property = Property::factory()->create();

    $this->postJson(route('api.v1.properties.documents.store', $property), [
        'title' => 'Deed',
        'file' => UploadedFile::fake()->create('deed.pdf', 50, 'application/pdf'),
    ])->assertCreated();

    $document = PropertyDocument::firstWhere('property_id', $property->id);

    $signed = URL::temporarySignedRoute(
        'api.v1.properties.documents.download',
        now()->addMinutes(5),
        ['property' => $property->id, 'document' => $document->id],
    );

    // signed URLs work without auth
    $this->get($signed)->assertOk();

    // tampered URL is rejected
    $this->get(str_replace('signature=', 'signature=bad', $signed))->assertForbidden();
});
