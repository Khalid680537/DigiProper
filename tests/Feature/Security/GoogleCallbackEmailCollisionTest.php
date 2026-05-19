<?php

use App\Models\OauthAccount;
use App\Models\User;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

function fakeGoogleCollisionUser(): SocialiteUser
{
    $user = new SocialiteUser;
    $user->id = 'google-sub-attacker';
    $user->name = 'Attacker';
    $user->email = 'victim@example.com';
    $user->avatar = 'https://example.test/x.png';
    $user->token = 'attacker-token';
    $user->refreshToken = 'attacker-refresh';
    $user->expiresIn = 3600;

    return $user;
}

test('callback refuses to silently link a returning Google identity to an existing email-only user', function () {
    $victim = User::factory()->create([
        'email' => 'victim@example.com',
        'name' => 'Victim',
    ]);

    $provider = Mockery::mock(Provider::class);
    $provider->shouldReceive('user')->once()->andReturn(fakeGoogleCollisionUser());
    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $response = $this->get('/auth/google/callback');

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors('google');
    $this->assertGuest();

    expect(User::count())->toBe(1);
    expect(OauthAccount::count())->toBe(0);
    expect($victim->fresh()->name)->toBe('Victim');
});
