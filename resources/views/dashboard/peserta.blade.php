<x-app-layout :$title>
<div
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
    }" class="bg-slate-50/50 dark:bg-slate-900 min-h-screen pb-12 font-poppins transition-colors duration-300">

    <div class="w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">

        <div class="space-y-6 sm:space-y-8">
            <!-- 2. WELCOME / DEADLINE CARD -->
            <div class="relative bg-gradient-to-br from-white to-orange-50/50 dark:from-slate-800 dark:to-slate-800/80 border border-orange-100 dark:border-slate-700/60 rounded-3xl p-5 sm:p-6 shadow-[0_2px_20px_-8px_rgba(0,0,0,0.05)] overflow-hidden flex flex-col justify-center group">
                
                <!-- Decorative background blur -->
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-orange-400/10 dark:bg-orange-500/10 rounded-full blur-3xl group-hover:bg-orange-400/20 transition-all duration-500"></div>

                <div class="relative flex items-center justify-between gap-4">
                    <!-- Left: Greetings & Deadline info -->
                    <div class="min-w-0 flex-1">
                        <h2 class="font-kalam text-xl sm:text-2xl font-bold text-slate-800 dark:text-amber-50 tracking-wide leading-tight flex items-center gap-2">
                            Hallo, Jingga muda! <span class="animate-wave inline-block origin-[70%_70%]">👋</span>
                        </h2>
                        
                        <div class="mt-3 inline-flex items-center gap-2 bg-white/60 dark:bg-slate-900/50 backdrop-blur-sm px-3 py-1.5 rounded-full border border-orange-100 dark:border-slate-700">
                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 font-medium">
                                Deadline: <span class="font-semibold text-slate-800 dark:text-slate-100 ml-1">Video perkenalan</span>
                            </p>
                        </div>
                    </div>

                    <!-- Right: Timer & Kumpulkan Button -->
                    <div class="flex flex-col items-end shrink-0 gap-2.5">
                        <div class="bg-red-50 dark:bg-red-500/10 px-3 py-1 rounded-lg border border-red-100 dark:border-red-500/20">
                            <span class="text-red-600 dark:text-red-400 font-bold text-sm sm:text-base tracking-wider">
                                12:45
                            </span>
                        </div>
                        
                        <a href="{{ route('peserta.arsip') }}"
                            class="inline-flex items-center justify-center gap-1 bg-[#FF5B00] hover:bg-[#E05000] text-white font-semibold text-xs sm:text-sm px-4 py-2 rounded-xl shadow-md shadow-orange-500/20 transition-all hover:-translate-y-0.5 active:scale-95 cursor-pointer">
                            Kumpulkan
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 12h14M12 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 3. LIST PENUGASAN SECTION -->
            <section>
                <div class="flex items-center gap-2 mb-4 pl-1">
                    <div class="w-1.5 h-5 bg-[#FF5B00] rounded-full"></div>
                    <h3 class="font-bold text-lg text-slate-800 dark:text-white">
                        List Penugasan
                    </h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- DAY 1 Card -->
                    <a href="{{ route('peserta.tugas-list') }}"
                        class="block bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200/60 dark:border-slate-700/60 shadow-sm hover:shadow-md hover:border-orange-300 dark:hover:border-orange-500/50 transition-all duration-300 hover:-translate-y-1 group cursor-pointer">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-orange-50 dark:bg-orange-500/10 flex items-center justify-center text-[#FF5B00] group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <span class="font-bold text-lg text-slate-800 dark:text-white tracking-wide group-hover:text-[#FF5B00] transition-colors">
                                    DAY 1
                                </span>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-slate-50 dark:bg-slate-700 flex items-center justify-center group-hover:bg-[#FF5B00] group-hover:text-white text-slate-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 font-medium mb-2 flex justify-between items-end">
                                <span>Progres Pengerjaan</span>
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold text-sm">68%</span>
                            </div>
                            <div class="w-full h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-500 rounded-full transition-all duration-1000 ease-out" style="width: 68%;"></div>
                            </div>
                        </div>
                    </a>

                    <!-- DAY 2 Card -->
                    <a href="{{ route('peserta.tugas-list') }}"
                        class="block bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200/60 dark:border-slate-700/60 shadow-sm hover:shadow-md hover:border-orange-300 dark:hover:border-orange-500/50 transition-all duration-300 hover:-translate-y-1 group cursor-pointer">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-orange-50 dark:bg-orange-500/10 flex items-center justify-center text-[#FF5B00] group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <span class="font-bold text-lg text-slate-800 dark:text-white tracking-wide group-hover:text-[#FF5B00] transition-colors">
                                    DAY 2
                                </span>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-slate-50 dark:bg-slate-700 flex items-center justify-center group-hover:bg-[#FF5B00] group-hover:text-white text-slate-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 font-medium mb-2 flex justify-between items-end">
                                <span>Progres Pengerjaan</span>
                                <span class="text-amber-500 dark:text-amber-400 font-bold text-sm">32%</span>
                            </div>
                            <div class="w-full h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-amber-400 rounded-full transition-all duration-1000 ease-out" style="width: 32%;"></div>
                            </div>
                        </div>
                    </a>
                </div>

                <p class="text-[11px] sm:text-xs text-slate-400 dark:text-slate-500 mt-3 pl-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    *Penugasan day 3 akan muncul segera.
                </p>
            </section>

            <!-- 4. TIMELINE SECTION -->
            <section>
                <div class="flex items-center gap-2 mb-4 pl-1">
                    <div class="w-1.5 h-5 bg-[#FF5B00] rounded-full"></div>
                    <h3 class="font-bold text-lg text-slate-800 dark:text-white">
                        Timeline Kegiatan
                    </h3>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-8 border border-slate-200/60 dark:border-slate-700/60 shadow-sm relative">
                    <!-- Modern Vertical Timeline -->
                    <div class="relative border-l-2 border-orange-100 dark:border-slate-700 ml-3 space-y-8">
                        
                        <!-- Step 1: DAY 1 -->
                        <div class="relative pl-6 sm:pl-8 group">
                            <div class="absolute -left-[9px] top-1.5 w-4 h-4 rounded-full bg-white dark:bg-slate-800 border-4 border-[#FF5B00] group-hover:scale-125 transition-transform shadow-[0_0_10px_rgba(255,91,0,0.4)]"></div>
                            <div class="bg-orange-50 dark:bg-slate-700/50 inline-block px-3 py-1 rounded-lg mb-1">
                                <h4 class="font-bold text-sm sm:text-base text-[#FF5B00] dark:text-orange-400">DAY 1</h4>
                            </div>
                            <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 font-medium">18 Oktober 2026</p>
                        </div>

                        <!-- Step 2: DAY 2 -->
                        <div class="relative pl-6 sm:pl-8 group">
                            <div class="absolute -left-[9px] top-1.5 w-4 h-4 rounded-full bg-white dark:bg-slate-800 border-4 border-slate-300 dark:border-slate-500 group-hover:border-[#FF5B00] group-hover:scale-125 transition-all"></div>
                            <div class="bg-slate-50 dark:bg-slate-700/30 inline-block px-3 py-1 rounded-lg mb-1 transition-colors group-hover:bg-orange-50 dark:group-hover:bg-slate-700/50">
                                <h4 class="font-bold text-sm sm:text-base text-slate-700 dark:text-slate-200 group-hover:text-[#FF5B00] dark:group-hover:text-orange-400 transition-colors">DAY 2</h4>
                            </div>
                            <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 font-medium">19 Oktober 2026</p>
                        </div>

                        <!-- Step 3: DAY 3 -->
                        <div class="relative pl-6 sm:pl-8 group">
                            <div class="absolute -left-[9px] top-1.5 w-4 h-4 rounded-full bg-white dark:bg-slate-800 border-4 border-slate-300 dark:border-slate-500 group-hover:border-[#FF5B00] group-hover:scale-125 transition-all"></div>
                            <div class="bg-slate-50 dark:bg-slate-700/30 inline-block px-3 py-1 rounded-lg mb-1 transition-colors group-hover:bg-orange-50 dark:group-hover:bg-slate-700/50">
                                <h4 class="font-bold text-sm sm:text-base text-slate-700 dark:text-slate-200 group-hover:text-[#FF5B00] dark:group-hover:text-orange-400 transition-colors">DAY 3</h4>
                            </div>
                            <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 font-medium">20 Oktober 2026</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 5. CEK BARANG BAWAAN PRIBADI SECTION -->
            <section>
                <div class="flex items-center gap-2 mb-4 pl-1">
                    <div class="w-1.5 h-5 bg-[#FF5B00] rounded-full"></div>
                    <h3 class="font-bold text-lg text-slate-800 dark:text-white">
                        Barang Bawaan Pribadi
                    </h3>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 sm:p-7 border border-slate-200/60 dark:border-slate-700/60 shadow-sm">
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">
                            Tandai barang yang sudah Anda siapkan:
                        </p>

                        <!-- Modern Dropdown Selection -->
                        <div class="relative min-w-[120px]">
                            <button type="button" @click="dayDropdownOpen = !dayDropdownOpen"
                                class="w-full bg-slate-50 hover:bg-slate-100 dark:bg-slate-700/50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 text-sm font-semibold px-4 py-2.5 rounded-xl flex items-center justify-between gap-3 transition-colors focus:ring-2 focus:ring-[#FF5B00]/50 outline-none">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-[#FF5B00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span x-text="activeDay">DAY 1</span>
                                </span>
                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-300" :class="dayDropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="dayDropdownOpen" @click.away="dayDropdownOpen = false" x-transition.opacity.duration.200ms x-cloak
                                class="absolute right-0 mt-2 w-full bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl shadow-lg shadow-black/5 py-1.5 z-20">
                                <template x-for="day in ['DAY 1', 'DAY 2', 'DAY 3']">
                                    <button type="button" @click="activeDay = day; dayDropdownOpen = false"
                                        class="w-full text-left px-4 py-2.5 text-sm font-medium transition-colors"
                                        :class="activeDay === day ? 'bg-orange-50 dark:bg-slate-700/80 text-[#FF5B00]' : 'hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-600 dark:text-slate-300'">
                                        <span x-text="day"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50/50 dark:bg-slate-900/30 rounded-2xl p-2 sm:p-4 border border-slate-100 dark:border-slate-800">
                        <!-- Checklist Items: DAY 1 -->
                        <div x-show="activeDay === 'DAY 1'" x-transition.opacity.duration.300ms class="space-y-1.5">
                            @php
                                $itemsDay1 = [
                                    'day1_buku' => 'Buku Angkatan',
                                    'day1_nametag' => 'Nametag METASTRO 2026',
                                    'day1_slayer' => 'Slayer / Pita Kelompok',
                                    'day1_alat_tulis' => 'Alat Tulis & Binder Catatan',
                                    'day1_tumbler' => 'Tumbler Air Minum 1.5L',
                                    'day1_obat' => 'Obat-obatan Pribadi',
                                ];
                            @endphp
                            @foreach ($itemsDay1 as $key => $label)
                                <label class="flex items-center gap-3.5 cursor-pointer p-3 rounded-xl hover:bg-white dark:hover:bg-slate-800 transition-colors group select-none" @click="toggleItem('{{ $key }}')">
                                    <div class="relative w-5 h-5 rounded-md border-2 flex items-center justify-center transition-all duration-200"
                                        :class="isItemChecked('{{ $key }}') ? 'bg-[#FF5B00] border-[#FF5B00] shadow-sm' : 'border-slate-300 dark:border-slate-500 group-hover:border-[#FF5B00]/50 bg-white dark:bg-slate-900'">
                                        <svg x-show="isItemChecked('{{ $key }}')" x-transition.scale class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <span class="text-sm sm:text-base font-medium transition-colors duration-200"
                                        :class="isItemChecked('{{ $key }}') ? 'line-through text-slate-400 dark:text-slate-500' : 'text-slate-700 dark:text-slate-200 group-hover:text-[#FF5B00] dark:group-hover:text-orange-400'">
                                        {{ $label }}
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        <!-- Checklist Items: DAY 2 -->
                        <div x-show="activeDay === 'DAY 2'" x-transition.opacity.duration.300ms x-cloak class="space-y-1.5">
                            @php
                                $itemsDay2 = [
                                    'day2_maket' => 'Maket Karya Inovasi Kelompok',
                                    'day2_kaos' => 'Kaos Berkerah / Polo Hitam',
                                    'day2_sepatu' => 'Sepatu Sneakers / Olahraga',
                                    'day2_snack' => 'Bekal Makan Siang & Snack Energi',
                                    'day2_jas_hujan' => 'Payung / Jas Hujan',
                                ];
                            @endphp
                            @foreach ($itemsDay2 as $key => $label)
                                <label class="flex items-center gap-3.5 cursor-pointer p-3 rounded-xl hover:bg-white dark:hover:bg-slate-800 transition-colors group select-none" @click="toggleItem('{{ $key }}')">
                                    <div class="relative w-5 h-5 rounded-md border-2 flex items-center justify-center transition-all duration-200"
                                        :class="isItemChecked('{{ $key }}') ? 'bg-[#FF5B00] border-[#FF5B00] shadow-sm' : 'border-slate-300 dark:border-slate-500 group-hover:border-[#FF5B00]/50 bg-white dark:bg-slate-900'">
                                        <svg x-show="isItemChecked('{{ $key }}')" x-transition.scale class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <span class="text-sm sm:text-base font-medium transition-colors duration-200"
                                        :class="isItemChecked('{{ $key }}') ? 'line-through text-slate-400 dark:text-slate-500' : 'text-slate-700 dark:text-slate-200 group-hover:text-[#FF5B00] dark:group-hover:text-orange-400'">
                                        {{ $label }}
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        <!-- Checklist Items: DAY 3 -->
                        <div x-show="activeDay === 'DAY 3'" x-transition.opacity.duration.300ms x-cloak class="space-y-1.5">
                            @php
                                $itemsDay3 = [
                                    'day3_almamater' => 'Almamater Universitas',
                                    'day3_kemeja' => 'Kemeja Putih & Celana Bahan Hitam',
                                    'day3_pantofel' => 'Sepatu Pantofel / Tali Hitam',
                                    'day3_ikrar' => 'Lembar Ikrar Mahasiswa Baru',
                                    'day3_tumbler' => 'Air Mineral & Obat Pribadi',
                                ];
                            @endphp
                            @foreach ($itemsDay3 as $key => $label)
                                <label class="flex items-center gap-3.5 cursor-pointer p-3 rounded-xl hover:bg-white dark:hover:bg-slate-800 transition-colors group select-none" @click="toggleItem('{{ $key }}')">
                                    <div class="relative w-5 h-5 rounded-md border-2 flex items-center justify-center transition-all duration-200"
                                        :class="isItemChecked('{{ $key }}') ? 'bg-[#FF5B00] border-[#FF5B00] shadow-sm' : 'border-slate-300 dark:border-slate-500 group-hover:border-[#FF5B00]/50 bg-white dark:bg-slate-900'">
                                        <svg x-show="isItemChecked('{{ $key }}')" x-transition.scale class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <span class="text-sm sm:text-base font-medium transition-colors duration-200"
                                        :class="isItemChecked('{{ $key }}') ? 'line-through text-slate-400 dark:text-slate-500' : 'text-slate-700 dark:text-slate-200 group-hover:text-[#FF5B00] dark:group-hover:text-orange-400'">
                                        {{ $label }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                </div>
            </section>
        </div>

    </div>
</div>

<style>
    /* Tambahan animasi kecil untuk hiasan melambai (wave) pada sambutan */
    @keyframes wave {
        0%, 100% { transform: rotate(0deg); }
        25% { transform: rotate(15deg); }
        75% { transform: rotate(-15deg); }
    }
    .animate-wave {
        animation: wave 2s infinite ease-in-out;
    }
</style>
</x-app-layout>