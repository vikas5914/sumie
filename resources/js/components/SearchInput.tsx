import { router } from '@inertiajs/react';
import { useCallback, useEffect, useRef, useState } from 'react';
import AppIcon from './AppIcon';

interface SearchInputProps {
    className?: string;
    placeholder?: string;
    defaultValue?: string;
    action?: string;
    filter?: string;
    autoSubmit?: boolean;
    debounceMs?: number;
    minLength?: number;
    only?: string[];
    onSearchRequestStart?: () => void;
    onSearchRequestFinish?: () => void;
}

type CancelToken = { cancel: () => void };

function normalizeQuery(value: string): string {
    return value.replace(/\s+/g, ' ').trim();
}

function buildRequestSignature(query: string, filter?: string): string {
    const normalizedQuery = normalizeQuery(query);
    const normalizedFilter = (filter ?? '').toString().toLowerCase();
    const effectiveFilter = normalizedFilter === '' ? 'all' : normalizedFilter;

    return `${normalizedQuery}::${effectiveFilter}`;
}

function rememberRecentSearch(query: string, key = 'sumie:recent-searches'): void {
    if (typeof window === 'undefined') {
        return;
    }

    const normalized = normalizeQuery(query);
    if (normalized.length === 0) {
        return;
    }

    try {
        const existing = JSON.parse(window.localStorage.getItem(key) ?? '[]');
        const safe = Array.isArray(existing) ? (existing.filter((q) => typeof q === 'string') as string[]) : [];
        const next = [normalized, ...safe.filter((q) => q.toLowerCase() !== normalized.toLowerCase())].slice(0, 8);
        window.localStorage.setItem(key, JSON.stringify(next));
    } catch {
        // ignore
    }
}

export default function SearchInput({
    className = '',
    placeholder = 'SEARCH MANGA, AUTHORS, OR GENRES...',
    defaultValue = '',
    action = '/search',
    filter,
    autoSubmit = false,
    debounceMs = 250,
    minLength = 2,
    only,
    onSearchRequestStart,
    onSearchRequestFinish,
}: SearchInputProps) {
    const [value, setValue] = useState(defaultValue);
    const [activeRequestCount, setActiveRequestCount] = useState(0);

    const debounceTimerRef = useRef<number | null>(null);
    const cancelTokenRef = useRef<CancelToken | null>(null);
    const inputRef = useRef<HTMLInputElement | null>(null);
    const skipNextAutoSubmitRef = useRef(false);
    const filterRef = useRef(filter);
    const lastSentRequestRef = useRef<string>(buildRequestSignature(defaultValue, filter));

    useEffect(() => {
        filterRef.current = filter;
    }, [filter]);

    useEffect(() => {
        if (inputRef.current && document.activeElement === inputRef.current) {
            return;
        }

        setValue(defaultValue);
        lastSentRequestRef.current = buildRequestSignature(defaultValue, filter);
        skipNextAutoSubmitRef.current = true;
    }, [defaultValue, filter]);

    const submit = useCallback(
        (rawQuery: string, options: { force?: boolean } = {}) => {
            const force = options.force ?? false;
            const query = normalizeQuery(rawQuery);

            if (!force && query.length > 0 && query.length < minLength) {
                return;
            }

            const shouldSearch = query.length > 0;

            const data: Record<string, string> = {};
            data.q = shouldSearch ? query : '';

            const normalizedFilter = (filterRef.current ?? '').toString().toLowerCase();
            if (normalizedFilter !== '' && normalizedFilter !== 'all') {
                data.filter = normalizedFilter;
            }

            const requestSignature = buildRequestSignature(data.q, normalizedFilter);
            if (!force && requestSignature === lastSentRequestRef.current) {
                return;
            }

            cancelTokenRef.current?.cancel();

            router.get(action, data, {
                async: true,
                replace: true,
                preserveState: true,
                preserveScroll: true,
                ...(Array.isArray(only) && only.length > 0 ? { only } : {}),
                onCancelToken: (token) => {
                    cancelTokenRef.current = token as unknown as CancelToken;
                },
                onStart: () => {
                    setActiveRequestCount((count) => count + 1);
                    onSearchRequestStart?.();
                },
                onFinish: () => {
                    setActiveRequestCount((count) => Math.max(0, count - 1));
                    onSearchRequestFinish?.();
                },
            });

            lastSentRequestRef.current = requestSignature;

            if (query.length >= minLength) {
                rememberRecentSearch(query);
            }
        },
        [action, minLength, onSearchRequestFinish, onSearchRequestStart, only],
    );

    useEffect(() => {
        if (!autoSubmit) {
            return;
        }

        if (skipNextAutoSubmitRef.current) {
            skipNextAutoSubmitRef.current = false;

            return;
        }

        if (debounceTimerRef.current) {
            window.clearTimeout(debounceTimerRef.current);
        }

        debounceTimerRef.current = window.setTimeout(() => {
            const normalized = normalizeQuery(value);

            if (normalized.length === 0) {
                submit('');
                return;
            }

            if (normalized.length < minLength) {
                submit('');
                return;
            }

            submit(normalized);
        }, debounceMs);

        return () => {
            if (debounceTimerRef.current) {
                window.clearTimeout(debounceTimerRef.current);
            }
        };
    }, [autoSubmit, debounceMs, minLength, submit, value]);

    return (
        <form
            className={`relative w-full ${className}`}
            onSubmit={(e) => {
                e.preventDefault();
                submit(value, { force: true });
            }}
        >
            <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                <AppIcon name="search" className="text-xl text-zinc-500" />
            </div>

            <input
                ref={inputRef}
                aria-label="Search"
                className="block w-full appearance-none border border-border-dark bg-surface-dark py-3 pr-20 pl-11 text-sm text-text-light placeholder-zinc-500 shadow-none outline-none focus:ring-2 focus:ring-primary [&::-webkit-search-cancel-button]:hidden [&::-webkit-search-decoration]:hidden"
                placeholder={placeholder}
                type="search"
                value={value}
                onChange={(e) => setValue(e.target.value)}
                onFocus={() => {
                    skipNextAutoSubmitRef.current = false;
                }}
            />

            <div className="absolute inset-y-0 right-0 flex items-center gap-1 pr-2">
                {activeRequestCount > 0 && <AppIcon name="progress_activity" className="animate-spin text-lg text-primary" />}

                {value.trim().length > 0 && (
                    <button
                        type="button"
                        className="flex size-9 items-center justify-center border border-border-dark bg-surface-dark text-text-light transition-colors hover:border-primary hover:text-primary"
                        onClick={() => {
                            setValue('');
                            submit('', { force: true });
                            inputRef.current?.focus();
                        }}
                        aria-label="Clear search"
                    >
                        <AppIcon name="close" className="text-lg" />
                    </button>
                )}

                <button
                    type="submit"
                    className="flex size-9 items-center justify-center border border-primary bg-primary text-black transition-transform active:translate-y-0.5"
                    aria-label="Submit search"
                >
                    <AppIcon name="arrow_forward" className="text-lg" />
                </button>
            </div>
        </form>
    );
}
