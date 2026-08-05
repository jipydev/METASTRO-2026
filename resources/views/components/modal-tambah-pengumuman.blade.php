<div
    x-show="openTambahPengumuman"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">

    <div
        x-show="openTambahPengumuman"
        x-transition
        @click.outside="openTambahPengumuman = false"
        class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl w-full max-w-2xl border border-gray-100 dark:border-slate-700 font-poppins">

        <form
            action="{{ route('pengumuman.store') }}"
            method="POST"
            enctype="multipart/form-data">

            @csrf

            {{-- Header --}}
            <div class="flex justify-between items-center border-b border-gray-100 dark:border-slate-700 px-6 py-5">

                <h2 class="text-2xl font-bold text-primary-600 dark:text-primary-400">

                    Tambah Pengumuman

                </h2>

                <button
                    type="button"
                    @click="openTambahPengumuman=false"
                    class="text-3xl text-gray-400 dark:text-slate-400 hover:text-red-500 transition cursor-pointer">

                    &times;

                </button>

            </div>

            {{-- Body --}}
            <div class="p-6 space-y-6 text-slate-800 dark:text-slate-200">

                {{-- Judul --}}
                <div>

                    <label class="block font-semibold mb-2 text-sm text-gray-700 dark:text-slate-300">

                        Judul

                    </label>

                    <input
                        type="text"
                        name="judul"
                        value="{{ old('judul') }}"
                        class="w-full rounded-xl border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                        required>

                    @error('judul')
                        <p class="text-red-500 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- Isi --}}
                <div>

                    <label class="block font-semibold mb-2 text-sm text-gray-700 dark:text-slate-300">

                        Isi Pengumuman

                    </label>

                    <textarea
                        name="isi"
                        rows="6"
                        class="w-full rounded-xl border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                        required>{{ old('isi') }}</textarea>

                    @error('isi')
                        <p class="text-red-500 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- Lampiran --}}
                <div>

                    <label class="block font-semibold mb-2 text-sm text-gray-700 dark:text-slate-300">

                        Lampiran

                    </label>

                    <input
                        type="file"
                        name="lampiran"
                        class="w-full rounded-xl border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-600 hover:file:bg-primary-100 dark:file:bg-slate-600 dark:file:text-primary-400">

                    <p class="text-xs text-gray-400 dark:text-slate-400 mt-2">

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

                    <label class="block font-semibold mb-2 text-sm text-gray-700 dark:text-slate-300">

                        Tanggal Publish

                    </label>

                    <input
                        type="datetime-local"
                        name="tanggal_publish"
                        value="{{ old('tanggal_publish') }}"
                        class="w-full rounded-xl border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                        required>

                    @error('tanggal_publish')
                        <p class="text-red-500 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- Status --}}
                <div>

                    <label class="block font-semibold mb-2 text-sm text-gray-700 dark:text-slate-300">

                        Status

                    </label>

                    <select
                        name="status"
                        class="w-full rounded-xl border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-primary-500 focus:ring-primary-500">

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
            <div class="border-t border-gray-100 dark:border-slate-700 px-6 py-5 flex justify-end gap-3">

                <button
                    type="button"
                    @click="openTambahPengumuman=false"
                    class="px-5 py-2 rounded-xl border border-gray-300 dark:border-slate-600 hover:bg-gray-100 dark:hover:bg-slate-700 text-gray-700 dark:text-slate-300 font-semibold cursor-pointer">

                    Batal

                </button>

                <button
                    type="submit"
                    class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-2 rounded-xl font-bold shadow-sm cursor-pointer">

                    Simpan

                </button>

            </div>

        </form>

    </div>

</div>