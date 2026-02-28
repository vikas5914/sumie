<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

it('proxies and caches a weebdex image url', function () {
    Storage::fake('public');

    Http::fake([
        'https://srv.weebdex.net/*' => Http::response('fake-image-binary', 200, [
            'Content-Type' => 'image/jpeg',
        ]),
    ]);

    $encoded = base64_encode('https://srv.weebdex.net/covers/test.jpg');

    $first = $this->get(route('image.proxy', ['encodedUrl' => $encoded]));

    $first
        ->assertOk()
        ->assertHeader('X-Cache', 'MISS');

    $second = $this->get(route('image.proxy', ['encodedUrl' => $encoded]));

    $second
        ->assertOk()
        ->assertHeader('X-Cache', 'HIT');

    expect(Storage::disk('public')->allFiles('covers'))->not->toBeEmpty();
});

it('proxies and caches chapter node image url', function () {
    Storage::fake('public');

    Http::fake([
        'https://s13.weebdex.net/*' => Http::response('fake-image-binary', 200, [
            'Content-Type' => 'image/jpeg',
        ]),
    ]);

    $encoded = base64_encode('https://s13.weebdex.net/data/chapter-id/page-1.jpg');

    $response = $this->get(route('image.proxy', ['encodedUrl' => $encoded]));

    $response
        ->assertOk()
        ->assertHeader('X-Cache', 'MISS');
});

it('proxies image urls from any http host', function () {
    Storage::fake('public');

    Http::fake([
        'https://example.com/*' => Http::response('fake-image-binary', 200, [
            'Content-Type' => 'image/jpeg',
        ]),
    ]);

    $encoded = base64_encode('https://example.com/image.jpg');

    $this->get(route('image.proxy', ['encodedUrl' => $encoded]))
        ->assertOk()
        ->assertHeader('X-Cache', 'MISS');
});

it('proxies and caches an allowed wowpic image url', function () {
    Storage::fake('public');

    Http::fake([
        'https://jdpw.wowpic4.store/*' => Http::response('fake-image-binary', 200, [
            'Content-Type' => 'image/webp',
        ]),
    ]);

    $encoded = base64_encode('https://jdpw.wowpic4.store/ii/example/001.webp');

    $response = $this->get(route('image.proxy', ['encodedUrl' => $encoded]));

    $response
        ->assertOk()
        ->assertHeader('X-Cache', 'MISS');
});
