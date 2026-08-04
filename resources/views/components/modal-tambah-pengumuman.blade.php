<div
    x-show="openTambahPengumuman"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">

    <div
        x-show="openTambahPengumuman"
        x-transition
        @click.outside="openTambahPengumuman = false"
        class="bg-white rounded-3xl shadow-xl w-full max-w-2xl">

        <form
            action="{{ route('pengumuman.store') }}"
            method="POST"
            enctype="multipart/form-data">

            @csrf

            {{-- Header --}}
            <div class="flex justify-between items-center border-b px-6 py-5">

                <h2 class="text-2xl font-bold text-[#105e75]">

                    Tambah Pengumuman

                </h2>

                <button
                    type="button"
                    @click="openTambahPengumuman=false"
                    class="text-3xl text-gray-500 hover:text-red-500">

                    &times;

                </button>

            </div>

            {{-- Body --}}
            <div class="p-6 space-y-6">

                {{-- Judul --}}
                <div>

                    <label class="block font-semibold mb-2">

                        Judul

                    </label>

                    <input
                        type="text"
                        name="judul"
                        value="{{ old('judul') }}"
                        class="w-full rounded-xl border-gray-300 focus:border-[#105e75] focus:ring-[#105e75]"
                        required>

                    @error('judul')
                        <p class="text-red-500 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- Isi --}}
                <div>

                    <label class="block font-semibold mb-2">

                        Isi Pengumuman

                    </label>

                    <textarea
                        name="isi"
                        rows="6"
                        class="w-full rounded-xl border-gray-300 focus:border-[#105e75] focus:ring-[#105e75]"
                        required>{{ old('isi') }}</textarea>

                    @error('isi')
                        <p class="text-red-500 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- Lampiran --}}
                <div>

                    <label class="block font-semibold mb-2">

                        Lampiran

                    </label>

                    <input
                        type="file"
                        name="lampiran"
                        class="w-full rounded-xl border-gray-300">

                    <p class="text-xs text-gray-400 mt-2">

                        Maksimal 5 MB

                    </p>

                    @error('lampiran')
                        <p class="text-red-500 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- Publish --}}
                <div>

                    <label class="block font-semibold mb-2">

                        Tanggal Publish

                    </label>

                    <input
                        type="datetime-local"
                        name="tanggal_publish"
                        value="{{ old('tanggal_publish') }}"
                        class="w-full rounded-xl border-gray-300 focus:border-[#105e75] focus:ring-[#105e75]"
                        required>

                    @error('tanggal_publish')
                        <p class="text-red-500 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- Status --}}
                <div>

                    <label class="block font-semibold mb-2">

                        Status

                    </label>

                    <select
                        name="status"
                        class="w-full rounded-xl border-gray-300 focus:border-[#105e75] focus:ring-[#105e75]">

                        <option value="Draft">

                            Draft

                        </option>

                        <option value="Publish" selected>

                            Publish

                        </option>

                    </select>

                    @error('status')
                        <p class="text-red-500 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>

            {{-- Footer --}}
            <div class="border-t px-6 py-5 flex justify-end gap-3">

                <button
                    type="button"
                    @click="openTambahPengumuman=false"
                    class="px-5 py-2 rounded-xl border border-gray-300 hover:bg-gray-100">

                    Batal

                </button>

                <button
                    type="submit"
                    class="bg-[#105e75] hover:bg-[#0d4d61] text-white px-6 py-2 rounded-xl">

                    Simpan

                </button>

            </div>

        </form>

    </div>

</div>