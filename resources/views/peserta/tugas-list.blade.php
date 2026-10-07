<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-dvh">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>List Penugasan Detail - METASTRO 2026</title>

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
<body class="font-poppins antialiased bg-[#FFFDF9] dark:bg-slate-900 text-[#1E293B] dark:text-slate-100 min-h-dvh transition-colors duration-200"
    x-data="{
        tab: 'semua',
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
                <h2 class="font-bold text-lg text-[#1E293B] dark:text-white">List Penugasan:</h2>
            </div>

            <!-- SORTING & FILTERS -->
            <div class="mb-5">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-xs text-slate-500 dark:text-slate-400">Sorting berdasarkan:</span>
                    <button type="button" class="bg-[#FF7A00] hover:bg-[#e06c00] text-white text-xs font-semibold px-3 py-1 rounded-lg flex items-center gap-1.5 transition shadow-2xs">
                        deadline
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </button>
                </div>

                <!-- TABS -->
                <div class="flex gap-2 text-xs font-semibold overflow-x-auto pb-1 scrollbar-hide">
                    <button type="button" @click="tab = 'semua'" :class="tab == 'semua' ? 'bg-[#006C7F] text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200'" class="px-4 py-2 rounded-xl whitespace-nowrap transition cursor-pointer">Semua</button>
                    <button type="button" @click="tab = 'individu'" :class="tab == 'individu' ? 'bg-[#006C7F] text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200'" class="px-4 py-2 rounded-xl whitespace-nowrap transition cursor-pointer">Individu</button>
                    <button type="button" @click="tab = 'regu'" :class="tab == 'regu' ? 'bg-[#006C7F] text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200'" class="px-4 py-2 rounded-xl whitespace-nowrap transition cursor-pointer">Regu</button>
                    <button type="button" @click="tab = 'angkatan'" :class="tab == 'angkatan' ? 'bg-[#006C7F] text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200'" class="px-4 py-2 rounded-xl whitespace-nowrap transition cursor-pointer">Angkatan</button>
                </div>
            </div>

            <!-- TASK LIST -->
            <div class="space-y-4">
                <!-- Individu 1 -->
                <div x-show="tab == 'semua' || tab == 'individu'" class="bg-white dark:bg-slate-800/90 border border-slate-100 dark:border-slate-700/60 rounded-2xl p-5 shadow-xs">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="bg-[#FF1A1A] text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full">Individu</span>
                        <h3 class="font-bold text-sm text-[#1E293B] dark:text-white">Membuat esai</h3>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
                        Setelah mendengarkan pemateri di hari pertama, peserta menulis esai terkait seluruh materi dengan ketentuan: maksimal 600 kata, tidak boleh AI...... &gt;&gt;
                    </p>
                    <div class="flex justify-center">
                        <a href="{{ route('peserta.tugas-kumpulkan') }}" class="bg-[#FFF2CC] dark:bg-amber-400 hover:bg-[#FFE599] dark:hover:bg-amber-300 text-[#1E293B] font-bold text-xs px-8 py-2 rounded-xl transition shadow-2xs inline-block">Kumpulkan</a>
                    </div>
                </div>

                <!-- Regu 1 -->
                <div x-show="tab == 'semua' || tab == 'regu'" class="bg-white dark:bg-slate-800/90 border border-slate-100 dark:border-slate-700/60 rounded-2xl p-5 shadow-xs">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="bg-[#4299E1] text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full">Regu</span>
                        <h3 class="font-bold text-sm text-[#1E293B] dark:text-white">Video yel-yel regu</h3>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
                        Setiap regu wajib membuat rekaman video yel-yel kekompakan regu masing-masing berdurasi maksimal 2 menit dengan semangat tinggi...... &gt;&gt;
                    </p>
                    <div class="flex justify-center">
                        <a href="{{ route('peserta.tugas-kumpulkan') }}" class="bg-[#FFF2CC] dark:bg-amber-400 hover:bg-[#FFE599] dark:hover:bg-amber-300 text-[#1E293B] font-bold text-xs px-8 py-2 rounded-xl transition shadow-2xs inline-block">Kumpulkan</a>
                    </div>
                </div>

                <!-- Regu 2 -->
                <div x-show="tab == 'semua' || tab == 'regu'" class="bg-white dark:bg-slate-800/90 border border-slate-100 dark:border-slate-700/60 rounded-2xl p-5 shadow-xs">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="bg-[#4299E1] text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full">Regu</span>
                        <h3 class="font-bold text-sm text-[#1E293B] dark:text-white">Bendera dengan tiang</h3>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
                        Membuat identitas bendera regu berukuran 60x40 cm dengan tiang bambu/kayu yang rapi dan kuat untuk dibawa selama kegiatan...... &gt;&gt;
                    </p>
                    <div class="flex justify-center">
                        <a href="{{ route('peserta.tugas-kumpulkan') }}" class="bg-[#FFF2CC] dark:bg-amber-400 hover:bg-[#FFE599] dark:hover:bg-amber-300 text-[#1E293B] font-bold text-xs px-8 py-2 rounded-xl transition shadow-2xs inline-block">Kumpulkan</a>
                    </div>
                </div>

                <!-- Angkatan -->
                <div x-show="tab == 'semua' || tab == 'angkatan'" class="bg-white dark:bg-slate-800/90 border border-slate-100 dark:border-slate-700/60 rounded-2xl p-5 shadow-xs">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="bg-emerald-500 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full">Angkatan</span>
                        <h3 class="font-bold text-sm text-[#1E293B] dark:text-white">Video jargon MKB</h3>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
                        Video kolaboratif seluruh angkatan menampilkan jargon kebersamaan dan kekeluargaan METASTRO 2026...... &gt;&gt;
                    </p>
                    <div class="flex justify-center">
                        <a href="{{ route('peserta.tugas-kumpulkan') }}" class="bg-[#FFF2CC] dark:bg-amber-400 hover:bg-[#FFE599] dark:hover:bg-amber-300 text-[#1E293B] font-bold text-xs px-8 py-2 rounded-xl transition shadow-2xs inline-block">Kumpulkan</a>
                    </div>
                </div>
                
                <!-- Individu 2 -->
                <div x-show="tab == 'semua' || tab == 'individu'" class="bg-white dark:bg-slate-800/90 border border-slate-100 dark:border-slate-700/60 rounded-2xl p-5 shadow-xs">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="bg-[#FF1A1A] text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full">Individu</span>
                        <h3 class="font-bold text-sm text-[#1E293B] dark:text-white">Koneksi LinkedIn 150+</h3>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
                        Mengembangkan jejaring profesional dengan menambahkan koneksi LinkedIn minimal 150 koneksi aktif dan follow page himpunan...... &gt;&gt;
                    </p>
                    <div class="flex justify-center">
                        <a href="{{ route('peserta.tugas-kumpulkan') }}" class="bg-[#FFF2CC] dark:bg-amber-400 hover:bg-[#FFE599] dark:hover:bg-amber-300 text-[#1E293B] font-bold text-xs px-8 py-2 rounded-xl transition shadow-2xs inline-block">Kumpulkan</a>
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
