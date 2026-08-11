<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            {{ __('Form Pengajuan Izin') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 font-poppins">
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

            <form action="{{ route('izin.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <!-- Tanggal Izin -->
                <div>
                    <x-input-label for="tanggal_izin" :value="__('Tanggal Izin')" />
                    <x-text-input id="tanggal_izin" class="block mt-1 w-full" type="date" name="tanggal_izin" :value="old('tanggal_izin')" required />
                    <x-input-error :messages="$errors->get('tanggal_izin')" class="mt-2" />
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
                    <input type="file" id="surat_izin" name="surat_izin" accept="application/pdf" class="block mt-1 w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-500/10 file:text-primary-600 hover:file:bg-primary-500/20 cursor-pointer" />
                    <x-input-error :messages="$errors->get('surat_izin')" class="mt-2" />
                </div>

                <!-- Upload Bukti Dokumentasi Gambar -->
                <div>
                    <x-input-label for="bukti" :value="__('Upload Bukti Dokumentasi (JPG/PNG, Maks 5MB)')" />
                    <input type="file" id="bukti" name="bukti" accept="image/*" class="block mt-1 w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-500/10 file:text-primary-600 hover:file:bg-primary-500/20 cursor-pointer" />
                    <x-input-error :messages="$errors->get('bukti')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-slate-700">
                    <a href="{{ route('izin.history') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-sm font-semibold hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                        Batal
                    </a>
                    <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-bold py-2.5 px-6 rounded-xl transition text-sm shadow-md flex items-center gap-2">
                        <span class="icon-[akar-icons--send]"></span>
                        Kirim Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
