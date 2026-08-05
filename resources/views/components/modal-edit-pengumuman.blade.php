<div
    x-show="openEditPengumuman"
    x-cloak
    class="fixed inset-0 z-[999] flex items-center justify-center p-4">

    <!-- Backdrop Overlay -->
    <div 
        x-show="openEditPengumuman" 
        x-transition.opacity 
        class="fixed inset-0 bg-black/50 backdrop-blur-sm" 
        @click="openEditPengumuman = false">
    </div>

    <!-- Modal Box -->
    <div
        x-show="openEditPengumuman"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="relative bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh] border border-gray-100 dark:border-slate-700 font-poppins">

        <form
            x-bind:action="'{{ url('pengumuman') }}/' + selectedPengumuman.id"
            method="POST"
            enctype="multipart/form-data"
            class="flex flex-col h-full">

            @csrf
            @method('PUT')

            {{-- HEADER --}}
            <div class="flex justify-between items-center border-b border-gray-100 dark:border-slate-700 px-6 py-5 shrink-0">
                <h2 class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                    Edit Pengumuman
                </h2>

                <button
                    type="button"
                    @click="openEditPengumuman=false"
                    class="text-3xl text-gray-400 dark:text-slate-400 hover:text-red-500 transition cursor-pointer">
                    &times;
                </button>
            </div>

            {{-- BODY --}}
            <div class="p-6 space-y-5 overflow-y-auto flex-1 text-slate-800 dark:text-slate-200">

                {{-- Judul --}}
                <div>
                    <label class="block font-semibold mb-1.5 text-sm text-slate-700 dark:text-slate-300">
                        Judul
                    </label>
                    <input
                        type="text"
                        name="judul"
                        x-model="selectedPengumuman.judul"
                        class="w-full rounded-xl border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-primary-500 focus:ring-primary-500 text-sm p-2.5"
                        required>
                </div>

                {{-- Isi --}}
                <div>
                    <label class="block font-semibold mb-1.5 text-sm text-slate-700 dark:text-slate-300">
                        Isi Pengumuman
                    </label>
                    <textarea
                        name="isi"
                        rows="4"
                        x-model="selectedPengumuman.isi"
                        class="w-full rounded-xl border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-primary-500 focus:ring-primary-500 text-sm p-2.5"
                        required></textarea>
                </div>

                {{-- Grid untuk Tanggal & Status --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Publish --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Tanggal Publish</label>
                        <input
                            type="datetime-local"
                            name="tanggal_publish"
                            x-model="selectedPengumuman.tanggal_publish"
                            class="w-full text-sm rounded-xl border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-primary-500 focus:ring-primary-500 p-2.5 transition"
                            required>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Status</label>
                        <select
                            name="status"
                            x-model="selectedPengumuman.status"
                            class="w-full text-sm rounded-xl border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-primary-500 focus:ring-primary-500 p-2.5 transition">
                            <option value="Draft">Draft</option>
                            <option value="Publish">Publish</option>
                        </select>
                    </div>
                </div>

                {{-- Lampiran --}}
                <div>
                    <label class="block font-semibold mb-1.5 text-sm text-slate-700 dark:text-slate-300">
                        Ganti Lampiran
                    </label>
                    <input
                        type="file"
                        name="lampiran"
                        class="w-full text-sm rounded-xl border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-600 hover:file:bg-primary-100 dark:file:bg-slate-600 dark:file:text-primary-400">

                    <template x-if="selectedPengumuman.lampiran">
                        <div class="mt-2.5">
                            <a
                                :href="'/storage/' + selectedPengumuman.lampiran"
                                target="_blank"
                                class="text-primary-500 dark:text-primary-400 hover:underline text-sm font-semibold flex items-center gap-1">
                                📎 Lihat Lampiran Saat Ini
                            </a>
                        </div>
                    </template>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="border-t border-gray-100 dark:border-slate-700 px-6 py-4 flex justify-end gap-3 shrink-0">
                <button
                    type="button"
                    @click="openEditPengumuman=false"
                    class="px-5 py-2 rounded-xl border border-gray-300 dark:border-slate-600 hover:bg-gray-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-sm cursor-pointer">
                    Batal
                </button>
                <button
                    type="submit"
                    class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-2 rounded-xl font-bold text-sm shadow-sm cursor-pointer">
                    Update Pengumuman
                </button>
            </div>

        </form>
    </div>
</div>