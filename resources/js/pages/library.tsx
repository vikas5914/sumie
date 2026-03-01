import { Head, Link, usePage } from '@inertiajs/react';
import { useState, useRef, useEffect } from 'react';
import { library as libraryRoute, search as searchRoute } from '@/routes';
import { show as showManga } from '@/routes/manga';
import AppIcon from '../components/AppIcon';
import Header from '../components/Header';
import AppLayout from '../layouts/AppLayout';
import { resolveImageUrl } from '../lib/image';

interface Genre {
    id: number;
    name: string;
}

interface Manga {
    id: string; // Weebdex manga id
    title: string;
    cover_image_url: string;
    rating_average: number;
    total_chapters: number;
    genres: Genre[];
    author?: string;
    status?: string;
}

interface LibraryItem {
    id: number;
    manga: Manga;
    status: 'reading' | 'completed' | 'on_hold' | 'dropped' | 'planned';
    current_chapter_number: number;
    progress_percentage: number;
    is_favorite: boolean;
    last_read_at: string | null;
}

interface LibraryCounts {
    all: number;
    reading: number;
    completed: number;
    on_hold: number;
    dropped: number;
    planned: number;
}

interface LibraryProps {
    auth: {
        user: {
            name: string;
        } | null;
    };
    libraryItems: LibraryItem[];
    currentStatus: string;
    currentSort?: string;
    counts: LibraryCounts;
    [key: string]: unknown;
}

const statusLabels: Record<string, string> = {
    all: 'All',
    reading: 'Reading',
    completed: 'Completed',
    on_hold: 'On Hold',
    dropped: 'Dropped',
    planned: 'Planned',
};

const sortLabels: Record<string, string> = {
    last_read: 'Last Read',
    title_asc: 'Title A-Z',
    title_desc: 'Title Z-A',
    progress: 'Progress',
    unread: 'Unread',
    added: 'Date Added',
};

const statusOrder = ['all', 'reading', 'completed', 'on_hold', 'dropped', 'planned'];
const LIBRARY_PAGE_SIZE = 20;

export default function Library() {
    const { auth, libraryItems, currentStatus, currentSort = 'last_read', counts } = usePage<LibraryProps>().props;
    const userName = auth.user?.name ?? 'Operator';
    const [isSortDropdownOpen, setIsSortDropdownOpen] = useState(false);
    const sortDropdownRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            if (sortDropdownRef.current && !sortDropdownRef.current.contains(event.target as Node)) {
                setIsSortDropdownOpen(false);
            }
        };
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);
    const buildBackgroundImage = (imageUrl: string | null | undefined): string => {
        const resolvedImageUrl = resolveImageUrl(imageUrl);

        return resolvedImageUrl ? `url("${resolvedImageUrl}")` : 'none';
    };

    const unreadCount = (item: LibraryItem): number => {
        return Math.max(0, item.manga.total_chapters - item.current_chapter_number);
    };
    const [visibleItemsCount, setVisibleItemsCount] = useState(LIBRARY_PAGE_SIZE);

    useEffect(() => {
        setVisibleItemsCount(LIBRARY_PAGE_SIZE);
    }, [libraryItems.length, currentStatus, currentSort]);

    const visibleItems = libraryItems.slice(0, visibleItemsCount);
    const hasMoreItems = visibleItemsCount < libraryItems.length;
    const remainingItemCount = Math.max(0, libraryItems.length - visibleItemsCount);

    return (
        <AppLayout>
            <Head title="Library" />

            <Header className="z-30 gap-4 bg-background-dark">
                <div className="flex items-center justify-between">
                    <h1 className="flex items-center gap-2 text-2xl font-extrabold tracking-tighter text-primary uppercase">
                        <span className="block h-6 w-3 bg-primary"></span>
                        LIBRARY
                    </h1>
                    <div className="flex gap-2">
                        <Link
                            href={searchRoute.url()}
                            className="flex size-10 items-center justify-center border border-border-dark bg-surface-dark transition-all hover:bg-primary hover:text-black"
                        >
                            <AppIcon name="search" className="text-[20px]" />
                        </Link>
                        <button className="flex size-10 items-center justify-center border border-border-dark bg-surface-dark transition-all hover:bg-primary hover:text-black">
                            <AppIcon name="tune" className="text-[20px]" />
                        </button>
                    </div>
                </div>
                <p className="text-[10px] font-bold tracking-[0.2em] text-zinc-500 uppercase">Operator: {userName}</p>
                <div className="no-scrollbar flex overflow-x-auto border-t border-border-dark bg-surface-dark">
                    {statusOrder.map((status) => {
                        const count = counts[status as keyof LibraryCounts] ?? 0;
                        const isActive = currentStatus === status;
                        return (
                            <Link
                                key={status}
                                href={libraryRoute.url({
                                    query: {
                                        status,
                                        sort: currentSort,
                                    },
                                })}
                                only={['libraryItems', 'currentStatus', 'currentSort', 'counts']}
                                preserveScroll
                                preserveState
                                replace
                                className={`border-r border-border-dark px-6 py-3 text-xs font-bold whitespace-nowrap uppercase transition-colors ${
                                    isActive ? 'bg-primary text-black' : 'text-zinc-500 hover:bg-border-dark hover:text-white'
                                }`}
                            >
                                {statusLabels[status]} ({count})
                            </Link>
                        );
                    })}
                </div>
            </Header>

            <main className="no-scrollbar flex flex-1 flex-col gap-0 overflow-y-auto pb-6">
                <div className="flex items-center justify-between border-b border-border-dark bg-background-dark px-4 py-3 text-xs text-zinc-500 uppercase">
                    <span>{libraryItems.length} ITEMS</span>
                    <div className="flex items-center gap-4">
                        <div className="relative" ref={sortDropdownRef}>
                            <button
                                onClick={() => setIsSortDropdownOpen(!isSortDropdownOpen)}
                                className="flex items-center gap-1 transition-colors hover:text-primary"
                            >
                                {sortLabels[currentSort]}{' '}
                                <AppIcon name={isSortDropdownOpen ? 'arrow_drop_up' : 'arrow_drop_down'} className="text-[14px]" />
                            </button>
                            {isSortDropdownOpen && (
                                <div className="absolute top-full right-0 z-50 mt-2 w-48 border border-border-dark bg-surface-dark shadow-xl">
                                    {Object.entries(sortLabels).map(([sortKey, label]) => (
                                        <Link
                                            key={sortKey}
                                            href={libraryRoute.url({
                                                query: {
                                                    status: currentStatus,
                                                    sort: sortKey,
                                                },
                                            })}
                                            only={['libraryItems', 'currentStatus', 'currentSort', 'counts']}
                                            preserveScroll
                                            preserveState
                                            className={`block px-4 py-2 text-left transition-colors hover:bg-border-dark hover:text-white ${
                                                currentSort === sortKey ? 'text-primary' : 'text-zinc-400'
                                            }`}
                                            onClick={() => setIsSortDropdownOpen(false)}
                                        >
                                            {label}
                                        </Link>
                                    ))}
                                </div>
                            )}
                        </div>
                        <button className="hover:text-primary">
                            <AppIcon name="grid_view" className="text-[18px]" />
                        </button>
                    </div>
                </div>

                {libraryItems.length > 0 ? (
                    <div className="grid grid-cols-1 gap-0">
                        {visibleItems.map((item) => (
                            <Link
                                key={item.id}
                                href={showManga.url(item.manga.id)}
                                className="group relative flex gap-4 border-b border-border-dark p-4 transition-colors hover:bg-surface-dark"
                            >
                                <div className="relative aspect-[2/3] w-20 shrink-0 border border-border-dark bg-surface-dark">
                                    <div
                                        className="absolute inset-0 bg-cover bg-center grayscale transition-all duration-300 group-hover:grayscale-0"
                                        style={{
                                            backgroundImage: buildBackgroundImage(item.manga.cover_image_url),
                                        }}
                                    ></div>
                                    {item.is_favorite && <div className="absolute -top-1 -left-1 z-10 h-2 w-2 bg-primary"></div>}
                                </div>
                                <div className="flex flex-1 flex-col justify-between py-1">
                                    <div>
                                        <div className="mb-1 flex items-start justify-between">
                                            <h3 className="text-lg leading-tight font-bold text-text-light uppercase transition-colors group-hover:text-primary">
                                                {item.manga.title}
                                            </h3>
                                            <button className="text-zinc-500 hover:text-primary">
                                                <AppIcon name="more_vert" className="text-[20px]" />
                                            </button>
                                        </div>
                                        <p className="text-xs text-zinc-500 uppercase">
                                            Chap {item.current_chapter_number}
                                            {unreadCount(item) > 0 && ` • Unread: ${unreadCount(item)}`}
                                        </p>
                                    </div>
                                    <div className="mt-2 flex items-end justify-between">
                                        <div className="mr-4 flex w-full flex-col gap-1">
                                            <div className="flex justify-between text-[10px] font-bold uppercase">
                                                <span className={item.progress_percentage === 100 ? 'text-zinc-500' : 'text-primary'}>
                                                    {item.progress_percentage === 100 ? 'Up to Date' : 'Progress'}
                                                </span>
                                                <span className={item.progress_percentage === 100 ? 'text-zinc-500' : 'text-primary'}>
                                                    {item.progress_percentage}%
                                                </span>
                                            </div>
                                            <div className="h-2 w-full border border-border-dark bg-border-dark p-[1px]">
                                                <div
                                                    className={`h-full ${item.progress_percentage === 100 ? 'bg-zinc-500' : 'bg-primary'}`}
                                                    style={{ width: `${item.progress_percentage}%` }}
                                                ></div>
                                            </div>
                                        </div>
                                        <button className="border border-border-dark bg-border-dark p-2 text-text-light transition-all hover:bg-primary hover:text-black">
                                            <AppIcon name={item.progress_percentage === 100 ? 'check' : 'play_arrow'} className="block text-[18px]" />
                                        </button>
                                    </div>
                                </div>
                            </Link>
                        ))}
                    </div>
                ) : (
                    <div className="flex flex-1 flex-col items-center justify-center p-8">
                        <div className="border border-border-dark bg-surface-dark p-8 text-center">
                            <AppIcon name="menu_book" className="mb-4 block text-4xl text-zinc-600" />
                            <p className="mb-2 text-sm font-bold text-zinc-400 uppercase">
                                {currentStatus === 'all' ? 'No Library Items' : `No ${statusLabels[currentStatus]} Items`}
                            </p>
                            <p className="mb-4 text-xs text-zinc-500">Start adding manga to your library</p>
                            <Link
                                href={searchRoute.url()}
                                className="inline-flex items-center gap-2 border border-primary bg-primary px-4 py-2 text-xs font-bold text-black uppercase transition-all hover:bg-white"
                            >
                                <AppIcon name="search" className="text-sm" />
                                Browse Manga
                            </Link>
                        </div>
                    </div>
                )}

                {hasMoreItems && (
                    <div className="p-4">
                        <button
                            type="button"
                            onClick={() => setVisibleItemsCount((count) => count + LIBRARY_PAGE_SIZE)}
                            className="w-full border border-border-dark bg-background-dark py-4 text-sm font-bold text-zinc-500 uppercase transition-all hover:border-primary hover:bg-surface-dark hover:text-primary"
                        >
                            LOAD MORE ({remainingItemCount})
                        </button>
                    </div>
                )}
            </main>
        </AppLayout>
    );
}
