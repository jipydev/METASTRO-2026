<div
    x-show="openEditPengumuman"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">

    <div
        x-show="openEditPengumuman"
        x-transition
        @click.outside="openEditPengumuman=false"
        class="bg-white rounded-3xl shadow-xl w-full max-w-2xl">

        <form

            x-bind:action="
                selectedPengumuman && selectedPengumuman.id
                ? '/pengumuman/' + selectedPengumuman.id
                : '{{ route('pengumuman.store') }}'
            "

            method="POST"
            enctype="multipart/form-data">

            @csrf

            <template x-if="selectedPengumuman && selectedPengumuman.id">
                <input
                    type="hidden"
                    name="_method"
                    value="PUT">
            </template>

            <div class="flex justify-between items-center border-b px-6 py-5">

                <h2
                    class="text-xl font-bold text-[#105e75]"
                    x-text="selectedPengumuman && selectedPengumuman.id ? 'Edit Pengumuman' : 'Tambah Pengumuman'">
                </h2>

                <button
                    type="button"
                    @click="openEditPengumuman=false"
                    class="text-2xl">

                    &times;

                </button>

            </div>

            <div class="p-6 space-y-5">

                <div>

                    <label class="font-semibold">

                        Judul

                    </label>

                    <input

                        type="text"

                        name="judul"

                        x-model="selectedPengumuman.judul"

                        class="mt-2 w-full rounded-xl border-gray-300"

                        required>

                </div>

                <div>

                    <label class="font-semibold">

                        Isi

                    </label>

                    <textarea

                        name="isi"

                        rows="6"

                        x-model="selectedPengumuman.isi"

                        class="mt-2 w-full rounded-xl border-gray-300"

                        required></textarea>

                </div>

                <div>

                    <label class="font-semibold">

                        Lampiran

                    </label>

                    <input
                        type="file"
                        name="lampiran"
                        class="mt-2 w-full">

                    <template x-if="selectedPengumuman && selectedPengumuman.lampiran">

                        <a

                            :href="'/storage/'+selectedPengumuman.lampiran"

                            target="_blank"

                            class="text-blue-600 text-sm">

                            Lampiran Saat Ini

                        </a>

                    </template>

                </div>

                <div>

                    <label class="font-semibold">

                        Tanggal Publish

                    </label>

                    <input

                        type="datetime-local"

                        name="tanggal_publish"

                        x-model="selectedPengumuman.tanggal_publish"

                        class="mt-2 w-full rounded-xl border-gray-300">

                </div>

                <div>

                    <label class="font-semibold">

                        Status

                    </label>

                    <select

                        name="status"

                        x-model="selectedPengumuman.status"

                        class="mt-2 w-full rounded-xl border-gray-300">

                        <option value="Draft">

                            Draft

                        </option>

                        <option value="Publish">

                            Publish

                        </option>

                    </select>

                </div>

            </div>

            <div class="border-t px-6 py-5 flex justify-between">

                <template x-if="selectedPengumuman && selectedPengumuman.id">

                    <button

                        type="submit"

                        formaction=""

                        @click.prevent="

                            if(confirm('Hapus pengumuman?')){

                                let f=document.createElement('form');

                                f.method='POST';

                                f.action='/pengumuman/'+selectedPengumuman.id;

                                f.innerHTML='@csrf<input type=hidden name=_method value=DELETE>';

                                document.body.appendChild(f);

                                f.submit();

                            }

                        "

                        class="bg-red-600 text-white px-5 py-2 rounded-xl">

                        Hapus

                    </button>

                </template>

                <div class="flex gap-3">

                    <button

                        type="button"

                        @click="openEditPengumuman=false"

                        class="px-5 py-2 border rounded-xl">

                        Batal

                    </button>

                    <button

                        type="submit"

                        class="bg-[#105e75] text-white px-6 py-2 rounded-xl">

                        Simpan

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>