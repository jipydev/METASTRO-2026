<div class="bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-8 flex flex-col justify-center items-center shadow-lg border border-gray-100 dark:border-slate-700">

    @if(isset($qrUrl) && $qrUrl)
        <div class="p-3 bg-white rounded-2xl shadow-inner border border-gray-100">
            <img
                src="{{ $qrUrl }}"
                alt="QR Code Absensi"
                class="w-52 h-52 sm:w-56 sm:h-56 object-contain">
        </div>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-4 text-center font-medium">Tunjukkan QR ini saat absensi</p>
    @else
        <div class="w-52 h-52 sm:w-56 sm:h-56 flex items-center justify-center bg-gray-100 dark:bg-slate-700 rounded-2xl">
            <p class="text-slate-400 dark:text-slate-400 text-sm text-center">QR Code belum tersedia.<br>Hubungi admin.</p>
        </div>
    @endif

</div>