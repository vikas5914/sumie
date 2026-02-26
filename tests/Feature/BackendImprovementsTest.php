<?php

use App\Models\Manga;
use App\Models\User;
use App\Models\UserManga;
use App\Services\ComickApiService;
use Illuminate\Support\Facades\Cache;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\mock;

it('caches trending manga data and serves from cache on subsequent requests', function () {
    Cache::flush();
    \Illuminate\Support\Facades\Config::set('services.comix.base_url', 'https://comix-proxy.test');

    \Illuminate\Support\Facades\Http::fake([
        'https://comix-proxy.test/top*' => \Illuminate\Support\Facades\Http::response([
            'status' => 200,
            'result' => [
                'items' => [[
                    'hash_id' => 'trending1',
                    'slug' => 'trending-manga',
                    'title' => 'Trending Manga',
                    'synopsis' => 'A trending manga',
                    'poster' => ['large' => 'https://static.comix.to/trending.jpg'],
                    'status' => 'releasing',
                    'type' => 'manga',
                    'is_nsfw' => false,
                    'year' => 2024,
                    'rated_avg' => 8.5,
                    'rated_count' => 100,
                    'follows_total' => 500,
                    'latest_chapter' => 100,
                    'term_ids' => [],
                ]],
            ],
        ], 200),
        'https://comix-proxy.test/terms*' => \Illuminate\Support\Facades\Http::response([
            'status' => 200,
            'result' => ['items' => []],
        ], 200),
    ]);

    // Call the service directly to trigger caching via the controller's fetchTrendingManga
    $controller = new \App\Http\Controllers\HomeController(app(\App\Services\ComickApiService::class));
    $method = new ReflectionMethod($controller, 'fetchTrendingManga');
    $method->setAccessible(true);

    $result1 = $method->invoke($controller);
    expect($result1)->toHaveCount(1)
        ->and(Cache::has('home:trending'))->toBeTrue();

    // Second call should serve from cache without hitting API again
    $result2 = $method->invoke($controller);
    expect($result2)->toBe($result1);
});

it('toggles favorite and returns correct message after toggle', function () {
    $user = User::factory()->create();
    $manga = Manga::factory()->create();

    $userManga = UserManga::query()->create([
        'user_id' => $user->id,
        'manga_id' => $manga->id,
        'status' => 'reading',
        'progress_percentage' => 50,
        'is_favorite' => false,
        'notify_on_update' => true,
        'last_read_at' => now(),
    ]);

    // Toggle to favorite
    $response = $this->actingAs($user)
        ->patch(route('library.toggle-favorite', ['id' => $userManga->id]));

    $response->assertRedirect()
        ->assertSessionHas('message', 'Added to favorites');

    $userManga->refresh();
    expect($userManga->is_favorite)->toBeTrue();

    // Toggle back to not favorite
    $response = $this->actingAs($user)
        ->patch(route('library.toggle-favorite', ['id' => $userManga->id]));

    $response->assertRedirect()
        ->assertSessionHas('message', 'Removed from favorites');

    $userManga->refresh();
    expect($userManga->is_favorite)->toBeFalse();
});

it('rejects invalid status in store request', function () {
    $user = User::factory()->create();
    $manga = Manga::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('library.store', ['mangaId' => $manga->id]), [
            'status' => 'invalid_status',
        ]);

    $response->assertRedirect();
    $response->assertSessionHasErrors('status');
});

it('rejects invalid status in update status request', function () {
    $user = User::factory()->create();
    $manga = Manga::factory()->create();

    $userManga = UserManga::query()->create([
        'user_id' => $user->id,
        'manga_id' => $manga->id,
        'status' => 'reading',
        'progress_percentage' => 0,
        'notify_on_update' => true,
    ]);

    $response = $this->actingAs($user)
        ->patch(route('library.update-status', ['id' => $userManga->id]), [
            'status' => 'totally_fake_status',
        ]);

    $response->assertRedirect();
    $response->assertSessionHasErrors('status');
});

it('accepts valid status in update status request', function () {
    $user = User::factory()->create();
    $manga = Manga::factory()->create();

    $userManga = UserManga::query()->create([
        'user_id' => $user->id,
        'manga_id' => $manga->id,
        'status' => 'reading',
        'progress_percentage' => 0,
        'notify_on_update' => true,
    ]);

    $response = $this->actingAs($user)
        ->patch(route('library.update-status', ['id' => $userManga->id]), [
            'status' => 'completed',
        ]);

    $response->assertRedirect()
        ->assertSessionHas('message', 'Status updated');

    $userManga->refresh();
    expect($userManga->status)->toBe('completed')
        ->and((float) $userManga->progress_percentage)->toBe(100.00)
        ->and($userManga->completed_at)->not->toBeNull();
});

it('only shares essential user fields via inertia', function () {
    $user = User::factory()->create([
        'name' => 'TestUser',
    ]);

    $comick = mock(ComickApiService::class);
    $comick->shouldNotReceive('getTrendingManga');

    $response = actingAs($user)->get(route('home'));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('auth.user.id', $user->id)
            ->where('auth.user.name', 'TestUser')
            ->missingAll(['auth.user.email', 'auth.user.password', 'auth.user.remember_token'])
        );
});

it('syncs manga without schema column check', function () {
    Cache::flush();
    \Illuminate\Support\Facades\Config::set('services.comix.base_url', 'https://comix-proxy.test');

    \Illuminate\Support\Facades\Http::fake(function (\Illuminate\Http\Client\Request $request) {
        $url = $request->url();

        if (str_contains($url, '/manga?')) {
            return \Illuminate\Support\Facades\Http::response([
                'status' => 200,
                'result' => [
                    'items' => [[
                        'manga_id' => 99999,
                        'hash_id' => 'synctest1',
                        'slug' => 'sync-test-manga',
                        'title' => 'Sync Test',
                        'synopsis' => 'Testing sync',
                        'poster' => ['large' => 'https://static.comix.to/sync.jpg'],
                        'status' => 'releasing',
                        'type' => 'manhwa',
                        'is_nsfw' => false,
                        'year' => 2024,
                        'rated_avg' => 7.5,
                        'rated_count' => 100,
                        'follows_total' => 500,
                        'latest_chapter' => 50,
                        'term_ids' => [],
                        'links' => [],
                    ]],
                    'pagination' => ['current_page' => 1, 'last_page' => 1],
                ],
            ], 200);
        }

        if (str_contains($url, '/manga/synctest1')) {
            return \Illuminate\Support\Facades\Http::response([
                'status' => 200,
                'result' => [
                    'manga_id' => 99999,
                    'hash_id' => 'synctest1',
                    'slug' => 'sync-test-manga',
                    'title' => 'Sync Test',
                    'synopsis' => 'Testing sync',
                    'poster' => ['large' => 'https://static.comix.to/sync.jpg'],
                    'status' => 'releasing',
                    'type' => 'manhwa',
                    'is_nsfw' => false,
                    'year' => 2024,
                    'rated_avg' => 7.5,
                    'rated_count' => 100,
                    'follows_total' => 500,
                    'latest_chapter' => 50,
                    'term_ids' => [],
                    'links' => [],
                ],
            ], 200);
        }

        if (str_contains($url, '/terms')) {
            return \Illuminate\Support\Facades\Http::response([
                'status' => 200,
                'result' => ['items' => []],
            ], 200);
        }

        return \Illuminate\Support\Facades\Http::response([
            'status' => 404,
            'message' => 'Not found',
            'result' => null,
        ], 200);
    });

    $comick = app(ComickApiService::class);
    $mangaData = $comick->getMangaBySlug('sync-test-manga');
    $manga = $comick->syncMangaToDatabase($mangaData);

    expect($manga->id)->toBe('synctest1')
        ->and($manga->slug)->toBe('sync-test-manga')
        ->and($manga->type)->toBe('manhwa')
        ->and($manga->is_nsfw)->toBeFalse()
        ->and($manga->demographics)->toBeArray()
        ->and($manga->formats)->toBeArray()
        ->and($manga->source_manga_id)->not->toBeNull();

    $this->assertDatabaseHas('mangas', [
        'id' => 'synctest1',
        'slug' => 'sync-test-manga',
        'type' => 'manhwa',
    ]);
});

it('returns null user in inertia when not authenticated on onboarding', function () {
    $response = $this->get(route('onboarding'));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('auth.user', null)
        );
});
