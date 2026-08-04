<x-app-layout>
    <!-- TAMBAHKAN x-data DI SINI UNTUK MENGONTROL MODAL -->
    <div x-data="{
    
        openEditPengumuman: false,
    
        selectedPengumuman: {
            judul: '',
            isi: '',
            status: 'Publish',
            tanggal_publish: '',
            lampiran: null
        },
    
        openEditTimeline: false,
        openIzinModal: false,
        openAddNotulensi: false,
        openViewNotulensi: false,
        notulensiTitle: ''
    
    }" class="min-h-screen bg-white md:bg-gray-50 pb-10">
        <div class="p-4 md:p-8 max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mt-2">


            <!-- =========================
    PENGUMUMAN
========================= -->

            <div
                class="bg-[#f2f7fb] md:bg-white md:shadow-sm rounded-2xl
    p-6 md:col-span-2 lg:col-span-3 border md:border-gray-100">

                <!-- Header -->
                <div class="flex items-center justify-between mb-6">

                    <div>
                        <h2 class="text-2xl font-bold text-[#105e75]">
                            Pengumuman
                        </h2>

                        <p class="text-sm text-gray-500">
                            Informasi terbaru panitia.
                        </p>
                    </div>

                    @role('Admin|Sekretaris')
                        <button
                            @click="

selectedPengumuman={
    judul:'',
    isi:'',
    status:'Publish',
    tanggal_publish:'',
    lampiran:null
};

openEditPengumuman=true;

"
                            class="bg-[#105e75] text-white px-5 py-2 rounded-xl">

                            + Tambah

                        </button>
                    @endrole

                </div>

                @forelse($pengumumanList as $item)
                    <div class="bg-white border rounded-2xl p-6 mb-5 shadow-sm">

                        <!-- Header Card -->
                        <div class="flex justify-between items-start">

                            <div>

                                <h3 class="text-xl font-bold text-[#105e75]">
                                    {{ $item->judul }}
                                </h3>

                                <p class="text-sm text-gray-400 mt-1">

                                    Publish
                                    {{ optional($item->tanggal_publish)->translatedFormat('d F Y H:i') }}

                                    •

                                    {{ $item->pembuat?->name }}

                                </p>

                            </div>

                            @role('Admin|Sekretaris')
                               ` <div class="flex gap-2">

                                    <!-- EDIT -->

                                 <button
    type="button"
    data-item='@json($item)'
    @click="
        selectedPengumuman = JSON.parse($el.dataset.item);
        openEditPengumuman = true;
    "
    class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">

    Edit

</button>

                                    <!-- HAPUS -->

                                    <form action="{{ route('pengumuman.destroy', $item->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus pengumuman ini?')">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">

                                            Hapus

                                        </button>

                                    </form>

                                </div>
                            @endrole

                        </div>

                        <!-- Isi -->

                        <div class="mt-5">

                            <p class="text-gray-700 whitespace-pre-line leading-7">

                                {{ $item->isi }}

                            </p>

                        </div>

                        <!-- Lampiran -->

                        @if ($item->lampiran)
                            <div class="mt-5">

                                <a href="{{ asset('storage/' . $item->lampiran) }}" target="_blank"
                                    class="inline-flex items-center gap-2 text-blue-600 hover:underline">

                                    📎 Lampiran

                                </a>

                            </div>
                        @endif

                        <!-- Footer -->

                        <div class="mt-6 border-t pt-4 flex flex-wrap justify-between text-sm text-gray-500">

                            <span>

                                Status :

                                <span class="font-semibold">

                                    {{ $item->status }}

                                </span>

                            </span>

                            <span>

                                Dibuat :

                                {{ $item->created_at->translatedFormat('d F Y') }}

                            </span>

                        </div>

                    </div>

                @empty

                    <div class="text-center py-16">

                        <div class="text-6xl">

                            📢

                        </div>

                        <h3 class="mt-4 text-xl font-bold text-gray-500">

                            Belum ada pengumuman

                        </h3>

                        <p class="text-gray-400 mt-2">

                            Klik tombol <strong>Tambah</strong> untuk membuat pengumuman pertama.

                        </p>

                    </div>
                @endforelse

            </div>


            <!-- PRESENSI -->
            <div
                class="bg-[#f2f7fb] md:bg-white md:shadow-sm rounded-2xl p-5 md:p-6 border md:border-gray-100 lg:col-span-2 flex flex-col justify-between">
                <div>
                    <h2 class="text-[#105e75] font-bold text-lg md:text-xl mb-1">Presensi Rapat Besar</h2>
                    <div class="flex items-baseline space-x-2 mb-6">
                        <span class="text-4xl md:text-5xl font-bold text-[#105e75]">
                            {{ $rapatTerbaru ? $rapatTerbaru->hadir : 0 }}/{{ $rapatTerbaru ? $rapatTerbaru->total : 0 }}
                        </span>
                        <span class="text-sm md:text-base font-medium text-gray-500">Panitia telah hadir</span>
                    </div>
                </div>


                <div class="flex flex-wrap lg:flex-nowrap gap-2 md:gap-3">
                    {{-- qr --}}
                    <a href="{{ route('dashboard.presensi.qr') }}"
                        class="cursor-pointer flex-1 min-w-30 bg-[#105e75] hover:bg-[#0b4354] text-white text-xs md:text-sm font-bold py-3 rounded-lg flex justify-center items-center gap-1 transition">
                        <span class="icon-[material-symbols--qr-code] size-4 md:size-5"></span>
                        QR ABSEN
                    </a>

                    {{-- izin --}}
                    <button @click="openIzinModal = !openIzinModal"
                        class="cursor-pointer flex-1 min-w-30 bg-[#105e75] hover:bg-[#0b4354] text-white text-xs md:text-sm font-bold py-3 rounded-lg flex justify-center items-center gap-1 transition">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        IZIN
                    </button>

                    {{-- scan presensi --}}
                    @can('scan presensi')
                        <a href="{{ url('/scan') }}"
                            class="cursor-pointer flex-1 min-w-30 bg-[#105e75] hover:bg-[#0b4354] text-white text-xs md:text-sm font-bold py-3 rounded-lg flex justify-center items-center transition gap-1">
                            <span class="icon-[boxicons--scan-filled] size-4 md:size-5"></span>
                            SCAN</a>
                    @endcan

                    {{-- lihat presensi --}}
                    @can('lihat presensi')
                        <a href="{{ route('dashboard.presensi.index') }}"
                            class="cursor-pointer flex-1 min-w-30 bg-[#105e75] hover:bg-[#0b4354] text-white text-xs md:text-sm font-bold py-3 rounded-lg flex justify-center items-center transition gap-1">
                            <span class="icon-[mdi--eye]"></span>
                            LIHAT</a>
                    @endcan

                </div>
            </div>

            <!-- TIMELINE -->
            <div
                class="bg-[#f2f7fb] md:bg-white md:shadow-sm rounded-2xl p-5 md:p-6 relative border md:border-gray-100 h-full">
                <h2 class="text-[#105e75] font-bold text-lg md:text-xl mb-3">Timeline</h2>

                {{-- tambah timeline --}}
                @can('tambah timeline')
                    <button @click="openEditTimeline = true"
                        class="absolute top-5 right-5 text-[#105e75] hover:scale-110 transition">
                        <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                            </path>
                        </svg>
                    </button>
                @endcan

                {{-- list timeline --}}
                <div class="text-[#105e75] font-medium text-sm md:text-base md:bg-blue-50/50 md:p-4 rounded-xl">
                    <div>
                        <p class="font-bold mb-1 text-base md:text-lg">{{ $rapatTerbaru->judul ?? 'Tidak ada jadwal' }}
                        </p>
                        <p>{{ $rapatTerbaru->tanggal ?? '-' }}</p>
                        <p>pukul {{ $rapatTerbaru->jam ?? '-' }}</p>
                        <div class="flex justify-between items-end mt-4">
                            <p class="font-semibold">{{ $rapatTerbaru->tempat ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- lihat timeline --}}
                    <a href="#"
                        class="text-primary-400 rounded-md font-bold text-sm leading-none transition flex justify-between items-center mt-4">
                        Lihat selengkapnya
                        <span class="icon-[ep--d-arrow-right]"></span>
                    </a>
                </div>
            </div>

            <!-- NOTULENSI -->
            <div
                class="bg-[#f2f7fb] md:bg-white md:shadow-sm rounded-2xl p-5 md:p-6 border md:border-gray-100 md:col-span-2 lg:col-span-3">

                <!-- HEADER NOTULENSI DENGAN TOMBOL PLUS -->
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-[#105e75] font-bold text-lg md:text-xl">Notulensi</h2>
                    <button @click="openAddNotulensi = true" 
                            class="text-white bg-[#105e75] hover:bg-[#0b4354] rounded-full p-1.5 transition shadow-sm flex items-center justify-center">
                        <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
                    @foreach($notulensi_list as $rabes)
                    <div
                        class="flex justify-between items-center md:bg-gray-50 md:p-4 rounded-xl md:border md:border-gray-100 mb-3 md:mb-0">
                        <span
                            class="text-[#105e75] font-bold text-sm md:text-base">{{ $rabes->judul }}</span>
                            
                        <div class="flex space-x-2 items-center">
                            <button
                                <button @click="openViewNotulensi = true; notulensiTitle = 'Notulensi {{ $rabes->judul }}'" class="bg-[#105e75] hover:bg-[#0b4354] text-white px-4 py-1.5 md:py-2 rounded-lg text-xs md:text-sm font-bold transition">Lihat</button>
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
                    @endforeach
                </div>
            </div>

        </div>

        <x-modal-edit-pengumuman />
        <x-modal-edit-timeline />
        <x-modal-izin />
        <x-modal-view-notulensi />

        
<div x-show="openAddNotulensi" style="display: none;" class="fixed inset-0 z-999 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        
        
        <div x-show="openAddNotulensi"
             x-transition.opacity
             class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" 
             aria-hidden="true" 
             @click="openAddNotulensi = false"></div>

                <!-- Modal panel -->
                <div x-show="openAddNotulensi" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">

                    <div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg font-bold leading-6 text-[#105e75]" id="modal-title">Tambah Notulensi
                            </h3>
                            <div class="mt-6 text-left">
                                <!-- Input Judul -->
                                <div class="mb-5">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Judul</label>
                                    <input type="text" placeholder="Ketik disini"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#105e75] focus:ring-[#105e75] sm:text-sm p-2 border outline-none">
                                </div>
                                <!-- Input File PDF -->
                                <div class="mb-4">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Upload File
                                        PDF</label>
                                    <input type="file" accept="application/pdf"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#f2f7fb] file:text-[#105e75] hover:file:bg-blue-100 cursor-pointer">
                                    <p class="mt-1.5 text-xs text-gray-500 font-medium">Format: PDF (Maks 5MB)</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 sm:flex sm:flex-row-reverse">
                        <button type="button"
                            class="inline-flex justify-center w-full px-4 py-2 text-base font-bold text-white bg-[#105e75] border border-transparent rounded-md shadow-sm hover:bg-[#0b4354] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#105e75] sm:ml-3 sm:w-auto sm:text-sm transition">
                            Tambah
                        </button>
                        <button @click="openAddNotulensi = false" type="button"
                            class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-bold text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#105e75] sm:mt-0 sm:w-auto sm:text-sm transition">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
