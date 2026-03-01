<?php

use App\Support\ImageUrlBuilder;

it('returns null for null image url', function () {
    expect(ImageUrlBuilder::build(null, false))->toBeNull()
        ->and(ImageUrlBuilder::build(null, true))->toBeNull();
});

it('returns direct url when proxy is disabled', function () {
    $url = 'https://srv.weebdex.net/covers/test.jpg';

    expect(ImageUrlBuilder::build($url, false))->toBe($url);
});

it('returns direct url when proxy flag is enabled on web', function () {
    $url = 'https://srv.weebdex.net/covers/test.jpg';
    $result = ImageUrlBuilder::build($url, true);

    expect($result)->toBe($url);
});

it('normalizes legacy proxied image urls into direct urls', function () {
    $originalUrl = 'https://srv.weebdex.net/covers/test.jpg';
    $legacyProxyUrl = '/images/proxy/'.rawurlencode(base64_encode($originalUrl));

    expect(ImageUrlBuilder::build($legacyProxyUrl, false))->toBe($originalUrl)
        ->and(ImageUrlBuilder::build($legacyProxyUrl, true))->toBe($originalUrl);
});

it('returns null for empty string url', function () {
    expect(ImageUrlBuilder::build('', false))->toBeNull();
});
