@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: @json(session('success')),
                confirmButtonColor: '#fe5a1d',
                timer: 2200,
                showConfirmButton: false
            });
        });
    </script>
@endif
@if (session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: @json(session('error')),
                confirmButtonColor: '#dc2626'
            });
        });
    </script>
@endif

<x-app-layout>
    <div x-data="{
        /* =========================
               PENGUMUMAN
           ========================= */
        openTambahPengumuman: false,
        openEditPengumuman: false,
        openDeletePengumuman: false,
        selectedPengumuman: {
            id: null,
            judul: '',
            isi: '',
            status: 'Draft',
            tanggal_publish: '',
            lampiran: null
        },
        /* =========================
            TIMELINE
        ========================= */
        openEditTimeline: false,
        selectedTimeline: {
            id: null,
            judul: '',
            tanggal: '',
            jam: '',
            tempat: ''
        },
        openIzinModal: false,
        /* =========================
            NOTULENSI
        ========================= */
        openAddNotulensi: false,
        openViewNotulensi: false,
        notulensiTitle: ''
    }" class="min-h-screen bg-gray-100 dark:bg-slate-900 pb-10 transition-colors duration-200 font-poppins">

        <div class="p-4 md:p-8 max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mt-2">

            <!-- =========================
                 PENGUMUMAN SECTION
            ========================= -->
            <div class="bg-primary-50/50 dark:bg-slate-800/90 md:bg-white md:dark:bg-slate-800 md:shadow-sm rounded-2xl p-6 md:col-span-2 lg:col-span-3 border border-primary-100/50 md:border-gray-100 dark:border-slate-700/80">

                <!-- Header Pengumuman -->
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                            Pengumuman
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-slate-400">
                            Informasi terbaru panitia.
                        </p>
                    </div>

                    @role('Admin|Sekretaris')
                        <button @click="selectedPengumuman = { id: null, judul: '', isi: '', status: 'Draft', tanggal_publish: '', lampiran: null }; openTambahPengumuman = true;"
                                class="bg-primary-500 hover:bg-primary-600 text-white px-5 py-2 rounded-xl font-semibold shadow-sm transition cursor-pointer flex items-center gap-1">
                            + Tambah
                        </button>
                    @endrole
                </div>

                <!-- List Pengumuman -->
                @forelse($pengumumanList as $item)
                    <div class="bg-white dark:bg-slate-800/90 rounded-2xl border border-gray-200 dark:border-slate-700/80 shadow-sm p-6 mb-5 relative overflow-hidden">
                        {{-- Accent bar --}}
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-primary-500"></div>

                        <div class="pl-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-xl font-bold text-primary-600 dark:text-primary-400">
                                        {{ $item->judul }}
                                    </h3>

                                    <div class="mt-1.5 flex flex-wrap items-center gap-2 text-sm text-gray-500 dark:text-slate-400">
                                        <div class="flex items-center gap-1">
                                            <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>{{ optional($item->tanggal_publish)->translatedFormat('d F Y H:i') }}</span>
                                        </div>
                                        <span class="text-gray-300 dark:text-slate-600">•</span>
                                        <div class="flex items-center gap-1">
                                            <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            <span>{{ $item->pembuat?->name }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Status & Actions --}}
                                <div class="flex items-center gap-2">
                                    @if ($item->status == 'Publish')
                                        <span class="bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 px-3 py-1 rounded-full text-xs font-semibold">
                                            Publish
                                        </span>
                                    @else
                                        <span class="bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-300 px-3 py-1 rounded-full text-xs font-semibold">
                                            Draft
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Isi Pengumuman --}}
                            <div class="mt-4">
                                <p class="whitespace-pre-line text-sm md:text-base leading-relaxed text-gray-700 dark:text-slate-300">
                                    {{ $item->isi }}
                                </p>
                            </div>

                            {{-- Lampiran --}}
                            @if ($item->lampiran)
                                <div class="mt-4">
                                    <a href="{{ asset('storage/' . $item->lampiran) }}" target="_blank"
                                        class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-slate-700/60 px-4 py-2 rounded-xl hover:bg-primary-100 dark:hover:bg-slate-700 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                        </svg>
                                        Lihat Lampiran
                                    </a>
                                </div>
                            @endif

                            {{-- Action Buttons (Admin/Sekretaris) --}}
                            @role('Admin|Sekretaris')
                                <div class="flex gap-2 mt-5 pt-4 border-t border-gray-100 dark:border-slate-700/80">
                                    @php
                                        $pengumumanData = [
                                            'id' => $item->id,
                                            'judul' => $item->judul,
                                            'isi' => $item->isi,
                                            'status' => $item->status,
                                            'lampiran' => $item->lampiran,
                                            'tanggal_publish' => $item->tanggal_publish
                                                ? \Carbon\Carbon::parse($item->tanggal_publish)->format('Y-m-d\TH:i')
                                                : '',
                                        ];
                                    @endphp

                                    <button type="button" data-item="{{ json_encode($pengumumanData) }}"
                                            @click="selectedPengumuman = JSON.parse($el.dataset.item); openEditPengumuman = true;"
                                            class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg font-semibold text-xs transition cursor-pointer">
                                        Edit
                                    </button>

                                    <button type="button" data-item='@json(['id' => $item->id, 'judul' => $item->judul])'
                                            @click="selectedPengumuman = JSON.parse($el.dataset.item); openDeletePengumuman = true;"
                                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold text-xs transition cursor-pointer">
                                        Hapus
                                    </button>
                                </div>
                            @endrole
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16">
                        <div class="text-6xl mb-3">📢</div>
                        <h3 class="text-xl font-bold text-gray-500 dark:text-slate-400">Belum ada pengumuman</h3>
                        <p class="text-gray-400 dark:text-slate-500 mt-1 text-sm">Belum terdapat pengumuman terbaru.</p>
                    </div>
                @endforelse

                <div class="mt-6">
                    {{ $pengumumanList->links() }}
                </div>
            </div>

            <!-- =========================
                 PRESENSI SECTION
            ========================= -->
            <div class="bg-primary-50/50 dark:bg-slate-800/90 md:bg-white md:dark:bg-slate-800 md:shadow-sm rounded-2xl p-5 md:p-6 border border-primary-100/50 md:border-gray-100 dark:border-slate-700/80 lg:col-span-2 flex flex-col justify-between">
                <div>
                    <h2 class="text-primary-600 dark:text-primary-400 font-bold text-lg md:text-xl mb-1">Presensi Rapat Besar</h2>
                    <div class="flex items-baseline space-x-2 mb-6">
                        <span class="text-4xl md:text-5xl font-bold text-primary-600 dark:text-primary-400">
                            {{ $rapatTerbaru ? $rapatTerbaru->hadir : 0 }}/{{ $rapatTerbaru ? $rapatTerbaru->total : 0 }}
                        </span>
                        <span class="text-sm md:text-base font-medium text-gray-500 dark:text-slate-400">Panitia telah hadir</span>
                    </div>
                </div>

                <div class="flex flex-wrap lg:flex-nowrap gap-2 md:gap-3">
                    {{-- QR Absen --}}
                    <a href="{{ url('/qr') }}"
                        class="cursor-pointer flex-1 min-w-30 bg-primary-500 hover:bg-primary-600 text-white text-xs md:text-sm font-bold py-3 rounded-xl flex justify-center items-center gap-1 transition shadow-sm">
                        <span class="icon-[material-symbols--qr-code] size-4 md:size-5"></span>
                        QR ABSEN
                    </a>

                    {{-- Izin --}}
                    <button @click="openIzinModal = !openIzinModal"
                        class="cursor-pointer flex-1 min-w-30 bg-primary-500 hover:bg-primary-600 text-white text-xs md:text-sm font-bold py-3 rounded-xl flex justify-center items-center gap-1 transition shadow-sm">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        IZIN
                    </button>

                    {{-- Scan --}}
                    @can('scan presensi')
                        <a href="{{ url('/scan') }}"
                            class="cursor-pointer flex-1 min-w-30 bg-primary-500 hover:bg-primary-600 text-white text-xs md:text-sm font-bold py-3 rounded-xl flex justify-center items-center transition gap-1 shadow-sm">
                            <span class="icon-[boxicons--scan-filled] size-4 md:size-5"></span>
                            SCAN</a>
                    @endcan

                    {{-- Lihat --}}
                    @can('lihat presensi')
                        <a href="{{ url('/lihat') }}"
                            class="cursor-pointer flex-1 min-w-30 bg-primary-500 hover:bg-primary-600 text-white text-xs md:text-sm font-bold py-3 rounded-xl flex justify-center items-center transition gap-1 shadow-sm">
                            <span class="icon-[mdi--eye]"></span>
                            LIHAT</a>
                    @endcan
                </div>
            </div>

            <!-- =========================
                 TIMELINE SECTION
            ========================= -->
            <div class="bg-primary-50/50 dark:bg-slate-800/90 md:bg-white md:dark:bg-slate-800 md:shadow-sm rounded-2xl p-5 md:p-6 relative border border-primary-100/50 md:border-gray-100 dark:border-slate-700/80 h-full">
                <h2 class="text-primary-600 dark:text-primary-400 font-bold text-lg md:text-xl mb-3">Timeline</h2>

                @can('tambah timeline')
                    @php
                        $rapatData = $rapatTerbaru
                            ? [
                                'id' => $rapatTerbaru->id,
                                'judul' => $rapatTerbaru->judul,
                                'tanggal' => \Carbon\Carbon::parse($rapatTerbaru->tanggal)->format('Y-m-d'),
                                'jam' => \Carbon\Carbon::parse($rapatTerbaru->jam)->format('H:i'),
                                'tempat' => $rapatTerbaru->tempat,
                            ]
                            : null;
                    @endphp
                    <button
                        @if ($rapatData) data-item='@json($rapatData)'
                            @click="selectedTimeline = JSON.parse($el.dataset.item); openEditTimeline = true;"
                        @else
                            @click="selectedTimeline = { id: null, judul: '', tanggal: '', jam: '', tempat: '' }; openEditTimeline = true;"
                        @endif
                        class="absolute top-5 right-5 text-primary-500 hover:scale-110 transition cursor-pointer">
                    </button>
                @endcan

                <div class="text-primary-600 dark:text-primary-400 font-medium text-sm md:text-base md:bg-primary-50/50 dark:md:bg-slate-700/50 md:p-4 rounded-xl">
                    <div>
                        <p class="font-bold mb-1 text-base md:text-lg text-primary-600 dark:text-primary-400">{{ $rapatTerbaru->judul ?? 'Tidak ada jadwal' }}</p>
                        <p class="text-slate-700 dark:text-slate-300">{{ $rapatTerbaru ? \Carbon\Carbon::parse($rapatTerbaru->tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY') : '-' }}</p>
                        <p class="text-slate-700 dark:text-slate-300">pukul {{ $rapatTerbaru ? \Carbon\Carbon::parse($rapatTerbaru->jam)->format('H.i') : '-' }}</p>
                        <div class="flex justify-between items-end mt-4">
                            <p class="font-semibold text-slate-800 dark:text-slate-200">{{ $rapatTerbaru->tempat ?? '-' }}</p>
                        </div>
                    </div>

                    <a href="{{ route('timeline.index') }}"
                        class="text-primary-500 dark:text-primary-400 hover:text-primary-600 rounded-md font-bold text-sm leading-none transition flex justify-between items-center mt-4">
                        Lihat selengkapnya
                        <span class="icon-[ep--d-arrow-right]"></span>
                    </a>
                </div>
            </div>

            <!-- =========================
                 NOTULENSI SECTION
            ========================= -->
            <div class="bg-primary-50/50 dark:bg-slate-800/90 md:bg-white md:dark:bg-slate-800 md:shadow-sm rounded-2xl p-5 md:p-6 border border-primary-100/50 md:border-gray-100 dark:border-slate-700/80 md:col-span-2 lg:col-span-3">

                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-primary-600 dark:text-primary-400 font-bold text-lg md:text-xl">Notulensi</h2>
                    <button @click="openAddNotulensi = true"
                        class="text-white bg-primary-500 hover:bg-primary-600 rounded-full p-1.5 transition shadow-sm flex items-center justify-center cursor-pointer">
                        <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
                    @foreach($notulensi_list as $rabes)
                    <div class="flex justify-between items-center md:bg-gray-50 dark:md:bg-slate-700/60 md:p-4 rounded-xl md:border md:border-gray-100 dark:md:border-slate-700 mb-3 md:mb-0">
                        <span class="text-primary-600 dark:text-primary-400 font-bold text-sm md:text-base">{{ $rabes->judul }}</span>
                            
                        <div class="flex space-x-2 items-center">
                            <button @click="openViewNotulensi = true; notulensiTitle = 'Notulensi {{ $rabes->judul }}'" class="bg-primary-500 hover:bg-primary-600 text-white px-4 py-1.5 md:py-2 rounded-lg text-xs md:text-sm font-bold transition cursor-pointer">Lihat</button>
                            <form action="{{ route('notulensi.destroy', $rabes->id) }}" method="POST"
                                  onsubmit="return confirm('Hapus notulensi {{ $rabes->judul }}?')"
                                  class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-primary-500 hover:text-red-600 dark:hover:text-red-400 md:hover:bg-red-50 dark:md:hover:bg-red-950/40 p-1.5 rounded-md transition cursor-pointer">
                                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- =========================
             MODAL COMPONENTS
        ========================= -->
        <x-modal-tambah-pengumuman />
        <x-modal-edit-pengumuman />
        <x-modal-hapus-pengumuman />
        <x-modal-edit-timeline />
        <x-modal-izin />
        <x-modal-view-notulensi />

        <!-- Modal Tambah Notulensi -->
        <div x-show="openAddNotulensi" style="display: none;" class="fixed inset-0 z-[999] overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">

                <div x-show="openAddNotulensi" x-transition.opacity
                    class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" aria-hidden="true"
                    @click="openAddNotulensi = false"></div>

                <!-- Modal panel -->
                <form action="{{ route('notulensi.store') }}" method="POST" enctype="multipart/form-data"
                    x-show="openAddNotulensi" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-slate-800 rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 border border-gray-100 dark:border-slate-700 font-poppins">
                    @csrf

                    <div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg font-bold leading-6 text-primary-600 dark:text-primary-400" id="modal-title">Tambah Notulensi</h3>
                            <div class="mt-6 text-left">
                                <!-- Input Judul -->
                                <div class="mb-5">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Judul</label>
                                    <input type="text" name="judul" placeholder="Ketik disini" required
                                        class="block w-full rounded-xl border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm p-2.5 border outline-none">
                                </div>
                                <!-- Input File PDF -->
                                <div class="mb-4">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Upload File PDF</label>
                                    <input type="file" name="lampiran" accept="application/pdf" required
                                        class="block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-600 hover:file:bg-primary-100 dark:file:bg-slate-700 dark:file:text-primary-400 cursor-pointer">
                                    <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400 font-medium">Format: PDF (Maks 5MB)</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit"
                            class="inline-flex justify-center w-full px-5 py-2.5 text-sm font-bold text-white bg-primary-500 border border-transparent rounded-xl shadow-sm hover:bg-primary-600 focus:outline-none sm:w-auto transition cursor-pointer">
                            Tambah
                        </button>
                        <button @click="openAddNotulensi = false" type="button"
                            class="inline-flex justify-center w-full px-5 py-2.5 mt-3 sm:mt-0 text-sm font-bold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-xl shadow-sm hover:bg-gray-50 dark:hover:bg-slate-600 focus:outline-none sm:w-auto transition cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
