<!DOCTYPE html>
<html class="dark" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Manga App Home</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#4CFF00","background-dark": "#0A0A0A","surface-dark": "#1A1A1A","text-light": "#E0E0E0",},
                    fontFamily: {
                        "mono": ["'Space Mono'", "monospace"]},
                    borderRadius: {
                        "none": "0px",},
                },
            },
        }
    </script>
<style>.no-scrollbar::-webkit-scrollbar {
            display: none;
        }.no-scrollbar {
            -ms-overflow-style: none;scrollbar-width: none;}
    </style>
<style>
    body {
      min-height: max(884px, 100dvh);
    }
  </style>
<style>
    body {
      min-height: max(884px, 100dvh);
    }
  </style>
  </head>
<body class="bg-background-dark font-mono text-text-light antialiased">
<div class="relative flex h-full w-full max-w-md mx-auto flex-col overflow-hidden pb-20 border-x border-slate-700">
<header class="flex flex-col gap-4 p-4 pt-12 pb-4 sticky top-0 z-20 bg-background-dark/95 backdrop-blur-sm border-b border-slate-700">
<div class="flex items-center justify-between">
<div class="flex items-center gap-3">
<div class="bg-center bg-no-repeat bg-cover size-10 ring-1 ring-primary" data-alt="User profile avatar with anime character" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDKIWh7R3m6byrcvwlY6mcmU9zKbBG_bIBWoqY5LZwrifsD28-9GNT7YWCG2diwAL5ry1w_JbimssL6xLUlBZU60hcAx2tRM6uqneOa5QEi8vLvPIPPlvjaR2wA9zgEHBJcDqThorjKEXzQ7hbGZHGsXbTZVa-ucRmJKBtqvuO_sTJQ6eYU0XJ0sNhKvmYbK_EqDRVex7fKwGHd3QxC3QZbMJoFsAn_3UKbLciTny1zHcVqKR_XDLXuOO9PcyJk1JEuOEUcJUuJcb8");'>
</div>
<div>
<p class="text-xs text-slate-400 font-bold uppercase tracking-widest">WELCOME BACK</p>
<p class="text-xl font-bold leading-tight text-primary">ALEX READER</p>
</div>
</div>
<button class="flex items-center justify-center size-10 bg-surface-dark hover:bg-slate-700 transition-colors relative border border-slate-700">
<span class="material-symbols-outlined text-text-light" style="font-size: 24px;">notifications</span>
<span class="absolute top-2 right-2 size-2.5 bg-primary border-2 border-surface-dark"></span>
</button>
</div>
<div class="relative w-full mt-4">
<div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
<span class="material-symbols-outlined text-slate-500" style="font-size: 20px;">search</span>
</div>
<input class="block w-full pl-11 pr-4 py-3 text-sm bg-surface-dark border border-slate-700 focus:ring-2 focus:ring-primary placeholder-slate-500 text-text-light shadow-none outline-none" placeholder="SEARCH MANGA, AUTHORS, OR GENRES..." type="text"/>
</div>
</header>
<main class="flex-1 flex flex-col gap-6 overflow-y-auto no-scrollbar pb-6 pt-4">
<section class="px-4">
<div class="relative w-full aspect-[4/3] overflow-hidden shadow-lg group cursor-pointer border border-slate-700">
<div class="absolute inset-0 bg-center bg-cover bg-no-repeat transition-transform duration-700 group-hover:scale-105" data-alt="Epic battle scene from One Piece manga" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBi1DW4Nbcv_qxuuXKEIO9ac0d4P9mkgGooxyAbK2WmcXA5dlFT4O6DKuk7X4eZWgJooiEz1V7S-no82oLLD03C2QoU7FiS189mwZFUeCsxGM2wGpkXQz-XmX1gB2vaP1DPYAOOKxKCR1jLnL7KUMWxbmcFZ-QOK0swXhRMEoZQDJAD7TSO2yQemk1FPx7t6kIzRA6UcvzHuhpBOJbVjpVzz5uimgAmdmxljxi2DjVG1E5hvuSv-Li34jwwksv8N2qS5Pa_jPsJxaI");'>
</div>
<div class="absolute inset-0 bg-gradient-to-t from-background-dark via-background-dark/60 to-transparent"></div>
<div class="absolute bottom-0 left-0 right-0 p-5 flex flex-col items-start gap-3">
<span class="px-3 py-1 bg-primary text-background-dark text-xs font-bold uppercase tracking-widest shadow-lg shadow-primary/40">
                            #1 TRENDING
                        </span>
<div>
<h2 class="text-3xl font-bold text-text-light mb-1 uppercase">ONE PIECE</h2>
<p class="text-slate-400 text-sm line-clamp-2">THE STRAW HAT PIRATES CONTINUE THEIR ADVENTURE IN THE EGGHEAD ARC WITH SHOCKING REVELATIONS.</p>
</div>
<div class="flex items-center gap-3 w-full mt-1">
<button class="flex-1 h-10 bg-text-light text-background-dark font-bold text-sm flex items-center justify-center gap-2 hover:bg-slate-300 transition-colors border border-text-light">
<span class="material-symbols-outlined" style="font-size: 20px;">menu_book</span>
                                READ CHAPTER 1090
                            </button>
<button class="size-10 flex items-center justify-center bg-surface-dark text-text-light hover:bg-slate-700 transition-colors border border-slate-700">
<span class="material-symbols-outlined" style="font-size: 20px;">bookmark_add</span>
</button>
</div>
</div>
</div>
</section>
<section class="flex flex-col gap-3">
<div class="flex items-center justify-between px-4">
<h3 class="text-lg font-bold text-text-light uppercase">CONTINUE READING</h3>
<a class="text-sm font-bold text-primary hover:text-primary/80 uppercase" href="#">SEE ALL</a>
</div>
<div class="flex overflow-x-auto gap-4 px-4 pb-2 no-scrollbar snap-x snap-mandatory">
<div class="flex-none snap-center w-[280px] p-3 bg-surface-dark shadow-sm flex items-center gap-3 border border-slate-700">
<div class="w-16 h-20 shrink-0 bg-center bg-cover relative overflow-hidden border border-slate-600" data-alt="Jujutsu Kaisen manga cover art" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDQhvePW49Fsq4MlbcW6Mz5u64_QINmF_gAGU6FT9TaPTGz-jQ_gKcCvbOU3F_1zvayWRY9yD2UsW7Q3dXyksVOona4bM5TSedaI-oBpsV-D8xccZcHvQUFZQY3LdEttg1KR0ky-qWj58iiPGbeFuoc7IbL9-WrhtJv7p7lroMAuCJyJvIwFlnYCg8Z4vMe3ifN95RnaDI3fvHA9MpybL0EHTKrjFiRdHevZhW16LthMHQPw_eH-aEEiuXww0OZMs7uU46953sMbHo");'>
</div>
<div class="flex flex-col justify-center flex-1 min-w-0">
<h4 class="font-bold text-text-light truncate uppercase">JUJUTSU KAISEN</h4>
<p class="text-xs text-slate-500 mb-2 uppercase">CHAPTER 45 • 2H AGO</p>
<div class="w-full h-1.5 bg-slate-700 overflow-hidden border border-slate-600">
<div class="h-full bg-primary" style="width: 60%;"></div>
</div>
</div>
<button class="size-8 bg-primary text-background-dark flex items-center justify-center shrink-0 border border-primary">
<span class="material-symbols-outlined" style="font-size: 20px;">play_arrow</span>
</button>
</div>
<div class="flex-none snap-center w-[280px] p-3 bg-surface-dark shadow-sm flex items-center gap-3 border border-slate-700">
<div class="w-16 h-20 shrink-0 bg-center bg-cover relative overflow-hidden border border-slate-600" data-alt="Spy x Family manga cover art" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuADeZRkMbfpPnZ6vXuOF6paOuRha3isQlaxakDW_fEWALkHAJY3IUQt1pJ2RW8a4LlPlnnSbwCkY0sC9IFXLvOi04s-46oqGmtWUcSJYjO3EXpmRv9A3AVrFYTzpMHHuj52YFZVAABP1s4KlSIjJ9lVflj0YhfB3oPOyVJuaU0h6RB8r8vNcpNnQ6QnL02BjxO8a3nylwnVGn2py7w3IlAd1gfabCllAQov4pKDTHatb3JpOQGzuiyB5K1uZ1hjm6BhwWYFpmRaEGg");'>
</div>
<div class="flex flex-col justify-center flex-1 min-w-0">
<h4 class="font-bold text-text-light truncate uppercase">SPY X FAMILY</h4>
<p class="text-xs text-slate-500 mb-2 uppercase">CHAPTER 12 • 1D AGO</p>
<div class="w-full h-1.5 bg-slate-700 overflow-hidden border border-slate-600">
<div class="h-full bg-primary" style="width: 25%;"></div>
</div>
</div>
<button class="size-8 bg-primary text-background-dark flex items-center justify-center shrink-0 border border-primary">
<span class="material-symbols-outlined" style="font-size: 20px;">play_arrow</span>
</button>
</div>
</div>
</section>
<section class="flex flex-col gap-3">
<div class="flex items-center justify-between px-4">
<h3 class="text-lg font-bold text-text-light uppercase">TRENDING NOW</h3>
</div>
<div class="grid grid-cols-2 sm:grid-cols-3 gap-4 px-4">
<div class="flex flex-col gap-2 group cursor-pointer">
<div class="relative aspect-[2/3] w-full overflow-hidden bg-slate-800 border border-slate-700">
<div class="absolute inset-0 bg-center bg-cover bg-no-repeat group-hover:scale-105 transition-transform duration-500" data-alt="Chainsaw Man dark fantasy manga cover" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuADzbhCv9pSoqAqE8jPB4-RLSGMVyLfLfaTG6vUmTlH_P7mnUfHHlCIcrCz5bk2f6PLiJB5V0YQ-VLeHLz_s5RLYjEGMtOIsknpLtBZid-CAMX8pdu7UkHiqOwyzyRlWtC8f5IOdst2njKsb69UEAIYgUkn2HwmJ5OxcLUxdzj2yOg4ESs5D3D4r02mVxefNEDesTbXwLM1QQt1sLwVp8TI6wPwcmL4mOxWOC_crhVdIzAN-E04W41w2KZ3Nre9bhggk3MQpc8m2Bs");'>
</div>
<div class="absolute top-2 right-2 bg-background-dark/80 px-2 py-0.5 text-[10px] font-bold text-text-light flex items-center gap-1 border border-slate-700">
<span class="material-symbols-outlined text-primary" style="font-size: 12px;">star</span> 4.9
                            </div>
</div>
<div>
<h4 class="font-bold text-sm text-text-light truncate uppercase">CHAINSAW MAN</h4>
<p class="text-xs text-slate-500 uppercase">DARK FANTASY</p>
</div>
</div>
<div class="flex flex-col gap-2 group cursor-pointer">
<div class="relative aspect-[2/3] w-full overflow-hidden bg-slate-800 border border-slate-700">
<div class="absolute inset-0 bg-center bg-cover bg-no-repeat group-hover:scale-105 transition-transform duration-500" data-alt="My Hero Academia superhero manga cover" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAzJ__yvE8BrvE4AZvGtbwYWC4ScWCQOu2A5xqjAxcqjefP3ZfR5yMdYaULENWyDVqRm_SVUuVxMNlCaY-jQH2oUi-FV_faQ33lSmhdXxb8edUeTdp1OPvcZTWg0s_mcpZEVMnYQkeKYTZt_qx4LZoJ_SNoZRrjisRqMmV4JDj6Iw7sXAOwaNZuxHc5vX7jpTpXyL6-b89BU0fo1egagsObpHe7v7msFdGerc3DY7p3U-cQVAJHh2XW9LNXWRZBSTBtHYKcCLlIzNo");'>
</div>
<div class="absolute top-2 left-2 bg-primary/90 px-2 py-0.5 text-[10px] font-bold text-background-dark uppercase border border-primary">
                                NEW
                            </div>
</div>
<div>
<h4 class="font-bold text-sm text-text-light truncate uppercase">MY HERO ACADEMIA</h4>
<p class="text-xs text-slate-500 uppercase">SUPERHERO</p>
</div>
</div>
<div class="flex flex-col gap-2 group cursor-pointer">
<div class="relative aspect-[2/3] w-full overflow-hidden bg-slate-800 border border-slate-700">
<div class="absolute inset-0 bg-center bg-cover bg-no-repeat group-hover:scale-105 transition-transform duration-500" data-alt="Attack on Titan giant monster manga cover" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCZz5l-kxYLLDul4fdJ2NibJ9EdAOft4txhulIbvK9GOCwsET0KWpkfBgp9ZmebGBq9zcjPvND2Oom2vb14GJL9yxM2ntRHjJAMX9rkF_uFmwvbVdm1am2t9S2YI_PVaO3oQkO4gSEfIXNI36g-quGJkpD_scdnjRBZobILh3kBZDsV1C4nQIYz5uWRy5tsc5g4ndmWsL6SLwjoLZOUFSXiEvBucXM7lx7iT2Ys1QO9Pi_Wk5PvuzEVNFBnDBau5TQhOAWVBKsFWI8");'>
</div>
<div class="absolute top-2 right-2 bg-background-dark/80 px-2 py-0.5 text-[10px] font-bold text-text-light flex items-center gap-1 border border-slate-700">
<span class="material-symbols-outlined text-primary" style="font-size: 12px;">star</span> 4.8
                            </div>
</div>
<div>
<h4 class="font-bold text-sm text-text-light truncate uppercase">ATTACK ON TITAN</h4>
<p class="text-xs text-slate-500 uppercase">DARK FANTASY</p>
</div>
</div>
<div class="flex flex-col gap-2 group cursor-pointer">
<div class="relative aspect-[2/3] w-full overflow-hidden bg-slate-800 border border-slate-700">
<div class="absolute inset-0 bg-center bg-cover bg-no-repeat group-hover:scale-105 transition-transform duration-500" data-alt="Demon Slayer sword action manga cover" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDVvFtjfGhkVLx6tNK6iC8czVaL92Ki72i-sNxajfDGL3CckLJtdMaxs-rUPkQePPeoghIhZe0aSx_CfSFhpUw3pSlwTIiJ7vAG_iU2fRo6qSwM8GUiwKfC1ANXYhTOUa2xu5qJW-OfYXoMfQwaQZRDukz2uarG-LCTrZZaNbFEzRWsV5TwgGox3a-P6lvct-Oc2VGiRIkWF2aP2RD67wXLy-nmwhH0ZOO4miE7ptKeKpOTWuTevyi6X5pKFSqvlw-UOL7jz31PXjw");'>
</div>
</div>
<div>
<h4 class="font-bold text-sm text-text-light truncate uppercase">DEMON SLAYER</h4>
<p class="text-xs text-slate-500 uppercase">ADVENTURE</p>
</div>
</div>
<div class="flex flex-col gap-2 group cursor-pointer">
<div class="relative aspect-[2/3] w-full overflow-hidden bg-slate-800 border border-slate-700">
<div class="absolute inset-0 bg-center bg-cover bg-no-repeat group-hover:scale-105 transition-transform duration-500" data-alt="Blue Lock sports soccer manga cover" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuC-Uy5BxepQ1Nb7ELOVtJalVKm7c4ItnrSySb4-VxM06R7ATsbeNffQMZtLXqxOSG9MJv8tA6R3Rcd7fZ9kQebd0AU6himFsEAQLaOpr-U6r9yzFKUX1j_DP9m6fOZr7QYjBYsaiPhi74I5umkR1bv-DjF69qKN4byG6y6Ze82a_nHUrW9LjF0VapgPBhdg-qKv9Wl6WzQrWsY1ZisB-0lbZAbZ0v3MGZ0mdhQRUwo7DiRyG4vewrPsgqBTBkVH-ljSJP29fEeYTYY");'>
</div>
<div class="absolute top-2 left-2 bg-primary/90 px-2 py-0.5 text-[10px] font-bold text-background-dark uppercase border border-primary">
                                UPDATED
                            </div>
</div>
<div>
<h4 class="font-bold text-sm text-text-light truncate uppercase">BLUE LOCK</h4>
<p class="text-xs text-slate-500 uppercase">SPORTS</p>
</div>
</div>
<div class="flex flex-col gap-2 group cursor-pointer">
<div class="relative aspect-[2/3] w-full overflow-hidden bg-slate-800 border border-slate-700">
<div class="absolute inset-0 bg-center bg-cover bg-no-repeat group-hover:scale-105 transition-transform duration-500" data-alt="Vinland Saga historical viking manga cover" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDjgeJFYaK2l32jhmD1vppMp9B6FveEjdrTkA2ywcGNECErAHXDaQFftZpx9c59cUBUEXsclr5D9KWURmaT6ywXcunfhV3ghc0v6LLy2UO8t88VGisIj6IL9-e_MvCcLEwgQMrWoqR7hDk4FYC1aTK4CIPfJlX7M___-fOUdyZvFAON65QvWt4jH3hZ0CPBmfFljR5j-mzAe1D1AakXoWrTGwUS0gBx8Q0eAQze_bIXOXaa0qwnWfzHP469a-GWsVNsQGDesf6J4XM");'>
</div>
<div class="absolute top-2 right-2 bg-background-dark/80 px-2 py-0.5 text-[10px] font-bold text-text-light flex items-center gap-1 border border-slate-700">
<span class="material-symbols-outlined text-primary" style="font-size: 12px;">star</span> 4.7
                            </div>
</div>
<div>
<h4 class="font-bold text-sm text-text-light truncate uppercase">VINLAND SAGA</h4>
<p class="text-xs text-slate-500 uppercase">HISTORICAL</p>
</div>
</div>
</div>
</section>
<section class="flex flex-col gap-3 pb-8">
<div class="flex items-center justify-between px-4">
<h3 class="text-lg font-bold text-text-light uppercase">BECAUSE YOU READ NARUTO</h3>
</div>
<div class="flex overflow-x-auto gap-4 px-4 pb-2 no-scrollbar snap-x snap-mandatory">
<div class="flex-none snap-center w-[140px] flex flex-col gap-2 group cursor-pointer">
<div class="relative aspect-[2/3] w-full overflow-hidden bg-slate-800 border border-slate-700">
<div class="absolute inset-0 bg-center bg-cover bg-no-repeat group-hover:scale-105 transition-transform duration-500" data-alt="Boruto manga cover art" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCPsLHBcy8sdfd8y6qkXKFns3KL7w6rV7YlxdYoD3UUIfUU1yOsBdcgaVHzsqFCQF9FqiUwO-Wcd8ZJfX5njcn73L6q3Oa_zVhktDcIFHKXX6nLXbUCSO8nNX0QR4CerHu5kbWLziNwbpvAFznxNoS_5tVogaoEX_VX75mPeDgA4DuzAsMIU0e8CnETwm3-yw8L8TfaK-_uh4HMOzaLRoPH5cVPL0LPeMdwtAJBo5hm-cmRl1iijaNOnmnRRo8yb3IWHHdCY3Pu1tI");'>
</div>
</div>
<div>
<h4 class="font-bold text-sm text-text-light truncate uppercase">BORUTO</h4>
<p class="text-xs text-slate-500 uppercase">NEXT GEN</p>
</div>
</div>
<div class="flex-none snap-center w-[140px] flex flex-col gap-2 group cursor-pointer">
<div class="relative aspect-[2/3] w-full overflow-hidden bg-slate-800 border border-slate-700">
<div class="absolute inset-0 bg-center bg-cover bg-no-repeat group-hover:scale-105 transition-transform duration-500" data-alt="Black Clover magic fantasy manga cover" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBY3rLfwO9Ew_iRWjNaPXQQfFY6z6U1fcp96-g1OzkTncYg1lpdf1CbOm41gXQaKv3ku4vf6JfLdZvpArN4I5xNDHulSxvsTkXyIpy6GZUk566b-Drme7qJgKL9_FR8gw0N5ucGnJ9501oqABDum3s5WH2fxOufJudy7rNg2q7t6INmmfi-Tj1EopM9PF6HLWYJWzZeDCWqAGDMvbepF9hR6w_WBNgqaY0gwqUMfuE72x0qX6EFnQ8EWpmQhV0I6kkCGUJ7Qdla_1w");'>
</div>
</div>
<div>
<h4 class="font-bold text-sm text-text-light truncate uppercase">BLACK CLOVER</h4>
<p class="text-xs text-slate-500 uppercase">MAGIC</p>
</div>
</div>
<div class="flex-none snap-center w-[140px] flex flex-col gap-2 group cursor-pointer">
<div class="relative aspect-[2/3] w-full overflow-hidden bg-slate-800 border border-slate-700">
<div class="absolute inset-0 bg-center bg-cover bg-no-repeat group-hover:scale-105 transition-transform duration-500" data-alt="Dragon Ball Super manga cover art" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuATV2LY7Ca0dweKG6PY9I90kIF1aFL8XM6fDjV3E2yLig62nrP2SRgT_XNmJwo8bX_qOgK6TqRTIws5nDArcVadrVsbKwk5wgEwBtyGiBWb0IW_w6LHJGH77hTfd_MdXNuduyTsC7e49SCXbNdvrBmUBOLvVLQq6DU-Q4wY9FL3IszRAyLAeUEBdkJVsY2aRbQpieu3GofRqOBrO-AB9EEcxrc27m0ijXOi9F3ljpQo8x_lOfTHbGj_njPHRyubNo3U16ZQn3CBRno");'>
</div>
</div>
<div>
<h4 class="font-bold text-sm text-text-light truncate uppercase">DRAGON BALL SUPER</h4>
<p class="text-xs text-slate-500 uppercase">ACTION</p>
</div>
</div>
</div>
</section>
</main>
<nav class="absolute bottom-0 left-0 right-0 px-4 pb-4 pt-2 z-30">
<div class="h-16 bg-surface-dark/95 backdrop-blur-xl shadow-[0_4px_16px_0_rgba(0,0,0,0.56)] flex items-center justify-around px-2 border border-slate-700">
<button class="flex flex-col items-center justify-center w-14 h-full gap-1 text-primary">
<span class="material-symbols-outlined" style="font-size: 24px; font-variation-settings: 'FILL' 1;">home</span>
<span class="text-[10px] font-bold uppercase">HOME</span>
</button>
<button class="flex flex-col items-center justify-center w-14 h-full gap-1 text-slate-500 hover:text-text-light transition-colors">
<span class="material-symbols-outlined" style="font-size: 24px;">explore</span>
<span class="text-[10px] font-bold uppercase">BROWSE</span>
</button>
<button class="flex flex-col items-center justify-center w-14 h-full gap-1 text-slate-500 hover:text-text-light transition-colors">
<span class="material-symbols-outlined" style="font-size: 24px;">bookmarks</span>
<span class="text-[10px] font-bold uppercase">LIBRARY</span>
</button>
<button class="flex flex-col items-center justify-center w-14 h-full gap-1 text-slate-500 hover:text-text-light transition-colors">
<span class="material-symbols-outlined" style="font-size: 24px;">person</span>
<span class="text-[10px] font-bold uppercase">ME</span>
</button>
</div>
</nav>
<div class="fixed top-0 left-1/2 -translate-x-1/2 w-full h-[50vh] bg-primary/10 blur-[80px] rounded-none pointer-events-none -z-10 opacity-30"></div>
</div>

</body></html>