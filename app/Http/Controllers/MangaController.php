<?php

namespace App\Http\Controllers;

use App\Models\Manga;
use App\Models\UserManga;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MangaController extends Controller
{
    public function show(Request $request, int $id)
    {
        $manga = Manga::with('chapters')->findOrFail($id);
        $user = $request->user();

        // Check if manga is in user's library
        $userManga = null;
        $readingProgress = null;

        if ($user) {
            $userManga = UserManga::with('currentChapter')
                ->where('user_id', $user->id)
                ->where('manga_id', $id)
                ->first();
        }

        // Format chapters
        $chapters = $manga->chapters->map(function ($chapter) {
            return [
                'id' => $chapter->id,
                'chapter_number' => $chapter->chapter_number,
                'title' => $chapter->title,
                'release_date' => $chapter->release_date?->diffForHumans(),
                'is_new' => $chapter->release_date?->gt(now()->subDays(7)),
            ];
        });

        return Inertia::render('manga-detail', [
            'manga' => [
                'id' => $manga->id,
                'title' => $manga->title,
                'description' => $manga->description,
                'cover_image_url' => $manga->getProxiedCoverUrl(),
                'banner_image_url' => $manga->getProxiedBannerUrl(),
                'author' => $manga->author,
                'artist' => $manga->artist,
                'status' => $manga->status,
                'genres' => $manga->genres,
                'themes' => $manga->themes,
                'rating_average' => $manga->rating_average,
                'rating_count' => $manga->rating_count,
                'view_count' => $manga->view_count,
                'total_chapters' => $manga->total_chapters,
                'release_year' => $manga->release_year,
                'chapters' => $chapters,
            ],
            'libraryStatus' => $userManga ? [
                'id' => $userManga->id,
                'status' => $userManga->status,
                'current_chapter_number' => $userManga->currentChapter?->chapter_number ?? 1,
                'progress_percentage' => $userManga->progress_percentage,
                'is_favorite' => $userManga->is_favorite,
            ] : null,
        ]);
    }
}
