<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserMangaRequest;
use App\Http\Requests\UpdateUserMangaStatusRequest;
use App\Models\Chapter;
use App\Models\Manga;
use App\Models\ReadingProgress;
use App\Models\UserManga;
use App\Services\ComickApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

class UserMangaController extends Controller
{
    public function store(StoreUserMangaRequest $request, string $mangaId, ComickApiService $comick): RedirectResponse
    {
        $user = $request->user();

        try {
            $manga = $this->resolveManga($mangaId, $comick);
        } catch (RuntimeException) {
            return redirect()->back()->with('error', 'Manga not found');
        }

        $status = $request->validated('status', 'reading');

        // Check if already in library
        $existing = UserManga::where('user_id', $user->id)
            ->where('manga_id', $manga->id)
            ->first();

        if ($existing) {
            return redirect()->back()->with('message', 'Already in your library');
        }

        // Get first chapter
        $firstChapter = Chapter::where('manga_id', $manga->id)
            ->orderBy('chapter_number')
            ->first();

        $isReadingNow = $status === 'reading';

        UserManga::create([
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

        $userManga = UserManga::where('id', $id)
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

        $userManga = UserManga::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $newFavoriteState = ! $userManga->is_favorite;

        $userManga->update([
            'is_favorite' => $newFavoriteState,
        ]);

        $message = $newFavoriteState ? 'Added to favorites' : 'Removed from favorites';

        return redirect()->back()->with('message', $message);
    }

    public function toggleBookmark(Request $request, string $mangaId, ComickApiService $comick): RedirectResponse
    {
        $user = $request->user();

        try {
            $manga = $this->resolveManga($mangaId, $comick);
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

    private function resolveManga(string $mangaId, ComickApiService $comick): Manga
    {
        $existing = Manga::query()->find($mangaId)
            ?? Manga::query()->where('slug', $mangaId)->first();

        if ($existing) {
            return $existing;
        }

        $mangaData = $comick->getMangaBySlug($mangaId);

        return $comick->syncMangaToDatabase($mangaData);
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $user = $request->user();

        $userManga = UserManga::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $userManga->delete();

        return redirect()->route('library')->with('message', 'Removed from library');
    }
}
