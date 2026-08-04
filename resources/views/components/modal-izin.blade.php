<div x-cloak x-show="openIzinModal" @keydown.window.escape="openIzinModal = false"
    class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 bg-black/40">
    <form action="" method="post" enctype="multipart/form-data" @click.outside="openIzinModal = false"
        class="bg-white p-4 sm:p-6 md:p-8 border border-primary-500 rounded-t-lg sm:rounded-lg w-full text-primary-900 grid grid-cols-1 gap-4 sm:gap-6 max-w-full sm:max-w-lg md:max-w-xl lg:max-w-2xl max-h-[90vh] overflow-y-auto shadow-lg"
        role="dialog" aria-modal="true" aria-labelledby="izinTitle">
        <div class="flex items-center justify-between">
            <h1 id="izinTitle" class="text-xl font-bold lg:text-2xl">Izin</h1>
            <button type="button" @click="openIzinModal = false" aria-label="Tutup"
                class="icon-[material-symbols--close] font-bold text-xl lg:text-2xl text-primary-800 hover:text-primary-900"></button>
        </div>

        <div>
            <label for="alasan" class="font-medium text-primary-800">Alasan tidak hadir</label>
            <select name="alasan" id="alasan"
                class="bg-primary-100 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 block w-full mt-1 sm:mt-2">
                <option value="sakit">Sakit</option>
                <option value="izin">Izin</option>
            </select>
        </div>

        <div>
            <label for="detail" class="font-medium text-primary-800">Detail penjelasan</label>
            <textarea name="detail" id="detail" placeholder="Ketik di sini..."
                class="bg-primary-100 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 block w-full mt-1 sm:mt-2"
                rows="3"></textarea>
        </div>

        <div>
            <label for="surat" class="font-medium text-primary-800">Upload surat izin</label>

            <label for="surat"
                class="bg-primary-100 w-full hover:bg-primary-200 text-primary-800 hover:text-primary-900 py-2 px-4 rounded-md cursor-pointer transition flex items-center justify-center gap-2 md:py-4 flex-col sm:mt-2">
                Unggah format pdf.
                <div
                    class="bg-primary-700 py-2 px-4 rounded-md text-primary-50 font-medium hover:bg-primary-800 transition flex items-center gap-2">
                    <span>Upload file</span>
                    <span class="icon-[grommet-icons--document-upload]"></span>
                </div>
            </label>

            <input type="file" name="surat" id="surat" accept="application/pdf" class="hidden">
        </div>

        <div>
            <label for="bukti" class="font-medium text-primary-800">Upload bukti dokumentasi</label>

            <label for="bukti"
                class="bg-primary-100 w-full hover:bg-primary-200 text-primary-800 hover:text-primary-900 py-2 px-4 rounded-md cursor-pointer transition flex items-center justify-center gap-2 md:py-4 flex-col sm:mt-2">
                Unggah format png.
                <div
                    class="bg-primary-700 py-2 px-4 rounded-md text-primary-50 font-medium hover:bg-primary-800 transition flex items-center gap-2">
                    <span>Upload gambar</span>
                    <span class="icon-[grommet-icons--document-upload]"></span>
                </div>
            </label>

            <input type="file" name="bukti" id="bukti" accept="image/*" class="hidden">
        </div>

        <div class="flex justify-end">
            <button type="submit"
                class="bg-primary-700 py-2 px-4 rounded-md text-primary-50 font-medium hover:bg-primary-800 transition">
                Kirim
            </button>
        </div>
    </form>
</div>
