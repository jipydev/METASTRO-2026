<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-dvh">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>List Penugasan Hari - METASTRO 2026</title>

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
            <div class="flex items-center gap-3 py-2 mb-3">
                <a href="{{ route('peserta.dashboard') }}" class="p-2 -ml-2 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" title="Kembali ke Dashboard">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                <h2 class="font-bold text-lg text-slate-900 dark:text-white">Pilih Hari Penugasan:</h2>
            </div>

            <!-- CONTENT -->
            <div class="space-y-4">
                @php
                    $days = [
                        ['day' => 1, 'progress' => 3, 'max' => 5],
                        ['day' => 2, 'progress' => 2, 'max' => 5],
                        ['day' => 3, 'progress' => 3, 'max' => 5],
                        ['day' => 4, 'progress' => 4, 'max' => 5],
                    ];
                @endphp

                @foreach($days as $item)
                <a href="{{ route('peserta.tugas-list') }}" class="block bg-white dark:bg-slate-800/90 rounded-2xl p-5 shadow-xs border border-slate-100 dark:border-slate-700/60 hover:border-[#FF5B00]/40 dark:hover:border-orange-500/40 hover:shadow-md transition active:scale-[0.99] group">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-bold text-base sm:text-lg text-slate-900 dark:text-white group-hover:text-[#FF5B00] transition-colors">
                            DAY {{ $item['day'] }}
                        </h3>
                        <svg class="w-5 h-5 text-slate-600 dark:text-slate-300 group-hover:translate-x-1 group-hover:text-[#FF5B00] transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                        </svg>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-2 font-medium">Total tugas: {{ $item['progress'] }} / {{ $item['max'] }} selesai</p>
                    
                    <div class="relative w-full">
                        <div class="h-2.5 w-full bg-slate-200 dark:bg-slate-700 rounded-full flex overflow-hidden">
                            <div class="h-full bg-[#FF5B00] rounded-full transition-all duration-500" style="width: {{ ($item['progress'] / $item['max']) * 100 }}%"></div>
                        </div>
                        <div class="flex justify-between mt-1.5 px-1 text-[10px] text-slate-400 dark:text-slate-500 font-medium">
                            <span>1</span>
                            <span>2</span>
                            <span>3</span>
                            <span>4</span>
                            <span>5</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>

        <!-- FOOTER BRANDING -->
        <footer class="text-center py-4 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-400 mt-8">
            &copy; 2026 METASTRO &bull; Spirit of HIRO, Heart of SOLDER
        </footer>
    </div>
</body>
</html>
