<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-dvh">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Peserta - METASTRO 2026</title>

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
    <link href="https://fonts.googleapis.com/css2?family=Kalam:wght@400;700&family=Oswald:wght@500;600;700;800&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Tailwind / App Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .font-kalam {
            font-family: 'Kalam', cursive;
        }
        .font-oswald-header {
            font-family: 'Oswald', sans-serif;
            letter-spacing: 0.12em;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.4);
            border-radius: 9999px;
        }
    </style>
</head>

<body class="font-poppins antialiased bg-[#FFFDF9] dark:bg-slate-900 text-slate-900 dark:text-slate-100 min-h-dvh transition-colors duration-200"
    x-data="{
        mobileMenu: false,
        notifOpen: false,
        dayDropdownOpen: false,
        activeDay: 'DAY 1',
        expandedDay1: false,
        expandedDay2: false,
        darkMode: document.documentElement.classList.contains('dark'),
        
        // Checklist Barang Bawaan state
        checklist: JSON.parse(localStorage.getItem('metastro_barang_checklist') || '{}'),
        toggleItem(key) {
            this.checklist[key] = !this.checklist[key];
            localStorage.setItem('metastro_barang_checklist', JSON.stringify(this.checklist));
        },
        isItemChecked(key) {
            return !!this.checklist[key];
        },
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

    <!-- CONTAINER MOBILE-FIRST -->
    <div class="max-w-md mx-auto sm:max-w-xl md:max-w-2xl px-4 py-5 min-h-screen flex flex-col justify-between">

        <div>
            <!-- 1. TOP HEADER APP BAR -->
            <header class="sticky top-0 z-40 bg-white dark:bg-slate-900 flex items-center justify-between py-4 px-4 mb-4 -mx-4 -mt-5 shadow-sm rounded-b-xl">
                <!-- Brand Title -->
                <a href="{{ route('peserta.dashboard') }}" class="inline-block group">
                    <h1 class="font-oswald-header text-xl sm:text-2xl font-bold uppercase tracking-[0.14em] text-slate-900 dark:text-white transition group-hover:text-[#FF5B00]">
                        METASTRO 2026
                    </h1>
                </a>

                <!-- Action Icons: Notifikasi & Hamburger Menu -->
                <div class="flex items-center gap-2">
                    <!-- Notification Bell -->
                    <button type="button" @click="notifOpen = !notifOpen"
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 relative transition cursor-pointer"
                        title="Notifikasi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                        <!-- Red Dot Notification Badge -->
                        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white dark:ring-slate-900"></span>
                    </button>

                    <!-- Hamburger Menu Button -->
                    <button type="button" @click="mobileMenu = true"
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-800 dark:text-slate-200 transition cursor-pointer"
                        title="Buka Menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                        </svg>
                    </button>
                </div>
            </header>

            <!-- 2. WELCOME / DEADLINE CARD -->
            <div class="bg-[#FFF9F2] dark:bg-amber-950/20 border-l-4 border-[#FF5B00] rounded-2xl p-4 sm:p-5 shadow-xs border border-orange-100/60 dark:border-amber-900/40 relative overflow-hidden mb-5">
                <div class="flex items-start justify-between gap-3">
                    <!-- Left: Greetings & Deadline info -->
                    <div class="min-w-0 pr-2">
                        <h2 class="font-kalam text-lg sm:text-xl font-bold text-slate-800 dark:text-amber-100 tracking-wide leading-tight">
                            Hallo, Jingga muda!
                        </h2>
                        
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-1.5">
                            Deadline terdekat:
                        </p>
                        <div class="flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-200 mt-0.5">
                            <span class="text-slate-700 dark:text-slate-300">•</span>
                            <span class="truncate">Video perkenalan singkat</span>
                        </div>
                    </div>

                    <!-- Right: Timer & Kumpulkan Button -->
                    <div class="flex flex-col items-end shrink-0 gap-2">
                        <span class="text-red-500 font-bold text-sm sm:text-base tracking-wide">
                            12:45
                        </span>
                        
                        <button type="button"
                            class="bg-[#FFF0B3] dark:bg-amber-400 hover:bg-[#FFE685] dark:hover:bg-amber-300 text-slate-800 font-bold text-xs sm:text-[13px] px-3.5 py-1.5 rounded-lg shadow-2xs transition active:scale-95 cursor-pointer">
                            Kumpulkan
                        </button>
                    </div>
                </div>
            </div>

            <!-- 3. LIST PENUGASAN SECTION -->
            <section class="mb-6">
                <h3 class="font-bold text-base sm:text-lg text-slate-900 dark:text-white mb-3">
                    List Penugasan:
                </h3>

                <div class="space-y-3">
                    <!-- DAY 1 Card -->
                    <div class="bg-white dark:bg-slate-800/90 rounded-2xl p-4 sm:p-5 border border-slate-100 dark:border-slate-700/60 shadow-xs hover:border-slate-200 dark:hover:border-slate-600 transition">
                        <div class="flex items-center justify-between cursor-pointer" @click="expandedDay1 = !expandedDay1">
                            <span class="font-bold text-base sm:text-lg text-slate-900 dark:text-white tracking-wide">
                                DAY 1
                            </span>
                            <span class="text-slate-700 dark:text-slate-300 font-bold text-sm tracking-tighter transition-transform duration-200"
                                :class="expandedDay1 ? 'rotate-90 text-[#FF5B00]' : ''">
                                &gt;&gt;
                            </span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-2.5">
                            <div class="text-xs text-slate-500 dark:text-slate-400 font-medium mb-1.5 flex justify-between">
                                <span>Progres:</span>
                                <span class="text-emerald-600 dark:text-emerald-400 font-semibold" x-show="expandedDay1">68%</span>
                            </div>
                            <div class="w-full h-2.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-[#00A82D] rounded-full transition-all duration-500" style="width: 68%;"></div>
                            </div>
                        </div>

                    </div>

                    <!-- DAY 2 Card -->
                    <div class="bg-white dark:bg-slate-800/90 rounded-2xl p-4 sm:p-5 border border-slate-100 dark:border-slate-700/60 shadow-xs hover:border-slate-200 dark:hover:border-slate-600 transition">
                        <div class="flex items-center justify-between cursor-pointer" @click="expandedDay2 = !expandedDay2">
                            <span class="font-bold text-base sm:text-lg text-slate-900 dark:text-white tracking-wide">
                                DAY 2
                            </span>
                            <span class="text-slate-700 dark:text-slate-300 font-bold text-sm tracking-tighter transition-transform duration-200"
                                :class="expandedDay2 ? 'rotate-90 text-[#FF5B00]' : ''">
                                &gt;&gt;
                            </span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-2.5">
                            <div class="text-xs text-slate-500 dark:text-slate-400 font-medium mb-1.5 flex justify-between">
                                <span>Progres:</span>
                                <span class="text-emerald-600 dark:text-emerald-400 font-semibold" x-show="expandedDay2">32%</span>
                            </div>
                            <div class="w-full h-2.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-[#00A82D] rounded-full transition-all duration-500" style="width: 32%;"></div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Footnote / Note -->
                <p class="text-xs italic text-slate-400 dark:text-slate-500 mt-2.5 pl-1 font-normal">
                    *Penugasan day 3 akan muncul segera.
                </p>
            </section>

            <!-- 4. TIMELINE SECTION -->
            <section class="mb-6">
                <h3 class="font-bold text-base sm:text-lg text-slate-900 dark:text-white mb-3">
                    Timeline:
                </h3>

                <div class="bg-white dark:bg-slate-800/90 rounded-2xl p-5 sm:p-6 border border-slate-100 dark:border-slate-700/60 shadow-xs relative overflow-hidden">
                    <!-- Diagonal / Stepped Timeline matching image -->
                    <div class="flex flex-col space-y-4 py-2">
                        <!-- Step 1: DAY 1 (Top Left) -->
                        <div class="self-start pl-2">
                            <div class="flex items-center gap-1.5 font-bold text-sm sm:text-base text-slate-900 dark:text-white">
                                <span class="text-slate-900 dark:text-white text-base leading-none">•</span>
                                <span>DAY 1</span>
                            </div>
                            <div class="text-xs sm:text-[13px] text-slate-600 dark:text-slate-400 pl-3.5 font-normal">
                                18 Oktober
                            </div>
                        </div>

                        <!-- Step 2: DAY 2 (Center) -->
                        <div class="self-center">
                            <div class="flex items-center gap-1.5 font-bold text-sm sm:text-base text-slate-900 dark:text-white">
                                <span class="text-slate-900 dark:text-white text-base leading-none">•</span>
                                <span>DAY 2</span>
                            </div>
                            <div class="text-xs sm:text-[13px] text-slate-600 dark:text-slate-400 pl-3.5 font-normal">
                                19 Oktober
                            </div>
                        </div>

                        <!-- Step 3: DAY 3 (Bottom Right) -->
                        <div class="self-end pr-2">
                            <div class="flex items-center gap-1.5 font-bold text-sm sm:text-base text-slate-900 dark:text-white">
                                <span class="text-slate-900 dark:text-white text-base leading-none">•</span>
                                <span>DAY 3</span>
                            </div>
                            <div class="text-xs sm:text-[13px] text-slate-600 dark:text-slate-400 pl-3.5 font-normal">
                                20 Oktober
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 5. CEK BARANG BAWAAN PRIBADI SECTION -->
            <section class="mb-8">
                <div class="bg-white dark:bg-slate-800/90 rounded-2xl p-5 sm:p-6 border border-slate-100 dark:border-slate-700/60 shadow-xs">
                    <h3 class="font-bold text-base sm:text-lg text-slate-900 dark:text-white leading-tight">
                        Cek barang bawaan pribadi
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1 mb-3.5 font-normal">
                        Periksa kembali barang bawaan individu untuk:
                    </p>

                    <!-- Filter Dropdown Pill Button (DAY 1 v) -->
                    <div class="relative inline-block mb-4">
                        <button type="button" @click="dayDropdownOpen = !dayDropdownOpen"
                            class="bg-[#FF5B00] hover:bg-[#e05000] text-white text-xs font-bold px-3 py-1.5 rounded-lg inline-flex items-center gap-1.5 shadow-2xs transition cursor-pointer">
                            <span x-text="activeDay">DAY 1</span>
                            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="dayDropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu for Day Selection -->
                        <div x-show="dayDropdownOpen" @click.away="dayDropdownOpen = false" x-cloak
                            class="absolute left-0 mt-1 w-32 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg py-1 z-20 text-xs">
                            <button type="button" @click="activeDay = 'DAY 1'; dayDropdownOpen = false"
                                class="w-full text-left px-3 py-1.5 hover:bg-orange-50 dark:hover:bg-slate-700 font-semibold"
                                :class="activeDay === 'DAY 1' ? 'text-[#FF5B00]' : 'text-slate-700 dark:text-slate-300'">
                                DAY 1
                            </button>
                            <button type="button" @click="activeDay = 'DAY 2'; dayDropdownOpen = false"
                                class="w-full text-left px-3 py-1.5 hover:bg-orange-50 dark:hover:bg-slate-700 font-semibold"
                                :class="activeDay === 'DAY 2' ? 'text-[#FF5B00]' : 'text-slate-700 dark:text-slate-300'">
                                DAY 2
                            </button>
                            <button type="button" @click="activeDay = 'DAY 3'; dayDropdownOpen = false"
                                class="w-full text-left px-3 py-1.5 hover:bg-orange-50 dark:hover:bg-slate-700 font-semibold"
                                :class="activeDay === 'DAY 3' ? 'text-[#FF5B00]' : 'text-slate-700 dark:text-slate-300'">
                                DAY 3
                            </button>
                        </div>
                    </div>

                    <!-- Checklist Items: DAY 1 -->
                    <div x-show="activeDay === 'DAY 1'" class="space-y-3">
                        @php
                            $itemsDay1 = [
                                'day1_buku' => 'buku angkatan',
                                'day1_nametag' => 'nametag METASTRO 2026',
                                'day1_slayer' => 'slayer / pita kelompok',
                                'day1_alat_tulis' => 'alat tulis & binder catatan',
                                'day1_tumbler' => 'tumbler air minum 1.5L',
                                'day1_obat' => 'obat-obatan pribadi',
                            ];
                        @endphp
                        @foreach ($itemsDay1 as $key => $label)
                            <label class="flex items-center gap-3 cursor-pointer select-none group" @click="toggleItem('{{ $key }}')">
                                <div class="w-4 h-4 rounded border-2 flex items-center justify-center transition-colors"
                                    :class="isItemChecked('{{ $key }}') ? 'bg-[#FF5B00] border-[#FF5B00]' : 'border-slate-800 dark:border-slate-400 group-hover:border-[#FF5B00]'">
                                    <svg x-show="isItemChecked('{{ $key }}')" class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </div>
                                <span class="text-xs sm:text-sm font-normal text-slate-800 dark:text-slate-200 transition"
                                    :class="isItemChecked('{{ $key }}') ? 'line-through text-slate-400 dark:text-slate-500' : ''">
                                    {{ $label }}
                                </span>
                            </label>
                        @endforeach
                    </div>

                    <!-- Checklist Items: DAY 2 -->
                    <div x-show="activeDay === 'DAY 2'" x-cloak class="space-y-3">
                        @php
                            $itemsDay2 = [
                                'day2_maket' => 'maket karya inovasi kelompok',
                                'day2_kaos' => 'kaos berkerah / polo hitam',
                                'day2_sepatu' => 'sepatu sneakers / olahraga',
                                'day2_snack' => 'bekal makan siang & snack energi',
                                'day2_jas_hujan' => 'payung / jas hujan',
                            ];
                        @endphp
                        @foreach ($itemsDay2 as $key => $label)
                            <label class="flex items-center gap-3 cursor-pointer select-none group" @click="toggleItem('{{ $key }}')">
                                <div class="w-4 h-4 rounded border-2 flex items-center justify-center transition-colors"
                                    :class="isItemChecked('{{ $key }}') ? 'bg-[#FF5B00] border-[#FF5B00]' : 'border-slate-800 dark:border-slate-400 group-hover:border-[#FF5B00]'">
                                    <svg x-show="isItemChecked('{{ $key }}')" class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </div>
                                <span class="text-xs sm:text-sm font-normal text-slate-800 dark:text-slate-200 transition"
                                    :class="isItemChecked('{{ $key }}') ? 'line-through text-slate-400 dark:text-slate-500' : ''">
                                    {{ $label }}
                                </span>
                            </label>
                        @endforeach
                    </div>

                    <!-- Checklist Items: DAY 3 -->
                    <div x-show="activeDay === 'DAY 3'" x-cloak class="space-y-3">
                        @php
                            $itemsDay3 = [
                                'day3_almamater' => 'almamater universitas',
                                'day3_kemeja' => 'kemeja putih lengan panjang & celana bahan hitam',
                                'day3_pantofel' => 'sepatu pantofel / tali hitam bertali',
                                'day3_ikrar' => 'lembar ikrar mahasiswa baru',
                                'day3_tumbler' => 'air mineral & obat pribadi',
                            ];
                        @endphp
                        @foreach ($itemsDay3 as $key => $label)
                            <label class="flex items-center gap-3 cursor-pointer select-none group" @click="toggleItem('{{ $key }}')">
                                <div class="w-4 h-4 rounded border-2 flex items-center justify-center transition-colors"
                                    :class="isItemChecked('{{ $key }}') ? 'bg-[#FF5B00] border-[#FF5B00]' : 'border-slate-800 dark:border-slate-400 group-hover:border-[#FF5B00]'">
                                    <svg x-show="isItemChecked('{{ $key }}')" class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </div>
                                <span class="text-xs sm:text-sm font-normal text-slate-800 dark:text-slate-200 transition"
                                    :class="isItemChecked('{{ $key }}') ? 'line-through text-slate-400 dark:text-slate-500' : ''">
                                    {{ $label }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>

        <!-- FOOTER BRANDING -->
        <footer class="text-center py-4 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-400">
            &copy; 2026 METASTRO &bull; Spirit of HIRO, Heart of SOLDER
        </footer>
    </div>



    <!-- 7. NOTIFIKASI MODAL / POPOVER -->
    <div x-show="notifOpen" x-cloak class="fixed inset-0 z-50 flex items-start justify-end p-4 bg-black/30"
        @click.self="notifOpen = false">
        <div class="bg-white dark:bg-slate-800 rounded-2xl w-full max-w-xs mt-12 p-4 shadow-xl border border-slate-100 dark:border-slate-700">
            <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-100 dark:border-slate-700">
                <span class="font-bold text-xs text-slate-900 dark:text-white">Pemberitahuan</span>
                <button type="button" @click="notifOpen = false" class="text-xs text-slate-400 hover:text-slate-600">Tutup</button>
            </div>
            <div class="space-y-2.5 text-xs">
                <div class="p-2.5 rounded-xl bg-orange-50 dark:bg-orange-950/40 border border-orange-100 dark:border-orange-900/40">
                    <p class="font-semibold text-[#FF5B00]">Deadline Terdekat!</p>
                    <p class="text-slate-600 dark:text-slate-300 mt-0.5 text-[11px]">Video perkenalan singkat berakhir dalam waktu 12 menit lagi.</p>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-700/50">
                    <p class="font-semibold text-slate-800 dark:text-slate-200">Pengingat Perlengkapan</p>
                    <p class="text-slate-500 dark:text-slate-400 mt-0.5 text-[11px]">Jangan lupa periksa buku angkatan dan nametag Anda untuk Day 1.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 8. SLIDE-OVER NAVIGATION DRAWER -->
    <div x-show="mobileMenu" x-cloak class="fixed inset-0 z-50 flex justify-end"
        @keydown.escape.window="mobileMenu = false">
        <!-- Backdrop -->
        <div x-show="mobileMenu" x-transition.opacity class="fixed inset-0 bg-black/50" @click="mobileMenu = false"></div>

        <!-- Drawer Content -->
        <div x-show="mobileMenu" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="relative w-72 max-w-[80vw] bg-white dark:bg-slate-800 h-full p-5 shadow-2xl flex flex-col justify-between z-10">
            
            <div>
                <!-- Header Drawer -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-700">
                    <div>
                        <h4 class="font-oswald-header text-base font-bold uppercase tracking-wider text-slate-900 dark:text-white">
                            METASTRO 2026
                        </h4>
                        <p class="text-[11px] text-slate-400">Portal Peserta</p>
                    </div>
                    <button type="button" @click="mobileMenu = false" class="p-1 text-slate-400 hover:text-slate-700 dark:hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- User Info (Card Style as per Image 4) -->
                <div class="flex gap-3 mb-6 mt-4 p-2">
                    <div class="text-slate-700 mt-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                    </div>
                    <div>
                        <p class="text-[13px] font-medium text-slate-800">Nama Lengkap: Siti Aisyah</p>
                        <p class="text-[13px] font-medium text-slate-800">NIM: 2601829</p>
                        <p class="text-[13px] font-medium text-slate-800">Regu: 8 Syntax</p>
                    </div>
                </div>

                <!-- Menu Links -->
                <nav class="space-y-4 mt-2">
                    <div>
                        <h5 class="text-[10px] text-slate-500 font-medium uppercase tracking-wider mb-2 px-3">NAVIGASI UTAMA</h5>
                        <div class="space-y-0.5">
                            <a href="{{ route('peserta.dashboard') }}" class="flex items-center gap-3 px-3 py-2 text-sm text-slate-700 hover:text-slate-900 border-b-2 border-orange-200">
                                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                <span>Beranda</span>
                            </a>
                            <a href="{{ route('peserta.arsip') }}" class="flex items-center gap-3 px-3 py-2 text-sm text-slate-700 hover:text-slate-900 border-b border-transparent">
                                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                <span>Arsip tugas</span>
                            </a>
                            <a href="#" class="flex items-center gap-3 px-3 py-2 text-sm text-slate-700 hover:text-slate-900 border-b border-transparent">
                                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                                <span>Izin</span>
                            </a>
                        </div>
                    </div>

                    <div>
                        <h5 class="text-[10px] text-slate-500 font-medium uppercase tracking-wider mb-2 px-3 mt-4">INFORMASI & MEDIA SOSIAL</h5>
                        <div class="space-y-0.5">
                            <a href="#" class="flex items-center gap-3 px-3 py-2 text-sm text-slate-700 hover:text-slate-900 border-b border-transparent">
                                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                <span>Booklet</span>
                            </a>
                            <a href="#" class="flex items-center gap-3 px-3 py-2 text-sm text-slate-700 hover:text-slate-900 border-b border-transparent">
                                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8a5 5 0 015-5h8a5 5 0 015 5v8a5 5 0 01-5 5H8a5 5 0 01-5-5V8zm5-3a3 3 0 00-3 3v8a3 3 0 003 3h8a3 3 0 003-3V8a3 3 0 00-3-3H8zm7.5 9.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zm-2.5-4a4 4 0 100 8 4 4 0 000-8zm4.5-1.5a1 1 0 11-2 0 1 1 0 012 0z"/></svg>
                                <span>Instagram</span>
                            </a>
                        </div>
                    </div>
                </nav>
            </div>

            <!-- Bottom of Drawer: Logout -->
            <div class="pt-4 flex justify-end px-3">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 text-[13px] font-medium text-slate-600 hover:text-slate-800 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
