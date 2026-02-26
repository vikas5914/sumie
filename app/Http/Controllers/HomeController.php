<?php

namespace App\Http\Controllers;

use App\Models\UserManga;
use App\Services\ComickApiService;
use App\Support\ImageUrlBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    private const TRENDING_CACHE_TTL_MINUTES = 10;

    public function __construct(
        private ComickApiService $comickApi
    ) {}

    public function index(Request $request): Response
    {
        $user = $request->user();
        $useImageProxy = true;

        return Inertia::render('home', [
            'homeFeed' => Inertia::defer(function () use ($user, $useImageProxy): array {
                $trendingManga = $this->getTrendingManga($useImageProxy);
                $continueReading = $this->getContinueReading($user, $useImageProxy);

                return [
                    'featuredManga' => $this->getFeaturedManga($trendingManga),
                    'trendingManga' => $trendingManga,
                    'continueReading' => $continueReading,
                    'recommendations' => $this->getRecommendations($user, $continueReading, $trendingManga),
                ];
            }, 'home-feed'),
        ]);
    }

    /**
     * Get featured manga for hero section.
     */
    private function getFeaturedManga(array $trendingManga): ?array
    {
        if (empty($trendingManga)) {
            return null;
        }

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
     * Get trending manga from the upstream API with caching.
     */
    private function getTrendingManga(bool $useImageProxy): array
    {
        $rawTrending = $this->fetchTrendingManga();

        return collect($rawTrending)
            ->map(fn (array $manga): array => [
                'id' => $manga['id'],
                'title' => $manga['title'],
                'description' => $manga['description'] ?? '',
                'cover_image_url' => ImageUrlBuilder::build($manga['cover_image_url'] ?? null, $useImageProxy),
                'banner_image_url' => ImageUrlBuilder::build($manga['banner_image_url'] ?? null, $useImageProxy),
                'rating_average' => $manga['rating_average'] ?? null,
                'total_chapters' => $manga['total_chapters'] ?? 0,
                'genres' => $manga['genres'] ?? [],
                'status' => $manga['status'] ?? 'unknown',
            ])
            ->values()
            ->toArray();
    }

    /**
     * Fetch trending manga with a cache layer.
     */
    private function fetchTrendingManga(): array
    {
        try {
            return Cache::remember('home:trending', now()->addMinutes(self::TRENDING_CACHE_TTL_MINUTES), function (): array {
                return $this->comickApi->getTrendingManga(12)
                    ->map(fn (array $mangaData): array => [
                        'id' => $mangaData['id'],
                        'title' => $mangaData['title'],
                        'description' => $mangaData['description'] ?? '',
                        'cover_image_url' => $mangaData['cover_image_url'] ?? null,
                        'banner_image_url' => $mangaData['banner_image_url'] ?? null,
                        'status' => $mangaData['status'] ?? 'unknown',
                        'genres' => $mangaData['genres'] ?? [],
                        'rating_average' => $mangaData['rating_average'] ?? null,
                        'total_chapters' => $mangaData['total_chapters'] ?? 0,
                    ])
                    ->values()
                    ->toArray();
            });
        } catch (\Throwable $exception) {
            Log::error('Failed to fetch trending manga.', [
                'error' => $exception->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Get continue reading section.
     */
    private function getContinueReading($user, bool $useImageProxy): array
    {
        if (! $user) {
            return [];
        }

        return UserManga::with(['manga', 'currentChapter'])
            ->where('user_id', $user->id)
            ->where('status', 'reading')
            ->whereNotNull('last_read_at')
            ->orderBy('last_read_at', 'desc')
            ->take(5)
            ->get()
            ->map(fn ($userManga) => [
                'id' => $userManga->manga->id,
                'title' => $userManga->manga->title,
                'cover_image_url' => $userManga->manga->getCoverImageUrl($useImageProxy),
                'genres' => $userManga->manga->genres,
                'current_chapter_id' => $userManga->currentChapter?->external_id,
                'current_chapter' => $userManga->currentChapter?->chapter_number ?? 1,
                'progress_percentage' => $userManga->progress_percentage,
                'last_read_at' => $userManga->last_read_at?->toISOString(),
            ])
            ->toArray();
    }

    /**
     * Get recommendations based on reading history.
     */
    private function getRecommendations($user, array $continueReading, array $trendingManga): array
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

        return collect($trendingManga)
            ->reject(fn (array $manga) => $excludedIds->contains($manga['id']))
            ->filter(function (array $manga) use ($userGenres): bool {
                $mangaGenres = collect($manga['genres'] ?? []);

                return $mangaGenres->intersect($userGenres)->isNotEmpty();
            })
            ->take(3)
            ->map(fn (array $manga): array => [
                'id' => $manga['id'],
                'title' => $manga['title'],
                'cover_image_url' => $manga['cover_image_url'] ?? null,
                'genres' => $manga['genres'] ?? [],
            ])
            ->values()
            ->toArray();
    }
}
