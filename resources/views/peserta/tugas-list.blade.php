<x-app-layout :$title>
    <div x-data="{
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
    }"
        class="bg-slate-50/50 dark:bg-slate-900 min-h-screen pb-12 font-poppins transition-colors duration-300">

        <div class="w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">

            <div class="space-y-6 sm:space-y-8">

                <!-- PAGE HEADER -->
                <div class="flex items-center gap-3 py-3">
                    <a href="{{ route('dashboard.index') }}"
                        class="p-2 -ml-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-200/60 dark:hover:bg-slate-800 transition-colors"
                        title="Kembali ke Dashboard">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                        </svg>
                    </a>
                    <div>
                        <h2 class="font-bold text-xl sm:text-2xl text-slate-900 dark:text-white tracking-tight">List
                            Penugasan</h2>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                            @if (isset($tim) && $tim)
                                Regu: <span class="font-semibold text-[#FF5B00]">{{ $tim->nama }}</span>
                                &bull;
                            @endif
                            Pilih &amp; selesaikan penugasan tepat waktu
                        </p>
                    </div>
                </div>

                <!-- SEARCH & FILTER FORM -->
                <form method="GET" action="{{ route('peserta.tugas-list') }}" class="space-y-2">
                    <div class="relative">
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari penugasan..."
                            class="w-full pl-10 pr-4 py-3 bg-white dark:bg-slate-800/90 border border-slate-200/70 dark:border-slate-700/60 rounded-2xl text-xs sm:text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#FF5B00]/40 transition shadow-xs">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        @if (!empty($search) || !empty($dayFilter))
                            <a href="{{ route('peserta.tugas-list') }}"
                                class="absolute right-3.5 top-3 text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                Reset
                            </a>
                        @endif
                    </div>

                    @if (!empty($dayFilter))
                        <input type="hidden" name="day" value="{{ $dayFilter }}">
                        <div
                            class="flex items-center gap-2 text-xs text-[#FF5B00] bg-orange-50 dark:bg-orange-950/40 px-3.5 py-2 rounded-xl border border-orange-200/50 dark:border-orange-900/40">
                            <span>Filter Aktif: <strong>{{ $dayFilter }}</strong></span>
                            <a href="{{ route('peserta.tugas-list', ['search' => $search ?? '']) }}"
                                class="ml-auto font-bold text-xs hover:underline">Hapus Filter</a>
                        </div>
                    @endif
                </form>

                <!-- TABS FILTER -->
                <div
                    class="flex gap-1.5 p-1.5 bg-slate-200/50 dark:bg-slate-800/60 rounded-2xl text-xs font-medium overflow-x-auto scrollbar-hide border border-slate-200/40 dark:border-slate-700/40">
                    <button type="button" @click="tab = 'semua'"
                        :class="tab == 'semua' ?
                            'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-xs font-semibold' :
                            'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'"
                        class="flex-1 py-2 px-3 rounded-xl whitespace-nowrap transition-all duration-200 text-center cursor-pointer">Semua</button>
                    <button type="button" @click="tab = 'individu'"
                        :class="tab == 'individu' ?
                            'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-xs font-semibold' :
                            'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'"
                        class="flex-1 py-2 px-3 rounded-xl whitespace-nowrap transition-all duration-200 text-center cursor-pointer">Individu</button>
                    <button type="button" @click="tab = 'tim'"
                        :class="tab == 'tim' ?
                            'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-xs font-semibold' :
                            'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'"
                        class="flex-1 py-2 px-3 rounded-xl whitespace-nowrap transition-all duration-200 text-center cursor-pointer">Regu
                        / Tim</button>
                    <button type="button" @click="tab = 'angkatan'"
                        :class="tab == 'angkatan' ?
                            'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-xs font-semibold' :
                            'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'"
                        class="flex-1 py-2 px-3 rounded-xl whitespace-nowrap transition-all duration-200 text-center cursor-pointer">Angkatan</button>
                </div>

                <!-- TASK LIST SECTION -->
                <section class="space-y-3.5">
                    @forelse($tasks ?? [] as $task)
                        <div x-show="tab === 'semua' || tab === '{{ $task['jenis'] }}'"
                            x-transition.opacity.duration.200ms
                            class="group bg-white dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700/60 hover:border-orange-300 dark:hover:border-orange-500/50 rounded-2xl p-5 shadow-sm transition-all duration-300">

                            <div class="flex items-start justify-between gap-2 mb-2.5">
                                <div>
                                    <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                                        @if ($task['jenis'] === 'individu')
                                            <span
                                                class="bg-rose-500/10 text-rose-600 dark:text-rose-400 text-[11px] font-semibold px-2.5 py-0.5 rounded-full shrink-0 border border-rose-500/20">Individu</span>
                                        @elseif($task['jenis'] === 'tim')
                                            <span
                                                class="bg-sky-500/10 text-sky-600 dark:text-sky-400 text-[11px] font-semibold px-2.5 py-0.5 rounded-full shrink-0 border border-sky-500/20">Regu
                                                / Tim</span>
                                        @else
                                            <span
                                                class="bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[11px] font-semibold px-2.5 py-0.5 rounded-full shrink-0 border border-emerald-500/20">Angkatan</span>
                                        @endif

                                        @if (!empty($task['day']))
                                            <span
                                                class="bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[10px] font-bold px-2 py-0.5 rounded-md border border-amber-500/20">
                                                {{ $task['day'] }}
                                            </span>
                                        @endif
                                    </div>
                                    <h3
                                        class="font-bold text-base text-slate-900 dark:text-white group-hover:text-[#FF5B00] transition-colors">
                                        {{ $task['judul'] }}
                                    </h3>
                                </div>

                                <!-- BADGE STATUS PENGERJAAN -->
                                <div class="shrink-0 text-right">
                                    @if ($task['status_pengerjaan'] === 'expired')
                                        <!-- Ketika sudah submit tapi telat -->
                                        <span
                                            class="inline-flex items-center gap-1 bg-rose-100 dark:bg-rose-950/50 text-rose-700 dark:text-rose-300 border border-rose-300 dark:border-rose-700 text-[10px] font-bold px-2.5 py-1 rounded-full">
                                            Terlambat Mengerjakan
                                        </span>
                                    @elseif ($task['status_pengerjaan'] === 'unsubmitted_expired')
                                        <!-- Ketika belum submit dan sudah lewat deadline -->
                                        <span
                                            class="inline-flex items-center gap-1 bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400 text-[10px] font-bold px-2.5 py-1 rounded-full">
                                            Belum Dikerjakan (Lewat Deadline)
                                        </span>
                                    @elseif ($task['status_pengerjaan'] === 'reviewed' || $task['status_pengerjaan'] === 'pending')
                                        <span
                                            class="inline-flex items-center gap-1 bg-emerald-100 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700 text-[10px] font-bold px-2.5 py-1 rounded-full">
                                            <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none"
                                                stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M4.5 12.75l6 6 9-13.5" />
                                            </svg>
                                            Sudah dikerjakan
                                        </span>
                                    @elseif($task['status_pengerjaan'] === 'rejected')
                                        <span
                                            class="inline-flex items-center gap-1 bg-rose-100 dark:bg-rose-950/50 text-rose-700 dark:text-rose-300 border border-rose-300 dark:border-rose-700 text-[10px] font-bold px-2.5 py-1 rounded-full">
                                            Perlu Revisi
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 text-[10px] font-medium px-2.5 py-1 rounded-full">
                                            Belum Dikerjakan
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <p
                                class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed mb-3.5 line-clamp-2">
                                {{ strip_tags($task['deskripsi']) ?: 'Tidak ada deskripsi rinci untuk penugasan ini.' }}
                            </p>

                            <!-- DEADLINE & ACTION -->
                            <div
                                class="flex items-center justify-between pt-3.5 border-t border-slate-100 dark:border-slate-700/60 flex-wrap gap-2">
                                <div
                                    class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>
                                        @if (!empty($task['tenggat_waktu']))
                                            Tenggat: <strong
                                                class="{{ $task['is_expired'] ? 'text-red-500' : 'text-slate-700 dark:text-slate-200' }}">{{ $task['tenggat_waktu']->translatedFormat('d M Y, H:i') }}
                                                WIB</strong>
                                        @else
                                            Tanpa tenggat waktu
                                        @endif
                                    </span>
                                </div>

                                <div>
                                    <a href="{{ route('peserta.tugas-kumpulkan', $task['id']) }}"
                                        class="inline-flex items-center gap-1.5 {{ !empty($task['submission']) ? 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-100' : 'bg-[#FF5B00] hover:bg-[#E05000] text-white shadow-md shadow-orange-500/20' }} font-semibold text-xs sm:text-sm px-4 py-2 rounded-xl transition-all hover:-translate-y-0.5 active:scale-95 cursor-pointer">
                                        <span>
                                            @if (!empty($task['submission']))
                                                Lihat / Edit Submisi
                                            @elseif(!empty($task['is_expired']))
                                                Detail Penugasan
                                            @else
                                                Kumpulkan Tugas
                                            @endif
                                        </span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                        </svg>
                                    </a>
                                </div>
                            </div>

                        </div>
                    @empty
                        <div
                            class="text-center py-12 bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/60 dark:border-slate-700/60 p-6 shadow-sm">
                            <div
                                class="w-12 h-12 rounded-full bg-orange-50 dark:bg-orange-500/10 flex items-center justify-center mx-auto text-[#FF5B00] mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h4 class="font-bold text-base text-slate-800 dark:text-slate-200">Belum ada penugasan</h4>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">Penugasan yang
                                diberikan oleh panitia akan muncul di sini.</p>
                        </div>
                    @endforelse
                </section>

            </div>

        </div>
    </div>
</x-app-layout>
