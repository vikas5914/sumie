<?php

use App\Models\Manga;
use App\Models\User;
use App\Services\MangaDexApiService;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\mock;

it('applies filters to both title and author matches', function () {
    $user = User::factory()->create();

    $mangaDex = mock(MangaDexApiService::class);

    $mangaDex->shouldReceive('searchManga')->once()->andReturn(collect([
        [
            'id' => 'a',
            'title' => 'Naruto',
            'author' => 'Someone Else',
            'status' => 'ongoing',
            'genres' => ['Action'],
            'format_tags' => [],
            'country_of_origin' => 'Japan',
            'rating_average' => 4.2,
            'cover_image_url' => null,
            'total_chapters' => 20,
        ],
        [
            'id' => 'b',
            'title' => 'Anything',
            'author' => 'Naruto Author',
            'status' => 'completed',
            'genres' => ['Action'],
            'format_tags' => [],
            'country_of_origin' => 'Japan',
            'rating_average' => 4.8,
            'cover_image_url' => null,
            'total_chapters' => 1,
        ],
    ]));

    $completed = Manga::factory()->create([
        'title' => 'Anything',
        'author' => 'Naruto Author',
        'status' => 'completed',
    ]);

    $mangaDex
        ->shouldReceive('syncMangaToDatabase')
        ->once()
        ->withArgs(fn (array $data) => ($data['status'] ?? null) === 'completed')
        ->andReturn($completed);

    $response = actingAs($user)->get(route('search', [
        'q' => 'Naruto',
        'filter' => 'COMPLETED',
    ]));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('search')
            ->where('query', 'Naruto')
            ->where('filter', 'completed')
            ->has('results', 1)
            ->where('results.0.status', 'completed')
        );
});

it('matches genres case-insensitively for exact tags', function () {
    $user = User::factory()->create();

    $mangaDex = mock(MangaDexApiService::class);

    $mangaDex->shouldReceive('searchManga')->once()->andReturn(collect([
        [
            'id' => 'c',
            'title' => 'Action Story',
            'author' => 'A',
            'status' => 'ongoing',
            'genres' => ['Action', 'Fantasy'],
            'format_tags' => [],
            'country_of_origin' => 'Japan',
            'rating_average' => 4.1,
            'cover_image_url' => null,
            'total_chapters' => 10,
        ],
    ]));

    $synced = Manga::factory()->create([
        'title' => 'Action Story',
        'author' => 'A',
        'genres' => ['Action', 'Fantasy'],
    ]);

    $mangaDex->shouldReceive('syncMangaToDatabase')->once()->andReturn($synced);

    $response = actingAs($user)->get(route('search', [
        'q' => 'action',
    ]));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('search')
            ->has('results', 1)
        );
});

it('supports oneshot filtering', function () {
    $user = User::factory()->create();

    $mangaDex = mock(MangaDexApiService::class);

    $mangaDex->shouldReceive('searchManga')->once()->andReturn(collect([
        [
            'id' => 'd',
            'title' => 'One Shot Example',
            'author' => 'A',
            'status' => 'ongoing',
            'genres' => ['Drama'],
            'format_tags' => ['Oneshot'],
            'country_of_origin' => 'Japan',
            'rating_average' => 4.0,
            'cover_image_url' => null,
            'total_chapters' => 1,
        ],
        [
            'id' => 'e',
            'title' => 'One Shot Example Extended',
            'author' => 'B',
            'status' => 'ongoing',
            'genres' => ['Drama'],
            'format_tags' => ['Adaptation'],
            'country_of_origin' => 'Japan',
            'rating_average' => 4.0,
            'cover_image_url' => null,
            'total_chapters' => 12,
        ],
    ]));

    $synced = Manga::factory()->create([
        'title' => 'One Shot Example',
        'format_tags' => ['Oneshot'],
        'total_chapters' => 1,
    ]);

    $mangaDex
        ->shouldReceive('syncMangaToDatabase')
        ->once()
        ->withArgs(fn (array $data) => ($data['title'] ?? null) === 'One Shot Example')
        ->andReturn($synced);

    $response = actingAs($user)->get(route('search', [
        'q' => 'One Shot',
        'filter' => 'oneshot',
    ]));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('search')
            ->where('filter', 'oneshot')
            ->has('results', 1)
            ->where('results.0.title', 'One Shot Example')
        );
});
