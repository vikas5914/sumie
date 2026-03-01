<?php

use App\Models\Chapter;
use App\Models\Manga;
use App\Models\User;
use App\Services\WeebdexApiService;

use function Pest\Laravel\mock;

it('redirects to onboarding from protected routes when no user exists', function () {
    $this->get(route('home'))->assertRedirect(route('onboarding'));
    $this->get(route('library'))->assertRedirect(route('onboarding'));
    $this->get(route('search'))->assertRedirect(route('onboarding'));
    $this->get(route('me'))->assertRedirect(route('onboarding'));
    $this->get(route('manga.show', ['id' => 'abc123']))->assertRedirect(route('onboarding'));
    $this->get(route('manga.read', ['id' => 'abc123', 'chapterId' => 'chap001']))->assertRedirect(route('onboarding'));
});

it('allows protected route access when a user exists in the database', function () {
    $user = User::factory()->create();
    $manga = Manga::factory()->create();
    $chapter = Chapter::factory()->create([
        'manga_id' => $manga->id,
    ]);

    $weebdex = mock(WeebdexApiService::class);
    $weebdex->shouldReceive('getMangaStatistics')->andReturn([
        'views' => 1000,
        'follows' => 100,
    ]);
    $weebdex->shouldIgnoreMissing();

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->where('auth.user.id', $user->id));
    $this->get(route('library'))->assertOk();
    $this->get(route('search'))->assertOk();
    $this->get(route('me'))->assertOk();
    $this->get(route('manga.show', ['id' => $manga->id]))->assertOk();
    $this->get(route('manga.read', ['id' => $manga->id, 'chapterId' => $chapter->id]))->assertOk();
});
