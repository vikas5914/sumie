<?php

declare(strict_types=1);

namespace App\Support;

class ImageUrlBuilder
{
    /**
     * Build an image URL, optionally proxied.
     */
    public static function build(?string $imageUrl, bool $useProxy): ?string
    {
        if (! $imageUrl) {
            return null;
        }

        if (! $useProxy) {
            return $imageUrl;
        }

        return route('image.proxy', ['encodedUrl' => base64_encode($imageUrl)]);
    }
}
