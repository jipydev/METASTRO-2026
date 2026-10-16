<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-dvh">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kumpulkan Tugas - METASTRO 2026</title>

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
        agreed: false,
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
                <a href="{{ route('peserta.tugas-list') }}" class="p-2 -ml-2 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" title="Kembali ke List Penugasan">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                <h2 class="font-bold text-lg text-[#1E293B] dark:text-white">Kumpulkan Tugas</h2>
            </div>

            <!-- FLASH NOTIFICATIONS -->
            @if(session('success'))
            <div class="mb-4 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-800 dark:text-emerald-200 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 p-4 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-xs text-red-800 dark:text-red-200 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span>{{ session('error') }}</span>
            </div>
            @endif

            <!-- CONTENT CARD -->
            <div class="bg-white dark:bg-slate-800/90 rounded-2xl p-5 sm:p-6 shadow-xs border border-slate-100 dark:border-slate-700/60">
                <div class="flex items-center justify-between mb-4">
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

                    @if($tugas->tenggat_waktu)
                    <p class="text-right text-xs italic text-slate-500 dark:text-slate-400">
                        Deadline: {{ \Carbon\Carbon::parse($tugas->tenggat_waktu)->translatedFormat('d F Y, H:i') }} WIB
                    </p>
                    @endif
                </div>

                <h3 class="font-bold text-lg text-[#1E293B] dark:text-white mb-3">
                    {{ $tugas->judul }}
                </h3>

                <!-- PREVIOUS SUBMISSION BANNER -->
                @if($submission)
                    @if($submission->status === 'rejected')
                        <div class="mb-5 p-3.5 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900/60 text-xs">
                            <div class="flex items-center gap-1.5 font-bold text-red-700 dark:text-red-400 mb-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span>Pengumpulan Perlu Direvisi</span>
                            </div>
                            <p class="text-slate-600 dark:text-slate-300">
                                Catatan Pemeriksa: <span class="font-medium italic text-red-800 dark:text-red-300">{{ $submission->catatan_pemeriksa ?: 'Tidak ada catatan khusus.' }}</span>
                            </p>
                            <div class="mt-2 text-[11px] text-slate-500">
                                Berkas sebelumnya: <a href="{{ asset('storage/' . ltrim($submission->file_path, '/')) }}" target="_blank" class="text-[#FF5B00] underline font-semibold">Unduh Berkas Lama</a>
                            </div>
                        </div>
                    @elseif($submission->status === 'reviewed')
                        <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900/60 text-xs">
                            <div class="flex items-center gap-1.5 font-bold text-emerald-700 dark:text-emerald-400 mb-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Tugas Telah Disetujui</span>
                            </div>
                            @if($submission->catatan_pemeriksa)
                            <p class="text-slate-600 dark:text-slate-300">
                                Komentar Pemeriksa: <span class="font-medium">{{ $submission->catatan_pemeriksa }}</span>
                            </p>
                            @endif
                            <div class="mt-2 text-[11px]">
                                Berkas terunggah: <a href="{{ asset('storage/' . ltrim($submission->file_path, '/')) }}" target="_blank" class="text-emerald-600 dark:text-emerald-400 underline font-semibold">Buka File</a>
                            </div>
                        </div>
                    @else
                        <div class="mb-5 p-3.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/60 text-xs">
                            <div class="flex items-center gap-1.5 font-bold text-amber-800 dark:text-amber-400 mb-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Menunggu Review Panitia</span>
                            </div>
                            <p class="text-slate-600 dark:text-slate-300 text-[11px]">
                                Terakhir dikumpulkan: {{ \Carbon\Carbon::parse($submission->tanggal_pengumpulan)->translatedFormat('d F Y, H:i') }} WIB. Anda dapat mengunggah revisi baru di bawah ini.
                            </p>
                            <div class="mt-1 text-[11px]">
                                Berkas saat ini: <a href="{{ asset('storage/' . ltrim($submission->file_path, '/')) }}" target="_blank" class="text-[#FF5B00] underline font-semibold">Lihat Berkas Saya</a>
                            </div>
                        </div>
                    @endif
                @endif

                <div class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed mb-6 space-y-3">
                    <p class="font-semibold text-slate-700 dark:text-slate-200">
                        Deskripsi Penugasan:<br>
                        <span class="font-normal text-slate-600 dark:text-slate-400 whitespace-pre-line">
                            {!! nl2br(e($tugas->deskripsi)) !!}
                        </span>
                    </p>

                    <p class="text-slate-500 dark:text-slate-400 pt-2 border-t border-slate-100 dark:border-slate-700/40 text-[11px]">
                        Pastikan file sudah sesuai dengan format yang telah ditentukan sebelum mengunggah. File yang sudah dikumpulkan tidak dapat dibatalkan setelah tenggat waktu berakhir.
                    </p>
                </div>

                <!-- SUBMISSION FORM -->
                <form method="POST" action="{{ route('peserta.tugas.store-kumpulkan', $tugas) }}" enctype="multipart/form-data" class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700/60">
                    @csrf

                    <!-- FILE INPUT -->
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-[#1E293B] dark:text-slate-200 mb-1.5">
                            Unggah Berkas Tugas <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="file" required accept=".pdf,.doc,.docx,.zip,.jpg,.jpeg,.png"
                            class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#FF5B00] file:text-white hover:file:bg-[#e05000] file:cursor-pointer border border-slate-200 dark:border-slate-700 rounded-xl p-1.5 dark:bg-slate-900 cursor-pointer">
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1.5">
                            Format didukung: PDF, DOC, DOCX, ZIP, JPG, PNG (Maksimal 10MB).
                        </p>
                        @error('file')
                            <p class="text-[11px] text-red-500 font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- CATATAN PESERTA -->
                    <div class="mb-5">
                        <label for="catatan_peserta" class="block text-xs font-bold text-[#1E293B] dark:text-slate-200 mb-1.5">
                            Catatan untuk Pemeriksa <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <textarea id="catatan_peserta" name="catatan_peserta" rows="3" placeholder="Tuliskan catatan tambahan jika ada..."
                            class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:border-[#FF5B00] focus:ring-[#FF5B00] transition">{{ old('catatan_peserta', $submission?->catatan_peserta !== '-' ? $submission?->catatan_peserta : '') }}</textarea>
                        @error('catatan_peserta')
                            <p class="text-[11px] text-red-500 font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- ACTION AREA -->
                    <div class="flex flex-col items-center pt-2">
                        <label class="flex items-start gap-2.5 cursor-pointer mb-5 max-w-[340px] select-none">
                            <input type="checkbox" name="agreed" value="1" x-model="agreed" required class="mt-0.5 w-4 h-4 rounded border-red-400 text-red-500 focus:ring-red-500 cursor-pointer">
                            <span class="text-[10px] text-red-500 dark:text-red-400 leading-tight">
                                Saya telah membaca deskripsi tugas dengan baik dan mengerjakan tugas sesuai dengan ketentuan.
                            </span>
                        </label>
                        @error('agreed')
                            <p class="text-[11px] text-red-500 font-medium mb-3">{{ $message }}</p>
                        @enderror

                        @php
                            $isPastDeadline = $tugas->tenggat_waktu && \Carbon\Carbon::parse($tugas->tenggat_waktu)->isPast();
                            $isLocked = $isPastDeadline && (! $submission || $submission->status !== 'rejected');
                        @endphp

                        @if($isLocked)
                            <div class="text-center p-3 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-500 text-xs font-semibold w-full sm:w-auto">
                                Pengumpulan Ditutup (Melewati Batas Waktu)
                            </div>
                        @else
                            <button type="submit" :disabled="!agreed"
                                :class="agreed ? 'bg-[#FF5B00] hover:bg-[#e05000] text-white cursor-pointer shadow-md' : 'bg-slate-300 dark:bg-slate-700 text-slate-500 dark:text-slate-400 cursor-not-allowed'"
                                class="text-xs font-bold px-8 py-3 rounded-xl transition w-full sm:w-auto text-center">
                                {{ $submission ? 'Kumpulkan Pembaruan Berkas' : 'Kumpulkan Tugas Disini' }}
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- FOOTER BRANDING -->
        <footer class="text-center py-4 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-400 mt-8">
            &copy; 2026 METASTRO &bull; Spirit of HIRO, Heart of SOLDER
        </footer>
    </div>
</body>
</html>
