<x-app-layout :$title>
    <div class="py-8 font-poppins min-h-screen bg-brand-50 dark:bg-slate-900 transition-colors duration-200">
        <div class="max-w-md mx-auto px-4">

            {{-- Kartu Utama --}}
            <div class="bg-white dark:bg-slate-800 rounded-[32px] p-6 sm:p-8 shadow-sm border border-slate-100 dark:border-slate-700/80">

                {{-- Header Tombol Kembali & Judul --}}
                <div class="mb-6 flex flex-col items-start">
                    <a href="{{ route('dashboard') }}"
                        class="mb-3 inline-flex items-center gap-1.5 text-slate-500 dark:text-slate-400 hover:text-brand-600 dark:hover:text-brand-400 transition font-semibold text-xs">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                            stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                        </svg>
                        <span>Kembali ke Dashboard</span>
                    </a>

                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white font-oswald tracking-tight">
                        QR Absensi Saya
                    </h1>
                </div>

                {{-- Info Profil Singkat --}}
                <div class="flex items-center gap-4 mb-6 p-3.5 bg-slate-50/80 dark:bg-slate-700/40 rounded-2xl border border-slate-100 dark:border-slate-700/60">
                    @php
                        $photoUrl = $user->foto
                            ? asset('storage/' . $user->foto)
                            : 'https://ui-avatars.com/api/?size=256&background=fe5a1d&color=fff&name=' .
                                urlencode($user->nama);
                    @endphp

                    <img src="{{ $photoUrl }}" alt="Foto {{ $user->nama }}"
                        class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl border-2 border-brand-500/80 object-cover shadow-sm shrink-0">

                    <div class="min-w-0">
                        <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white truncate">
                            {{ $user->nama }}
                        </h3>

                        <p class="text-xs text-slate-600 dark:text-slate-400 font-semibold truncate mt-0.5">
                            {{ $user->getFormattedDivisiJabatanAttribute() }}
                        </p>

                        <p class="text-[11px] font-mono text-slate-400 dark:text-slate-400 mt-0.5">
                            NIM: {{ $user->nim }}
                        </p>
                    </div>
                </div>

                {{-- Kartu QR Code --}}
                <div class="bg-gradient-to-b from-slate-50 to-slate-50/50 dark:from-slate-700/60 dark:to-slate-800/60 rounded-3xl p-6 sm:p-7 flex flex-col justify-center items-center border border-slate-200/60 dark:border-slate-600/60">
                    @if (isset($qrUrl) && $qrUrl)
                        <div class="p-4 bg-white dark:bg-white rounded-2xl shadow-sm border border-slate-200/80 transition transform hover:scale-[1.02]">
                            <img src="{{ $qrUrl }}" alt="QR Code Absensi"
                                class="w-48 h-48 sm:w-52 sm:h-52 object-contain mx-auto">
                        </div>

                        <p class="text-xs text-slate-600 dark:text-slate-300 mt-4 text-center font-medium leading-relaxed">
                            Tunjukkan QR Code ini kepada petugas saat melakukan presensi kegiatan.
                        </p>

                        @if ($qrGeneratedAt)
                            <div class="mt-3 inline-flex items-center gap-1.5 px-3 py-1 bg-slate-200/60 dark:bg-slate-700/80 rounded-full text-[11px] font-medium text-slate-600 dark:text-slate-300">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Diperbarui {{ $qrGeneratedAt->diffForHumans() }}</span>
                            </div>
                        @endif
                    @else
                        <div class="w-48 h-48 sm:w-52 sm:h-52 flex flex-col items-center justify-center bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-600 p-4 text-center shadow-inner">
                            <span class="text-3xl mb-2">⚠️</span>
                            <p class="text-slate-500 dark:text-slate-400 text-xs font-medium leading-relaxed">
                                QR Code belum tersedia.<br>Silakan hubungi administrator.
                            </p>
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </div>
</x-app-layout>