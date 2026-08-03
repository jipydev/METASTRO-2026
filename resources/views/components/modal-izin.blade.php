<div x-cloak x-show="openIzinModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
    <form action="" method="post" @click.outside="openIzinModal = false"
        class="bg-white p-4 border border-primary-500 rounded-md w-full text-primary-900 grid grid-cols-1 lg:grid-cols-2 gap-4 max-w-md lg:max-w-xl lg:gap-8 lg:p-8">
        <div class="flex items-center justify-between lg:col-span-2">
            <h1 class="text-xl font-bold lg:text-2xl">Izin</h1>
            <span @click="openIzinModal = false" class="icon-[material-symbols--close] font-bold text-xl lg:text-2xl"></span>
        </div>

        <div>
            <label for="alasan" class="font-medium text-primary-800">Alasan tidak hadir</label>
            <select name="alasan" id="alasan"
                class="bg-primary-100 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 block w-full mt-1 lg:mt-4">
                <option value="sakit">Sakit</option>
                <option value="izin">Izin</option>
            </select>
        </div>

        <div>
            <label for="detail" class="font-medium text-primary-800">Detail penjelasan</label>
            <textarea name="detail" id="detail" placeholder="Ketik di sini..."
                class="bg-primary-100 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 block w-full mt-1 lg:mt-4"
                rows="2"></textarea>
        </div>

        <div>
            <label for="surat" class="font-medium text-primary-800">Upload surat izin</label>

            <label for="surat"
                class="bg-primary-100 w-full hover:bg-primary-200 text-primary-800 hover:text-primary-900 py-2 px-4 rounded-md cursor-pointer transition flex items-center justify-center gap-2 md:py-4 flex-col lg:mt-2">
                Unggah format pdf.
                <button
                    class="bg-primary-700 py-2 px-4 rounded-md text-primary-50 font-medium hover:bg-primary-800 transition flex items-center gap-2">
                    Upload file
                    <span class="icon-[grommet-icons--document-upload]"></span>
                </button>
            </label>

            <input type="file" name="surat" id="surat" class="hidden">
        </div>

        <div>
            <label for="surat" class="font-medium text-primary-800">Upload bukti dokumentasi</label>

            <label for="surat"
                class="bg-primary-100 w-full hover:bg-primary-200 text-primary-800 hover:text-primary-900 py-2 px-4 rounded-md cursor-pointer transition flex items-center justify-center gap-2 md:py-4 flex-col lg:mt-4">
                Unggah format png.
                <button
                    class="bg-primary-700 py-2 px-4 rounded-md text-primary-50 font-medium hover:bg-primary-800 transition flex items-center gap-2">
                    Upload gambar
                    <span class="icon-[grommet-icons--document-upload]"></span>
                </button>
            </label>

            <input type="file" name="surat" id="surat" class="hidden">
        </div>

        <div class="flex justify-end lg:col-span-2">
            <button type="submit"
                class="bg-primary-700 py-2 px-4 rounded-md text-primary-50 font-medium hover:bg-primary-800 transition">
                Kirim
            </button>
        </div>
    </form>
</div>
