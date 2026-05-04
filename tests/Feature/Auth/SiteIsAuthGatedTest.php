<?php

use App\Models\User;

test('guest visiting root is redirected to login', function () {
    $this->get('/')->assertRedirect(route('login'));
});

test('guest visiting dashboard is redirected to login', function () {
    $this->get('/dashboard')->assertRedirect(route('login'));
});

test('guest visiting profile is redirected to login', function () {
    $this->get('/profile')->assertRedirect(route('login'));
});

test('authenticated user visiting root is redirected to dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/')->assertRedirect(route('dashboard'));
});

test('authenticated user can reach dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/dashboard')->assertOk();
});

test('authenticated user visiting login is redirected to dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/login')->assertRedirect(route('dashboard'));
});
