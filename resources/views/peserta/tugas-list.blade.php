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
<body class="font-poppins antialiased bg-slate-50/60 dark:bg-slate-900 text-slate-800 dark:text-slate-100 min-h-dvh transition-colors duration-300"
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

    <div class="max-w-md mx-auto sm:max-w-xl md:max-w-2xl px-4 py-6 min-h-screen flex flex-col justify-between">
        <div>
            <!-- TOP HEADER APP BAR (NAVBAR SELALU ADA) -->
            @include('peserta.partials.navbar')

            <!-- PAGE HEADER -->
            <div class="flex items-center gap-3 py-3 mb-4">
                <a href="/dashboard" class="p-2 -ml-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-200/60 dark:hover:bg-slate-800 transition-colors" title="Kembali ke Dashboard">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                <div>
                    <h2 class="font-bold text-xl text-slate-900 dark:text-white tracking-tight">List Penugasan</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-normal">Pilih & selesaikan penugasan tepat waktu</p>
                </div>
            </div>

            <!-- SORTING & FILTERS -->
            <div class="mb-6 space-y-3">
                <div class="flex items-center justify-between bg-white dark:bg-slate-800/80 p-2.5 px-3.5 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 shadow-xs">
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Urutkan</span>
                    <button type="button" class="bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 hover:bg-amber-500/20 text-xs font-semibold px-3 py-1.5 rounded-xl flex items-center gap-1.5 transition-colors cursor-pointer">
                        Deadline Terdekat
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </button>
                </div>

                <!-- TABS FILTER -->
                <div class="flex gap-1.5 p-1.5 bg-slate-200/50 dark:bg-slate-800/60 rounded-2xl text-xs font-medium overflow-x-auto scrollbar-hide border border-slate-200/40 dark:border-slate-700/40">
                    <button type="button" x-on:click="tab = 'semua'" :class="tab == 'semua' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-xs font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'" class="flex-1 py-2 px-3 rounded-xl whitespace-nowrap transition-all duration-200 text-center cursor-pointer">Semua</button>
                    <button type="button" x-on:click="tab = 'individu'" :class="tab == 'individu' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-xs font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'" class="flex-1 py-2 px-3 rounded-xl whitespace-nowrap transition-all duration-200 text-center cursor-pointer">Individu</button>
                    <button type="button" x-on:click="tab = 'regu'" :class="tab == 'regu' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-xs font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'" class="flex-1 py-2 px-3 rounded-xl whitespace-nowrap transition-all duration-200 text-center cursor-pointer">Regu</button>
                    <button type="button" x-on:click="tab = 'angkatan'" :class="tab == 'angkatan' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-xs font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'" class="flex-1 py-2 px-3 rounded-xl whitespace-nowrap transition-all duration-200 text-center cursor-pointer">Angkatan</button>
                </div>
            </div>

            <!-- TASK LIST -->
            <div class="space-y-3.5">
                <!-- Individu 1 -->
                <div x-show="tab == 'semua' || tab == 'individu'" x-transition.opacity.duration.200ms class="group bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 hover:border-slate-300 dark:hover:border-slate-600 rounded-2xl p-5 shadow-xs transition-all duration-200">
                    <div class="flex items-center justify-between gap-2 mb-2.5">
                        <h3 class="font-bold text-base text-slate-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">Membuat esai</h3>
                        <span class="bg-rose-500/10 text-rose-600 dark:text-rose-400 text-[11px] font-semibold px-2.5 py-0.5 rounded-full shrink-0 border border-rose-500/20">Individu</span>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-4 line-clamp-2">
                        Setelah mendengarkan pemateri di hari pertama, peserta menulis esai terkait seluruh materi dengan ketentuan: maksimal 600 kata, tidak boleh AI.
                    </p>
                    <div class="flex items-center justify-end pt-2 border-t border-slate-100 dark:border-slate-700/50">
                        <a href="{{ route('peserta.tugas-kumpulkan') }}" class="inline-flex items-center gap-1.5 bg-orange-500 dark:bg-amber-400 hover:bg-slate-800 dark:hover:bg-amber-300 text-white dark:text-slate-950 font-semibold text-xs px-5 py-2 rounded-xl transition-all shadow-xs active:scale-95 cursor-pointer">
                            Kumpulkan
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Regu 1 -->
                <div x-show="tab == 'semua' || tab == 'regu'" x-transition.opacity.duration.200ms class="group bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 hover:border-slate-300 dark:hover:border-slate-600 rounded-2xl p-5 shadow-xs transition-all duration-200">
                    <div class="flex items-center justify-between gap-2 mb-2.5">
                        <h3 class="font-bold text-base text-slate-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">Video yel-yel regu</h3>
                        <span class="bg-sky-500/10 text-sky-600 dark:text-sky-400 text-[11px] font-semibold px-2.5 py-0.5 rounded-full shrink-0 border border-sky-500/20">Regu</span>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-4 line-clamp-2">
                        Setiap regu wajib membuat rekaman video yel-yel kekompakan regu masing-masing berdurasi maksimal 2 menit dengan semangat tinggi.
                    </p>
                    <div class="flex items-center justify-end pt-2 border-t border-slate-100 dark:border-slate-700/50">
                        <a href="{{ route('peserta.tugas-kumpulkan') }}" class="inline-flex items-center gap-1.5 bg-slate-900 dark:bg-amber-400 hover:bg-slate-800 dark:hover:bg-amber-300 text-white dark:text-slate-950 font-semibold text-xs px-5 py-2 rounded-xl transition-all shadow-xs active:scale-95 cursor-pointer">
                            Kumpulkan
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Regu 2 -->
                <div x-show="tab == 'semua' || tab == 'regu'" x-transition.opacity.duration.200ms class="group bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 hover:border-slate-300 dark:hover:border-slate-600 rounded-2xl p-5 shadow-xs transition-all duration-200">
                    <div class="flex items-center justify-between gap-2 mb-2.5">
                        <h3 class="font-bold text-base text-slate-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">Bendera dengan tiang</h3>
                        <span class="bg-sky-500/10 text-sky-600 dark:text-sky-400 text-[11px] font-semibold px-2.5 py-0.5 rounded-full shrink-0 border border-sky-500/20">Regu</span>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-4 line-clamp-2">
                        Membuat identitas bendera regu berukuran 60x40 cm dengan tiang bambu/kayu yang rapi dan kuat untuk dibawa selama kegiatan.
                    </p>
                    <div class="flex items-center justify-end pt-2 border-t border-slate-100 dark:border-slate-700/50">
                        <a href="{{ route('peserta.tugas-kumpulkan') }}" class="inline-flex items-center gap-1.5 bg-slate-900 dark:bg-amber-400 hover:bg-slate-800 dark:hover:bg-amber-300 text-white dark:text-slate-950 font-semibold text-xs px-5 py-2 rounded-xl transition-all shadow-xs active:scale-95 cursor-pointer">
                            Kumpulkan
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Angkatan -->
                <div x-show="tab == 'semua' || tab == 'angkatan'" x-transition.opacity.duration.200ms class="group bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 hover:border-slate-300 dark:hover:border-slate-600 rounded-2xl p-5 shadow-xs transition-all duration-200">
                    <div class="flex items-center justify-between gap-2 mb-2.5">
                        <h3 class="font-bold text-base text-slate-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">Video jargon MKB</h3>
                        <span class="bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[11px] font-semibold px-2.5 py-0.5 rounded-full shrink-0 border border-emerald-500/20">Angkatan</span>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-4 line-clamp-2">
                        Video kolaboratif seluruh angkatan menampilkan jargon kebersamaan dan kekeluargaan METASTRO 2026.
                    </p>
                    <div class="flex items-center justify-end pt-2 border-t border-slate-100 dark:border-slate-700/50">
                        <a href="{{ route('peserta.tugas-kumpulkan') }}" class="inline-flex items-center gap-1.5 bg-slate-900 dark:bg-amber-400 hover:bg-slate-800 dark:hover:bg-amber-300 text-white dark:text-slate-950 font-semibold text-xs px-5 py-2 rounded-xl transition-all shadow-xs active:scale-95 cursor-pointer">
                            Kumpulkan
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </a>
                    </div>
                </div>
                
                <!-- Individu 2 -->
                <div x-show="tab == 'semua' || tab == 'individu'" x-transition.opacity.duration.200ms class="group bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 hover:border-slate-300 dark:hover:border-slate-600 rounded-2xl p-5 shadow-xs transition-all duration-200">
                    <div class="flex items-center justify-between gap-2 mb-2.5">
                        <h3 class="font-bold text-base text-slate-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">Koneksi LinkedIn 150+</h3>
                        <span class="bg-rose-500/10 text-rose-600 dark:text-rose-400 text-[11px] font-semibold px-2.5 py-0.5 rounded-full shrink-0 border border-rose-500/20">Individu</span>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-4 line-clamp-2">
                        Mengembangkan jejaring profesional dengan menambahkan koneksi LinkedIn minimal 150 koneksi aktif dan follow page himpunan.
                    </p>
                    <div class="flex items-center justify-end pt-2 border-t border-slate-100 dark:border-slate-700/50">
                        <a href="{{ route('peserta.tugas-kumpulkan') }}" class="inline-flex items-center gap-1.5 bg-slate-900 dark:bg-amber-400 hover:bg-slate-800 dark:hover:bg-amber-300 text-white dark:text-slate-950 font-semibold text-xs px-5 py-2 rounded-xl transition-all shadow-xs active:scale-95 cursor-pointer">
                            Kumpulkan
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER BRANDING -->
        <footer class="text-center py-5 border-t border-slate-200/50 dark:border-slate-800 text-xs text-slate-400 dark:text-slate-500 mt-10">
            &copy; 2026 METASTRO &bull; Spirit of HIRO, Heart of SOLDER
        </footer>
    </div>
</body>
</html>