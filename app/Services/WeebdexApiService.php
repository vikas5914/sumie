<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Chapter;
use App\Models\Manga;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class WeebdexApiService
{
    private const DEFAULT_LIMIT = 20;

    /**
     * @var array<int, string>
     */
    private const DEFAULT_CONTENT_RATINGS = ['safe', 'suggestive'];

    private const STATS_CACHE_SECONDS = 300;

    private const SEARCH_CACHE_SECONDS = 120;

    private const MANGA_CACHE_SECONDS = 300;

    private const CHAPTER_CACHE_SECONDS = 120;

    private const TOP_CACHE_SECONDS = 300;

    private readonly PendingRequest $http;

    private readonly string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.weebdex.base_url', 'https://api.weebdex.org'), '/');
        $this->http = Http::acceptJson()
            ->timeout(30)
            ->withHeaders([
                'Origin' => 'https://weebdex.org',
                'Referer' => 'https://weebdex.org/',
            ]);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Collection<int, array<string, mixed>>
     */
    public function searchManga(array $filters = []): Collection
    {
        $limit = max(1, min(100, (int) ($filters['limit'] ?? self::DEFAULT_LIMIT)));
        $page = max(1, (int) ($filters['page'] ?? 1));
        $title = trim((string) ($filters['title'] ?? $filters['q'] ?? ''));

        $params = [
            'limit' => $limit,
            'page' => $page,
            'sort' => 'relevance',
            'order' => 'desc',
            'contentRating' => self::DEFAULT_CONTENT_RATINGS,
            'title' => $title,
        ];

        $status = $filters['status'] ?? null;
        if (is_string($status) && $status !== '' && $status !== 'all') {
            $params['status'] = $status;
        }

        $cacheKey = 'weebdex:search:'.sha1(json_encode($params, JSON_THROW_ON_ERROR));

        /** @var array<string, mixed> $payload */
        $payload = Cache::remember($cacheKey, now()->addSeconds(self::SEARCH_CACHE_SECONDS), function () use ($params): array {
            return $this->request('/manga', $params);
        });

        $items = is_array($payload['data'] ?? null) ? $payload['data'] : [];

        return collect($items)
            ->filter(fn (mixed $item): bool => is_array($item) && isset($item['id']))
            ->map(fn (array $item): array => $this->normalizeManga($item))
            ->values();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function getTrendingManga(int $limit = 10): Collection
    {
        $limit = max(1, min(100, $limit));
        $params = [
            'limit' => $limit,
            'page' => 1,
            'rank' => 'read',
            'time' => '30d',
            'contentRating' => self::DEFAULT_CONTENT_RATINGS,
        ];

        $cacheKey = 'weebdex:top:'.sha1(json_encode($params, JSON_THROW_ON_ERROR));

        /** @var array<string, mixed> $payload */
        $payload = Cache::remember($cacheKey, now()->addSeconds(self::TOP_CACHE_SECONDS), function () use ($params): array {
            return $this->request('/manga/top', $params);
        });

        $items = is_array($payload['data'] ?? null) ? $payload['data'] : [];

        return collect($items)
            ->filter(fn (mixed $item): bool => is_array($item) && isset($item['id']))
            ->map(fn (array $item): array => $this->normalizeManga($item))
            ->values();
    }

    /**
     * @return array<string, mixed>
     */
    public function getMangaById(string $id): array
    {
        $cacheKey = 'weebdex:manga:'.$id;

        /** @var array<string, mixed> $payload */
        $payload = Cache::remember($cacheKey, now()->addSeconds(self::MANGA_CACHE_SECONDS), function () use ($id): array {
            return $this->request('/manga/'.$id);
        });

        $manga = $this->normalizeManga($payload);

        if (($manga['chapters_count'] ?? 0) === 0 && ($manga['views_count'] ?? 0) === 0 && ($manga['follows_count'] ?? 0) === 0) {
            $stats = $this->getMangaStats($id);
            $manga['chapters_count'] = (int) ($stats['chapters'] ?? 0);
            $manga['follows_count'] = (int) ($stats['follows'] ?? 0);
            $manga['views_count'] = (int) ($stats['views'] ?? 0);
            $manga['total_chapters'] = $manga['chapters_count'];
            $manga['rating_average'] = $this->estimateRating((int) $manga['follows_count'], (int) $manga['views_count']);
        }

        return $manga;
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function getMangaChaptersById(string $id): Collection
    {
        $page = 1;
        $limit = 200;
        $maxPages = 15;
        $chapters = collect();

        do {
            $payload = $this->request('/manga/'.$id.'/chapters', [
                'limit' => $limit,
                'page' => $page,
                'order' => 'desc',
                'sort' => 'publishedAt',
                'tlang' => 'en',
            ]);

            $items = is_array($payload['data'] ?? null) ? $payload['data'] : [];
            $total = max(0, (int) ($payload['total'] ?? 0));
            $resultLimit = max(1, (int) ($payload['limit'] ?? $limit));

            $chapters = $chapters->merge(
                collect($items)
                    ->filter(fn (mixed $item): bool => is_array($item) && isset($item['id']))
                    ->map(fn (array $item): array => $this->normalizeChapter($item, $id))
            );

            $page++;

            if ($items === [] || $total === 0 || ($page - 1) * $resultLimit >= $total) {
                break;
            }
        } while ($page <= $maxPages);

        return $chapters->unique('id')->values();
    }

    /**
     * @return array<string, mixed>
     */
    public function getChapterById(string $id): array
    {
        $cacheKey = 'weebdex:chapter:'.$id;

        /** @var array<string, mixed> $payload */
        $payload = Cache::remember($cacheKey, now()->addSeconds(self::CHAPTER_CACHE_SECONDS), function () use ($id): array {
            return $this->request('/chapter/'.$id);
        });

        return $this->normalizeChapter($payload);
    }

    /**
     * @param  array<string, mixed>  $mangaData
     */
    public function syncMangaToDatabase(array $mangaData): Manga
    {
        $attributes = $this->mapMangaForDatabase($mangaData);
        $id = (string) $attributes['id'];

        $manga = Manga::query()->find($id) ?? new Manga;
        $manga->id = $id;
        $manga->fill($attributes);
        $manga->save();

        return $manga;
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $chapters
     */
    public function syncChapters(Manga $manga, Collection $chapters): void
    {
        $chapters->each(function (array $chapterData) use ($manga): void {
            $attributes = $this->mapChapterForDatabase($chapterData, $manga->id);

            Chapter::query()->updateOrCreate(
                ['id' => (string) $attributes['id']],
                $attributes
            );
        });
    }

    /**
     * @param  array<string, mixed>  $chapterData
     */
    public function syncChapterPages(string $chapterId, array $chapterData): Chapter
    {
        $existing = Chapter::query()->find($chapterId);
        $fallbackMangaId = $existing?->manga_id;

        $attributes = $this->mapChapterForDatabase($chapterData, $fallbackMangaId);
        $attributes['id'] = $chapterId;

        if (! isset($attributes['manga_id']) || ! is_string($attributes['manga_id']) || $attributes['manga_id'] === '') {
            throw new RuntimeException('Chapter payload missing manga id.');
        }

        return Chapter::query()->updateOrCreate(
            ['id' => $chapterId],
            $attributes
        );
    }

    public function isStale(?\DateTimeInterface $syncedAt, int $minutes): bool
    {
        if ($syncedAt === null) {
            return true;
        }

        return $syncedAt->lt(now()->subMinutes($minutes));
    }

    public function buildCoverImageUrl(string $mangaId, ?string $coverId, ?string $coverExt): ?string
    {
        if (! is_string($coverId) || $coverId === '') {
            return null;
        }

        $extension = ltrim((string) $coverExt, '.');
        $extension = $extension === '' ? 'jpg' : $extension;

        return sprintf('https://srv.weebdex.net/covers/%s/%s.%s', $mangaId, $coverId, $extension);
    }

    public function buildPageImageUrl(string $chapterId, string $fileName, ?string $node): ?string
    {
        if ($chapterId === '' || $fileName === '') {
            return null;
        }

        $baseNode = is_string($node) && $node !== '' ? rtrim($node, '/') : 'https://srv.weebdex.net';

        return sprintf('%s/data/%s/%s', $baseNode, $chapterId, rawurlencode($fileName));
    }

    /**
     * @return array<string, mixed>
     */
    private function request(string $path, array $query = []): array
    {
        $url = $this->baseUrl.$path;
        $queryString = $this->buildQueryString($query);

        if ($queryString !== '') {
            $url .= '?'.$queryString;
        }

        $response = $this->http->get($url);

        if ($response->status() === 404) {
            throw new RuntimeException('Not found');
        }

        if ($response->failed()) {
            throw new RuntimeException('Weebdex API request failed: '.$response->status());
        }

        $payload = $response->json();

        if (! is_array($payload)) {
            throw new RuntimeException('Unexpected Weebdex API response format.');
        }

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $query
     */
    private function buildQueryString(array $query): string
    {
        $pairs = [];

        foreach ($query as $key => $value) {
            if ($value === null) {
                continue;
            }

            if (is_array($value)) {
                foreach ($value as $nestedValue) {
                    if ($nestedValue === null) {
                        continue;
                    }

                    $pairs[] = rawurlencode((string) $key).'='.rawurlencode((string) $nestedValue);
                }

                continue;
            }

            $pairs[] = rawurlencode((string) $key).'='.rawurlencode((string) $value);
        }

        return implode('&', $pairs);
    }

    /**
     * @return array<string, mixed>
     */
    private function getMangaStats(string $id): array
    {
        $cacheKey = 'weebdex:manga-stats:'.$id;

        /** @var array<string, mixed> $payload */
        $payload = Cache::remember($cacheKey, now()->addSeconds(self::STATS_CACHE_SECONDS), function () use ($id): array {
            return $this->request('/manga/'.$id.'/statistics');
        });

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $raw
     * @return array<string, mixed>
     */
    private function normalizeManga(array $raw): array
    {
        $relationships = is_array($raw['relationships'] ?? null) ? $raw['relationships'] : [];
        $tags = is_array($relationships['tags'] ?? null) ? $relationships['tags'] : [];
        $cover = is_array($relationships['cover'] ?? null) ? $relationships['cover'] : [];
        $stats = is_array($relationships['stats'] ?? null) ? $relationships['stats'] : [];
        $authors = $this->extractRelationshipNames($relationships, 'authors');
        $artists = $this->extractRelationshipNames($relationships, 'artists');

        $genres = collect($tags)
            ->filter(fn (mixed $tag): bool => is_array($tag) && ($tag['group'] ?? null) === 'genre')
            ->map(fn (array $tag): string => (string) ($tag['name'] ?? ''))
            ->filter(fn (string $name): bool => $name !== '')
            ->values()
            ->all();

        $themes = collect($tags)
            ->filter(fn (mixed $tag): bool => is_array($tag) && ($tag['group'] ?? null) === 'theme')
            ->map(fn (array $tag): string => (string) ($tag['name'] ?? ''))
            ->filter(fn (string $name): bool => $name !== '')
            ->values()
            ->all();

        $coverId = $this->toNullableString($cover['id'] ?? null);
        $coverExt = $this->toNullableString($cover['ext'] ?? null);
        $mangaId = (string) ($raw['id'] ?? '');
        $followsCount = (int) ($stats['follows'] ?? 0);
        $viewsCount = (int) ($stats['views'] ?? 0);
        $chaptersCount = (int) ($stats['chapters'] ?? 0);
        $contentRating = (string) ($raw['content_rating'] ?? 'safe');

        return [
            'id' => $mangaId,
            'title' => (string) ($raw['title'] ?? 'Untitled'),
            'description' => (string) ($raw['description'] ?? ''),
            'status' => (string) ($raw['status'] ?? 'unknown'),
            'demographic' => $this->toNullableString($raw['demographic'] ?? null),
            'content_rating' => $contentRating,
            'year' => $this->toNullableInt($raw['year'] ?? null),
            'language' => $this->toNullableString($raw['language'] ?? null),
            'cover_id' => $coverId,
            'cover_ext' => $coverExt,
            'cover_image_url' => $this->buildCoverImageUrl($mangaId, $coverId, $coverExt),
            'genres' => $genres,
            'themes' => $themes,
            'authors' => $authors,
            'artists' => $artists,
            'available_languages' => array_values(array_filter((array) ($relationships['available_languages'] ?? []), 'is_string')),
            'links' => is_array($relationships['links'] ?? null) ? $relationships['links'] : [],
            'chapters_count' => $chaptersCount,
            'follows_count' => $followsCount,
            'views_count' => $viewsCount,
            'source_payload' => $raw,
            'synced_at' => now(),
            'total_chapters' => $chaptersCount,
            'rating_average' => $this->estimateRating($followsCount, $viewsCount),
            'author' => $authors[0] ?? null,
            'is_nsfw' => in_array($contentRating, ['erotica', 'pornographic'], true),
        ];
    }

    /**
     * @param  array<string, mixed>  $raw
     * @return array<string, mixed>
     */
    private function normalizeChapter(array $raw, ?string $fallbackMangaId = null): array
    {
        $relationships = is_array($raw['relationships'] ?? null) ? $raw['relationships'] : [];
        $mangaRelation = is_array($relationships['manga'] ?? null) ? $relationships['manga'] : [];
        $mangaId = $this->toNullableString($mangaRelation['id'] ?? null) ?? $fallbackMangaId;

        $pages = is_array($raw['data'] ?? null) ? $raw['data'] : [];
        $pageCount = count($pages);

        return [
            'id' => (string) ($raw['id'] ?? ''),
            'manga_id' => $mangaId,
            'chapter_number' => $this->toNullableString($raw['chapter'] ?? null),
            'volume' => $this->toNullableString($raw['volume'] ?? null),
            'title' => $this->toNullableString($raw['title'] ?? null),
            'language' => $this->toNullableString($raw['language'] ?? null) ?? 'en',
            'published_at' => $this->toNullableString($raw['published_at'] ?? null),
            'node' => $this->toNullableString($raw['node'] ?? null),
            'pages' => $pages,
            'page_count' => $pageCount,
            'is_unavailable' => (bool) ($raw['is_unavailable'] ?? false),
            'source_payload' => $raw,
            'synced_at' => now(),
        ];
    }

    /**
     * @param  array<string, mixed>  $mangaData
     * @return array<string, mixed>
     */
    private function mapMangaForDatabase(array $mangaData): array
    {
        $normalized = isset($mangaData['relationships'])
            ? $this->normalizeManga($mangaData)
            : [
                'id' => (string) ($mangaData['id'] ?? ''),
                'title' => (string) ($mangaData['title'] ?? 'Untitled'),
                'description' => (string) ($mangaData['description'] ?? ''),
                'status' => (string) ($mangaData['status'] ?? 'unknown'),
                'demographic' => $this->toNullableString($mangaData['demographic'] ?? null),
                'content_rating' => (string) ($mangaData['content_rating'] ?? 'safe'),
                'year' => $this->toNullableInt($mangaData['year'] ?? null),
                'language' => $this->toNullableString($mangaData['language'] ?? null),
                'cover_id' => $this->toNullableString($mangaData['cover_id'] ?? null),
                'cover_ext' => $this->toNullableString($mangaData['cover_ext'] ?? null),
                'cover_image_url' => $this->toNullableString($mangaData['cover_image_url'] ?? null),
                'genres' => is_array($mangaData['genres'] ?? null) ? $mangaData['genres'] : [],
                'themes' => is_array($mangaData['themes'] ?? null) ? $mangaData['themes'] : [],
                'authors' => is_array($mangaData['authors'] ?? null) ? $mangaData['authors'] : [],
                'artists' => is_array($mangaData['artists'] ?? null) ? $mangaData['artists'] : [],
                'available_languages' => is_array($mangaData['available_languages'] ?? null) ? $mangaData['available_languages'] : [],
                'links' => is_array($mangaData['links'] ?? null) ? $mangaData['links'] : [],
                'chapters_count' => (int) ($mangaData['chapters_count'] ?? $mangaData['total_chapters'] ?? 0),
                'follows_count' => (int) ($mangaData['follows_count'] ?? 0),
                'views_count' => (int) ($mangaData['views_count'] ?? 0),
                'source_payload' => is_array($mangaData['source_payload'] ?? null) ? $mangaData['source_payload'] : $mangaData,
                'synced_at' => $mangaData['synced_at'] ?? now(),
            ];

        return Arr::only($normalized, [
            'id',
            'title',
            'description',
            'status',
            'demographic',
            'content_rating',
            'year',
            'language',
            'cover_id',
            'cover_ext',
            'cover_image_url',
            'genres',
            'themes',
            'authors',
            'artists',
            'available_languages',
            'links',
            'chapters_count',
            'follows_count',
            'views_count',
            'source_payload',
            'synced_at',
        ]);
    }

    /**
     * @param  array<string, mixed>  $chapterData
     * @return array<string, mixed>
     */
    private function mapChapterForDatabase(array $chapterData, ?string $fallbackMangaId = null): array
    {
        $normalized = isset($chapterData['relationships']) || isset($chapterData['data'])
            ? $this->normalizeChapter($chapterData, $fallbackMangaId)
            : [
                'id' => (string) ($chapterData['id'] ?? ''),
                'manga_id' => (string) ($chapterData['manga_id'] ?? $fallbackMangaId ?? ''),
                'chapter_number' => $this->toNullableString($chapterData['chapter_number'] ?? null),
                'volume' => $this->toNullableString($chapterData['volume'] ?? null),
                'title' => $this->toNullableString($chapterData['title'] ?? null),
                'language' => $this->toNullableString($chapterData['language'] ?? null) ?? 'en',
                'published_at' => $this->toNullableString($chapterData['published_at'] ?? null),
                'node' => $this->toNullableString($chapterData['node'] ?? null),
                'pages' => is_array($chapterData['pages'] ?? null) ? $chapterData['pages'] : [],
                'page_count' => (int) ($chapterData['page_count'] ?? 0),
                'is_unavailable' => (bool) ($chapterData['is_unavailable'] ?? false),
                'source_payload' => is_array($chapterData['source_payload'] ?? null) ? $chapterData['source_payload'] : $chapterData,
                'synced_at' => $chapterData['synced_at'] ?? now(),
            ];

        return Arr::only($normalized, [
            'id',
            'manga_id',
            'chapter_number',
            'volume',
            'title',
            'language',
            'published_at',
            'node',
            'pages',
            'page_count',
            'is_unavailable',
            'source_payload',
            'synced_at',
        ]);
    }

    /**
     * @param  array<string, mixed>  $relationships
     * @return array<int, string>
     */
    private function extractRelationshipNames(array $relationships, string $key): array
    {
        $values = is_array($relationships[$key] ?? null) ? $relationships[$key] : [];

        return collect($values)
            ->filter(fn (mixed $item): bool => is_array($item))
            ->map(fn (array $item): string => (string) ($item['name'] ?? ''))
            ->filter(fn (string $name): bool => $name !== '')
            ->values()
            ->all();
    }

    private function estimateRating(int $follows, int $views): ?float
    {
        if ($follows <= 0 || $views <= 0) {
            return null;
        }

        $ratio = min(1, $follows / max(1, $views));

        return round(5 + ($ratio * 5), 2);
    }

    private function toNullableString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    private function toNullableInt(mixed $value): ?int
    {
        if (! is_numeric($value)) {
            return null;
        }

        return (int) $value;
    }
}
