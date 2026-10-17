<x-app-layout :$title>
    <div x-data="{
        mobileMenu: false,
        notifOpen: false,
        openPengumumanDetail: false,
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
    }"
        class="bg-slate-50/50 dark:bg-slate-900 min-h-screen pb-12 font-poppins transition-colors duration-300">

        <div class="w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">

            <div>
                <!-- 2. WELCOME / DEADLINE CARD -->
                <div
                    class="bg-white dark:bg-slate-800 border border-orange-100/60 dark:border-amber-900/40 border-l-4 !border-l-[#F97316] rounded-2xl p-4 sm:p-5 shadow-sm relative overflow-hidden mb-5">
                    <div class="flex items-center justify-between gap-3">
                        <!-- Left: Greetings & Deadline info -->
                        <div class="min-w-0 pr-2">
                            <h2
                                class="font-kalam text-lg sm:text-xl font-bold text-[#1E293B] dark:text-amber-100 tracking-wide leading-tight">
                                Hallo, Jingga muda!
                            </h2>

                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-1.5">
                                Deadline terdekat:
                            </p>
                            <div
                                class="flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-[#1E293B] dark:text-slate-200 mt-0.5">
                                <span class="text-[#1E293B] dark:text-slate-300">•</span>
                                <span class="truncate">Video perkenalan singkat</span>
                            </div>
                        </div>

                        <!-- Right: Deadline Time side-by-side with Kumpulkan Button -->
                        <div class="flex items-center shrink-0 gap-3">
                            <span class="text-red-500 font-bold text-sm sm:text-base tracking-wide whitespace-nowrap">
                                12:45
                            </span>

                            <a href="{{ route('peserta.arsip') }}"
                                class="inline-block bg-[#FFF0B3] dark:bg-amber-400 hover:bg-[#FFE685] dark:hover:bg-amber-300 text-[#1E293B] font-bold text-xs sm:text-[13px] px-3.5 py-1.5 rounded-lg shadow-2xs transition active:scale-95 cursor-pointer text-center whitespace-nowrap">
                                Kumpulkan
                            </a>
                        </div>
                    </div>

                    <!-- Pengumuman Singkat Dibawah Deadline -->
                    <div class="mt-3.5 pt-3 border-t border-dashed border-slate-200/80 dark:border-slate-700/80">
                        <button type="button" @click="openPengumumanDetail = true"
                            class="w-full flex items-center justify-between gap-2.5 p-2.5 rounded-xl bg-orange-50/70 hover:bg-orange-100/70 dark:bg-orange-950/30 dark:hover:bg-orange-950/50 border border-orange-200/60 dark:border-orange-800/40 text-left transition group cursor-pointer">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span
                                    class="flex items-center justify-center w-7 h-7 rounded-lg bg-[#FF5B00]/10 text-[#FF5B00] shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z">
                                        </path>
                                    </svg>
                                </span>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="text-[11px] font-bold tracking-wider uppercase text-[#FF5B00]">Pengumuman</span>
                                        <span class="text-[10px] text-slate-400">•
                                            {{ $pengumumanTerbaru?->tanggal_publish ? \Carbon\Carbon::parse($pengumumanTerbaru->tanggal_publish)->diffForHumans() : 'Terbaru' }}</span>
                                    </div>
                                    <p
                                        class="text-xs font-medium text-slate-700 dark:text-slate-200 truncate mt-0.5 group-hover:text-[#FF5B00] transition-colors">
                                        {{ $pengumumanTerbaru?->judul ?? 'Pemberitahuan penting kegiatan METASTRO 2026' }}
                                    </p>
                                </div>
                            </div>

                            <span
                                class="text-xs font-semibold text-[#FF5B00] flex items-center gap-1 shrink-0 group-hover:translate-x-0.5 transition-transform">
                                <span class="hidden sm:inline text-[11px]">Lihat Detail</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Modal Detail Pengumuman -->
                <div x-show="openPengumumanDetail" x-cloak
                    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
                    @click.self="openPengumumanDetail = false" @keydown.escape.window="openPengumumanDetail = false">
                    <div x-show="openPengumumanDetail" x-transition.scale.95
                        class="bg-white dark:bg-slate-800 rounded-2xl w-full max-w-lg p-5 sm:p-6 shadow-2xl border border-slate-100 dark:border-slate-700 relative max-h-[90vh] flex flex-col">
                        <!-- Modal Header -->
                        <div
                            class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-700">
                            <div class="flex items-center gap-2.5">
                                <span
                                    class="flex items-center justify-center w-8 h-8 rounded-xl bg-orange-100 dark:bg-orange-950/60 text-[#FF5B00] shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z">
                                        </path>
                                    </svg>
                                </span>
                                <div>
                                    <span
                                        class="text-[10px] font-bold tracking-wider uppercase px-2 py-0.5 rounded-full bg-orange-100 dark:bg-orange-950/60 text-[#FF5B00]">
                                        Pengumuman
                                    </span>
                                    <h3 class="font-bold text-base sm:text-lg text-[#1E293B] dark:text-white mt-1">
                                        {{ $pengumumanTerbaru?->judul ?? 'Pemberitahuan Kegiatan METASTRO 2026' }}
                                    </h3>
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        {{ $pengumumanTerbaru?->tanggal_publish ? \Carbon\Carbon::parse($pengumumanTerbaru->tanggal_publish)->locale('id')->isoFormat('D MMMM Y, HH.mm') . ' WIB' : 'Hari ini' }}
                                        • Oleh {{ $pengumumanTerbaru?->pembuat?->nama ?? 'Panitia METASTRO' }}
                                    </p>
                                </div>
                            </div>
                            <button type="button" @click="openPengumumanDetail = false"
                                class="p-1.5 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div
                            class="overflow-y-auto py-4 text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line flex-1">
                            {{ $pengumumanTerbaru?->isi ?? "Halo teman-teman Jingga Muda!\n\nPastikan seluruh berkas dan tugas dikumpulkan tepat waktu sebelum batas tenggat berakhir. Jika ada kendala terkait penugasan atau atribut, segera hubungi guider kelompok masing-masing ya!\n\nSemangat dan persiapkan diri dengan baik!" }}
                        </div>

                        @if ($pengumumanTerbaru?->lampiran)
                            <div class="pt-3 border-t border-slate-100 dark:border-slate-700 mb-3">
                                <a href="{{ asset('storage/' . $pengumumanTerbaru->lampiran) }}" target="_blank"
                                    class="inline-flex items-center gap-2 text-xs font-semibold text-[#FF5B00] hover:underline">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                                        </path>
                                    </svg>
                                    <span>Lihat Lampiran Dokumen</span>
                                </a>
                            </div>
                        @endif

                        <!-- Modal Footer -->
                        <div class="pt-3 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                            <button type="button" @click="openPengumumanDetail = false"
                                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-[#1E293B] dark:text-slate-200 font-semibold text-xs rounded-xl transition cursor-pointer">
                                Tutup
                            </button>
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
                        @forelse ($days ?? [] as $index => $day)
                            @php
                                // Persentase dummy pengerjaan
                                $dummyProgress = [68, 32, 50, 10, 80][$index % 5] ?? 50;
                                $isPassed = $dummyProgress >= 50;
                                $textClass = $isPassed
                                    ? 'text-emerald-600 dark:text-emerald-400'
                                    : 'text-amber-500 dark:text-amber-400';
                                $bgClass = $isPassed ? 'bg-emerald-500' : 'bg-amber-400';
                            @endphp

                            <!-- Card Day Dinamis -->
                            <a href="{{ route('peserta.tugas-list', ['day' => $day]) }}"
                                class="block bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200/60 dark:border-slate-700/60 shadow-sm hover:shadow-md hover:border-orange-300 dark:hover:border-orange-500/50 transition-all duration-300 hover:-translate-y-1 group cursor-pointer">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-full bg-orange-50 dark:bg-orange-500/10 flex items-center justify-center text-[#FF5B00] group-hover:scale-110 transition-transform">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                        </div>
                                        <span
                                            class="font-bold text-lg text-slate-800 dark:text-white tracking-wide group-hover:text-[#FF5B00] transition-colors uppercase">
                                            {{ $day }}
                                        </span>
                                    </div>
                                    <div
                                        class="w-8 h-8 rounded-full bg-slate-50 dark:bg-slate-700 flex items-center justify-center group-hover:bg-[#FF5B00] group-hover:text-white text-slate-400 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div>
                                    <div
                                        class="text-xs text-slate-500 dark:text-slate-400 font-medium mb-2 flex justify-between items-end">
                                        <span>Progres Pengerjaan</span>
                                        <span class="{{ $textClass }} font-bold text-sm">
                                            {{ $dummyProgress }}%
                                        </span>
                                    </div>
                                    <div
                                        class="w-full h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                        <div class="h-full {{ $bgClass }} rounded-full transition-all duration-1000 ease-out"
                                            style="width: {{ $dummyProgress }}%;"></div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div
                                class="col-span-full py-8 text-center bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 text-slate-400 text-xs">
                                Belum ada penugasan yang tersedia saat ini.
                            </div>
                        @endforelse
                    </div>

                    @if (!empty($days) && (is_countable($days) ? count($days) > 0 : true))
                        <p
                            class="text-[11px] sm:text-xs text-slate-400 dark:text-slate-500 mt-3 pl-2 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            *Penugasan day selanjutnya coming soon.
                        </p>
                    @endif
                </section>

                <!-- 4. TIMELINE SECTION -->
                <section class="mt-4">
                    <div class="flex items-center gap-2 mb-4 pl-1">
                        <div class="w-1.5 h-5 bg-[#FF5B00] rounded-full"></div>
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white">
                            Timeline Kegiatan
                        </h3>
                    </div>

                    <div
                        class="bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-8 border border-slate-200/60 dark:border-slate-700/60 shadow-sm relative">
                        <!-- Modern Vertical Timeline -->
                        <div class="relative border-l-2 border-orange-100 dark:border-slate-700 ml-3 space-y-8">

                            <!-- Tampilkan list kegiatan yang sudah publish -->
                            @foreach ($kegiatans as $kegiatan)
                                <div class="relative pl-6 sm:pl-8 group">
                                    <div
                                        class="absolute -left-[9px] top-1.5 w-4 h-4 rounded-full bg-white dark:bg-slate-800 border-4 border-[#FF5B00] group-hover:scale-125 transition-transform shadow-[0_0_10px_rgba(255,91,0,0.4)]">
                                    </div>
                                    <div
                                        class="bg-orange-50 dark:bg-slate-700/50 inline-block px-3 py-1 rounded-lg mb-1">
                                        <h4 class="font-bold text-sm sm:text-base text-[#FF5B00] dark:text-orange-400">
                                            {{ $kegiatan->nama }}
                                        </h4>
                                    </div>
                                    <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 font-medium">
                                        {{ \Carbon\Carbon::parse($kegiatan->tanggal_mulai)->translatedFormat('d F Y') }}
                                    </p>
                                </div>
                            @endforeach

                            <!-- Elemen "Coming Soon" otomatis muncul jika ada kegiatan yang belum di-publish -->
                            @if ($hasUnpublished)
                                <div class="relative pl-6 sm:pl-8 group opacity-75">
                                    <!-- Dot Timeline dengan gaya putus-putus / abu-abu penanda coming soon -->
                                    <div
                                        class="absolute -left-[9px] top-1.5 w-4 h-4 rounded-full bg-white dark:bg-slate-800 border-4 border-slate-400 dark:border-slate-600 shadow-[0_0_8px_rgba(156,163,175,0.4)] animate-pulse">
                                    </div>
                                    <div
                                        class="bg-slate-100 dark:bg-slate-700 inline-block px-3 py-1 rounded-lg mb-1 border border-dashed border-slate-300 dark:border-slate-600">
                                        <h4
                                            class="font-bold text-sm sm:text-base text-slate-500 dark:text-slate-400 dynamic-pulse">
                                            Coming Soon
                                        </h4>
                                    </div>
                                </div>
                            @endif

                            <!-- Kondisi cadangan: Jika sama sekali TIDAK ADA kegiatan (baik publish maupun belum) -->
                            @if ($kegiatans->isEmpty() && !$hasUnpublished)
                                <div class="text-center py-4 text-slate-400 dark:text-slate-500">
                                    Belum ada agenda kegiatan saat ini.
                                </div>
                            @endif

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

                    <div
                        class="bg-white dark:bg-slate-800 rounded-3xl p-5 sm:p-7 border border-slate-200/60 dark:border-slate-700/60 shadow-sm">

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                            <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">
                                Tandai barang yang sudah Anda siapkan:
                            </p>

                            <!-- Modern Dropdown Selection -->
                            <div class="relative min-w-[120px]">
                                <button type="button" @click="dayDropdownOpen = !dayDropdownOpen"
                                    class="w-full bg-slate-50 hover:bg-slate-100 dark:bg-slate-700/50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 text-sm font-semibold px-4 py-2.5 rounded-xl flex items-center justify-between gap-3 transition-colors focus:ring-2 focus:ring-[#FF5B00]/50 outline-none">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-[#FF5B00]" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <span x-text="activeDay">DAY 1</span>
                                    </span>
                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-300"
                                        :class="dayDropdownOpen ? 'rotate-180' : ''" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <!-- Dropdown Menu -->
                                <div x-show="dayDropdownOpen" @click.away="dayDropdownOpen = false"
                                    x-transition.opacity.duration.200ms x-cloak
                                    class="absolute right-0 mt-2 w-full bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl shadow-lg shadow-black/5 py-1.5 z-20">
                                    <template x-for="day in ['DAY 1', 'DAY 2', 'DAY 3']">
                                        <button type="button" @click="activeDay = day; dayDropdownOpen = false"
                                            class="w-full text-left px-4 py-2.5 text-sm font-medium transition-colors"
                                            :class="activeDay === day ? 'bg-orange-50 dark:bg-slate-700/80 text-[#FF5B00]' :
                                                'hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-600 dark:text-slate-300'">
                                            <span x-text="day"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-slate-50/50 dark:bg-slate-900/30 rounded-2xl p-2 sm:p-4 border border-slate-100 dark:border-slate-800">
                            <!-- Checklist Items: DAY 1 -->
                            <div x-show="activeDay === 'DAY 1'" x-transition.opacity.duration.300ms
                                class="space-y-1.5">
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
                                    <label
                                        class="flex items-center gap-3.5 cursor-pointer p-3 rounded-xl hover:bg-white dark:hover:bg-slate-800 transition-colors group select-none"
                                        @click="toggleItem('{{ $key }}')">
                                        <div class="relative w-5 h-5 rounded-md border-2 flex items-center justify-center transition-all duration-200"
                                            :class="isItemChecked('{{ $key }}') ?
                                                'bg-[#FF5B00] border-[#FF5B00] shadow-sm' :
                                                'border-slate-300 dark:border-slate-500 group-hover:border-[#FF5B00]/50 bg-white dark:bg-slate-900'">
                                            <svg x-show="isItemChecked('{{ $key }}')" x-transition.scale
                                                class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor"
                                                stroke-width="3" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <span class="text-sm sm:text-base font-medium transition-colors duration-200"
                                            :class="isItemChecked('{{ $key }}') ?
                                                'line-through text-slate-400 dark:text-slate-500' :
                                                'text-slate-700 dark:text-slate-200 group-hover:text-[#FF5B00] dark:group-hover:text-orange-400'">
                                            {{ $label }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>

                            <!-- Checklist Items: DAY 2 -->
                            <div x-show="activeDay === 'DAY 2'" x-transition.opacity.duration.300ms x-cloak
                                class="space-y-1.5">
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
                                    <label
                                        class="flex items-center gap-3.5 cursor-pointer p-3 rounded-xl hover:bg-white dark:hover:bg-slate-800 transition-colors group select-none"
                                        @click="toggleItem('{{ $key }}')">
                                        <div class="relative w-5 h-5 rounded-md border-2 flex items-center justify-center transition-all duration-200"
                                            :class="isItemChecked('{{ $key }}') ?
                                                'bg-[#FF5B00] border-[#FF5B00] shadow-sm' :
                                                'border-slate-300 dark:border-slate-500 group-hover:border-[#FF5B00]/50 bg-white dark:bg-slate-900'">
                                            <svg x-show="isItemChecked('{{ $key }}')" x-transition.scale
                                                class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor"
                                                stroke-width="3" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <span class="text-sm sm:text-base font-medium transition-colors duration-200"
                                            :class="isItemChecked('{{ $key }}') ?
                                                'line-through text-slate-400 dark:text-slate-500' :
                                                'text-slate-700 dark:text-slate-200 group-hover:text-[#FF5B00] dark:group-hover:text-orange-400'">
                                            {{ $label }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>

                            <!-- Checklist Items: DAY 3 -->
                            <div x-show="activeDay === 'DAY 3'" x-transition.opacity.duration.300ms x-cloak
                                class="space-y-1.5">
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
                                    <label
                                        class="flex items-center gap-3.5 cursor-pointer p-3 rounded-xl hover:bg-white dark:hover:bg-slate-800 transition-colors group select-none"
                                        @click="toggleItem('{{ $key }}')">
                                        <div class="relative w-5 h-5 rounded-md border-2 flex items-center justify-center transition-all duration-200"
                                            :class="isItemChecked('{{ $key }}') ?
                                                'bg-[#FF5B00] border-[#FF5B00] shadow-sm' :
                                                'border-slate-300 dark:border-slate-500 group-hover:border-[#FF5B00]/50 bg-white dark:bg-slate-900'">
                                            <svg x-show="isItemChecked('{{ $key }}')" x-transition.scale
                                                class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor"
                                                stroke-width="3" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <span class="text-sm sm:text-base font-medium transition-colors duration-200"
                                            :class="isItemChecked('{{ $key }}') ?
                                                'line-through text-slate-400 dark:text-slate-500' :
                                                'text-slate-700 dark:text-slate-200 group-hover:text-[#FF5B00] dark:group-hover:text-orange-400'">
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

            0%,
            100% {
                transform: rotate(0deg);
            }

            25% {
                transform: rotate(15deg);
            }

            75% {
                transform: rotate(-15deg);
            }
        }

        .animate-wave {
            animation: wave 2s infinite ease-in-out;
        }
    </style>
</x-app-layout>
