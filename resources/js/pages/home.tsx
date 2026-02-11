import { Deferred, Head, Link, router, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import AppIcon from '../components/AppIcon';
import Header from '../components/Header';
import SearchInput from '../components/SearchInput';
import AppLayout from '../layouts/AppLayout';

interface Genre {
    id: number;
    name: string;
}

interface Manga {
    id: string; // Comick slug
    title: string;
    description: string;
    cover_image_url: string;
    banner_image_url: string;
    rating_average: number;
    total_chapters: number;
    genres: Genre[];
    status?: string;
}

interface ContinueReading {
    id: string; // Comick slug
    title: string;
    cover_image_url: string;
    current_chapter: number;
    progress_percentage: number;
    last_read_at: string;
}

interface Recommendation {
    id: string; // Comick slug
    title: string;
    cover_image_url: string;
    genres: Genre[];
}

interface HomeMeta {
    isDataStale: boolean;
    hasCachedData: boolean;
    lastFetchedAt: string | null;
}

interface HomeProps {
    auth: {
        user: {
            name: string;
            avatar?: string;
        } | null;
    };
    homeFeed?: {
        featuredManga: Manga | null;
        trendingManga: Manga[];
        continueReading: ContinueReading[];
        recommendations: Recommendation[];
    };
    meta: HomeMeta;
    [key: string]: unknown;
}

function timeAgo(dateString: string): string {
    const date = new Date(dateString);
    const now = new Date();
    const diffInHours = Math.floor((now.getTime() - date.getTime()) / (1000 * 60 * 60));

    if (diffInHours < 1) return 'Just now';
    if (diffInHours < 24) return `${diffInHours}h ago`;
    const diffInDays = Math.floor(diffInHours / 24);
    if (diffInDays < 30) return `${diffInDays}d ago`;
    return `${Math.floor(diffInDays / 30)}mo ago`;
}

export default function Home() {
    const { auth, homeFeed, meta } = usePage<HomeProps>().props;
    const featuredManga = homeFeed?.featuredManga ?? null;
    const trendingManga = homeFeed?.trendingManga ?? [];
    const continueReading = homeFeed?.continueReading ?? [];
    const recommendations = homeFeed?.recommendations ?? [];
    const userName = auth.user?.name ?? 'Operator';
    const avatarUrl = auth.user?.avatar ?? `https://ui-avatars.com/api/?name=${encodeURIComponent(userName)}&background=22c55e&color=000`;

    const [isRefreshing, setIsRefreshing] = useState(false);
    const [pullStartY, setPullStartY] = useState<number | null>(null);
    const [pullDistance, setPullDistance] = useState(0);

    // Background refresh when data is stale
    useEffect(() => {
        if (meta.isDataStale && meta.hasCachedData) {
            performBackgroundRefresh();
        }
    }, [meta.isDataStale, meta.hasCachedData]);

    // Periodic polling for updates (every 5 minutes)
    useEffect(() => {
        const interval = setInterval(
            () => {
                if (meta.hasCachedData) {
                    performBackgroundRefresh();
                }
            },
            5 * 60 * 1000,
        ); // 5 minutes

        return () => clearInterval(interval);
    }, [meta.hasCachedData]);

    const performBackgroundRefresh = async () => {
        if (isRefreshing) return;

        try {
            setIsRefreshing(true);
            const response = await fetch('/home/refresh', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (response.ok) {
                // Silently reload the page to get fresh data
                router.reload({ only: ['homeFeed', 'meta'] });
            }
        } catch (error) {
            console.error('Background refresh failed:', error);
        } finally {
            setIsRefreshing(false);
        }
    };

    // Pull-to-refresh handlers
    const handleTouchStart = (e: React.TouchEvent) => {
        if (window.scrollY === 0) {
            setPullStartY(e.touches[0].clientY);
        }
    };

    const handleTouchMove = (e: React.TouchEvent) => {
        if (pullStartY !== null && window.scrollY === 0) {
            const distance = e.touches[0].clientY - pullStartY;
            if (distance > 0) {
                setPullDistance(Math.min(distance, 100)); // Max 100px
            }
        }
    };

    const handleTouchEnd = () => {
        if (pullDistance > 50) {
            performBackgroundRefresh();
        }
        setPullStartY(null);
        setPullDistance(0);
    };

    return (
        <AppLayout>
            <Head title="Home" />

            {/* Pull to refresh indicator */}
            {pullDistance > 0 && (
                <div
                    className="fixed top-0 right-0 left-0 z-50 flex items-center justify-center bg-surface-dark transition-all"
                    style={{ height: `${pullDistance}px` }}
                >
                    <div className="flex items-center gap-2 text-sm text-zinc-400">
                        <AppIcon name="keyboard_arrow_down" className={`transition-transform ${pullDistance > 50 ? 'rotate-180' : ''}`} />
                        {pullDistance > 50 ? 'Release to refresh' : 'Pull to refresh'}
                    </div>
                </div>
            )}

            {/* Refreshing indicator */}
            {isRefreshing && (
                <div className="fixed top-0 right-0 left-0 z-50 flex items-center justify-center bg-primary py-2 text-xs font-bold text-background-dark">
                    <AppIcon name="sync" className="mr-2 animate-spin" />
                    Updating manga...
                </div>
            )}

            {/* Header */}
            <Header className="">
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <div
                            className="size-10 bg-cover bg-center bg-no-repeat ring-1 ring-primary"
                            style={{
                                backgroundImage: `url("${avatarUrl}")`,
                            }}
                        ></div>
                        <div>
                            <p className="text-xs font-bold tracking-widest text-zinc-400 uppercase">WELCOME BACK</p>
                            <p className="text-xl leading-tight font-bold text-primary uppercase">{userName}</p>
                        </div>
                    </div>
                    <button className="relative flex size-10 items-center justify-center border border-border-dark bg-surface-dark transition-colors hover:bg-zinc-800">
                        <AppIcon name="notifications" className="text-2xl text-text-light" />
                        <span className="absolute top-2 right-2 size-2.5 border-2 border-surface-dark bg-primary"></span>
                    </button>
                </div>
                <SearchInput className="mt-4" />
            </Header>

            <main
                className="no-scrollbar flex flex-1 flex-col gap-6 overflow-y-auto pt-4 pb-6"
                onTouchStart={handleTouchStart}
                onTouchMove={handleTouchMove}
                onTouchEnd={handleTouchEnd}
            >
                {/* Stale data indicator */}
                {meta.isDataStale && meta.hasCachedData && (
                    <div className="mx-4 mb-2 flex items-center justify-between rounded border border-yellow-600/50 bg-yellow-600/10 px-3 py-2">
                        <span className="text-xs text-yellow-500">Data may be outdated</span>
                        <button
                            onClick={performBackgroundRefresh}
                            disabled={isRefreshing}
                            className="flex items-center gap-1 text-xs font-bold text-yellow-500"
                        >
                            <AppIcon name="refresh" className="text-sm" />
                            Refresh
                        </button>
                    </div>
                )}

                <Deferred data="homeFeed" fallback={<HomeFeedSkeleton />}>
                    <>
                        {/* Hero Section */}
                        {featuredManga && (
                            <section className="px-4">
                                <Link href={`/manga/${featuredManga.id}`} prefetch>
                                    <div className="group relative aspect-[4/3] w-full cursor-pointer overflow-hidden border border-border-dark shadow-lg">
                                        <div
                                            className="absolute inset-0 bg-cover bg-center bg-no-repeat transition-transform duration-700 group-hover:scale-105"
                                            style={{
                                                backgroundImage: `url("${featuredManga.banner_image_url || featuredManga.cover_image_url}")`,
                                            }}
                                        ></div>
                                        <div className="absolute inset-0 bg-gradient-to-t from-background-dark via-background-dark/60 to-transparent"></div>
                                        <div className="absolute right-0 bottom-0 left-0 flex flex-col items-start gap-3 p-5">
                                            <span className="bg-primary px-3 py-1 text-xs font-bold tracking-widest text-background-dark uppercase shadow-lg shadow-primary/40">
                                                #1 TRENDING
                                            </span>
                                            <div>
                                                <h2 className="mb-1 text-3xl font-bold text-text-light uppercase">{featuredManga.title}</h2>
                                                <p className="line-clamp-2 text-sm text-zinc-400">{featuredManga.description}</p>
                                            </div>
                                            <div className="mt-1 flex w-full items-center gap-3">
                                                <button className="flex h-10 flex-1 items-center justify-center gap-2 border border-text-light bg-text-light text-sm font-bold text-background-dark transition-colors hover:bg-zinc-300">
                                                    <AppIcon name="menu_book" className="text-xl" />
                                                    READ CHAPTER {featuredManga.total_chapters}
                                                </button>
                                                <button className="flex size-10 items-center justify-center border border-border-dark bg-surface-dark text-text-light transition-colors hover:bg-zinc-800">
                                                    <AppIcon name="bookmark_add" className="text-xl" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </Link>
                            </section>
                        )}

                        {/* Continue Reading */}
                        <section className="flex flex-col gap-3">
                            <div className="flex items-center justify-between px-4">
                                <h3 className="text-lg font-bold text-text-light uppercase">CONTINUE READING</h3>
                                <Link className="text-sm font-bold text-primary uppercase hover:text-primary/80" href="/library" prefetch>
                                    SEE ALL
                                </Link>
                            </div>
                            {continueReading.length > 0 ? (
                                <div className="no-scrollbar flex snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-2">
                                    {continueReading.map((item) => (
                                        <Link key={item.id} href={`/manga/${item.id}`} prefetch className="flex w-[280px] flex-none snap-center">
                                            <div className="flex w-full items-center gap-3 border border-border-dark bg-surface-dark p-3 shadow-sm">
                                                <div
                                                    className="relative h-20 w-16 shrink-0 overflow-hidden border border-zinc-600 bg-cover bg-center"
                                                    style={{
                                                        backgroundImage: `url("${item.cover_image_url}")`,
                                                    }}
                                                ></div>
                                                <div className="flex min-w-0 flex-1 flex-col justify-center">
                                                    <h4 className="truncate font-bold text-text-light uppercase">{item.title}</h4>
                                                    <p className="mb-2 text-xs text-zinc-500 uppercase">
                                                        CHAPTER {item.current_chapter} • {timeAgo(item.last_read_at)}
                                                    </p>
                                                    <div className="h-1.5 w-full overflow-hidden border border-zinc-600 bg-zinc-700">
                                                        <div className="h-full bg-primary" style={{ width: `${item.progress_percentage}%` }}></div>
                                                    </div>
                                                </div>
                                                <button className="flex size-8 shrink-0 items-center justify-center border border-primary bg-primary text-background-dark">
                                                    <AppIcon name="play_arrow" className="text-xl" />
                                                </button>
                                            </div>
                                        </Link>
                                    ))}
                                </div>
                            ) : (
                                <div className="px-4">
                                    <div className="border border-border-dark bg-surface-dark p-6 text-center">
                                        <p className="text-sm text-zinc-500">No reading history found.</p>
                                        <Link
                                            href="/search"
                                            prefetch
                                            className="mt-2 inline-block text-sm font-bold text-primary uppercase hover:text-primary/80"
                                        >
                                            Start Reading
                                        </Link>
                                    </div>
                                </div>
                            )}
                        </section>

                        {/* Trending Now */}
                        <section className="flex flex-col gap-3">
                            <div className="flex items-center justify-between px-4">
                                <h3 className="text-lg font-bold text-text-light uppercase">TRENDING NOW</h3>
                            </div>
                            {trendingManga.length > 0 ? (
                                <div className="grid grid-cols-2 gap-4 px-4 sm:grid-cols-3">
                                    {trendingManga.map((manga) => (
                                        <Link
                                            key={manga.id}
                                            href={`/manga/${manga.id}`}
                                            prefetch
                                            className="group flex cursor-pointer flex-col gap-2"
                                        >
                                            <div className="relative aspect-[2/3] w-full overflow-hidden border border-border-dark bg-zinc-800">
                                                <div
                                                    className="absolute inset-0 bg-cover bg-center bg-no-repeat transition-transform duration-500 group-hover:scale-105"
                                                    style={{
                                                        backgroundImage: `url("${manga.cover_image_url}")`,
                                                    }}
                                                ></div>
                                                <div className="absolute top-2 right-2 flex items-center gap-1 border border-border-dark bg-background-dark/80 px-2 py-0.5 text-[10px] font-bold text-text-light">
                                                    <AppIcon name="star" className="text-[12px] text-primary" />{' '}
                                                    {(manga.rating_average ?? 0).toFixed(1)}
                                                </div>
                                                {manga.status === 'ongoing' && (
                                                    <div className="absolute top-2 left-2 border border-primary bg-primary/90 px-2 py-0.5 text-[10px] font-bold text-background-dark uppercase">
                                                        NEW
                                                    </div>
                                                )}
                                            </div>
                                            <div>
                                                <h4 className="truncate text-sm font-bold text-text-light uppercase">{manga.title}</h4>
                                                <p className="text-xs text-zinc-500 uppercase">{manga.genres?.[0]?.name ?? 'Manga'}</p>
                                            </div>
                                        </Link>
                                    ))}
                                </div>
                            ) : (
                                <div className="px-4">
                                    <div className="border border-border-dark bg-surface-dark p-6 text-center">
                                        <p className="text-sm text-zinc-500">No trending manga available.</p>
                                    </div>
                                </div>
                            )}
                        </section>

                        {/* Recommendations */}
                        {recommendations.length > 0 && (
                            <section className="flex flex-col gap-3 pb-8">
                                <div className="flex items-center justify-between px-4">
                                    <h3 className="text-lg font-bold text-text-light uppercase">RECOMMENDED FOR YOU</h3>
                                </div>
                                <div className="no-scrollbar flex snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-2">
                                    {recommendations.map((manga) => (
                                        <Link
                                            key={manga.id}
                                            href={`/manga/${manga.id}`}
                                            prefetch
                                            className="group flex w-[140px] flex-none cursor-pointer snap-center flex-col gap-2"
                                        >
                                            <div className="relative aspect-[2/3] w-full overflow-hidden border border-border-dark bg-zinc-800">
                                                <div
                                                    className="absolute inset-0 bg-cover bg-center bg-no-repeat transition-transform duration-500 group-hover:scale-105"
                                                    style={{
                                                        backgroundImage: `url("${manga.cover_image_url}")`,
                                                    }}
                                                ></div>
                                            </div>
                                            <div>
                                                <h4 className="truncate text-sm font-bold text-text-light uppercase">{manga.title}</h4>
                                                <p className="text-xs text-zinc-500 uppercase">{manga.genres?.[0]?.name ?? 'Manga'}</p>
                                            </div>
                                        </Link>
                                    ))}
                                </div>
                            </section>
                        )}
                    </>
                </Deferred>

                {/* Background Glow */}
                <div className="pointer-events-none fixed top-0 left-1/2 -z-10 h-[50vh] w-full -translate-x-1/2 rounded-none bg-primary/10 opacity-30 blur-[80px]"></div>
            </main>
        </AppLayout>
    );
}

function HomeFeedSkeleton() {
    return (
        <div className="flex flex-col gap-6">
            <div className="px-4">
                <div className="aspect-[4/3] w-full animate-pulse border border-border-dark bg-surface-dark" />
            </div>

            <section className="flex flex-col gap-3">
                <div className="flex items-center justify-between px-4">
                    <div className="h-5 w-40 animate-pulse bg-zinc-700/50" />
                    <div className="h-4 w-16 animate-pulse bg-zinc-700/40" />
                </div>
                <div className="no-scrollbar flex gap-4 overflow-x-auto px-4 pb-2">
                    {Array.from({ length: 2 }).map((_, index) => (
                        <div key={index} className="h-[110px] w-[280px] flex-none animate-pulse border border-border-dark bg-surface-dark" />
                    ))}
                </div>
            </section>

            <section className="flex flex-col gap-3 px-4">
                <div className="h-5 w-36 animate-pulse bg-zinc-700/50" />
                <div className="grid grid-cols-2 gap-4 sm:grid-cols-3">
                    {Array.from({ length: 6 }).map((_, index) => (
                        <div key={index} className="flex flex-col gap-2">
                            <div className="aspect-[2/3] w-full animate-pulse border border-border-dark bg-surface-dark" />
                            <div className="h-4 w-full animate-pulse bg-zinc-700/50" />
                        </div>
                    ))}
                </div>
            </section>
        </div>
    );
}
