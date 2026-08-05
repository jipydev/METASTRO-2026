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
        x-transition
        @click.outside="openEditPengumuman=false"
        class="bg-white rounded-3xl shadow-xl w-full max-w-2xl">

        <form
            x-bind:action="'{{ url('pengumuman') }}/' + selectedPengumuman.id"
            method="POST"
            enctype="multipart/form-data"
            class="flex flex-col h-full">

            @csrf
            @method('PUT')

            {{-- HEADER --}}
            <div class="flex justify-between items-center border-b px-6 py-5">

                <h2 class="text-2xl font-bold text-[#105e75]">

                    Edit Pengumuman

                </h2>

                <button
                    type="button"
                    @click="openEditPengumuman=false"
                    class="text-3xl text-gray-500 hover:text-red-500">

                    &times;

                </button>
            </div>

            {{-- BODY --}}
            <div class="p-6 space-y-6">

                {{-- Judul --}}
                <div>

                    <label class="block font-semibold mb-2">

                        Judul

                    </label>

                    <input
                        type="text"
                        name="judul"
                        x-model="selectedPengumuman.judul"
                        class="w-full rounded-xl border-gray-300 focus:border-[#105e75] focus:ring-[#105e75]"
                        required>
                </div>

                {{-- Isi --}}
                <div>

                    <label class="block font-semibold mb-2">

                        Isi Pengumuman

                    </label>

                    <textarea
                        name="isi"
                        rows="4"
                        x-model="selectedPengumuman.isi"
                        class="w-full rounded-xl border-gray-300 focus:border-[#105e75] focus:ring-[#105e75]"
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
                <div>

                    <label class="block font-semibold mb-2">

                        Ganti Lampiran

                    </label>

                    <input
                        type="file"
                        name="lampiran"
                        class="w-full rounded-xl border-gray-300">

                    <template x-if="selectedPengumuman.lampiran">

                        <div class="mt-3">

                            <a
                                :href="'/storage/' + selectedPengumuman.lampiran"
                                target="_blank"
                                class="text-blue-600 hover:underline">

                                📎 Lihat Lampiran Saat Ini

                            </a>
                        </div>
                    </template>

                </div>

                {{-- Publish --}}
                <div>

                    <label class="block font-semibold mb-2">

                        Tanggal Publish

                    </label>

                    <input
                        type="datetime-local"
                        name="tanggal_publish"
                        x-model="selectedPengumuman.tanggal_publish"
                        class="w-full rounded-xl border-gray-300 focus:border-[#105e75] focus:ring-[#105e75]"
                        required>

                </div>

                {{-- Status --}}
                <div>

                    <label class="block font-semibold mb-2">

                        Status

                    </label>

                    <select
                        name="status"
                        x-model="selectedPengumuman.status"
                        class="w-full rounded-xl border-gray-300 focus:border-[#105e75] focus:ring-[#105e75]">

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
            <div class="border-t px-6 py-5 flex justify-end gap-3">

                <button
                    type="button"
                    @click="openEditPengumuman=false"
                    class="px-5 py-2 rounded-xl border border-gray-300 hover:bg-gray-100">

                    Batal
                </button>
                <button
                    type="submit"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-xl">

                    Update Pengumuman

                </button>
            </div>

        </form>
    </div>
</div>