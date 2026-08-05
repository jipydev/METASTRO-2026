<!-- resources/views/components/modal-view-notulensi.blade.php -->
<div x-show="openViewNotulensi" style="display: none;" class="fixed inset-0 z-[999] flex items-center justify-center p-3 sm:p-5" aria-labelledby="modal-notulensi-title" role="dialog" aria-modal="true">

    <!-- Background Overlay -->
    <div x-show="openViewNotulensi"
         x-transition.opacity
         class="fixed inset-0 bg-black/60 backdrop-blur-sm"
         aria-hidden="true"
         @click="openViewNotulensi = false"></div>

    <!-- Modal Panel -->
    <div x-show="openViewNotulensi"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="relative w-full max-w-3xl bg-white dark:bg-slate-800 rounded-2xl shadow-2xl flex flex-col font-poppins border border-gray-100 dark:border-slate-700"
         style="height: 90vh; max-height: 90vh;">

        <!-- Header -->
        <div class="flex justify-between items-center px-5 py-3.5 border-b border-gray-100 dark:border-slate-700 shrink-0">
            <h3 class="text-base font-bold text-primary-600 dark:text-primary-400 truncate pr-4" id="modal-notulensi-title" x-text="notulensiTitle">
                Notulensi
            </h3>
            <div class="flex items-center gap-2 shrink-0">
                <!-- Download Button -->
                <a :href="notulensiDownloadUrl" download
                   x-show="notulensiDownloadUrl"
                   class="flex items-center gap-1.5 bg-primary-50 hover:bg-primary-100 dark:bg-slate-700 dark:hover:bg-slate-600 text-primary-600 dark:text-primary-400 text-xs font-bold px-3 py-1.5 rounded-lg transition cursor-pointer no-underline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Unduh
                </a>
                <!-- Close Button -->
                <button @click="openViewNotulensi = false"
                        class="text-gray-400 dark:text-slate-400 hover:text-gray-700 dark:hover:text-white transition w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-slate-700 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- PDF Viewer Body -->
        <div class="flex-1 relative overflow-hidden bg-gray-100 dark:bg-slate-900 rounded-b-2xl" style="min-height: 0;">

            <!-- No PDF state -->
            <template x-if="!notulensiViewUrl">
                <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 dark:text-slate-500">
                    <svg class="w-14 h-14 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="font-semibold text-sm">Tidak ada lampiran PDF</p>
                </div>
            </template>

            <!-- Iframe PDF Viewer -->
            <template x-if="notulensiViewUrl">
                <div class="w-full h-full relative">
                    <!-- Loading spinner -->
                    <div id="pdfIframeLoading" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-100 dark:bg-slate-900 z-10">
                        <div class="w-10 h-10 border-4 border-primary-500 border-t-transparent rounded-full animate-spin mb-3"></div>
                        <p class="text-sm font-semibold text-primary-600 dark:text-primary-400">Memuat PDF...</p>
                    </div>
                    <iframe
                        :src="notulensiViewUrl"
                        class="w-full h-full border-0 rounded-b-2xl"
                        @load="document.getElementById('pdfIframeLoading') && (document.getElementById('pdfIframeLoading').style.display = 'none')"
                        title="Preview Notulensi">
                    </iframe>
                </div>
            </template>
        </div>
    </div>
</div>