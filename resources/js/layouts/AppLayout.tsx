import { Link, router, usePage } from '@inertiajs/react';
import AppIcon from '../components/AppIcon';

export default function AppLayout({ children }: { children: React.ReactNode }) {
    const { url } = usePage();

    const isActive = (path: string) => url.startsWith(path);
    const handleLibraryClick = (event: React.MouseEvent) => {
        if (! isActive('/library')) {
            return;
        }

        event.preventDefault();
        router.visit('/library', {
            fresh: true,
            preserveState: false,
            preserveScroll: false,
            replace: true,
        });
    };

    return (
        <div className="min-h-screen bg-background-dark font-mono text-text-light antialiased selection:bg-primary selection:text-black">
            <div className="relative mx-auto flex h-full min-h-screen w-full max-w-md flex-col overflow-hidden border-x border-border-dark pb-24">
                {children}

                <nav className="fixed right-0 bottom-0 left-0 z-40 mx-auto max-w-md">
                    <div className="flex h-16 items-center justify-around border-t border-border-dark bg-background-dark/95 px-2 backdrop-blur-sm">
                        <Link
                            href="/home"
                            className={`group flex h-full w-14 flex-col items-center justify-center gap-1 transition-colors ${
                                isActive('/home') ? 'text-primary' : 'text-zinc-500 hover:text-text-light'
                            }`}
                        >
                            {isActive('/home') && <div className="absolute top-0 h-[2px] w-14 bg-primary shadow-[0_0_8px_rgba(76,255,0,0.8)]" />}
                            <AppIcon
                                name="home"
                                className={`text-[22px] ${!isActive('/home') && 'transition-transform group-hover:-translate-y-1'}`}
                            />
                            <span className="text-[9px] font-bold tracking-wider uppercase">Home</span>
                        </Link>

                        <Link
                            href="/search"
                            className={`group flex h-full w-14 flex-col items-center justify-center gap-1 transition-colors ${
                                isActive('/search') ? 'text-primary' : 'text-zinc-500 hover:text-text-light'
                            }`}
                        >
                            {isActive('/search') && <div className="absolute top-0 h-[2px] w-14 bg-primary shadow-[0_0_8px_rgba(76,255,0,0.8)]" />}
                            <AppIcon
                                name="manage_search"
                                className={`text-[22px] ${!isActive('/search') && 'transition-transform group-hover:-translate-y-1'}`}
                            />
                            <span className="text-[9px] font-bold tracking-wider uppercase">Search</span>
                        </Link>

                        <Link
                            href="/library"
                            fresh
                            onClick={handleLibraryClick}
                            className={`group flex h-full w-14 flex-col items-center justify-center gap-1 transition-colors ${
                                isActive('/library') ? 'text-primary' : 'text-zinc-500 hover:text-text-light'
                            }`}
                        >
                            {isActive('/library') && <div className="absolute top-0 h-[2px] w-14 bg-primary shadow-[0_0_8px_rgba(76,255,0,0.8)]" />}
                            <AppIcon
                                name="bookmarks"
                                className={`text-[22px] ${!isActive('/library') && 'transition-transform group-hover:-translate-y-1'}`}
                            />
                            <span className="text-[9px] font-bold tracking-wider uppercase">Lib</span>
                        </Link>

                        <Link
                            href="/me"
                            className={`group flex h-full w-14 flex-col items-center justify-center gap-1 transition-colors ${
                                isActive('/me') ? 'text-primary' : 'text-zinc-500 hover:text-text-light'
                            }`}
                        >
                            {isActive('/me') && <div className="absolute top-0 h-[2px] w-14 bg-primary shadow-[0_0_8px_rgba(76,255,0,0.8)]" />}
                            <AppIcon
                                name="person"
                                className={`text-[22px] ${!isActive('/me') && 'transition-transform group-hover:-translate-y-1'}`}
                            />
                            <span className="text-[9px] font-bold tracking-wider uppercase">Me</span>
                        </Link>
                    </div>
                </nav>
            </div>
        </div>
    );
}
