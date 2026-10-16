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
            <div class="flex items-center justify-between py-2 mb-3">
                <div class="flex items-center gap-3">
                    <a href="{{ route('peserta.tugas-hari') }}" class="p-2 -ml-2 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" title="Kembali ke Pilihan Hari">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                        </svg>
                    </a>
                    <h2 class="font-bold text-lg text-[#1E293B] dark:text-white">
                        List Penugasan {{ $selectedHari ? 'DAY ' . $selectedHari : '' }}:
                    </h2>
                </div>
                @if($selectedHari)
                <a href="{{ route('peserta.tugas-list') }}" class="text-xs text-[#FF5B00] hover:underline font-semibold">
                    Lihat Semua Day
                </a>
                @endif
            </div>

            <!-- SORTING & FILTERS -->
            <div class="mb-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-500 dark:text-slate-400">Sorting:</span>
                        <a href="{{ route('peserta.tugas-list', array_merge(request()->query(), ['sort' => $currentSort === 'deadline' ? 'terbaru' : 'deadline'])) }}"
                            class="bg-[#FF7A00] hover:bg-[#e06c00] text-white text-xs font-semibold px-3 py-1 rounded-lg flex items-center gap-1.5 transition shadow-2xs">
                            {{ $currentSort === 'deadline' ? 'Deadline' : 'Terbaru' }}
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                        </a>
                    </div>
                    <span class="text-xs text-slate-400">
                        Total: <strong class="text-slate-700 dark:text-slate-200">{{ $tugases->count() }}</strong> tugas
                    </span>
                </div>

                <!-- TABS -->
                <div class="flex gap-2 text-xs font-semibold overflow-x-auto pb-1 scrollbar-hide">
                    <button type="button" @click="tab = 'semua'" :class="tab == 'semua' ? 'bg-[#006C7F] text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200'" class="px-4 py-2 rounded-xl whitespace-nowrap transition cursor-pointer">Semua</button>
                    <button type="button" @click="tab = 'individu'" :class="tab == 'individu' ? 'bg-[#006C7F] text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200'" class="px-4 py-2 rounded-xl whitespace-nowrap transition cursor-pointer">Individu</button>
                    <button type="button" @click="tab = 'tim'" :class="tab == 'tim' ? 'bg-[#006C7F] text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200'" class="px-4 py-2 rounded-xl whitespace-nowrap transition cursor-pointer">Regu / Tim</button>
                    <button type="button" @click="tab = 'angkatan'" :class="tab == 'angkatan' ? 'bg-[#006C7F] text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200'" class="px-4 py-2 rounded-xl whitespace-nowrap transition cursor-pointer">Angkatan</button>
                </div>
            </div>

            <!-- TASK LIST -->
            <div class="space-y-4">
                @forelse($tugases as $tugas)
                <div x-show="tab == 'semua' || tab == '{{ $tugas->jenis }}'" class="bg-white dark:bg-slate-800/90 border border-slate-100 dark:border-slate-700/60 rounded-2xl p-5 shadow-xs transition hover:shadow-md">
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <div class="flex items-center gap-2">
                            @if($tugas->jenis === 'individu')
                                <span class="bg-[#FF1A1A] text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full">Individu</span>
                            @elseif($tugas->jenis === 'tim')
                                <span class="bg-[#4299E1] text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full">Regu</span>
                            @else
                                <span class="bg-emerald-500 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full">Angkatan</span>
                            @endif

                            @if($tugas->hari)
                                <span class="bg-orange-100 dark:bg-orange-950/60 text-[#FF5B00] dark:text-orange-400 text-[10px] font-bold px-2 py-0.5 rounded-full">DAY {{ $tugas->hari }}</span>
                            @endif
                        </div>

                        <!-- Submission Status Badge -->
                        <div>
                            @if($tugas->is_submitted)
                                @php $subStatus = $tugas->user_submission?->status; @endphp
                                @if($subStatus === 'reviewed')
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        Disetujui
                                    </span>
                                @elseif($subStatus === 'rejected')
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-red-100 text-red-800 dark:bg-red-950/60 dark:text-red-300">
                                        Perlu Revisi
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                        Menunggu Review
                                    </span>
                                @endif
                            @else
                                @php
                                    $isExpired = $tugas->tenggat_waktu && \Carbon\Carbon::parse($tugas->tenggat_waktu)->isPast();
                                @endphp
                                @if($isExpired)
                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400">
                                        Lewat Tenggat
                                    </span>
                                @else
                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                                        Belum Dikumpulkan
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>

                    <h3 class="font-bold text-sm sm:text-base text-[#1E293B] dark:text-white mb-1.5">
                        {{ $tugas->judul }}
                    </h3>

                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-3 line-clamp-3">
                        {{ strip_tags($tugas->deskripsi) }}
                    </p>

                    @if($tugas->tenggat_waktu)
                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mb-4 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Deadline: <strong class="text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($tugas->tenggat_waktu)->translatedFormat('d F Y, H:i') }} WIB</strong></span>
                    </div>
                    @endif

                    <div class="flex justify-center pt-2 border-t border-slate-50 dark:border-slate-700/50">
                        @if($tugas->is_submitted && $tugas->user_submission?->status === 'reviewed')
                            <a href="{{ route('peserta.arsip') }}" class="bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 text-emerald-700 dark:text-emerald-300 font-bold text-xs px-8 py-2 rounded-xl transition shadow-2xs inline-block">
                                Lihat di Arsip
                            </a>
                        @elseif($tugas->is_submitted)
                            <a href="{{ route('peserta.tugas.show-kumpulkan', $tugas) }}" class="bg-[#FFF2CC] dark:bg-amber-400 hover:bg-[#FFE599] dark:hover:bg-amber-300 text-[#1E293B] font-bold text-xs px-8 py-2 rounded-xl transition shadow-2xs inline-block">
                                {{ $tugas->user_submission?->status === 'rejected' ? 'Kumpulkan Revisi' : 'Lihat / Ubah Berkas' }}
                            </a>
                        @else
                            <a href="{{ route('peserta.tugas.show-kumpulkan', $tugas) }}" class="bg-[#FFF2CC] dark:bg-amber-400 hover:bg-[#FFE599] dark:hover:bg-amber-300 text-[#1E293B] font-bold text-xs px-8 py-2 rounded-xl transition shadow-2xs inline-block">
                                Kumpulkan
                            </a>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-12 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Tidak ada tugas pada kategori ini.</p>
                </div>
                @endforelse
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
