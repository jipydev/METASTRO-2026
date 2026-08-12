<div x-show="openJadwalAbsen" 
     style="display: none;"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
     
    <div x-show="openJadwalAbsen"
         @click.outside="openJadwalAbsen = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 rounded-[2rem] p-6 w-full max-w-md relative shadow-2xl font-poppins">
        
        <!-- Tombol Close (X) -->
        <button type="button" @click="openJadwalAbsen = false" class="absolute top-5 right-6 text-gray-400 dark:text-slate-400 hover:text-gray-700 dark:hover:text-white font-bold text-lg transition cursor-pointer">
            &times;
        </button>

        <div class="flex items-center gap-3 mb-4 border-b border-gray-100 dark:border-slate-700 pb-3">
            <div class="p-2.5 rounded-xl bg-primary-50 dark:bg-primary-950/60 text-primary-600 dark:text-primary-400 text-xl">
                ⏱️
            </div>
            <div>
                <h3 class="text-slate-900 dark:text-white font-bold text-lg">Kontrol & Jadwal Absensi</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400" x-text="selectedAbsen ? selectedAbsen.judul : ''"></p>
            </div>
        </div>

        <!-- Form Penjadwalan Absensi -->
        <form x-bind:action="selectedAbsen && selectedAbsen.id ? '/absen/' + selectedAbsen.id + '/schedule' : '#'"
              method="POST" class="space-y-4">
            @csrf

            <!-- Status Absen -->
            <div>
                <label class="block text-slate-700 dark:text-slate-300 font-semibold text-xs mb-1">Status Absensi:</label>
                <select name="status_absen" x-model="selectedAbsen.status_absen" required class="w-full bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl py-2 px-3 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                    <option value="Buka">Buka (Manual)</option>
                    <option value="Tutup">Tutup (Manual)</option>
                    <option value="Dijadwalkan">Dijadwalkan OOT / Otomatis Waktu</option>
                </select>
            </div>

            <!-- Jam Absen Dibuka -->
            <div>
                <label class="block text-slate-700 dark:text-slate-300 font-semibold text-xs mb-1">Kapan Absen Dibuka (Jam Buka):</label>
                <input type="time" name="waktu_buka" x-model="selectedAbsen.waktu_buka" class="w-full bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl py-2 px-3 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                <p class="text-[11px] text-slate-400 mt-0.5">Sebelum jam ini, QR scanner akan menolak absensi.</p>
            </div>

            <!-- Jam Terhitung Telat -->
            <div>
                <label class="block text-slate-700 dark:text-slate-300 font-semibold text-xs mb-1">Absen Terhitung Telat (Jam Batas Tepat Waktu):</label>
                <input type="time" name="waktu_telat" x-model="selectedAbsen.waktu_telat" class="w-full bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl py-2 px-3 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                <p class="text-[11px] text-slate-400 mt-0.5">Absen setelah jam ini akan otomatis tercatat sebagai "Telat".</p>
            </div>

            <!-- Jam Absen Ditutup -->
            <div>
                <label class="block text-slate-700 dark:text-slate-300 font-semibold text-xs mb-1">Kapan Absen Ditutup (Jam Tutup):</label>
                <input type="time" name="waktu_tutup" x-model="selectedAbsen.waktu_tutup" class="w-full bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl py-2 px-3 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                <p class="text-[11px] text-slate-400 mt-0.5">Setelah jam ini, QR scanner akan menolak absensi.</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 pt-3 border-t border-gray-100 dark:border-slate-700">
                <button type="button" @click="openJadwalAbsen = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-semibold hover:bg-slate-200 cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-xl text-xs font-bold shadow-md cursor-pointer">
                    Simpan Pengaturan Absensi
                </button>
            </div>
        </form>
    </div>
</div>
