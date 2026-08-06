<?php

use App\Models\User;

/**
 * This app replaced the Breeze scaffold with a custom AuthController:
 * there is no /dashboard route, and a successful login lands on the rental
 * homepage (or /admin for administrators).
 */

test('login screen can be rendered', function () {
    $this->get('/login')->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    // AuthController takes a single 'identity' field (email or phone),
    // not Breeze's 'email'.
    $response = $this->post('/login', [
        'identity' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('rental.home', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'identity' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect(route('login', absolute: false));
});
