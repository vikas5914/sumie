<?php

use App\Models\User;
use App\Services\ComickApiService;
use Illuminate\Support\Facades\Cache;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\mock;

beforeEach(function () {
    Cache::flush();
});

it('loads the home shell without resolving deferred feed data', function () {
    $user = User::factory()->create();

    $comick = mock(ComickApiService::class);

    $comick->shouldNotReceive('getTrendingManga');

    $response = actingAs($user)->get(route('home'));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('home')
            ->where('meta.hasCachedData', false)
        );
});

it('refreshes home cache in the background', function () {
    $user = User::factory()->create();

    $comick = mock(ComickApiService::class);

    $comick->shouldReceive('getTrendingManga')->once()->with(12)->andReturn(collect([
        [
            'id' => '7nzg',
            'slug' => 'jujutsu-kaisen',
            'title' => 'Jujutsu Kaisen',
            'description' => 'desc',
            'cover_image_url' => 'https://static.comix.to/m/n/o.jpg',
            'banner_image_url' => null,
            'author' => null,
            'artist' => null,
            'status' => 'completed',
            'content_rating' => 'safe',
            'is_nsfw' => false,
            'genres' => ['Action'],
            'themes' => [],
            'demographics' => ['Shounen'],
            'formats' => ['Web Comic'],
            'release_year' => 2018,
            'rating_average' => 8.9,
            'rating_count' => 2016,
            'total_chapters' => 271,
            'links' => [],
        ],
    ]));

    $response = actingAs($user)->post(route('home.refresh'));

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Data refreshed successfully');
});
