import { Head, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import AppIcon from '../components/AppIcon';
import Header from '../components/Header';
import UserAvatar from '../components/UserAvatar';
import AppLayout from '../layouts/AppLayout';

type ReadingMode = 'vertical_scroll' | 'page_by_page';

interface MeProps {
    auth: {
        user: {
            name: string;
        } | null;
    };
    profile: {
        level: number;
        member_id: string;
        status: string;
        joined_at: string | null;
        environment: string;
        stats: {
            chapters_read: number;
            reading_hours: number;
            library_items: number;
        };
    };
    [key: string]: unknown;
}

const READING_MODE_KEY = 'sumie:reading-mode';

export default function Me() {
    const { auth, profile } = usePage<MeProps>().props;
    const userName = auth.user?.name ?? 'Operator';
    const [readingMode, setReadingMode] = useState<ReadingMode>('vertical_scroll');
    const [isSaved, setIsSaved] = useState(false);

    useEffect(() => {
        if (typeof window === 'undefined') {
            return;
        }

        const storedMode = window.localStorage.getItem(READING_MODE_KEY);

        if (storedMode === 'vertical_scroll' || storedMode === 'page_by_page') {
            setReadingMode(storedMode);
        }
    }, []);

    const updateReadingMode = (mode: ReadingMode) => {
        setReadingMode(mode);

        if (typeof window !== 'undefined') {
            window.localStorage.setItem(READING_MODE_KEY, mode);
        }

        setIsSaved(true);
        window.setTimeout(() => setIsSaved(false), 1200);
    };

    return (
        <AppLayout>
            <Head title="Profile" />

            <Header className="z-30 gap-4 bg-background-dark">
                <div className="flex items-center justify-between">
                    <h1 className="flex items-center gap-2 text-2xl font-extrabold tracking-tighter text-primary uppercase">
                        <span className="block h-6 w-3 bg-primary"></span>
                        PROFILE
                    </h1>
                    <div aria-hidden="true" className="h-10 w-[88px]" />
                </div>
                <div className="flex flex-col gap-6 px-5 pb-5">
                    <div className="flex items-start gap-4">
                        <div className="group relative cursor-pointer">
                            <UserAvatar name={userName} size={76} className="p-0 transition-all duration-300" />
                            <div className="absolute -right-2 -bottom-2 border border-black bg-primary px-1.5 py-0.5 text-[10px] font-bold text-black shadow-[2px_2px_0px_0px_rgba(255,255,255,0.2)]">
                                LVL.{profile.level}
                            </div>
                        </div>
                        <div className="flex-1 pt-1">
                            <h1 className="mb-1 text-2xl leading-none font-bold tracking-tighter text-white uppercase">{userName}</h1>
                            <p className="mb-3 text-xs font-bold tracking-wide text-zinc-500 uppercase">MEMBER_ID: {profile.member_id}</p>
                            <div className="flex gap-2">
                                <span className="cursor-default border border-primary px-2 py-0.5 text-[10px] font-bold text-primary uppercase transition-colors hover:bg-primary hover:text-black">
                                    PREMIUM
                                </span>
                                <span className="border border-zinc-700 px-2 py-0.5 text-[10px] font-bold text-zinc-400 uppercase">
                                    {profile.status}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="grid grid-cols-3 divide-x divide-border-dark border-t border-border-dark">
                    <div className="group cursor-pointer p-3 text-center transition-colors hover:bg-surface-dark">
                        <p className="mb-1 text-[10px] text-zinc-500 uppercase group-hover:text-primary">READ</p>
                        <p className="text-lg font-bold text-white">{profile.stats.chapters_read}</p>
                    </div>
                    <div className="group cursor-pointer p-3 text-center transition-colors hover:bg-surface-dark">
                        <p className="mb-1 text-[10px] text-zinc-500 uppercase group-hover:text-primary">HOURS</p>
                        <p className="text-lg font-bold text-white">{profile.stats.reading_hours.toFixed(1)}</p>
                    </div>
                    <div className="group cursor-pointer p-3 text-center transition-colors hover:bg-surface-dark">
                        <p className="mb-1 text-[10px] text-zinc-500 uppercase group-hover:text-primary">LISTS</p>
                        <p className="text-lg font-bold text-white">{profile.stats.library_items}</p>
                    </div>
                </div>
            </Header>

            <main className="no-scrollbar flex flex-1 flex-col gap-8 overflow-y-auto px-4 pt-4 pb-8">
                <section>
                    <div className="mb-3 flex items-center gap-2">
                        <AppIcon name="settings_suggest" className="text-sm text-primary" />
                        <h2 className="text-xs font-bold tracking-widest text-zinc-500 uppercase">APP_PREFERENCES</h2>
                    </div>
                    <div className="flex flex-col border border-border-dark bg-surface-dark">
                        <div className="flex w-full items-center justify-between p-4 text-left">
                            <div className="flex flex-col">
                                <span className="text-sm font-bold text-white uppercase">READING_MODE</span>
                                <span className="mt-1 text-[10px] tracking-wide text-zinc-500 uppercase">Applied in manga reader</span>
                            </div>
                            <div className="grid grid-cols-2 gap-1 border border-border-dark bg-background-dark p-1">
                                <button
                                    type="button"
                                    onClick={() => updateReadingMode('vertical_scroll')}
                                    className={`px-2 py-1 text-[10px] font-bold uppercase transition-colors ${
                                        readingMode === 'vertical_scroll'
                                            ? 'bg-primary text-black'
                                            : 'bg-background-dark text-zinc-400 hover:text-primary'
                                    }`}
                                >
                                    Vertical
                                </button>
                                <button
                                    type="button"
                                    onClick={() => updateReadingMode('page_by_page')}
                                    className={`px-2 py-1 text-[10px] font-bold uppercase transition-colors ${
                                        readingMode === 'page_by_page'
                                            ? 'bg-primary text-black'
                                            : 'bg-background-dark text-zinc-400 hover:text-primary'
                                    }`}
                                >
                                    Paged
                                </button>
                            </div>
                        </div>
                        <div className="border-t border-border-dark px-4 py-2 text-[10px] tracking-wide text-zinc-500 uppercase">
                            {isSaved ? 'Preference saved' : `Current: ${readingMode.replace('_', ' ')}`}
                        </div>
                    </div>
                </section>

                <section className="mb-6">
                    <div className="mt-4 text-center">
                        <p className="text-[10px] font-bold text-zinc-700 uppercase">
                            ENV: {profile.environment} // MEMBER_SINCE: {profile.joined_at ?? 'UNKNOWN'}
                        </p>
                    </div>
                </section>
            </main>
        </AppLayout>
    );
}
