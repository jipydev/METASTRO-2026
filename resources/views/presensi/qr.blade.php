<x-app-layout :$title>
    <div class="py-8 font-poppins min-h-screen bg-brand-50 dark:bg-slate-900 transition-colors duration-200">
        <div class="max-w-md mx-auto px-4">

            {{-- Kartu Utama --}}
            <div class="bg-white dark:bg-slate-800 rounded-[32px] p-6 sm:p-8 shadow-sm border border-slate-100 dark:border-slate-700/80">

                {{-- Header Tombol Kembali & Judul --}}
                <div class="mb-6 flex flex-col items-start">
                    <a href="{{ route('dashboard.index') }}"
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
                            {{ $user->getFormattedDivisiJabatanOrTimAttribute() }}
                        </p>

                        <p class="text-[11px] font-mono text-slate-400 dark:text-slate-400 mt-0.5">
                            NIM: {{ $user->nim }}
                        </p>
                    </div>
                </div>

                {{-- Kartu QR Code & Kode Unik --}}
                <div x-data="{ copied: false }" class="bg-linear-to-b from-slate-50 to-slate-50/50 dark:from-slate-700/60 dark:to-slate-800/60 rounded-3xl p-6 sm:p-7 flex flex-col justify-center items-center border border-slate-200/60 dark:border-slate-600/60">
                    @if (isset($qrUrl) && $qrUrl)
                        <div class="p-4 bg-white dark:bg-white rounded-2xl shadow-sm border border-slate-200/80 transition transform hover:scale-[1.02]">
                            <img src="{{ $qrUrl }}" alt="QR Code Absensi"
                                class="w-48 h-48 sm:w-52 sm:h-52 object-contain mx-auto">
                        </div>
                    @else
                        <div class="w-48 h-48 sm:w-52 sm:h-52 flex flex-col items-center justify-center bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-600 p-4 text-center shadow-inner">
                            <span class="text-3xl mb-2">⚠️</span>
                            <p class="text-slate-500 dark:text-slate-400 text-xs font-medium leading-relaxed">
                                Gambar QR sedang disiapkan.<br>Gunakan kode unik di bawah ini.
                            </p>
                        </div>
                    @endif

                    {{-- Kode Unik Absensi (Fallback Manual) --}}
                    <div class="mt-5 flex flex-col items-center w-full">
                        <span class="text-[11px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider mb-2">
                            Kode Unik Absensi (Cadangan)
                        </span>

                        <div class="flex items-center gap-2 p-1.5 pl-4 bg-slate-100 dark:bg-slate-700/90 rounded-2xl border border-slate-200 dark:border-slate-600 shadow-inner">
                            <span class="font-mono text-xl sm:text-2xl font-black tracking-widest text-brand-600 dark:text-brand-400 select-all">
                                {{ $kodeUnik ?? $user->kode_unik ?? '-' }}
                            </span>

                            @if (!empty($user->kode_unik))
                                <button type="button"
                                    @click="navigator.clipboard.writeText('{{ $user->kode_unik }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                    class="p-2 bg-white dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 rounded-xl transition shadow-xs cursor-pointer flex items-center gap-1 text-[11px] font-semibold"
                                    title="Salin Kode Unik">
                                    <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    <svg x-show="copied" style="display: none;" class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span x-text="copied ? 'Tersalin' : 'Salin'"></span>
                                </button>
                            @endif
                        </div>

                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-3 text-center max-w-xs leading-relaxed">
                            Bila ada kendala teknis (layar rusak, HP mati, tidak ada sinyal, atau barcode tidak terbaca), sebutkan kode unik ini kepada petugas scan.
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>