<?php

use App\Models\Chapter;
use App\Models\Manga;
use App\Models\User;

it('redirects guests to onboarding from protected routes', function () {
    $this->get(route('home'))->assertRedirect(route('onboarding'));
    $this->get(route('library'))->assertRedirect(route('onboarding'));
    $this->get(route('search'))->assertRedirect(route('onboarding'));
    $this->get(route('me'))->assertRedirect(route('onboarding'));
    $this->get(route('manga.show', ['id' => 'abc123']))->assertRedirect(route('onboarding'));
    $this->get(route('manga.read', ['id' => 'abc123', 'chapterId' => 'chap001']))->assertRedirect(route('onboarding'));
});

it('allows authenticated users to access protected routes', function () {
    $user = User::factory()->create();
    $manga = Manga::factory()->create();
    $chapter = Chapter::factory()->create([
        'manga_id' => $manga->id,
    ]);

    $this->actingAs($user)->get(route('home'))->assertOk();
    $this->actingAs($user)->get(route('library'))->assertOk();
    $this->actingAs($user)->get(route('search'))->assertOk();
    $this->actingAs($user)->get(route('me'))->assertOk();
    $this->actingAs($user)->get(route('manga.show', ['id' => $manga->id]))->assertOk();
    $this->actingAs($user)->get(route('manga.read', ['id' => $manga->id, 'chapterId' => $chapter->id]))->assertOk();
});
