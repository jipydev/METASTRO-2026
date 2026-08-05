<div
    x-show="openTambahPengumuman"
    x-cloak
    class="fixed inset-0 z-[999] flex items-center justify-center p-4">

    <div 
        x-show="openTambahPengumuman" 
        x-transition.opacity 
        class="fixed inset-0 bg-black/40 backdrop-blur-sm" 
        @click="openTambahPengumuman = false">
    </div>

    <div
        x-show="openTambahPengumuman"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col max-h-[85vh] border border-gray-100 dark:border-slate-700 font-poppins">

        <form
            action="{{ route('pengumuman.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="flex flex-col h-full max-h-[85vh]">

            @csrf

            {{-- Header --}}
            <div class="flex justify-between items-center border-b border-gray-100 dark:border-slate-700 px-5 py-3.5 shrink-0">
                <h2 class="text-base font-bold text-primary-600 dark:text-primary-400">
                    Tambah Pengumuman
                </h2>
                <button
                    type="button"
                    @click="openTambahPengumuman=false"
                    class="text-gray-400 dark:text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 p-1.5 rounded-lg transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-5 space-y-3 overflow-y-auto flex-1 text-slate-800 dark:text-slate-200">

                {{-- Judul --}}
                <div>
                    <label class="block font-semibold mb-1 text-sm text-slate-700 dark:text-slate-300">Judul</label>
                    <input
                        type="text"
                        name="judul"
                        value="{{ old('judul') }}"
                        class="w-full rounded-xl border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-primary-500 focus:ring-primary-500 text-sm p-2 transition"
                        required>
                    @error('judul') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Isi --}}
                <div>
                    <label class="block font-semibold mb-1 text-sm text-slate-700 dark:text-slate-300">Isi Pengumuman</label>
                    <textarea
                        name="isi"
                        rows="3"
                        class="w-full rounded-xl border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-primary-500 focus:ring-primary-500 text-sm p-2 transition"
                        required>{{ old('isi') }}</textarea>
                    @error('isi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Grid untuk Tanggal & Status --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    {{-- Publish --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Publish</label>
                        <input
                            type="datetime-local"
                            name="tanggal_publish"
                            value="{{ old('tanggal_publish') }}"
                            class="w-full text-sm rounded-xl border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-primary-500 focus:ring-primary-500 p-2 transition"
                            required>
                        @error('tanggal_publish') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Status</label>
                        <select
                            name="status"
                            class="w-full text-sm rounded-xl border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-primary-500 focus:ring-primary-500 p-2 transition">
                            <option value="Publish" selected>Publish</option>
                            <option value="Draft">Draft</option>
                        </select>
                        @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Lampiran --}}
                <div>
                    <label class="block font-semibold mb-1 text-sm text-slate-700 dark:text-slate-300">Lampiran</label>
                    <input
                        type="file"
                        name="lampiran"
                        class="w-full text-sm rounded-xl border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-600 hover:file:bg-primary-100 dark:file:bg-slate-600 dark:file:text-primary-400 transition cursor-pointer">
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Opsional (Maksimal 5 MB)</p>
                    @error('lampiran') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

            </div>

            {{-- Footer --}}
            <div class="border-t border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50 px-5 py-3.5 flex justify-end gap-3 shrink-0">
                <button
                    type="button"
                    @click="openTambahPengumuman=false"
                    class="px-5 py-1.5 rounded-xl border border-gray-300 dark:border-slate-600 hover:bg-gray-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-sm cursor-pointer transition">
                    Batal
                </button>
                <button
                    type="submit"
                    class="bg-primary-500 hover:bg-primary-600 text-white px-5 py-1.5 rounded-xl font-bold text-sm shadow-sm cursor-pointer transition">
                    Simpan
                </button>
            </div>

        </form>
    </div>
</div>