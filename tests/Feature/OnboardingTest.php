<?php

use App\Models\User;

it('shows onboarding for guests', function () {
    $this->get('/')->assertOk();
});

it('skips onboarding when a user already exists', function () {
    User::factory()->create();

    $this->get('/')->assertRedirect(route('home'));
});

it('creates the first user from onboarding', function () {
    $payload = ['name' => 'Nova Reader'];

    $response = $this->post(route('onboarding.store'), $payload);

    $response->assertRedirect(route('home'));
    expect(User::query()->where('name', $payload['name'])->exists())->toBeTrue();
});
