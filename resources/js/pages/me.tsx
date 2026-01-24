import { Head } from '@inertiajs/react';
import Header from '../components/Header';
import AppLayout from '../layouts/AppLayout';

export default function Me() {
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
                            <div
                                className="size-20 border border-zinc-600 bg-cover bg-center bg-no-repeat grayscale transition-all duration-300 hover:grayscale-0"
                                style={{
                                    backgroundImage:
                                        'url("https://lh3.googleusercontent.com/aida-public/AB6AXuDKIWh7R3m6byrcvwlY6mcmU9zKbBG_bIBWoqY5LZwrifsD28-9GNT7YWCG2diwAL5ry1w_JbimssL6xLUlBZU60hcAx2tRM6uqneOa5QEi8vLvPIPPlvjaR2wA9zgEHBJcDqThorjKEXzQ7hbGZHGsXbTZVa-ucRmJKBtqvuO_sTJQ6eYU0XJ0sNhKvmYbK_EqDRVex7fKwGHd3QxC3QZbMJoFsAn_3UKbLciTny1zHcVqKR_XDLXuOO9PcyJk1JEuOEUcJUuJcb8")',
                                }}
                            ></div>
                            <div className="absolute -right-2 -bottom-2 border border-black bg-primary px-1.5 py-0.5 text-[10px] font-bold text-black shadow-[2px_2px_0px_0px_rgba(255,255,255,0.2)]">
                                LVL.42
                            </div>
                        </div>
                        <div className="flex-1 pt-1">
                            <h1 className="mb-1 text-2xl leading-none font-bold tracking-tighter text-white uppercase">ALEX_READER</h1>
                            <p className="mb-3 text-xs font-bold tracking-wide text-zinc-500 uppercase">MEMBER_ID: #8842-XJ</p>
                            <div className="flex gap-2">
                                <span className="cursor-default border border-primary px-2 py-0.5 text-[10px] font-bold text-primary uppercase transition-colors hover:bg-primary hover:text-black">
                                    PREMIUM
                                </span>
                                <span className="border border-zinc-700 px-2 py-0.5 text-[10px] font-bold text-zinc-400 uppercase">ONLINE</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="grid grid-cols-3 divide-x divide-border-dark border-t border-border-dark">
                    <div className="group cursor-pointer p-3 text-center transition-colors hover:bg-surface-dark">
                        <p className="mb-1 text-[10px] text-zinc-500 uppercase group-hover:text-primary">READ</p>
                        <p className="text-lg font-bold text-white">842</p>
                    </div>
                    <div className="group cursor-pointer p-3 text-center transition-colors hover:bg-surface-dark">
                        <p className="mb-1 text-[10px] text-zinc-500 uppercase group-hover:text-primary">HOURS</p>
                        <p className="text-lg font-bold text-white">128.5</p>
                    </div>
                    <div className="group cursor-pointer p-3 text-center transition-colors hover:bg-surface-dark">
                        <p className="mb-1 text-[10px] text-zinc-500 uppercase group-hover:text-primary">LISTS</p>
                        <p className="text-lg font-bold text-white">14</p>
                    </div>
                </div>
            </Header>

            <main className="no-scrollbar flex flex-1 flex-col gap-8 overflow-y-auto px-4 pt-4 pb-8">
                <section>
                    <div className="mb-3 flex items-center gap-2">
                        <span className="material-symbols-outlined text-sm text-primary">terminal</span>
                        <h2 className="text-xs font-bold tracking-widest text-zinc-500 uppercase">ACCOUNT_CONFIGURATION</h2>
                    </div>
                    <div className="border border-border-dark bg-surface-dark">
                        <button className="group flex w-full items-center justify-between border-b border-border-dark p-4 text-left transition-colors hover:bg-zinc-800/50">
                            <div className="flex flex-col">
                                <span className="text-sm font-bold text-white uppercase transition-colors group-hover:text-primary">
                                    EDIT_PROFILE
                                </span>
                                <span className="text-[10px] text-zinc-500 uppercase">AVATAR, BIO, USERNAME</span>
                            </div>
                            <span className="material-symbols-outlined text-zinc-600 transition-colors group-hover:text-primary">chevron_right</span>
                        </button>
                        <button className="group flex w-full items-center justify-between border-b border-border-dark p-4 text-left transition-colors hover:bg-zinc-800/50">
                            <div className="flex flex-col">
                                <span className="text-sm font-bold text-white uppercase transition-colors group-hover:text-primary">
                                    SUBSCRIPTION
                                </span>
                                <span className="text-[10px] text-primary uppercase">PLAN: ANIME_GOD_TIER</span>
                            </div>
                            <span className="material-symbols-outlined text-zinc-600 transition-colors group-hover:text-primary">chevron_right</span>
                        </button>
                        <button className="group flex w-full items-center justify-between p-4 text-left transition-colors hover:bg-zinc-800/50">
                            <div className="flex flex-col">
                                <span className="text-sm font-bold text-white uppercase transition-colors group-hover:text-primary">SECURITY</span>
                                <span className="text-[10px] text-zinc-500 uppercase">PASSWORD, 2FA</span>
                            </div>
                            <span className="material-symbols-outlined text-zinc-600 transition-colors group-hover:text-primary">chevron_right</span>
                        </button>
                    </div>
                </section>

                <section>
                    <div className="mb-3 flex items-center gap-2">
                        <span className="material-symbols-outlined text-sm text-primary">settings_suggest</span>
                        <h2 className="text-xs font-bold tracking-widest text-zinc-500 uppercase">APP_PREFERENCES</h2>
                    </div>
                    <div className="flex flex-col border border-border-dark bg-surface-dark">
                        <div className="flex items-center justify-between border-b border-border-dark p-4">
                            <div className="flex flex-col gap-1">
                                <span className="text-sm font-bold text-white uppercase">DOWNLOADS_WIFI_ONLY</span>
                            </div>
                            <div className="group relative inline-flex cursor-pointer items-center">
                                <input defaultChecked className="peer sr-only" type="checkbox" />
                                <div className="relative h-5 w-10 border border-zinc-600 bg-zinc-900 transition-colors peer-checked:border-primary peer-checked:bg-zinc-900 peer-focus:ring-0">
                                    <div className="absolute top-0.5 left-0.5 h-3.5 w-3.5 bg-zinc-500 transition-all duration-200 peer-checked:translate-x-5 peer-checked:bg-primary"></div>
                                </div>
                            </div>
                        </div>
                        <div className="flex items-center justify-between border-b border-border-dark p-4">
                            <div className="flex flex-col gap-1">
                                <span className="text-sm font-bold text-white uppercase">PUSH_NOTIFICATIONS</span>
                            </div>
                            <div className="group relative inline-flex cursor-pointer items-center">
                                <input className="peer sr-only" type="checkbox" />
                                <div className="relative h-5 w-10 border border-zinc-600 bg-zinc-900 transition-colors peer-checked:border-primary peer-checked:bg-zinc-900 peer-focus:ring-0">
                                    <div className="absolute top-0.5 left-0.5 h-3.5 w-3.5 bg-zinc-500 transition-all duration-200 peer-checked:translate-x-5 peer-checked:bg-primary"></div>
                                </div>
                            </div>
                        </div>
                        <button className="group flex w-full items-center justify-between p-4 text-left transition-colors hover:bg-zinc-800/50">
                            <div className="flex flex-col">
                                <span className="text-sm font-bold text-white uppercase transition-colors group-hover:text-primary">
                                    READING_MODE
                                </span>
                            </div>
                            <div className="flex items-center gap-2">
                                <span className="border border-zinc-600 px-2 py-1 text-xs font-bold text-zinc-400 uppercase">VERTICAL_SCROLL</span>
                                <span className="material-symbols-outlined text-[16px] text-zinc-600 transition-colors group-hover:text-primary">
                                    edit
                                </span>
                            </div>
                        </button>
                    </div>
                </section>

                <section className="mb-6">
                    <button className="group relative flex w-full items-center justify-center gap-3 overflow-hidden border border-red-900/50 bg-transparent p-4 transition-all hover:border-red-500 hover:bg-red-900/10">
                        <span className="material-symbols-outlined text-red-700 transition-colors group-hover:text-red-500">power_settings_new</span>
                        <span className="text-sm font-bold tracking-widest text-red-700 uppercase transition-colors group-hover:text-red-500">
                            TERMINATE_SESSION
                        </span>
                        <div className="absolute top-0 left-0 h-full w-1 bg-red-500 opacity-0 transition-opacity group-hover:opacity-100"></div>
                    </button>
                    <div className="mt-4 text-center">
                        <p className="text-[10px] font-bold text-zinc-700 uppercase">BUILD_ID: 8993.221-ALPHA // SERVER: TOKYO_03</p>
                    </div>
                </section>
            </main>
        </AppLayout>
    );
}
