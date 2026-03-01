/// <reference types="vite/client" />

import type { Router } from '@inertiajs/core';

declare global {
    interface Window {
        router: Router;
    }
}

export {};
