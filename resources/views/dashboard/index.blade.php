<x-app-layout>
    <!-- TAMBAHKAN x-data DI SINI UNTUK MENGONTROL MODAL -->
    <div x-data="{ openEditPengumuman: false, openEditTimeline: false, openIzinModal: false }" class="min-h-screen bg-white md:bg-gray-50 pb-10">
        <div class="p-4 md:p-8 max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mt-2">

            <!-- PENGUMUMAN -->
            <div
                class="bg-[#f2f7fb] md:bg-white md:shadow-sm rounded-2xl p-5 md:p-6 relative md:col-span-2 lg:col-span-3 border md:border-gray-100">
                <h2 class="text-[#105e75] font-bold text-lg md:text-xl mb-2">Pengumuman<span class="text-red-500">*</span>
                </h2>
                <p class="text-[#105e75] font-medium text-sm md:text-base">RABES 2: 2 Agustus 2026 pukul 08.00</p>

                <button @click="openEditPengumuman = true"
                    class="absolute top-5 right-5 text-[#105e75] hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                        </path>
                    </svg>
                </button>

            </div>

            <!-- PRESENSI -->
            <div
                class="bg-[#f2f7fb] md:bg-white md:shadow-sm rounded-2xl p-5 md:p-6 border md:border-gray-100 lg:col-span-2 flex flex-col justify-between">
                <div>
                    <h2 class="text-[#105e75] font-bold text-lg md:text-xl mb-1">Presensi Rapat Besar</h2>
                    <div class="flex items-baseline space-x-2 mb-6">
                        <span class="text-4xl md:text-5xl font-bold text-[#105e75]">5/120</span>
                        <span class="text-sm md:text-base font-medium text-gray-500">Panitia telah hadir</span>
                    </div>
                </div>

                <div class="flex flex-wrap lg:flex-nowrap gap-2 md:gap-3">
                    <button
                        class="cursor-pointer flex-1 min-w-30 bg-[#105e75] hover:bg-[#0b4354] text-white text-xs md:text-sm font-bold py-3 rounded-lg flex justify-center items-center gap-1 transition">
                        <span class="icon-[material-symbols--qr-code] size-4 md:size-5"></span>
                        QR ABSEN
                    </button>

                    <button @click="openIzinModal = !openIzinModal"
                        class="cursor-pointer flex-1 min-w-30 bg-[#105e75] hover:bg-[#0b4354] text-white text-xs md:text-sm font-bold py-3 rounded-lg flex justify-center items-center gap-1 transition">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        IZIN
                    </button>


                    <button
                        class="cursor-pointer flex-1 min-w-30 bg-[#105e75] hover:bg-[#0b4354] text-white text-xs md:text-sm font-bold py-3 rounded-lg flex justify-center items-center transition gap-1">
                        <span class="icon-[boxicons--scan-filled] size-4 md:size-5"></span>
                        SCAN</button>
                    <button
                        class="cursor-pointer flex-1 min-w-30 bg-[#105e75] hover:bg-[#0b4354] text-white text-xs md:text-sm font-bold py-3 rounded-lg flex justify-center items-center transition gap-1">
                        <span class="icon-[mdi--eye]"></span>
                        LIHAT</button>

                </div>
            </div>

            <!-- TIMELINE -->
            <div
                class="bg-[#f2f7fb] md:bg-white md:shadow-sm rounded-2xl p-5 md:p-6 relative border md:border-gray-100 h-full">
                <h2 class="text-[#105e75] font-bold text-lg md:text-xl mb-3">Timeline</h2>

                <button @click="openEditTimeline = true"
                    class="absolute top-5 right-5 text-[#105e75] hover:scale-110 transition">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                        </path>
                    </svg>
                </button>

                <div class="text-[#105e75] font-medium text-sm md:text-base md:bg-blue-50/50 md:p-4 rounded-xl">
                    <p class="font-bold mb-1 text-base md:text-lg">RABES 1</p>
                    <p>Selasa, 20 Juli 2026</p>
                    <p>pukul 08.00</p>
                    <div class="flex justify-between items-end mt-4">
                        <p class="font-semibold">Ruang PGSD 4</p>
                        <a href="#"
                            class="text-[#105e75] md:bg-blue-100 hover:bg-blue-200 md:px-3 md:py-1 rounded-md font-bold text-lg leading-none transition"></a>
                    </div>
                </div>
            </div>

            <div
                class="bg-[#f2f7fb] md:bg-white md:shadow-sm rounded-2xl p-5 md:p-6 border md:border-gray-100 md:col-span-2 lg:col-span-3">
                <h2 class="text-[#105e75] font-bold text-lg md:text-xl mb-4">Notulensi</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
<<<<<<< HEAD
                    <div class="flex justify-between items-center md:bg-gray-50 md:p-4 rounded-xl md:border md:border-gray-100 mb-3 md:mb-0">
                        <span class="text-[#105e75] font-bold text-sm md:text-base">{{ $rabes['title'] }}</span>
                        
=======
                    <div
                        class="flex justify-between items-center md:bg-gray-50 md:p-4 rounded-xl md:border md:border-gray-100 mb-3 md:mb-0">
                        <span
                            class="text-[#105e75] font-bold text-sm md:text-base">{{ $rabes['title'] ?? 'Rapat Besar 1' }}</span>

>>>>>>> d0c2b5edca525371c85e1f95468e2567b874d38b
                        <div class="flex space-x-2 items-center">
                            <button
                                class="bg-[#105e75] hover:bg-[#0b4354] text-white px-4 py-1.5 md:py-2 rounded-lg text-xs md:text-sm font-bold transition">Lihat</button>


                            <button
                                class="bg-[#105e75] hover:bg-[#0b4354] text-white px-4 py-1.5 md:py-2 rounded-lg text-xs md:text-sm font-bold transition">Upload</button>
                            <button
                                class="text-[#105e75] hover:text-red-600 md:hover:bg-red-50 p-1.5 rounded-md transition">
                                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                            </button>

                        </div>
                    </div>

                </div>
            </div>

        </div>

        <x-modal-edit-pengumuman />
        <x-modal-edit-timeline />
        <x-modal-izin />

    </div>
</x-app-layout>
