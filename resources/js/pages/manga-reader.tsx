import { Deferred, Head, Link, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { read as readManga, show as showManga } from '@/routes/manga';
import AppIcon from '../components/AppIcon';
import Header from '../components/Header';
import { resolveImageUrl } from '../lib/image';

type ReadingMode = 'vertical_scroll' | 'page_by_page';

const READING_MODE_KEY = 'sumie:reading-mode';

interface MangaReaderProps {
    auth: {
        user: Record<string, unknown> | null;
    };
    manga: {
        id: string;
        title: string;
        cover_image_url: string | null;
    };
    chapter: {
        id: string;
        number: number;
        label: string | null;
        title: string;
        page_count: number;
    };
    images?: Array<{
        id: number;
        url: string;
        width: number | null;
        height: number | null;
    }>;
    navigation: {
        previous_chapter_id: string | null;
        next_chapter_id: string | null;
    };
    source_url: string | null;
    [key: string]: unknown;
}

function ReaderPagesSkeleton({ isPageByPageMode }: { isPageByPageMode: boolean }) {
    return (
        <>
            {Array.from({ length: isPageByPageMode ? 1 : 4 }).map((_, index) => (
                <div
                    key={index}
                    className={`w-full animate-pulse border border-border-dark bg-surface-dark ${
                        isPageByPageMode ? 'h-[calc(100vh-14rem)] snap-start' : 'aspect-2/3'
                    }`}
                />
            ))}
        </>
    );
}

export default function MangaReader() {
    const { manga, chapter, images = [], navigation, source_url } = usePage<MangaReaderProps>().props;

    const chapterLabel = chapter.label ?? chapter.number.toString();
    const [readingMode, setReadingMode] = useState<ReadingMode>('vertical_scroll');
    const isPageByPageMode = readingMode === 'page_by_page';

    useEffect(() => {
        if (typeof window === 'undefined') {
            return;
        }

        const storedMode = window.localStorage.getItem(READING_MODE_KEY);

        if (storedMode === 'vertical_scroll' || storedMode === 'page_by_page') {
            setReadingMode(storedMode);
        }
    }, []);

    return (
        <div className="min-h-screen bg-background-dark font-mono text-text-light antialiased selection:bg-primary selection:text-black">
            <Head title={`${manga.title} - Chapter ${chapterLabel}`} />

            <div className="relative mx-auto flex w-full max-w-md flex-col overflow-hidden border-x border-border-dark pb-24">
                <Header className="bg-background-dark/90">
                    <div className="flex w-full items-center justify-between gap-3">
                        <div className="flex min-w-0 items-center gap-2">
                            <Link
                                href={showManga.url(manga.id)}
                                className="flex size-9 shrink-0 items-center justify-center border border-border-dark bg-surface-dark transition-colors hover:border-primary hover:text-primary"
                            >
                                <AppIcon name="arrow_back" />
                            </Link>
                            <div className="min-w-0">
                                <h1 className="truncate text-sm font-bold uppercase">{manga.title}</h1>
                                <p className="truncate text-[10px] tracking-wider text-primary uppercase">
                                    Chapter {chapterLabel} {chapter.title ? `• ${chapter.title}` : ''} • {isPageByPageMode ? 'PAGED' : 'VERTICAL'}
                                </p>
                            </div>
                        </div>
                        {source_url && (
                            <a
                                href={source_url}
                                target="_blank"
                                rel="noreferrer noopener"
                                className="flex h-9 items-center gap-1 border border-border-dark bg-surface-dark px-3 text-xs font-bold uppercase transition-colors hover:border-primary hover:text-primary"
                            >
                                Source
                                <AppIcon name="open_in_new" className="text-base" />
                            </a>
                        )}
                    </div>
                </Header>

                <main
                    className={`flex w-full flex-col gap-3 px-4 py-4 ${
                        isPageByPageMode ? 'h-[calc(100vh-11rem)] snap-y snap-mandatory overflow-y-auto pb-8' : ''
                    }`}
                >
                    <Deferred data="images" fallback={<ReaderPagesSkeleton isPageByPageMode={isPageByPageMode} />}>
                        {images.length > 0 ? (
                            images.map((image) => (
                                <img
                                    key={image.id}
                                    src={resolveImageUrl(image.url) ?? image.url}
                                    alt={`${manga.title} Chapter ${chapterLabel} Page ${image.id}`}
                                    width={image.width ?? undefined}
                                    height={image.height ?? undefined}
                                    loading="lazy"
                                    decoding="async"
                                    className={`w-full border border-border-dark bg-surface-dark ${isPageByPageMode ? 'snap-start' : ''}`}
                                />
                            ))
                        ) : (
                            <div className="flex flex-col items-center gap-3 border border-border-dark bg-surface-dark px-4 py-10 text-center">
                                <p className="text-sm text-zinc-400">No page images are available for this chapter yet.</p>
                                {source_url && (
                                    <a
                                        href={source_url}
                                        target="_blank"
                                        rel="noreferrer noopener"
                                        className="border border-primary bg-primary px-3 py-2 text-xs font-bold text-background-dark uppercase transition-colors hover:bg-white"
                                    >
                                        Open Source Chapter
                                    </a>
                                )}
                            </div>
                        )}
                    </Deferred>
                </main>

                <footer
                    className="fixed right-0 bottom-0 left-0 z-40 mx-auto max-w-md border-t border-border-dark bg-background-dark/95 backdrop-blur-sm"
                    style={{ paddingBottom: 'var(--inset-bottom, 0px)' }}
                >
                    <div className="flex w-full gap-2 px-4 py-3">
                        {navigation.previous_chapter_id ? (
                            <Link
                                href={readManga.url({ id: manga.id, chapterId: navigation.previous_chapter_id })}
                                className="flex h-10 flex-1 items-center justify-center gap-1 border border-border-dark bg-surface-dark text-xs font-bold uppercase transition-colors hover:border-primary hover:text-primary"
                            >
                                <AppIcon name="chevron_left" className="text-base" />
                                Previous
                            </Link>
                        ) : (
                            <span className="flex h-10 flex-1 items-center justify-center border border-border-dark bg-background-dark text-xs font-bold text-zinc-600 uppercase">
                                Start
                            </span>
                        )}
                        {navigation.next_chapter_id ? (
                            <Link
                                href={readManga.url({ id: manga.id, chapterId: navigation.next_chapter_id })}
                                className="flex h-10 flex-1 items-center justify-center gap-1 border border-primary bg-primary text-xs font-bold text-background-dark uppercase transition-colors hover:bg-white"
                            >
                                Next
                                <AppIcon name="chevron_right" className="text-base" />
                            </Link>
                        ) : (
                            <span className="flex h-10 flex-1 items-center justify-center border border-border-dark bg-background-dark text-xs font-bold text-zinc-600 uppercase">
                                End
                            </span>
                        )}
                    </div>
                </footer>
            </div>
        </div>
    );
}
