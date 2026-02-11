<?php

namespace App\Http\Controllers;

use App\Models\UserManga;
use App\Services\ComickApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    private const CACHE_TTL_MINUTES = 60; // Cache for 1 hour

    private const CACHE_KEY_TRENDING = 'home:trending_manga';

    private const CACHE_KEY_LAST_FETCH = 'home:last_fetched_at';

    public function __construct(
        private ComickApiService $comickApi
    ) {}

    public function index(Request $request): Response
    {
        $user = $request->user();

        $lastFetchedAt = Cache::get(self::CACHE_KEY_LAST_FETCH);
        $isDataStale = $this->isCacheStale();

        return Inertia::render('home', [
            'homeFeed' => Inertia::defer(function () use ($user): array {
                $trendingManga = $this->getTrendingManga();
                $continueReading = $this->getContinueReading($user);

                return [
                    'featuredManga' => $this->getFeaturedManga($trendingManga),
                    'trendingManga' => $trendingManga,
                    'continueReading' => $continueReading,
                    'recommendations' => $this->getRecommendations($user, $continueReading),
                ];
            }, 'home-feed'),
            // Meta information for the frontend
            'meta' => [
                'isDataStale' => $isDataStale,
                'hasCachedData' => $this->hasTrendingCache(),
                'lastFetchedAt' => $lastFetchedAt,
            ],
        ]);
    }

    /**
     * Background refresh endpoint - called by frontend when data is stale
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            $this->fetchAndCacheFreshData();

            return response()->json([
                'success' => true,
                'message' => 'Data refreshed successfully',
                'lastFetchedAt' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Background refresh failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh data',
                'error' => $e->getMessage(),
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }

    /**
     * Check if cached data is stale (older than CACHE_TTL_MINUTES)
     */
    private function isCacheStale(): bool
    {
        $lastFetch = Cache::get(self::CACHE_KEY_LAST_FETCH);

        if (! $lastFetch) {
            return true;
        }

        return $lastFetch->diffInMinutes(now()) > self::CACHE_TTL_MINUTES;
    }

    private function hasTrendingCache(): bool
    {
        $cachedTrending = Cache::get(self::CACHE_KEY_TRENDING);

        return is_array($cachedTrending) && ! empty($cachedTrending);
    }

    /**
     * Get cached trending manga or fetch fresh data
     */
    private function getCachedTrendingManga(): array
    {
        $cached = Cache::get(self::CACHE_KEY_TRENDING);

        if (! empty($cached)) {
            return $cached;
        }

        // If no cache, fetch and cache fresh data
        try {
            $this->fetchAndCacheFreshData();

            return Cache::get(self::CACHE_KEY_TRENDING) ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to fetch trending manga: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Fetch fresh data from Comix API and cache it
     */
    private function fetchAndCacheFreshData(): void
    {
        // Fetch trending manga
        $mangas = $this->comickApi->getTrendingManga(12);

        $cachedData = $mangas->map(fn ($mangaData) => [
            'id' => $mangaData['id'],
            'slug' => $mangaData['slug'] ?? null,
            'title' => $mangaData['title'],
            'description' => $mangaData['description'] ?? '',
            'cover_image_url' => $mangaData['cover_image_url'] ?? null,
            'banner_image_url' => $mangaData['banner_image_url'] ?? null,
            'author' => $mangaData['author'] ?? null,
            'artist' => $mangaData['artist'] ?? null,
            'type' => $mangaData['type'] ?? null,
            'status' => $mangaData['status'] ?? 'unknown',
            'content_rating' => $mangaData['content_rating'] ?? 'safe',
            'is_nsfw' => $mangaData['is_nsfw'] ?? false,
            'genres' => $mangaData['genres'] ?? [],
            'themes' => $mangaData['themes'] ?? [],
            'demographics' => $mangaData['demographics'] ?? [],
            'formats' => $mangaData['formats'] ?? [],
            'release_year' => $mangaData['release_year'] ?? null,
            'rating_average' => $mangaData['rating_average'] ?? null,
            'rating_count' => $mangaData['rating_count'] ?? 0,
            'total_chapters' => $mangaData['total_chapters'] ?? 0,
            'links' => $mangaData['links'] ?? [],
        ])->toArray();

        Cache::put(self::CACHE_KEY_TRENDING, $cachedData, now()->addMinutes(self::CACHE_TTL_MINUTES));
        Cache::put(self::CACHE_KEY_LAST_FETCH, now(), now()->addMinutes(self::CACHE_TTL_MINUTES));

        Log::info('Fetched and cached '.$mangas->count().' manga from Comix');
    }

    /**
     * Get featured manga for hero section
     */
    private function getFeaturedManga(array $trendingManga): ?array
    {
        if (empty($trendingManga)) {
            return null;
        }

        // Get highest rated manga from trending as featured
        $featured = collect($trendingManga)
            ->sortByDesc('rating_average')
            ->first();

        if (! $featured) {
            return null;
        }

        return [
            'id' => $featured['id'],
            'title' => $featured['title'],
            'description' => $featured['description'] ?? '',
            'cover_image_url' => $featured['cover_image_url'] ?? null,
            'banner_image_url' => $featured['banner_image_url'] ?? null,
            'rating_average' => $featured['rating_average'] ?? null,
            'total_chapters' => $featured['total_chapters'] ?? 0,
            'genres' => $featured['genres'] ?? [],
        ];
    }

    /**
     * Get trending manga
     */
    private function getTrendingManga(): array
    {
        return collect($this->getCachedTrendingManga())
            ->map(fn ($manga) => [
                'id' => $manga['id'],
                'title' => $manga['title'],
                'description' => $manga['description'] ?? '',
                'cover_image_url' => $this->getProxiedCoverUrl($manga['cover_image_url'] ?? null),
                'banner_image_url' => isset($manga['banner_image_url'])
                    ? $this->getProxiedCoverUrl($manga['banner_image_url'])
                    : null,
                'rating_average' => $manga['rating_average'] ?? null,
                'total_chapters' => $manga['total_chapters'] ?? 0,
                'genres' => $manga['genres'] ?? [],
                'status' => $manga['status'] ?? 'unknown',
            ])
            ->values()
            ->toArray();
    }

    /**
     * Get continue reading section
     */
    private function getContinueReading($user): array
    {
        if (! $user) {
            return [];
        }

        return UserManga::with(['manga', 'currentChapter'])
            ->where('user_id', $user->id)
            ->where('status', 'reading')
            ->orderBy('last_read_at', 'desc')
            ->take(5)
            ->get()
            ->map(fn ($userManga) => [
                'id' => $userManga->manga->id,
                'title' => $userManga->manga->title,
                'cover_image_url' => $userManga->manga->getProxiedCoverUrl(),
                'genres' => $userManga->manga->genres,
                'current_chapter' => $userManga->currentChapter?->chapter_number ?? 1,
                'progress_percentage' => $userManga->progress_percentage,
                'last_read_at' => $userManga->last_read_at?->diffForHumans(),
            ])
            ->toArray();
    }

    /**
     * Get recommendations based on reading history
     */
    private function getRecommendations($user, array $continueReading): array
    {
        if (! $user || empty($continueReading)) {
            return [];
        }

        $userGenres = collect($continueReading)
            ->pluck('genres')
            ->flatten()
            ->unique()
            ->take(3);

        $excludedIds = collect($continueReading)->pluck('id');

        // Get recommendations from cached trending data
        $trending = $this->getCachedTrendingManga();

        return collect($trending)
            ->reject(fn ($manga) => $excludedIds->contains($manga['id']))
            ->filter(function ($manga) use ($userGenres) {
                $mangaGenres = collect($manga['genres'] ?? []);

                return $mangaGenres->intersect($userGenres)->isNotEmpty();
            })
            ->take(3)
            ->map(fn ($manga) => [
                'id' => $manga['id'],
                'title' => $manga['title'],
                'cover_image_url' => $this->getProxiedCoverUrl($manga['cover_image_url']),
                'genres' => $manga['genres'],
            ])
            ->toArray();
    }

    /**
     * Generate proxied cover image URL
     */
    private function getProxiedCoverUrl(?string $coverUrl): ?string
    {
        if (! $coverUrl) {
            return null;
        }

        return route('image.proxy', ['encodedUrl' => base64_encode($coverUrl)]);
    }
}
