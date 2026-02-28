<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Manga;
use App\Models\ReadingProgress;
use App\Models\UserManga;
use App\Services\ComickApiService;
use App\Support\ImageUrlBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class MangaController extends Controller
{
    public function show(Request $request, string $id, ComickApiService $comick): Response|RedirectResponse
    {
        $manga = Manga::query()->find($id);

        if (! $manga) {
            $manga = Manga::query()->where('slug', $id)->first();

            if ($manga) {
                return redirect()->route('manga.show', ['id' => $manga->id], 302);
            }

            try {
                $mangaData = $comick->getMangaBySlug($id);
                $manga = $comick->syncMangaToDatabase($mangaData);
            } catch (RuntimeException $exception) {
                if ($this->isUpstreamNotFound($exception)) {
                    abort(404, 'Manga not found');
                }

                throw $exception;
            }
        }

        if ($manga->id !== $id) {
            return redirect()->route('manga.show', ['id' => $manga->id], 302);
        }

        $user = $request->user();
        $useImageProxy = true;

        $userManga = null;

        if ($user) {
            $userManga = UserManga::with('currentChapter')
                ->where('user_id', $user->id)
                ->where('manga_id', $manga->id)
                ->first();
        }

        $hasAnyChapters = $manga->chapters()->exists();
        $hasLegacyChapterRows = $manga->chapters()->whereNull('external_id')->exists();
        $hasMissingChapterTitles = $manga->chapters()
            ->where(function ($query): void {
                $query->whereNull('title')
                    ->orWhere('title', '')
                    ->orWhere('title', 'Untitled Chapter');
            })
            ->exists();
        $requiresChapterSync = ! $hasAnyChapters || $hasLegacyChapterRows || $hasMissingChapterTitles;

        $genres = collect($manga->genres ?? [])->map(fn (string $genre, int $index) => [
            'id' => $index + 1,
            'name' => $genre,
        ])->values()->all();

        return Inertia::render('manga-detail', [
            'manga' => [
                'id' => $manga->id,
                'title' => $manga->title,
                'description' => $manga->description,
                'cover_image_url' => $manga->getCoverImageUrl($useImageProxy),
                'banner_image_url' => $manga->getBannerImageUrl($useImageProxy),
                'author' => $manga->author,
                'artist' => $manga->artist,
                'status' => $manga->status,
                'genres' => $genres,
                'themes' => $manga->themes ?? [],
                'rating_average' => (float) ($manga->rating_average ?? 0),
                'rating_count' => $manga->rating_count,
                'total_views' => $manga->view_count,
                'total_chapters' => $manga->total_chapters,
                'release_year' => $manga->release_year,
                'first_chapter_id' => $manga->chapters()->whereNotNull('external_id')->orderBy('chapter_number')->value('external_id'),
            ],
            'chapters' => Inertia::defer(function () use ($manga, $requiresChapterSync) {
                if ($requiresChapterSync) {
                    try {
                        $comick = app(ComickApiService::class);
                        $chaptersFromApi = $comick->getMangaChaptersBySlug($manga->id);
                        $comick->syncChapters($manga, $chaptersFromApi);
                    } catch (\Throwable $exception) {
                        Log::warning('Failed to sync chapters for manga detail page.', [
                            'manga_id' => $manga->id,
                            'error' => $exception->getMessage(),
                        ]);
                    }
                }

                return $manga->chapters()
                    ->whereNotNull('external_id')
                    ->orderBy('chapter_number')
                    ->orderByDesc('release_date')
                    ->get()
                    ->map(fn ($chapter) => [
                        'id' => (string) $chapter->external_id,
                        'local_id' => $chapter->id,
                        'external_id' => $chapter->external_id,
                        'chapter_number' => (float) $chapter->chapter_number,
                        'chapter_label' => $chapter->chapter_label,
                        'title' => $chapter->title,
                        'language' => $chapter->language,
                        'published_at' => $chapter->release_date?->toISOString(),
                        'is_new' => $chapter->release_date?->gt(now()->subDays(7)) ?? false,
                    ])
                    ->values()
                    ->all();
            }),
            'libraryStatus' => $userManga ? [
                'id' => $userManga->id,
                'status' => $userManga->status,
                'current_chapter_id' => $userManga->currentChapter?->external_id
                    ?? ($userManga->currentChapter?->chapter_number
                        ? $manga->chapters()
                            ->where('chapter_number', $userManga->currentChapter->chapter_number)
                            ->whereNotNull('external_id')
                            ->orderByDesc('release_date')
                            ->value('external_id')
                        : null),
                'current_chapter_number' => $userManga->currentChapter?->chapter_number ?? 1,
                'progress_percentage' => $userManga->progress_percentage,
                'is_favorite' => $userManga->is_favorite,
            ] : null,
        ]);
    }

    public function read(Request $request, string $id, string $chapterId, ComickApiService $comick): Response|RedirectResponse
    {
        $manga = Manga::query()->find($id);

        if (! $manga) {
            $manga = Manga::query()->where('slug', $id)->first();

            if ($manga) {
                return redirect()->route('manga.read', [
                    'id' => $manga->id,
                    'chapterId' => $chapterId,
                ], 302);
            }

            try {
                $mangaData = $comick->getMangaBySlug($id);
                $manga = $comick->syncMangaToDatabase($mangaData);
            } catch (RuntimeException $exception) {
                if ($this->isUpstreamNotFound($exception)) {
                    abort(404, 'Manga not found');
                }

                throw $exception;
            }
        }

        if ($manga->id !== $id) {
            return redirect()->route('manga.read', [
                'id' => $manga->id,
                'chapterId' => $chapterId,
            ], 302);
        }

        $chapter = $manga->chapters()
            ->where('external_id', $chapterId)
            ->first();

        if (! $chapter) {
            try {
                $chaptersFromApi = $comick->getMangaChaptersBySlug($manga->id);
                $comick->syncChapters($manga, $chaptersFromApi);
            } catch (\Throwable $exception) {
                Log::warning('Failed to sync chapters for manga reader page.', [
                    'manga_id' => $manga->id,
                    'chapter_id' => $chapterId,
                    'error' => $exception->getMessage(),
                ]);
            }

            $chapter = $manga->chapters()
                ->where('external_id', $chapterId)
                ->first();
        }

        if (! $chapter) {
            abort(404, 'Chapter not found');
        }

        try {
            $chapterData = $comick->getChapterById($chapterId);
        } catch (RuntimeException $exception) {
            if ($this->isUpstreamNotFound($exception)) {
                abort(404, 'Chapter not found');
            }

            throw $exception;
        }

        $orderedChapters = $manga->chapters()
            ->whereNotNull('external_id')
            ->orderBy('chapter_number')
            ->orderBy('id')
            ->get(['id', 'external_id', 'chapter_number']);

        $currentIndex = $orderedChapters->search(fn (Chapter $item): bool => $item->external_id === $chapterId);
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

        $chapterTitle = isset($chapterData['title']) && is_string($chapterData['title']) && $chapterData['title'] !== ''
            ? $chapterData['title']
            : ($chapter->title ?: 'Chapter '.$chapter->chapter_number);
        $useImageProxy = true;

        return Inertia::render('manga-reader', [
            'manga' => [
                'id' => $manga->id,
                'title' => $manga->title,
                'cover_image_url' => $manga->getCoverImageUrl($useImageProxy),
            ],
            'chapter' => [
                'id' => (string) $chapterId,
                'number' => (float) $chapter->chapter_number,
                'label' => $chapter->chapter_label,
                'title' => $chapterTitle,
                'page_count' => (int) ($chapterData['page_count'] ?? $chapter->page_count ?? 0),
            ],
            'images' => $this->buildChapterImages($chapterData['images'] ?? [], $useImageProxy),
            'navigation' => [
                'previous_chapter_id' => $previousChapter?->external_id,
                'next_chapter_id' => $nextChapter?->external_id,
            ],
            'source_url' => $this->buildSourceChapterUrl(
                $manga->id,
                (string) ($chapter->external_id ?: $chapterId),
                $chapter->source_url
            ),
        ]);
    }

    /**
     * Build chapter images with parallel downloading in NativePHP.
     */
    private function buildChapterImages(array $rawImages, bool $useImageProxy): array
    {
        $validImages = collect($rawImages)
            ->filter(fn (mixed $image): bool => is_array($image) && ! empty($image['url']))
            ->values();

        $urls = $validImages->pluck('url')->map(fn ($url) => (string) $url)->all();
        $builtUrls = ImageUrlBuilder::buildMany($urls, $useImageProxy);

        return $validImages
            ->map(fn (array $image, int $index): array => [
                'id' => $index + 1,
                'url' => $builtUrls[$index] ?? '',
                'width' => isset($image['width']) ? (int) $image['width'] : null,
                'height' => isset($image['height']) ? (int) $image['height'] : null,
            ])
            ->filter(fn (array $image): bool => $image['url'] !== '')
            ->values()
            ->all();
    }

    private function isUpstreamNotFound(RuntimeException $exception): bool
    {
        return str_contains(strtolower($exception->getMessage()), 'not found');
    }

    private function buildSourceChapterUrl(string $mangaId, string $chapterExternalId, ?string $fallback): ?string
    {
        if ($mangaId !== '' && $chapterExternalId !== '') {
            return "https://comix.to/title/{$mangaId}/{$chapterExternalId}";
        }

        if (is_string($fallback) && $fallback !== '') {
            return $fallback;
        }

        return null;
    }
}
