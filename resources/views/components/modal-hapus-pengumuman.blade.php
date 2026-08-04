<div
    x-show="openDeletePengumuman"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">

    <div
        @click.outside="openDeletePengumuman = false"
        class="bg-white rounded-2xl w-full max-w-md p-6">

        <h2 class="text-xl font-bold text-red-600">
            Hapus Pengumuman
        </h2>

        <p class="mt-3">
            Yakin ingin menghapus
            <strong x-text="selectedPengumuman?.judul"></strong> ?
        </p>

        <form
            :action="'{{ url('pengumuman') }}/' + selectedPengumuman.id"
            method="POST"
            class="mt-6">

            @csrf
            @method('DELETE')

            <div class="flex justify-end gap-3">

                <button
                    type="button"
                    @click="openDeletePengumuman=false"
                    class="px-4 py-2 border rounded-lg">

                    Batal

                </button>

                <button
                    type="submit"
                    class="bg-red-600 text-white px-5 py-2 rounded-lg">

                    Hapus

                </button>

            </div>

        </form>

    </div>

</div>