import { Head } from '@inertiajs/react';
import Header from '../components/Header';
import AppLayout from '../layouts/AppLayout';

export default function Search() {
    return (
        <AppLayout>
            <Head title="Search" />

            <Header className="z-30 gap-4 bg-background-dark">
                <div className="flex items-center justify-between">
                    <h1 className="flex items-center gap-2 text-2xl font-extrabold tracking-tighter text-primary uppercase">
                        <span className="block h-6 w-3 bg-primary"></span>
                        SEARCH
                    </h1>
                    <div className="flex gap-2">
                        <button className="flex size-10 items-center justify-center border border-border-dark bg-surface-dark transition-all hover:border-primary hover:bg-primary hover:text-black active:translate-y-0.5">
                            <span className="material-symbols-outlined text-xl">tune</span>
                        </button>
                    </div>
                </div>
                <div className="group relative w-full">
                    <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <span className="material-symbols-outlined text-zinc-600 transition-colors group-focus-within:text-primary">terminal</span>
                    </div>
                    <input
                        className="block h-12 w-full border border-border-dark bg-surface-dark px-4 pl-10 text-sm font-bold text-text-light uppercase placeholder-zinc-600 transition-colors outline-none focus:border-primary focus:shadow-[4px_4px_0_0_#333]"
                        placeholder="INPUT KEYWORDS..."
                        type="text"
                    />
                    <div className="absolute inset-y-0 right-0 flex items-center pr-2">
                        <button className="flex size-8 items-center justify-center border border-zinc-700 bg-zinc-900 text-primary transition-colors hover:bg-primary hover:text-black">
                            <span className="material-symbols-outlined text-lg">arrow_forward</span>
                        </button>
                    </div>
                </div>
                <div className="no-scrollbar flex gap-2 overflow-x-auto pb-1">
                    {['ALL', 'MANGA', 'MANHWA', 'COMPLETED', 'ONESHOT'].map((filter, index) => (
                        <button
                            key={filter}
                            className={`h-8 shrink-0 border px-4 text-xs font-bold uppercase transition-colors ${
                                index === 0
                                    ? 'border-primary bg-primary text-black shadow-[2px_2px_0_0_rgba(255,255,255,0.2)]'
                                    : 'border-border-dark bg-transparent text-zinc-400 hover:border-primary hover:text-primary'
                            }`}
                        >
                            {filter}
                        </button>
                    ))}
                </div>
            </Header>

            <main className="no-scrollbar flex-1 overflow-y-auto bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImEiIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTTAgNDBoNDBWMEgwdi4yaDQwdjM5LjhIMHoiIGZpbGw9IiMzMzMiIGZpbGwtb3BhY2l0eT0iMC4wNSIvPjwvcGF0dGVybj48L2RlZnM+PHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0idXJsKCNhKSIvPjwvc3ZnPg==')] pb-6">
                <section className="grid grid-cols-2 gap-4 p-4">
                    {/* Result Item 1 */}
                    <div className="group flex cursor-pointer flex-col gap-2">
                        <div className="relative aspect-[2/3] w-full overflow-hidden border border-border-dark bg-surface-dark">
                            <div
                                className="absolute inset-0 bg-cover bg-center grayscale transition-all duration-300 group-hover:grayscale-0"
                                style={{
                                    backgroundImage:
                                        'url("https://lh3.googleusercontent.com/aida-public/AB6AXuBi1DW4Nbcv_qxuuXKEIO9ac0d4P9mkgGooxyAbK2WmcXA5dlFT4O6DKuk7X4eZWgJooiEz1V7S-no82oLLD03C2QoU7FiS189mwZFUeCsxGM2wGpkXQz-XmX1gB2vaP1DPYAOOKxKCR1jLnL7KUMWxbmcFZ-QOK0swXhRMEoZQDJAD7TSO2yQemk1FPx7t6kIzRA6UcvzHuhpBOJbVjpVzz5uimgAmdmxljxi2DjVG1E5hvuSv-Li34jwwksv8N2qS5Pa_jPsJxaI")',
                                }}
                            ></div>
                            <div className="absolute top-2 left-2 border border-black bg-primary px-1.5 py-0.5 text-[10px] font-bold text-black">
                                98% MATCH
                            </div>
                        </div>
                        <div>
                            <h3 className="truncate text-sm font-bold text-text-light uppercase group-hover:text-primary">One Piece</h3>
                            <p className="text-[10px] text-zinc-500 uppercase">Eiichiro Oda</p>
                        </div>
                    </div>

                    {/* Result Item 2 */}
                    <div className="group flex cursor-pointer flex-col gap-2">
                        <div className="relative aspect-[2/3] w-full overflow-hidden border border-border-dark bg-surface-dark">
                            <div
                                className="absolute inset-0 bg-cover bg-center grayscale transition-all duration-300 group-hover:grayscale-0"
                                style={{
                                    backgroundImage:
                                        'url("https://lh3.googleusercontent.com/aida-public/AB6AXuADzbhCv9pSoqAqE8jPB4-RLSGMVyLfLfaTG6vUmTlH_P7mnUfHHlCIcrCz5bk2f6PLiJB5V0YQ-VLeHLz_s5RLYjEGMtOIsknpLtBZid-CAMX8pdu7UkHiqOwyzyRlWtC8f5IOdst2njKsb69UEAIYgUkn2HwmJ5OxcLUxdzj2yOg4ESs5D3D4r02mVxefNEDesTbXwLM1QQt1sLwVp8TI6wPwcmL4mOxWOC_crhVdIzAN-E04W41w2KZ3Nre9bhggk3MQpc8m2Bs")',
                                }}
                            ></div>
                        </div>
                        <div>
                            <h3 className="truncate text-sm font-bold text-text-light uppercase group-hover:text-primary">Chainsaw Man</h3>
                            <p className="text-[10px] text-zinc-500 uppercase">Tatsuki Fujimoto</p>
                        </div>
                    </div>

                    {/* Result Item 3 */}
                    <div className="group flex cursor-pointer flex-col gap-2">
                        <div className="relative aspect-[2/3] w-full overflow-hidden border border-border-dark bg-surface-dark">
                            <div
                                className="absolute inset-0 bg-cover bg-center grayscale transition-all duration-300 group-hover:grayscale-0"
                                style={{
                                    backgroundImage:
                                        'url("https://lh3.googleusercontent.com/aida-public/AB6AXuDQhvePW49Fsq4MlbcW6Mz5u64_QINmF_gAGU6FT9TaPTGz-jQ_gKcCvbOU3F_1zvayWRY9yD2UsW7Q3dXyksVOona4bM5TSedaI-oBpsV-D8xccZcHvQUFZQY3LdEttg1KR0ky-qWj58iiPGbeFuoc7IbL9-WrhtJv7p7lroMAuCJyJvIwFlnYCg8Z4vMe3ifN95RnaDI3fvHA9MpybL0EHTKrjFiRdHevZhW16LthMHQPw_eH-aEEiuXww0OZMs7uU46953sMbHo")',
                                }}
                            ></div>
                        </div>
                        <div>
                            <h3 className="truncate text-sm font-bold text-text-light uppercase group-hover:text-primary">Jujutsu Kaisen</h3>
                            <p className="text-[10px] text-zinc-500 uppercase">Gege Akutami</p>
                        </div>
                    </div>

                    {/* Result Item 4 */}
                    <div className="group flex cursor-pointer flex-col gap-2">
                        <div className="relative aspect-[2/3] w-full overflow-hidden border border-border-dark bg-surface-dark">
                            <div
                                className="absolute inset-0 bg-cover bg-center grayscale transition-all duration-300 group-hover:grayscale-0"
                                style={{
                                    backgroundImage:
                                        'url("https://lh3.googleusercontent.com/aida-public/AB6AXuC-Uy5BxepQ1Nb7ELOVtJalVKm7c4ItnrSySb4-VxM06R7ATsbeNffQMZtLXqxOSG9MJv8tA6R3Rcd7fZ9kQebd0AU6himFsEAQLaOpr-U6r9yzFKUX1j_DP9m6fOZr7QYjBYsaiPhi74I5umkR1bv-DjF69qKN4byG6y6Ze82a_nHUrW9LjF0VapgPBhdg-qKv9Wl6WzQrWsY1ZisB-0lbZAbZ0v3MGZ0mdhQRUwo7DiRyG4vewrPsgqBTBkVH-ljSJP29fEeYTYY")',
                                }}
                            ></div>
                        </div>
                        <div>
                            <h3 className="truncate text-sm font-bold text-text-light uppercase group-hover:text-primary">Blue Lock</h3>
                            <p className="text-[10px] text-zinc-500 uppercase">Muneyuki Kaneshiro</p>
                        </div>
                    </div>
                </section>
            </main>
        </AppLayout>
    );
}
