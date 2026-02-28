<?php

use App\Models\Chapter;
use App\Models\Manga;
use App\Models\ReadingProgress;
use App\Models\User;
use App\Models\UserManga;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;

it('renders profile with persisted user stats', function () {
    $user = User::factory()->create([
        'name' => 'Stats Operator',
    ]);

    $firstManga = Manga::factory()->create();
    $secondManga = Manga::factory()->create();

    $firstChapter = Chapter::factory()->create([
        'id' => 'profile-ch-1',
        'manga_id' => $firstManga->id,
    ]);

    $secondChapter = Chapter::factory()->create([
        'id' => 'profile-ch-2',
        'manga_id' => $secondManga->id,
    ]);

    UserManga::query()->create([
        'user_id' => $user->id,
        'manga_id' => $firstManga->id,
        'status' => 'reading',
        'current_chapter_id' => $firstChapter->id,
        'progress_percentage' => 40,
        'notify_on_update' => true,
    ]);

    UserManga::query()->create([
        'user_id' => $user->id,
        'manga_id' => $secondManga->id,
        'status' => 'planned',
        'progress_percentage' => 0,
        'notify_on_update' => true,
    ]);

    ReadingProgress::query()->create([
        'user_id' => $user->id,
        'chapter_id' => $firstChapter->id,
        'manga_id' => $firstManga->id,
        'duration_seconds' => 3600,
    ]);

    ReadingProgress::query()->create([
        'user_id' => $user->id,
        'chapter_id' => $secondChapter->id,
        'manga_id' => $secondManga->id,
        'duration_seconds' => 1800,
    ]);

    actingAs($user)
        ->get(route('me'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('me')
            ->where('profile.level', 1)
            ->where('profile.member_id', sprintf('#%04d-%s', $user->id, strtoupper(substr(sha1((string) $user->id), 0, 2))))
            ->where('profile.status', 'online')
            ->where('profile.joined_at', $user->created_at?->toDateString())
            ->where('profile.environment', strtoupper((string) app()->environment()))
            ->where('profile.stats.chapters_read', 2)
            ->where('profile.stats.reading_hours', 1.5)
            ->where('profile.stats.library_items', 2)
        );
});

it('falls back to estimated hours when no tracked duration is present', function () {
    $user = User::factory()->create();
    $manga = Manga::factory()->create();
    $chapter = Chapter::factory()->create([
        'id' => 'profile-fallback-1',
        'manga_id' => $manga->id,
    ]);

    ReadingProgress::query()->create([
        'user_id' => $user->id,
        'chapter_id' => $chapter->id,
        'manga_id' => $manga->id,
        'duration_seconds' => 0,
    ]);

    actingAs($user)
        ->get(route('me'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('profile.stats.chapters_read', 1)
            ->where('profile.stats.reading_hours', 0.1)
        );
});
