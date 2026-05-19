<?php

use App\Models\User;

test('security headers are present on authenticated pages', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertOk();
    $response->assertHeader('X-Frame-Options', 'DENY');
    $response->assertHeader('X-Content-Type-Options', 'nosniff');
    $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    $response->assertHeader('Cross-Origin-Opener-Policy', 'same-origin');
    $response->assertHeader('Cross-Origin-Resource-Policy', 'same-site');

    expect($response->headers->get('Permissions-Policy'))
        ->toContain('camera=()')
        ->toContain('microphone=()')
        ->toContain('geolocation=()');
});

test('security headers are present on the login page', function () {
    $response = $this->get('/login');

    $response->assertOk();
    $response->assertHeader('X-Frame-Options', 'DENY');
    $response->assertHeader('X-Content-Type-Options', 'nosniff');
});

test('content security policy is strict and forbids inline scripts', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/dashboard');

    $csp = $response->headers->get('Content-Security-Policy');

    expect($csp)
        ->toContain("default-src 'self'")
        ->toContain("script-src 'self'")
        ->toContain("frame-ancestors 'none'")
        ->toContain("object-src 'none'")
        ->toContain("base-uri 'self'")
        ->toContain("form-action 'self'")
        ->not->toContain("script-src 'self' 'unsafe-inline'")
        ->not->toContain("'unsafe-eval'");
});

test('hsts is only sent in production', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/dashboard');

    expect($response->headers->has('Strict-Transport-Security'))->toBeFalse();
});
