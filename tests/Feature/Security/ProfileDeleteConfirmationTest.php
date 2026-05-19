<?php

use App\Models\User;
use Illuminate\Support\Facades\Log;

test('profile destroy requires a typed confirmation', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete('/profile');

    $response->assertSessionHasErrors('confirmation');
    $this->assertAuthenticated();
    expect($user->fresh())->not->toBeNull();
});

test('profile destroy rejects wrong confirmation text', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->delete('/profile', ['confirmation' => 'delete']);

    $response->assertSessionHasErrors('confirmation');
    $this->assertAuthenticated();
    expect($user->fresh())->not->toBeNull();
});

test('profile destroy succeeds with confirmation and writes audit log', function () {
    Log::spy();

    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->delete('/profile', ['confirmation' => 'DELETE']);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect('/');
    $this->assertGuest();
    expect($user->fresh())->toBeNull();

    Log::shouldHaveReceived('warning')
        ->once()
        ->with('account_deletion', Mockery::on(fn ($ctx) => ($ctx['user_id'] ?? null) === $user->id
            && ($ctx['email'] ?? null) === $user->email));
});
