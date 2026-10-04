<x-app-layout :$title>

    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight font-oswald tracking-tight">
                {{ __('Tambah Penugasan Baru') }}
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Buat penugasan baru untuk peserta Metastro 2026
            </p>
        </div>
        <a href="{{ route('dashboard.tugas.index') }}"
            class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-xl transition">
            &larr; Kembali
        </a>
    </div>

    <div x-data="{
        isSubmitting: false,
        editorHtml: @js(old('deskripsi', '')),
        activeFormats: { bold: false, italic: false, underline: false, insertUnorderedList: false, insertOrderedList: false },
        format(command) {
            this.$refs.editor.focus();
            if (command === 'normal') {
                document.execCommand('removeFormat', false, null);
                if (document.queryCommandState('insertUnorderedList')) {
                    document.execCommand('insertUnorderedList', false, null);
                }
                if (document.queryCommandState('insertOrderedList')) {
                    document.execCommand('insertOrderedList', false, null);
                }
                document.execCommand('formatBlock', false, 'p');
            } else {
                document.execCommand(command, false, null);
            }
            this.syncEditor();
            this.updateToolbar();
        },
        syncEditor() {
            this.editorHtml = this.$refs.editor.innerHTML;
        },
        updateToolbar() {
            this.activeFormats = {
                bold: document.queryCommandState('bold'),
                italic: document.queryCommandState('italic'),
                underline: document.queryCommandState('underline'),
                insertUnorderedList: document.queryCommandState('insertUnorderedList'),
                insertOrderedList: document.queryCommandState('insertOrderedList')
            };
        },
        init() {
            this.$refs.editor.innerHTML = this.editorHtml;
            this.updateToolbar();
            this.$refs.editor.addEventListener('keyup', () => this.updateToolbar());
            this.$refs.editor.addEventListener('mouseup', () => this.updateToolbar());
            this.$refs.editor.addEventListener('focus', () => this.updateToolbar());
        }
    }" class="py-8 max-w-4xl mx-auto font-poppins">

        <form action="{{ route('dashboard.tugas.store') }}" method="POST" @submit="isSubmitting = true"
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

                {{-- Jenis Tugas --}}
                <div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Jenis Penugasan <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis" required class="form-control-app w-full">
                            <option value="individu" @selected(old('jenis') === 'individu')>Individu (Dikerjakan & dikumpul per
                                peserta)</option>
                            <option value="tim" @selected(old('jenis') === 'tim')>Tim / Kelompok (Dikumpulkan oleh 1
                                perwakilan tim)</option>
                            <option value="angkatan" @selected(old('jenis') === 'angkatan')>Angkatan (Dikumpulkan oleh 1
                                perwakilan angkatan)</option>
                        </select>
                        <p class="text-[10px] text-slate-400 mt-1">Untuk tugas tim/angkatan, status review otomatis
                            berlaku ke seluruh anggota terkait.</p>
                    </div>
                </div>

                {{-- Tenggat Waktu --}}
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Tenggat Waktu Pengumpulan <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="tenggat_waktu" required value="{{ old('tenggat_waktu') }}"
                        class="form-control-app w-full sm:w-80">
                </div>

                {{-- Deskripsi / Instruksi Penugasan --}}
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Deskripsi & Instruksi Penugasan <span class="text-red-500">*</span>
                    </label>
                    <div class="overflow-hidden rounded-xl border border-slate-300 dark:border-slate-600 focus-within:ring-2 focus-within:ring-brand-500">
                        <div class="flex flex-wrap items-center gap-1 border-b border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/60 p-2">
                            <button type="button" @mousedown.prevent @click="format('bold')" title="Tebal" :class="activeFormats.bold ? 'bg-brand-100 text-brand-700 dark:bg-brand-950/60 dark:text-brand-300 ring-1 ring-brand-500' : ''" class="rounded-lg px-2.5 py-1.5 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-600"><strong>B</strong></button>
                            <button type="button" @mousedown.prevent @click="format('italic')" title="Miring" :class="activeFormats.italic ? 'bg-brand-100 text-brand-700 dark:bg-brand-950/60 dark:text-brand-300 ring-1 ring-brand-500' : ''" class="rounded-lg px-2.5 py-1.5 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-600"><em>I</em></button>
                            <button type="button" @mousedown.prevent @click="format('underline')" title="Garis bawah" :class="activeFormats.underline ? 'bg-brand-100 text-brand-700 dark:bg-brand-950/60 dark:text-brand-300 ring-1 ring-brand-500' : ''" class="rounded-lg px-2.5 py-1.5 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-600"><u>U</u></button>
                            <span class="mx-1 h-5 border-l border-slate-300 dark:border-slate-500"></span>
                            <button type="button" @mousedown.prevent @click="format('insertUnorderedList')" title="Daftar poin" :class="activeFormats.insertUnorderedList ? 'bg-brand-100 text-brand-700 dark:bg-brand-950/60 dark:text-brand-300 ring-1 ring-brand-500' : ''" class="rounded-lg px-2.5 py-1.5 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-600">•&nbsp; Daftar</button>
                            <button type="button" @mousedown.prevent @click="format('insertOrderedList')" title="Daftar bernomor" :class="activeFormats.insertOrderedList ? 'bg-brand-100 text-brand-700 dark:bg-brand-950/60 dark:text-brand-300 ring-1 ring-brand-500' : ''" class="rounded-lg px-2.5 py-1.5 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-600">1.&nbsp; Daftar</button>
                            <button type="button" @mousedown.prevent @click="format('normal')" title="Hapus semua format" class="rounded-lg px-2.5 py-1.5 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-600">Normal</button>
                        </div>
                        <div x-ref="editor" contenteditable="true" @input="syncEditor()"
                            data-placeholder="Tuliskan petunjuk pengerjaan, format berkas, kriteria penilaian, dan instruksi..."
                            class="rich-text-editor min-h-40 w-full bg-white dark:bg-slate-800 px-3.5 py-3 text-sm text-slate-900 dark:text-white outline-none empty:before:content-[attr(data-placeholder)] empty:before:text-slate-400"></div>
                    </div>
                    <textarea name="deskripsi" x-model="editorHtml" class="hidden"></textarea>
                    @error('deskripsi')
                        <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Action Buttons with Loading Indicator --}}
            <div class="pt-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-end gap-3">
                <a href="{{ route('dashboard.tugas.index') }}"
                    class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl transition">
                    Batal
                </a>

                <button type="submit" :disabled="isSubmitting"
                    class="btn-primary inline-flex items-center gap-2 disabled:opacity-50">
                    <template x-if="isSubmitting">
                        <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </template>
                    <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Tugas'"></span>
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
