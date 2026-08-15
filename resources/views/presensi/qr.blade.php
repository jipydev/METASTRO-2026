<x-app-layout :$title>
    <div class="py-8 font-poppins min-h-screen bg-gray-50 dark:bg-slate-900 transition-colors duration-200">
        <div class="max-w-md mx-auto px-4">

            {{-- Kartu Utama --}}
            <div class="bg-white dark:bg-slate-800 rounded-[28px] p-6 sm:p-7 shadow-sm border border-gray-100 dark:border-slate-700">

                {{-- Header Tombol Kembali --}}
                <div class="mb-6 flex flex-col items-start">
                    <a href="{{ route('dashboard') }}"
                       class="mb-2 inline-flex items-center gap-1.5 text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition font-medium text-xs sm:text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                        </svg>
                        <span>Kembali ke Dashboard</span>
                    </a>

                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        QR Absensi
                    </h1>
                </div>

                {{-- Info Profil Singkat --}}
                <div class="flex items-center gap-4 mb-6 p-3 bg-slate-50 dark:bg-slate-700/40 rounded-2xl border border-gray-100 dark:border-slate-700">
                    @php
                        $photoUrl = $user->foto
                            ? asset('storage/' . $user->foto)
                            : 'https://ui-avatars.com/api/?size=256&background=4f46e5&color=fff&name=' . urlencode($user->nama);
                    @endphp

                    <img src="{{ $photoUrl }}"
                         alt="Foto {{ $user->nama }}"
                         class="w-14 h-14 sm:w-16 sm:h-16 rounded-full border-2 border-indigo-500 object-cover shadow-sm shrink-0">

                    <div class="min-w-0">
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white truncate">
                            {{ $user->nama }}
                        </h3>

                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                            @php
                                $jabatanName = $user->jabatan?->nama;
                                $divisiName  = $user->divisi?->nama;
                                $hideDivisi  = in_array(strtolower($jabatanName ?? ''), ['ketua', 'wakil ketua', 'ketua pelaksana']);
                            @endphp

                            {{ $jabatanName ?? 'Peserta' }}@unless($hideDivisi){{ $divisiName ? ' - ' . $divisiName : '' }}@endunless
                        </p>

                        <p class="text-[11px] font-mono text-gray-400 dark:text-slate-500 mt-0.5">
                            NIM: {{ $user->nim }}
                        </p>
                    </div>
                </div>

                {{-- Kartu QR Code --}}
                <div class="bg-indigo-50/50 dark:bg-slate-700/60 rounded-3xl p-6 sm:p-8 flex flex-col justify-center items-center border border-indigo-100/60 dark:border-slate-600">
                    @if(isset($qrUrl) && $qrUrl)
                        <div class="p-3.5 bg-white rounded-2xl shadow-sm border border-gray-200/80">
                            <img src="{{ $qrUrl }}"
                                 alt="QR Code Absensi"
                                 class="w-48 h-48 sm:w-52 sm:h-52 object-contain">
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-4 text-center font-medium">
                            Tunjukkan QR Code ini ke petugas saat presensi kegiatan.
                        </p>
                    @else
                        <div class="w-48 h-48 sm:w-52 sm:h-52 flex flex-col items-center justify-center bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-600 p-4 text-center">
                            <span class="text-3xl mb-2">⚠️</span>
                            <p class="text-slate-500 dark:text-slate-400 text-xs font-medium">
                                QR Code belum tersedia.<br>Silakan hubungi administrator.
                            </p>
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </div>
</x-app-layout>