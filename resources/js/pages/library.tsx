import { Head, Link, usePage } from '@inertiajs/react';
import AppIcon from '../components/AppIcon';
import Header from '../components/Header';
import AppLayout from '../layouts/AppLayout';
import { readImageProxyPreference, resolveImageUrl } from '../lib/image';

interface Genre {
    id: number;
    name: string;
}

interface Manga {
    id: string; // Comick slug
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
    reading: number;
    completed: number;
    downloaded: number;
    dropped: number;
}

interface LibraryProps {
    auth: {
        user: {
            name: string;
            use_image_proxy?: boolean;
        } | null;
    };
    libraryItems: LibraryItem[];
    currentStatus: string;
    counts: LibraryCounts;
    [key: string]: unknown;
}

const statusLabels: Record<string, string> = {
    reading: 'Reading',
    completed: 'Completed',
    on_hold: 'On Hold',
    dropped: 'Dropped',
    planned: 'Planned',
};

const statusOrder = ['reading', 'completed', 'on_hold', 'dropped', 'planned'];

export default function Library() {
    const { auth, libraryItems, currentStatus, counts } = usePage<LibraryProps>().props;
    const userName = auth.user?.name ?? 'Operator';
    const useImageProxy = readImageProxyPreference(Boolean(auth.user?.use_image_proxy));
    const buildBackgroundImage = (imageUrl: string | null | undefined): string => {
        const resolvedImageUrl = resolveImageUrl(imageUrl, useImageProxy);

        return resolvedImageUrl ? `url("${resolvedImageUrl}")` : 'none';
    };

    const unreadCount = (item: LibraryItem): number => {
        return Math.max(0, item.manga.total_chapters - item.current_chapter_number);
    };

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
                            href="/search"
                            prefetch
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
                                href={`/library?status=${status}`}
                                prefetch
                                only={['libraryItems', 'currentStatus', 'counts']}
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
                        <button className="flex items-center gap-1 hover:text-primary">
                            LAST READ <AppIcon name="arrow_drop_down" className="text-[14px]" />
                        </button>
                        <button className="hover:text-primary">
                            <AppIcon name="grid_view" className="text-[18px]" />
                        </button>
                    </div>
                </div>

                {libraryItems.length > 0 ? (
                    <div className="grid grid-cols-1 gap-0">
                        {libraryItems.map((item) => (
                            <Link
                                key={item.id}
                                href={`/manga/${item.manga.id}`}
                                prefetch
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
                            <p className="mb-2 text-sm font-bold text-zinc-400 uppercase">No {statusLabels[currentStatus]} Items</p>
                            <p className="mb-4 text-xs text-zinc-500">Start adding manga to your library</p>
                            <Link
                                href="/search"
                                prefetch
                                className="inline-flex items-center gap-2 border border-primary bg-primary px-4 py-2 text-xs font-bold text-black uppercase transition-all hover:bg-white"
                            >
                                <AppIcon name="search" className="text-sm" />
                                Browse Manga
                            </Link>
                        </div>
                    </div>
                )}

                {libraryItems.length > 0 && (
                    <div className="p-4">
                        <button className="w-full border border-border-dark bg-background-dark py-4 text-sm font-bold text-zinc-500 uppercase transition-all hover:border-primary hover:bg-surface-dark hover:text-primary">
                            // Load More Logs
                        </button>
                    </div>
                )}
            </main>
        </AppLayout>
    );
}
