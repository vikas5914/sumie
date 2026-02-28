<?php

use App\Models\User;
use App\Services\WeebdexApiService;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\mock;

it('applies completed filter to upstream search results', function () {
    $user = User::factory()->create();

    $weebdex = mock(WeebdexApiService::class);
    $weebdex->shouldReceive('searchManga')->once()->withArgs(function (array $filters): bool {
        return $filters['title'] === 'Naruto'
            && $filters['limit'] === 28;
    })->andReturn(collect([
        [
            'id' => 'ongoing1',
            'title' => 'Ongoing Item',
            'author' => 'A',
            'status' => 'ongoing',
            'genres' => ['Action'],
            'rating_average' => 4.2,
            'cover_image_url' => null,
            'total_chapters' => 20,
        ],
        [
            'id' => 'completed1',
            'title' => 'Completed Item',
            'author' => 'B',
            'status' => 'completed',
            'genres' => ['Action'],
            'rating_average' => 4.8,
            'cover_image_url' => null,
            'total_chapters' => 12,
        ],
    ]));

    actingAs($user)->get(route('search', [
        'q' => 'Naruto',
        'filter' => 'completed',
    ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('search')
            ->where('filter', 'completed')
            ->has('results', 1)
            ->where('results.0.id', 'completed1')
        );
});

it('applies ongoing filter to upstream search results', function () {
    $user = User::factory()->create();

    $weebdex = mock(WeebdexApiService::class);
    $weebdex->shouldReceive('searchManga')->once()->andReturn(collect([
        [
            'id' => 'ongoing1',
            'title' => 'Ongoing Item',
            'author' => 'A',
            'status' => 'ongoing',
            'genres' => ['Action'],
            'rating_average' => 4.2,
            'cover_image_url' => null,
            'total_chapters' => 20,
        ],
        [
            'id' => 'completed1',
            'title' => 'Completed Item',
            'author' => 'B',
            'status' => 'completed',
            'genres' => ['Action'],
            'rating_average' => 4.8,
            'cover_image_url' => null,
            'total_chapters' => 12,
        ],
    ]));

    actingAs($user)->get(route('search', [
        'q' => 'status',
        'filter' => 'ongoing',
    ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('filter', 'ongoing')
            ->has('results', 1)
            ->where('results.0.id', 'ongoing1')
        );
});

it('supports oneshot filtering from chapter count', function () {
    $user = User::factory()->create();

    $weebdex = mock(WeebdexApiService::class);
    $weebdex->shouldReceive('searchManga')->once()->andReturn(collect([
        [
            'id' => 'one-shot',
            'title' => 'One Shot',
            'author' => 'A',
            'status' => 'ongoing',
            'genres' => ['Drama'],
            'rating_average' => 4.0,
            'cover_image_url' => null,
            'total_chapters' => 1,
        ],
        [
            'id' => 'not-one-shot',
            'title' => 'Not One Shot',
            'author' => 'B',
            'status' => 'ongoing',
            'genres' => ['Drama'],
            'rating_average' => 4.0,
            'cover_image_url' => null,
            'total_chapters' => 12,
        ],
    ]));

    actingAs($user)->get(route('search', [
        'q' => 'shot',
        'filter' => 'oneshot',
    ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('filter', 'oneshot')
            ->has('results', 1)
            ->where('results.0.id', 'one-shot')
        );
});

it('returns empty results for short queries', function () {
    $user = User::factory()->create();

    $weebdex = mock(WeebdexApiService::class);
    $weebdex->shouldNotReceive('searchManga');

    actingAs($user)->get(route('search', [
        'q' => 'a',
    ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('results', 0)
        );
});

it('always returns proxied image urls for search results', function () {
    $user = User::factory()->create();
    $coverUrl = 'https://srv.weebdex.net/covers/test/cover.jpg';

    $weebdex = mock(WeebdexApiService::class);
    $weebdex->shouldReceive('searchManga')->once()->andReturn(collect([
        [
            'id' => 'proxied-url-case',
            'title' => 'Proxied URL Case',
            'author' => 'A',
            'status' => 'ongoing',
            'genres' => ['Action'],
            'rating_average' => 4.5,
            'cover_image_url' => $coverUrl,
            'total_chapters' => 12,
        ],
    ]));

    $encodedUrl = base64_encode($coverUrl);

    actingAs($user)
        ->get(route('search', ['q' => 'Proxy']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('results.0.cover_image_url', route('image.proxy', ['encodedUrl' => $encodedUrl]))
        );
});
