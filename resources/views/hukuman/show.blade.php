<x-app-layout :$title>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight">
                    {{ __('Detail Hukuman') }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    @if ($isTarget && ! $hukuman->isSelesai())
                        Ajukan pembelaan terlebih dahulu, lalu kerjakan tugas sesuai kategori
                    @else
                        Informasi lengkap hukuman
                    @endif
                </p>
            </div>

            @if ($isTarget)
                <a href="{{ route('hukuman.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-xs transition self-start sm:self-auto">
                    ← Riwayat Saya
                </a>
            @elseif (auth()->user()->canIssueHukumanRanger() && $hukuman->issuer_mode === 'ranger')
                <a href="{{ route('hukuman.kelola', 'ranger') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-xs transition self-start sm:self-auto">
                    ← Kelola
                </a>
            @elseif (auth()->user()->canIssueHukumanPengawas() && $hukuman->issuer_mode === 'pengawas')
                <a href="{{ route('hukuman.kelola', 'pengawas') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-xs transition self-start sm:self-auto">
                    ← Kelola
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 font-poppins space-y-5">
        @if (session('success'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-2xl text-emerald-700 dark:text-emerald-300 text-xs font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 rounded-2xl text-red-600 dark:text-red-400 text-xs font-semibold">
                {{ session('error') }}
            </div>
        @endif

        {{-- Ringkasan --}}
        <div class="bg-white dark:bg-slate-800 shadow-sm rounded-3xl p-6 sm:p-7 border border-gray-100 dark:border-slate-700 space-y-4">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold">Kategori</p>
                    <span class="inline-flex mt-1 px-3 py-1.5 rounded-xl text-sm font-bold {{ $hukuman->kategoriBadgeClasses() }}">
                        {{ $hukuman->kategoriLabel() }}
                    </span>
                </div>
                <div class="text-right">
                    <p class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold">Status</p>
                    <span class="inline-flex mt-1 px-3 py-1.5 rounded-xl text-sm font-bold {{ $hukuman->statusBadgeClasses() }}">
                        {{ $hukuman->statusLabel() }}
                    </span>
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-4 pt-2 border-t border-gray-100 dark:border-slate-700">
                <div>
                    <p class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold mb-1">Yang Dihukum</p>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $hukuman->user?->nama }}</p>
                    <p class="text-xs text-slate-400">{{ $hukuman->user?->formatted_divisi_jabatan }}</p>
                </div>
                <div>
                    <p class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold mb-1">Diterbitkan Oleh</p>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $hukuman->issuer?->nama }}</p>
                    <p class="text-xs text-slate-400">{{ $hukuman->issuer?->formatted_divisi_jabatan }}</p>
                </div>
                <div>
                    <p class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold mb-1">Diterbitkan</p>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $hukuman->created_at?->translatedFormat('d M Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold mb-1">Deadline</p>
                    <p class="text-sm font-semibold {{ $hukuman->isExpired() && ! $hukuman->isSelesai() ? 'text-rose-600 dark:text-rose-400' : 'text-gray-900 dark:text-white' }}">
                        {{ $hukuman->deadline_at?->translatedFormat('d M Y H:i') }}
                    </p>
                </div>
            </div>

            <div>
                <p class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold mb-1">Alasan</p>
                <p class="text-sm text-gray-700 dark:text-slate-300 whitespace-pre-wrap">{{ $hukuman->alasan }}</p>
            </div>

            @if ($canManage ?? false)
                <div class="flex flex-wrap gap-2 pt-2 border-t border-gray-100 dark:border-slate-700">
                    <a href="{{ route('hukuman.edit', $hukuman) }}"
                       class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-xs transition">
                        Edit Hukuman
                    </a>
                    <form method="POST" action="{{ route('hukuman.destroy', $hukuman) }}"
                          onsubmit="return confirm('Hapus hukuman ini? Target akan mendapat notifikasi pembatalan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-950/60 text-rose-600 dark:text-rose-300 font-semibold rounded-xl text-xs transition">
                            Hapus Hukuman
                        </button>
                    </form>
                </div>
            @endif
        </div>

        {{-- Pembelaan (read-only jika sudah ada) --}}
        @if ($hukuman->sudahPembelaan())
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-3xl p-6 sm:p-7 border border-gray-100 dark:border-slate-700">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-2">Pembelaan / Klarifikasi</h3>
                <p class="text-xs text-slate-400 mb-3">Dikirim {{ $hukuman->pembelaan_at?->translatedFormat('d M Y H:i') }}</p>
                <p class="text-sm text-gray-700 dark:text-slate-300 whitespace-pre-wrap">{{ $hukuman->pembelaan }}</p>
            </div>
        @elseif ($isTarget && ! $hukuman->isSelesai())
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-3xl p-6 sm:p-7 border border-amber-200 dark:border-amber-800/60">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Pembelaan Wajib</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
                    Ajukan pembelaan atau klarifikasi terlebih dahulu. Proses ini tidak membatalkan hukuman, namun wajib dilakukan sebelum mengerjakan tugas.
                </p>
                <form method="POST" action="{{ route('hukuman.pembelaan', $hukuman) }}" class="space-y-3">
                    @csrf
                    <textarea name="pembelaan" rows="4" required maxlength="2000"
                              placeholder="Tuliskan pembelaan atau klarifikasi Anda..."
                              class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:border-brand-500 focus:ring-brand-500">{{ old('pembelaan') }}</textarea>
                    @error('pembelaan')
                        <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <button type="submit"
                            class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl text-xs shadow-sm transition">
                        Kirim Pembelaan
                    </button>
                </form>
            </div>
        @endif

        {{-- Tugas (opsional) --}}
        @if ($hukuman->sudahPembelaan() && ! $hukuman->isSelesai() && $isTarget)
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-3xl p-6 sm:p-7 border border-gray-100 dark:border-slate-700">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Submit Tugas (Opsional)</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
                    Unggah link Google Drive atau dokumen tugas jika diminta. Kolom ini opsional.
                </p>
                <form method="POST" action="{{ route('hukuman.tugas', $hukuman) }}" class="space-y-3">
                    @csrf
                    <input type="url" name="tugas_link" value="{{ old('tugas_link', $hukuman->tugas_link) }}"
                           placeholder="https://drive.google.com/..."
                           class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:border-brand-500 focus:ring-brand-500">
                    @error('tugas_link')
                        <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <div class="flex flex-wrap gap-2">
                        <button type="submit"
                                class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl text-xs shadow-sm transition">
                            Simpan Link
                        </button>
                    </div>
                </form>

                @if ($hukuman->tugas_link)
                    <p class="mt-4 text-xs text-slate-500">
                        Link tersimpan:
                        <a href="{{ $hukuman->tugas_link }}" target="_blank" rel="noopener noreferrer"
                           class="text-brand-600 dark:text-brand-400 hover:underline break-all">
                            {{ $hukuman->tugas_link }}
                        </a>
                    </p>
                @endif
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-3xl p-6 sm:p-7 border border-emerald-200 dark:border-emerald-800/60">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Selesaikan Hukuman</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
                    Tandai hukuman selesai setelah Anda mengerjakan tugas sesuai kategori (2×24 jam).
                </p>
                <form method="POST" action="{{ route('hukuman.selesai', $hukuman) }}"
                      onsubmit="return confirm('Yakin hukuman sudah selesai dikerjakan?')">
                    @csrf
                    <button type="submit"
                            class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-xs shadow-sm transition">
                        Tandai Selesai
                    </button>
                </form>
            </div>
        @elseif ($hukuman->isSelesai())
            <div class="bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 rounded-3xl p-6 text-center">
                <p class="text-sm font-bold text-emerald-700 dark:text-emerald-300">Hukuman selesai</p>
                <p class="text-xs text-emerald-600/80 dark:text-emerald-400/80 mt-1">
                    Diselesaikan pada {{ $hukuman->selesai_at?->translatedFormat('d M Y H:i') }}
                </p>
            </div>
        @endif
    </div>
</x-app-layout>
