<!DOCTYPE html>
<html class="dark" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Manga App Search</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#4CFF00", // Neon Green
                        "background-dark": "#050505", // Deep Black
                        "surface-dark": "#111111", // Off-black
                        "text-light": "#E0E0E0",
                        "border-color": "#333333"
                    },
                    fontFamily: {
                        "mono": ["'Space Mono'", "monospace"]
                    },
                    borderRadius: {
                        "none": "0px",
                        DEFAULT: "0px",
                    },
                    boxShadow: {
                        'brutalist': '4px 4px 0px 0px #4CFF00',
                    }
                },
            },
        }
    </script>
<style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
<style>
    body {
      min-height: max(884px, 100dvh);
    }
  </style>
  </head>
<body class="bg-background-dark font-mono text-text-light antialiased min-h-screen flex flex-col selection:bg-primary selection:text-black">
<div class="relative flex h-full w-full max-w-md mx-auto flex-col overflow-hidden border-x border-border-color bg-background-dark flex-1">
<header class="sticky top-0 z-30 bg-background-dark border-b border-border-color p-4 flex flex-col gap-4">
<div class="flex items-center justify-between">
<h1 class="text-xl font-bold tracking-tighter text-primary uppercase flex items-center gap-2">
<span class="w-2 h-4 bg-primary inline-block"></span>
                SEARCH_INDEX
            </h1>
<div class="flex gap-2">
<button class="size-8 flex items-center justify-center border border-border-color hover:bg-primary hover:text-black hover:border-primary transition-all active:translate-y-0.5">
<span class="material-symbols-outlined text-lg">tune</span>
</button>
</div>
</div>
<div class="relative w-full group">
<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
<span class="material-symbols-outlined text-zinc-600 group-focus-within:text-primary transition-colors">terminal</span>
</div>
<input class="block w-full h-12 bg-surface-dark border border-border-color focus:border-primary px-4 pl-10 text-sm text-text-light placeholder-zinc-600 outline-none transition-colors uppercase font-bold focus:shadow-[4px_4px_0_0_#333]" placeholder="INPUT KEYWORDS..." type="text"/>
<div class="absolute inset-y-0 right-0 pr-2 flex items-center">
<button class="size-8 bg-zinc-900 border border-zinc-700 text-primary flex items-center justify-center hover:bg-primary hover:text-black transition-colors">
<span class="material-symbols-outlined text-lg">arrow_forward</span>
</button>
</div>
</div>
<div class="flex gap-2 overflow-x-auto no-scrollbar pb-1">
<button class="shrink-0 h-8 px-4 border border-primary bg-primary text-black text-xs font-bold uppercase shadow-[2px_2px_0_0_rgba(255,255,255,0.2)]">ALL</button>
<button class="shrink-0 h-8 px-4 border border-border-color bg-transparent text-zinc-400 hover:border-primary hover:text-primary text-xs font-bold uppercase transition-colors">MANGA</button>
<button class="shrink-0 h-8 px-4 border border-border-color bg-transparent text-zinc-400 hover:border-primary hover:text-primary text-xs font-bold uppercase transition-colors">MANHWA</button>
<button class="shrink-0 h-8 px-4 border border-border-color bg-transparent text-zinc-400 hover:border-primary hover:text-primary text-xs font-bold uppercase transition-colors">COMPLETED</button>
<button class="shrink-0 h-8 px-4 border border-border-color bg-transparent text-zinc-400 hover:border-primary hover:text-primary text-xs font-bold uppercase transition-colors">ONESHOT</button>
</div>
</header>
<main class="flex-1 overflow-y-auto no-scrollbar pb-24 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImEiIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTTAgNDBoNDBWMEgwdi4yaDQwdjM5LjhIMHoiIGZpbGw9IiMzMzMiIGZpbGwtb3BhY2l0eT0iMC4wNSIvPjwvcGF0dGVybj48L2RlZnM+PHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0idXJsKCNhKSIvPjwvc3ZnPg==')]">
<section class="border-b border-border-color bg-background-dark">
<div class="px-4 py-3 border-b border-border-color flex justify-between items-center bg-surface-dark">
<h2 class="text-[10px] font-bold text-primary uppercase tracking-widest flex items-center gap-2">
<span class="material-symbols-outlined text-[12px]">history</span>
                    RECENT_LOGS
                </h2>
<button class="text-[10px] text-zinc-500 hover:text-primary uppercase border border-transparent hover:border-zinc-700 px-2 py-0.5 transition-all">CLEAR_CACHE</button>
</div>
<div class="flex flex-col">
<button class="flex items-center justify-between w-full px-4 py-4 border-b border-border-color hover:bg-surface-dark group text-left transition-colors">
<span class="text-xs font-bold text-zinc-400 group-hover:text-primary uppercase tracking-wide">
<span class="text-zinc-600 mr-3">&gt;</span>CHAINSAW MAN
                    </span>
<span class="material-symbols-outlined text-zinc-700 group-hover:text-primary text-sm -rotate-45 group-hover:rotate-0 transition-transform">arrow_forward</span>
</button>
<button class="flex items-center justify-between w-full px-4 py-4 border-b border-border-color hover:bg-surface-dark group text-left transition-colors">
<span class="text-xs font-bold text-zinc-400 group-hover:text-primary uppercase tracking-wide">
<span class="text-zinc-600 mr-3">&gt;</span>ONE PIECE
                    </span>
<span class="material-symbols-outlined text-zinc-700 group-hover:text-primary text-sm -rotate-45 group-hover:rotate-0 transition-transform">arrow_forward</span>
</button>
</div>
</section>
<section class="mt-8">
<div class="px-4 mb-4 flex items-end justify-between">
<h2 class="text-xl font-bold text-text-light uppercase leading-none border-l-4 border-primary pl-3">
<span class="block text-[10px] text-zinc-500 mb-1 font-normal">Algorithm_Results</span>
                    TRENDING_NODES
                </h2>
</div>
<div class="flex flex-col gap-4 px-4">
<div class="flex border border-border-color bg-background-dark group cursor-pointer hover:border-primary transition-all relative shadow-[4px_4px_0_0_#1a1a1a] hover:shadow-[4px_4px_0_0_#4CFF00]">
<div class="w-28 shrink-0 relative border-r border-border-color">
<div class="absolute inset-0 bg-cover bg-center grayscale group-hover:grayscale-0 transition-all duration-300" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBi1DW4Nbcv_qxuuXKEIO9ac0d4P9mkgGooxyAbK2WmcXA5dlFT4O6DKuk7X4eZWgJooiEz1V7S-no82oLLD03C2QoU7FiS189mwZFUeCsxGM2wGpkXQz-XmX1gB2vaP1DPYAOOKxKCR1jLnL7KUMWxbmcFZ-QOK0swXhRMEoZQDJAD7TSO2yQemk1FPx7t6kIzRA6UcvzHuhpBOJbVjpVzz5uimgAmdmxljxi2DjVG1E5hvuSv-Li34jwwksv8N2qS5Pa_jPsJxaI");'></div>
<div class="absolute inset-0 bg-black/20 group-hover:bg-transparent transition-colors"></div>
<div class="absolute top-0 left-0 bg-primary text-black text-[10px] font-bold px-1.5 py-0.5 border-b border-r border-black">#01</div>
</div>
<div class="flex flex-col justify-between flex-1 p-3">
<div>
<div class="flex justify-between items-start mb-1">
<h3 class="text-sm font-bold text-text-light uppercase group-hover:text-primary truncate pr-2">ONE PIECE</h3>
<span class="text-[10px] font-bold text-primary border border-primary px-1">9.8</span>
</div>
<p class="text-[10px] text-zinc-500 line-clamp-2 mb-2 uppercase">Pirates • Adventure • Action</p>
</div>
<div class="flex items-center gap-2 mt-2">
<button class="flex-1 py-1.5 border border-zinc-700 text-[10px] font-bold text-zinc-400 hover:bg-zinc-800 hover:text-white transition-colors uppercase">
                                Read
                             </button>
<button class="size-7 flex items-center justify-center border border-zinc-700 text-zinc-400 hover:bg-primary hover:text-black hover:border-primary transition-colors">
<span class="material-symbols-outlined text-[16px]">add</span>
</button>
</div>
</div>
</div>
<div class="flex border border-border-color bg-background-dark group cursor-pointer hover:border-primary transition-all relative shadow-[4px_4px_0_0_#1a1a1a] hover:shadow-[4px_4px_0_0_#4CFF00]">
<div class="w-28 shrink-0 relative border-r border-border-color">
<div class="absolute inset-0 bg-cover bg-center grayscale group-hover:grayscale-0 transition-all duration-300" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuADzbhCv9pSoqAqE8jPB4-RLSGMVyLfLfaTG6vUmTlH_P7mnUfHHlCIcrCz5bk2f6PLiJB5V0YQ-VLeHLz_s5RLYjEGMtOIsknpLtBZid-CAMX8pdu7UkHiqOwyzyRlWtC8f5IOdst2njKsb69UEAIYgUkn2HwmJ5OxcLUxdzj2yOg4ESs5D3D4r02mVxefNEDesTbXwLM1QQt1sLwVp8TI6wPwcmL4mOxWOC_crhVdIzAN-E04W41w2KZ3Nre9bhggk3MQpc8m2Bs");'></div>
<div class="absolute top-0 left-0 bg-zinc-800 text-zinc-300 text-[10px] font-bold px-1.5 py-0.5 border-b border-r border-black">#02</div>
</div>
<div class="flex flex-col justify-between flex-1 p-3">
<div>
<div class="flex justify-between items-start mb-1">
<h3 class="text-sm font-bold text-text-light uppercase group-hover:text-primary truncate pr-2">CHAINSAW MAN</h3>
<span class="text-[10px] font-bold text-zinc-500 border border-zinc-700 px-1 group-hover:text-primary group-hover:border-primary transition-colors">9.5</span>
</div>
<p class="text-[10px] text-zinc-500 line-clamp-2 mb-2 uppercase">Dark Fantasy • Gore • Demons</p>
</div>
<div class="flex items-center gap-2 mt-2">
<button class="flex-1 py-1.5 border border-zinc-700 text-[10px] font-bold text-zinc-400 hover:bg-zinc-800 hover:text-white transition-colors uppercase">
                                Read
                             </button>
<button class="size-7 flex items-center justify-center border border-zinc-700 text-zinc-400 hover:bg-primary hover:text-black hover:border-primary transition-colors">
<span class="material-symbols-outlined text-[16px]">add</span>
</button>
</div>
</div>
</div>
<div class="flex border border-border-color bg-background-dark group cursor-pointer hover:border-primary transition-all relative shadow-[4px_4px_0_0_#1a1a1a] hover:shadow-[4px_4px_0_0_#4CFF00]">
<div class="w-28 shrink-0 relative border-r border-border-color">
<div class="absolute inset-0 bg-cover bg-center grayscale group-hover:grayscale-0 transition-all duration-300" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDQhvePW49Fsq4MlbcW6Mz5u64_QINmF_gAGU6FT9TaPTGz-jQ_gKcCvbOU3F_1zvayWRY9yD2UsW7Q3dXyksVOona4bM5TSedaI-oBpsV-D8xccZcHvQUFZQY3LdEttg1KR0ky-qWj58iiPGbeFuoc7IbL9-WrhtJv7p7lroMAuCJyJvIwFlnYCg8Z4vMe3ifN95RnaDI3fvHA9MpybL0EHTKrjFiRdHevZhW16LthMHQPw_eH-aEEiuXww0OZMs7uU46953sMbHo");'></div>
<div class="absolute top-0 left-0 bg-zinc-800 text-zinc-300 text-[10px] font-bold px-1.5 py-0.5 border-b border-r border-black">#03</div>
</div>
<div class="flex flex-col justify-between flex-1 p-3">
<div>
<div class="flex justify-between items-start mb-1">
<h3 class="text-sm font-bold text-text-light uppercase group-hover:text-primary truncate pr-2">JUJUTSU KAISEN</h3>
<span class="text-[10px] font-bold text-zinc-500 border border-zinc-700 px-1 group-hover:text-primary group-hover:border-primary transition-colors">9.4</span>
</div>
<p class="text-[10px] text-zinc-500 line-clamp-2 mb-2 uppercase">Supernatural • School • Curse</p>
</div>
<div class="flex items-center gap-2 mt-2">
<button class="flex-1 py-1.5 border border-zinc-700 text-[10px] font-bold text-zinc-400 hover:bg-zinc-800 hover:text-white transition-colors uppercase">
                                Read
                             </button>
<button class="size-7 flex items-center justify-center border border-zinc-700 text-zinc-400 hover:bg-primary hover:text-black hover:border-primary transition-colors">
<span class="material-symbols-outlined text-[16px]">add</span>
</button>
</div>
</div>
</div>
</div>
</section>
<section class="mt-8 px-4">
<div class="mb-4 pt-4 border-t border-dashed border-border-color">
<h2 class="text-sm font-bold text-zinc-500 uppercase">
                    BROWSE_BY_TAG
                </h2>
</div>
<div class="grid grid-cols-2 gap-3">
<button class="h-12 border border-border-color bg-surface-dark hover:border-primary hover:bg-primary hover:text-black flex items-center justify-between px-4 group transition-all">
<span class="font-bold text-xs uppercase">Action</span>
<span class="material-symbols-outlined text-zinc-600 group-hover:text-black text-sm">east</span>
</button>
<button class="h-12 border border-border-color bg-surface-dark hover:border-primary hover:bg-primary hover:text-black flex items-center justify-between px-4 group transition-all">
<span class="font-bold text-xs uppercase">Romance</span>
<span class="material-symbols-outlined text-zinc-600 group-hover:text-black text-sm">east</span>
</button>
<button class="h-12 border border-border-color bg-surface-dark hover:border-primary hover:bg-primary hover:text-black flex items-center justify-between px-4 group transition-all">
<span class="font-bold text-xs uppercase">Sci-Fi</span>
<span class="material-symbols-outlined text-zinc-600 group-hover:text-black text-sm">east</span>
</button>
<button class="h-12 border border-border-color bg-surface-dark hover:border-primary hover:bg-primary hover:text-black flex items-center justify-between px-4 group transition-all">
<span class="font-bold text-xs uppercase">Horror</span>
<span class="material-symbols-outlined text-zinc-600 group-hover:text-black text-sm">east</span>
</button>
</div>
</section>
</main>
<nav class="absolute bottom-0 left-0 right-0 z-40 border-t border-border-color bg-background-dark/95 backdrop-blur-sm">
<div class="h-16 flex items-center justify-around px-2">
<button class="flex flex-col items-center justify-center w-14 h-full gap-1 text-zinc-500 hover:text-text-light transition-colors group">
<span class="material-symbols-outlined group-hover:-translate-y-1 transition-transform text-[22px]">home</span>
<span class="text-[9px] font-bold uppercase tracking-wider">Home</span>
</button>
<button class="flex flex-col items-center justify-center w-14 h-full gap-1 text-primary relative bg-surface-dark border-x border-border-color">
<div class="absolute inset-x-0 top-0 h-[2px] bg-primary shadow-[0_0_8px_rgba(76,255,0,0.8)]"></div>
<span class="material-symbols-outlined text-[22px]">manage_search</span>
<span class="text-[9px] font-bold uppercase tracking-wider">Search</span>
</button>
<button class="flex flex-col items-center justify-center w-14 h-full gap-1 text-zinc-500 hover:text-text-light transition-colors group">
<span class="material-symbols-outlined group-hover:-translate-y-1 transition-transform text-[22px]">bookmarks</span>
<span class="text-[9px] font-bold uppercase tracking-wider">Lib</span>
</button>
<button class="flex flex-col items-center justify-center w-14 h-full gap-1 text-zinc-500 hover:text-text-light transition-colors group">
<span class="material-symbols-outlined group-hover:-translate-y-1 transition-transform text-[22px]">person</span>
<span class="text-[9px] font-bold uppercase tracking-wider">Me</span>
</button>
</div>
</nav>
<div class="fixed inset-0 pointer-events-none z-50 opacity-[0.03] bg-[linear-gradient(rgba(18,16,16,0)_50%,rgba(0,0,0,0.25)_50%),linear-gradient(90deg,rgba(255,0,0,0.06),rgba(0,255,0,0.02),rgba(0,0,255,0.06))] bg-[length:100%_2px,3px_100%]"></div>
</div>

</body></html>