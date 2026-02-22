<?php

use App\Support\ImageUrlBuilder;

it('returns null for null image url', function () {
    expect(ImageUrlBuilder::build(null, false))->toBeNull()
        ->and(ImageUrlBuilder::build(null, true))->toBeNull();
});

it('returns direct url when proxy is disabled', function () {
    $url = 'https://static.comix.to/covers/test.jpg';

    expect(ImageUrlBuilder::build($url, false))->toBe($url);
});

it('returns proxied url when proxy is enabled', function () {
    $url = 'https://static.comix.to/covers/test.jpg';
    $result = ImageUrlBuilder::build($url, true);

    expect($result)->toContain('/images/proxy/')
        ->and($result)->toContain(base64_encode($url));
});

it('returns null for empty string url', function () {
    expect(ImageUrlBuilder::build('', false))->toBeNull();
});
