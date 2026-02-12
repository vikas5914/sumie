import { Head, Link, usePage } from '@inertiajs/react';
import AppIcon from '../components/AppIcon';
import Header from '../components/Header';
import { readImageProxyPreference, resolveImageUrl } from '../lib/image';

interface MangaReaderProps {
    auth: {
        user: {
            use_image_proxy?: boolean;
        } | null;
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
    images: Array<{
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

export default function MangaReader() {
    const { auth, manga, chapter, images, navigation, source_url } = usePage<MangaReaderProps>().props;
    const useImageProxy = readImageProxyPreference(Boolean(auth.user?.use_image_proxy));
    const chapterLabel = chapter.label ?? chapter.number.toString();

    return (
        <div className="min-h-screen bg-black font-mono text-text-light antialiased">
            <Head title={`${manga.title} - Chapter ${chapterLabel}`} />

            <Header className="bg-black/90 px-4 py-5">
                <div className="mx-auto flex w-full max-w-3xl items-center justify-between gap-3">
                    <div className="flex min-w-0 items-center gap-2">
                        <Link
                            href={`/manga/${manga.id}`}
                            prefetch
                            className="flex size-9 shrink-0 items-center justify-center border border-border-dark bg-surface-dark transition-colors hover:border-primary hover:text-primary"
                        >
                            <AppIcon name="arrow_back" />
                        </Link>
                        <div className="min-w-0">
                            <h1 className="truncate text-sm font-bold uppercase">{manga.title}</h1>
                            <p className="truncate text-[10px] tracking-wider text-primary uppercase">
                                Chapter {chapterLabel} {chapter.title ? `• ${chapter.title}` : ''}
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

            <main className="mx-auto flex w-full max-w-3xl flex-col gap-3 px-3 py-4 pb-24">
                {images.length > 0 ? (
                    images.map((image) => (
                        <img
                            key={image.id}
                            src={resolveImageUrl(image.url, useImageProxy) ?? image.url}
                            alt={`${manga.title} Chapter ${chapterLabel} Page ${image.id}`}
                            width={image.width ?? undefined}
                            height={image.height ?? undefined}
                            loading="lazy"
                            decoding="async"
                            className="w-full border border-border-dark bg-surface-dark"
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
            </main>

            <footer className="fixed right-0 bottom-0 left-0 z-20 border-t border-border-dark bg-black/90 backdrop-blur-sm">
                <div className="mx-auto flex w-full max-w-3xl gap-2 px-3 py-3">
                    {navigation.previous_chapter_id ? (
                        <Link
                            href={`/manga/${manga.id}/read/${navigation.previous_chapter_id}`}
                            prefetch
                            className="flex h-10 flex-1 items-center justify-center gap-1 border border-border-dark bg-surface-dark text-xs font-bold uppercase transition-colors hover:border-primary hover:text-primary"
                        >
                            <AppIcon name="chevron_left" className="text-base" />
                            Previous
                        </Link>
                    ) : (
                        <span className="flex h-10 flex-1 items-center justify-center border border-border-dark bg-black text-xs font-bold text-zinc-600 uppercase">
                            Start
                        </span>
                    )}
                    {navigation.next_chapter_id ? (
                        <Link
                            href={`/manga/${manga.id}/read/${navigation.next_chapter_id}`}
                            prefetch
                            className="flex h-10 flex-1 items-center justify-center gap-1 border border-primary bg-primary text-xs font-bold text-background-dark uppercase transition-colors hover:bg-white"
                        >
                            Next
                            <AppIcon name="chevron_right" className="text-base" />
                        </Link>
                    ) : (
                        <span className="flex h-10 flex-1 items-center justify-center border border-border-dark bg-black text-xs font-bold text-zinc-600 uppercase">
                            End
                        </span>
                    )}
                </div>
            </footer>
        </div>
    );
}
