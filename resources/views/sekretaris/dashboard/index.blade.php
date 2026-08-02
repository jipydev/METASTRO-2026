<x-app-layout>
    <div class="flex flex-col gap-4 p-4">
        <div class="bg-primary-100 text-primary-900 p-4 rounded-sm flex flex-col gap-4">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-lg">Pengumuman<span class="text-red-500">*</span></h2>
                <span class="icon-[ci--edit-pencil-line-01] text-2xl"></span>
            </div>
            <p class="text-sm">RABES 2: 2 Agustus 2026 pukul 08:00</p>
        </div>

        <div class="bg-primary-100 text-primary-900 p-4 rounded-sm flex flex-col gap-4">
            <h2 class="font-semibold text-lg">Presensi Rapat Besar</h2>

            <div class="flex flex-col gap-2">
                <h3 class="text-sm"><span class="font-semibold text-xl">15/120</span> Panitia telah hadir</h3>

                <div class="flex items-center gap-1">
                    <button class="p-2 text-primary-50 bg-primary-700 rounded-md text-xs">QR ABSEN <span
                            class="icon-[mdi--arrow-right]"></span></button>
                    <button class="p-2 text-primary-50 bg-primary-700 rounded-md text-xs">IZIN <span
                            class="icon-[mdi--arrow-right]"></span></button>
                    <button class="p-2 text-primary-50 bg-primary-700 rounded-md text-xs">SCAN <span
                            class="icon-[mdi--arrow-right]"></span></button>
                    <button class="p-2 text-primary-50 bg-primary-700 rounded-md text-xs">LIHAT <span
                            class="icon-[mdi--arrow-right]"></span></button>
                </div>
            </div>
        </div>

        <div class="bg-primary-100 text-primary-900 p-4 rounded-sm flex flex-col gap-4">
            <h2 class="font-semibold text-lg">Notulensi</h2>

            <div class="flex flex-col gap-2">
                <div class="flex items-center justify-between">
                    <h4>RABES 1</h4>
                    <div class="flex items-center gap-2">
                        <button class="px-2 py-1 text-primary-50 bg-primary-700 rounded-md text-sm">Lihat</button>
                        <button class="px-2 py-1 text-primary-50 bg-primary-700 rounded-md text-sm">Upload</button>
                        <button class="px-2 py-1 text-primary-700 rounded-md text-xl icon-[mdi--trash-can-outline]"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
