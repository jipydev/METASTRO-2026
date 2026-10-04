<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-dvh">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kumpulkan Tugas - METASTRO 2026</title>

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
        agreed: false,
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
                <a href="{{ route('peserta.tugas-list') }}" class="p-2 -ml-2 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" title="Kembali ke List Penugasan">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                <h2 class="font-bold text-lg text-slate-900 dark:text-white">Kumpulkan Tugas</h2>
            </div>

            <!-- CONTENT CARD -->
            <div class="bg-white dark:bg-slate-800/90 rounded-2xl p-5 sm:p-6 shadow-xs border border-slate-100 dark:border-slate-700/60">
                <p class="text-right text-xs italic text-slate-500 dark:text-slate-400 mb-4">
                    Deadline tugas: 21 Oktober pukul 23.59
                </p>

                <div class="flex items-center gap-2 mb-4">
                    <span class="bg-[#FF1A1A] text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full">Individu</span>
                    <h3 class="font-bold text-base text-slate-800 dark:text-white">Membuat esai</h3>
                </div>

                <div class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed mb-6 space-y-3">
                    <p class="font-semibold text-slate-700 dark:text-slate-200">
                        Deskripsi:<br>
                        <span class="font-normal text-slate-600 dark:text-slate-400">
                            Setelah mendengarkan pemateri di hari pertama, peserta menulis esai terkait seluruh materi dengan ketentuan:
                        </span>
                    </p>
                    
                    <ol class="list-decimal pl-4 space-y-1 text-slate-600 dark:text-slate-400">
                        <li>Maksimal 600 kata</li>
                        <li>Tidak boleh AI generated (plagiarisme &lt; 15%)</li>
                        <li>Font Times New Roman ukuran 12</li>
                        <li>Spacing sebanyak 1,5 line</li>
                    </ol>

                    <p class="text-slate-500 dark:text-slate-400 pt-2">
                        Pastikan file sudah sesuai dengan format yang telah ditentukan sebelum mengunggah. File yang sudah dikumpulkan tidak dapat dibatalkan setelah tenggat waktu berakhir.
                    </p>
                </div>

                <!-- ACTION AREA -->
                <div class="mt-8 flex flex-col items-center pt-4 border-t border-slate-100 dark:border-slate-700/60">
                    <label class="flex items-start gap-2.5 cursor-pointer mb-5 max-w-[320px] select-none">
                        <input type="checkbox" x-model="agreed" class="mt-0.5 w-4 h-4 rounded border-red-400 text-red-500 focus:ring-red-500 cursor-pointer">
                        <span class="text-[10px] text-red-500 dark:text-red-400 leading-tight">
                            Saya telah membaca deskripsi tugas dengan baik dan mengerjakan tugas sesuai dengan ketentuan.
                        </span>
                    </label>

                    <button type="button" :disabled="!agreed"
                        :class="agreed ? 'bg-[#FF5B00] hover:bg-[#e05000] text-white cursor-pointer shadow-md' : 'bg-slate-300 dark:bg-slate-700 text-slate-500 dark:text-slate-400 cursor-not-allowed'"
                        class="text-xs font-bold px-8 py-3 rounded-xl transition w-full sm:w-auto text-center">
                        Kumpulkan Tugas Disini
                    </button>
                    <p class="text-[10px] italic text-slate-400 dark:text-slate-500 mt-2.5">Upload file didukung: PDF (Maks. 5MB).</p>
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
