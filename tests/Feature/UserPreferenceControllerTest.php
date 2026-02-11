<?php

use App\Models\User;

it('updates image proxy preference for authenticated users', function () {
    $user = User::factory()->create([
        'use_image_proxy' => false,
    ]);

    $this->actingAs($user)
        ->patch(route('me.preferences.image-proxy'), [
            'use_image_proxy' => true,
        ])
        ->assertRedirect();

    expect($user->fresh()->use_image_proxy)->toBeTrue();
});

it('validates image proxy preference payload', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch(route('me.preferences.image-proxy'), [
            'use_image_proxy' => 'invalid',
        ])
        ->assertSessionHasErrors('use_image_proxy');
});
