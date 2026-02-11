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
    onSearchingChange?: (isSearching: boolean) => void;
}

type CancelToken = { cancel: () => void };

function normalizeQuery(value: string): string {
    return value.replace(/\s+/g, ' ').trim();
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
    onSearchingChange,
}: SearchInputProps) {
    const [value, setValue] = useState(defaultValue);
    const [isSearching, setIsSearching] = useState(false);

    const debounceTimerRef = useRef<number | null>(null);
    const cancelTokenRef = useRef<CancelToken | null>(null);
    const inputRef = useRef<HTMLInputElement | null>(null);
    const lastSentQueryRef = useRef<string>('');

    useEffect(() => {
        setValue(defaultValue);
    }, [defaultValue]);

    useEffect(() => {
        onSearchingChange?.(isSearching);
    }, [isSearching, onSearchingChange]);

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

            const normalizedFilter = (filter ?? '').toString().toLowerCase();
            if (normalizedFilter !== '' && normalizedFilter !== 'all') {
                data.filter = normalizedFilter;
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
                    setIsSearching(true);
                },
                onFinish: () => {
                    setIsSearching(false);
                },
            });

            lastSentQueryRef.current = data.q;

            if (query.length >= minLength) {
                rememberRecentSearch(query);
            }
        },
        [action, filter, minLength, only],
    );

    useEffect(() => {
        if (!autoSubmit) {
            return;
        }

        if (debounceTimerRef.current) {
            window.clearTimeout(debounceTimerRef.current);
        }

        debounceTimerRef.current = window.setTimeout(() => {
            const normalized = normalizeQuery(value);

            if (normalized.length === 0) {
                if (lastSentQueryRef.current !== '') {
                    submit('', { force: true });
                }
                return;
            }

            if (normalized.length < minLength) {
                if (lastSentQueryRef.current !== '') {
                    submit('', { force: true });
                }
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
            />

            <div className="absolute inset-y-0 right-0 flex items-center gap-1 pr-2">
                {isSearching && <AppIcon name="progress_activity" className="animate-spin text-lg text-primary" />}

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
