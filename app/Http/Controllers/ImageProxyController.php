<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ImageProxyController extends Controller
{
    private const CACHE_DURATION_DAYS = 30;

    private const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB

    /**
     * Proxy and cache a remote image.
     */
    public function __invoke(Request $request, string $encodedUrl): Response
    {
        $url = base64_decode($encodedUrl, true);

        if (! is_string($url) || $url === '') {
            abort(400, 'Invalid image URL');
        }

        if (! $this->isValidImageUrl($url)) {
            abort(403, 'Invalid image URL');
        }

        $cacheKey = $this->getCacheKey($url);
        $disk = Storage::disk('public');
        $cachedPath = 'covers/'.$cacheKey;

        // Check if we have the image cached locally
        if ($disk->exists($cachedPath)) {
            return $this->serveCachedImage($disk, $cachedPath);
        }

        // Download and cache the image
        return $this->downloadAndCache($url, $disk, $cachedPath);
    }

    /**
     * Serve a locally cached image
     */
    private function serveCachedImage(FilesystemAdapter $disk, string $path): Response
    {
        $content = $disk->get($path);
        $mimeType = $disk->mimeType($path) ?? 'image/jpeg';

        return response($content, 200, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age='.(self::CACHE_DURATION_DAYS * 24 * 60 * 60),
            'Expires' => now()->addDays(self::CACHE_DURATION_DAYS)->toRfc7231String(),
            'Last-Modified' => now()->toRfc7231String(),
            'ETag' => md5($content),
            'X-Cache' => 'HIT',
        ]);
    }

    /**
     * Download an image and cache it locally
     */
    private function downloadAndCache(string $url, FilesystemAdapter $disk, string $cachePath): Response
    {
        try {
            /** @var ClientResponse $response */
            $response = Http::timeout(30)
            ->withHeaders([
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
            ])
            ->get($url);

            if (! $response->successful()) {
                abort(404, 'Image not found');
            }

            $content = $response->body();

            // Check file size
            if (strlen($content) > self::MAX_FILE_SIZE) {
                abort(413, 'Image too large');
            }

            // Store locally
            $disk->put($cachePath, $content);

            // Determine mime type
            $mimeType = $response->header('Content-Type') ?? $this->guessMimeType($content);

            return response($content, 200, [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'public, max-age='.(self::CACHE_DURATION_DAYS * 24 * 60 * 60),
                'Expires' => now()->addDays(self::CACHE_DURATION_DAYS)->toRfc7231String(),
                'Last-Modified' => now()->toRfc7231String(),
                'ETag' => md5($content),
                'X-Cache' => 'MISS',
            ]);
        } catch (\Throwable $e) {
            report($e);
            abort(502, 'Failed to fetch image');
        }
    }

    /**
     * Validate that URL has a supported image protocol and host.
     */
    private function isValidImageUrl(string $url): bool
    {
        $scheme = parse_url($url, PHP_URL_SCHEME);
        if (! in_array($scheme, ['http', 'https'], true)) {
            return false;
        }

        $host = parse_url($url, PHP_URL_HOST);

        if (! is_string($host) || $host === '') {
            return false;
        }

        return true;
    }

    /**
     * Generate a cache key from URL
     */
    private function getCacheKey(string $url): string
    {
        $urlParts = parse_url($url);
        $path = $urlParts['path'] ?? '';
        $extension = pathinfo($path, PATHINFO_EXTENSION) ?: 'jpg';

        return md5($url).'.'.$extension;
    }

    /**
     * Guess mime type from content
     */
    private function guessMimeType(string $content): string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($content);

        return $mimeType ?: 'image/jpeg';
    }
}
