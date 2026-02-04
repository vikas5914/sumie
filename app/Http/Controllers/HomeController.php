<?php

namespace App\Http\Controllers;

use App\Models\Manga;
use App\Models\UserManga;
use App\Services\MangaDexApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class HomeController extends Controller
{
    private const CACHE_STALE_MINUTES = 60; // Data is stale after 1 hour

    public function __construct(
        private MangaDexApiService $mangaDexApi
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();

        // Check if we have any manga data with cover images
        $hasCachedData = Manga::whereNotNull('cover_image_url')->exists();
        $isDataStale = $this->isDataStale();

        // If no cached data at all, we need to fetch immediately (blocking)
        if (! $hasCachedData) {
            try {
                $this->fetchFreshData();
            } catch (\Exception $e) {
                Log::error('Failed to fetch initial manga data: '.$e->getMessage());
            }
        }

        // Get cached data (either fresh from above or previously cached)
        $featuredManga = $this->getFeaturedManga();
        $trendingManga = $this->getTrendingManga();
        $continueReading = $this->getContinueReading($user);
        $recommendations = $this->getRecommendations($user, $continueReading);

        return Inertia::render('home', [
            'featuredManga' => $featuredManga,
            'trendingManga' => $trendingManga,
            'continueReading' => $continueReading,
            'recommendations' => $recommendations,
            // Meta information for the frontend
            'meta' => [
                'isDataStale' => $isDataStale,
                'hasCachedData' => $hasCachedData,
                'lastFetchedAt' => Manga::whereNotNull('last_fetched_at')
                    ->latest('last_fetched_at')
                    ->value('last_fetched_at'),
            ],
        ]);
    }

    /**
     * Background refresh endpoint - called by frontend when data is stale
     */
    public function refresh(Request $request)
    {
        try {
            $this->fetchFreshData();

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
     * Check if cached data is stale (older than CACHE_STALE_MINUTES)
     */
    private function isDataStale(): bool
    {
        $lastFetch = Manga::whereNotNull('last_fetched_at')
            ->latest('last_fetched_at')
            ->value('last_fetched_at');

        if (! $lastFetch) {
            return true;
        }

        return $lastFetch->diffInMinutes(now()) > self::CACHE_STALE_MINUTES;
    }

    /**
     * Fetch fresh data from MangaDex API
     */
    private function fetchFreshData(): void
    {
        // Fetch trending manga
        $mangas = $this->mangaDexApi->getTrendingManga(12);

        foreach ($mangas as $mangaData) {
            $this->mangaDexApi->syncMangaToDatabase($mangaData);
        }

        Log::info('Fetched '.$mangas->count().' manga from MangaDex');
    }

    /**
     * Get featured manga for hero section
     */
    private function getFeaturedManga(): ?array
    {
        $featured = Manga::whereNotNull('cover_image_url')
            ->orderBy('rating_average', 'desc')
            ->first();

        if (! $featured) {
            return null;
        }

        return [
            'id' => $featured->id,
            'title' => $featured->title,
            'description' => $featured->description,
            'cover_image_url' => $featured->getProxiedCoverUrl(),
            'banner_image_url' => $featured->getProxiedBannerUrl(),
            'rating_average' => $featured->rating_average,
            'total_chapters' => $featured->total_chapters,
            'genres' => $featured->genres,
        ];
    }

    /**
     * Get trending manga
     */
    private function getTrendingManga(): array
    {
        // Get trending manga with cover images
        $mangas = Manga::whereNotNull('cover_image_url')
            ->where('status', '!=', 'cancelled')
            ->orderBy('view_count', 'desc')
            ->take(6)
            ->get();

        return $mangas->map(fn ($manga) => [
            'id' => $manga->id,
            'title' => $manga->title,
            'cover_image_url' => $manga->getProxiedCoverUrl(),
            'rating_average' => $manga->rating_average,
            'genres' => $manga->genres,
            'status' => $manga->status,
        ])->toArray();
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
            ->pluck('manga.genres')
            ->flatten()
            ->unique()
            ->take(3);

        $excludedIds = collect($continueReading)->pluck('id');

        return Manga::whereNotIn('id', $excludedIds)
            ->where(function ($query) use ($userGenres) {
                foreach ($userGenres as $genre) {
                    $query->orWhereJsonContains('genres', $genre);
                }
            })
            ->take(3)
            ->get()
            ->map(fn ($manga) => [
                'id' => $manga->id,
                'title' => $manga->title,
                'cover_image_url' => $manga->getProxiedCoverUrl(),
                'genres' => $manga->genres,
            ])
            ->toArray();
    }
}
