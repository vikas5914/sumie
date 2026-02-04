import { Head, Link, router, usePage } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import Header from '../components/Header';
import SearchInput from '../components/SearchInput';
import AppLayout from '../layouts/AppLayout';

interface SearchResult {
    id: number;
    title: string;
    cover_image_url: string | null;
    author: string | null;
    rating_average: number | string | null;
    status: string;
    genres: string[];
}

interface SearchProps {
    auth: {
        user: {
            name: string;
        } | null;
    };
    query: string;
    results: SearchResult[];
    filter: string;
    [key: string]: unknown;
}

const filters = [
    { label: 'ALL', value: 'all' },
    { label: 'MANGA', value: 'manga' },
    { label: 'MANHWA', value: 'manhwa' },
    { label: 'COMPLETED', value: 'completed' },
    { label: 'ONESHOT', value: 'oneshot' },
];

function readRecentSearches(key = 'sumie:recent-searches'): string[] {
    if (typeof window === 'undefined') {
        return [];
    }

    try {
        const existing = JSON.parse(window.localStorage.getItem(key) ?? '[]');
        return Array.isArray(existing) ? (existing.filter((q) => typeof q === 'string') as string[]) : [];
    } catch {
        return [];
    }
}

function buildSearchHref(query: string, filter: string): string {
    const params = new URLSearchParams();
    const normalizedQuery = query.trim();
    const normalizedFilter = filter.toLowerCase();

    if (normalizedQuery !== '') {
        params.set('q', normalizedQuery);
    }

    if (normalizedFilter !== '' && normalizedFilter !== 'all') {
        params.set('filter', normalizedFilter);
    }

    const qs = params.toString();
    return qs ? `/search?${qs}` : '/search';
}

export default function Search() {
    const { auth, query, results, filter } = usePage<SearchProps>().props;
    const userName = auth.user?.name ?? 'Operator';

    const [isSearching, setIsSearching] = useState(false);
    const [recentSearches, setRecentSearches] = useState<string[]>([]);

    useEffect(() => {
        setRecentSearches(readRecentSearches());

        return router.on('navigate', () => {
            setRecentSearches(readRecentSearches());
        });
    }, []);

    const activeFilter = useMemo(() => {
        const normalized = (filter ?? '').toString().toLowerCase();
        return normalized === '' ? 'all' : normalized;
    }, [filter]);

    return (
        <AppLayout>
            <Head title={query ? `Search: ${query}` : 'Search'} />

            <Header className="z-30 gap-4 bg-background-dark">
                <div className="flex items-center justify-between">
                    <h1 className="flex items-center gap-2 text-2xl font-extrabold tracking-tighter text-primary uppercase">
                        <span className="block h-6 w-3 bg-primary"></span>
                        SEARCH
                    </h1>
                    <div className="flex gap-2">
                        <button className="flex size-10 items-center justify-center border border-border-dark bg-surface-dark transition-all hover:border-primary hover:bg-primary hover:text-black active:translate-y-0.5">
                            <span className="material-symbols-outlined text-xl">tune</span>
                        </button>
                    </div>
                </div>
                <p className="text-[10px] font-bold tracking-[0.2em] text-zinc-500 uppercase">Operator: {userName}</p>
                <SearchInput
                    defaultValue={query}
                    filter={activeFilter}
                    autoSubmit
                    only={['query', 'results', 'filter']}
                    onSearchingChange={setIsSearching}
                />
                <div className="no-scrollbar flex gap-2 overflow-x-auto pb-1">
                    {filters.map((f) => {
                        const isActive = activeFilter === f.value;
                        return (
                            <Link
                                key={f.value}
                                href={buildSearchHref(query, f.value)}
                                preserveState
                                replace
                                className={`h-8 shrink-0 border px-4 text-xs font-bold whitespace-nowrap uppercase transition-colors ${
                                    isActive
                                        ? 'border-primary bg-primary text-black shadow-[2px_2px_0_0_rgba(255,255,255,0.2)]'
                                        : 'border-border-dark bg-transparent text-zinc-400 hover:border-primary hover:text-primary'
                                }`}
                            >
                                {f.label}
                            </Link>
                        );
                    })}
                </div>
            </Header>

            <main className="no-scrollbar flex-1 overflow-y-auto bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImEiIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTTAgNDBoNDBWMEgwdi4yaDQwdjM5LjhIMHoiIGZpbGw9IiMzMzMiIGZpbGwtb3BhY2l0eT0iMC4wNSIvPjwvcGF0dGVybj48L2RlZnM+PHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0idXJsKCNhKSIvPjwvc3ZnPg==')] pb-6">
                {query && (
                    <div className="border-b border-border-dark bg-surface-dark px-4 py-3">
                        <p className="text-xs text-zinc-500 uppercase">
                            {results.length} RESULTS FOR <span className="font-bold text-primary">"{query}"</span>
                        </p>
                    </div>
                )}

                {isSearching && query.trim().length >= 2 && results.length === 0 && (
                    <section className="grid grid-cols-2 gap-4 p-4">
                        {Array.from({ length: 8 }).map((_, index) => (
                            <div key={index} className="flex flex-col gap-2">
                                <div className="relative aspect-[2/3] w-full overflow-hidden border border-border-dark bg-surface-dark">
                                    <div className="absolute inset-0 animate-pulse bg-zinc-800"></div>
                                </div>
                                <div className="space-y-2">
                                    <div className="h-3 w-4/5 animate-pulse bg-zinc-800"></div>
                                    <div className="h-2 w-2/5 animate-pulse bg-zinc-800"></div>
                                </div>
                            </div>
                        ))}
                    </section>
                )}

                {results.length > 0 ? (
                    <section className="grid grid-cols-2 gap-4 p-4">
                        {results.map((manga) => (
                            <Link key={manga.id} href={`/manga/${manga.id}`} className="group flex cursor-pointer flex-col gap-2">
                                <div className="relative aspect-[2/3] w-full overflow-hidden border border-border-dark bg-surface-dark">
                                    <div
                                        className="absolute inset-0 bg-cover bg-center grayscale transition-all duration-300 group-hover:grayscale-0"
                                        style={{
                                            backgroundImage: manga.cover_image_url ? `url("${manga.cover_image_url}")` : 'none',
                                        }}
                                    ></div>
                                    <div className="absolute top-2 left-2 border border-black bg-primary px-1.5 py-0.5 text-[10px] font-bold text-black">
                                        {typeof manga.rating_average === 'number'
                                            ? `${(manga.rating_average * 20).toFixed(0)}% MATCH`
                                            : `${(Number(manga.rating_average || 0) * 20).toFixed(0)}% MATCH`}
                                    </div>
                                </div>
                                <div>
                                    <h3 className="truncate text-sm font-bold text-text-light uppercase group-hover:text-primary">{manga.title}</h3>
                                    <p className="text-[10px] text-zinc-500 uppercase">{manga.author ?? 'Unknown Author'}</p>
                                </div>
                            </Link>
                        ))}
                    </section>
                ) : (
                    <div className="flex flex-1 flex-col items-center justify-center p-8">
                        <div className="border border-border-dark bg-surface-dark p-8 text-center">
                            <span className="material-symbols-outlined mb-4 block text-4xl text-zinc-600">search</span>
                            {query ? (
                                <>
                                    <p className="mb-2 text-sm font-bold text-zinc-400 uppercase">No Results Found</p>
                                    <p className="mb-4 text-xs text-zinc-500">No manga matching "{query}"</p>
                                </>
                            ) : (
                                <>
                                    <p className="mb-2 text-sm font-bold text-zinc-400 uppercase">Start Searching</p>
                                    <p className="mb-4 text-xs text-zinc-500">Enter a title to find manga</p>

                                    {recentSearches.length > 0 && (
                                        <div className="mt-6 border-t border-border-dark pt-4 text-left">
                                            <p className="mb-3 text-[10px] font-bold tracking-[0.2em] text-zinc-500 uppercase">Recent</p>
                                            <div className="flex flex-wrap gap-2">
                                                {recentSearches.slice(0, 8).map((q) => (
                                                    <button
                                                        key={q}
                                                        type="button"
                                                        onClick={() =>
                                                            router.get(
                                                                '/search',
                                                                { q },
                                                                {
                                                                    replace: true,
                                                                    preserveState: true,
                                                                    preserveScroll: true,
                                                                    only: ['query', 'results', 'filter'],
                                                                },
                                                            )
                                                        }
                                                        className="border border-border-dark bg-background-dark px-3 py-2 text-xs font-bold text-zinc-300 uppercase transition-colors hover:border-primary hover:text-primary"
                                                    >
                                                        {q}
                                                    </button>
                                                ))}
                                            </div>
                                        </div>
                                    )}
                                </>
                            )}
                        </div>
                    </div>
                )}
            </main>
        </AppLayout>
    );
}
