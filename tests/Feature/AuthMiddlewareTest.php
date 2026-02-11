<?php

use App\Models\Manga;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

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
    $response = $this->get(route('manga.show', ['id' => 'naruto']));

    $response->assertRedirect(route('onboarding'));
});

it('redirects guests to onboarding from manga reader', function () {
    $response = $this->get(route('manga.read', ['id' => 'naruto', 'chapterId' => '1001']));

    $response->assertRedirect(route('onboarding'));
});

it('allows authenticated users to access home', function () {
    $user = User::factory()->create();
    Cache::put('home:trending_manga', [
        [
            'id' => 'naruto',
            'title' => 'Naruto',
            'description' => 'desc',
            'cover_image_url' => 'https://meo.comick.pictures/2zB1b.jpg',
            'banner_image_url' => null,
            'rating_average' => 8.3,
            'total_chapters' => 701,
            'genres' => ['Action'],
            'status' => 'completed',
        ],
    ], now()->addHour());
    Cache::put('home:last_fetched_at', now(), now()->addHour());

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
