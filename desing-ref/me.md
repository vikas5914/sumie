<!DOCTYPE html>
<html class="dark" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>User Profile/Settings</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#4CFF00",
                        "background-dark": "#050505",
                        "surface-dark": "#121212",
                        "text-light": "#E0E0E0",
                    },
                    fontFamily: {
                        "mono": ["'Space Mono'", "monospace"]
                    },
                    borderRadius: {
                        "DEFAULT": "0px",
                        "none": "0px",
                        "sm": "0px",
                        "md": "0px",
                        "lg": "0px",
                        "xl": "0px",
                        "2xl": "0px",
                        "full": "0px",
                    },
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
<body class="bg-background-dark font-mono text-text-light antialiased selection:bg-primary selection:text-black">
<div class="relative flex h-full w-full max-w-md mx-auto flex-col overflow-hidden pb-20 border-x border-slate-800 bg-background-dark">
<header class="flex flex-col gap-0 sticky top-0 z-20 bg-background-dark/95 backdrop-blur-md border-b border-slate-800">
<div class="flex items-center justify-between px-4 py-2 bg-primary text-black text-[10px] font-bold uppercase tracking-widest">
<span>System_Ready</span>
<span>V.2.4.0</span>
</div>
<div class="p-5 flex flex-col gap-6">
<div class="flex items-start gap-4">
<div class="relative group cursor-pointer">
<div class="size-20 bg-center bg-no-repeat bg-cover border border-slate-600 grayscale hover:grayscale-0 transition-all duration-300" data-alt="User profile avatar with anime character" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDKIWh7R3m6byrcvwlY6mcmU9zKbBG_bIBWoqY5LZwrifsD28-9GNT7YWCG2diwAL5ry1w_JbimssL6xLUlBZU60hcAx2tRM6uqneOa5QEi8vLvPIPPlvjaR2wA9zgEHBJcDqThorjKEXzQ7hbGZHGsXbTZVa-ucRmJKBtqvuO_sTJQ6eYU0XJ0sNhKvmYbK_EqDRVex7fKwGHd3QxC3QZbMJoFsAn_3UKbLciTny1zHcVqKR_XDLXuOO9PcyJk1JEuOEUcJUuJcb8");'></div>
<div class="absolute -bottom-2 -right-2 bg-primary text-black text-[10px] font-bold px-1.5 py-0.5 border border-black shadow-[2px_2px_0px_0px_rgba(255,255,255,0.2)]">LVL.42</div>
</div>
<div class="flex-1 pt-1">
<h1 class="text-2xl font-bold leading-none text-white uppercase tracking-tighter mb-1">ALEX_READER</h1>
<p class="text-xs text-slate-500 font-bold uppercase tracking-wide mb-3">MEMBER_ID: #8842-XJ</p>
<div class="flex gap-2">
<span class="px-2 py-0.5 border border-primary text-primary text-[10px] font-bold uppercase hover:bg-primary hover:text-black transition-colors cursor-default">PREMIUM</span>
<span class="px-2 py-0.5 border border-slate-700 text-slate-400 text-[10px] font-bold uppercase">ONLINE</span>
</div>
</div>
</div>
</div>
<div class="grid grid-cols-3 border-t border-slate-800 divide-x divide-slate-800">
<div class="p-3 text-center hover:bg-surface-dark transition-colors cursor-pointer group">
<p class="text-[10px] text-slate-500 uppercase mb-1 group-hover:text-primary">READ</p>
<p class="text-lg font-bold text-white">842</p>
</div>
<div class="p-3 text-center hover:bg-surface-dark transition-colors cursor-pointer group">
<p class="text-[10px] text-slate-500 uppercase mb-1 group-hover:text-primary">HOURS</p>
<p class="text-lg font-bold text-white">128.5</p>
</div>
<div class="p-3 text-center hover:bg-surface-dark transition-colors cursor-pointer group">
<p class="text-[10px] text-slate-500 uppercase mb-1 group-hover:text-primary">LISTS</p>
<p class="text-lg font-bold text-white">14</p>
</div>
</div>
</header>
<main class="flex-1 flex flex-col overflow-y-auto no-scrollbar pt-6 px-4 gap-8">
<section>
<div class="flex items-center gap-2 mb-3">
<span class="material-symbols-outlined text-primary text-sm">terminal</span>
<h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest">ACCOUNT_CONFIGURATION</h2>
</div>
<div class="border border-slate-800 bg-surface-dark">
<button class="w-full flex items-center justify-between p-4 border-b border-slate-800 hover:bg-slate-800/50 transition-colors group text-left">
<div class="flex flex-col">
<span class="text-sm font-bold text-white uppercase group-hover:text-primary transition-colors">EDIT_PROFILE</span>
<span class="text-[10px] text-slate-500 uppercase">AVATAR, BIO, USERNAME</span>
</div>
<span class="material-symbols-outlined text-slate-600 group-hover:text-primary transition-colors">chevron_right</span>
</button>
<button class="w-full flex items-center justify-between p-4 border-b border-slate-800 hover:bg-slate-800/50 transition-colors group text-left">
<div class="flex flex-col">
<span class="text-sm font-bold text-white uppercase group-hover:text-primary transition-colors">SUBSCRIPTION</span>
<span class="text-[10px] text-primary uppercase">PLAN: ANIME_GOD_TIER</span>
</div>
<span class="material-symbols-outlined text-slate-600 group-hover:text-primary transition-colors">chevron_right</span>
</button>
<button class="w-full flex items-center justify-between p-4 hover:bg-slate-800/50 transition-colors group text-left">
<div class="flex flex-col">
<span class="text-sm font-bold text-white uppercase group-hover:text-primary transition-colors">SECURITY</span>
<span class="text-[10px] text-slate-500 uppercase">PASSWORD, 2FA</span>
</div>
<span class="material-symbols-outlined text-slate-600 group-hover:text-primary transition-colors">chevron_right</span>
</button>
</div>
</section>
<section>
<div class="flex items-center gap-2 mb-3">
<span class="material-symbols-outlined text-primary text-sm">settings_suggest</span>
<h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest">APP_PREFERENCES</h2>
</div>
<div class="border border-slate-800 bg-surface-dark flex flex-col">
<div class="flex items-center justify-between p-4 border-b border-slate-800">
<div class="flex flex-col gap-1">
<span class="text-sm font-bold text-white uppercase">DOWNLOADS_WIFI_ONLY</span>
</div>
<div class="relative inline-flex items-center cursor-pointer group">
<input checked="" class="sr-only peer" type="checkbox"/>
<div class="w-10 h-5 bg-slate-900 border border-slate-600 peer-focus:ring-0 peer-checked:border-primary peer-checked:bg-slate-900 transition-colors relative">
<div class="absolute top-0.5 left-0.5 w-3.5 h-3.5 bg-slate-500 peer-checked:bg-primary peer-checked:translate-x-5 transition-all duration-200"></div>
</div>
</div>
</div>
<div class="flex items-center justify-between p-4 border-b border-slate-800">
<div class="flex flex-col gap-1">
<span class="text-sm font-bold text-white uppercase">PUSH_NOTIFICATIONS</span>
</div>
<div class="relative inline-flex items-center cursor-pointer group">
<input class="sr-only peer" type="checkbox"/>
<div class="w-10 h-5 bg-slate-900 border border-slate-600 peer-focus:ring-0 peer-checked:border-primary peer-checked:bg-slate-900 transition-colors relative">
<div class="absolute top-0.5 left-0.5 w-3.5 h-3.5 bg-slate-500 peer-checked:bg-primary peer-checked:translate-x-5 transition-all duration-200"></div>
</div>
</div>
</div>
<button class="w-full flex items-center justify-between p-4 hover:bg-slate-800/50 transition-colors group text-left">
<div class="flex flex-col">
<span class="text-sm font-bold text-white uppercase group-hover:text-primary transition-colors">READING_MODE</span>
</div>
<div class="flex items-center gap-2">
<span class="text-xs font-bold text-slate-400 uppercase border border-slate-600 px-2 py-1">VERTICAL_SCROLL</span>
<span class="material-symbols-outlined text-slate-600 group-hover:text-primary transition-colors" style="font-size: 16px;">edit</span>
</div>
</button>
</div>
</section>
<section class="mb-6">
<button class="w-full group relative overflow-hidden bg-transparent border border-red-900/50 p-4 flex items-center justify-center gap-3 transition-all hover:bg-red-900/10 hover:border-red-500">
<span class="material-symbols-outlined text-red-700 group-hover:text-red-500 transition-colors">power_settings_new</span>
<span class="text-sm font-bold text-red-700 uppercase tracking-widest group-hover:text-red-500 transition-colors">TERMINATE_SESSION</span>
<div class="absolute top-0 left-0 w-1 h-full bg-red-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
</button>
<div class="mt-4 text-center">
<p class="text-[10px] text-slate-700 uppercase font-bold">BUILD_ID: 8993.221-ALPHA // SERVER: TOKYO_03</p>
</div>
</section>
</main>
<nav class="absolute bottom-0 left-0 right-0 px-4 pb-4 pt-2 z-30 pointer-events-none">
<div class="h-16 bg-surface-dark/95 backdrop-blur-xl shadow-[0_4px_16px_0_rgba(0,0,0,0.56)] flex items-center justify-around px-2 border border-slate-700 pointer-events-auto">
<button class="flex flex-col items-center justify-center w-14 h-full gap-1 text-slate-500 hover:text-text-light transition-colors group">
<span class="material-symbols-outlined group-hover:scale-110 transition-transform" style="font-size: 24px;">home</span>
<span class="text-[10px] font-bold uppercase">HOME</span>
</button>
<button class="flex flex-col items-center justify-center w-14 h-full gap-1 text-slate-500 hover:text-text-light transition-colors group">
<span class="material-symbols-outlined group-hover:scale-110 transition-transform" style="font-size: 24px;">explore</span>
<span class="text-[10px] font-bold uppercase">BROWSE</span>
</button>
<button class="flex flex-col items-center justify-center w-14 h-full gap-1 text-slate-500 hover:text-text-light transition-colors group">
<span class="material-symbols-outlined group-hover:scale-110 transition-transform" style="font-size: 24px;">bookmarks</span>
<span class="text-[10px] font-bold uppercase">LIBRARY</span>
</button>
<button class="flex flex-col items-center justify-center w-14 h-full gap-1 text-primary relative">
<span class="absolute top-0 inset-x-0 h-0.5 bg-primary"></span>
<span class="material-symbols-outlined" style="font-size: 24px; font-variation-settings: 'FILL' 1;">person</span>
<span class="text-[10px] font-bold uppercase">ME</span>
</button>
</div>
</nav>
<div class="fixed top-0 left-1/2 -translate-x-1/2 w-full h-[50vh] bg-primary/5 blur-[120px] rounded-none pointer-events-none -z-10 opacity-20"></div>
<div class="fixed inset-0 pointer-events-none z-0 opacity-[0.03]" style="background-image: linear-gradient(#4CFF00 1px, transparent 1px), linear-gradient(90deg, #4CFF00 1px, transparent 1px); background-size: 40px 40px;"></div>
</div>

</body></html>