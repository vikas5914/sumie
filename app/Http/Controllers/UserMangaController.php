<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Manga;
use App\Models\UserManga;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserMangaController extends Controller
{
    public function store(Request $request, int $mangaId): RedirectResponse
    {
        $user = $request->user();
        $manga = Manga::findOrFail($mangaId);

        // Check if already in library
        $existing = UserManga::where('user_id', $user->id)
            ->where('manga_id', $mangaId)
            ->first();

        if ($existing) {
            return redirect()->back()->with('message', 'Already in your library');
        }

        // Get first chapter
        $firstChapter = Chapter::where('manga_id', $mangaId)
            ->orderBy('chapter_number')
            ->first();

        UserManga::create([
            'user_id' => $user->id,
            'manga_id' => $mangaId,
            'status' => 'reading',
            'current_chapter_id' => $firstChapter?->id,
            'progress_percentage' => 0,
            'is_favorite' => false,
            'notify_on_update' => true,
            'started_at' => now(),
        ]);

        return redirect()->back()->with('message', 'Added to library');
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $user = $request->user();
        $status = $request->input('status');

        $validStatuses = ['reading', 'completed', 'on_hold', 'dropped', 'planned'];
        if (! in_array($status, $validStatuses)) {
            return redirect()->back()->with('error', 'Invalid status');
        }

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

        $userManga->update([
            'is_favorite' => ! $userManga->is_favorite,
        ]);

        $message = $userManga->is_favorite ? 'Added to favorites' : 'Removed from favorites';

        return redirect()->back()->with('message', $message);
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
