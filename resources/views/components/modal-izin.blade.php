<div x-cloak x-show="openIzinModal" @keydown.window.escape="openIzinModal = false"
    class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
    <form action="{{ route('izin.store') }}" method="post" enctype="multipart/form-data" @click.outside="openIzinModal = false"
        class="bg-white dark:bg-slate-800 p-4 sm:p-6 md:p-8 border border-primary-500 rounded-t-2xl sm:rounded-2xl w-full text-slate-900 dark:text-slate-100 grid grid-cols-1 gap-4 sm:gap-6 max-w-full sm:max-w-lg md:max-w-xl lg:max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl font-poppins"
        role="dialog" aria-modal="true" aria-labelledby="izinTitle">
        @csrf
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-slate-700 pb-3">
            <h1 id="izinTitle" class="text-xl font-bold lg:text-2xl text-primary-600 dark:text-primary-400">Form Izin</h1>
            <button type="button" @click="openIzinModal = false" aria-label="Tutup"
                class="text-gray-400 dark:text-slate-400 hover:text-gray-700 dark:hover:text-white font-bold text-2xl transition cursor-pointer">&times;</button>
        </div>

        <div>
            <label for="alasan" class="font-semibold text-slate-800 dark:text-slate-200 text-sm">Alasan tidak hadir</label>
            <select name="alasan" id="alasan"
                class="bg-primary-50/50 dark:bg-slate-700 border border-gray-200 dark:border-slate-600 text-slate-900 dark:text-white rounded-xl py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-primary-500 block w-full mt-1.5 text-sm">
                <option value="sakit">Sakit</option>
                <option value="izin">Izin</option>
            </select>
        </div>

        <div>
            <label for="detail" class="font-semibold text-slate-800 dark:text-slate-200 text-sm">Detail penjelasan</label>
            <textarea name="detail" id="detail" placeholder="Ketik di sini..."
                class="bg-primary-50/50 dark:bg-slate-700 border border-gray-200 dark:border-slate-600 text-slate-900 dark:text-white rounded-xl py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-primary-500 block w-full mt-1.5 text-sm"
                rows="3"></textarea>
        </div>

        <div>
            <label for="surat" class="font-semibold text-slate-800 dark:text-slate-200 text-sm">Upload surat izin</label>

            <label for="surat"
                class="bg-primary-50/50 dark:bg-slate-700/60 border border-dashed border-primary-300 dark:border-slate-600 w-full hover:bg-primary-100/50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 py-3 px-4 rounded-xl cursor-pointer transition flex items-center justify-center gap-2 md:py-4 flex-col mt-1.5 text-xs sm:text-sm">
                Unggah format PDF (Maks 5MB).
                <div
                    class="bg-primary-500 py-2 px-4 rounded-lg text-white font-medium hover:bg-primary-600 transition flex items-center gap-2 text-xs shadow-sm mt-1">
                    <span x-text="suratFileName || 'Upload file'"></span>
                    <span class="icon-[grommet-icons--document-upload]"></span>
                </div>
            </label>

            <input type="file" name="surat" id="surat" accept="application/pdf" class="hidden" @change="suratFileName = $event.target.files[0] ? $event.target.files[0].name : ''">
        </div>

        <div>
            <label for="bukti" class="font-semibold text-slate-800 dark:text-slate-200 text-sm">Upload bukti dokumentasi</label>

            <label for="bukti"
                class="bg-primary-50/50 dark:bg-slate-700/60 border border-dashed border-primary-300 dark:border-slate-600 w-full hover:bg-primary-100/50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 py-3 px-4 rounded-xl cursor-pointer transition flex items-center justify-center gap-2 md:py-4 flex-col mt-1.5 text-xs sm:text-sm">
                Unggah format gambar (PNG/JPG).
                <div
                    class="bg-primary-500 py-2 px-4 rounded-lg text-white font-medium hover:bg-primary-600 transition flex items-center gap-2 text-xs shadow-sm mt-1">
                    <span x-text="buktiFileName || 'Upload gambar'"></span>
                    <span class="icon-[grommet-icons--document-upload]"></span>
                </div>
            </label>

            <input type="file" name="bukti" id="bukti" accept="image/*" class="hidden" @change="buktiFileName = $event.target.files[0] ? $event.target.files[0].name : ''">
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <button type="button" @click="openIzinModal = false"
                class="px-5 py-2 rounded-xl border border-gray-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-sm font-semibold hover:bg-gray-100 dark:hover:bg-slate-700 transition cursor-pointer">
                Batal
            </button>
            <button type="submit"
                class="bg-primary-500 py-2 px-6 rounded-xl text-white font-bold hover:bg-primary-600 transition text-sm shadow-sm cursor-pointer">
                Kirim
            </button>
        </div>
    </form>
</div>
