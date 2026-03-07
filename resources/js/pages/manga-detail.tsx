import { Deferred, Head, Link, router, usePage } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import { home as homeRoute, search as searchRoute } from '@/routes';
import { toggle as toggleLibraryBookmark } from '@/routes/library/bookmark';
import { read as readManga, show as showManga } from '@/routes/manga';
import AppIcon from '../components/AppIcon';
import Header from '../components/Header';
import { resolveImageUrl } from '../lib/image';

interface Genre {
    id: number;
    name: string;
}

interface Chapter {
    id: string;
    local_id: number;
    external_id: string | null;
    chapter_number: number;
    chapter_label: string | null;
    title: string | null;
    language: string;
    published_at: string | null;
    is_new: boolean;
}

interface Manga {
    id: string; // Weebdex manga id
    first_chapter_id: string | null;
    title: string;
    description: string;
    cover_image_url: string;
    banner_image_url: string | null;
    author: string | null;
    artist: string | null;
    status: string;
    rating_average: number | null;
    rating_count: number;
    total_chapters: number;
    total_views: number | null;
    is_mature: boolean;
    genres: Genre[];
}

interface LibraryStatus {
    id: number;
    status: 'reading' | 'completed' | 'on_hold' | 'dropped' | 'planned';
    current_chapter_id: string | null;
    current_chapter_number: number;
    progress_percentage: number;
    is_favorite: boolean;
}

interface MangaDetailProps {
    auth: {
        user: {
            name: string;
        } | null;
    };
    manga: Manga;
    statistics?: {
        total_views: number;
        follows_count: number;
        total_chapters: number;
    };
    chapters: Chapter[];
    libraryStatus: LibraryStatus | null;
    [key: string]: unknown;
}

function timeAgo(dateString: string | null): string {
    if (!dateString) return 'Unknown';
    const date = new Date(dateString);
    const now = new Date();
    const diffInHours = Math.floor((now.getTime() - date.getTime()) / (1000 * 60 * 60));

    if (diffInHours < 1) return 'Just now';
    if (diffInHours < 24) return `${diffInHours}h ago`;
    const diffInDays = Math.floor(diffInHours / 24);
    if (diffInDays < 7) return `${diffInDays}d ago`;
    if (diffInDays < 30) return `${Math.floor(diffInDays / 7)}w ago`;
    return `${Math.floor(diffInDays / 30)}mo ago`;
}

function formatStatus(status: string): string {
    return status.replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase());
}

function normalizeDescriptionText(value: string): string {
    return value
        .replace(/<\s*br\s*\/?>/gi, '\n')
        .replace(/<\s*\/p\s*>/gi, '\n\n')
        .replace(/<\s*\/li\s*>/gi, '\n')
        .replace(/<[^>]+>/g, '')
        .trim();
}

function ChapterList({ manga, libraryStatus }: { manga: Manga; libraryStatus: LibraryStatus | null }) {
    const { chapters } = usePage<MangaDetailProps>().props;
    const [sortDirection, setSortDirection] = useState<'desc' | 'asc'>('desc');
    const chaptersWithProviderId = (chapters || []).filter((chapter) => Boolean(chapter.external_id));
    const sortedChapters = [...chaptersWithProviderId].sort((a, b) => {
        return sortDirection === 'desc' ? b.chapter_number - a.chapter_number : a.chapter_number - b.chapter_number;
    });
    const chapterCount = chaptersWithProviderId.length;

    return (
        <>
            <div className="sticky top-0 z-10 flex items-center justify-between border-b border-border-dark bg-background-dark px-4 py-3">
                <h3 className="flex items-center gap-2 text-sm font-bold tracking-wider uppercase">
                    <span className="inline-block size-2 bg-primary"></span>
                    Chapter Index ({chapterCount})
                </h3>
                <div className="flex gap-2">
                    <button
                        type="button"
                        onClick={() => setSortDirection((current) => (current === 'desc' ? 'asc' : 'desc'))}
                        className={`transition-colors hover:text-primary ${sortDirection === 'desc' ? 'text-primary' : 'text-zinc-500'}`}
                        aria-label={`Sort chapters ${sortDirection === 'desc' ? 'ascending' : 'descending'}`}
                    >
                        <AppIcon name="sort" className={`text-lg transition-transform duration-200 ${sortDirection === 'asc' ? 'rotate-180' : ''}`} />
                    </button>
                </div>
            </div>

            {sortedChapters.length > 0 ? (
                <div className="divide-y divide-border-dark border-b border-border-dark">
                    {sortedChapters.map((chapter) => {
                        const isNew = chapter.is_new;
                        const isRead = libraryStatus
                            ? chapter.chapter_number < libraryStatus.current_chapter_number ||
                              (chapter.chapter_number === libraryStatus.current_chapter_number &&
                                  chapter.external_id === libraryStatus.current_chapter_id)
                            : false;
                        const chapterTitle = chapter.title || (chapter.chapter_label ? `Chapter ${chapter.chapter_label}` : 'Chapter Release');
                        const chapterReadId = chapter.external_id;

                        if (!chapterReadId) {
                            return null;
                        }

                        return (
                            <Link
                                key={chapterReadId}
                                href={readManga.url({ id: manga.id, chapterId: chapterReadId })}
                                className={`group flex cursor-pointer items-center justify-between border-l-2 p-4 transition-colors hover:border-primary hover:bg-surface-dark ${
                                    isRead ? 'border-transparent' : 'border-primary'
                                }`}
                            >
                                <div className="flex flex-col gap-1">
                                    <span
                                        className={`text-sm font-bold transition-colors ${isRead ? 'text-zinc-500' : 'text-text-light group-hover:text-primary'}`}
                                    >
                                        CHP. {chapter.chapter_number}
                                    </span>
                                    <span className="max-w-[200px] truncate text-xs text-zinc-500 uppercase">{chapterTitle}</span>
                                </div>
                                <div className="flex flex-col items-end gap-1">
                                    {isNew ? (
                                        <span className="border border-primary/30 bg-primary/10 px-1 py-0.5 text-[10px] text-primary">NEW</span>
                                    ) : (
                                        <span className="text-[10px] text-zinc-600">{timeAgo(chapter.published_at)}</span>
                                    )}
                                    {isNew && <span className="text-[10px] font-bold text-zinc-600">{timeAgo(chapter.published_at)}</span>}
                                </div>
                            </Link>
                        );
                    })}
                </div>
            ) : (
                <div className="p-8 text-center">
                    <p className="text-sm text-zinc-500">No chapters available yet.</p>
                </div>
            )}
        </>
    );
}

function ChapterListSkeleton() {
    return (
        <>
            <div className="sticky top-0 z-10 flex items-center justify-between border-b border-border-dark bg-background-dark px-4 py-3">
                <h3 className="flex items-center gap-2 text-sm font-bold tracking-wider uppercase">
                    <span className="inline-block size-2 bg-primary"></span>
                    Chapter Index
                </h3>
                <div className="flex gap-2">
                    <button className="text-zinc-500 transition-colors hover:text-primary">
                        <AppIcon name="sort" className="text-lg" />
                    </button>
                    <button className="text-zinc-500 transition-colors hover:text-primary">
                        <AppIcon name="filter_list" className="text-lg" />
                    </button>
                </div>
            </div>
            <div className="divide-y divide-border-dark border-b border-border-dark">
                {Array.from({ length: 5 }).map((_, i) => (
                    <div key={i} className="flex items-center justify-between border-l-2 border-border-dark p-4">
                        <div className="flex flex-col gap-2">
                            <div className="h-4 w-20 animate-pulse rounded bg-surface-dark"></div>
                            <div className="h-3 w-32 animate-pulse rounded bg-surface-dark"></div>
                        </div>
                        <div className="h-4 w-12 animate-pulse rounded bg-surface-dark"></div>
                    </div>
                ))}
            </div>
        </>
    );
}

export default function MangaDetail() {
    const { auth, manga, statistics, libraryStatus } = usePage<MangaDetailProps>().props;
    const userName = auth.user?.name ?? 'Operator';
    const buildBackgroundImage = (imageUrl: string | null | undefined): string => {
        const resolvedImageUrl = resolveImageUrl(imageUrl);

        return resolvedImageUrl ? `url("${resolvedImageUrl}")` : 'none';
    };
    const descriptionText = useMemo(() => normalizeDescriptionText(manga.description ?? ''), [manga.description]);
    const continueChapterId = libraryStatus?.current_chapter_id ?? manga.first_chapter_id;
    const startChapterId = manga.first_chapter_id;
    const [isBookmarked, setIsBookmarked] = useState<boolean>(Boolean(libraryStatus));

    useEffect(() => {
        setIsBookmarked(Boolean(libraryStatus));
    }, [libraryStatus]);

    const handleBookmark = (): void => {
        const wasBookmarked = isBookmarked;
        setIsBookmarked(!wasBookmarked);

        router.post(
            toggleLibraryBookmark.url({ mangaId: manga.id }),
            {},
            {
                preserveScroll: true,
                preserveState: true,
                onError: () => {
                    setIsBookmarked(wasBookmarked);
                },
            },
        );
    };

    return (
        <div className="min-h-screen bg-background-dark font-mono text-text-light antialiased selection:bg-primary selection:text-black">
            <Head title={manga.title} />
            <div className="relative mx-auto flex w-full max-w-md flex-col border-x border-border-dark bg-background-dark">
                <Header className="z-50 flex-row items-center justify-between backdrop-blur-md">
                    <Link
                        href={homeRoute.url()}
                        className="flex size-10 items-center justify-center border border-border-dark bg-surface-dark transition-colors hover:bg-border-dark hover:text-primary active:translate-y-0.5"
                    >
                        <AppIcon name="arrow_back" />
                    </Link>
                    <div className="flex flex-col items-center gap-1">
                        <h1 className="text-xs font-bold tracking-[0.2em] text-primary uppercase">Sys.Manga_ID: {manga.id.slice(0, 8)}</h1>
                        <span className="text-[10px] font-bold tracking-[0.2em] text-zinc-500 uppercase">{userName}</span>
                    </div>
                    <button className="flex size-10 items-center justify-center border border-border-dark bg-surface-dark transition-colors hover:bg-border-dark hover:text-primary active:translate-y-0.5">
                        <AppIcon name="share" />
                    </button>
                </Header>

                <main className="no-scrollbar flex-1 overflow-y-auto pb-10">
                    <section className="border-b border-border-dark p-4">
                        <div className="group relative mb-6 aspect-[3/4] w-full overflow-hidden border border-border-dark bg-surface-dark">
                            <div
                                className="absolute inset-0 bg-cover bg-center bg-no-repeat grayscale-[20%] filter transition-transform duration-500 group-hover:scale-105"
                                style={{
                                    backgroundImage: buildBackgroundImage(manga.cover_image_url),
                                }}
                            ></div>
                            <div className="absolute top-0 right-0 border-b border-l border-black bg-primary px-3 py-1 text-[10px] font-bold text-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                Status: {formatStatus(manga.status)}
                            </div>
                            {libraryStatus?.is_favorite && (
                                <div className="absolute top-0 left-0 border-r border-b border-black bg-yellow-500 px-3 py-1 text-[10px] font-bold text-black uppercase">
                                    ★ FAVORITE
                                </div>
                            )}
                            <div className="pointer-events-none absolute inset-0 border-[0.5px] border-white/10"></div>
                            <div className="absolute bottom-0 left-0 h-1/3 w-full bg-gradient-to-t from-background-dark to-transparent"></div>
                        </div>

                        <div className="mb-6 flex flex-col gap-2">
                            <h2 className="text-3xl leading-tight font-bold tracking-tight text-white uppercase">{manga.title}</h2>
                            <div className="flex items-center gap-2 text-xs font-bold tracking-wider text-zinc-500 uppercase">
                                <span className="text-primary">{manga.author || 'Unknown Author'}</span>
                                {manga.artist && manga.artist !== manga.author && (
                                    <>
                                        <span className="text-zinc-700">//</span>
                                        <span>Art: {manga.artist}</span>
                                    </>
                                )}
                            </div>
                        </div>

                        <div className="mb-6 grid grid-cols-3 gap-px border border-border-dark bg-border-dark">
                            <div className="flex flex-col gap-1 bg-background-dark p-3 text-center transition-colors hover:bg-surface-dark">
                                <span className="text-[10px] tracking-widest text-zinc-500 uppercase">Views</span>
                                <Deferred data="statistics" fallback={<span className="mx-auto h-5 w-14 animate-pulse rounded bg-surface-dark" />}>
                                    <span className="font-bold text-text-light">
                                        {((statistics?.total_views ?? manga.total_views ?? 0) / 1000).toFixed(1)}K
                                    </span>
                                </Deferred>
                            </div>
                            <div className="flex flex-col gap-1 bg-background-dark p-3 text-center transition-colors hover:bg-surface-dark">
                                <span className="text-[10px] tracking-widest text-zinc-500 uppercase">Chapters</span>
                                <Deferred data="statistics" fallback={<span className="mx-auto h-5 w-14 animate-pulse rounded bg-surface-dark" />}>
                                    <span className="font-bold text-text-light">{statistics?.total_chapters ?? manga.total_chapters}</span>
                                </Deferred>
                            </div>
                            <div className="flex flex-col gap-1 bg-background-dark p-3 text-center transition-colors hover:bg-surface-dark">
                                <span className="text-[10px] tracking-widest text-zinc-500 uppercase">Follows</span>
                                <Deferred data="statistics" fallback={<span className="mx-auto h-5 w-14 animate-pulse rounded bg-surface-dark" />}>
                                    <span className="font-bold text-text-light">
                                        {((statistics?.follows_count ?? manga.rating_count ?? 0) / 1000).toFixed(1)}K
                                    </span>
                                </Deferred>
                            </div>
                        </div>

                        <div className="mb-8 flex gap-3">
                            {libraryStatus ? (
                                <Link
                                    href={continueChapterId ? readManga.url({ id: manga.id, chapterId: continueChapterId }) : showManga.url(manga.id)}
                                    className="flex h-12 flex-1 items-center justify-center gap-2 border border-primary bg-primary text-sm font-bold text-background-dark uppercase transition-colors hover:bg-white active:translate-y-0.5"
                                >
                                    <AppIcon name="menu_book" />
                                    {libraryStatus.progress_percentage > 0 ? `CONTINUE CH. ${libraryStatus.current_chapter_number}` : 'READ NOW'}
                                </Link>
                            ) : (
                                <Link
                                    href={startChapterId ? readManga.url({ id: manga.id, chapterId: startChapterId }) : showManga.url(manga.id)}
                                    className="flex h-12 flex-1 items-center justify-center gap-2 border border-primary bg-primary text-sm font-bold text-background-dark uppercase transition-colors hover:bg-white active:translate-y-0.5"
                                >
                                    <AppIcon name="menu_book" />
                                    START READING
                                </Link>
                            )}
                            <button
                                type="button"
                                onClick={handleBookmark}
                                className={`flex size-12 items-center justify-center border transition-colors active:translate-y-0.5 ${
                                    isBookmarked
                                        ? 'border-primary bg-primary text-black shadow-[0_0_0_1px_rgba(0,0,0,0.2)_inset] hover:brightness-95'
                                        : 'border-border-dark bg-surface-dark text-text-light hover:border-primary hover:text-primary'
                                }`}
                            >
                                <AppIcon name={isBookmarked ? 'bookmarks' : 'bookmark_add'} />
                            </button>
                        </div>

                        <div className="mb-6 space-y-3">
                            <h3 className="border-l-2 border-primary pl-3 text-xs font-bold tracking-widest text-primary uppercase">Synopsis_Log</h3>
                            <p className="ml-[1px] border-l border-border-dark pl-3 text-sm leading-relaxed whitespace-pre-line text-zinc-400">
                                {descriptionText || 'No synopsis available.'}
                            </p>
                        </div>

                        <div className="flex flex-wrap gap-2">
                            {manga.genres.map((genre) => (
                                <Link
                                    key={genre.id}
                                    href={searchRoute.url({
                                        query: {
                                            q: genre.name,
                                        },
                                    })}
                                    className="cursor-pointer border border-border-dark bg-surface-dark px-3 py-1.5 text-[10px] tracking-wider text-zinc-300 uppercase transition-colors hover:border-primary hover:bg-background-dark hover:text-primary"
                                >
                                    {genre.name}
                                </Link>
                            ))}
                        </div>
                    </section>

                    <section>
                        <Deferred data="chapters" fallback={<ChapterListSkeleton />}>
                            <ChapterList manga={manga} libraryStatus={libraryStatus} />
                        </Deferred>
                    </section>
                </main>
            </div>
        </div>
    );
}
