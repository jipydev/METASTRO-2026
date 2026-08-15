<x-app-layout :$title>
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

        {{-- Flash Error Alert --}}
        @if(session('error'))
            <div class="mb-5 p-4 bg-red-50 dark:bg-red-950/50 border border-red-200 dark:border-red-800 rounded-2xl text-red-600 dark:text-red-400 text-xs font-semibold flex items-center gap-2.5">
                <span>❌</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="bg-white dark:bg-slate-800 shadow-sm rounded-3xl p-6 sm:p-8 border border-gray-100 dark:border-slate-700">
            
            {{-- Form Header --}}
            <div class="flex items-center gap-3.5 border-b border-gray-100 dark:border-slate-700 pb-5 mb-6">
                <div class="p-3 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 rounded-2xl text-2xl">
                    📝
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Pengajuan Izin Tidak Hadir</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Silakan lengkapi formulir pengajuan izin ketidakhadiran kegiatan.</p>
                </div>
            </div>

            {{-- Form Body --}}
            <form action="{{ route('pengajuan-izin.store') }}" method="POST" enctype="multipart/form-data" @submit="isSubmitting = true" class="space-y-5 text-xs">
                @csrf

                {{-- Pilih Kegiatan --}}
                <div>
                    <label for="kegiatan_id" class="block font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                        Pilih Kegiatan / Jadwal *
                    </label>
                    <select id="kegiatan_id" name="kegiatan_id" required 
                            class="block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm py-2.5 px-3 text-xs outline-none">
                        <option value="">-- Pilih Jadwal Kegiatan --</option>
                        @foreach($kegiatans as $kegiatan)
                            <option value="{{ $kegiatan->id }}" {{ old('kegiatan_id') == $kegiatan->id ? 'selected' : '' }}>
                                {{ $kegiatan->judul }} — {{ \Carbon\Carbon::parse($kegiatan->tanggal)->locale('id')->isoFormat('D MMMM YYYY') }} ({{ $kegiatan->waktu_mulai ? substr($kegiatan->waktu_mulai, 0, 5) . ' WIB' : 'Waktu menyusul' }})
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('kegiatan_id')" class="mt-1" />
                </div>

                {{-- Jenis Izin --}}
                <div>
                    <label for="jenis_izin" class="block font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                        Jenis / Alasan Izin *
                    </label>
                    <select id="jenis_izin" name="jenis_izin" required 
                            class="block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm py-2.5 px-3 text-xs outline-none">
                        <option value="Sakit" {{ old('jenis_izin') === 'Sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="Izin" {{ old('jenis_izin') === 'Izin' ? 'selected' : '' }}>Izin (Kepentingan Akademik / Mendesak)</option>
                    </select>
                    <x-input-error :messages="$errors->get('jenis_izin')" class="mt-1" />
                </div>

                {{-- Detail Alasan --}}
                <div>
                    <label for="alasan" class="block font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                        Detail Penjelasan Alasan *
                    </label>
                    <textarea id="alasan" name="alasan" rows="4" required placeholder="Jelaskan alasan ketidakhadiran Anda secara terperinci..." 
                              class="block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm py-2.5 px-3 text-xs outline-none">{{ old('alasan') }}</textarea>
                    <x-input-error :messages="$errors->get('alasan')" class="mt-1" />
                </div>

                {{-- Upload Surat Izin PDF --}}
                <div>
                    <label for="surat_izin" class="block font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                        Upload Surat Izin (PDF, Maks. 5MB)
                    </label>
                    <input type="file" id="surat_izin" name="surat_izin" accept="application/pdf" @change="handleSurat($event)" 
                           class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 dark:file:bg-slate-700 dark:file:text-indigo-400 cursor-pointer" />
                    <x-input-error :messages="$errors->get('surat_izin')" class="mt-1" />

                    {{-- Preview File Surat --}}
                    <div x-show="suratFileName" x-transition class="mt-2.5 flex items-center justify-between bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl px-3.5 py-2.5">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="text-lg">📄</span>
                            <div class="truncate">
                                <p class="font-semibold text-gray-900 dark:text-slate-100 truncate" x-text="suratFileName"></p>
                                <p class="text-[10px] text-gray-500"><span x-text="suratFileSize"></span> • Siap diunggah</p>
                            </div>
                        </div>
                        <button type="button" @click="removeSurat()" class="text-red-500 hover:text-red-700 transition font-bold px-2 py-1">
                            ✕
                        </button>
                    </div>
                </div>

                {{-- Upload Bukti Gambar --}}
                <div>
                    <label for="bukti" class="block font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                        Upload Bukti Dokumentasi (JPG/PNG, Maks. 5MB)
                    </label>
                    <input type="file" id="bukti" name="bukti" accept="image/*" @change="handleBukti($event)" 
                           class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 dark:file:bg-slate-700 dark:file:text-indigo-400 cursor-pointer" />
                    <x-input-error :messages="$errors->get('bukti')" class="mt-1" />

                    {{-- Preview Gambar Bukti --}}
                    <div x-show="buktiFileName" x-transition class="mt-2.5 flex items-center justify-between bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl px-3.5 py-2.5">
                        <div class="flex items-center gap-3 min-w-0">
                            <template x-if="buktiPreview">
                                <img :src="buktiPreview" alt="Preview" class="w-10 h-10 object-cover rounded-lg border border-emerald-300 shadow-sm shrink-0">
                            </template>
                            <div class="truncate">
                                <p class="font-semibold text-gray-900 dark:text-slate-100 truncate" x-text="buktiFileName"></p>
                                <p class="text-[10px] text-gray-500"><span x-text="buktiFileSize"></span> • Siap diunggah</p>
                            </div>
                        </div>
                        <button type="button" @click="removeBukti()" class="text-red-500 hover:text-red-700 transition font-bold px-2 py-1">
                            ✕
                        </button>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-gray-100 dark:border-slate-700">
                    <a href="{{ route('pengajuan-izin.index') }}" 
                       class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-200 font-semibold transition">
                        Batal
                    </a>
                    <button type="submit" :disabled="isSubmitting" 
                            class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold transition shadow-sm disabled:opacity-60 cursor-pointer">
                        <span x-show="!isSubmitting">Kirim Pengajuan</span>
                        <span x-show="isSubmitting" class="animate-pulse">Mengirim...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>