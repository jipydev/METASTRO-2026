<x-app-layout :$title>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 lg:gap-8 p-4">

        {{-- Pengumuman --}}
        <div class="bg-primary-100 text-primary-900 p-4 rounded-md flex flex-col gap-4">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-lg md:text-xl lg:text-2xl">Pengumuman<span class="text-red-500">*</span>
                </h2>
            </div>
            <p class="text-sm">RABES 2: 2 Agustus 2026 pukul 08:00</p>
        </div>

        {{-- Presensi Rapat Besar --}}
        <div class="bg-primary-100 text-primary-900 p-4 rounded-md flex flex-col gap-4">
            <h2 class="font-semibold text-lg md:text-xl lg:text-2xl">Presensi Rapat Besar</h2>

            <div class="flex flex-col gap-4">
                <h3 class="text-sm"><span class="font-semibold text-xl md:text-2xl lg:text-3xl">15/120</span> Panitia
                    telah hadir</h3>

                <div class="flex items-center justify-evenly gap-1">
                    <a href="{{route('kegiatan.QR')  }}" class="py-2 px-4 text-primary-50 bg-primary-700 rounded-md text-xs md:text-sm lg:text-base flex items-center gap-2">
                        <span class="icon-[material-symbols--qr-code]"></span>
                        QR ABSEN <span class="icon-[mdi--arrow-right]"></span></a>
                    <a href=""
                        class="py-2 px-4 text-primary-50 bg-primary-700 rounded-md text-xs md:text-sm lg:text-base flex items-center gap-2">
                        <span class="icon-[akar-icons--file]"></span>
                        IZIN <span class="icon-[mdi--arrow-right]"></span></a>

                </div>
            </div>
        </div>

        {{-- Notulensi --}}
        <div class="bg-primary-100 text-primary-900 p-4 rounded-md flex flex-col gap-4">
            <h2 class="font-semibold text-lg md:text-xl lg:text-2xl">Notulensi</h2>

            <div class="flex flex-col gap-2">
                <div class="flex items-center justify-between">
                    <h4>RABES 1</h4>
                    <div class="flex items-center justify-evenly gap-2">
                        <button
                            class="px-2 py-1 text-primary-50 bg-primary-700 rounded-md text-sm md:text-base lg:text-lg">Lihat</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Timeline --}}
        <div class="bg-primary-100 text-primary-900 p-4 rounded-md flex flex-col gap-4">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-lg md:text-xl lg:text-2xl ">Timeline</h2>
            </div>

            <div class="flex flex-col gap-4 relative">
                <div class="text-zinc-900">
                    <h4 class="text-primary-900 font-medium">RABES 1</h4>
                    <ul>
                        <li>Selasa, 20 Juli 2026</li>
                        <li>pukul 08.00</li>
                        <li>Ruang PGSD 4</li>
                    </ul>
                </div>
                <div class="text-zinc-900">
                    <h4 class="text-primary-900 font-medium">RABES 2</h4>
                    <ul>
                        <li>Selasa, 29 Juli 2026</li>
                        <li>pukul 08.00</li>
                        <li>Ruang PGSD 4</li>
                    </ul>
                </div>

                <a href="#" class="icon-[ep--d-arrow-right] inline absolute right-0 bottom-1"></a>
            </div>
        </div>

    </div>

</x-app-layout>
