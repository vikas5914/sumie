import { Head, Link, usePage } from '@inertiajs/react';
import Header from '../components/Header';
import type { SharedData } from '../types';

export default function MangaDetail({ id }: { id: string }) {
    const { auth } = usePage<SharedData>().props;
    const userName = auth.user?.name ?? 'Operator';

    return (
        <div className="min-h-screen bg-background-dark font-mono text-text-light antialiased selection:bg-primary selection:text-black">
            <Head title="One Piece" />
            <div className="relative mx-auto flex h-full min-h-screen w-full max-w-md flex-col border-x border-border-dark bg-background-dark">
                <Header className="z-50 flex-row items-center justify-between backdrop-blur-md">
                    <Link
                        href="/home"
                        className="flex size-10 items-center justify-center border border-border-dark bg-surface-dark transition-colors hover:bg-border-dark hover:text-primary active:translate-y-0.5"
                    >
                        <span className="material-symbols-outlined">arrow_back</span>
                    </Link>
                    <div className="flex flex-col items-center gap-1">
                        <h1 className="text-xs font-bold tracking-[0.2em] text-primary uppercase">Sys.Manga_ID: #{id || '8942'}</h1>
                        <span className="text-[10px] font-bold tracking-[0.2em] text-zinc-500 uppercase">{userName}</span>
                    </div>
                    <button className="flex size-10 items-center justify-center border border-border-dark bg-surface-dark transition-colors hover:bg-border-dark hover:text-primary active:translate-y-0.5">
                        <span className="material-symbols-outlined">share</span>
                    </button>
                </Header>

                <main className="no-scrollbar flex-1 overflow-y-auto pb-10">
                    <section className="border-b border-border-dark p-4">
                        <div className="group relative mb-6 aspect-[3/4] w-full overflow-hidden border border-border-dark bg-surface-dark">
                            <div
                                className="absolute inset-0 bg-cover bg-center bg-no-repeat grayscale-[20%] filter transition-transform duration-500 group-hover:scale-105"
                                style={{
                                    backgroundImage:
                                        'url("https://lh3.googleusercontent.com/aida-public/AB6AXuBi1DW4Nbcv_qxuuXKEIO9ac0d4P9mkgGooxyAbK2WmcXA5dlFT4O6DKuk7X4eZWgJooiEz1V7S-no82oLLD03C2QoU7FiS189mwZFUeCsxGM2wGpkXQz-XmX1gB2vaP1DPYAOOKxKCR1jLnL7KUMWxbmcFZ-QOK0swXhRMEoZQDJAD7TSO2yQemk1FPx7t6kIzRA6UcvzHuhpBOJbVjpVzz5uimgAmdmxljxi2DjVG1E5hvuSv-Li34jwwksv8N2qS5Pa_jPsJxaI")',
                                }}
                            ></div>
                            <div className="absolute top-0 right-0 border-b border-l border-black bg-primary px-3 py-1 text-[10px] font-bold text-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                Status: Ongoing
                            </div>
                            <div className="pointer-events-none absolute inset-0 border-[0.5px] border-white/10"></div>
                            <div className="absolute bottom-0 left-0 h-1/3 w-full bg-gradient-to-t from-background-dark to-transparent"></div>
                        </div>

                        <div className="mb-6 flex flex-col gap-2">
                            <h2 className="text-3xl leading-tight font-bold tracking-tight text-white uppercase">One Piece</h2>
                            <div className="flex items-center gap-2 text-xs font-bold tracking-wider text-zinc-500 uppercase">
                                <span className="text-primary">Eiichiro Oda</span>
                                <span className="text-zinc-700">//</span>
                                <span>Shonen Jump</span>
                            </div>
                        </div>

                        <div className="mb-6 grid grid-cols-3 gap-px border border-border-dark bg-border-dark">
                            <div className="flex flex-col gap-1 bg-background-dark p-3 text-center transition-colors hover:bg-surface-dark">
                                <span className="text-[10px] tracking-widest text-zinc-500 uppercase">Rating</span>
                                <div className="flex items-center justify-center gap-1 font-bold text-primary">
                                    <span className="material-symbols-outlined text-sm">star</span>
                                    <span>4.9</span>
                                </div>
                            </div>
                            <div className="flex flex-col gap-1 bg-background-dark p-3 text-center transition-colors hover:bg-surface-dark">
                                <span className="text-[10px] tracking-widest text-zinc-500 uppercase">Chapters</span>
                                <span className="font-bold text-text-light">1090</span>
                            </div>
                            <div className="flex flex-col gap-1 bg-background-dark p-3 text-center transition-colors hover:bg-surface-dark">
                                <span className="text-[10px] tracking-widest text-zinc-500 uppercase">Views</span>
                                <span className="font-bold text-text-light">2.4M</span>
                            </div>
                        </div>

                        <div className="mb-8 flex gap-3">
                            <button className="flex h-12 flex-1 items-center justify-center gap-2 border border-primary bg-primary text-sm font-bold text-background-dark uppercase transition-colors hover:bg-white active:translate-y-0.5">
                                <span className="material-symbols-outlined">menu_book</span>
                                Read Now
                            </button>
                            <button className="flex size-12 items-center justify-center border border-border-dark bg-surface-dark text-text-light transition-colors hover:border-primary hover:text-primary active:translate-y-0.5">
                                <span className="material-symbols-outlined">bookmark_add</span>
                            </button>
                        </div>

                        <div className="mb-6 space-y-3">
                            <h3 className="border-l-2 border-primary pl-3 text-xs font-bold tracking-widest text-primary uppercase">Synopsis_Log</h3>
                            <p className="ml-[1px] border-l border-border-dark pl-3 text-justify text-sm leading-relaxed text-zinc-400">
                                Gold Roger was known as the "Pirate King", the strongest and most infamous being to have sailed the Grand Line. The
                                capture and execution of Roger by the World Government brought a change to the entire world. His last words before his
                                death revealed the existence of the greatest treasure in the world, One Piece.
                            </p>
                        </div>

                        <div className="flex flex-wrap gap-2">
                            {['Adventure', 'Fantasy', 'Shonen'].map((tag) => (
                                <span
                                    key={tag}
                                    className="cursor-pointer border border-border-dark bg-surface-dark px-3 py-1.5 text-[10px] tracking-wider text-zinc-300 uppercase transition-colors hover:border-primary hover:bg-background-dark hover:text-primary"
                                >
                                    {tag}
                                </span>
                            ))}
                        </div>
                    </section>

                    <section>
                        <div className="sticky top-0 z-10 flex items-center justify-between border-b border-border-dark bg-background-dark px-4 py-3">
                            <h3 className="flex items-center gap-2 text-sm font-bold tracking-wider uppercase">
                                <span className="inline-block size-2 bg-primary"></span>
                                Chapter Index
                            </h3>
                            <div className="flex gap-2">
                                <button className="text-zinc-500 transition-colors hover:text-primary">
                                    <span className="material-symbols-outlined text-lg">sort</span>
                                </button>
                                <button className="text-zinc-500 transition-colors hover:text-primary">
                                    <span className="material-symbols-outlined text-lg">filter_list</span>
                                </button>
                            </div>
                        </div>

                        <div className="divide-y divide-border-dark border-b border-border-dark">
                            {[
                                { num: '1090', title: 'New Admiral Kizaru', time: '2h ago', new: true },
                                { num: '1089', title: 'Siege Situation', time: '1d ago', new: false },
                                { num: '1088', title: 'Final Lesson', time: '7d ago', new: false },
                                { num: '1087', title: 'Battleship Bags', time: '14d ago', new: false },
                                { num: '1086', title: 'Five Elders', time: '21d ago', new: false },
                            ].map((chapter) => (
                                <div
                                    key={chapter.num}
                                    className="group flex cursor-pointer items-center justify-between border-l-2 border-transparent p-4 transition-colors hover:border-primary hover:bg-surface-dark"
                                >
                                    <div className="flex flex-col gap-1">
                                        <span className="text-sm font-bold text-text-light transition-colors group-hover:text-primary">
                                            CHP. {chapter.num}
                                        </span>
                                        <span className="max-w-[200px] truncate text-xs text-zinc-500 uppercase">{chapter.title}</span>
                                    </div>
                                    <div className="flex flex-col items-end gap-1">
                                        {chapter.new ? (
                                            <span className="border border-primary/30 bg-primary/10 px-1 py-0.5 text-[10px] text-primary">NEW</span>
                                        ) : (
                                            <span className="text-[10px] text-zinc-600">{chapter.time}</span>
                                        )}
                                        {chapter.new && <span className="text-[10px] font-bold text-zinc-600">{chapter.time}</span>}
                                    </div>
                                </div>
                            ))}
                        </div>
                    </section>
                </main>
            </div>
        </div>
    );
}
