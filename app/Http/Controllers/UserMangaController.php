<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserMangaRequest;
use App\Http\Requests\UpdateUserMangaStatusRequest;
use App\Models\Chapter;
use App\Models\Manga;
use App\Models\ReadingProgress;
use App\Models\UserManga;
use App\Services\WeebdexApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

class UserMangaController extends Controller
{
    public function store(StoreUserMangaRequest $request, string $mangaId, WeebdexApiService $weebdex): RedirectResponse
    {
        $user = $request->user();

        try {
            $manga = $this->resolveManga($mangaId, $weebdex);
        } catch (RuntimeException) {
            return redirect()->back()->with('error', 'Manga not found');
        }

        $status = $request->validated('status', 'reading');

        $existing = UserManga::query()
            ->where('user_id', $user->id)
            ->where('manga_id', $manga->id)
            ->first();

        if ($existing) {
            return redirect()->back()->with('message', 'Already in your library');
        }

        $firstChapter = Chapter::query()
            ->where('manga_id', $manga->id)
            ->orderBy('published_at')
            ->orderBy('id')
            ->first();

        if ($firstChapter === null) {
            $chapters = $weebdex->getMangaChaptersById($manga->id);
            $weebdex->syncChapters($manga, $chapters);

            $firstChapter = Chapter::query()
                ->where('manga_id', $manga->id)
                ->orderBy('published_at')
                ->orderBy('id')
                ->first();
        }

        $isReadingNow = $status === 'reading';

        UserManga::query()->create([
            'user_id' => $user->id,
            'manga_id' => $manga->id,
            'status' => $status,
            'current_chapter_id' => $isReadingNow ? $firstChapter?->id : null,
            'progress_percentage' => 0,
            'is_favorite' => false,
            'notify_on_update' => true,
            'started_at' => $isReadingNow ? now() : null,
        ]);

        $message = $status === 'planned' ? 'Bookmarked' : 'Added to library';

        return redirect()->back()->with('message', $message);
    }

    public function updateStatus(UpdateUserMangaStatusRequest $request, int $id): RedirectResponse
    {
        $user = $request->user();
        $status = $request->validated('status');

        $userManga = UserManga::query()
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $updateData = ['status' => $status];

        if ($status === 'completed') {
            $updateData['completed_at'] = now();
            $updateData['progress_percentage'] = 100;
        }

        $userManga->update($updateData);

        return redirect()->back()->with('message', 'Status updated');
    }

    public function toggleFavorite(Request $request, int $id): RedirectResponse
    {
        $user = $request->user();

        $userManga = UserManga::query()
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $newFavoriteState = ! $userManga->is_favorite;

        $userManga->update([
            'is_favorite' => $newFavoriteState,
        ]);

        $message = $newFavoriteState ? 'Added to favorites' : 'Removed from favorites';

        return redirect()->back()->with('message', $message);
    }

    public function toggleBookmark(Request $request, string $mangaId, WeebdexApiService $weebdex): RedirectResponse
    {
        $user = $request->user();

        try {
            $manga = $this->resolveManga($mangaId, $weebdex);
        } catch (RuntimeException) {
            return redirect()->back()->with('error', 'Manga not found');
        }

        $existing = UserManga::query()
            ->where('user_id', $user->id)
            ->where('manga_id', $manga->id)
            ->first();

        if ($existing) {
            $existing->delete();

            ReadingProgress::query()
                ->where('user_id', $user->id)
                ->where('manga_id', $manga->id)
                ->delete();

            return redirect()->back()->with('message', 'Removed from library');
        }

        UserManga::query()->create([
            'user_id' => $user->id,
            'manga_id' => $manga->id,
            'status' => 'planned',
            'progress_percentage' => 0,
            'is_favorite' => false,
            'notify_on_update' => true,
        ]);

        return redirect()->back()->with('message', 'Bookmarked');
    }

    private function resolveManga(string $mangaId, WeebdexApiService $weebdex): Manga
    {
        $existing = Manga::query()->find($mangaId);

        if ($existing && ! $weebdex->isStale($existing->synced_at, 360)) {
            return $existing;
        }

        $mangaData = $weebdex->getMangaById($mangaId);

        return $weebdex->syncMangaToDatabase($mangaData);
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $user = $request->user();

        $userManga = UserManga::query()
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $userManga->delete();

        return redirect()->route('library')->with('message', 'Removed from library');
    }
}
