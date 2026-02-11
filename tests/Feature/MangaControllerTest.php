<?php

use App\Models\Chapter;
use App\Models\Manga;
use App\Models\User;
use App\Services\ComickApiService;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\mock;

it('fetches missing manga from api and syncs chapters', function () {
    $user = User::factory()->create();

    $manga = Manga::factory()->make([
        'id' => '93q1r',
        'slug' => 'the-summoner-apocalypse-rewinds',
        'title' => 'The Summoner: Apocalypse Rewinds',
        'genres' => ['Action'],
        'themes' => [],
        'view_count' => 100,
    ]);

    $comick = mock(ComickApiService::class);

    $comick->shouldReceive('getMangaBySlug')->once()->with('93q1r')->andReturn([
        'id' => '93q1r',
        'slug' => 'the-summoner-apocalypse-rewinds',
        'title' => 'The Summoner: Apocalypse Rewinds',
    ]);
    $comick->shouldReceive('syncMangaToDatabase')->once()->andReturn($manga);
    $comick->shouldNotReceive('getMangaChaptersBySlug');
    $comick->shouldNotReceive('syncChapters');

    $response = actingAs($user)->get(route('manga.show', ['id' => '93q1r']));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('manga-detail')
            ->where('manga.id', '93q1r')
            ->where('manga.title', 'The Summoner: Apocalypse Rewinds')
        );
});

it('redirects legacy slug URL to canonical hash id URL', function () {
    $user = User::factory()->create();

    $manga = Manga::factory()->create([
        'id' => '93q1r',
        'slug' => 'the-summoner-apocalypse-rewinds',
    ]);

    $comick = mock(ComickApiService::class);
    $comick->shouldNotReceive('getMangaBySlug');
    $comick->shouldNotReceive('syncMangaToDatabase');

    actingAs($user)
        ->get(route('manga.show', ['id' => $manga->slug]))
        ->assertRedirect(route('manga.show', ['id' => '93q1r']));
});

it('returns not found when upstream manga fetch fails', function () {
    $user = User::factory()->create();

    $comick = mock(ComickApiService::class);
    $comick->shouldReceive('getMangaBySlug')->once()->with('missing-slug')->andThrow(new RuntimeException('Not found'));

    actingAs($user)
        ->get(route('manga.show', ['id' => 'missing-slug']))
        ->assertNotFound();
});

it('does not return not found for upstream request errors', function () {
    $user = User::factory()->create();

    $comick = mock(ComickApiService::class);
    $comick->shouldReceive('getMangaBySlug')->once()->with('93q1r')->andThrow(new RuntimeException('Comix API request failed: 500'));

    actingAs($user)
        ->get(route('manga.show', ['id' => '93q1r']))
        ->assertServerError();
});

it('keeps manga detail page working when chapter sync fails', function () {
    $user = User::factory()->create();

    Manga::factory()->create([
        'id' => '93q1r',
        'slug' => 'the-summoner-apocalypse-rewinds',
        'title' => 'The Summoner: Apocalypse Rewinds',
        'genres' => ['Action'],
        'themes' => [],
    ]);

    $comick = mock(ComickApiService::class);
    $comick->shouldNotReceive('getMangaBySlug');
    $comick->shouldNotReceive('syncMangaToDatabase');
    $comick->shouldNotReceive('getMangaChaptersBySlug');
    $comick->shouldNotReceive('syncChapters');

    $response = actingAs($user)->get(route('manga.show', ['id' => '93q1r']));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('manga-detail')
            ->where('manga.id', '93q1r')
        );
});

it('renders manga reader for a valid chapter and tracks progress', function () {
    $this->withoutVite();

    $user = User::factory()->create();
    $manga = Manga::factory()->create(['id' => '793e', 'slug' => 'sample-manga']);

    $previousChapter = Chapter::factory()->create([
        'manga_id' => $manga->id,
        'chapter_number' => 45,
        'chapter_label' => '45',
        'external_id' => '8159269',
    ]);

    $chapter = Chapter::factory()->create([
        'manga_id' => $manga->id,
        'chapter_number' => 46,
        'chapter_label' => '46',
        'external_id' => '8159270',
        'title' => 'The Next Layer',
    ]);

    $nextChapter = Chapter::factory()->create([
        'manga_id' => $manga->id,
        'chapter_number' => 47,
        'chapter_label' => '47',
        'external_id' => '8159271',
    ]);

    $comick = mock(ComickApiService::class);
    $comick->shouldNotReceive('getMangaBySlug');
    $comick->shouldNotReceive('syncMangaToDatabase');
    $comick->shouldNotReceive('getMangaChaptersBySlug');
    $comick->shouldNotReceive('syncChapters');
    $comick->shouldReceive('getChapterById')->once()->with('8159270')->andReturn([
        'id' => '8159270',
        'title' => 'The Next Layer',
        'page_count' => 2,
        'images' => [
            ['url' => 'https://example.com/p1.webp', 'width' => 800, 'height' => 1200],
            ['url' => 'https://example.com/p2.webp', 'width' => 800, 'height' => 1200],
        ],
    ]);

    $response = actingAs($user)->get(route('manga.read', ['id' => $manga->id, 'chapterId' => '8159270']));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('manga-reader')
            ->where('manga.id', $manga->id)
            ->where('chapter.id', '8159270')
            ->where('navigation.previous_chapter_id', $previousChapter->external_id)
            ->where('navigation.next_chapter_id', $nextChapter->external_id)
            ->has('images', 2)
        );

    $this->assertDatabaseHas('reading_progress', [
        'user_id' => $user->id,
        'chapter_id' => $chapter->id,
        'manga_id' => $manga->id,
    ]);
});

it('redirects manga reader legacy slug URL to canonical hash id URL', function () {
    $user = User::factory()->create();

    $manga = Manga::factory()->create([
        'id' => '93q1r',
        'slug' => 'the-summoner-apocalypse-rewinds',
    ]);

    Chapter::factory()->create([
        'manga_id' => $manga->id,
        'external_id' => '8159270',
    ]);

    $comick = mock(ComickApiService::class);
    $comick->shouldNotReceive('getMangaBySlug');
    $comick->shouldNotReceive('syncMangaToDatabase');
    $comick->shouldNotReceive('getChapterById');

    actingAs($user)
        ->get(route('manga.read', ['id' => $manga->slug, 'chapterId' => '8159270']))
        ->assertRedirect(route('manga.read', ['id' => '93q1r', 'chapterId' => '8159270']));
});

it('returns not found when chapter does not exist for manga reader route', function () {
    $user = User::factory()->create();
    $manga = Manga::factory()->create(['id' => '93q1r']);

    $comick = mock(ComickApiService::class);
    $comick->shouldNotReceive('getMangaBySlug');
    $comick->shouldNotReceive('syncMangaToDatabase');
    $comick->shouldReceive('getMangaChaptersBySlug')->once()->with($manga->id)->andReturn(collect());
    $comick->shouldReceive('syncChapters')->once();
    $comick->shouldNotReceive('getChapterById');

    actingAs($user)
        ->get(route('manga.read', ['id' => $manga->id, 'chapterId' => 'missingchapter']))
        ->assertNotFound();
});
