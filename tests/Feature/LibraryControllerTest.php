<?php

use App\Models\Manga;
use App\Models\User;
use App\Models\UserManga;
use Inertia\Testing\AssertableInertia as Assert;

it('shows all library items by default including planned bookmarks', function () {
    $user = User::factory()->create();
    $manga = Manga::factory()->create();

    UserManga::query()->create([
        'user_id' => $user->id,
        'manga_id' => $manga->id,
        'status' => 'planned',
        'progress_percentage' => 0,
        'notify_on_update' => true,
    ]);

    $this->actingAs($user)
        ->get(route('library'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('library')
            ->where('currentStatus', 'all')
            ->where('counts.all', 1)
            ->has('libraryItems', 1)
            ->where('libraryItems.0.status', 'planned')
        );
});
