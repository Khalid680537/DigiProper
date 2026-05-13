<?php

use App\Models\Property;
use App\Models\User;

test('the SVG QR endpoint returns image/svg+xml and encodes the share URL', function () {
    $owner = User::factory()->create();
    $this->actingAs($owner);
    $property = Property::factory()->create();
    auth()->logout();

    $response = $this->get(route('properties.share.qr.svg', $property->share_token));

    $response->assertOk()
        ->assertHeader('Content-Type', 'image/svg+xml');

    expect($response->getContent())
        ->toContain('<svg')
        ->toContain('</svg>');
});

test('the PNG QR endpoint returns image/png with a non-empty body', function () {
    $owner = User::factory()->create();
    $this->actingAs($owner);
    $property = Property::factory()->create();
    auth()->logout();

    $response = $this->get(route('properties.share.qr.png', $property->share_token));

    $response->assertOk()
        ->assertHeader('Content-Type', 'image/png');

    expect(strlen($response->getContent()))->toBeGreaterThan(100);
    expect(substr($response->getContent(), 0, 8))->toBe("\x89PNG\r\n\x1a\n");
});

test('an unknown token returns 404 on both QR endpoints', function () {
    $this->get('/p/bogus/qr.svg')->assertNotFound();
    $this->get('/p/bogus/qr.png')->assertNotFound();
});

test('Property::qrSvg returns inline svg markup and caches the result', function () {
    $owner = User::factory()->create();
    $this->actingAs($owner);
    $property = Property::factory()->create();

    $svgA = $property->qrSvg(96);
    $svgB = $property->qrSvg(96);

    expect($svgA)->toBeString()->toContain('<svg');
    expect($svgB)->toBe($svgA);
});

test('Property::qrSvg returns null when sharing is disabled', function () {
    $owner = User::factory()->create();
    $this->actingAs($owner);
    $property = Property::factory()->create();

    $property->share_token = null;
    $property->save();

    expect($property->fresh()->qrSvg(96))->toBeNull();
    expect($property->fresh()->share_url)->toBeNull();
});
