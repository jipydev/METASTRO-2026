<div
    x-show="openEditPengumuman"
    x-cloak
    class="fixed inset-0 z-[999] flex items-center justify-center p-4 sm:p-0">

    <div 
        x-show="openEditPengumuman" 
        x-transition.opacity 
        class="fixed inset-0 bg-black/40 backdrop-blur-sm" 
        @click="openEditPengumuman = false">
    </div>

    <div
        x-show="openEditPengumuman"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]">

        <form
            x-bind:action="'{{ url('pengumuman') }}/' + selectedPengumuman.id"
            method="POST"
            enctype="multipart/form-data"
            class="flex flex-col h-full">

            @csrf
            @method('PUT')

            {{-- HEADER --}}
            <div class="flex justify-between items-center border-b border-gray-100 px-6 py-4 shrink-0">
                <h2 class="text-lg font-bold text-[#105e75]">Edit Pengumuman</h2>
                <button
                    type="button"
                    @click="openEditPengumuman = false"
                    class="text-gray-400 hover:text-red-500 hover:bg-red-50 p-1.5 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- BODY (Scrollable) --}}
            <div class="p-6 space-y-4 overflow-y-auto">

                {{-- Judul --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Judul</label>
                    <input
                        type="text"
                        name="judul"
                        x-model="selectedPengumuman.judul"
                        class="w-full text-sm rounded-xl border-gray-300 focus:border-[#105e75] focus:ring-[#105e75] transition"
                        required>
                </div>

                {{-- Isi --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Isi Pengumuman</label>
                    <textarea
                        name="isi"
                        rows="4"
                        x-model="selectedPengumuman.isi"
                        class="w-full text-sm rounded-xl border-gray-300 focus:border-[#105e75] focus:ring-[#105e75] transition"
                        required></textarea>
                </div>

                {{-- Grid untuk Tanggal & Status agar hemat tempat --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Publish --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Publish</label>
                        <input
                            type="datetime-local"
                            name="tanggal_publish"
                            x-model="selectedPengumuman.tanggal_publish"
                            class="w-full text-sm rounded-xl border-gray-300 focus:border-[#105e75] focus:ring-[#105e75] transition"
                            required>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Status</label>
                        <select
                            name="status"
                            x-model="selectedPengumuman.status"
                            class="w-full text-sm rounded-xl border-gray-300 focus:border-[#105e75] focus:ring-[#105e75] transition">
                            <option value="Draft">Draft</option>
                            <option value="Publish">Publish</option>
                        </select>
                    </div>
                </div>

                {{-- Lampiran --}}
                <div class="pt-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Ganti Lampiran (Opsional)</label>
                    <input
                        type="file"
                        name="lampiran"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#f2f7fb] file:text-[#105e75] hover:file:bg-blue-100 cursor-pointer transition">
                    
                    <template x-if="selectedPengumuman.lampiran">
                        <div class="mt-2.5">
                            <a :href="'/storage/' + selectedPengumuman.lampiran" target="_blank"
                               class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:underline bg-blue-50 px-3 py-1.5 rounded-md">
                                📎 Lihat File Saat Ini
                            </a>
                        </div>
                    </template>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="border-t border-gray-100 bg-gray-50/50 px-6 py-4 flex justify-end gap-3 shrink-0">
                <button
                    type="button"
                    @click="openEditPengumuman = false"
                    class="px-5 py-2 text-sm font-semibold text-gray-600 rounded-xl border border-gray-300 hover:bg-gray-100 transition">
                    Batal
                </button>
                <button
                    type="submit"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-bold px-6 py-2 rounded-xl transition shadow-sm">
                    Update
                </button>
            </div>

        </form>
    </div>
</div>