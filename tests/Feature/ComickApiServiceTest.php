<?php

use App\Services\WeebdexApiService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Config::set('services.weebdex.base_url', 'https://api.weebdex.test');
});

it('sends required headers and safe content filter when searching', function () {
    Http::fake(function (Request $request) {
        expect($request->header('Origin'))->toContain('https://weebdex.org')
            ->and($request->header('Referer'))->toContain('https://weebdex.org/')
            ->and($request->url())->toContain('contentRating=safe&contentRating=suggestive')
            ->and($request->url())->toContain('sort=relevance')
            ->and($request->url())->toContain('order=desc');

        return Http::response([
            'data' => [],
            'total' => 0,
            'limit' => 20,
            'page' => 1,
        ], 200);
    });

    $service = app(WeebdexApiService::class);
    $results = $service->searchManga([
        'title' => 'solo',
        'limit' => 20,
    ]);

    expect($results)->toHaveCount(0);
});

it('uses expected query parameters when loading top manga', function () {
    Http::fake(function (Request $request) {
        expect($request->url())->toContain('/manga/top')
            ->and($request->url())->toContain('rank=read')
            ->and($request->url())->toContain('time=30d')
            ->and($request->url())->toContain('contentRating=safe&contentRating=suggestive');

        return Http::response([
            'data' => [],
            'total' => 0,
            'limit' => 12,
            'page' => 1,
        ], 200);
    });

    $service = app(WeebdexApiService::class);
    $results = $service->getTrendingManga(12);

    expect($results)->toHaveCount(0);
});

it('maps manga details and statistics into normalized fields', function () {
    Http::fake(function (Request $request) {
        if (str_contains($request->url(), '/manga/abc123/statistics')) {
            return Http::response([
                'chapters' => 88,
                'follows' => 1200,
                'views' => 50000,
            ], 200);
        }

        return Http::response([
            'id' => 'abc123',
            'title' => 'Sample Manga',
            'description' => 'Desc',
            'status' => 'ongoing',
            'demographic' => 'shounen',
            'content_rating' => 'safe',
            'year' => 2024,
            'language' => 'ja',
            'relationships' => [
                'cover' => [
                    'id' => 'cover001',
                    'ext' => '.jpg',
                ],
                'tags' => [
                    ['group' => 'genre', 'name' => 'Action'],
                    ['group' => 'theme', 'name' => 'Adventure'],
                ],
                'authors' => [
                    ['name' => 'Author One'],
                ],
                'artists' => [
                    ['name' => 'Artist One'],
                ],
                'available_languages' => ['en'],
            ],
        ], 200);
    });

    $service = app(WeebdexApiService::class);
    $manga = $service->getMangaById('abc123');

    expect($manga['id'])->toBe('abc123')
        ->and($manga['title'])->toBe('Sample Manga')
        ->and($manga['cover_id'])->toBe('cover001')
        ->and($manga['cover_image_url'])->toContain('/covers/abc123/cover001.jpg')
        ->and($manga['genres'])->toContain('Action')
        ->and($manga['themes'])->toContain('Adventure')
        ->and($manga['authors'])->toContain('Author One')
        ->and($manga['artists'])->toContain('Artist One')
        ->and($manga['chapters_count'])->toBe(88)
        ->and($manga['follows_count'])->toBe(1200)
        ->and($manga['views_count'])->toBe(50000);
});

it('loads chapter detail with page payload', function () {
    Http::fake([
        'https://api.weebdex.test/chapter/chap001*' => Http::response([
            'id' => 'chap001',
            'chapter' => '12.5',
            'language' => 'en',
            'node' => 'https://s13.weebdex.net',
            'data' => [
                ['name' => '1-image.jpg', 'dimensions' => [800, 1200]],
            ],
            'relationships' => [
                'manga' => ['id' => 'abc123'],
            ],
        ], 200),
    ]);

    $service = app(WeebdexApiService::class);
    $chapter = $service->getChapterById('chap001');

    expect($chapter['id'])->toBe('chap001')
        ->and($chapter['manga_id'])->toBe('abc123')
        ->and($chapter['node'])->toBe('https://s13.weebdex.net')
        ->and($chapter['page_count'])->toBe(1);
});
