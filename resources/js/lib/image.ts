const IMAGE_PROXY_PATH_PREFIX = '/images/proxy/';
const IMAGE_PROXY_PREFERENCE_KEY = 'sumie:use-image-proxy';

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

export function readImageProxyPreference(fallbackValue = false): boolean {
    if (typeof window === 'undefined') {
        return fallbackValue;
    }

    const storedValue = window.localStorage.getItem(IMAGE_PROXY_PREFERENCE_KEY);

    if (storedValue === '1') {
        return true;
    }

    if (storedValue === '0') {
        return false;
    }

    return fallbackValue;
}

export function writeImageProxyPreference(useImageProxy: boolean): void {
    if (typeof window === 'undefined') {
        return;
    }

    window.localStorage.setItem(IMAGE_PROXY_PREFERENCE_KEY, useImageProxy ? '1' : '0');
}

export function resolveImageUrl(imageUrl: string | null | undefined, useImageProxy: boolean): string | null {
    if (!imageUrl) {
        return null;
    }

    const originalImageUrl = extractOriginalImageUrl(imageUrl);

    if (!useImageProxy) {
        return originalImageUrl;
    }

    return `${IMAGE_PROXY_PATH_PREFIX}${encodeBase64(originalImageUrl)}`;
}
