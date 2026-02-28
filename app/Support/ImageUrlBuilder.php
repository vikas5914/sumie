<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ImageUrlBuilder
{
    private const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB

    public const DOWNLOAD_HEADERS = [
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',
        'Referer' => 'https://comix.to',
        'Origin' => 'https://comix.to',
        'Accept' => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
        'Accept-Language' => 'en-US,en;q=0.9',
        'Accept-Encoding' => 'gzip, deflate, br, zstd',
        'Connection' => 'keep-alive',
        'Sec-Fetch-Dest' => 'image',
        'Sec-Fetch-Mode' => 'no-cors',
        'Sec-Fetch-Site' => 'cross-site',
        'Sec-Fetch-User' => '?1',
        'Priority' => 'u=0, i',
    ];

    /**
     * Build an image URL, optionally proxied.
     *
     * In NativePHP, images are pre-downloaded and served via the static
     * _assets handler because the PHP bridge corrupts binary response data.
     */
    public static function build(?string $imageUrl, bool $useProxy): ?string
    {
        if (! $imageUrl) {
            return null;
        }

        if (! $useProxy) {
            return $imageUrl;
        }

        if (env('NATIVEPHP_RUNNING')) {
            return self::resolveForNative($imageUrl);
        }

        return route('image.proxy', ['encodedUrl' => base64_encode($imageUrl)]);
    }

    /**
     * Build multiple image URLs in batch (parallel download in NativePHP).
     *
     * @param  array<int, string|null>  $imageUrls
     * @return array<int, string|null>
     */
    public static function buildMany(array $imageUrls, bool $useProxy): array
    {
        if (! $useProxy || ! env('NATIVEPHP_RUNNING')) {
            return array_map(fn (?string $url) => self::build($url, $useProxy), $imageUrls);
        }

        $disk = Storage::disk('public');
        $results = [];
        $toDownload = [];

        // Check cache and collect uncached URLs
        foreach ($imageUrls as $index => $url) {
            if (! $url) {
                $results[$index] = null;

                continue;
            }

            $cacheKey = self::cacheKey($url);
            $cachedPath = 'covers/'.$cacheKey;

            if ($disk->exists($cachedPath)) {
                $results[$index] = '/_assets/storage/covers/'.$cacheKey;
            } else {
                $toDownload[$index] = ['url' => $url, 'key' => $cacheKey, 'path' => $cachedPath];
            }
        }

        if (empty($toDownload)) {
            return $results;
        }

        // Parallel download uncached images
        $responses = Http::pool(function (Pool $pool) use ($toDownload): void {
            foreach ($toDownload as $index => $item) {
                $pool->as((string) $index)
                    ->timeout(30)
                    ->withHeaders(self::DOWNLOAD_HEADERS)
                    ->get($item['url']);
            }
        });

        foreach ($toDownload as $index => $item) {
            $response = $responses[(string) $index] ?? null;

            if ($response && $response->successful() && strlen($response->body()) <= self::MAX_FILE_SIZE) {
                $disk->put($item['path'], $response->body());
                $results[$index] = '/_assets/storage/covers/'.$item['key'];
            } else {
                // Fallback: return original URL (may not work due to hotlinking)
                $results[$index] = $imageUrls[$index];
            }
        }

        return $results;
    }

    /**
     * Download and cache a single image for NativePHP, return _assets URL.
     */
    private static function resolveForNative(string $imageUrl): string
    {
        $cacheKey = self::cacheKey($imageUrl);
        $disk = Storage::disk('public');
        $cachedPath = 'covers/'.$cacheKey;

        if ($disk->exists($cachedPath)) {
            return '/_assets/storage/covers/'.$cacheKey;
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders(self::DOWNLOAD_HEADERS)
                ->get($imageUrl);

            if ($response->successful() && strlen($response->body()) <= self::MAX_FILE_SIZE) {
                $disk->put($cachedPath, $response->body());
            }
        } catch (\Throwable $e) {
            report($e);

            return $imageUrl;
        }

        return '/_assets/storage/covers/'.$cacheKey;
    }

    /**
     * Generate a cache key from a URL.
     */
    private static function cacheKey(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH) ?? '';
        $extension = pathinfo($path, PATHINFO_EXTENSION) ?: 'jpg';

        return md5($url).'.'.$extension;
    }
}
