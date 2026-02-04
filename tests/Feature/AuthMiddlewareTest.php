<?php

use App\Models\Manga;
use App\Models\User;

it('redirects guests to onboarding from home', function () {
    $response = $this->get(route('home'));

    $response->assertRedirect(route('onboarding'));
});

it('redirects guests to onboarding from library', function () {
    $response = $this->get(route('library'));

    $response->assertRedirect(route('onboarding'));
});

it('redirects guests to onboarding from search', function () {
    $response = $this->get(route('search'));

    $response->assertRedirect(route('onboarding'));
});

it('redirects guests to onboarding from me', function () {
    $response = $this->get(route('me'));

    $response->assertRedirect(route('onboarding'));
});

it('redirects guests to onboarding from manga detail', function () {
    $response = $this->get(route('manga.show', ['id' => 1]));

    $response->assertRedirect(route('onboarding'));
});

it('allows authenticated users to access home', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertOk();
});

it('allows authenticated users to access library', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('library'));

    $response->assertOk();
});

it('allows authenticated users to access search', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('search'));

    $response->assertOk();
});

it('allows authenticated users to access me', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('me'));

    $response->assertOk();
});

it('allows authenticated users to access manga detail', function () {
    $user = User::factory()->create();
    $manga = Manga::factory()->create();

    $response = $this->actingAs($user)->get(route('manga.show', ['id' => $manga->id]));

    $response->assertOk();
});
