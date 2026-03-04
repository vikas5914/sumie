import { Link, router, usePage } from '@inertiajs/react';
import { home, library, me, search } from '@/routes';
import AppIcon from '../components/AppIcon';

export default function AppLayout({ children }: { children: React.ReactNode }) {
    const { url } = usePage();
    const homePath = home.url();
    const searchPath = search.url();
    const libraryPath = library.url();
    const mePath = me.url();

    const isActive = (path: string) => url.startsWith(path);
    const handleLibraryClick = (event: React.MouseEvent) => {
        if (!isActive(libraryPath)) {
            return;
        }

        event.preventDefault();
        router.visit(libraryPath, {
            fresh: true,
            preserveState: false,
            preserveScroll: false,
            replace: true,
        });
    };

    return (
        <div className="min-h-screen bg-background-dark font-mono text-text-light antialiased selection:bg-primary selection:text-black">
            <div className="relative mx-auto flex min-h-screen w-full max-w-md flex-col overflow-hidden rounded-t-lg border-x border-border-dark pb-24">
                {children}

                <nav className="fixed right-0 bottom-0 left-0 z-40 mx-auto max-w-md bg-background-dark/95 backdrop-blur-sm" style={{ paddingBottom: 'var(--inset-bottom, 0px)' }}>
                    <div className="flex h-16 items-center justify-around border-t border-border-dark bg-background-dark/95 px-2 backdrop-blur-sm">
                        <Link
                            href={homePath}
                            className={`group flex h-full w-14 flex-col items-center justify-center gap-1 transition-colors ${
                                isActive(homePath) ? 'text-primary' : 'text-zinc-500 hover:text-text-light'
                            }`}
                        >
                            {isActive(homePath) && <div className="absolute top-0 h-[2px] w-14 bg-primary shadow-[0_0_8px_rgba(76,255,0,0.8)]" />}
                            <AppIcon
                                name="home"
                                className={`text-[22px] ${!isActive(homePath) && 'transition-transform group-hover:-translate-y-1'}`}
                            />
                            <span className="text-[9px] font-bold tracking-wider uppercase">Home</span>
                        </Link>

                        <Link
                            href={searchPath}
                            className={`group flex h-full w-14 flex-col items-center justify-center gap-1 transition-colors ${
                                isActive(searchPath) ? 'text-primary' : 'text-zinc-500 hover:text-text-light'
                            }`}
                        >
                            {isActive(searchPath) && <div className="absolute top-0 h-[2px] w-14 bg-primary shadow-[0_0_8px_rgba(76,255,0,0.8)]" />}
                            <AppIcon
                                name="manage_search"
                                className={`text-[22px] ${!isActive(searchPath) && 'transition-transform group-hover:-translate-y-1'}`}
                            />
                            <span className="text-[9px] font-bold tracking-wider uppercase">Search</span>
                        </Link>

                        <Link
                            href={libraryPath}
                            fresh
                            onClick={handleLibraryClick}
                            className={`group flex h-full w-14 flex-col items-center justify-center gap-1 transition-colors ${
                                isActive(libraryPath) ? 'text-primary' : 'text-zinc-500 hover:text-text-light'
                            }`}
                        >
                            {isActive(libraryPath) && (
                                <div className="absolute top-0 h-[2px] w-14 bg-primary shadow-[0_0_8px_rgba(76,255,0,0.8)]" />
                            )}
                            <AppIcon
                                name="bookmarks"
                                className={`text-[22px] ${!isActive(libraryPath) && 'transition-transform group-hover:-translate-y-1'}`}
                            />
                            <span className="text-[9px] font-bold tracking-wider uppercase">Lib</span>
                        </Link>

                        <Link
                            href={mePath}
                            className={`group flex h-full w-14 flex-col items-center justify-center gap-1 transition-colors ${
                                isActive(mePath) ? 'text-primary' : 'text-zinc-500 hover:text-text-light'
                            }`}
                        >
                            {isActive(mePath) && <div className="absolute top-0 h-[2px] w-14 bg-primary shadow-[0_0_8px_rgba(76,255,0,0.8)]" />}
                            <AppIcon
                                name="person"
                                className={`text-[22px] ${!isActive(mePath) && 'transition-transform group-hover:-translate-y-1'}`}
                            />
                            <span className="text-[9px] font-bold tracking-wider uppercase">Me</span>
                        </Link>
                    </div>
                </nav>
            </div>
        </div>
    );
}
