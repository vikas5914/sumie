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
        $useImageProxy = true;
        $status = $request->query('status', 'all');
        $sort = $request->query('sort', 'last_read');

        // Validate status parameter
        $validStatuses = ['all', 'reading', 'completed', 'on_hold', 'dropped', 'planned'];
        if (! in_array($status, $validStatuses)) {
            $status = 'all';
        }

        // Validate sort parameter
        $validSorts = ['last_read', 'title_asc', 'title_desc', 'progress', 'unread', 'added'];
        if (! in_array($sort, $validSorts)) {
            $sort = 'last_read';
        }

        // Get user's library entries
        $query = UserManga::with(['manga', 'currentChapter'])
            ->where('user_id', $user->id)
            ->when($status !== 'all', function ($query) use ($status) {
                $query->where('user_mangas.status', $status);
            });

        switch ($sort) {
            case 'title_asc':
                $query->join('mangas', 'user_mangas.manga_id', '=', 'mangas.id')
                    ->orderBy('mangas.title', 'asc')
                    ->select('user_mangas.*');
                break;
            case 'title_desc':
                $query->join('mangas', 'user_mangas.manga_id', '=', 'mangas.id')
                    ->orderBy('mangas.title', 'desc')
                    ->select('user_mangas.*');
                break;
            case 'progress':
                $query->orderBy('user_mangas.progress_percentage', 'desc')
                    ->select('user_mangas.*');
                break;
            case 'unread':
                $query->join('mangas', 'user_mangas.manga_id', '=', 'mangas.id')
                    ->leftJoin('chapters', 'user_mangas.current_chapter_id', '=', 'chapters.id')
                    ->orderByRaw('(mangas.total_chapters - COALESCE(chapters.chapter_number, 0)) DESC')
                    ->select('user_mangas.*');
                break;
            case 'added':
                $query->orderBy('user_mangas.created_at', 'desc')
                    ->select('user_mangas.*');
                break;
            case 'last_read':
            default:
                $query->orderBy('user_mangas.last_read_at', 'desc')
                    ->select('user_mangas.*');
                break;
        }

        $libraryItems = $query->get()
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

        $statusCounts = UserManga::query()
            ->where('user_id', $user->id)
            ->selectRaw('status, COUNT(*) as aggregate_count')
            ->groupBy('status')
            ->pluck('aggregate_count', 'status');

        // Get counts for each status.
        $counts = [
            'all' => (int) $statusCounts->sum(),
            'reading' => (int) ($statusCounts->get('reading') ?? 0),
            'completed' => (int) ($statusCounts->get('completed') ?? 0),
            'on_hold' => (int) ($statusCounts->get('on_hold') ?? 0),
            'dropped' => (int) ($statusCounts->get('dropped') ?? 0),
            'planned' => (int) ($statusCounts->get('planned') ?? 0),
        ];

        return Inertia::render('library', [
            'libraryItems' => $libraryItems,
            'currentStatus' => $status,
            'currentSort' => $sort,
            'counts' => $counts,
        ]);
    }
}
