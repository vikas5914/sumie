<?php

use App\Http\Controllers\HomeController;
use App\Models\Chapter;
use App\Models\Manga;
use App\Models\User;
use App\Models\UserManga;
use App\Services\WeebdexApiService;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;

it('loads the home shell without resolving deferred feed data', function () {
    $user = User::factory()->create();

    $response = actingAs($user)->get(route('home'));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('home')
        );
});

it('returns continue reading timestamps as ISO-8601', function () {
    $user = User::factory()->create();
    $manga = Manga::factory()->create([
        'id' => 'homefeed01',
        'title' => 'Home Feed Test',
    ]);
    $chapter = Chapter::factory()->create([
        'id' => 'homechap01',
        'manga_id' => $manga->id,
        'chapter_number' => '5',
    ]);

    UserManga::query()->create([
        'user_id' => $user->id,
        'manga_id' => $manga->id,
        'status' => 'reading',
        'current_chapter_id' => $chapter->id,
        'progress_percentage' => 25,
        'notify_on_update' => true,
        'last_read_at' => now()->subHours(2),
    ]);

    $controller = new HomeController(app(WeebdexApiService::class));
    $method = new ReflectionMethod($controller, 'getContinueReading');
    $method->setAccessible(true);

    /** @var array<int, array<string, mixed>> $result */
    $result = $method->invoke($controller, $user, false);

    expect($result)
        ->toHaveCount(1)
        ->and($result[0]['current_chapter_id'])->toBe('homechap01')
        ->and($result[0]['last_read_at'])->toBeString()
        ->and(str_contains((string) $result[0]['last_read_at'], 'T'))->toBeTrue();
});
