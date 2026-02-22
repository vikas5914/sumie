<?php

use App\Services\ComickApiService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Cache::flush();
    Config::set('services.comix.base_url', 'https://comix-proxy.test');
});

it('throws on logical proxy status errors', function () {
    Http::fake([
        'https://comix-proxy.test/manga*' => Http::response([
            'status' => 500,
            'message' => 'Proxy error',
            'result' => null,
        ], 200),
    ]);

    $service = app(ComickApiService::class);

    expect(fn () => $service->searchManga(['title' => 'solo']))
        ->toThrow(\RuntimeException::class, 'Comix API request failed: 500');
});

it('applies safe-search query parameters with provider-compatible genres syntax', function () {
    Http::fake(function (Request $request) {
        $url = $request->url();

        if (str_contains($url, '/manga?')) {
            expect($url)->toContain('keyword=solo%20leveling')
                ->and($url)->toContain('order%5Brelevance%5D=desc')
                ->and($url)->toContain('limit=28')
                ->and($url)->toContain('genres_mode=and')
                ->and($url)->toContain('genres%5B%5D=-87264')
                ->and($url)->toContain('genres%5B%5D=-87266')
                ->and($url)->toContain('genres%5B%5D=-87268')
                ->and($url)->toContain('genres%5B%5D=-87265');

            return Http::response([
                'status' => 200,
                'result' => [
                    'items' => [],
                    'pagination' => [
                        'current_page' => 1,
                        'last_page' => 1,
                    ],
                ],
            ], 200);
        }

        return Http::response([
            'status' => 200,
            'result' => ['items' => []],
        ], 200);
    });

    $service = app(ComickApiService::class);
    $service->searchManga([
        'title' => 'solo leveling',
        'limit' => 28,
        'showall' => false,
    ]);
});

it('resolves legacy slug to hash id and maps term names', function () {
    Http::fake(function (Request $request) {
        $url = $request->url();

        if (str_contains($url, '/manga?')) {
            return Http::response([
                'status' => 200,
                'result' => [
                    'items' => [[
                        'manga_id' => 32026,
                        'hash_id' => 'emqg8',
                        'slug' => 'solo-leveling',
                        'title' => 'Solo Leveling',
                        'synopsis' => 'desc',
                        'poster' => ['large' => 'https://static.comix.to/solo.jpg'],
                        'status' => 'finished',
                        'type' => 'manhwa',
                        'is_nsfw' => false,
                        'year' => 2018,
                        'rated_avg' => 8.9,
                        'rated_count' => 516,
                        'follows_total' => 11342,
                        'latest_chapter' => 200,
                        'final_chapter' => 200,
                        'term_ids' => [6, 44, 2, 93169],
                        'links' => [],
                        'created_at' => 1758311450,
                        'updated_at' => 1763519114,
                    ]],
                    'pagination' => [
                        'current_page' => 1,
                        'last_page' => 1,
                    ],
                ],
            ], 200);
        }

        if (str_contains($url, '/manga/emqg8')) {
            return Http::response([
                'status' => 200,
                'result' => [
                    'manga_id' => 32026,
                    'hash_id' => 'emqg8',
                    'slug' => 'solo-leveling',
                    'title' => 'Solo Leveling',
                    'synopsis' => 'desc',
                    'poster' => ['large' => 'https://static.comix.to/solo.jpg'],
                    'status' => 'finished',
                    'type' => 'manhwa',
                    'is_nsfw' => false,
                    'year' => 2018,
                    'rated_avg' => 8.9,
                    'rated_count' => 516,
                    'follows_total' => 11342,
                    'latest_chapter' => 200,
                    'final_chapter' => 200,
                    'term_ids' => [6, 44, 2, 93169],
                    'links' => [],
                    'created_at' => 1758311450,
                    'updated_at' => 1763519114,
                ],
            ], 200);
        }

        if (str_contains($url, '/terms?type=genre')) {
            return Http::response([
                'status' => 200,
                'result' => ['items' => [['term_id' => 6, 'title' => 'Action']]],
            ], 200);
        }

        if (str_contains($url, '/terms?type=theme')) {
            return Http::response([
                'status' => 200,
                'result' => ['items' => [['term_id' => 44, 'title' => 'Magic']]],
            ], 200);
        }

        if (str_contains($url, '/terms?type=demographic')) {
            return Http::response([
                'status' => 200,
                'result' => ['items' => [['term_id' => 2, 'title' => 'Shounen']]],
            ], 200);
        }

        if (str_contains($url, '/terms?type=format')) {
            return Http::response([
                'status' => 200,
                'result' => ['items' => [['term_id' => 93169, 'title' => 'Oneshot']]],
            ], 200);
        }

        return Http::response([
            'status' => 404,
            'message' => 'Resource not found',
            'result' => null,
        ], 200);
    });

    $service = app(ComickApiService::class);
    $manga = $service->getMangaBySlug('solo-leveling');

    expect($manga['id'])->toBe('emqg8')
        ->and($manga['slug'])->toBe('solo-leveling')
        ->and($manga['status'])->toBe('completed')
        ->and($manga['genres'])->toContain('Action')
        ->and($manga['themes'])->toContain('Magic')
        ->and($manga['demographics'])->toContain('Shounen')
        ->and($manga['formats'])->toContain('Oneshot');
});

it('excludes nsfw manga from search feed by default', function () {
    Http::fake([
        'https://comix-proxy.test/manga*' => Http::response([
            'status' => 200,
            'result' => [
                'items' => [
                    [
                        'hash_id' => 'safe1',
                        'slug' => 'safe',
                        'title' => 'Safe',
                        'synopsis' => '',
                        'poster' => ['large' => 'https://static.comix.to/safe.jpg'],
                        'status' => 'releasing',
                        'type' => 'manga',
                        'is_nsfw' => false,
                        'year' => 2024,
                        'rated_avg' => 7.0,
                        'rated_count' => 1,
                        'follows_total' => 1,
                        'latest_chapter' => 1,
                        'term_ids' => [],
                    ],
                    [
                        'hash_id' => 'nsfw1',
                        'slug' => 'nsfw',
                        'title' => 'Nsfw',
                        'synopsis' => '',
                        'poster' => ['large' => 'https://static.comix.to/nsfw.jpg'],
                        'status' => 'releasing',
                        'type' => 'manga',
                        'is_nsfw' => true,
                        'year' => 2024,
                        'rated_avg' => 7.0,
                        'rated_count' => 1,
                        'follows_total' => 1,
                        'latest_chapter' => 1,
                        'term_ids' => [],
                    ],
                ],
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                ],
            ],
        ], 200),
        'https://comix-proxy.test/terms*' => Http::response([
            'status' => 200,
            'result' => ['items' => []],
        ], 200),
    ]);

    $service = app(ComickApiService::class);

    $defaultResults = $service->searchManga(['title' => 'x', 'limit' => 10]);
    $showAllResults = $service->searchManga(['title' => 'x', 'limit' => 10, 'showall' => true]);

    expect($defaultResults->pluck('id')->all())->toBe(['safe1'])
        ->and($showAllResults->pluck('id')->all())->toBe(['safe1', 'nsfw1']);
});

it('excludes nsfw manga from home trending feed', function () {
    Http::fake([
        'https://comix-proxy.test/top*' => Http::response([
            'status' => 200,
            'result' => [
                'items' => [
                    [
                        'hash_id' => 'safe1',
                        'slug' => 'safe',
                        'title' => 'Safe',
                        'synopsis' => '',
                        'poster' => ['large' => 'https://static.comix.to/safe.jpg'],
                        'status' => 'releasing',
                        'type' => 'manga',
                        'is_nsfw' => false,
                        'year' => 2024,
                        'rated_avg' => 7.0,
                        'rated_count' => 1,
                        'follows_total' => 1,
                        'latest_chapter' => 1,
                        'term_ids' => [],
                    ],
                    [
                        'hash_id' => 'nsfw1',
                        'slug' => 'nsfw',
                        'title' => 'Nsfw',
                        'synopsis' => '',
                        'poster' => ['large' => 'https://static.comix.to/nsfw.jpg'],
                        'status' => 'releasing',
                        'type' => 'manga',
                        'is_nsfw' => true,
                        'year' => 2024,
                        'rated_avg' => 7.0,
                        'rated_count' => 1,
                        'follows_total' => 1,
                        'latest_chapter' => 1,
                        'term_ids' => [],
                    ],
                ],
            ],
        ], 200),
        'https://comix-proxy.test/terms*' => Http::response([
            'status' => 200,
            'result' => ['items' => []],
        ], 200),
    ]);

    $service = app(ComickApiService::class);

    $results = $service->getTrendingManga(10);

    expect($results->pluck('id')->all())->toBe(['safe1']);
});

it('provides chapter fallback title from provider metadata when name is missing', function () {
    Http::fake(function (Request $request) {
        $url = $request->url();

        if (str_contains($url, '/manga?')) {
            return Http::response([
                'status' => 200,
                'result' => [
                    'items' => [[
                        'hash_id' => 'emqg8',
                        'slug' => 'solo-leveling',
                    ]],
                    'pagination' => [
                        'current_page' => 1,
                        'last_page' => 1,
                    ],
                ],
            ], 200);
        }

        if (str_contains($url, '/manga/emqg8/chapters')) {
            return Http::response([
                'status' => 200,
                'result' => [
                    'items' => [[
                        'chapter_id' => 5552463,
                        'number' => 1,
                        'name' => '',
                        'language' => 'en',
                        'is_official' => true,
                        'scanlation_group' => [
                            'name' => 'Flame Comics',
                        ],
                        'created_at' => 1764097524,
                    ]],
                    'pagination' => [
                        'current_page' => 1,
                        'last_page' => 1,
                    ],
                ],
            ], 200);
        }

        return Http::response([
            'status' => 200,
            'result' => ['items' => []],
        ], 200);
    });

    $service = app(ComickApiService::class);

    $chapters = $service->getMangaChaptersBySlug('solo-leveling');

    expect($chapters)->toHaveCount(1)
        ->and($chapters->first()['id'])->toBe('5552463')
        ->and($chapters->first()['title'])->toBe('Flame Comics');
});
