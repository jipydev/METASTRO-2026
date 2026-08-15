<x-app-layout :$title>
    {{-- ================================================================= --}}
    {{-- NOTIFIKASI SWEETALERT                                             --}}
    {{-- ================================================================= --}}
    @if (session('success') || session('error') || $errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                @if (session('success'))
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: @json(session('success')), confirmButtonColor: '#4f46e5', timer: 2200, showConfirmButton: false });
                @elseif (session('error'))
                    Swal.fire({ icon: 'error', title: 'Gagal', text: @json(session('error')), confirmButtonColor: '#dc2626' });
                @elseif ($errors->any())
                    Swal.fire({ icon: 'error', title: 'Validasi Gagal', html: `{!! implode('<br>', $errors->all()) !!}`, confirmButtonColor: '#dc2626' });
                @endif
            });
        </script>
    @endif

    {{-- ================================================================= --}}
    {{-- CONTAINER UTAMA & ALPINE STATE                                    --}}
    {{-- ================================================================= --}}
    <div x-data="{
            // Pengumuman State
            openTambahPengumuman: false,
            openEditPengumuman: false,
            openDeletePengumuman: false,
            selectedPengumuman: { id: null, judul: '', isi: '', status: 'Draft', tanggal_publish: '', lampiran: null },

            // Timeline & Notulensi State
            openEditTimeline: false,
            selectedTimeline: { id: null, judul: '', tanggal: '', waktu_mulai: '', tempat: '' },
            openAddNotulensi: false,
            openViewNotulensi: false,
            notulensiTitle: ''
        }"
        class="min-h-screen bg-gray-50 dark:bg-slate-900 pb-12 font-poppins transition-colors duration-200">

        <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            {{-- ============================================================= --}}
            {{-- 1. PENGUMUMAN SECTION (Full Width)                            --}}
            {{-- ============================================================= --}}
            <section class="md:col-span-2 lg:col-span-3 bg-white dark:bg-slate-800 rounded-2xl p-6 border border-gray-200 dark:border-slate-700 shadow-sm">
                
                {{-- Header Pengumuman --}}
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">📢 Pengumuman</h2>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Informasi terbaru untuk panitia dan peserta</p>
                    </div>

                    @if(auth()->user()->canManageSekretariat())
                        <button type="button"
                            @click="selectedPengumuman = { id: null, judul: '', isi: '', status: 'Draft', tanggal_publish: '', lampiran: null }; openTambahPengumuman = true;"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                            <span>+</span> Tambah Pengumuman
                        </button>
                    @endif
                </div>

                {{-- Daftar Pengumuman --}}
                <div class="space-y-4">
                    @forelse($pengumumanList as $item)
                        @if ($item->status === 'Draft' && !auth()->user()->canManageSekretariat())
                            @continue
                        @endif

                        <article class="relative pl-5 pr-6 py-5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
                            {{-- Aksen Garis Kiri --}}
                            <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $item->status === 'Publish' ? 'bg-indigo-600' : 'bg-amber-500' }}"></div>

                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ $item->judul }}</h3>
                                
                                <span class="self-start sm:self-auto px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $item->status === 'Publish' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300' }}">
                                    {{ $item->status }}
                                </span>
                            </div>

                            <div class="flex items-center gap-3 text-xs text-gray-400 dark:text-slate-400 mb-3">
                                <span>🗓 {{ optional($item->tanggal_publish)->translatedFormat('d M Y H:i') ?? 'Belum dipublish' }}</span>
                                <span>•</span>
                                <span>✍️ {{ $item->pembuat?->nama ?? 'Admin' }}</span>
                            </div>

                            <p class="text-xs leading-relaxed text-gray-700 dark:text-slate-300 whitespace-pre-line">
                                {{ $item->isi }}
                            </p>

                            {{-- Lampiran & Aksi Edit/Hapus --}}
                            <div class="mt-4 pt-3 border-t border-gray-200 dark:border-slate-700 flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    @if ($item->lampiran)
                                        <a href="{{ asset('storage/' . $item->lampiran) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                                            📎 Lihat Lampiran PDF
                                        </a>
                                    @endif
                                </div>

                                @if(auth()->user()->canManageSekretariat())
                                    <div class="flex items-center gap-2">
                                        @php
                                            $jsonItem = json_encode([
                                                'id'              => $item->id,
                                                'judul'           => $item->judul,
                                                'isi'             => $item->isi,
                                                'status'          => $item->status,
                                                'lampiran'        => $item->lampiran,
                                                'tanggal_publish' => $item->tanggal_publish ? \Carbon\Carbon::parse($item->tanggal_publish)->format('Y-m-d\TH:i') : '',
                                            ]);
                                        @endphp

                                        <button type="button" data-item="{{ $jsonItem }}"
                                            @click="selectedPengumuman = JSON.parse($el.dataset.item); openEditPengumuman = true;"
                                            class="px-3 py-1 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded-lg transition">
                                            Edit
                                        </button>

                                        <button type="button"
                                            @click="selectedPengumuman = { id: {{ $item->id }}, judul: '{{ addslashes($item->judul) }}' }; openDeletePengumuman = true;"
                                            class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition">
                                            Hapus
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="text-center py-10">
                            <span class="text-4xl block mb-2">📢</span>
                            <p class="text-xs text-gray-500 dark:text-slate-400 font-medium">Belum ada pengumuman yang dipublikasikan.</p>
                        </div>
                    @endforelse
                </div>

                @if($pengumumanList->hasPages())
                    <div class="mt-4 pt-3 border-t border-gray-100 dark:border-slate-700">
                        {{ $pengumumanList->links() }}
                    </div>
                @endif
            </section>

            {{-- ============================================================= --}}
            {{-- 2. PRESENSI SECTION                                           --}}
            {{-- ============================================================= --}}
            <section class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl p-6 border border-gray-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">📋 Presensi Kehadiran</h2>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5 truncate">
                        {{ $kegiatanTerbaru?->judul ?? 'Tidak ada sesi kegiatan aktif' }}
                    </p>

                    <div class="my-6 flex items-baseline gap-2">
                        <span class="text-4xl sm:text-5xl font-black text-indigo-600 dark:text-indigo-400">
                            {{ $hadirCount }}/{{ $totalUserCount }}
                        </span>
                        <span class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                            Peserta Telah Hadir
                        </span>
                    </div>
                </div>

                {{-- Action Bar Presensi --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 pt-4 border-t border-gray-100 dark:border-slate-700">
                    <a href="{{ route('admin.qr.index') }}"
                       class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-800 dark:text-white text-xs font-bold rounded-xl flex items-center justify-center gap-1.5 transition">
                        <span>📱</span> QR Saya
                    </a>

                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            <button class="w-full px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-800 dark:text-white text-xs font-bold rounded-xl flex items-center justify-center gap-1.5 transition">
                                <span>📝</span> Izin
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('pengajuan-izin.create')">Ajukan Izin Baru</x-dropdown-link>
                            <x-dropdown-link :href="route('pengajuan-izin.index')">Riwayat Izin Saya</x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    @if(auth()->user()->canScanPresensi())
                        <a href="{{ route('presensi.scan') }}"
                           class="col-span-2 sm:col-span-1 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl flex items-center justify-center gap-1.5 transition shadow-sm">
                            <span>📷</span> Scan QR
                        </a>
                    @endif
                </div>
            </section>

            {{-- ============================================================= --}}
            {{-- 3. TIMELINE / KEGIATAN SECTION                                --}}
            {{-- ============================================================= --}}
            <section class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-gray-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">⏳ Jadwal Kegiatan</h2>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Kegiatan atau rapat mendatang</p>

                    <div class="mt-4 p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl space-y-2 text-xs">
                        <p class="font-bold text-indigo-600 dark:text-indigo-400 text-sm">
                            {{ $kegiatanTerbaru?->judul ?? 'Belum ada agenda' }}
                        </p>
                        <p class="text-gray-600 dark:text-slate-300">
                            📅 {{ $kegiatanTerbaru ? \Carbon\Carbon::parse($kegiatanTerbaru->tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY') : '-' }}
                        </p>
                        <p class="text-gray-600 dark:text-slate-300">
                            ⏰ Pukul {{ $kegiatanTerbaru?->waktu_mulai ? substr($kegiatanTerbaru->waktu_mulai, 0, 5) . ' WIB' : '-' }}
                        </p>
                        <p class="text-gray-600 dark:text-slate-300">
                            📍 {{ $kegiatanTerbaru?->tempat ?? '-' }}
                        </p>
                    </div>
                </div>

                <a href="{{ route('kegiatan.index') }}"
                   class="mt-4 inline-flex items-center justify-between text-xs font-bold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                    <span>Lihat Semua Jadwal</span>
                    <span>&rarr;</span>
                </a>
            </section>

            {{-- ============================================================= --}}
            {{-- 4. NOTULENSI SECTION (Full Width)                             --}}
            {{-- ============================================================= --}}
            <section class="md:col-span-2 lg:col-span-3 bg-white dark:bg-slate-800 rounded-2xl p-6 border border-gray-200 dark:border-slate-700 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">📄 Notulensi Rapat</h2>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Arsip berkas hasil koordinasi kegiatan</p>
                    </div>

                    @if(auth()->user()->canManageSekretariat())
                        <button type="button" @click="openAddNotulensi = true"
                            class="p-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition shadow-sm cursor-pointer"
                            title="Tambah Notulensi">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </button>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @forelse ($notulensiList as $notulensi)
                        <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl border border-gray-200 dark:border-slate-700 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ $notulensi->judul }}</p>
                                <p class="text-[11px] text-gray-400 truncate">{{ $notulensi->kegiatan?->judul ?? 'Umum' }}</p>
                            </div>

                            @if($notulensi->file_notulensi)
                                <a href="{{ asset('storage/' . $notulensi->file_notulensi) }}" target="_blank"
                                   class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition shrink-0">
                                    Unduh
                                </a>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-full text-center py-8 text-xs text-gray-400">
                            Belum ada dokumen notulensi yang diunggah.
                        </div>
                    @endforelse
                </div>
            </section>

        </div>

        {{-- ================================================================= --}}
        {{-- MODAL TAMBAH NOTULENSI                                            --}}
        {{-- ================================================================= --}}
        <div x-show="openAddNotulensi" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 p-0">
                <div x-show="openAddNotulensi" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="openAddNotulensi = false"></div>

                <form action="{{ route('notulensi.store') }}" method="POST" enctype="multipart/form-data"
                      x-show="openAddNotulensi" x-transition
                      class="relative bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 p-6 w-full max-w-md shadow-xl text-xs">
                    @csrf
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Tambah Notulensi Baru</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block font-semibold text-gray-700 dark:text-slate-300 mb-1">Judul Dokumen *</label>
                            <input type="text" name="judul" required placeholder="Contoh: Notulensi Pleno Divisi"
                                   class="w-full bg-slate-50 dark:bg-slate-700 rounded-xl border-gray-300 dark:border-slate-600 p-2.5 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block font-semibold text-gray-700 dark:text-slate-300 mb-1">File Dokumen (PDF) *</label>
                            <input type="file" name="file_notulensi" accept="application/pdf" required
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 cursor-pointer">
                            <span class="text-[10px] text-gray-400 block mt-1">Maksimal ukuran file: 5MB</span>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" @click="openAddNotulensi = false"
                                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-gray-700 dark:text-slate-200 font-semibold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-sm transition">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>