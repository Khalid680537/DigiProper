<?php

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('search:'.User::query()->max('id'));
});

test('search endpoint allows up to 30 requests per minute, then 429s', function () {
    $user = User::factory()->create();
    RateLimiter::clear('search:'.$user->id);

    for ($i = 0; $i < 30; $i++) {
        $response = $this->actingAs($user)
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->get('/search?q=test');

        $response->assertOk();
    }

    $response = $this->actingAs($user)
        ->withHeader('X-Requested-With', 'XMLHttpRequest')
        ->get('/search?q=test');

    $response->assertStatus(429);
});
