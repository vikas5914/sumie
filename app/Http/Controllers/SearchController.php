<?php

namespace App\Http\Controllers;

use App\Services\ComickApiService;
use App\Support\ImageUrlBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
    private const SEARCH_RESULT_LIMIT = 28;

    private const SEARCH_CACHE_TTL_SECONDS = 120;

    public function index(Request $request, ComickApiService $comick): Response
    {
        $useImageProxy = true;
        $query = trim((string) $request->query('q', ''));
        $filter = strtolower(trim((string) $request->query('filter', 'all')));
        $filter = $filter === '' ? 'all' : $filter;

        $filter = match ($filter) {
            'all', 'manga', 'manhwa', 'completed', 'oneshot' => $filter,
            default => 'all',
        };

        $results = collect();

        if ($query !== '' && mb_strlen($query) >= 2) {
            try {
                $remote = $this->fetchSearchResults($comick, $query);

                $filtered = $this->applyFilter($remote, $filter);

                $results = $filtered
                    ->map(fn (array $mangaData) => [
                        'id' => $mangaData['id'],
                        'title' => $mangaData['title'],
                        'cover_image_url' => ImageUrlBuilder::build($mangaData['cover_image_url'] ?? null, $useImageProxy),
                        'author' => $mangaData['author'],
                        'rating_average' => $mangaData['rating_average'],
                        'status' => $mangaData['status'],
                        'genres' => $mangaData['genres'],
                    ])
                    ->values();
            } catch (\Throwable $exception) {
                Log::warning('Search request failed.', [
                    'query' => $query,
                    'filter' => $filter,
                    'error' => $exception->getMessage(),
                ]);
                $results = collect();
            }
        }

        return Inertia::render('search', [
            'query' => $query,
            'results' => $results,
            'filter' => $filter,
        ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function fetchSearchResults(ComickApiService $comick, string $query): Collection
    {
        $cacheKey = $this->buildSearchCacheKey($query);

        $results = Cache::remember($cacheKey, now()->addSeconds(self::SEARCH_CACHE_TTL_SECONDS), function () use ($comick, $query): Collection {
            return $comick->searchManga([
                'title' => $query,
                'limit' => self::SEARCH_RESULT_LIMIT,
                'showall' => false,
                'genres_mode' => 'and',
            ]);
        });

        return $results instanceof Collection ? $results : collect();
    }

    private function buildSearchCacheKey(string $query): string
    {
        return 'search:query:'.sha1(mb_strtolower(trim($query)));
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $results
     * @return Collection<int, array<string, mixed>>
     */
    private function applyFilter(Collection $results, string $filter): Collection
    {
        if ($filter === 'all') {
            return $results;
        }

        return $results->filter(function (array $manga) use ($filter): bool {
            if ($filter === 'completed') {
                return ($manga['status'] ?? null) === 'completed';
            }

            if ($filter === 'manga') {
                return strtolower((string) ($manga['type'] ?? '')) === 'manga';
            }

            if ($filter === 'manhwa') {
                return strtolower((string) ($manga['type'] ?? '')) === 'manhwa';
            }

            if ($filter === 'oneshot') {
                $totalChapters = (int) ($manga['total_chapters'] ?? 0);
                $formats = is_array($manga['formats'] ?? null) ? $manga['formats'] : [];
                $hasOneshotFormat = collect($formats)->contains(function ($format): bool {
                    return in_array(strtolower((string) $format), ['oneshot', 'one shot'], true);
                });

                return $totalChapters <= 1 || $hasOneshotFormat;
            }

            return true;
        });
    }
}
