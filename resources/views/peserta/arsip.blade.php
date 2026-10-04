<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-dvh">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Arsip Tugas - METASTRO 2026</title>

    <!-- Anti-FOUC Theme Script -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700;800&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .font-poppins { font-family: 'Poppins', sans-serif; }
        .font-oswald-header { font-family: 'Oswald', sans-serif; letter-spacing: 0.12em; }
    </style>
</head>
<body class="font-poppins antialiased bg-[#FFFDF9] dark:bg-slate-900 text-slate-900 dark:text-slate-100 min-h-dvh transition-colors duration-200"
    x-data="{
        mobileMenu: false,
        notifOpen: false,
        darkMode: document.documentElement.classList.contains('dark'),
        toggleTheme() {
            this.darkMode = !this.darkMode;
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            }
        }
    }">

    <div class="max-w-md mx-auto sm:max-w-xl md:max-w-2xl px-4 py-5 min-h-screen flex flex-col justify-between">
        <div>
            <!-- TOP HEADER APP BAR (NAVBAR SELALU ADA) -->
            @include('peserta.partials.navbar')

            <!-- PAGE HEADER -->
            <div class="flex items-center gap-3 py-2 mb-4">
                <a href="{{ route('peserta.dashboard') }}" class="p-2 -ml-2 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" title="Kembali ke Dashboard">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                <h2 class="font-bold text-lg text-slate-900 dark:text-white">Arsip Tugas</h2>
            </div>

            <!-- CONTENT -->
            <div>
                <h3 class="font-bold text-base text-slate-900 dark:text-white mb-4">
                    Day 1 telah terkumpul: <span class="text-[#00A82D]">25%</span>
                </h3>

                <div class="space-y-4">
                    <div class="bg-white dark:bg-slate-800/90 border border-slate-100 dark:border-slate-700/60 rounded-2xl p-5 shadow-xs">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="bg-[#FF1A1A] text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full">Individu</span>
                            <h4 class="font-bold text-sm text-slate-800 dark:text-white">Membuat esai</h4>
                        </div>
                        
                        <div class="text-xs text-slate-600 dark:text-slate-400 space-y-1 mb-4">
                            <p><span class="font-medium text-slate-700 dark:text-slate-300">Deadline tugas:</span> 20 Oktober 2026 23:59</p>
                            <p><span class="font-medium text-slate-700 dark:text-slate-300">Dikumpulkan:</span> 20 Oktober 2026 18:35</p>
                        </div>

                        <div class="flex justify-center">
                            <a href="{{ route('peserta.tugas-kumpulkan') }}" class="bg-[#FFF2CC] dark:bg-amber-400 hover:bg-[#FFE599] dark:hover:bg-amber-300 text-slate-800 font-bold text-xs px-8 py-2 rounded-xl transition shadow-2xs">
                                Lihat file
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER BRANDING -->
        <footer class="text-center py-4 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-400 mt-8">
            &copy; 2026 METASTRO &bull; Spirit of HIRO, Heart of SOLDER
        </footer>
    </div>
</body>
</html>
