<?php

use App\Models\Property;
use App\Models\PropertyPhoto;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
});

test('a user can upload a photo to a property', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $property = Property::factory()->create();

    $file = UploadedFile::fake()->image('front.jpg', 800, 600);

    $this->post(route('properties.photos.store', $property), ['file' => $file])
        ->assertRedirect(route('properties.show', $property))
        ->assertSessionHas('status', 'photo-uploaded');

    $photo = PropertyPhoto::firstWhere('property_id', $property->id);

    expect($photo)->not->toBeNull()
        ->and($photo->original_name)->toBe('front.jpg')
        ->and($photo->mime_type)->toBe('image/jpeg')
        ->and($photo->is_primary)->toBeTrue()
        ->and($photo->created_by)->toBe($user->id);

    Storage::disk('local')->assertExists($photo->file_path);
});

test('the first uploaded photo becomes the primary thumbnail automatically', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $property = Property::factory()->create();

    $this->post(route('properties.photos.store', $property), [
        'file' => UploadedFile::fake()->image('one.jpg'),
    ]);

    $this->post(route('properties.photos.store', $property), [
        'file' => UploadedFile::fake()->image('two.jpg'),
    ]);

    $primaries = PropertyPhoto::where('property_id', $property->id)->where('is_primary', true)->get();

    expect($primaries)->toHaveCount(1)
        ->and($primaries->first()->original_name)->toBe('one.jpg');
});

test('uploading with is_primary takes over the primary slot', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $property = Property::factory()->create();

    $this->post(route('properties.photos.store', $property), [
        'file' => UploadedFile::fake()->image('one.jpg'),
    ]);

    $this->post(route('properties.photos.store', $property), [
        'file' => UploadedFile::fake()->image('two.jpg'),
        'is_primary' => 1,
    ]);

    $primaries = PropertyPhoto::where('property_id', $property->id)->where('is_primary', true)->get();

    expect($primaries)->toHaveCount(1)
        ->and($primaries->first()->original_name)->toBe('two.jpg');
});

test('makePrimary flips the chosen photo and clears every other', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $property = Property::factory()->create();

    $this->post(route('properties.photos.store', $property), [
        'file' => UploadedFile::fake()->image('one.jpg'),
    ]);
    $this->post(route('properties.photos.store', $property), [
        'file' => UploadedFile::fake()->image('two.jpg'),
    ]);

    $two = PropertyPhoto::where('property_id', $property->id)
        ->where('original_name', 'two.jpg')
        ->firstOrFail();

    $this->patch(route('properties.photos.makePrimary', [$property, $two]))
        ->assertRedirect(route('properties.show', $property))
        ->assertSessionHas('status', 'photo-primary-set');

    $primaries = PropertyPhoto::where('property_id', $property->id)->where('is_primary', true)->get();

    expect($primaries)->toHaveCount(1)
        ->and($primaries->first()->id)->toBe($two->id);
});

test('deleting the primary photo promotes the next one to primary', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $property = Property::factory()->create();

    $this->post(route('properties.photos.store', $property), [
        'file' => UploadedFile::fake()->image('first.jpg'),
    ]);
    $this->post(route('properties.photos.store', $property), [
        'file' => UploadedFile::fake()->image('second.jpg'),
    ]);

    $primary = PropertyPhoto::where('property_id', $property->id)
        ->where('is_primary', true)
        ->firstOrFail();

    $this->delete(route('properties.photos.destroy', [$property, $primary]))
        ->assertRedirect(route('properties.show', $property))
        ->assertSessionHas('status', 'photo-deleted');

    Storage::disk('local')->assertMissing($primary->file_path);

    $remaining = PropertyPhoto::where('property_id', $property->id)->get();

    expect($remaining)->toHaveCount(1)
        ->and($remaining->first()->original_name)->toBe('second.jpg')
        ->and($remaining->first()->is_primary)->toBeTrue();
});

test('deleting the only photo leaves no primary behind', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $property = Property::factory()->create();

    $this->post(route('properties.photos.store', $property), [
        'file' => UploadedFile::fake()->image('solo.jpg'),
    ]);

    $photo = PropertyPhoto::firstWhere('property_id', $property->id);

    $this->delete(route('properties.photos.destroy', [$property, $photo]))
        ->assertRedirect(route('properties.show', $property));

    expect(PropertyPhoto::where('property_id', $property->id)->exists())->toBeFalse();
});

test('a user can stream a photo they own', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $property = Property::factory()->create();

    $this->post(route('properties.photos.store', $property), [
        'file' => UploadedFile::fake()->image('front.jpg'),
    ]);

    $photo = PropertyPhoto::firstWhere('property_id', $property->id);

    $response = $this->get(route('properties.photos.show', [$property, $photo]));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('image/jpeg');
});

test('validation rejects non-image uploads', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $property = Property::factory()->create();

    $this->from(route('properties.show', $property))
        ->post(route('properties.photos.store', $property), [
            'file' => UploadedFile::fake()->create('not-an-image.pdf', 50, 'application/pdf'),
        ])
        ->assertSessionHasErrors('file');
});

test('validation rejects files over the size limit', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $property = Property::factory()->create();

    $this->from(route('properties.show', $property))
        ->post(route('properties.photos.store', $property), [
            'file' => UploadedFile::fake()->create('huge.jpg', 6000, 'image/jpeg'),
        ])
        ->assertSessionHasErrors('file');
});

test("a user cannot upload to, view, or delete another user's property photos", function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();

    $this->actingAs($alice);
    $aliceProperty = Property::factory()->create();
    $this->post(route('properties.photos.store', $aliceProperty), [
        'file' => UploadedFile::fake()->image('alice.jpg'),
    ]);
    $alicePhoto = PropertyPhoto::firstWhere('property_id', $aliceProperty->id);

    $this->actingAs($bob);

    $this->post(route('properties.photos.store', $aliceProperty), [
        'file' => UploadedFile::fake()->image('hijack.jpg'),
    ])->assertNotFound();

    $this->get(route('properties.photos.show', [$aliceProperty, $alicePhoto]))
        ->assertNotFound();

    $this->patch(route('properties.photos.makePrimary', [$aliceProperty, $alicePhoto]))
        ->assertNotFound();

    $this->delete(route('properties.photos.destroy', [$aliceProperty, $alicePhoto]))
        ->assertNotFound();

    expect(PropertyPhoto::find($alicePhoto->id))->not->toBeNull();
});
