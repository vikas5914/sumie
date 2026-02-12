<?php

namespace App\Http\Controllers;

use App\Models\UserManga;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LibraryController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $useImageProxy = $user?->shouldUseImageProxy() ?? false;
        $status = $request->query('status', 'all');

        // Validate status parameter
        $validStatuses = ['all', 'reading', 'completed', 'on_hold', 'dropped', 'planned'];
        if (! in_array($status, $validStatuses)) {
            $status = 'all';
        }

        // Get user's library entries
        $libraryItems = UserManga::with(['manga', 'currentChapter'])
            ->where('user_id', $user->id)
            ->when($status !== 'all', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('last_read_at', 'desc')
            ->get()
            ->map(function ($userManga) use ($useImageProxy) {
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
            'all' => UserManga::where('user_id', $user->id)->count(),
            'reading' => UserManga::where('user_id', $user->id)->where('status', 'reading')->count(),
            'completed' => UserManga::where('user_id', $user->id)->where('status', 'completed')->count(),
            'on_hold' => UserManga::where('user_id', $user->id)->where('status', 'on_hold')->count(),
            'dropped' => UserManga::where('user_id', $user->id)->where('status', 'dropped')->count(),
            'planned' => UserManga::where('user_id', $user->id)->where('status', 'planned')->count(),
        ];

        return Inertia::render('library', [
            'libraryItems' => $libraryItems,
            'currentStatus' => $status,
            'counts' => $counts,
        ]);
    }
}
