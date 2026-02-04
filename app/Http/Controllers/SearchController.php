<?php

namespace App\Http\Controllers;

use App\Services\MangaDexApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
    public function index(Request $request, MangaDexApiService $mangaDex): Response
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
                $remote = $mangaDex->searchManga([
                    'title' => $query,
                    'limit' => 20,
                    'contentRating' => ['safe', 'suggestive'],
                    'order' => ['rating' => 'desc'],
                ]);

                $filtered = $this->applyFilter($remote, $filter);

                $results = $filtered
                    ->map(function (array $mangaData) use ($mangaDex) {
                        $manga = $mangaDex->syncMangaToDatabase($mangaData);

                        return [
                            'id' => $manga->id,
                            'title' => $manga->title,
                            'cover_image_url' => $manga->getProxiedCoverUrl(),
                            'author' => $manga->author,
                            'rating_average' => $manga->rating_average,
                            'status' => $manga->status,
                            'genres' => $manga->genres,
                        ];
                    })
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

    /**
     * @param  Collection<int, array<string, mixed>>  $results
     * @return Collection<int, array<string, mixed>>
     */
    private function applyFilter(Collection $results, string $filter): Collection
    {
        if ($filter === 'all') {
            return $results;
        }

        return $results->filter(function (array $manga) use ($filter) {
            if ($filter === 'completed') {
                return ($manga['status'] ?? null) === 'completed';
            }

            if ($filter === 'manga') {
                return ($manga['country_of_origin'] ?? null) === 'Japan';
            }

            if ($filter === 'manhwa') {
                return ($manga['country_of_origin'] ?? null) === 'Korea';
            }

            if ($filter === 'oneshot') {
                $totalChapters = (int) ($manga['total_chapters'] ?? 0);
                $formatTags = is_array($manga['format_tags'] ?? null) ? $manga['format_tags'] : [];
                $hasOneshotTag = in_array('Oneshot', $formatTags, true) || in_array('One Shot', $formatTags, true);

                return $totalChapters <= 1 || $hasOneshotTag;
            }

            return true;
        });
    }
}
