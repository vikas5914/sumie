<!DOCTYPE html>
<html class="dark" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Manga App Library</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "tech-green": "#00FF41",
                        "tech-dark": "#050505",
                        "tech-panel": "#111111",
                        "tech-border": "#333333",
                        "tech-text": "#F0F0F0",
                        "tech-muted": "#666666"
                    },
                    fontFamily: {
                        "mono": ["'JetBrains Mono'", "monospace"]
                    },
                    borderRadius: {
                        "none": "0px",
                    },
                    boxShadow: {
                        'tech': '4px 4px 0px 0px #333333',
                        'tech-hover': '2px 2px 0px 0px #00FF41',
                        'tech-active': '0px 0px 0px 0px #000000',
                    }
                },
            },
        }
    </script>
<style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        body {
            min-height: 100dvh;
        }.tech-checkbox {
            appearance: none;
            background-color: transparent;
            margin: 0;
            font: inherit;
            color: currentColor;
            width: 1.15em;
            height: 1.15em;
            border: 1px solid currentColor;
            display: grid;
            place-content: center;
        }
        .tech-checkbox::before {
            content: "";
            width: 0.65em;
            height: 0.65em;
            transform: scale(0);
            transition: 120ms transform ease-in-out;
            box-shadow: inset 1em 1em var(--primary-color);
            background-color: #00FF41;
        }
        .tech-checkbox:checked::before {
            transform: scale(1);
        }
    </style>
<style>
    body {
      min-height: max(884px, 100dvh);
    }
  </style>
  </head>
<body class="bg-tech-dark font-mono text-tech-text antialiased selection:bg-tech-green selection:text-tech-dark">
<div class="relative flex h-full w-full max-w-md mx-auto flex-col overflow-hidden pb-24 border-x border-tech-border bg-tech-dark min-h-screen">
<header class="flex flex-col gap-0 pt-12 sticky top-0 z-20 bg-tech-dark/95 backdrop-blur-sm border-b border-tech-border">
<div class="flex items-center justify-between px-4 pb-4">
<h1 class="text-2xl font-extrabold uppercase tracking-tighter text-tech-green flex items-center gap-2">
<span class="w-3 h-6 bg-tech-green block"></span>
                LIBRARY
            </h1>
<div class="flex gap-2">
<button class="size-10 flex items-center justify-center bg-tech-panel hover:bg-tech-green hover:text-tech-dark transition-all border border-tech-border">
<span class="material-symbols-outlined" style="font-size: 20px;">search</span>
</button>
<button class="size-10 flex items-center justify-center bg-tech-panel hover:bg-tech-green hover:text-tech-dark transition-all border border-tech-border">
<span class="material-symbols-outlined" style="font-size: 20px;">tune</span>
</button>
</div>
</div>
<div class="flex overflow-x-auto no-scrollbar border-t border-tech-border bg-tech-panel">
<button class="px-6 py-3 text-xs font-bold uppercase border-r border-tech-border bg-tech-green text-tech-dark">Reading</button>
<button class="px-6 py-3 text-xs font-bold uppercase border-r border-tech-border hover:bg-tech-border hover:text-white text-tech-muted transition-colors">Completed</button>
<button class="px-6 py-3 text-xs font-bold uppercase border-r border-tech-border hover:bg-tech-border hover:text-white text-tech-muted transition-colors">Downloaded</button>
<button class="px-6 py-3 text-xs font-bold uppercase hover:bg-tech-border hover:text-white text-tech-muted transition-colors">Dropped</button>
</div>
</header>
<main class="flex-1 flex flex-col gap-0 overflow-y-auto no-scrollbar">
<div class="flex items-center justify-between px-4 py-3 border-b border-tech-border bg-tech-dark text-xs uppercase text-tech-muted">
<span>24 ITEMS</span>
<div class="flex items-center gap-4">
<button class="hover:text-tech-green flex items-center gap-1">
                    LAST READ <span class="material-symbols-outlined text-[14px]">arrow_drop_down</span>
</button>
<button class="hover:text-tech-green">
<span class="material-symbols-outlined text-[18px]">grid_view</span>
</button>
</div>
</div>
<div class="grid grid-cols-1 gap-0">
<div class="group relative flex border-b border-tech-border hover:bg-tech-panel transition-colors p-4 gap-4">
<div class="w-20 aspect-[2/3] shrink-0 border border-tech-border relative bg-tech-panel">
<div class="absolute inset-0 bg-cover bg-center grayscale group-hover:grayscale-0 transition-all duration-300" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuADzbhCv9pSoqAqE8jPB4-RLSGMVyLfLfaTG6vUmTlH_P7mnUfHHlCIcrCz5bk2f6PLiJB5V0YQ-VLeHLz_s5RLYjEGMtOIsknpLtBZid-CAMX8pdu7UkHiqOwyzyRlWtC8f5IOdst2njKsb69UEAIYgUkn2HwmJ5OxcLUxdzj2yOg4ESs5D3D4r02mVxefNEDesTbXwLM1QQt1sLwVp8TI6wPwcmL4mOxWOC_crhVdIzAN-E04W41w2KZ3Nre9bhggk3MQpc8m2Bs");'></div>
<div class="absolute -top-1 -left-1 w-2 h-2 bg-tech-green z-10"></div>
</div>
<div class="flex flex-col flex-1 justify-between py-1">
<div>
<div class="flex justify-between items-start mb-1">
<h3 class="font-bold text-lg leading-tight uppercase text-tech-text group-hover:text-tech-green transition-colors">Chainsaw Man</h3>
<button class="text-tech-muted hover:text-tech-green"><span class="material-symbols-outlined text-[20px]">more_vert</span></button>
</div>
<p class="text-xs text-tech-muted uppercase">Chap 143 • Unread: 2</p>
</div>
<div class="flex items-end justify-between mt-2">
<div class="flex flex-col gap-1 w-full mr-4">
<div class="flex justify-between text-[10px] uppercase font-bold text-tech-green">
<span>Progress</span>
<span>88%</span>
</div>
<div class="w-full h-2 bg-tech-border border border-tech-border p-[1px]">
<div class="h-full bg-tech-green w-[88%]"></div>
</div>
</div>
<button class="bg-tech-border hover:bg-tech-green hover:text-tech-dark text-tech-text border border-tech-border p-2 transition-all">
<span class="material-symbols-outlined text-[18px] block">play_arrow</span>
</button>
</div>
</div>
</div>
<div class="group relative flex border-b border-tech-border hover:bg-tech-panel transition-colors p-4 gap-4">
<div class="w-20 aspect-[2/3] shrink-0 border border-tech-border relative bg-tech-panel">
<div class="absolute inset-0 bg-cover bg-center grayscale group-hover:grayscale-0 transition-all duration-300" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDQhvePW49Fsq4MlbcW6Mz5u64_QINmF_gAGU6FT9TaPTGz-jQ_gKcCvbOU3F_1zvayWRY9yD2UsW7Q3dXyksVOona4bM5TSedaI-oBpsV-D8xccZcHvQUFZQY3LdEttg1KR0ky-qWj58iiPGbeFuoc7IbL9-WrhtJv7p7lroMAuCJyJvIwFlnYCg8Z4vMe3ifN95RnaDI3fvHA9MpybL0EHTKrjFiRdHevZhW16LthMHQPw_eH-aEEiuXww0OZMs7uU46953sMbHo");'></div>
</div>
<div class="flex flex-col flex-1 justify-between py-1">
<div>
<div class="flex justify-between items-start mb-1">
<h3 class="font-bold text-lg leading-tight uppercase text-tech-text group-hover:text-tech-green transition-colors">Jujutsu Kaisen</h3>
<button class="text-tech-muted hover:text-tech-green"><span class="material-symbols-outlined text-[20px]">more_vert</span></button>
</div>
<p class="text-xs text-tech-muted uppercase">Chap 236 • Unread: 0</p>
</div>
<div class="flex items-end justify-between mt-2">
<div class="flex flex-col gap-1 w-full mr-4">
<div class="flex justify-between text-[10px] uppercase font-bold text-tech-muted">
<span>Up to Date</span>
<span>100%</span>
</div>
<div class="w-full h-2 bg-tech-border border border-tech-border p-[1px]">
<div class="h-full bg-tech-muted w-full"></div>
</div>
</div>
<button class="bg-tech-border hover:bg-tech-green hover:text-tech-dark text-tech-text border border-tech-border p-2 transition-all">
<span class="material-symbols-outlined text-[18px] block">check</span>
</button>
</div>
</div>
</div>
<div class="group relative flex border-b border-tech-border hover:bg-tech-panel transition-colors p-4 gap-4">
<div class="w-20 aspect-[2/3] shrink-0 border border-tech-border relative bg-tech-panel">
<div class="absolute inset-0 bg-cover bg-center grayscale group-hover:grayscale-0 transition-all duration-300" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuC-Uy5BxepQ1Nb7ELOVtJalVKm7c4ItnrSySb4-VxM06R7ATsbeNffQMZtLXqxOSG9MJv8tA6R3Rcd7fZ9kQebd0AU6himFsEAQLaOpr-U6r9yzFKUX1j_DP9m6fOZr7QYjBYsaiPhi74I5umkR1bv-DjF69qKN4byG6y6Ze82a_nHUrW9LjF0VapgPBhdg-qKv9Wl6WzQrWsY1ZisB-0lbZAbZ0v3MGZ0mdhQRUwo7DiRyG4vewrPsgqBTBkVH-ljSJP29fEeYTYY");'></div>
<div class="absolute bottom-0 right-0 bg-tech-green text-tech-dark text-[10px] font-bold px-1 py-0.5 border-t border-l border-tech-dark">DL</div>
</div>
<div class="flex flex-col flex-1 justify-between py-1">
<div>
<div class="flex justify-between items-start mb-1">
<h3 class="font-bold text-lg leading-tight uppercase text-tech-text group-hover:text-tech-green transition-colors">Blue Lock</h3>
<button class="text-tech-muted hover:text-tech-green"><span class="material-symbols-outlined text-[20px]">more_vert</span></button>
</div>
<p class="text-xs text-tech-muted uppercase">Chap 245 • Unread: 5</p>
</div>
<div class="flex items-end justify-between mt-2">
<div class="flex flex-col gap-1 w-full mr-4">
<div class="flex justify-between text-[10px] uppercase font-bold text-tech-green">
<span>Progress</span>
<span>45%</span>
</div>
<div class="w-full h-2 bg-tech-border border border-tech-border p-[1px]">
<div class="h-full bg-tech-green w-[45%]"></div>
</div>
</div>
<button class="bg-tech-border hover:bg-tech-green hover:text-tech-dark text-tech-text border border-tech-border p-2 transition-all">
<span class="material-symbols-outlined text-[18px] block">play_arrow</span>
</button>
</div>
</div>
</div>
<div class="group relative flex border-b border-tech-border hover:bg-tech-panel transition-colors p-4 gap-4">
<div class="w-20 aspect-[2/3] shrink-0 border border-tech-border relative bg-tech-panel">
<div class="absolute inset-0 bg-cover bg-center grayscale group-hover:grayscale-0 transition-all duration-300" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDjgeJFYaK2l32jhmD1vppMp9B6FveEjdrTkA2ywcGNECErAHXDaQFftZpx9c59cUBUEXsclr5D9KWURmaT6ywXcunfhV3ghc0v6LLy2UO8t88VGisIj6IL9-e_MvCcLEwgQMrWoqR7hDk4FYC1aTK4CIPfJlX7M___-fOUdyZvFAON65QvWt4jH3hZ0CPBmfFljR5j-mzAe1D1AakXoWrTGwUS0gBx8Q0eAQze_bIXOXaa0qwnWfzHP469a-GWsVNsQGDesf6J4XM");'></div>
</div>
<div class="flex flex-col flex-1 justify-between py-1">
<div>
<div class="flex justify-between items-start mb-1">
<h3 class="font-bold text-lg leading-tight uppercase text-tech-text group-hover:text-tech-green transition-colors">Vinland Saga</h3>
<button class="text-tech-muted hover:text-tech-green"><span class="material-symbols-outlined text-[20px]">more_vert</span></button>
</div>
<p class="text-xs text-tech-muted uppercase">Chap 198 • Unread: 12</p>
</div>
<div class="flex items-end justify-between mt-2">
<div class="flex flex-col gap-1 w-full mr-4">
<div class="flex justify-between text-[10px] uppercase font-bold text-tech-green">
<span>Progress</span>
<span>12%</span>
</div>
<div class="w-full h-2 bg-tech-border border border-tech-border p-[1px]">
<div class="h-full bg-tech-green w-[12%]"></div>
</div>
</div>
<button class="bg-tech-border hover:bg-tech-green hover:text-tech-dark text-tech-text border border-tech-border p-2 transition-all">
<span class="material-symbols-outlined text-[18px] block">play_arrow</span>
</button>
</div>
</div>
</div>
<div class="group relative flex border-b border-tech-border hover:bg-tech-panel transition-colors p-4 gap-4">
<div class="w-20 aspect-[2/3] shrink-0 border border-tech-border relative bg-tech-panel">
<div class="absolute inset-0 bg-cover bg-center grayscale group-hover:grayscale-0 transition-all duration-300" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBY3rLfwO9Ew_iRWjNaPXQQfFY6z6U1fcp96-g1OzkTncYg1lpdf1CbOm41gXQaKv3ku4vf6JfLdZvpArN4I5xNDHulSxvsTkXyIpy6GZUk566b-Drme7qJgKL9_FR8gw0N5ucGnJ9501oqABDum3s5WH2fxOufJudy7rNg2q7t6INmmfi-Tj1EopM9PF6HLWYJWzZeDCWqAGDMvbepF9hR6w_WBNgqaY0gwqUMfuE72x0qX6EFnQ8EWpmQhV0I6kkCGUJ7Qdla_1w");'></div>
<div class="absolute bottom-0 right-0 bg-tech-green text-tech-dark text-[10px] font-bold px-1 py-0.5 border-t border-l border-tech-dark">DL</div>
</div>
<div class="flex flex-col flex-1 justify-between py-1">
<div>
<div class="flex justify-between items-start mb-1">
<h3 class="font-bold text-lg leading-tight uppercase text-tech-text group-hover:text-tech-green transition-colors">Black Clover</h3>
<button class="text-tech-muted hover:text-tech-green"><span class="material-symbols-outlined text-[20px]">more_vert</span></button>
</div>
<p class="text-xs text-tech-muted uppercase">Chap 368 • Unread: 1</p>
</div>
<div class="flex items-end justify-between mt-2">
<div class="flex flex-col gap-1 w-full mr-4">
<div class="flex justify-between text-[10px] uppercase font-bold text-tech-green">
<span>Progress</span>
<span>98%</span>
</div>
<div class="w-full h-2 bg-tech-border border border-tech-border p-[1px]">
<div class="h-full bg-tech-green w-[98%]"></div>
</div>
</div>
<button class="bg-tech-border hover:bg-tech-green hover:text-tech-dark text-tech-text border border-tech-border p-2 transition-all">
<span class="material-symbols-outlined text-[18px] block">play_arrow</span>
</button>
</div>
</div>
</div>
</div>
<div class="p-4">
<button class="w-full py-4 text-sm font-bold uppercase border border-tech-border bg-tech-dark text-tech-muted hover:text-tech-green hover:border-tech-green hover:bg-tech-panel transition-all">
                // Load More Logs
            </button>
</div>
</main>
<nav class="fixed bottom-0 left-0 right-0 z-30 max-w-md mx-auto border-x border-tech-border">
<div class="h-20 bg-tech-dark border-t border-tech-border grid grid-cols-4">
<button class="group flex flex-col items-center justify-center gap-2 hover:bg-tech-panel transition-colors border-r border-tech-border">
<span class="material-symbols-outlined text-tech-muted group-hover:text-tech-green transition-colors" style="font-size: 24px;">home</span>
<span class="text-[10px] font-bold uppercase text-tech-muted group-hover:text-tech-green tracking-widest">Home</span>
</button>
<button class="group flex flex-col items-center justify-center gap-2 hover:bg-tech-panel transition-colors border-r border-tech-border">
<span class="material-symbols-outlined text-tech-muted group-hover:text-tech-green transition-colors" style="font-size: 24px;">explore</span>
<span class="text-[10px] font-bold uppercase text-tech-muted group-hover:text-tech-green tracking-widest">Browse</span>
</button>
<button class="group flex flex-col items-center justify-center gap-2 bg-tech-panel transition-colors border-r border-tech-border relative">
<div class="absolute top-0 left-0 right-0 h-0.5 bg-tech-green"></div>
<span class="material-symbols-outlined text-tech-green" style="font-size: 24px; font-variation-settings: 'FILL' 1;">bookmarks</span>
<span class="text-[10px] font-bold uppercase text-tech-green tracking-widest">Lib</span>
</button>
<button class="group flex flex-col items-center justify-center gap-2 hover:bg-tech-panel transition-colors">
<span class="material-symbols-outlined text-tech-muted group-hover:text-tech-green transition-colors" style="font-size: 24px;">person</span>
<span class="text-[10px] font-bold uppercase text-tech-muted group-hover:text-tech-green tracking-widest">User</span>
</button>
</div>
</nav>
</div>

</body></html>