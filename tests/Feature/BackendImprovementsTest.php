<?php

use App\Models\Manga;
use App\Models\User;
use App\Models\UserManga;
use App\Services\WeebdexApiService;
use Illuminate\Support\Facades\Cache;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\mock;

it('caches trending manga data and serves from cache on subsequent requests', function () {
    Cache::flush();

    $weebdex = mock(WeebdexApiService::class);
    $weebdex->shouldReceive('getTrendingManga')->once()->with(12)->andReturn(collect([
        [
            'id' => 'trend001',
            'title' => 'Trending Manga',
            'description' => 'A trending manga',
            'cover_image_url' => 'https://srv.weebdex.net/covers/trend001/cover.jpg',
            'status' => 'ongoing',
            'genres' => ['Action'],
            'rating_average' => 8.5,
            'total_chapters' => 10,
        ],
    ]));

    $controller = new \App\Http\Controllers\HomeController(app(WeebdexApiService::class));
    $method = new ReflectionMethod($controller, 'fetchTrendingManga');
    $method->setAccessible(true);

    $resultOne = $method->invoke($controller);
    $resultTwo = $method->invoke($controller);

    expect($resultOne)->toHaveCount(1)
        ->and($resultTwo)->toBe($resultOne)
        ->and(Cache::has('home:trending'))->toBeTrue();
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

    $this->actingAs($user)
        ->patch(route('library.toggle-favorite', ['id' => $userManga->id]))
        ->assertRedirect()
        ->assertSessionHas('message', 'Added to favorites');

    $userManga->refresh();
    expect($userManga->is_favorite)->toBeTrue();

    $this->actingAs($user)
        ->patch(route('library.toggle-favorite', ['id' => $userManga->id]))
        ->assertRedirect()
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

    actingAs($user)->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('auth.user.id', $user->id)
            ->where('auth.user.name', 'TestUser')
            ->missingAll(['auth.user.email', 'auth.user.password', 'auth.user.remember_token'])
        );
});

it('returns null user in inertia when not authenticated on onboarding', function () {
    $this->get(route('onboarding'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('auth.user', null)
        );
});
