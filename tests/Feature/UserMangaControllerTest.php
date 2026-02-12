<?php

use App\Models\Chapter;
use App\Models\Manga;
use App\Models\ReadingProgress;
use App\Models\User;
use App\Models\UserManga;
use App\Services\ComickApiService;

use function Pest\Laravel\mock;

it('stores bookmarked manga as planned in library', function () {
    $user = User::factory()->create();
    $manga = Manga::factory()->create();

    Chapter::factory()->create([
        'manga_id' => $manga->id,
        'chapter_number' => 1,
        'external_id' => '10001',
    ]);

    $response = $this->actingAs($user)->post(route('library.store', ['mangaId' => $manga->id]), [
        'status' => 'planned',
    ]);

    $response->assertRedirect();

    $entry = UserManga::query()
        ->where('user_id', $user->id)
        ->where('manga_id', $manga->id)
        ->first();

    expect($entry)
        ->not->toBeNull()
        ->and($entry->status)->toBe('planned')
        ->and($entry->current_chapter_id)->toBeNull()
        ->and($entry->started_at)->toBeNull();
});

it('toggles bookmark on and off by manga id', function () {
    $user = User::factory()->create();
    $manga = Manga::factory()->create();

    $this->actingAs($user)
        ->post(route('library.bookmark.toggle', ['mangaId' => $manga->id]))
        ->assertRedirect();

    $createdEntry = UserManga::query()
        ->where('user_id', $user->id)
        ->where('manga_id', $manga->id)
        ->first();

    expect($createdEntry)
        ->not->toBeNull()
        ->and($createdEntry->status)->toBe('planned');

    $this->actingAs($user)
        ->post(route('library.bookmark.toggle', ['mangaId' => $manga->id]))
        ->assertRedirect();

    $deletedEntry = UserManga::query()
        ->where('user_id', $user->id)
        ->where('manga_id', $manga->id)
        ->first();

    expect($deletedEntry)->toBeNull();
});

it('removing bookmark clears library entry and reading progress', function () {
    $user = User::factory()->create();
    $manga = Manga::factory()->create();
    $chapter = Chapter::factory()->create([
        'manga_id' => $manga->id,
        'external_id' => '20001',
        'chapter_number' => 1,
    ]);

    UserManga::query()->create([
        'user_id' => $user->id,
        'manga_id' => $manga->id,
        'status' => 'reading',
        'current_chapter_id' => $chapter->id,
        'progress_percentage' => 55,
        'notify_on_update' => true,
        'last_read_at' => now(),
    ]);

    ReadingProgress::query()->create([
        'user_id' => $user->id,
        'chapter_id' => $chapter->id,
        'manga_id' => $manga->id,
        'page_number' => 5,
        'is_finished' => false,
        'read_percentage' => 55,
    ]);

    $this->actingAs($user)
        ->post(route('library.bookmark.toggle', ['mangaId' => $manga->id]))
        ->assertRedirect();

    $this->assertDatabaseMissing('user_mangas', [
        'user_id' => $user->id,
        'manga_id' => $manga->id,
    ]);

    $this->assertDatabaseMissing('reading_progress', [
        'user_id' => $user->id,
        'manga_id' => $manga->id,
    ]);
});

it('bookmarks unsynced manga ids by syncing before create', function () {
    $user = User::factory()->create();

    $comick = mock(ComickApiService::class);
    $comick->shouldReceive('getMangaBySlug')->once()->with('remote-slug')->andReturn([
        'id' => 'abc12',
        'slug' => 'remote-slug',
        'title' => 'Remote Manga',
    ]);
    $comick->shouldReceive('syncMangaToDatabase')->once()->andReturnUsing(function (array $mangaData) {
        return Manga::factory()->create([
            'id' => (string) $mangaData['id'],
            'slug' => (string) $mangaData['slug'],
            'title' => (string) $mangaData['title'],
        ]);
    });

    $this->actingAs($user)
        ->post(route('library.bookmark.toggle', ['mangaId' => 'remote-slug']))
        ->assertRedirect();

    $this->assertDatabaseHas('user_mangas', [
        'user_id' => $user->id,
        'manga_id' => 'abc12',
        'status' => 'planned',
    ]);
});
