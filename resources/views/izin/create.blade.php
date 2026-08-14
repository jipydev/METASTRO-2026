<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            {{ __('Form Pengajuan Izin') }}
        </h2>
    </x-slot>

    <div x-data="{
        isSubmitting: false,
        suratFileName: null,
        suratFileSize: null,
        buktiFileName: null,
        buktiFileSize: null,
        buktiPreview: null,

        formatSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        },

        handleSurat(event) {
            const file = event.target.files[0];
            if (!file) { this.suratFileName = null; this.suratFileSize = null; return; }
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file ' + file.name + ' terlalu besar (' + (file.size / (1024 * 1024)).toFixed(1) + ' MB). Maksimal ukuran file adalah 5 MB.');
                event.target.value = '';
                this.suratFileName = null;
                this.suratFileSize = null;
                return;
            }
            this.suratFileName = file.name;
            this.suratFileSize = this.formatSize(file.size);
        },

        handleBukti(event) {
            const file = event.target.files[0];
            if (!file) { this.buktiFileName = null; this.buktiFileSize = null; this.buktiPreview = null; return; }
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file ' + file.name + ' terlalu besar (' + (file.size / (1024 * 1024)).toFixed(1) + ' MB). Maksimal ukuran file adalah 5 MB.');
                event.target.value = '';
                this.buktiFileName = null;
                this.buktiFileSize = null;
                this.buktiPreview = null;
                return;
            }
            this.buktiFileName = file.name;
            this.buktiFileSize = this.formatSize(file.size);
            const reader = new FileReader();
            reader.onload = (e) => { this.buktiPreview = e.target.result; };
            reader.readAsDataURL(file);
        },

        removeSurat() {
            document.getElementById('surat_izin').value = '';
            this.suratFileName = null;
            this.suratFileSize = null;
        },

        removeBukti() {
            document.getElementById('bukti').value = '';
            this.buktiFileName = null;
            this.buktiFileSize = null;
            this.buktiPreview = null;
        }
    }" class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 font-poppins">
        @if(session('error'))
            <div class="mb-4 p-4 bg-rose-500/10 border border-rose-500/30 rounded-xl text-rose-600 dark:text-rose-400 flex items-center gap-3">
                <span class="icon-[akar-icons--circle-x] text-xl"></span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="bg-white dark:bg-slate-800 shadow-xl rounded-2xl p-6 sm:p-8 border border-gray-100 dark:border-slate-700">
            <div class="flex items-center gap-3 border-b border-gray-100 dark:border-slate-700 pb-4 mb-6">
                <div class="p-3 bg-primary-500/10 text-primary-500 rounded-xl">
                    <span class="icon-[grommet-icons--document-notes] text-2xl"></span>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Pengajuan Izin Tidak Hadir</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Silakan lengkapi formulir pengajuan izin di bawah ini dengan benar.</p>
                </div>
            </div>

            <form action="{{ route('izin.store') }}" method="POST" enctype="multipart/form-data" @submit="isSubmitting = true" class="space-y-5">
                @csrf

                <!-- Pilih Rapat / Timeline -->
                <div>
                    <x-input-label for="rapat_id" :value="__('Pilih Rapat / Timeline Kegiatan')" class="font-semibold text-slate-700 dark:text-slate-300" />
                    <select id="rapat_id" name="rapat_id" required class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-xl shadow-sm py-2.5 px-3 text-sm">
                        <option value="">-- Pilih Rapat / Timeline --</option>
                        @foreach($rapats as $rapat)
                            <option value="{{ $rapat->id }}" {{ old('rapat_id') == $rapat->id ? 'selected' : '' }}>
                                {{ $rapat->judul }} — {{ \Carbon\Carbon::parse($rapat->tanggal)->locale('id')->isoFormat('D MMMM YYYY') }} ({{ \Carbon\Carbon::parse($rapat->jam)->format('H:i') }} WIB)
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('rapat_id')" class="mt-2" />
                </div>

                <!-- Jenis Izin -->
                <div>
                    <x-input-label for="jenis_izin" :value="__('Alasan / Jenis Izin')" />
                    <select id="jenis_izin" name="jenis_izin" required class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-xl shadow-sm py-2.5 px-3">
                        <option value="Sakit" {{ old('jenis_izin') == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="Izin" {{ old('jenis_izin') == 'Izin' ? 'selected' : '' }}>Izin (Kepentingan/Acara)</option>
                    </select>
                    <x-input-error :messages="$errors->get('jenis_izin')" class="mt-2" />
                </div>

                <!-- Detail Alasan -->
                <div>
                    <x-input-label for="alasan" :value="__('Detail Penjelasan Alasan')" />
                    <textarea id="alasan" name="alasan" rows="4" required placeholder="Jelaskan alasan ketidakhadiran Anda secara detail..." class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-xl shadow-sm py-2.5 px-3">{{ old('alasan') }}</textarea>
                    <x-input-error :messages="$errors->get('alasan')" class="mt-2" />
                </div>

                <!-- Upload Surat Izin PDF -->
                <div>
                    <x-input-label for="surat_izin" :value="__('Upload Surat Izin (PDF, Maks 5MB)')" />
                    <input type="file" id="surat_izin" name="surat_izin" accept="application/pdf" @change="handleSurat($event)" class="block mt-1 w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-500/10 file:text-primary-600 hover:file:bg-primary-500/20 cursor-pointer" />
                    <x-input-error :messages="$errors->get('surat_izin')" class="mt-2" />

                    <!-- Preview Surat Izin -->
                    <div x-show="suratFileName" x-transition class="mt-3 flex items-center gap-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/40 rounded-xl px-4 py-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-red-500/10 text-red-500 rounded-lg flex items-center justify-center">
                            <span class="icon-[akar-icons--file] text-xl"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 truncate" x-text="suratFileName"></p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                <span x-text="suratFileSize"></span> · <span class="text-emerald-600 dark:text-emerald-400 font-semibold">Siap dikirim</span>
                            </p>
                        </div>
                        <button type="button" @click="removeSurat()" class="flex-shrink-0 text-slate-400 hover:text-rose-500 transition cursor-pointer" title="Hapus file">
                            <span class="icon-[akar-icons--circle-x] text-xl"></span>
                        </button>
                    </div>
                </div>

                <!-- Upload Bukti Dokumentasi Gambar -->
                <div>
                    <x-input-label for="bukti" :value="__('Upload Bukti Dokumentasi (JPG/PNG, Maks 5MB)')" />
                    <input type="file" id="bukti" name="bukti" accept="image/*" @change="handleBukti($event)" class="block mt-1 w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-500/10 file:text-primary-600 hover:file:bg-primary-500/20 cursor-pointer" />
                    <x-input-error :messages="$errors->get('bukti')" class="mt-2" />

                    <!-- Preview Bukti Dokumentasi -->
                    <div x-show="buktiFileName" x-transition class="mt-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/40 rounded-xl px-4 py-3">
                        <div class="flex items-center gap-3">
                            <template x-if="buktiPreview">
                                <img :src="buktiPreview" alt="Preview" class="flex-shrink-0 w-14 h-14 object-cover rounded-lg border-2 border-white dark:border-slate-600 shadow-sm">
                            </template>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 truncate" x-text="buktiFileName"></p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    <span x-text="buktiFileSize"></span> · <span class="text-emerald-600 dark:text-emerald-400 font-semibold">Siap dikirim</span>
                                </p>
                            </div>
                            <button type="button" @click="removeBukti()" class="flex-shrink-0 text-slate-400 hover:text-rose-500 transition cursor-pointer" title="Hapus file">
                                <span class="icon-[akar-icons--circle-x] text-xl"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-slate-700">
                    <a href="{{ route('izin.history') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-sm font-semibold hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                        Batal
                    </a>
                    <button type="submit" :disabled="isSubmitting" class="bg-primary-500 hover:bg-primary-600 disabled:opacity-50 text-white font-bold py-2.5 px-6 rounded-xl transition text-sm shadow-md flex items-center gap-2 cursor-pointer">
                        <template x-if="!isSubmitting">
                            <span class="flex items-center gap-2">
                                <span class="icon-[akar-icons--send]"></span>
                                Kirim Pengajuan
                            </span>
                        </template>
                        <template x-if="isSubmitting">
                            <span class="flex items-center gap-2">
                                <span class="animate-spin icon-[akar-icons--loading]"></span>
                                Mengirim...
                            </span>
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
