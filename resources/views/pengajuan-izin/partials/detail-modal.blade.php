<div x-show="detail" x-cloak
     x-data="{
        statusLabel(status, final = false) {
            const value = (status || '').toLowerCase();
            if (final) {
                return { pending: 'Menunggu Review', diproses: 'Proses Ranger', approved: 'Disetujui', rejected: 'Ditolak' }[value] || value;
            }
            return { pending: 'Pending', approved: 'Disetujui', rejected: 'Ditolak', diproses: 'Proses Ranger' }[value] || value;
        },
        statusClass(status, final = false) {
            const value = (status || '').toLowerCase();
            if (final) {
                return {
                    pending: 'bg-amber-500 text-white',
                    diproses: 'bg-sky-500 text-white',
                    approved: 'bg-emerald-600 text-white',
                    rejected: 'bg-red-600 text-white',
                }[value] || 'bg-slate-400 text-white';
            }
            return {
                pending: 'bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800',
                approved: 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800',
                rejected: 'bg-red-50 text-red-700 border border-red-200 dark:bg-red-950/40 dark:text-red-300 dark:border-red-800',
                diproses: 'bg-sky-50 text-sky-700 border border-sky-200 dark:bg-sky-950/40 dark:text-sky-300 dark:border-sky-800',
            }[value] || 'bg-slate-100 text-slate-600 border border-slate-200';
        }
     }"
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     @keydown.window.escape="detail = null">
    <div class="absolute inset-0 bg-black/50" @click="detail = null"></div>

    <div x-show="detail" x-transition
         class="relative bg-white dark:bg-slate-800 rounded-3xl border border-gray-100 dark:border-slate-700 shadow-xl w-full max-w-lg p-6 text-xs max-h-[90vh] overflow-y-auto">
        <div class="flex items-start justify-between gap-3 mb-4 pb-4 border-b border-gray-100 dark:border-slate-700">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Detail Pengajuan Izin</h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5" x-text="detail?.kegiatan"></p>
            </div>
            <button type="button" @click="detail = null" class="text-slate-400 hover:text-slate-700 dark:hover:text-white font-bold text-lg leading-none">
                &times;
            </button>
        </div>

        <div class="space-y-3.5 text-slate-700 dark:text-slate-300">
            <template x-if="detail?.pemohon">
                <div>
                    <p class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 mb-0.5">Pemohon</p>
                    <p class="font-semibold text-gray-900 dark:text-white" x-text="detail.pemohon"></p>
                    <p class="text-[11px] text-slate-400">
                        <span x-text="detail.nim"></span>
                        <span x-show="detail.divisi"> · </span>
                        <span x-text="detail.divisi"></span>
                    </p>
                </div>
            </template>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <p class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 mb-0.5">Tanggal kegiatan</p>
                    <p class="font-medium text-gray-900 dark:text-white" x-text="detail?.tanggal"></p>
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 mb-1">Jenis</p>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold border"
                          :class="detail?.jenis === 'Sakit'
                            ? 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800'
                            : 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/50 dark:text-blue-300 dark:border-blue-800'"
                          x-text="detail?.jenis"></span>
                </div>
            </div>

            <div>
                <p class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 mb-1">Alasan</p>
                <p class="whitespace-pre-wrap leading-relaxed bg-slate-50 dark:bg-slate-700/40 rounded-xl px-3.5 py-2.5 text-gray-900 dark:text-slate-100" x-text="detail?.alasan"></p>
            </div>

            <div>
                <p class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 mb-1.5">Berkas</p>
                <div class="flex flex-wrap gap-2">
                    <template x-if="detail?.surat">
                        <a :href="detail.surat" target="_blank"
                           class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800/50 dark:hover:bg-slate-700/50 dark:text-slate-300 font-semibold">
                            Surat PDF
                        </a>
                    </template>
                    <template x-if="detail?.bukti">
                        <a :href="detail.bukti" target="_blank"
                           class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:hover:bg-emerald-900/50 dark:text-emerald-300 font-semibold">
                            Lihat Bukti
                        </a>
                    </template>
                    <p x-show="!detail?.surat && !detail?.bukti" class="text-slate-400">Tidak ada lampiran</p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-2 pt-1">
                <div>
                    <p class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 mb-1.5">Koordinator</p>
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-semibold"
                          :class="statusClass(detail?.statusKoor)"
                          x-text="statusLabel(detail?.statusKoor)"></span>
                    <p class="text-[11px] font-medium text-slate-600 dark:text-slate-300 mt-1.5" x-show="detail?.reviewerKoor" x-text="'oleh ' + detail?.reviewerKoor"></p>
                    <p class="text-[10px] text-slate-400" x-show="detail?.reviewedAtKoor" x-text="detail?.reviewedAtKoor"></p>
                    <p class="text-[11px] text-slate-400 mt-0.5" x-show="detail?.catatanKoor" x-text="detail?.catatanKoor"></p>
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 mb-1.5">Ranger</p>
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-semibold"
                          :class="statusClass(detail?.statusRanger)"
                          x-text="statusLabel(detail?.statusRanger)"></span>
                    <p class="text-[11px] font-medium text-slate-600 dark:text-slate-300 mt-1.5" x-show="detail?.reviewerRanger" x-text="'oleh ' + detail?.reviewerRanger"></p>
                    <p class="text-[10px] text-slate-400" x-show="detail?.reviewedAtRanger" x-text="detail?.reviewedAtRanger"></p>
                    <p class="text-[11px] text-slate-400 mt-0.5" x-show="detail?.catatanRanger" x-text="detail?.catatanRanger"></p>
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 mb-1.5">Status akhir</p>
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-semibold"
                          :class="statusClass(detail?.status, true)"
                          x-text="statusLabel(detail?.status, true)"></span>
                </div>
            </div>
        </div>

        <template x-if="detail?.canAct">
            <div class="mt-5 pt-4 border-t border-gray-100 dark:border-slate-700 space-y-3">
                <div>
                    <label class="block text-[10px] uppercase tracking-wider font-semibold text-slate-400 mb-1">Catatan review (opsional)</label>
                    <textarea x-model="detail.catatan"
                              rows="2"
                              placeholder="Tambahkan catatan jika perlu..."
                              class="block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-xl text-xs"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <template x-if="detail?.canDelete">
                        <form :action="detail.deleteUrl" method="POST"
                              onsubmit="return confirm('Hapus pengajuan izin ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="px-4 py-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-700 font-semibold">
                                Hapus
                            </button>
                        </form>
                    </template>
                    <form :action="detail.rejectUrl" method="POST">
                        @csrf
                        <input type="hidden" name="catatan" :value="detail.catatan || ''">
                        <button type="submit"
                                class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold"
                                x-text="detail.rejectLabel || 'Tolak'">
                            Tolak
                        </button>
                    </form>
                    <form :action="detail.approveUrl" method="POST">
                        @csrf
                        <input type="hidden" name="catatan" :value="detail.catatan || ''">
                        <button type="submit"
                                class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold"
                                x-text="detail.approveLabel || 'Setujui'">
                            Setujui
                        </button>
                    </form>
                </div>
            </div>
        </template>

        <template x-if="!detail?.canAct">
            <div class="mt-5 pt-4 border-t border-gray-100 dark:border-slate-700 flex justify-end gap-2">
                <template x-if="detail?.canDelete">
                    <form :action="detail.deleteUrl" method="POST"
                          onsubmit="return confirm('Hapus pengajuan izin ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-4 py-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-700 font-semibold">
                            Hapus
                        </button>
                    </form>
                </template>
                <button type="button" @click="detail = null"
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-200 font-semibold">
                    Tutup
                </button>
            </div>
        </template>
    </div>
</div>
