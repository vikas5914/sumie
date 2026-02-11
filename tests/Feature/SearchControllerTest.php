<?php

use App\Models\User;
use App\Services\ComickApiService;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\mock;

it('applies filters to search results from api', function () {
    $user = User::factory()->create();

    $comick = mock(ComickApiService::class);

    $comick->shouldReceive('searchManga')->once()->withArgs(function (array $filters): bool {
        return $filters['title'] === 'Naruto'
            && $filters['limit'] === 28
            && $filters['showall'] === false
            && $filters['genres_mode'] === 'and'
            && ! array_key_exists('sort', $filters);
    })->andReturn(collect([
        [
            'id' => 'abc12',
            'title' => 'Naruto',
            'author' => 'Someone Else',
            'status' => 'ongoing',
            'genres' => ['Action'],
            'type' => 'manga',
            'rating_average' => 4.2,
            'cover_image_url' => null,
            'total_chapters' => 20,
            'formats' => [],
        ],
        [
            'id' => 'def34',
            'title' => 'Anything',
            'author' => 'Naruto Author',
            'status' => 'completed',
            'genres' => ['Action'],
            'type' => 'manga',
            'rating_average' => 4.8,
            'cover_image_url' => null,
            'total_chapters' => 1,
            'formats' => ['Oneshot'],
        ],
    ]));

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
            ->where('results.0.id', 'def34')
        );
});

it('returns all search results from api when no filter', function () {
    $user = User::factory()->create();

    $comick = mock(ComickApiService::class);

    $comick->shouldReceive('searchManga')->once()->withArgs(function (array $filters): bool {
        return $filters['title'] === 'action'
            && $filters['limit'] === 28
            && $filters['showall'] === false
            && $filters['genres_mode'] === 'and'
            && ! array_key_exists('sort', $filters);
    })->andReturn(collect([
        [
            'id' => 'action-story',
            'title' => 'Action Story',
            'author' => 'A',
            'status' => 'ongoing',
            'genres' => ['Action', 'Fantasy'],
            'type' => 'manga',
            'rating_average' => 4.1,
            'cover_image_url' => null,
            'total_chapters' => 10,
            'formats' => [],
        ],
    ]));

    $response = actingAs($user)->get(route('search', [
        'q' => 'action',
    ]));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('search')
            ->has('results', 1)
            ->where('results.0.title', 'Action Story')
        );
});

it('filters manga type using comix type field', function () {
    $user = User::factory()->create();

    $comick = mock(ComickApiService::class);

    $comick->shouldReceive('searchManga')->once()->andReturn(collect([
        [
            'id' => 'manga-id',
            'title' => 'Manga Item',
            'author' => 'A',
            'status' => 'ongoing',
            'genres' => ['Action'],
            'type' => 'manga',
            'rating_average' => 4.1,
            'cover_image_url' => null,
            'total_chapters' => 10,
            'formats' => [],
        ],
        [
            'id' => 'manhwa-id',
            'title' => 'Manhwa Item',
            'author' => 'B',
            'status' => 'ongoing',
            'genres' => ['Action'],
            'type' => 'manhwa',
            'rating_average' => 4.1,
            'cover_image_url' => null,
            'total_chapters' => 10,
            'formats' => [],
        ],
    ]));

    $response = actingAs($user)->get(route('search', [
        'q' => 'action',
        'filter' => 'manga',
    ]));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('search')
            ->has('results', 1)
            ->where('results.0.id', 'manga-id')
        );
});

it('supports oneshot filtering from formats', function () {
    $user = User::factory()->create();

    $comick = mock(ComickApiService::class);

    $comick->shouldReceive('searchManga')->once()->andReturn(collect([
        [
            'id' => 'one-shot-example',
            'title' => 'One Shot Example',
            'author' => 'A',
            'status' => 'ongoing',
            'genres' => ['Drama'],
            'type' => 'manga',
            'rating_average' => 4.0,
            'cover_image_url' => null,
            'total_chapters' => 12,
            'formats' => ['Oneshot'],
        ],
        [
            'id' => 'one-shot-example-extended',
            'title' => 'One Shot Example Extended',
            'author' => 'B',
            'status' => 'ongoing',
            'genres' => ['Drama'],
            'type' => 'manga',
            'rating_average' => 4.0,
            'cover_image_url' => null,
            'total_chapters' => 12,
            'formats' => ['Web Comic'],
        ],
    ]));

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

it('returns empty results for short queries', function () {
    $user = User::factory()->create();

    $comick = mock(ComickApiService::class);

    $comick->shouldNotReceive('searchManga');

    $response = actingAs($user)->get(route('search', [
        'q' => 'a',
    ]));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('search')
            ->has('results', 0)
        );
});
