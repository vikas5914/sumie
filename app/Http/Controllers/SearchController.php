<?php

namespace App\Http\Controllers;

use App\Services\ComickApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
    private const SEARCH_RESULT_LIMIT = 28;

    public function index(Request $request, ComickApiService $comick): Response
    {
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
                $remote = $comick->searchManga([
                    'title' => $query,
                    'limit' => self::SEARCH_RESULT_LIMIT,
                    'showall' => false,
                    'genres_mode' => 'and',
                ]);

                $filtered = $this->applyFilter($remote, $filter);

                $results = $filtered
                    ->map(fn (array $mangaData) => [
                        'id' => $mangaData['id'],
                        'title' => $mangaData['title'],
                        'cover_image_url' => $this->getProxiedCoverUrl($mangaData['cover_image_url']),
                        'author' => $mangaData['author'],
                        'rating_average' => $mangaData['rating_average'],
                        'status' => $mangaData['status'],
                        'genres' => $mangaData['genres'],
                    ])
                    ->values();
            } catch (\Throwable) {
                $results = collect();
            }
        }

        return Inertia::render('search', [
            'query' => $query,
            'results' => $results,
            'filter' => $filter,
        ]);
    }

    private function getProxiedCoverUrl(?string $coverUrl): ?string
    {
        if (! $coverUrl) {
            return null;
        }

        return route('image.proxy', ['encodedUrl' => base64_encode($coverUrl)]);
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
