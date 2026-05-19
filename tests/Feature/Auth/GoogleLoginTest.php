<?php

use App\Models\OauthAccount;
use App\Models\User;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as SocialiteUser;

function fakeGoogleUser(array $overrides = []): SocialiteUser
{
    $user = new SocialiteUser;
    $user->id = $overrides['id'] ?? 'google-sub-123';
    $user->name = $overrides['name'] ?? 'Ada Lovelace';
    $user->email = $overrides['email'] ?? 'ada@example.com';
    $user->avatar = $overrides['avatar'] ?? 'https://example.test/ada.png';
    $user->token = $overrides['token'] ?? 'access-token-abc';
    $user->refreshToken = $overrides['refreshToken'] ?? 'refresh-token-xyz';
    $user->expiresIn = $overrides['expiresIn'] ?? 3600;

    return $user;
}

test('login screen can be rendered', function () {
    $this->get('/login')
        ->assertOk()
        ->assertSee('Continue with Google');
});

test('redirect route sends user to google', function () {
    $response = $this->get('/auth/google/redirect');

    $response->assertRedirect();
    expect($response->headers->get('Location'))->toContain('accounts.google.com');
});

test('callback creates a new user and oauth account', function () {
    $provider = Mockery::mock(Provider::class);
    $provider->shouldReceive('user')->once()->andReturn(fakeGoogleUser());
    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $response = $this->get('/auth/google/callback');

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticated();

    $user = User::firstWhere('email', 'ada@example.com');
    expect($user)->not->toBeNull();
    expect($user->name)->toBe('Ada Lovelace');
    expect($user->avatar_url)->toBe('https://example.test/ada.png');
    expect($user->email_verified_at)->not->toBeNull();

    $oauth = OauthAccount::where('provider', 'google')
        ->where('provider_user_id', 'google-sub-123')
        ->firstOrFail();
    expect($oauth->user_id)->toBe($user->id);
    expect($oauth->access_token)->toBe('access-token-abc');
    expect($oauth->refresh_token)->toBe('refresh-token-xyz');
    expect($oauth->token_expires_at)->not->toBeNull();
});

test('callback reuses existing oauth account and refreshes tokens', function () {
    $existing = User::factory()->create(['email' => 'ada@example.com']);
    OauthAccount::create([
        'user_id' => $existing->id,
        'provider' => 'google',
        'provider_user_id' => 'google-sub-123',
        'email' => 'ada@example.com',
        'access_token' => 'old-token',
        'refresh_token' => 'old-refresh',
    ]);

    $provider = Mockery::mock(Provider::class);
    $provider->shouldReceive('user')->once()->andReturn(fakeGoogleUser([
        'token' => 'new-token',
        'refreshToken' => 'new-refresh',
    ]));
    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $this->get('/auth/google/callback')
        ->assertRedirect(route('dashboard', absolute: false));

    expect(User::count())->toBe(1);
    expect(OauthAccount::count())->toBe(1);

    $oauth = OauthAccount::first();
    expect($oauth->access_token)->toBe('new-token');
    expect($oauth->refresh_token)->toBe('new-refresh');
    $this->assertAuthenticatedAs($existing);
});

test('callback refuses to merge into an existing email-only account', function () {
    $existing = User::factory()->create(['email' => 'ada@example.com', 'name' => 'Existing']);

    $provider = Mockery::mock(Provider::class);
    $provider->shouldReceive('user')->once()->andReturn(fakeGoogleUser());
    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $response = $this->get('/auth/google/callback');

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors('google');
    $this->assertGuest();

    expect(User::count())->toBe(1);
    expect(OauthAccount::count())->toBe(0);
    expect($existing->fresh()->name)->toBe('Existing');
});

test('invalid state redirects to login with error', function () {
    $provider = Mockery::mock(Provider::class);
    $provider->shouldReceive('user')->once()->andThrow(new InvalidStateException);
    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $response = $this->get('/auth/google/callback');

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors('google');
    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
