<div class="bg-white rounded-3xl p-8 flex flex-col justify-center items-center">

    @if(isset($qrUrl) && $qrUrl)
        <img
            src="{{ url($qrUrl) }}"
            alt="QR Code Absensi"
            class="w-56 h-56 object-contain">
        <p class="text-xs text-gray-400 mt-3 text-center">Tunjukkan QR ini saat absensi</p>
    @else
        <div class="w-56 h-56 flex items-center justify-center bg-gray-100 rounded-xl">
            <p class="text-gray-400 text-sm text-center">QR Code belum tersedia.<br>Hubungi admin.</p>
        </div>
    @endif

</div>