<?php

namespace App\Http\Controllers;

use App\Models\UserManga;
use App\Services\WeebdexApiService;
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
        private WeebdexApiService $weebdexApi
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
                    'recommendations' => [],
                ];
            }, 'home-feed'),
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $trendingManga
     * @return array<string, mixed>|null
     */
    private function getFeaturedManga(array $trendingManga): ?array
    {
        if ($trendingManga === []) {
            return null;
        }

        /** @var array<string, mixed>|null $featured */
        $featured = collect($trendingManga)
            ->sortByDesc(fn (array $manga): float => (float) ($manga['rating_average'] ?? 0))
            ->first();

        if (! is_array($featured)) {
            return null;
        }

        return [
            'id' => $featured['id'],
            'title' => $featured['title'],
            'description' => $featured['description'] ?? '',
            'cover_image_url' => $featured['cover_image_url'] ?? null,
            'banner_image_url' => null,
            'rating_average' => $featured['rating_average'] ?? null,
            'total_chapters' => $featured['total_chapters'] ?? 0,
            'genres' => $featured['genres'] ?? [],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getTrendingManga(bool $useImageProxy): array
    {
        $rawTrending = $this->fetchTrendingManga();

        return collect($rawTrending)
            ->map(function (array $manga) use ($useImageProxy): array {
                $genres = collect($manga['genres'] ?? [])
                    ->filter(fn (mixed $genre): bool => is_string($genre) && $genre !== '')
                    ->values()
                    ->map(fn (string $name, int $index): array => [
                        'id' => $index + 1,
                        'name' => $name,
                    ])
                    ->all();

                return [
                    'id' => $manga['id'],
                    'title' => $manga['title'],
                    'description' => $manga['description'] ?? '',
                    'cover_image_url' => ImageUrlBuilder::build($manga['cover_image_url'] ?? null, $useImageProxy),
                    'banner_image_url' => null,
                    'rating_average' => $manga['rating_average'] ?? null,
                    'total_chapters' => $manga['total_chapters'] ?? 0,
                    'genres' => $genres,
                    'status' => $manga['status'] ?? 'unknown',
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fetchTrendingManga(): array
    {
        try {
            return Cache::remember('home:trending', now()->addMinutes(self::TRENDING_CACHE_TTL_MINUTES), function (): array {
                return $this->weebdexApi->getTrendingManga(12)
                    ->map(fn (array $mangaData): array => [
                        'id' => $mangaData['id'],
                        'title' => $mangaData['title'],
                        'description' => $mangaData['description'] ?? '',
                        'cover_image_url' => $mangaData['cover_image_url'] ?? null,
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
     * @return array<int, array<string, mixed>>
     */
    private function getContinueReading(mixed $user, bool $useImageProxy): array
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
            ->filter(fn (UserManga $userManga): bool => $userManga->manga !== null)
            ->map(function (UserManga $userManga) use ($useImageProxy): array {
                $genres = collect($userManga->manga->genres ?? [])
                    ->filter(fn (mixed $genre): bool => is_string($genre) && $genre !== '')
                    ->values()
                    ->all();

                return [
                    'id' => $userManga->manga->id,
                    'title' => $userManga->manga->title,
                    'cover_image_url' => $userManga->manga->getCoverImageUrl($useImageProxy),
                    'genres' => $genres,
                    'current_chapter_id' => $userManga->currentChapter?->id,
                    'current_chapter' => (float) ($userManga->currentChapter?->chapter_number ?? 1),
                    'progress_percentage' => (float) $userManga->progress_percentage,
                    'last_read_at' => $userManga->last_read_at?->toISOString(),
                ];
            })
            ->values()
            ->toArray();
    }
}
