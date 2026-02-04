<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Chapter;
use App\Models\Manga;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MangaDexApiService
{
    private const DEFAULT_LIMIT = 20;

    private readonly PendingRequest $http;

    private readonly string $baseUrl;

    private readonly string $coverUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.mangadex.base_url');
        $this->coverUrl = config('services.mangadex.cover_url');
        $this->http = Http::withOptions([
            'timeout' => 30,
        ])->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Search for manga with optional filters
     *
     * @param  array<string, mixed>  $filters
     * @return Collection<int, array<string, mixed>>
     */
    public function searchManga(array $filters = []): Collection
    {
        $params = [
            'limit' => $filters['limit'] ?? self::DEFAULT_LIMIT,
            'offset' => $filters['offset'] ?? 0,
        ];

        // Add reference expansions to get cover art, author, and artist
        $includes = ['cover_art', 'author', 'artist'];
        foreach ($includes as $index => $include) {
            $params["includes[{$index}]"] = $include;
        }

        // Add optional filters
        if (! empty($filters['title'])) {
            $params['title'] = $filters['title'];
        }

        // Add array parameters properly
        $contentRatings = $filters['contentRating'] ?? ['safe', 'suggestive'];
        foreach ($contentRatings as $index => $rating) {
            $params["contentRating[{$index}]"] = $rating;
        }

        // Add included tags
        if (! empty($filters['includedTags'])) {
            foreach ($filters['includedTags'] as $index => $tag) {
                $params["includedTags[{$index}]"] = $tag;
            }
        }

        // Add excluded tags
        if (! empty($filters['excludedTags'])) {
            foreach ($filters['excludedTags'] as $index => $tag) {
                $params["excludedTags[{$index}]"] = $tag;
            }
        }

        // Add order parameters
        $params = array_merge($params, $this->buildOrderParams($filters['order'] ?? []));

        $response = $this->http->get("{$this->baseUrl}/manga", $params);

        if (! $response->successful()) {
            throw new \Exception('MangaDex API request failed: '.$response->body());
        }

        $data = $response->json('data') ?? [];

        $mangaIds = collect($data)
            ->pluck('id')
            ->filter()
            ->values()
            ->all();

        $statsByMangaId = $this->getMangaStatistics($mangaIds);

        return collect($data)->map(fn (array $manga) => $this->normalizeMangaData($manga, $statsByMangaId));
    }

    /**
     * Fetch manga statistics (ratings / follows) in batch with caching.
     *
     * @param  array<int, string>  $mangaIds
     * @return array<string, mixed>
     */
    private function getMangaStatistics(array $mangaIds): array
    {
        $mangaIds = array_values(array_filter(array_unique($mangaIds)));
        if (empty($mangaIds)) {
            return [];
        }

        $cacheTtl = now()->addHours(6);
        $statsById = [];
        $missingIds = [];

        foreach ($mangaIds as $id) {
            $cached = Cache::get("mangadex:stats:manga:{$id}");
            if (is_array($cached)) {
                $statsById[$id] = $cached;
            } else {
                $missingIds[] = $id;
            }
        }

        if (empty($missingIds)) {
            return $statsById;
        }

        $params = [];
        foreach ($missingIds as $index => $id) {
            $params["manga[{$index}]"] = $id;
        }

        try {
            $response = $this->http->get("{$this->baseUrl}/statistics/manga", $params);
            if (! $response->successful()) {
                return $statsById;
            }

            $statistics = $response->json('statistics') ?? [];
            if (! is_array($statistics)) {
                return $statsById;
            }

            foreach ($statistics as $id => $stat) {
                if (! is_array($stat)) {
                    continue;
                }

                Cache::put("mangadex:stats:manga:{$id}", $stat, $cacheTtl);
                $statsById[$id] = $stat;
            }
        } catch (\Throwable) {
            return $statsById;
        }

        return $statsById;
    }

    /**
     * Get trending manga ordered by followed count
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getTrendingManga(int $limit = 10): Collection
    {
        return $this->searchManga([
            'limit' => $limit,
            'contentRating' => ['safe', 'suggestive'],
            'order' => ['followedCount' => 'desc'],
        ]);
    }

    /**
     * Get highly rated manga
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getTopRatedManga(int $limit = 10): Collection
    {
        return $this->searchManga([
            'limit' => $limit,
            'contentRating' => ['safe', 'suggestive'],
            'order' => ['rating' => 'desc'],
        ]);
    }

    /**
     * Get recently updated manga
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getRecentlyUpdatedManga(int $limit = 10): Collection
    {
        return $this->searchManga([
            'limit' => $limit,
            'contentRating' => ['safe', 'suggestive'],
            'order' => ['latestUploadedChapter' => 'desc'],
        ]);
    }

    /**
     * Get manga details by ID
     *
     * @return array<string, mixed>
     */
    public function getMangaById(string $mangaId): array
    {
        $params = [];
        foreach (['cover_art', 'author', 'artist'] as $index => $include) {
            $params["includes[{$index}]"] = $include;
        }

        $response = $this->http->get("{$this->baseUrl}/manga/{$mangaId}", $params);

        if (! $response->successful()) {
            throw new \Exception('MangaDex API request failed: '.$response->body());
        }

        return $this->normalizeMangaData($response->json('data'));
    }

    /**
     * Get manga chapters feed
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getMangaFeed(string $mangaId, int $limit = 100): Collection
    {
        $params = [
            'limit' => $limit,
            'order[chapter]' => 'asc',
        ];

        foreach (['en'] as $index => $language) {
            $params["translatedLanguage[{$index}]"] = $language;
        }

        $response = $this->http->get("{$this->baseUrl}/manga/{$mangaId}/feed", $params);

        if (! $response->successful()) {
            throw new \Exception('MangaDex API request failed: '.$response->body());
        }

        $chapters = $response->json('data') ?? [];

        return collect($chapters)->map(fn (array $chapter) => [
            'id' => $chapter['id'],
            'chapter_number' => (int) ($chapter['attributes']['chapter'] ?? 0),
            'title' => $chapter['attributes']['title'] ?? null,
            'volume' => $chapter['attributes']['volume'] ?? null,
            'language' => $chapter['attributes']['translatedLanguage'] ?? 'en',
            'published_at' => $chapter['attributes']['publishAt'] ?? null,
            'pages' => $chapter['attributes']['pages'] ?? 0,
        ]);
    }

    /**
     * Get chapter pages
     *
     * @return array<string, mixed>|null
     */
    public function getChapterPages(string $chapterId): ?array
    {
        $response = $this->http->get("{$this->baseUrl}/at-home/server/{$chapterId}");

        if (! $response->successful()) {
            return null;
        }

        $baseUrl = $response->json('baseUrl');
        $chapter = $response->json('chapter');

        if (! $baseUrl || ! $chapter) {
            return null;
        }

        return [
            'base_url' => $baseUrl,
            'hash' => $chapter['hash'],
            'pages' => $chapter['data'],
            'data_saver' => $chapter['dataSaver'],
        ];
    }

    /**
     * Sync manga from MangaDex to local database
     */
    public function syncMangaToDatabase(array $mangaData): Manga
    {
        // First try to find by external_id
        $manga = Manga::where('external_id', $mangaData['id'])->first();

        // If not found by external_id, try to find by slug
        if (! $manga) {
            $slug = Str::slug($mangaData['title']);
            $manga = Manga::where('slug', $slug)->first();
        }

        // If still not found, create a new one
        if (! $manga) {
            $manga = new Manga;
        }

        // Update all fields
        $manga->external_id = $mangaData['id'];
        $manga->title = $mangaData['title'];
        $manga->slug = Str::slug($mangaData['title']);
        $manga->description = $mangaData['description'] ?? '';
        $manga->cover_image_url = $mangaData['cover_image_url'] ?? null;
        $manga->banner_image_url = $mangaData['banner_image_url'] ?? null;
        $manga->author = $mangaData['author'] ?? null;
        $manga->artist = $mangaData['artist'] ?? null;
        $manga->status = $mangaData['status'] ?? 'unknown';
        $manga->content_rating = $mangaData['content_rating'] ?? 'safe';
        $manga->genres = $mangaData['genres'] ?? [];
        $manga->themes = $mangaData['themes'] ?? [];
        $manga->demographics = $mangaData['demographics'] ?? [];
        $manga->demographics_data = $mangaData['demographics_data'] ?? [];
        $manga->content_tags = $mangaData['content_tags'] ?? [];
        $manga->format_tags = $mangaData['format_tags'] ?? [];
        $manga->release_year = $mangaData['release_year'] ?? null;
        $manga->country_of_origin = $mangaData['country_of_origin'] ?? null;
        $manga->original_language = $mangaData['original_language'] ?? null;
        $manga->rating_average = $mangaData['rating_average'] ?? null;
        $manga->rating_count = $mangaData['rating_count'] ?? 0;
        $manga->total_chapters = $mangaData['total_chapters'] ?? 0;
        $manga->last_fetched_at = now();
        $manga->source_name = 'MangaDex';
        $manga->source_url = "https://mangadex.org/title/{$mangaData['id']}";

        // New fields from MangaDex API
        $manga->alt_titles = $mangaData['alt_titles'] ?? [];
        $manga->last_volume = $mangaData['last_volume'] ?? null;
        $manga->last_chapter = $mangaData['last_chapter'] ?? null;
        $manga->links = $mangaData['links'] ?? [];
        $manga->available_translated_languages = $mangaData['available_translated_languages'] ?? [];
        $manga->is_locked = $mangaData['is_locked'] ?? false;
        $manga->api_version = $mangaData['api_version'] ?? 1;
        $manga->chapter_numbers_reset_on_new_volume = $mangaData['chapter_numbers_reset_on_new_volume'] ?? false;
        $manga->state = $mangaData['state'] ?? 'published';
        $manga->latest_uploaded_chapter_uuid = $mangaData['latest_uploaded_chapter'] ?? null;
        $manga->created_at_api = $mangaData['created_at_api'] ?? null;
        $manga->updated_at_api = $mangaData['updated_at_api'] ?? null;

        $manga->save();

        return $manga;
    }

    /**
     * Sync chapters for a manga
     *
     * @param  Collection<int, array<string, mixed>>  $chapters
     */
    public function syncChapters(Manga $manga, Collection $chapters): void
    {
        foreach ($chapters as $chapterData) {
            Chapter::updateOrCreate(
                [
                    'manga_id' => $manga->id,
                    'chapter_number' => $chapterData['chapter_number'],
                ],
                [
                    'title' => $chapterData['title'] ?? "Chapter {$chapterData['chapter_number']}",
                    'volume' => $chapterData['volume'] ?? null,
                    'language' => $chapterData['language'] ?? 'en',
                    'published_at' => $chapterData['published_at'] ?? now(),
                    'total_pages' => $chapterData['pages'] ?? 0,
                ]
            );
        }

        $manga->update(['total_chapters' => $manga->chapters()->count()]);
    }

    /**
     * Normalize manga data from MangaDex API
     *
     * @return array<string, mixed>
     */
    private function normalizeMangaData(array $manga, array $statsByMangaId = []): array
    {
        $attributes = $manga['attributes'] ?? [];
        $relationships = $manga['relationships'] ?? [];

        $stats = $statsByMangaId[$manga['id']] ?? null;
        $ratingAverage = is_array($stats) ? ($stats['rating']['average'] ?? null) : null;
        $ratingBayesian = is_array($stats) ? ($stats['rating']['bayesian'] ?? null) : null;
        $rating = $ratingAverage ?? $ratingBayesian;

        // Extract cover art filename
        $coverArt = collect($relationships)->firstWhere('type', 'cover_art');
        $coverFileName = $coverArt['attributes']['fileName'] ?? null;

        // Extract author
        $author = collect($relationships)->firstWhere('type', 'author');
        $authorName = $author['attributes']['name'] ?? null;

        // Extract artist
        $artist = collect($relationships)->firstWhere('type', 'artist');
        $artistName = $artist['attributes']['name'] ?? null;

        // Get title (prefer English)
        $title = $attributes['title']['en']
            ?? $attributes['title'][array_key_first($attributes['title'] ?? [])]
            ?? 'Untitled';

        // Get description (prefer English)
        $description = $attributes['description']['en']
            ?? $attributes['description'][array_key_first($attributes['description'] ?? [])]
            ?? '';

        // Build cover image URL
        $coverImageUrl = $coverFileName
            ? "{$this->coverUrl}/{$manga['id']}/{$coverFileName}"
            : null;

        // Extract tags as genres, themes, content, and format
        $tags = $attributes['tags'] ?? [];
        $genres = [];
        $themes = [];
        $contentTags = [];
        $formatTags = [];

        foreach ($tags as $tag) {
            $tagGroup = $tag['attributes']['group'] ?? '';
            $tagName = $tag['attributes']['name']['en'] ?? '';

            match ($tagGroup) {
                'genre' => $genres[] = $tagName,
                'theme' => $themes[] = $tagName,
                'content' => $contentTags[] = $tagName,
                'format' => $formatTags[] = $tagName,
                default => null,
            };
        }

        // Extract demographic (singular value from API, but stored as array)
        $publicationDemographic = $attributes['publicationDemographic'] ?? null;
        $demographics = $publicationDemographic ? [$publicationDemographic] : [];

        // Get alternative titles
        $altTitles = $attributes['altTitles'] ?? [];

        // Extract last chapter and derive total chapters (numeric only)
        $rawLastChapter = $attributes['lastChapter'] ?? null;
        $lastChapter = $rawLastChapter !== null ? trim((string) $rawLastChapter) : null;
        $lastChapter = $lastChapter === '' ? null : $lastChapter;
        $totalChapters = is_numeric($lastChapter) ? (int) $lastChapter : 0;

        return [
            'id' => $manga['id'],
            'title' => $title,
            'alt_titles' => $altTitles,
            'description' => $description,
            'cover_image_url' => $coverImageUrl,
            'banner_image_url' => null, // MangaDex doesn't provide banners
            'author' => $authorName,
            'artist' => $artistName ?? $authorName,
            'status' => $attributes['status'] ?? 'unknown',
            'content_rating' => $attributes['contentRating'] ?? 'safe',
            'genres' => $genres,
            'themes' => $themes,
            'content_tags' => $contentTags,
            'format_tags' => $formatTags,
            'demographics' => $demographics,
            'demographics_data' => $demographics,
            'release_year' => $attributes['year'] ?? null,
            'original_language' => $attributes['originalLanguage'] ?? null,
            'country_of_origin' => $this->mapCountryOfOrigin($attributes['originalLanguage'] ?? 'ja'),
            'last_volume' => $attributes['lastVolume'] ?? null,
            'last_chapter' => $lastChapter,
            'links' => $attributes['links'] ?? [],
            'available_translated_languages' => $attributes['availableTranslatedLanguages'] ?? [],
            'is_locked' => $attributes['isLocked'] ?? false,
            'api_version' => $attributes['version'] ?? 1,
            'chapter_numbers_reset_on_new_volume' => $attributes['chapterNumbersResetOnNewVolume'] ?? false,
            'state' => $attributes['state'] ?? 'published',
            'latest_uploaded_chapter' => $attributes['latestUploadedChapter'] ?? null,
            'created_at_api' => $attributes['createdAt'] ?? null,
            'updated_at_api' => $attributes['updatedAt'] ?? null,
            'rating_average' => $rating,
            'rating_count' => 0,
            'total_chapters' => $totalChapters,
        ];
    }

    /**
     * Build order parameters for MangaDex API
     *
     * @param  array<string, string>  $order
     * @return array<string, string>
     */
    private function buildOrderParams(array $order): array
    {
        $params = [];
        foreach ($order as $field => $direction) {
            $params["order[{$field}]"] = $direction;
        }

        return $params;
    }

    /**
     * Map language code to country of origin
     */
    private function mapCountryOfOrigin(string $language): string
    {
        $mapping = [
            'ja' => 'Japan',
            'ko' => 'Korea',
            'zh' => 'China',
            'zh-hk' => 'China',
            'en' => 'Other',
        ];

        return $mapping[$language] ?? 'Other';
    }
}
