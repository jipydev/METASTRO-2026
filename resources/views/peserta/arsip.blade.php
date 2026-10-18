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

            <!-- CONTENT -->
            <div>
                <h3 class="font-bold text-base text-[#1E293B] dark:text-white mb-4">
                    Total tugas telah terkumpul: <span class="text-[#00A82D] dark:text-emerald-400">{{ $persentase }}%</span>
                </h3>

                <div class="space-y-4">
                    @forelse($submissions as $submisi)
                        <div class="bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl p-5 shadow-xs">
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <div class="flex items-center gap-2">
                                    @if($submisi->tugas?->jenis === 'individu')
                                        <span class="bg-rose-500/10 text-rose-600 dark:text-rose-400 text-[10px] font-bold px-2.5 py-0.5 rounded-full border border-rose-500/20">Individu</span>
                                    @elseif($submisi->tugas?->jenis === 'tim')
                                        <span class="bg-sky-500/10 text-sky-600 dark:text-sky-400 text-[10px] font-bold px-2.5 py-0.5 rounded-full border border-sky-500/20">Regu / Tim</span>
                                    @else
                                        <span class="bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold px-2.5 py-0.5 rounded-full border border-emerald-500/20">Angkatan</span>
                                    @endif
                                    <h4 class="font-bold text-sm text-[#1E293B] dark:text-white">
                                        {{ $submisi->tugas?->judul ?? 'Tugas' }}
                                    </h4>
                                </div>
                                <div>
                                    @if($submisi->status === 'reviewed')
                                        <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700">Disetujui</span>
                                    @elseif($submisi->status === 'rejected')
                                        <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-300 dark:border-rose-700">Perlu Revisi</span>
                                    @else
                                        <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-300 dark:border-amber-700">Menunggu Review</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="text-xs text-slate-600 dark:text-slate-400 space-y-1 mb-4">
                                @if($submisi->tugas?->tenggat_waktu)
                                    <p><span class="font-medium text-slate-700 dark:text-slate-300">Deadline tugas:</span> {{ $submisi->tugas->tenggat_waktu->translatedFormat('d F Y H:i') }} WIB</p>
                                @endif
                                <p><span class="font-medium text-slate-700 dark:text-slate-300">Dikumpulkan:</span> {{ $submisi->tanggal_pengumpulan ? $submisi->tanggal_pengumpulan->translatedFormat('d F Y H:i') . ' WIB' : '-' }}</p>
                                @if($submisi->original_participant_name)
                                    <p><span class="font-medium text-slate-700 dark:text-slate-300">Pengunggah:</span> {{ $submisi->original_participant_name }}</p>
                                @endif
                            </div>

                            <div class="flex items-center justify-between gap-3 pt-3 border-t border-slate-100 dark:border-slate-700/60">
                                <a href="{{ asset('storage/' . ltrim($submisi->file_path, '/')) }}" target="_blank" class="bg-orange-50 dark:bg-orange-950/40 hover:bg-orange-100 text-[#FF5B00] font-bold text-xs px-4 py-2 rounded-xl transition flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>Lihat File PDF</span>
                                </a>

                                @if($submisi->tugas)
                                    <a href="{{ route('peserta.tugas-kumpulkan', $submisi->tugas->id) }}" class="text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-[#FF5B00]">
                                        Detail Submisi &rarr;
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700">
                            <p class="text-xs text-slate-500">Belum ada tugas yang dikumpulkan.</p>
                            <a href="{{ route('peserta.tugas-list') }}" class="inline-block mt-3 text-xs font-bold text-[#FF5B00] hover:underline">
                                Lihat List Penugasan &rarr;
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
