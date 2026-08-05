<div
    x-show="openEditPengumuman"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">

    <div
        x-show="openEditPengumuman"
        x-transition
        @click.outside="openEditPengumuman=false"
        class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl w-full max-w-2xl border border-gray-100 dark:border-slate-700 font-poppins">

        <form
            x-bind:action="'{{ url('pengumuman') }}/' + selectedPengumuman.id"
            method="POST"
            enctype="multipart/form-data">

            @csrf
            @method('PUT')

            {{-- HEADER --}}
            <div class="flex justify-between items-center border-b border-gray-100 dark:border-slate-700 px-6 py-5">

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
            <div class="p-6 space-y-6 text-slate-800 dark:text-slate-200">

                {{-- Judul --}}
                <div>

                    <label class="block font-semibold mb-2 text-sm text-gray-700 dark:text-slate-300">

                        Judul

                    </label>

                    <input
                        type="text"
                        name="judul"
                        x-model="selectedPengumuman.judul"
                        class="w-full rounded-xl border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                        required>

                </div>

                {{-- Isi --}}
                <div>

                    <label class="block font-semibold mb-2 text-sm text-gray-700 dark:text-slate-300">

                        Isi Pengumuman

                    </label>

                    <textarea
                        name="isi"
                        rows="6"
                        x-model="selectedPengumuman.isi"
                        class="w-full rounded-xl border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                        required></textarea>

                </div>

                {{-- Lampiran --}}
                <div>

                    <label class="block font-semibold mb-2 text-sm text-gray-700 dark:text-slate-300">

                        Ganti Lampiran

                    </label>

                    <input
                        type="file"
                        name="lampiran"
                        class="w-full rounded-xl border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-600 hover:file:bg-primary-100 dark:file:bg-slate-600 dark:file:text-primary-400">

                    <template x-if="selectedPengumuman.lampiran">

                        <div class="mt-3">

                            <a
                                :href="'/storage/' + selectedPengumuman.lampiran"
                                target="_blank"
                                class="text-primary-500 dark:text-primary-400 hover:underline text-sm font-semibold">

                                📎 Lihat Lampiran Saat Ini

                            </a>

                        </div>

                    </template>

                </div>

                {{-- Publish --}}
                <div>

                    <label class="block font-semibold mb-2 text-sm text-gray-700 dark:text-slate-300">

                        Tanggal Publish

                    </label>

                    <input
                        type="datetime-local"
                        name="tanggal_publish"
                        x-model="selectedPengumuman.tanggal_publish"
                        class="w-full rounded-xl border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                        required>

                </div>

                {{-- Status --}}
                <div>

                    <label class="block font-semibold mb-2 text-sm text-gray-700 dark:text-slate-300">

                        Status

                    </label>

                    <select
                        name="status"
                        x-model="selectedPengumuman.status"
                        class="w-full rounded-xl border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-primary-500 focus:ring-primary-500">

                        <option value="Draft">

                            Draft

                        </option>

                        <option value="Publish">

                            Publish

                        </option>

                    </select>

                </div>

            </div>

            {{-- FOOTER --}}
            <div class="border-t border-gray-100 dark:border-slate-700 px-6 py-5 flex justify-end gap-3">

                <button
                    type="button"
                    @click="openEditPengumuman=false"
                    class="px-5 py-2 rounded-xl border border-gray-300 dark:border-slate-600 hover:bg-gray-100 dark:hover:bg-slate-700 text-gray-700 dark:text-slate-300 font-semibold cursor-pointer">

                    Batal

                </button>

                <button
                    type="submit"
                    class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-2 rounded-xl font-bold shadow-sm cursor-pointer">

                    Update Pengumuman

                </button>

            </div>

        </form>

    </div>

</div>