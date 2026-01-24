<?php

use App\Models\User;

it('shows onboarding for guests', function () {
    $this->get('/')->assertOk();
});

it('skips onboarding for authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/')->assertRedirect(route('home'));
});

it('creates a user and logs in from onboarding', function () {
    $payload = ['name' => 'Nova Reader'];

    $response = $this->post(route('onboarding.store'), $payload);

    $response->assertRedirect(route('home'));
    $this->assertAuthenticated();
    expect(User::query()->where('name', $payload['name'])->exists())->toBeTrue();
});
