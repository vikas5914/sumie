import { Head, Link } from '@inertiajs/react';
import Header from '../components/Header';
import SearchInput from '../components/SearchInput';
import AppLayout from '../layouts/AppLayout';

export default function Home() {
    return (
        <AppLayout>
            <Head title="Home" />

            {/* Header */}
            <Header className="">
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <div
                            className="size-10 bg-cover bg-center bg-no-repeat ring-1 ring-primary"
                            style={{
                                backgroundImage:
                                    'url("https://lh3.googleusercontent.com/aida-public/AB6AXuDKIWh7R3m6byrcvwlY6mcmU9zKbBG_bIBWoqY5LZwrifsD28-9GNT7YWCG2diwAL5ry1w_JbimssL6xLUlBZU60hcAx2tRM6uqneOa5QEi8vLvPIPPlvjaR2wA9zgEHBJcDqThorjKEXzQ7hbGZHGsXbTZVa-ucRmJKBtqvuO_sTJQ6eYU0XJ0sNhKvmYbK_EqDRVex7fKwGHd3QxC3QZbMJoFsAn_3UKbLciTny1zHcVqKR_XDLXuOO9PcyJk1JEuOEUcJUuJcb8")',
                            }}
                        ></div>
                        <div>
                            <p className="text-xs font-bold tracking-widest text-zinc-400 uppercase">WELCOME BACK</p>
                            <p className="text-xl leading-tight font-bold text-primary">ALEX READER</p>
                        </div>
                    </div>
                    <button className="relative flex size-10 items-center justify-center border border-border-dark bg-surface-dark transition-colors hover:bg-zinc-800">
                        <span className="material-symbols-outlined text-2xl text-text-light">notifications</span>
                        <span className="absolute top-2 right-2 size-2.5 border-2 border-surface-dark bg-primary"></span>
                    </button>
                </div>
                <SearchInput className="mt-4" />
            </Header>

            <main className="no-scrollbar flex flex-1 flex-col gap-6 overflow-y-auto pt-4 pb-6">
                {/* Hero Section */}
                <section className="px-4">
                    <div className="group relative aspect-[4/3] w-full cursor-pointer overflow-hidden border border-border-dark shadow-lg">
                        <div
                            className="absolute inset-0 bg-cover bg-center bg-no-repeat transition-transform duration-700 group-hover:scale-105"
                            style={{
                                backgroundImage:
                                    'url("https://lh3.googleusercontent.com/aida-public/AB6AXuBi1DW4Nbcv_qxuuXKEIO9ac0d4P9mkgGooxyAbK2WmcXA5dlFT4O6DKuk7X4eZWgJooiEz1V7S-no82oLLD03C2QoU7FiS189mwZFUeCsxGM2wGpkXQz-XmX1gB2vaP1DPYAOOKxKCR1jLnL7KUMWxbmcFZ-QOK0swXhRMEoZQDJAD7TSO2yQemk1FPx7t6kIzRA6UcvzHuhpBOJbVjpVzz5uimgAmdmxljxi2DjVG1E5hvuSv-Li34jwwksv8N2qS5Pa_jPsJxaI")',
                            }}
                        ></div>
                        <div className="absolute inset-0 bg-gradient-to-t from-background-dark via-background-dark/60 to-transparent"></div>
                        <div className="absolute right-0 bottom-0 left-0 flex flex-col items-start gap-3 p-5">
                            <span className="bg-primary px-3 py-1 text-xs font-bold tracking-widest text-background-dark uppercase shadow-lg shadow-primary/40">
                                #1 TRENDING
                            </span>
                            <div>
                                <h2 className="mb-1 text-3xl font-bold text-text-light uppercase">ONE PIECE</h2>
                                <p className="line-clamp-2 text-sm text-zinc-400">
                                    THE STRAW HAT PIRATES CONTINUE THEIR ADVENTURE IN THE EGGHEAD ARC WITH SHOCKING REVELATIONS.
                                </p>
                            </div>
                            <div className="mt-1 flex w-full items-center gap-3">
                                <button className="flex h-10 flex-1 items-center justify-center gap-2 border border-text-light bg-text-light text-sm font-bold text-background-dark transition-colors hover:bg-zinc-300">
                                    <span className="material-symbols-outlined text-xl">menu_book</span>
                                    READ CHAPTER 1090
                                </button>
                                <button className="flex size-10 items-center justify-center border border-border-dark bg-surface-dark text-text-light transition-colors hover:bg-zinc-800">
                                    <span className="material-symbols-outlined text-xl">bookmark_add</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Continue Reading */}
                <section className="flex flex-col gap-3">
                    <div className="flex items-center justify-between px-4">
                        <h3 className="text-lg font-bold text-text-light uppercase">CONTINUE READING</h3>
                        <Link className="text-sm font-bold text-primary uppercase hover:text-primary/80" href="#">
                            SEE ALL
                        </Link>
                    </div>
                    <div className="no-scrollbar flex snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-2">
                        <div className="flex w-[280px] flex-none snap-center items-center gap-3 border border-border-dark bg-surface-dark p-3 shadow-sm">
                            <div
                                className="relative h-20 w-16 shrink-0 overflow-hidden border border-zinc-600 bg-cover bg-center"
                                style={{
                                    backgroundImage:
                                        'url("https://lh3.googleusercontent.com/aida-public/AB6AXuDQhvePW49Fsq4MlbcW6Mz5u64_QINmF_gAGU6FT9TaPTGz-jQ_gKcCvbOU3F_1zvayWRY9yD2UsW7Q3dXyksVOona4bM5TSedaI-oBpsV-D8xccZcHvQUFZQY3LdEttg1KR0ky-qWj58iiPGbeFuoc7IbL9-WrhtJv7p7lroMAuCJyJvIwFlnYCg8Z4vMe3ifN95RnaDI3fvHA9MpybL0EHTKrjFiRdHevZhW16LthMHQPw_eH-aEEiuXww0OZMs7uU46953sMbHo")',
                                }}
                            ></div>
                            <div className="flex min-w-0 flex-1 flex-col justify-center">
                                <h4 className="truncate font-bold text-text-light uppercase">JUJUTSU KAISEN</h4>
                                <p className="mb-2 text-xs text-zinc-500 uppercase">CHAPTER 45 • 2H AGO</p>
                                <div className="h-1.5 w-full overflow-hidden border border-zinc-600 bg-zinc-700">
                                    <div className="h-full w-[60%] bg-primary"></div>
                                </div>
                            </div>
                            <button className="flex size-8 shrink-0 items-center justify-center border border-primary bg-primary text-background-dark">
                                <span className="material-symbols-outlined text-xl">play_arrow</span>
                            </button>
                        </div>
                        <div className="flex w-[280px] flex-none snap-center items-center gap-3 border border-border-dark bg-surface-dark p-3 shadow-sm">
                            <div
                                className="relative h-20 w-16 shrink-0 overflow-hidden border border-zinc-600 bg-cover bg-center"
                                style={{
                                    backgroundImage:
                                        'url("https://lh3.googleusercontent.com/aida-public/AB6AXuADeZRkMbfpPnZ6vXuOF6paOuRha3isQlaxakDW_fEWALkHAJY3IUQt1pJ2RW8a4LlPlnnSbwCkY0sC9IFXLvOi04s-46oqGmtWUcSJYjO3EXpmRv9A3AVrFYTzpMHHuj52YFZVAABP1s4KlSIjJ9lVflj0YhfB3oPOyVJuaU0h6RB8r8vNcpNnQ6QnL02BjxO8a3nylwnVGn2py7w3IlAd1gfabCllAQov4pKDTHatb3JpOQGzuiyB5K1uZ1hjm6BhwWYFpmRaEGg")',
                                }}
                            ></div>
                            <div className="flex min-w-0 flex-1 flex-col justify-center">
                                <h4 className="truncate font-bold text-text-light uppercase">SPY X FAMILY</h4>
                                <p className="mb-2 text-xs text-zinc-500 uppercase">CHAPTER 12 • 1D AGO</p>
                                <div className="h-1.5 w-full overflow-hidden border border-zinc-600 bg-zinc-700">
                                    <div className="h-full w-[25%] bg-primary"></div>
                                </div>
                            </div>
                            <button className="flex size-8 shrink-0 items-center justify-center border border-primary bg-primary text-background-dark">
                                <span className="material-symbols-outlined text-xl">play_arrow</span>
                            </button>
                        </div>
                    </div>
                </section>

                {/* Trending Now */}
                <section className="flex flex-col gap-3">
                    <div className="flex items-center justify-between px-4">
                        <h3 className="text-lg font-bold text-text-light uppercase">TRENDING NOW</h3>
                    </div>
                    <div className="grid grid-cols-2 gap-4 px-4 sm:grid-cols-3">
                        <div className="group flex cursor-pointer flex-col gap-2">
                            <div className="relative aspect-[2/3] w-full overflow-hidden border border-border-dark bg-zinc-800">
                                <div
                                    className="absolute inset-0 bg-cover bg-center bg-no-repeat transition-transform duration-500 group-hover:scale-105"
                                    style={{
                                        backgroundImage:
                                            'url("https://lh3.googleusercontent.com/aida-public/AB6AXuADzbhCv9pSoqAqE8jPB4-RLSGMVyLfLfaTG6vUmTlH_P7mnUfHHlCIcrCz5bk2f6PLiJB5V0YQ-VLeHLz_s5RLYjEGMtOIsknpLtBZid-CAMX8pdu7UkHiqOwyzyRlWtC8f5IOdst2njKsb69UEAIYgUkn2HwmJ5OxcLUxdzj2yOg4ESs5D3D4r02mVxefNEDesTbXwLM1QQt1sLwVp8TI6wPwcmL4mOxWOC_crhVdIzAN-E04W41w2KZ3Nre9bhggk3MQpc8m2Bs")',
                                    }}
                                ></div>
                                <div className="absolute top-2 right-2 flex items-center gap-1 border border-border-dark bg-background-dark/80 px-2 py-0.5 text-[10px] font-bold text-text-light">
                                    <span className="material-symbols-outlined text-[12px] text-primary">star</span> 4.9
                                </div>
                            </div>
                            <div>
                                <h4 className="truncate text-sm font-bold text-text-light uppercase">CHAINSAW MAN</h4>
                                <p className="text-xs text-zinc-500 uppercase">DARK FANTASY</p>
                            </div>
                        </div>
                        <div className="group flex cursor-pointer flex-col gap-2">
                            <div className="relative aspect-[2/3] w-full overflow-hidden border border-border-dark bg-zinc-800">
                                <div
                                    className="absolute inset-0 bg-cover bg-center bg-no-repeat transition-transform duration-500 group-hover:scale-105"
                                    style={{
                                        backgroundImage:
                                            'url("https://lh3.googleusercontent.com/aida-public/AB6AXuAzJ__yvE8BrvE4AZvGtbwYWC4ScWCQOu2A5xqjAxcqjefP3ZfR5yMdYaULENWyDVqRm_SVUuVxMNlCaY-jQH2oUi-FV_faQ33lSmhdXxb8edUeTdp1OPvcZTWg0s_mcpZEVMnYQkeKYTZt_qx4LZoJ_SNoZRrjisRqMmV4JDj6Iw7sXAOwaNZuxHc5vX7jpTpXyL6-b89BU0fo1egagsObpHe7v7msFdGerc3DY7p3U-cQVAJHh2XW9LNXWRZBSTBtHYKcCLlIzNo")',
                                    }}
                                ></div>
                                <div className="absolute top-2 left-2 border border-primary bg-primary/90 px-2 py-0.5 text-[10px] font-bold text-background-dark uppercase">
                                    NEW
                                </div>
                            </div>
                            <div>
                                <h4 className="truncate text-sm font-bold text-text-light uppercase">MY HERO ACADEMIA</h4>
                                <p className="text-xs text-zinc-500 uppercase">SUPERHERO</p>
                            </div>
                        </div>
                        <div className="group flex cursor-pointer flex-col gap-2">
                            <div className="relative aspect-[2/3] w-full overflow-hidden border border-border-dark bg-zinc-800">
                                <div
                                    className="absolute inset-0 bg-cover bg-center bg-no-repeat transition-transform duration-500 group-hover:scale-105"
                                    style={{
                                        backgroundImage:
                                            'url("https://lh3.googleusercontent.com/aida-public/AB6AXuCZz5l-kxYLLDul4fdJ2NibJ9EdAOft4txhulIbvK9GOCwsET0KWpkfBgp9ZmebGBq9zcjPvND2Oom2vb14GJL9yxM2ntRHjJAMX9rkF_uFmwvbVdm1am2t9S2YI_PVaO3oQkO4gSEfIXNI36g-quGJkpD_scdnjRBZobILh3kBZDsV1C4nQIYz5uWRy5tsc5g4ndmWsL6SLwjoLZOUFSXiEvBucXM7lx7iT2Ys1QO9Pi_Wk5PvuzEVNFBnDBau5TQhOAWVBKsFWI8")',
                                    }}
                                ></div>
                                <div className="absolute top-2 right-2 flex items-center gap-1 border border-border-dark bg-background-dark/80 px-2 py-0.5 text-[10px] font-bold text-text-light">
                                    <span className="material-symbols-outlined text-[12px] text-primary">star</span> 4.8
                                </div>
                            </div>
                            <div>
                                <h4 className="truncate text-sm font-bold text-text-light uppercase">ATTACK ON TITAN</h4>
                                <p className="text-xs text-zinc-500 uppercase">DARK FANTASY</p>
                            </div>
                        </div>
                        <div className="group flex cursor-pointer flex-col gap-2">
                            <div className="relative aspect-[2/3] w-full overflow-hidden border border-border-dark bg-zinc-800">
                                <div
                                    className="absolute inset-0 bg-cover bg-center bg-no-repeat transition-transform duration-500 group-hover:scale-105"
                                    style={{
                                        backgroundImage:
                                            'url("https://lh3.googleusercontent.com/aida-public/AB6AXuDVvFtjfGhkVLx6tNK6iC8czVaL92Ki72i-sNxajfDGL3CckLJtdMaxs-rUPkQePPeoghIhZe0aSx_CfSFhpUw3pSlwTIiJ7vAG_iU2fRo6qSwM8GUiwKfC1ANXYhTOUa2xu5qJW-OfYXoMfQwaQZRDukz2uarG-LCTrZZaNbFEzRWsV5TwgGox3a-P6lvct-Oc2VGiRIkWF2aP2RD67wXLy-nmwhH0ZOO4miE7ptKeKpOTWuTevyi6X5pKFSqvlw-UOL7jz31PXjw")',
                                    }}
                                ></div>
                            </div>
                            <div>
                                <h4 className="truncate text-sm font-bold text-text-light uppercase">DEMON SLAYER</h4>
                                <p className="text-xs text-zinc-500 uppercase">ADVENTURE</p>
                            </div>
                        </div>
                        <div className="group flex cursor-pointer flex-col gap-2">
                            <div className="relative aspect-[2/3] w-full overflow-hidden border border-border-dark bg-zinc-800">
                                <div
                                    className="absolute inset-0 bg-cover bg-center bg-no-repeat transition-transform duration-500 group-hover:scale-105"
                                    style={{
                                        backgroundImage:
                                            'url("https://lh3.googleusercontent.com/aida-public/AB6AXuC-Uy5BxepQ1Nb7ELOVtJalVKm7c4ItnrSySb4-VxM06R7ATsbeNffQMZtLXqxOSG9MJv8tA6R3Rcd7fZ9kQebd0AU6himFsEAQLaOpr-U6r9yzFKUX1j_DP9m6fOZr7QYjBYsaiPhi74I5umkR1bv-DjF69qKN4byG6y6Ze82a_nHUrW9LjF0VapgPBhdg-qKv9Wl6WzQrWsY1ZisB-0lbZAbZ0v3MGZ0mdhQRUwo7DiRyG4vewrPsgqBTBkVH-ljSJP29fEeYTYY")',
                                    }}
                                ></div>
                                <div className="absolute top-2 left-2 border border-primary bg-primary/90 px-2 py-0.5 text-[10px] font-bold text-background-dark uppercase">
                                    UPDATED
                                </div>
                            </div>
                            <div>
                                <h4 className="truncate text-sm font-bold text-text-light uppercase">BLUE LOCK</h4>
                                <p className="text-xs text-zinc-500 uppercase">SPORTS</p>
                            </div>
                        </div>
                        <div className="group flex cursor-pointer flex-col gap-2">
                            <div className="relative aspect-[2/3] w-full overflow-hidden border border-border-dark bg-zinc-800">
                                <div
                                    className="absolute inset-0 bg-cover bg-center bg-no-repeat transition-transform duration-500 group-hover:scale-105"
                                    style={{
                                        backgroundImage:
                                            'url("https://lh3.googleusercontent.com/aida-public/AB6AXuDjgeJFYaK2l32jhmD1vppMp9B6FveEjdrTkA2ywcGNECErAHXDaQFftZpx9c59cUBUEXsclr5D9KWURmaT6ywXcunfhV3ghc0v6LLy2UO8t88VGisIj6IL9-e_MvCcLEwgQMrWoqR7hDk4FYC1aTK4CIPfJlX7M___-fOUdyZvFAON65QvWt4jH3hZ0CPBmfFljR5j-mzAe1D1AakXoWrTGwUS0gBx8Q0eAQze_bIXOXaa0qwnWfzHP469a-GWsVNsQGDesf6J4XM")',
                                    }}
                                ></div>
                                <div className="absolute top-2 right-2 flex items-center gap-1 border border-border-dark bg-background-dark/80 px-2 py-0.5 text-[10px] font-bold text-text-light">
                                    <span className="material-symbols-outlined text-[12px] text-primary">star</span> 4.7
                                </div>
                            </div>
                            <div>
                                <h4 className="truncate text-sm font-bold text-text-light uppercase">VINLAND SAGA</h4>
                                <p className="text-xs text-zinc-500 uppercase">HISTORICAL</p>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Because You Read... */}
                <section className="flex flex-col gap-3 pb-8">
                    <div className="flex items-center justify-between px-4">
                        <h3 className="text-lg font-bold text-text-light uppercase">BECAUSE YOU READ NARUTO</h3>
                    </div>
                    <div className="no-scrollbar flex snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-2">
                        <div className="group flex w-[140px] flex-none cursor-pointer snap-center flex-col gap-2">
                            <div className="relative aspect-[2/3] w-full overflow-hidden border border-border-dark bg-zinc-800">
                                <div
                                    className="absolute inset-0 bg-cover bg-center bg-no-repeat transition-transform duration-500 group-hover:scale-105"
                                    style={{
                                        backgroundImage:
                                            'url("https://lh3.googleusercontent.com/aida-public/AB6AXuCPsLHBcy8sdfd8y6qkXKFns3KL7w6rV7YlxdYoD3UUIfUU1yOsBdcgaVHzsqFCQF9FqiUwO-Wcd8ZJfX5njcn73L6q3Oa_zVhktDcIFHKXX6nLXbUCSO8nNX0QR4CerHu5kbWLziNwbpvAFznxNoS_5tVogaoEX_VX75mPeDgA4DuzAsMIU0e8CnETwm3-yw8L8TfaK-_uh4HMOzaLRoPH5cVPL0LPeMdwtAJBo5hm-cmRl1iijaNOnmnRRo8yb3IWHHdCY3Pu1tI")',
                                    }}
                                ></div>
                            </div>
                            <div>
                                <h4 className="truncate text-sm font-bold text-text-light uppercase">BORUTO</h4>
                                <p className="text-xs text-zinc-500 uppercase">NEXT GEN</p>
                            </div>
                        </div>
                        <div className="group flex w-[140px] flex-none cursor-pointer snap-center flex-col gap-2">
                            <div className="relative aspect-[2/3] w-full overflow-hidden border border-border-dark bg-zinc-800">
                                <div
                                    className="absolute inset-0 bg-cover bg-center bg-no-repeat transition-transform duration-500 group-hover:scale-105"
                                    style={{
                                        backgroundImage:
                                            'url("https://lh3.googleusercontent.com/aida-public/AB6AXuBY3rLfwO9Ew_iRWjNaPXQQfFY6z6U1fcp96-g1OzkTncYg1lpdf1CbOm41gXQaKv3ku4vf6JfLdZvpArN4I5xNDHulSxvsTkXyIpy6GZUk566b-Drme7qJgKL9_FR8gw0N5ucGnJ9501oqABDum3s5WH2fxOufJudy7rNg2q7t6INmmfi-Tj1EopM9PF6HLWYJWzZeDCWqAGDMvbepF9hR6w_WBNgqaY0gwqUMfuE72x0qX6EFnQ8EWpmQhV0I6kkCGUJ7Qdla_1w")',
                                    }}
                                ></div>
                            </div>
                            <div>
                                <h4 className="truncate text-sm font-bold text-text-light uppercase">BLACK CLOVER</h4>
                                <p className="text-xs text-zinc-500 uppercase">MAGIC</p>
                            </div>
                        </div>
                        <div className="group flex w-[140px] flex-none cursor-pointer snap-center flex-col gap-2">
                            <div className="relative aspect-[2/3] w-full overflow-hidden border border-border-dark bg-zinc-800">
                                <div
                                    className="absolute inset-0 bg-cover bg-center bg-no-repeat transition-transform duration-500 group-hover:scale-105"
                                    style={{
                                        backgroundImage:
                                            'url("https://lh3.googleusercontent.com/aida-public/AB6AXuATV2LY7Ca0dweKG6PY9I90kIF1aFL8XM6fDjV3E2yLig62nrP2SRgT_XNmJwo8bX_qOgK6TqRTIws5nDArcVadrVsbKwk5wgEwBtyGiBWb0IW_w6LHJGH77hTfd_MdXNuduyTsC7e49SCXbNdvrBmUBOLvVLQq6DU-Q4wY9FL3IszRAyLAeUEBdkJVsY2aRbQpieu3GofRqOBrO-AB9EEcxrc27m0ijXOi9F3ljpQo8x_lOfTHbGj_njPHRyubNo3U16ZQn3CBRno")',
                                    }}
                                ></div>
                            </div>
                            <div>
                                <h4 className="truncate text-sm font-bold text-text-light uppercase">DRAGON BALL SUPER</h4>
                                <p className="text-xs text-zinc-500 uppercase">ACTION</p>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Background Glow */}
                <div className="pointer-events-none fixed top-0 left-1/2 -z-10 h-[50vh] w-full -translate-x-1/2 rounded-none bg-primary/10 opacity-30 blur-[80px]"></div>
            </main>
        </AppLayout>
    );
}
