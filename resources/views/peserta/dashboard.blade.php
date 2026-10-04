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
            <!-- 1. TOP HEADER APP BAR (NAVBAR) -->
            @include('peserta.partials.navbar')

            <!-- 2. WELCOME / DEADLINE CARD -->
            <div class="bg-[#FFF9F2] dark:bg-amber-950/20 border border-orange-100/60 dark:border-amber-900/40 border-l-4 !border-l-[#F97316] rounded-2xl p-4 sm:p-5 shadow-xs relative overflow-hidden mb-5">
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
                        
                        <a href="{{ route('peserta.arsip') }}"
                            class="inline-block bg-[#FFF0B3] dark:bg-amber-400 hover:bg-[#FFE685] dark:hover:bg-amber-300 text-slate-800 font-bold text-xs sm:text-[13px] px-3.5 py-1.5 rounded-lg shadow-2xs transition active:scale-95 cursor-pointer text-center">
                            Kumpulkan
                        </a>
                    </div>
                </div>
            </div>

            <!-- 3. LIST PENUGASAN SECTION -->
            <section class="mb-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-bold text-base sm:text-lg text-slate-900 dark:text-white">
                        List Penugasan:
                    </h3>
                </div>

                <div class="space-y-3">
                    <!-- DAY 1 Card -->
                    <a href="{{ route('peserta.tugas-list') }}"
                        class="block bg-white dark:bg-slate-800/90 rounded-2xl p-4 sm:p-5 border border-slate-100 dark:border-slate-700/60 shadow-xs hover:border-[#FF5B00]/40 dark:hover:border-orange-500/40 hover:shadow-md transition active:scale-[0.99] group cursor-pointer">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-base sm:text-lg text-slate-900 dark:text-white tracking-wide group-hover:text-[#FF5B00] transition-colors">
                                DAY 1
                            </span>
                            <span class="text-slate-700 dark:text-slate-300 font-bold text-sm tracking-tighter group-hover:translate-x-1 group-hover:text-[#FF5B00] transition-all duration-200">
                                &gt;&gt;
                            </span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-2.5">
                            <div class="text-xs text-slate-500 dark:text-slate-400 font-medium mb-1.5 flex justify-between">
                                <span>Progres:</span>
                                <span class="text-emerald-600 dark:text-emerald-400 font-semibold">68%</span>
                            </div>
                            <div class="w-full h-2.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-[#00A82D] rounded-full transition-all duration-500" style="width: 68%;"></div>
                            </div>
                        </div>
                    </a>

                    <!-- DAY 2 Card -->
                    <a href="{{ route('peserta.tugas-list') }}"
                        class="block bg-white dark:bg-slate-800/90 rounded-2xl p-4 sm:p-5 border border-slate-100 dark:border-slate-700/60 shadow-xs hover:border-[#FF5B00]/40 dark:hover:border-orange-500/40 hover:shadow-md transition active:scale-[0.99] group cursor-pointer">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-base sm:text-lg text-slate-900 dark:text-white tracking-wide group-hover:text-[#FF5B00] transition-colors">
                                DAY 2
                            </span>
                            <span class="text-slate-700 dark:text-slate-300 font-bold text-sm tracking-tighter group-hover:translate-x-1 group-hover:text-[#FF5B00] transition-all duration-200">
                                &gt;&gt;
                            </span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-2.5">
                            <div class="text-xs text-slate-500 dark:text-slate-400 font-medium mb-1.5 flex justify-between">
                                <span>Progres:</span>
                                <span class="text-emerald-600 dark:text-emerald-400 font-semibold">32%</span>
                            </div>
                            <div class="w-full h-2.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-[#00A82D] rounded-full transition-all duration-500" style="width: 32%;"></div>
                            </div>
                        </div>
                    </a>
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
</body>
</html>
