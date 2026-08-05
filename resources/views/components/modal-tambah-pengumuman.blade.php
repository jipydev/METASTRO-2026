<div
    x-show="openTambahPengumuman"
    x-cloak
    class="fixed inset-0 z-[999] flex items-center justify-center p-4 sm:p-0">

    <div 
        x-show="openTambahPengumuman" 
        x-transition.opacity 
        class="fixed inset-0 bg-black/40 backdrop-blur-sm" 
        @click="openTambahPengumuman = false">
    </div>

    <div
        x-show="openTambahPengumuman"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]">

        <form
            action="{{ route('pengumuman.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="flex flex-col h-full">

            @csrf

            {{-- Header --}}
            <div class="flex justify-between items-center border-b border-gray-100 px-6 py-4 shrink-0">
                <h2 class="text-lg font-bold text-[#105e75]">Tambah Pengumuman</h2>
                <button
                    type="button"
                    @click="openTambahPengumuman = false"
                    class="text-gray-400 hover:text-red-500 hover:bg-red-50 p-1.5 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Body (Scrollable) --}}
            <div class="p-6 space-y-4 overflow-y-auto">

                {{-- Judul --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Judul</label>
                    <input
                        type="text"
                        name="judul"
                        value="{{ old('judul') }}"
                        placeholder="Masukkan judul pengumuman..."
                        class="w-full text-sm rounded-xl border-gray-300 focus:border-[#105e75] focus:ring-[#105e75] transition"
                        required>
                    @error('judul') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Isi --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Isi Pengumuman</label>
                    <textarea
                        name="isi"
                        rows="4"
                        placeholder="Tuliskan informasi di sini..."
                        class="w-full text-sm rounded-xl border-gray-300 focus:border-[#105e75] focus:ring-[#105e75] transition"
                        required>{{ old('isi') }}</textarea>
                    @error('isi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Grid untuk Tanggal & Status --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Publish --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Publish</label>
                        <input
                            type="datetime-local"
                            name="tanggal_publish"
                            value="{{ old('tanggal_publish') }}"
                            class="w-full text-sm rounded-xl border-gray-300 focus:border-[#105e75] focus:ring-[#105e75] transition"
                            required>
                        @error('tanggal_publish') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Status</label>
                        <select
                            name="status"
                            class="w-full text-sm rounded-xl border-gray-300 focus:border-[#105e75] focus:ring-[#105e75] transition">
                            <option value="Publish" selected>Publish</option>
                            <option value="Draft">Draft</option>
                        </select>
                        @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Lampiran --}}
                <div class="pt-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Lampiran</label>
                    <input
                        type="file"
                        name="lampiran"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#f2f7fb] file:text-[#105e75] hover:file:bg-blue-100 cursor-pointer transition">
                    <p class="text-xs text-gray-400 mt-1.5">Maksimal 5 MB (Opsional)</p>
                    @error('lampiran') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

            </div>

            {{-- Footer --}}
            <div class="border-t border-gray-100 bg-gray-50/50 px-6 py-4 flex justify-end gap-3 shrink-0">
                <button
                    type="button"
                    @click="openTambahPengumuman = false"
                    class="px-5 py-2 text-sm font-semibold text-gray-600 rounded-xl border border-gray-300 hover:bg-gray-100 transition">
                    Batal
                </button>
                <button
                    type="submit"
                    class="bg-[#105e75] hover:bg-[#0d4d61] text-white text-sm font-bold px-6 py-2 rounded-xl transition shadow-sm">
                    Simpan
                </button>
            </div>

        </form>
    </div>
</div>