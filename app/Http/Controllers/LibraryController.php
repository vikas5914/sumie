<?php

namespace App\Http\Controllers;

use App\Models\UserManga;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $useImageProxy = $user?->shouldUseImageProxy() ?? false;
        $status = $request->query('status', 'reading');

        // Validate status parameter
        $validStatuses = ['reading', 'completed', 'on_hold', 'dropped', 'planned'];
        if (! in_array($status, $validStatuses)) {
            $status = 'reading';
        }

        // Get user's library entries
        $libraryItems = UserManga::with(['manga', 'currentChapter'])
            ->where('user_id', $user->id)
            ->when($status !== 'all', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('last_read_at', 'desc')
            ->get()
            ->map(function ($userManga) {
                return [
                    'id' => $userManga->id,
                    'manga' => [
                        'id' => $userManga->manga->id,
                        'title' => $userManga->manga->title,
                        'cover_image_url' => $userManga->manga->getCoverImageUrl($useImageProxy),
                        'total_chapters' => $userManga->manga->total_chapters,
                    ],
                    'status' => $userManga->status,
                    'current_chapter_number' => $userManga->currentChapter?->chapter_number ?? 1,
                    'progress_percentage' => $userManga->progress_percentage,
                    'is_favorite' => $userManga->is_favorite,
                    'last_read_at' => $userManga->last_read_at,
                    'updated_at' => $userManga->updated_at,
                ];
            });

        // Get counts for each status
        $counts = [
            'reading' => UserManga::where('user_id', $user->id)->where('status', 'reading')->count(),
            'completed' => UserManga::where('user_id', $user->id)->where('status', 'completed')->count(),
            'downloaded' => 0, // TODO: Implement downloads
            'dropped' => UserManga::where('user_id', $user->id)->where('status', 'dropped')->count(),
        ];

        return Inertia::render('library', [
            'libraryItems' => $libraryItems,
            'currentStatus' => $status,
            'counts' => $counts,
        ]);
    }
}
