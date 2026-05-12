<?php

use App\Models\User;

test('a user can exchange credentials for a personal access token', function () {
    $user = User::factory()->create([
        'email' => 'gyan@abctransport.co.in',
        'password' => 'secret-password',
    ]);

    $response = $this->postJson(route('api.v1.login'), [
        'email' => 'gyan@abctransport.co.in',
        'password' => 'secret-password',
        'device_name' => 'pest-test',
    ]);

    $response->assertCreated()
        ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email']])
        ->assertJsonPath('user.id', $user->id);

    expect($user->fresh()->tokens()->count())->toBe(1);
});

test('login with bad credentials returns 422', function () {
    User::factory()->create([
        'email' => 'gyan@abctransport.co.in',
        'password' => 'secret-password',
    ]);

    $this->postJson(route('api.v1.login'), [
        'email' => 'gyan@abctransport.co.in',
        'password' => 'wrong-password',
    ])->assertStatus(422)->assertJsonValidationErrors('email');
});

test('login is rate-limited after 5 attempts per minute', function () {
    User::factory()->create([
        'email' => 'gyan@abctransport.co.in',
        'password' => 'secret-password',
    ]);

    foreach (range(1, 5) as $_) {
        $this->postJson(route('api.v1.login'), [
            'email' => 'gyan@abctransport.co.in',
            'password' => 'wrong-password',
        ])->assertStatus(422);
    }

    $this->postJson(route('api.v1.login'), [
        'email' => 'gyan@abctransport.co.in',
        'password' => 'wrong-password',
    ])->assertStatus(429);
});

test('protected endpoints require a sanctum token', function () {
    $this->getJson(route('api.v1.me'))->assertUnauthorized();
});

test('me returns the authenticated user', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson(route('api.v1.me'))
        ->assertOk()
        ->assertJsonPath('data.id', $user->id);
});

test('logout revokes the current token', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson(route('api.v1.logout'))
        ->assertOk();

    expect($user->fresh()->tokens()->count())->toBe(0);
});
