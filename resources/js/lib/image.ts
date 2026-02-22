const IMAGE_PROXY_PATH_PREFIX = '/images/proxy/';

function encodeBase64(value: string): string {
    if (typeof window !== 'undefined' && typeof window.btoa === 'function') {
        return window.btoa(value);
    }

    return btoa(value);
}

function decodeBase64(value: string): string | null {
    try {
        if (typeof window !== 'undefined' && typeof window.atob === 'function') {
            return window.atob(value);
        }

        return atob(value);
    } catch {
        return null;
    }
}

function extractOriginalImageUrl(imageUrl: string): string {
    if (typeof window === 'undefined') {
        return imageUrl;
    }

    try {
        const parsed = new URL(imageUrl, window.location.origin);

        if (!parsed.pathname.startsWith(IMAGE_PROXY_PATH_PREFIX)) {
            return imageUrl;
        }

        const encodedUrl = decodeURIComponent(parsed.pathname.slice(IMAGE_PROXY_PATH_PREFIX.length));
        const decodedUrl = decodeBase64(encodedUrl);

        if (!decodedUrl || !/^https?:\/\//i.test(decodedUrl)) {
            return imageUrl;
        }

        return decodedUrl;
    } catch {
        return imageUrl;
    }
}

export function resolveImageUrl(imageUrl: string | null | undefined): string | null {
    if (!imageUrl) {
        return null;
    }

    const originalImageUrl = extractOriginalImageUrl(imageUrl);

    return `${IMAGE_PROXY_PATH_PREFIX}${encodeBase64(originalImageUrl)}`;
}
