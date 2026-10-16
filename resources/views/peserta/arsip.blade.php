<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-dvh">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Arsip Tugas - METASTRO 2026</title>

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
            <div class="flex items-center gap-3 py-2 mb-4">
                <a href="{{ route('peserta.dashboard') }}" class="p-2 -ml-2 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" title="Kembali ke Dashboard">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                <h2 class="font-bold text-lg text-[#1E293B] dark:text-white">Arsip Tugas</h2>
            </div>

            <!-- FLASH NOTIFICATIONS -->
            @if(session('success'))
            <div class="mb-4 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-800 dark:text-emerald-200 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            <!-- CONTENT -->
            <div>
                <div class="bg-white dark:bg-slate-800/90 border border-slate-100 dark:border-slate-700/60 rounded-2xl p-4 sm:p-5 shadow-xs mb-5">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-bold text-sm sm:text-base text-[#1E293B] dark:text-white">
                            Total Tugas Terkumpul
                        </h3>
                        <span class="font-bold text-sm text-emerald-600 dark:text-emerald-400">
                            {{ $progressPercent }}% ({{ $totalSubmitted }}/{{ $totalTugas }})
                        </span>
                    </div>
                    <div class="w-full h-2.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-500 rounded-full transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
                    </div>
                </div>

                <div class="space-y-4">
                    @forelse($submissions as $sub)
                    <div class="bg-white dark:bg-slate-800/90 border border-slate-100 dark:border-slate-700/60 rounded-2xl p-5 shadow-xs">
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <div class="flex items-center gap-2">
                                @if($sub->tugas?->jenis === 'individu')
                                    <span class="bg-[#FF1A1A] text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full">Individu</span>
                                @elseif($sub->tugas?->jenis === 'tim')
                                    <span class="bg-[#4299E1] text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full">Regu: {{ $sub->tim?->nama ?? '-' }}</span>
                                @else
                                    <span class="bg-emerald-500 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full">Angkatan</span>
                                @endif

                                @if($sub->tugas?->hari)
                                    <span class="bg-orange-100 dark:bg-orange-950/60 text-[#FF5B00] dark:text-orange-400 text-[10px] font-bold px-2 py-0.5 rounded-full">DAY {{ $sub->tugas->hari }}</span>
                                @endif
                            </div>

                            <!-- Status Badge -->
                            <div>
                                @if($sub->status === 'reviewed')
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        Disetujui
                                    </span>
                                @elseif($sub->status === 'rejected')
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-red-100 text-red-800 dark:bg-red-950/60 dark:text-red-300">
                                        Perlu Revisi
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                        Menunggu Review
                                    </span>
                                @endif
                            </div>
                        </div>

                        <h4 class="font-bold text-sm sm:text-base text-[#1E293B] dark:text-white mb-2">
                            {{ $sub->tugas?->judul ?? 'Tugas #' . $sub->tugas_id }}
                        </h4>
                        
                        <div class="text-xs text-slate-600 dark:text-slate-400 space-y-1 mb-4 bg-slate-50 dark:bg-slate-900/60 p-3 rounded-xl">
                            @if($sub->tugas?->tenggat_waktu)
                                <p><span class="font-medium text-slate-700 dark:text-slate-300">Deadline:</span> {{ \Carbon\Carbon::parse($sub->tugas->tenggat_waktu)->translatedFormat('d F Y, H:i') }} WIB</p>
                            @endif
                            <p><span class="font-medium text-slate-700 dark:text-slate-300">Dikumpulkan:</span> {{ \Carbon\Carbon::parse($sub->tanggal_pengumpulan)->translatedFormat('d F Y, H:i') }} WIB</p>
                            @if($sub->catatan_peserta && $sub->catatan_peserta !== '-')
                                <p><span class="font-medium text-slate-700 dark:text-slate-300">Catatan saya:</span> {{ $sub->catatan_peserta }}</p>
                            @endif
                            @if($sub->catatan_pemeriksa)
                                <p class="pt-1 border-t border-slate-200 dark:border-slate-800 {{ $sub->status === 'rejected' ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-slate-600 dark:text-slate-300' }}">
                                    <span>Catatan Pemeriksa:</span> {{ $sub->catatan_pemeriksa }}
                                </p>
                            @endif
                        </div>

                        <div class="flex items-center justify-center gap-2 pt-1">
                            <a href="{{ asset('storage/' . ltrim($sub->file_path, '/')) }}" target="_blank"
                                class="bg-[#FFF2CC] dark:bg-amber-400 hover:bg-[#FFE599] dark:hover:bg-amber-300 text-[#1E293B] font-bold text-xs px-6 py-2 rounded-xl transition shadow-2xs inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span>Lihat Berkas</span>
                            </a>

                            @if($sub->tugas)
                            <a href="{{ route('peserta.tugas.show-kumpulkan', $sub->tugas) }}"
                                class="bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold text-xs px-5 py-2 rounded-xl transition shadow-2xs">
                                {{ $sub->status === 'rejected' ? 'Revisi Tugas' : 'Detail Tugas' }}
                            </a>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700">
                        <div class="w-12 h-12 mx-auto mb-3 text-slate-300 dark:text-slate-600 flex items-center justify-center">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Belum ada tugas yang dikumpulkan</p>
                        <p class="text-xs text-slate-400 mt-1">Kumpulkan tugas Anda melalui menu List Penugasan.</p>
                        <a href="{{ route('peserta.tugas-list') }}" class="inline-block mt-4 bg-[#FF5B00] hover:bg-[#e05000] text-white text-xs font-bold px-6 py-2 rounded-xl transition shadow-xs">
                            Buka List Penugasan
                        </a>
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
