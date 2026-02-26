<?php

namespace App\Http\Controllers;

use App\Models\ReadingProgress;
use App\Models\UserManga;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    private const ESTIMATED_SECONDS_PER_CHAPTER = 420;

    public function index(Request $request): Response
    {
        $user = $request->user();

        $libraryItemCount = UserManga::query()
            ->where('user_id', $user->id)
            ->count();

        $chaptersReadCount = ReadingProgress::query()
            ->where('user_id', $user->id)
            ->distinct('chapter_id')
            ->count('chapter_id');

        $recordedReadingSeconds = (int) ReadingProgress::query()
            ->where('user_id', $user->id)
            ->sum('duration_seconds');

        $estimatedReadingSeconds = $chaptersReadCount * self::ESTIMATED_SECONDS_PER_CHAPTER;
        $readingSeconds = max($recordedReadingSeconds, $estimatedReadingSeconds);
        $readingHours = round($readingSeconds / 3600, 1);
        $level = max(1, (int) floor($chaptersReadCount / 25) + 1);

        return Inertia::render('me', [
            'profile' => [
                'level' => $level,
                'member_id' => $this->buildMemberId($user->id),
                'status' => 'online',
                'joined_at' => $user->created_at?->toDateString(),
                'environment' => strtoupper((string) app()->environment()),
                'stats' => [
                    'chapters_read' => $chaptersReadCount,
                    'reading_hours' => $readingHours,
                    'library_items' => $libraryItemCount,
                ],
            ],
        ]);
    }

    private function buildMemberId(int $userId): string
    {
        $suffix = strtoupper(substr(sha1((string) $userId), 0, 2));

        return sprintf('#%04d-%s', $userId, $suffix);
    }
}
