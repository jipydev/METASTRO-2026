<x-app-layout :$title>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight font-oswald tracking-tight">
                    {{ __('Tambah Penugasan Baru — Divisi Acara') }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Buat instruksi penugasan baru untuk peserta Metastro 2026
                </p>
            </div>
            <a href="{{ route('tugas.index') }}"
                class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-xl transition">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div x-data="{ isSubmitting: false }" class="py-8 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 font-poppins">
        
        {{-- Strict Deadline Notice --}}
        <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/60 rounded-2xl flex items-start gap-3 text-xs text-amber-800 dark:text-amber-300">
            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <strong class="font-bold">Kebijakan Batas Waktu Ketat (Strict Deadline):</strong>
                Sistem akan secara otomatis menutup akses pengumpulan dan menolak submisi yang dilakukan setelah waktu tenggat waktu berakhir. Pastikan batas waktu telah terkoordinasi dengan baik.
            </div>
        </div>

        <form action="{{ route('tugas.store') }}" method="POST" @submit="isSubmitting = true"
            class="bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-700 shadow-sm space-y-6 text-xs">
            @csrf

            <div class="space-y-4">
                {{-- Judul --}}
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Judul Penugasan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="judul" required maxlength="255" value="{{ old('judul') }}"
                        placeholder="Contoh: Resume Materi Kepemimpinan dan Etika Metastro"
                        class="form-control-app w-full">
                    @error('judul')
                        <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jenis Tugas & Nilai Maksimal --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Jenis Penugasan <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis" required class="form-control-app w-full">
                            <option value="individu" @selected(old('jenis') === 'individu')>Individu (Dikerjakan & dikumpul per peserta)</option>
                            <option value="tim" @selected(old('jenis') === 'tim')>Tim / Kelompok (Dikumpulkan oleh 1 perwakilan tim)</option>
                            <option value="angkatan" @selected(old('jenis') === 'angkatan')>Angkatan (Dikumpulkan oleh 1 perwakilan angkatan)</option>
                        </select>
                        <p class="text-[10px] text-slate-400 mt-1">Untuk tugas tim/angkatan, status reviu otomatis berlaku ke seluruh anggota terkait.</p>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Nilai Maksimal Acuan <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="nilai_maksimal" required min="1" max="1000" value="{{ old('nilai_maksimal', 100) }}"
                            class="form-control-app w-full">
                    </div>
                </div>

                {{-- Tenggat Waktu --}}
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Tenggat Waktu Pengumpulan (Strict Deadline) <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="tenggat_waktu" required value="{{ old('tenggat_waktu') }}"
                        class="form-control-app w-full sm:w-80">
                    <p class="text-[10px] text-slate-400 mt-1">Portal pengumpulan peserta akan dikunci tepat pada tanggal dan jam ini.</p>
                </div>

                {{-- Deskripsi / Instruksi Penugasan --}}
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Deskripsi & Instruksi Penugasan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="deskripsi" rows="6" required
                        placeholder="Tuliskan petunjuk pengerjaan, format berkas (PDF/link gdrive/video), kriteria penilaian, dan instruksi perwakilan kelompok..."
                        class="form-control-app w-full resize-y">{{ old('deskripsi') }}</textarea>
                </div>
            </div>

            {{-- Action Buttons with Loading Indicator --}}
            <div class="pt-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-end gap-3">
                <a href="{{ route('tugas.index') }}"
                    class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl transition">
                    Batal
                </a>
                
                <button type="submit" :disabled="isSubmitting"
                    class="btn-primary inline-flex items-center gap-2 disabled:opacity-50">
                    <template x-if="isSubmitting">
                        <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    </template>
                    <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Tugas'"></span>
                </button>
            </div>
        </form>
    </div>
</x-app-layout>