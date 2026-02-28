<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Manga;
use App\Models\ReadingProgress;
use App\Models\UserManga;
use App\Services\WeebdexApiService;
use App\Support\ImageUrlBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class MangaController extends Controller
{
    public function show(Request $request, string $id, WeebdexApiService $weebdex): Response|RedirectResponse
    {
        $manga = Manga::query()->find($id);

        if ($manga === null || $weebdex->isStale($manga->synced_at, 360)) {
            try {
                $manga = $weebdex->syncMangaToDatabase($weebdex->getMangaById($id));
            } catch (RuntimeException $exception) {
                if ($this->isUpstreamNotFound($exception)) {
                    abort(404, 'Manga not found');
                }

                throw $exception;
            }
        }

        $shouldSyncChapters = $manga->chapters()->count() === 0
            || $manga->chapters()->whereNull('synced_at')->exists()
            || $manga->chapters()->where('synced_at', '<', now()->subMinutes(90))->exists();

        if ($shouldSyncChapters) {
            try {
                $chaptersFromApi = $weebdex->getMangaChaptersById($manga->id);
                $weebdex->syncChapters($manga, $chaptersFromApi);
            } catch (\Throwable $exception) {
                Log::warning('Failed to sync chapters for manga detail page.', [
                    'manga_id' => $manga->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $user = $request->user();
        $useImageProxy = true;

        $userManga = null;

        if ($user) {
            $userManga = UserManga::query()
                ->with('currentChapter')
                ->where('user_id', $user->id)
                ->where('manga_id', $manga->id)
                ->first();
        }

        $firstChapterId = $manga->chapters()
            ->orderBy('published_at')
            ->orderBy('id')
            ->value('id');

        $genres = collect($manga->genres ?? [])
            ->filter(fn (mixed $genre): bool => is_string($genre))
            ->values()
            ->map(fn (string $genre, int $index): array => [
                'id' => $index + 1,
                'name' => $genre,
            ])
            ->all();

        return Inertia::render('manga-detail', [
            'manga' => [
                'id' => $manga->id,
                'title' => $manga->title,
                'description' => $manga->description,
                'cover_image_url' => $manga->getCoverImageUrl($useImageProxy),
                'banner_image_url' => null,
                'author' => $manga->authors[0] ?? null,
                'artist' => $manga->artists[0] ?? null,
                'status' => $manga->status,
                'genres' => $genres,
                'themes' => $manga->themes ?? [],
                'rating_average' => $this->estimateRating($manga->follows_count, $manga->views_count),
                'rating_count' => $manga->follows_count,
                'total_views' => $manga->views_count,
                'total_chapters' => $manga->chapters_count,
                'release_year' => $manga->year,
                'first_chapter_id' => $firstChapterId,
                'is_mature' => in_array($manga->content_rating, ['erotica', 'pornographic'], true),
            ],
            'chapters' => Inertia::defer(function () use ($manga): array {
                return $this->formatChaptersForView($manga->chapters()->get());
            }),
            'libraryStatus' => $userManga ? [
                'id' => $userManga->id,
                'status' => $userManga->status,
                'current_chapter_id' => $userManga->currentChapter?->id,
                'current_chapter_number' => (float) ($userManga->currentChapter?->chapter_number ?? 1),
                'progress_percentage' => (float) $userManga->progress_percentage,
                'is_favorite' => (bool) $userManga->is_favorite,
            ] : null,
        ]);
    }

    public function read(Request $request, string $id, string $chapterId, WeebdexApiService $weebdex): Response|RedirectResponse
    {
        $manga = Manga::query()->find($id);

        if ($manga === null || $weebdex->isStale($manga->synced_at, 360)) {
            try {
                $manga = $weebdex->syncMangaToDatabase($weebdex->getMangaById($id));
            } catch (RuntimeException $exception) {
                if ($this->isUpstreamNotFound($exception)) {
                    abort(404, 'Manga not found');
                }

                throw $exception;
            }
        }

        $chapter = Chapter::query()
            ->where('manga_id', $manga->id)
            ->where('id', $chapterId)
            ->first();

        if ($chapter === null) {
            try {
                $weebdex->syncChapters($manga, $weebdex->getMangaChaptersById($manga->id));
            } catch (\Throwable $exception) {
                Log::warning('Failed to sync chapters for manga reader page.', [
                    'manga_id' => $manga->id,
                    'chapter_id' => $chapterId,
                    'error' => $exception->getMessage(),
                ]);
            }

            $chapter = Chapter::query()
                ->where('manga_id', $manga->id)
                ->where('id', $chapterId)
                ->first();
        }

        if ($chapter === null) {
            abort(404, 'Chapter not found');
        }

        if (
            $weebdex->isStale($chapter->synced_at, 30)
            || ! is_string($chapter->node)
            || $chapter->node === ''
            || ! is_array($chapter->pages)
            || $chapter->pages === []
        ) {
            try {
                $chapter = $weebdex->syncChapterPages($chapterId, $weebdex->getChapterById($chapterId));
            } catch (RuntimeException $exception) {
                if ($this->isUpstreamNotFound($exception)) {
                    abort(404, 'Chapter not found');
                }

                throw $exception;
            }
        }

        $orderedChapters = $manga->chapters()
            ->get(['id', 'chapter_number', 'published_at'])
            ->sort(function (Chapter $left, Chapter $right): int {
                $numberComparison = $this->chapterNumber($left->chapter_number) <=> $this->chapterNumber($right->chapter_number);

                if ($numberComparison !== 0) {
                    return $numberComparison;
                }

                return strcmp((string) $left->id, (string) $right->id);
            })
            ->values();

        $currentIndex = $orderedChapters->search(fn (Chapter $item): bool => $item->id === $chapterId);
        $index = is_int($currentIndex) ? $currentIndex : -1;

        $previousChapter = $index > 0 ? $orderedChapters[$index - 1] : null;
        $nextChapter = $index >= 0 && $index < $orderedChapters->count() - 1
            ? $orderedChapters[$index + 1]
            : null;

        $user = $request->user();

        if ($user) {
            $totalChapters = max(1, $orderedChapters->count());
            $progressPercentage = $index >= 0
                ? round((($index + 1) / $totalChapters) * 100, 2)
                : 0.0;

            ReadingProgress::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'chapter_id' => $chapter->id,
                ],
                [
                    'manga_id' => $manga->id,
                    'page_number' => 1,
                    'is_finished' => false,
                    'read_percentage' => 0,
                    'started_at' => now(),
                ]
            );

            $userManga = UserManga::query()
                ->where('user_id', $user->id)
                ->where('manga_id', $manga->id)
                ->first();

            if ($userManga) {
                $userManga->update([
                    'current_chapter_id' => $chapter->id,
                    'last_read_at' => now(),
                    'progress_percentage' => $progressPercentage,
                    'status' => $userManga->status === 'planned' ? 'reading' : $userManga->status,
                    'started_at' => $userManga->started_at ?? now(),
                ]);
            } else {
                UserManga::query()->create([
                    'user_id' => $user->id,
                    'manga_id' => $manga->id,
                    'status' => 'reading',
                    'current_chapter_id' => $chapter->id,
                    'progress_percentage' => $progressPercentage,
                    'is_favorite' => false,
                    'notify_on_update' => true,
                    'started_at' => now(),
                    'last_read_at' => now(),
                ]);
            }
        }

        $chapterTitle = is_string($chapter->title) && $chapter->title !== ''
            ? $chapter->title
            : ('Chapter '.($chapter->chapter_number ?? '?'));

        $useImageProxy = true;
        $pages = is_array($chapter->pages) ? $chapter->pages : [];

        return Inertia::render('manga-reader', [
            'manga' => [
                'id' => $manga->id,
                'title' => $manga->title,
                'cover_image_url' => $manga->getCoverImageUrl($useImageProxy),
            ],
            'chapter' => [
                'id' => (string) $chapter->id,
                'number' => $this->chapterNumber($chapter->chapter_number),
                'label' => $chapter->chapter_number,
                'title' => $chapterTitle,
                'page_count' => $chapter->page_count,
            ],
            'images' => $this->buildChapterImages(
                (string) $chapter->id,
                is_string($chapter->node) ? $chapter->node : null,
                $pages,
                $useImageProxy,
                $weebdex
            ),
            'navigation' => [
                'previous_chapter_id' => $previousChapter?->id,
                'next_chapter_id' => $nextChapter?->id,
            ],
            'source_url' => $this->buildSourceChapterUrl((string) $chapter->id),
        ]);
    }

    /**
     * @param  array<int, mixed>  $rawPages
     * @return array<int, array<string, mixed>>
     */
    private function buildChapterImages(
        string $chapterId,
        ?string $node,
        array $rawPages,
        bool $useImageProxy,
        WeebdexApiService $weebdex
    ): array {
        $images = collect($rawPages)
            ->filter(fn (mixed $page): bool => is_array($page) && is_string($page['name'] ?? null) && $page['name'] !== '')
            ->values();

        return $images
            ->map(function (array $page, int $index) use ($chapterId, $node, $useImageProxy, $weebdex): ?array {
                $name = (string) $page['name'];
                $url = $weebdex->buildPageImageUrl($chapterId, $name, $node);

                if ($url === null) {
                    return null;
                }

                $dimensions = is_array($page['dimensions'] ?? null) ? $page['dimensions'] : [];
                $width = isset($dimensions[0]) && is_numeric($dimensions[0]) ? (int) $dimensions[0] : null;
                $height = isset($dimensions[1]) && is_numeric($dimensions[1]) ? (int) $dimensions[1] : null;

                return [
                    'id' => $index + 1,
                    'url' => ImageUrlBuilder::build($url, $useImageProxy) ?? $url,
                    'width' => $width,
                    'height' => $height,
                ];
            })
            ->filter(fn (mixed $image): bool => is_array($image))
            ->values()
            ->all();
    }

    /**
     * @param  Collection<int, Chapter>  $chapters
     * @return array<int, array<string, mixed>>
     */
    private function formatChaptersForView(Collection $chapters): array
    {
        return $chapters
            ->sort(function (Chapter $left, Chapter $right): int {
                $numberComparison = $this->chapterNumber($left->chapter_number) <=> $this->chapterNumber($right->chapter_number);

                if ($numberComparison !== 0) {
                    return $numberComparison;
                }

                return strcmp((string) $right->id, (string) $left->id);
            })
            ->values()
            ->map(function (Chapter $chapter, int $index): array {
                return [
                    'id' => (string) $chapter->id,
                    'local_id' => $index + 1,
                    'external_id' => (string) $chapter->id,
                    'chapter_number' => $this->chapterNumber($chapter->chapter_number),
                    'chapter_label' => $chapter->chapter_number,
                    'title' => $chapter->title,
                    'language' => $chapter->language,
                    'published_at' => $chapter->published_at?->toISOString(),
                    'is_new' => $chapter->published_at?->gt(now()->subDays(7)) ?? false,
                ];
            })
            ->all();
    }

    private function isUpstreamNotFound(RuntimeException $exception): bool
    {
        return str_contains(strtolower($exception->getMessage()), 'not found');
    }

    private function chapterNumber(?string $value): float
    {
        if (! is_string($value) || $value === '' || ! is_numeric($value)) {
            return 0.0;
        }

        return (float) $value;
    }

    private function estimateRating(int $follows, int $views): float
    {
        if ($follows <= 0 || $views <= 0) {
            return 0.0;
        }

        $ratio = min(1, $follows / max(1, $views));

        return round(5 + ($ratio * 5), 2);
    }

    private function buildSourceChapterUrl(string $chapterId): string
    {
        return "https://weebdex.org/chapter/{$chapterId}";
    }
}
