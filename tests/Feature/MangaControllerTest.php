<?php

use App\Models\Chapter;
use App\Models\Manga;
use App\Models\User;
use App\Models\UserManga;
use App\Services\WeebdexApiService;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\mock;

it('fetches missing manga from api and renders detail page', function () {
    $user = User::factory()->create();

    $manga = Manga::factory()->make([
        'id' => '93q1r00001',
        'title' => 'The Summoner',
        'synced_at' => now(),
        'views_count' => 0,
        'follows_count' => 0,
    ]);

    $weebdex = mock(WeebdexApiService::class);
    $weebdex->shouldReceive('getMangaById')->once()->with('93q1r00001')->andReturn([
        'id' => '93q1r00001',
        'title' => 'The Summoner',
        'description' => 'desc',
        'status' => 'ongoing',
    ]);
    $weebdex->shouldReceive('syncMangaToDatabase')->once()->andReturn($manga);
    $weebdex->shouldNotReceive('getMangaStatistics');
    $weebdex->shouldNotReceive('getMangaChaptersById');
    $weebdex->shouldNotReceive('syncChapters');

    actingAs($user)
        ->get(route('manga.show', ['id' => '93q1r00001']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('manga-detail')
            ->where('manga.id', '93q1r00001')
            ->where('manga.title', 'The Summoner')
        );
});

it('returns not found when upstream manga fetch fails', function () {
    $user = User::factory()->create();

    $weebdex = mock(WeebdexApiService::class);
    $weebdex->shouldReceive('getMangaById')->once()->with('missing-id')->andThrow(new RuntimeException('Not found'));
    $weebdex->shouldNotReceive('syncMangaToDatabase');

    actingAs($user)
        ->get(route('manga.show', ['id' => 'missing-id']))
        ->assertNotFound();
});

it('renders manga reader for a valid chapter and tracks progress', function () {
    $user = User::factory()->create();
    $manga = Manga::factory()->create(['id' => 'manga001']);

    $previousChapter = Chapter::factory()->create([
        'id' => 'chap0001',
        'manga_id' => $manga->id,
        'chapter_number' => '45',
    ]);

    $chapter = Chapter::factory()->create([
        'id' => 'chap0002',
        'manga_id' => $manga->id,
        'chapter_number' => '46',
        'title' => 'The Next Layer',
        'node' => 'https://s13.weebdex.net',
        'pages' => [
            ['name' => '1-image.webp', 'dimensions' => [800, 1200]],
            ['name' => '2-image.webp', 'dimensions' => [800, 1200]],
        ],
        'synced_at' => now(),
    ]);

    $nextChapter = Chapter::factory()->create([
        'id' => 'chap0003',
        'manga_id' => $manga->id,
        'chapter_number' => '47',
    ]);

    $weebdex = mock(WeebdexApiService::class);
    $weebdex->shouldNotReceive('isStale');
    $weebdex->shouldNotReceive('getChapterById');
    $weebdex->shouldNotReceive('syncChapterPages');
    $weebdex->shouldNotReceive('buildPageImageUrl');

    actingAs($user)
        ->get(route('manga.read', ['id' => $manga->id, 'chapterId' => $chapter->id]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('manga-reader')
            ->where('manga.id', $manga->id)
            ->where('chapter.id', 'chap0002')
            ->where('source_url', 'https://weebdex.org/chapter/chap0002')
            ->where('navigation.previous_chapter_id', $previousChapter->id)
            ->where('navigation.next_chapter_id', $nextChapter->id)
        );

    $this->assertDatabaseHas('reading_progress', [
        'user_id' => $user->id,
        'chapter_id' => $chapter->id,
        'manga_id' => $manga->id,
    ]);
});

it('moves bookmarked manga into reading when opening a chapter', function () {
    $user = User::factory()->create();
    $manga = Manga::factory()->create(['id' => 'book1']);

    $chapter = Chapter::factory()->create([
        'id' => '910001',
        'manga_id' => $manga->id,
        'chapter_number' => '1',
        'title' => 'Start',
        'node' => 'https://s13.weebdex.net',
        'pages' => [
            ['name' => '1-image.webp', 'dimensions' => [800, 1200]],
        ],
        'synced_at' => now(),
    ]);

    UserManga::query()->create([
        'user_id' => $user->id,
        'manga_id' => $manga->id,
        'status' => 'planned',
        'progress_percentage' => 0,
        'is_favorite' => false,
        'notify_on_update' => true,
    ]);

    $weebdex = mock(WeebdexApiService::class);
    $weebdex->shouldNotReceive('isStale');
    $weebdex->shouldNotReceive('buildPageImageUrl');

    actingAs($user)
        ->get(route('manga.read', ['id' => $manga->id, 'chapterId' => $chapter->id]))
        ->assertOk();

    $entry = UserManga::query()
        ->where('user_id', $user->id)
        ->where('manga_id', $manga->id)
        ->first();

    expect($entry)
        ->not->toBeNull()
        ->and($entry->status)->toBe('reading')
        ->and($entry->current_chapter_id)->toBe($chapter->id)
        ->and((float) $entry->progress_percentage)->toBeGreaterThan(0)
        ->and($entry->last_read_at)->not->toBeNull();
});

it('returns not found when chapter does not exist for manga reader route', function () {
    $user = User::factory()->create();
    $manga = Manga::factory()->create(['id' => 'manga404']);

    $weebdex = mock(WeebdexApiService::class);
    $weebdex->shouldNotReceive('isStale');
    $weebdex->shouldReceive('getMangaChaptersById')->once()->with('manga404')->andReturn(collect());
    $weebdex->shouldReceive('syncChapters')->once();
    $weebdex->shouldNotReceive('getChapterById');

    actingAs($user)
        ->get(route('manga.read', ['id' => $manga->id, 'chapterId' => 'missingchapter']))
        ->assertNotFound();
});
