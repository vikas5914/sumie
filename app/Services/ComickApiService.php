<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Chapter;
use App\Models\Manga;
use Carbon\Carbon;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class ComickApiService
{
    private const DEFAULT_LIMIT = 20;

    private const MAX_CHAPTER_PAGES = 50;

    /**
     * @var array<int, int>
     */
    private const DEFAULT_EXCLUDED_GENRE_IDS = [-87264, -87266, -87268, -87265];

    private readonly PendingRequest $http;

    private readonly string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.comix.base_url', 'https://comix-proxy.kapadiya.net'), '/');
        $this->http = Http::withOptions([
            'timeout' => 30,
        ])->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Search for manga with optional filters.
     *
     * @param  array<string, mixed>  $filters
     * @return Collection<int, array<string, mixed>>
     */
    public function searchManga(array $filters = []): Collection
    {
        $limit = $this->resolveLimit($filters);
        $page = $this->resolvePage($filters, $limit);
        $sort = $this->resolveSort($filters);

        if ($sort === 'follow') {
            $items = $this->fetchTopItems('follows', 30, $limit);

            return $this->normalizeMangaCollection($items, $filters, $limit);
        }

        $params = [
            'limit' => $limit,
            'page' => $page,
        ];

        $title = trim((string) ($filters['title'] ?? $filters['q'] ?? ''));
        if ($title !== '') {
            $params['keyword'] = $title;
        }

        $excludedGenreIds = $this->resolveExcludedGenreIds($filters);
        if ($excludedGenreIds !== []) {
            $params['genres[]'] = $excludedGenreIds;
            $params['genres_mode'] = $this->resolveGenresMode($filters);
        }

        if ($sort === 'uploaded') {
            $params['order[chapter_updated_at]'] = 'desc';
        } else {
            $params['order[relevance]'] = 'desc';
        }

        $result = $this->requestResult('/api/manga', $params);

        if (! is_array($result)) {
            throw new \RuntimeException('Unexpected Comix search response format.');
        }

        $items = is_array($result['items'] ?? null) ? $result['items'] : [];

        return $this->normalizeMangaCollection($items, $filters, $limit, $sort);
    }

    /**
     * Get trending manga ordered by follows.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getTrendingManga(int $limit = 10): Collection
    {
        $items = $this->fetchTopItems('trending', 7, $limit);

        return $this->normalizeMangaCollection($items, ['showall' => false], $limit);
    }

    /**
     * Get highest-rated manga.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getTopRatedManga(int $limit = 10): Collection
    {
        return $this->searchManga([
            'limit' => $limit,
            'sort' => 'rating',
            'showall' => false,
        ]);
    }

    /**
     * Get recently updated manga.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getRecentlyUpdatedManga(int $limit = 10): Collection
    {
        return $this->searchManga([
            'limit' => $limit,
            'sort' => 'uploaded',
            'showall' => false,
        ]);
    }

    /**
     * Get manga details by identifier.
     *
     * @return array<string, mixed>
     */
    public function getMangaBySlug(string $slug): array
    {
        $code = $this->resolveMangaCode($slug);

        if ($code === null) {
            throw new \RuntimeException('Manga not found.');
        }

        $manga = $this->fetchMangaByCode($code);

        if (! is_array($manga)) {
            throw new \RuntimeException('Unexpected Comix manga response format.');
        }

        return $this->normalizeMangaData($manga);
    }

    /**
     * Fetch manga chapters by identifier.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getMangaChaptersBySlug(string $slug): Collection
    {
        $code = $this->resolveMangaCode($slug);

        if ($code === null) {
            return collect();
        }

        $chapters = collect();
        $page = 1;
        $lastPage = 1;

        do {
            $result = $this->requestResult('/api/manga/'.$code.'/chapters', [
                'limit' => 100,
                'page' => $page,
                'order[number]' => 'asc',
            ]);

            if (! is_array($result)) {
                break;
            }

            $items = is_array($result['items'] ?? null) ? $result['items'] : [];
            $chapters = $chapters->merge($items);

            $pagination = is_array($result['pagination'] ?? null) ? $result['pagination'] : [];
            $lastPage = max(1, (int) ($pagination['last_page'] ?? 1));
            $page++;
        } while ($page <= $lastPage && $page <= self::MAX_CHAPTER_PAGES);

        return $chapters
            ->filter(fn (mixed $chapter): bool => is_array($chapter) && ! empty($chapter['chapter_id']))
            ->map(fn (array $chapter): array => $this->normalizeChapterData($chapter))
            ->values();
    }

    /**
     * Fetch chapter details by chapter identifier.
     *
     * @return array<string, mixed>
     */
    public function getChapterById(string $chapterId): array
    {
        $result = $this->requestResult('/api/chapters/'.$chapterId);

        if (! is_array($result)) {
            throw new \RuntimeException('Unexpected Comix chapter response format.');
        }

        return $this->normalizeChapterDetails($result);
    }

    /**
     * Sync manga data to local database.
     *
     * @param  array<string, mixed>  $mangaData
     */
    public function syncMangaToDatabase(array $mangaData): Manga
    {
        $mangaId = (string) ($mangaData['id'] ?? '');

        if ($mangaId === '') {
            throw new \InvalidArgumentException('Comix manga payload is missing hash id.');
        }

        $slug = isset($mangaData['slug']) ? (string) $mangaData['slug'] : null;

        $manga = Manga::query()->find($mangaId)
            ?? ($slug !== null ? Manga::query()->where('slug', $slug)->first() : null)
            ?? new Manga;

        $manga->id = $mangaId;
        $this->setIfColumnExists($manga, 'slug', $slug);
        $manga->title = (string) ($mangaData['title'] ?? 'Untitled');
        $manga->description = (string) ($mangaData['description'] ?? '');
        $manga->cover_image_url = $mangaData['cover_image_url'] ?? null;
        $manga->banner_image_url = $mangaData['banner_image_url'] ?? null;
        $manga->author = $mangaData['author'] ?? null;
        $manga->artist = $mangaData['artist'] ?? null;
        $manga->status = (string) ($mangaData['status'] ?? 'unknown');
        $manga->content_rating = (string) ($mangaData['content_rating'] ?? 'safe');
        $manga->genres = is_array($mangaData['genres'] ?? null) ? $mangaData['genres'] : [];
        $manga->themes = is_array($mangaData['themes'] ?? null) ? $mangaData['themes'] : [];
        $this->setIfColumnExists($manga, 'demographics', is_array($mangaData['demographics'] ?? null) ? $mangaData['demographics'] : []);
        $this->setIfColumnExists($manga, 'formats', is_array($mangaData['formats'] ?? null) ? $mangaData['formats'] : []);
        $manga->total_chapters = (int) ($mangaData['total_chapters'] ?? 0);
        $manga->release_year = $this->toNullableInt($mangaData['release_year'] ?? null);
        $manga->country_of_origin = $mangaData['country_of_origin'] ?? null;
        $manga->rating_average = $this->toNullableFloat($mangaData['rating_average'] ?? null);
        $manga->rating_count = (int) ($mangaData['rating_count'] ?? 0);
        $manga->view_count = (int) ($mangaData['view_count'] ?? 0);
        $manga->source_name = (string) ($mangaData['source_name'] ?? 'Comix');
        $manga->source_url = $mangaData['source_url'] ?? null;
        $manga->links = is_array($mangaData['links'] ?? null) ? $mangaData['links'] : [];
        $this->setIfColumnExists($manga, 'source_manga_id', $this->toNullableInt($mangaData['source_manga_id'] ?? null));
        $this->setIfColumnExists($manga, 'type', isset($mangaData['type']) ? (string) $mangaData['type'] : null);
        $this->setIfColumnExists($manga, 'is_nsfw', (bool) ($mangaData['is_nsfw'] ?? false));
        $manga->created_at_api = $mangaData['created_at_api'] ?? null;
        $manga->updated_at_api = $mangaData['updated_at_api'] ?? null;
        $manga->last_fetched_at = now();
        $manga->save();

        return $manga;
    }

    /**
     * Sync manga chapters to local database.
     *
     * @param  Collection<int, array<string, mixed>>  $chapters
     */
    public function syncChapters(Manga $manga, Collection $chapters): void
    {
        foreach ($chapters as $chapterData) {
            $externalId = (string) ($chapterData['id'] ?? '');
            $chapterNumber = (float) ($chapterData['chapter_number'] ?? 0.0);

            $attributes = [
                'chapter_number' => $chapterNumber,
                'chapter_label' => $chapterData['chapter_label'] ?? null,
                'volume_number' => $chapterData['volume'] ?? null,
                'title' => $chapterData['title'] ?? null,
                'release_date' => $chapterData['published_at'] ?? null,
                'language' => (string) ($chapterData['language'] ?? 'en'),
                'page_count' => (int) ($chapterData['page_count'] ?? 0),
                'source_url' => $externalId !== '' ? "https://comix.to/title/{$manga->id}/{$externalId}" : null,
                'is_published' => true,
            ];

            if ($externalId !== '') {
                Chapter::updateOrCreate(
                    [
                        'manga_id' => $manga->id,
                        'external_id' => $externalId,
                    ],
                    $attributes
                );

                continue;
            }

            Chapter::updateOrCreate(
                [
                    'manga_id' => $manga->id,
                    'chapter_number' => $chapterNumber,
                ],
                $attributes
            );
        }

        $manga->update([
            'total_chapters' => max((int) $manga->total_chapters, $manga->chapters()->count()),
        ]);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function resolveSort(array $filters): ?string
    {
        $directSort = $filters['sort'] ?? null;
        if (is_string($directSort) && $directSort !== '') {
            return $directSort;
        }

        if (! is_array($filters['order'] ?? null)) {
            return null;
        }

        /** @var array<string, string> $legacyOrder */
        $legacyOrder = $filters['order'];

        if (isset($legacyOrder['followedCount'])) {
            return 'follow';
        }

        if (isset($legacyOrder['rating'])) {
            return 'rating';
        }

        if (isset($legacyOrder['latestUploadedChapter'])) {
            return 'uploaded';
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function resolvePage(array $filters, int $limit): int
    {
        $page = (int) ($filters['page'] ?? 0);
        if ($page > 0) {
            return $page;
        }

        $offset = (int) ($filters['offset'] ?? 0);
        if ($offset <= 0 || $limit <= 0) {
            return 1;
        }

        return intdiv($offset, $limit) + 1;
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function resolveLimit(array $filters): int
    {
        $limit = (int) ($filters['limit'] ?? self::DEFAULT_LIMIT);

        if ($limit <= 0) {
            return self::DEFAULT_LIMIT;
        }

        return min(100, $limit);
    }

    /**
     * @param  array<int, mixed>  $items
     * @param  array<string, mixed>  $filters
     * @return Collection<int, array<string, mixed>>
     */
    private function normalizeMangaCollection(array $items, array $filters, int $limit, ?string $sort = null): Collection
    {
        $includeNsfw = $this->shouldIncludeNsfw($filters);

        $collection = collect($items)
            ->filter(fn (mixed $manga): bool => is_array($manga))
            ->map(fn (array $manga): array => $this->normalizeMangaData($manga))
            ->filter(function (array $manga) use ($includeNsfw): bool {
                if ($includeNsfw) {
                    return true;
                }

                return ! (bool) ($manga['is_nsfw'] ?? false);
            });

        if ($sort === 'rating') {
            $collection = $collection->sortByDesc(fn (array $manga): float => (float) ($manga['rating_average'] ?? 0));
        }

        return $collection
            ->take($limit)
            ->values();
    }

    private function shouldIncludeNsfw(array $filters): bool
    {
        if (! array_key_exists('showall', $filters) && ! array_key_exists('include_nsfw', $filters)) {
            return false;
        }

        return filter_var($filters['showall'] ?? $filters['include_nsfw'] ?? false, FILTER_VALIDATE_BOOL);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<int, int>
     */
    private function resolveExcludedGenreIds(array $filters): array
    {
        if ($this->shouldIncludeNsfw($filters)) {
            return [];
        }

        $rawExcludedGenres = $filters['excluded_genres'] ?? self::DEFAULT_EXCLUDED_GENRE_IDS;

        if (! is_array($rawExcludedGenres)) {
            return self::DEFAULT_EXCLUDED_GENRE_IDS;
        }

        return collect($rawExcludedGenres)
            ->map(fn (mixed $genreId): ?int => $this->toNullableInt($genreId))
            ->filter(fn (?int $genreId): bool => $genreId !== null && $genreId !== 0)
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function resolveGenresMode(array $filters): string
    {
        $genresMode = strtolower((string) ($filters['genres_mode'] ?? 'and'));

        return in_array($genresMode, ['and', 'or'], true) ? $genresMode : 'and';
    }

    /**
     * @return array<int, mixed>
     */
    private function fetchTopItems(string $type, int $days, int $limit): array
    {
        $result = $this->requestResult('/api/top', [
            'type' => $type,
            'days' => $days,
            'limit' => $limit,
        ]);

        if (! is_array($result)) {
            return [];
        }

        return is_array($result['items'] ?? null) ? $result['items'] : [];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function fetchMangaByCode(string $code): ?array
    {
        $result = $this->requestResult('/api/manga/'.$code);

        return is_array($result) ? $result : null;
    }

    private function resolveMangaCode(string $identifier): ?string
    {
        $trimmedIdentifier = trim($identifier);

        if ($trimmedIdentifier === '') {
            return null;
        }

        if ($this->looksLikeComixCode($trimmedIdentifier)) {
            try {
                $manga = $this->fetchMangaByCode($trimmedIdentifier);

                if (is_array($manga)) {
                    return (string) ($manga['hash_id'] ?? $trimmedIdentifier);
                }
            } catch (\RuntimeException $exception) {
                if (! $this->isNotFoundException($exception)) {
                    throw $exception;
                }
            }
        }

        $result = $this->requestResult('/api/manga', [
            'keyword' => $trimmedIdentifier,
            'limit' => 25,
            'page' => 1,
            'order[relevance]' => 'desc',
        ]);

        if (! is_array($result)) {
            return null;
        }

        $items = is_array($result['items'] ?? null) ? $result['items'] : [];

        $match = collect($items)->first(function (mixed $item) use ($trimmedIdentifier): bool {
            if (! is_array($item)) {
                return false;
            }

            return ($item['slug'] ?? null) === $trimmedIdentifier
                || ($item['hash_id'] ?? null) === $trimmedIdentifier;
        });

        if (! is_array($match) || empty($match['hash_id'])) {
            return null;
        }

        return (string) $match['hash_id'];
    }

    private function looksLikeComixCode(string $value): bool
    {
        return preg_match('/^[A-Za-z0-9]{4,8}$/', $value) === 1;
    }

    /**
     * @param  array<string, mixed>  $manga
     * @return array<string, mixed>
     */
    private function normalizeMangaData(array $manga): array
    {
        $termIds = is_array($manga['term_ids'] ?? null)
            ? collect($manga['term_ids'])->map(fn (mixed $id): int => (int) $id)->all()
            : [];

        $termsByType = $this->mapTermsByType($termIds);

        $slug = (string) ($manga['slug'] ?? '');
        $hashId = (string) ($manga['hash_id'] ?? '');

        return [
            'id' => $hashId,
            'slug' => $slug !== '' ? $slug : null,
            'source_manga_id' => $this->toNullableInt($manga['manga_id'] ?? null),
            'title' => (string) ($manga['title'] ?? 'Untitled'),
            'description' => (string) ($manga['synopsis'] ?? ''),
            'cover_image_url' => $this->resolveCoverUrl($manga),
            'banner_image_url' => null,
            'author' => null,
            'artist' => null,
            'type' => isset($manga['type']) ? (string) $manga['type'] : null,
            'status' => $this->mapStatus($manga['status'] ?? null),
            'content_rating' => $this->mapContentRating($manga['is_nsfw'] ?? null),
            'is_nsfw' => (bool) ($manga['is_nsfw'] ?? false),
            'genres' => $termsByType['genre'],
            'themes' => $termsByType['theme'],
            'demographics' => $termsByType['demographic'],
            'formats' => $termsByType['format'],
            'release_year' => $this->resolveReleaseYear($manga),
            'country_of_origin' => $this->mapCountryCode($manga['original_language'] ?? null),
            'rating_average' => $this->toNullableFloat($manga['rated_avg'] ?? null),
            'rating_count' => (int) ($manga['rated_count'] ?? 0),
            'view_count' => (int) ($manga['follows_total'] ?? 0),
            'total_chapters' => $this->resolveTotalChapters($manga),
            'links' => is_array($manga['links'] ?? null) ? $manga['links'] : [],
            'source_name' => 'Comix',
            'source_url' => $slug !== '' ? "https://comix.to/comic/{$slug}" : null,
            'created_at_api' => $this->toNullableDateTime($manga['created_at'] ?? null),
            'updated_at_api' => $this->toNullableDateTime($manga['updated_at'] ?? null),
        ];
    }

    /**
     * @param  array<int, int>  $termIds
     * @return array{genre: array<int, string>, theme: array<int, string>, demographic: array<int, string>, format: array<int, string>}
     */
    private function mapTermsByType(array $termIds): array
    {
        $lookups = $this->getTermLookups();

        return [
            'genre' => $this->resolveTermsByIds($termIds, $lookups['genre']),
            'theme' => $this->resolveTermsByIds($termIds, $lookups['theme']),
            'demographic' => $this->resolveTermsByIds($termIds, $lookups['demographic']),
            'format' => $this->resolveTermsByIds($termIds, $lookups['format']),
        ];
    }

    /**
     * @param  array<int, string>  $lookup
     * @return array<int, string>
     */
    private function resolveTermsByIds(array $termIds, array $lookup): array
    {
        return collect($termIds)
            ->map(fn (int $id): ?string => $lookup[$id] ?? null)
            ->filter(fn (?string $name): bool => $name !== null && $name !== '')
            ->values()
            ->all();
    }

    /**
     * @return array{genre: array<int, string>, theme: array<int, string>, demographic: array<int, string>, format: array<int, string>}
     */
    private function getTermLookups(): array
    {
        return [
            'genre' => $this->getTermLookup('genre'),
            'theme' => $this->getTermLookup('theme'),
            'demographic' => $this->getTermLookup('demographic'),
            'format' => $this->getTermLookup('format'),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function getTermLookup(string $type): array
    {
        try {
            /** @var array<int, string> $lookup */
            $lookup = Cache::remember("comix:terms:{$type}", now()->addHours(12), function () use ($type): array {
                $result = $this->requestResult('/api/terms', ['type' => $type]);

                if (! is_array($result)) {
                    return [];
                }

                $items = is_array($result['items'] ?? null) ? $result['items'] : [];

                return collect($items)
                    ->filter(fn (mixed $term): bool => is_array($term))
                    ->mapWithKeys(fn (array $term): array => [
                        (int) ($term['term_id'] ?? 0) => (string) ($term['title'] ?? ''),
                    ])
                    ->filter(fn (string $name, int $id): bool => $id > 0 && $name !== '')
                    ->all();
            });

            return $lookup;
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * @param  array<string, mixed>  $chapter
     * @return array<string, mixed>
     */
    private function normalizeChapterData(array $chapter): array
    {
        $chapterNumber = $this->toNullableFloat($chapter['number'] ?? null) ?? 0.0;
        $language = (string) ($chapter['language'] ?? 'en');
        $scanlationGroupName = is_array($chapter['scanlation_group'] ?? null)
            ? trim((string) ($chapter['scanlation_group']['name'] ?? ''))
            : '';
        $isOfficial = filter_var($chapter['is_official'] ?? false, FILTER_VALIDATE_BOOL);
        $chapterTitle = trim((string) ($chapter['name'] ?? ''));

        if ($chapterTitle === '') {
            $chapterTitle = $this->resolveChapterFallbackTitle($chapterNumber, $isOfficial, $scanlationGroupName) ?? '';
        }

        return [
            'id' => (string) ($chapter['chapter_id'] ?? ''),
            'chapter_number' => $chapterNumber,
            'chapter_label' => $this->formatChapterNumber($chapterNumber),
            'title' => $chapterTitle !== '' ? $chapterTitle : null,
            'volume' => isset($chapter['volume']) && (float) $chapter['volume'] > 0 ? (string) $chapter['volume'] : null,
            'language' => $language,
            'published_at' => $this->toNullableDateTime($chapter['created_at'] ?? null),
            'page_count' => $this->toNullableInt($chapter['page_count'] ?? $chapter['pages'] ?? null) ?? 0,
            'is_official' => $isOfficial,
            'scanlation_group' => $scanlationGroupName !== '' ? $scanlationGroupName : null,
        ];
    }

    /**
     * @param  array<string, mixed>  $chapter
     * @return array<string, mixed>
     */
    private function normalizeChapterDetails(array $chapter): array
    {
        $images = is_array($chapter['images'] ?? null) ? $chapter['images'] : [];

        return [
            ...$this->normalizeChapterData($chapter),
            'manga_source_id' => $this->toNullableInt($chapter['manga_id'] ?? null),
            'images' => collect($images)
                ->filter(fn (mixed $image): bool => is_array($image) && ! empty($image['url']))
                ->map(fn (array $image): array => [
                    'url' => (string) $image['url'],
                    'width' => $this->toNullableInt($image['width'] ?? null),
                    'height' => $this->toNullableInt($image['height'] ?? null),
                ])
                ->values()
                ->all(),
        ];
    }

    private function formatChapterNumber(float $chapterNumber): string
    {
        if ($chapterNumber <= 0) {
            return '0';
        }

        $formatted = number_format($chapterNumber, 2, '.', '');
        $trimmed = rtrim(rtrim($formatted, '0'), '.');

        return $trimmed === '' ? '0' : $trimmed;
    }

    private function resolveChapterFallbackTitle(
        float $chapterNumber,
        bool $isOfficial,
        string $scanlationGroupName
    ): ?string {
        if ($scanlationGroupName !== '') {
            return $scanlationGroupName;
        }

        if ($isOfficial) {
            return 'Official Release';
        }

        if ($chapterNumber <= 0) {
            return null;
        }

        return 'Chapter '.$this->formatChapterNumber($chapterNumber);
    }

    /**
     * @param  array<string, mixed>  $manga
     */
    private function resolveCoverUrl(array $manga): ?string
    {
        if (! is_array($manga['poster'] ?? null)) {
            return null;
        }

        $poster = $manga['poster'];

        foreach (['large', 'medium', 'small'] as $key) {
            $url = $poster[$key] ?? null;
            if (is_string($url) && $url !== '') {
                return $url;
            }
        }

        return null;
    }

    private function mapStatus(mixed $status): string
    {
        if (! is_string($status) || $status === '') {
            return 'unknown';
        }

        return match (strtolower($status)) {
            'finished' => 'completed',
            'releasing' => 'ongoing',
            'cancelled' => 'cancelled',
            'hiatus', 'on_hiatus' => 'hiatus',
            default => 'unknown',
        };
    }

    private function mapContentRating(mixed $isNsfw): string
    {
        return filter_var($isNsfw, FILTER_VALIDATE_BOOL) ? 'suggestive' : 'safe';
    }

    private function mapCountryCode(mixed $languageCode): ?string
    {
        if (! is_string($languageCode) || $languageCode === '') {
            return null;
        }

        return match (strtolower($languageCode)) {
            'ja' => 'Japan',
            'ko' => 'Korea',
            'zh' => 'China',
            default => strtoupper($languageCode),
        };
    }

    /**
     * @param  array<string, mixed>  $manga
     */
    private function resolveTotalChapters(array $manga): int
    {
        $latest = $this->toNullableFloat($manga['latest_chapter'] ?? null) ?? 0.0;
        $final = $this->toNullableFloat($manga['final_chapter'] ?? null) ?? 0.0;

        $chapterCount = max($latest, $final);

        if ($chapterCount <= 0) {
            return 0;
        }

        return (int) ceil($chapterCount);
    }

    /**
     * @param  array<string, mixed>  $manga
     */
    private function resolveReleaseYear(array $manga): ?int
    {
        $year = $this->toNullableInt($manga['year'] ?? null);
        if ($year !== null && $year > 0) {
            return $year;
        }

        return $this->toNullableInt($manga['start_date'] ?? null);
    }

    private function toNullableFloat(mixed $value): ?float
    {
        if (is_float($value) || is_int($value)) {
            return (float) $value;
        }

        if (! is_string($value) || trim($value) === '' || ! is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }

    private function toNullableInt(mixed $value): ?int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_float($value)) {
            return (int) $value;
        }

        if (! is_string($value) || trim($value) === '' || ! is_numeric($value)) {
            return null;
        }

        return (int) $value;
    }

    private function toNullableDateTime(mixed $value): ?Carbon
    {
        if (is_int($value) || (is_string($value) && ctype_digit($value))) {
            return Carbon::createFromTimestamp((int) $value);
        }

        if (is_string($value) && trim($value) !== '') {
            try {
                return Carbon::parse($value);
            } catch (\Throwable) {
                return null;
            }
        }

        return null;
    }

    private function setIfColumnExists(Manga $manga, string $column, mixed $value): void
    {
        if (! Schema::hasColumn($manga->getTable(), $column)) {
            return;
        }

        $manga->setAttribute($column, $value);
    }

    private function isNotFoundException(\RuntimeException $exception): bool
    {
        return str_contains(strtolower($exception->getMessage()), 'not found');
    }

    /**
     * @param  array<string, mixed>  $query
     * @return mixed
     */
    private function requestResult(string $path, array $query = [])
    {
        $response = $this->http->get($this->buildRequestUrl($path, $query));

        if (! $response->successful()) {
            throw new \RuntimeException('Comix API request failed: '.$response->status());
        }

        $contentType = strtolower((string) $response->header('Content-Type', ''));
        if (! str_contains($contentType, 'application/json')) {
            throw new \RuntimeException('Comix API returned a non-JSON response.');
        }

        $payload = $response->json();

        if (! is_array($payload)) {
            throw new \RuntimeException('Comix API returned an invalid response payload.');
        }

        $logicalStatus = (int) ($payload['status'] ?? 200);

        if ($logicalStatus !== 200) {
            $message = (string) ($payload['message'] ?? 'Unknown error');
            throw new \RuntimeException("Comix API request failed: {$logicalStatus} {$message}");
        }

        return $payload['result'] ?? null;
    }

    /**
     * @param  array<string, mixed>  $query
     */
    private function buildRequestUrl(string $path, array $query): string
    {
        $url = $this->baseUrl.$path;

        if ($query === []) {
            return $url;
        }

        $queryParts = [];

        foreach ($query as $key => $value) {
            $this->appendQueryPart($queryParts, (string) $key, $value);
        }

        if ($queryParts === []) {
            return $url;
        }

        return $url.'?'.implode('&', $queryParts);
    }

    /**
     * @param  array<int, string>  $queryParts
     */
    private function appendQueryPart(array &$queryParts, string $key, mixed $value): void
    {
        if ($value === null) {
            return;
        }

        if (is_array($value)) {
            if (str_ends_with($key, '[]')) {
                foreach ($value as $nestedValue) {
                    $this->appendQueryPart($queryParts, $key, $nestedValue);
                }

                return;
            }

            foreach ($value as $nestedKey => $nestedValue) {
                $this->appendQueryPart($queryParts, $key.'['.(string) $nestedKey.']', $nestedValue);
            }

            return;
        }

        $normalizedValue = match (true) {
            is_bool($value) => $value ? '1' : '0',
            $value instanceof \Stringable => (string) $value,
            default => (string) $value,
        };

        $queryParts[] = rawurlencode($key).'='.rawurlencode($normalizedValue);
    }
}
