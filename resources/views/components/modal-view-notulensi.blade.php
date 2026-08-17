<div x-show="openViewNotulensi" style="display: none;" class="fixed inset-0 z-[999] overflow-y-auto" aria-labelledby="modal-notulensi-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div x-show="openViewNotulensi"
             x-transition.opacity
             class="fixed inset-0 bg-black/50 backdrop-blur-sm"
             aria-hidden="true"
             @click="openViewNotulensi = false"></div>

        <article x-show="openViewNotulensi"
             x-transition
             class="relative w-full max-w-lg bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 overflow-hidden font-poppins">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-brand-600"></div>

            <div class="pl-6 pr-5 py-5">
                <div class="flex items-start justify-between gap-3 mb-2">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white leading-snug" id="modal-notulensi-title" x-text="selectedNotulensi.judul">
                        Notulensi
                    </h3>
                    <button type="button" @click="openViewNotulensi = false"
                            class="shrink-0 text-slate-400 hover:text-slate-700 dark:hover:text-white transition w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 dark:hover:bg-slate-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="flex flex-wrap items-center gap-2 text-[11px] text-slate-400 mb-4 font-medium">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700 dark:bg-slate-800/60 dark:text-slate-300" x-text="selectedNotulensi.kegiatan"></span>
                    <span>•</span>
                    <span x-text="selectedNotulensi.tanggal"></span>
                    <span>•</span>
                    <span x-text="selectedNotulensi.pembuat"></span>
                </div>

                <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-300 whitespace-pre-line"
                   x-show="selectedNotulensi.isi"
                   x-text="selectedNotulensi.isi"></p>
                <p class="text-sm text-slate-400" x-show="!selectedNotulensi.isi">Tidak ada isi notulensi.</p>

                <div class="mt-5 pt-4 border-t border-slate-200/80 dark:border-slate-700/80" x-show="selectedNotulensi.lampiranUrl">
                    <a :href="selectedNotulensi.lampiranUrl" target="_blank"
                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-brand-600 dark:text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                        </svg>
                        <span>Lihat Lampiran PDF</span>
                    </a>
                </div>
            </div>
        </article>
    </div>
</div>
