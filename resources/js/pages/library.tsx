import { Head } from '@inertiajs/react';
import Header from '../components/Header';
import AppLayout from '../layouts/AppLayout';

export default function Library() {
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
                        <button className="flex size-10 items-center justify-center border border-border-dark bg-surface-dark transition-all hover:bg-primary hover:text-black">
                            <span className="material-symbols-outlined text-[20px]">search</span>
                        </button>
                        <button className="flex size-10 items-center justify-center border border-border-dark bg-surface-dark transition-all hover:bg-primary hover:text-black">
                            <span className="material-symbols-outlined text-[20px]">tune</span>
                        </button>
                    </div>
                </div>
                <div className="no-scrollbar flex overflow-x-auto border-t border-border-dark bg-surface-dark">
                    <button className="border-r border-border-dark bg-primary px-6 py-3 text-xs font-bold text-black uppercase">Reading</button>
                    <button className="border-r border-border-dark px-6 py-3 text-xs font-bold text-zinc-500 uppercase transition-colors hover:bg-border-dark hover:text-white">
                        Completed
                    </button>
                    <button className="border-r border-border-dark px-6 py-3 text-xs font-bold text-zinc-500 uppercase transition-colors hover:bg-border-dark hover:text-white">
                        Downloaded
                    </button>
                    <button className="px-6 py-3 text-xs font-bold text-zinc-500 uppercase transition-colors hover:bg-border-dark hover:text-white">
                        Dropped
                    </button>
                </div>
            </Header>

            <main className="no-scrollbar flex flex-1 flex-col gap-0 overflow-y-auto pb-6">
                <div className="flex items-center justify-between border-b border-border-dark bg-background-dark px-4 py-3 text-xs text-zinc-500 uppercase">
                    <span>24 ITEMS</span>
                    <div className="flex items-center gap-4">
                        <button className="flex items-center gap-1 hover:text-primary">
                            LAST READ <span className="material-symbols-outlined text-[14px]">arrow_drop_down</span>
                        </button>
                        <button className="hover:text-primary">
                            <span className="material-symbols-outlined text-[18px]">grid_view</span>
                        </button>
                    </div>
                </div>

                <div className="grid grid-cols-1 gap-0">
                    {/* Library Item 1 */}
                    <div className="group relative flex gap-4 border-b border-border-dark p-4 transition-colors hover:bg-surface-dark">
                        <div className="relative aspect-[2/3] w-20 shrink-0 border border-border-dark bg-surface-dark">
                            <div
                                className="absolute inset-0 bg-cover bg-center grayscale transition-all duration-300 group-hover:grayscale-0"
                                style={{
                                    backgroundImage:
                                        'url("https://lh3.googleusercontent.com/aida-public/AB6AXuADzbhCv9pSoqAqE8jPB4-RLSGMVyLfLfaTG6vUmTlH_P7mnUfHHlCIcrCz5bk2f6PLiJB5V0YQ-VLeHLz_s5RLYjEGMtOIsknpLtBZid-CAMX8pdu7UkHiqOwyzyRlWtC8f5IOdst2njKsb69UEAIYgUkn2HwmJ5OxcLUxdzj2yOg4ESs5D3D4r02mVxefNEDesTbXwLM1QQt1sLwVp8TI6wPwcmL4mOxWOC_crhVdIzAN-E04W41w2KZ3Nre9bhggk3MQpc8m2Bs")',
                                }}
                            ></div>
                            <div className="absolute -top-1 -left-1 z-10 h-2 w-2 bg-primary"></div>
                        </div>
                        <div className="flex flex-1 flex-col justify-between py-1">
                            <div>
                                <div className="mb-1 flex items-start justify-between">
                                    <h3 className="text-lg leading-tight font-bold text-text-light uppercase transition-colors group-hover:text-primary">
                                        Chainsaw Man
                                    </h3>
                                    <button className="text-zinc-500 hover:text-primary">
                                        <span className="material-symbols-outlined text-[20px]">more_vert</span>
                                    </button>
                                </div>
                                <p className="text-xs text-zinc-500 uppercase">Chap 143 • Unread: 2</p>
                            </div>
                            <div className="mt-2 flex items-end justify-between">
                                <div className="mr-4 flex w-full flex-col gap-1">
                                    <div className="flex justify-between text-[10px] font-bold text-primary uppercase">
                                        <span>Progress</span>
                                        <span>88%</span>
                                    </div>
                                    <div className="h-2 w-full border border-border-dark bg-border-dark p-[1px]">
                                        <div className="h-full w-[88%] bg-primary"></div>
                                    </div>
                                </div>
                                <button className="border border-border-dark bg-border-dark p-2 text-text-light transition-all hover:bg-primary hover:text-black">
                                    <span className="material-symbols-outlined block text-[18px]">play_arrow</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {/* Library Item 2 */}
                    <div className="group relative flex gap-4 border-b border-border-dark p-4 transition-colors hover:bg-surface-dark">
                        <div className="relative aspect-[2/3] w-20 shrink-0 border border-border-dark bg-surface-dark">
                            <div
                                className="absolute inset-0 bg-cover bg-center grayscale transition-all duration-300 group-hover:grayscale-0"
                                style={{
                                    backgroundImage:
                                        'url("https://lh3.googleusercontent.com/aida-public/AB6AXuDQhvePW49Fsq4MlbcW6Mz5u64_QINmF_gAGU6FT9TaPTGz-jQ_gKcCvbOU3F_1zvayWRY9yD2UsW7Q3dXyksVOona4bM5TSedaI-oBpsV-D8xccZcHvQUFZQY3LdEttg1KR0ky-qWj58iiPGbeFuoc7IbL9-WrhtJv7p7lroMAuCJyJvIwFlnYCg8Z4vMe3ifN95RnaDI3fvHA9MpybL0EHTKrjFiRdHevZhW16LthMHQPw_eH-aEEiuXww0OZMs7uU46953sMbHo")',
                                }}
                            ></div>
                        </div>
                        <div className="flex flex-1 flex-col justify-between py-1">
                            <div>
                                <div className="mb-1 flex items-start justify-between">
                                    <h3 className="text-lg leading-tight font-bold text-text-light uppercase transition-colors group-hover:text-primary">
                                        Jujutsu Kaisen
                                    </h3>
                                    <button className="text-zinc-500 hover:text-primary">
                                        <span className="material-symbols-outlined text-[20px]">more_vert</span>
                                    </button>
                                </div>
                                <p className="text-xs text-zinc-500 uppercase">Chap 236 • Unread: 0</p>
                            </div>
                            <div className="mt-2 flex items-end justify-between">
                                <div className="mr-4 flex w-full flex-col gap-1">
                                    <div className="flex justify-between text-[10px] font-bold text-zinc-500 uppercase">
                                        <span>Up to Date</span>
                                        <span>100%</span>
                                    </div>
                                    <div className="h-2 w-full border border-border-dark bg-border-dark p-[1px]">
                                        <div className="h-full w-full bg-zinc-500"></div>
                                    </div>
                                </div>
                                <button className="border border-border-dark bg-border-dark p-2 text-text-light transition-all hover:bg-primary hover:text-black">
                                    <span className="material-symbols-outlined block text-[18px]">check</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {/* Library Item 3 */}
                    <div className="group relative flex gap-4 border-b border-border-dark p-4 transition-colors hover:bg-surface-dark">
                        <div className="relative aspect-[2/3] w-20 shrink-0 border border-border-dark bg-surface-dark">
                            <div
                                className="absolute inset-0 bg-cover bg-center grayscale transition-all duration-300 group-hover:grayscale-0"
                                style={{
                                    backgroundImage:
                                        'url("https://lh3.googleusercontent.com/aida-public/AB6AXuC-Uy5BxepQ1Nb7ELOVtJalVKm7c4ItnrSySb4-VxM06R7ATsbeNffQMZtLXqxOSG9MJv8tA6R3Rcd7fZ9kQebd0AU6himFsEAQLaOpr-U6r9yzFKUX1j_DP9m6fOZr7QYjBYsaiPhi74I5umkR1bv-DjF69qKN4byG6y6Ze82a_nHUrW9LjF0VapgPBhdg-qKv9Wl6WzQrWsY1ZisB-0lbZAbZ0v3MGZ0mdhQRUwo7DiRyG4vewrPsgqBTBkVH-ljSJP29fEeYTYY")',
                                }}
                            ></div>
                            <div className="absolute right-0 bottom-0 border-t border-l border-background-dark bg-primary px-1 py-0.5 text-[10px] font-bold text-black">
                                DL
                            </div>
                        </div>
                        <div className="flex flex-1 flex-col justify-between py-1">
                            <div>
                                <div className="mb-1 flex items-start justify-between">
                                    <h3 className="text-lg leading-tight font-bold text-text-light uppercase transition-colors group-hover:text-primary">
                                        Blue Lock
                                    </h3>
                                    <button className="text-zinc-500 hover:text-primary">
                                        <span className="material-symbols-outlined text-[20px]">more_vert</span>
                                    </button>
                                </div>
                                <p className="text-xs text-zinc-500 uppercase">Chap 245 • Unread: 5</p>
                            </div>
                            <div className="mt-2 flex items-end justify-between">
                                <div className="mr-4 flex w-full flex-col gap-1">
                                    <div className="flex justify-between text-[10px] font-bold text-primary uppercase">
                                        <span>Progress</span>
                                        <span>45%</span>
                                    </div>
                                    <div className="h-2 w-full border border-border-dark bg-border-dark p-[1px]">
                                        <div className="h-full w-[45%] bg-primary"></div>
                                    </div>
                                </div>
                                <button className="border border-border-dark bg-border-dark p-2 text-text-light transition-all hover:bg-primary hover:text-black">
                                    <span className="material-symbols-outlined block text-[18px]">play_arrow</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="p-4">
                    <button className="w-full border border-border-dark bg-background-dark py-4 text-sm font-bold text-zinc-500 uppercase transition-all hover:border-primary hover:bg-surface-dark hover:text-primary">
                        // Load More Logs
                    </button>
                </div>
            </main>
        </AppLayout>
    );
}
