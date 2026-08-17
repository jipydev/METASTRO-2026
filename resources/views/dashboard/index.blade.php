<x-app-layout :$title>
    {{-- Notifikasi SweetAlert --}}
    @if (session('success') || session('error') || $errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                @if (session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: @json(session('success')),
                        confirmButtonColor: window.appBrandColor(),
                        timer: 2200,
                        showConfirmButton: false
                    });
                @elseif (session('error'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: @json(session('error')),
                        confirmButtonColor: '#dc2626'
                    });
                @elseif ($errors->any())
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        html: `{!! implode('<br>', $errors->all()) !!}`,
                        confirmButtonColor: '#dc2626'
                    });
                @endif
            });
        </script>
    @endif

    {{-- Container Utama & Alpine State --}}
    <div x-data="{
        openTambahPengumuman: false,
        openEditPengumuman: false,
        openDeletePengumuman: false,
        selectedPengumuman: { id: null, judul: '', isi: '', status: 'Draft', tanggal_publish: '', lampiran: null },
        openAddNotulensi: {{ $errors->has('lampiran') ? 'true' : 'false' }},
        openEditNotulensi: false,
        selectedNotulensi: { id: null, judul: '', isi: '', kegiatan_id: '', hasLampiran: false },
    }" class="bg-slate-50 dark:bg-slate-900 pb-8 font-poppins transition-colors duration-200">

        <div class="max-w-7xl mx-auto px-3 py-4 sm:p-6 lg:p-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">

            {{-- 1. PENGUMUMAN SECTION --}}
            <section class="md:col-span-2 bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-5 border border-slate-200/80 dark:border-slate-700 shadow-sm min-w-0">
                <div class="flex flex-col gap-3 mb-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-slate-600 dark:text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                            <span>Pengumuman</span>
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Informasi terbaru untuk panitia</p>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('pengumuman.index') }}" class="px-2.5 py-2 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-brand-600 dark:hover:text-brand-400 whitespace-nowrap">Lihat semua</a>
                        @if (auth()->user()->canCreatePengumuman())
                            <button type="button"
                                @click="selectedPengumuman = { id: null, judul: '', isi: '', status: 'draft', tanggal_publish: '' }; openTambahPengumuman = true;"
                                class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer whitespace-nowrap">
                                + Tambah
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Daftar Pengumuman --}}
                <div class="space-y-2">
                    @forelse($pengumumanList as $item)
                        <article x-data="{ open: false }"
                            class="relative bg-slate-50/80 dark:bg-slate-700/30 rounded-xl border border-slate-200/80 dark:border-slate-700 overflow-hidden">
                            <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $item->isPublished() ? 'bg-emerald-500' : 'bg-amber-500' }}"></div>

                            <button type="button" @click="open = !open"
                                class="w-full pl-5 pr-4 py-3.5 flex items-center gap-3 text-left cursor-pointer">
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $item->judul }}</h3>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $item->status === 'published' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300' }}">
                                            {{ $item->status }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-0.5 truncate">
                                        {{ $item->tanggal_publish ? \Carbon\Carbon::parse($item->tanggal_publish)->diffForHumans() : 'Draft' }}
                                        • {{ $item->pembuat?->nama ?? 'Admin' }}
                                    </p>
                                </div>
                                <svg class="w-4 h-4 text-slate-400 shrink-0 transition-transform duration-200" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div x-show="open" x-cloak x-transition.opacity class="pl-5 pr-4 pb-4">
                                <p class="text-xs leading-relaxed text-slate-600 dark:text-slate-300 whitespace-pre-line">
                                    {{ $item->isi }}
                                </p>

                                <div class="mt-4 pt-3 border-t border-slate-200/80 dark:border-slate-700/80 flex flex-wrap items-center justify-between gap-3">
                                    <div>
                                        @if ($item->lampiran)
                                            <a href="{{ asset('storage/' . $item->lampiran) }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-brand-600 dark:text-slate-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                <span>Lihat Lampiran PDF</span>
                                            </a>
                                        @endif
                                    </div>

                                    @if ($item->canBeManagedBy(auth()->user()))
                                        <div class="flex items-center gap-2">
                                            @php
                                                $jsonItem = json_encode([
                                                    'id' => $item->id,
                                                    'judul' => $item->judul,
                                                    'isi' => $item->isi,
                                                    'status' => $item->status,
                                                    'tanggal_publish' => $item->tanggal_publish
                                                        ? \Carbon\Carbon::parse($item->tanggal_publish)->format('Y-m-d\TH:i')
                                                        : '',
                                                ]);
                                            @endphp

                                            <button type="button" data-item="{{ $jsonItem }}"
                                                @click="selectedPengumuman = JSON.parse($el.dataset.item); openEditPengumuman = true;"
                                                class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded-xl transition">
                                                Edit
                                            </button>

                                            <button type="button"
                                                @click="selectedPengumuman = { id: {{ $item->id }}, judul: '{{ addslashes($item->judul) }}' }; openDeletePengumuman = true;"
                                                class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-xl transition">
                                                Hapus
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="text-center py-4">
                            <p class="text-xs text-slate-400 font-medium">Belum ada pengumuman yang dipublikasikan.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- 2. JADWAL KEGIATAN --}}
            <section class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-5 border border-slate-200/80 dark:border-slate-700 shadow-sm flex flex-col justify-between min-w-0">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Agenda Terdekat</span>
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kegiatan atau rapat mendatang</p>

                    <div class="mt-4 p-4 bg-slate-50/80 dark:bg-slate-700/40 rounded-xl space-y-2 text-xs border border-slate-100 dark:border-slate-700/60">
                        <p class="font-bold text-slate-900 dark:text-white text-sm truncate">
                            {{ $kegiatanTerbaru?->nama ?? 'Belum ada agenda' }}
                        </p>
                        <p class="text-slate-600 dark:text-slate-300 font-medium flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            {{ $kegiatanTerbaru ? \Carbon\Carbon::parse($kegiatanTerbaru->tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY') : '-' }}
                        </p>
                        <p class="text-slate-600 dark:text-slate-300 font-medium flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Pukul {{ $kegiatanTerbaru?->waktu_mulai ? substr($kegiatanTerbaru->waktu_mulai, 0, 5) . ' WIB' : '-' }}
                        </p>
                        <p class="text-slate-600 dark:text-slate-300 font-medium flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            {{ $kegiatanTerbaru?->tempat ?? '-' }}
                        </p>
                    </div>
                </div>

                <a href="{{ route('kegiatan.index') }}"
                    class="mt-4 inline-flex items-center justify-between text-xs font-bold text-slate-600 hover:text-brand-600 dark:text-slate-400">
                    <span>Lihat Semua Jadwal</span>
                    <span>&rarr;</span>
                </a>
            </section>

            {{-- 3. PRESENSI SECTION --}}
            <section class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-5 border border-slate-200/80 dark:border-slate-700 shadow-sm flex flex-col justify-between min-w-0">
                <div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2m-6 9l2 2 4-4"></path></svg>
                                <span>Rekapitulasi Presensi</span>
                            </h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                                Sesi Aktif: <strong class="text-slate-700 dark:text-slate-200">{{ $kegiatanTerbaru?->nama ?? 'Tidak ada sesi aktif' }}</strong>
                            </p>
                        </div>
                    </div>

                    {{-- Grid Badge / Pill Status Kehadiran --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 my-5">
                        {{-- Hadir --}}
                        <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl border border-emerald-100 dark:border-emerald-800/40 flex flex-col justify-between">
                            <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Hadir</span>
                            <span class="text-2xl font-black text-emerald-700 dark:text-emerald-300 font-mono mt-1">{{ $hadirCount ?? 0 }}</span>
                        </div>

                        {{-- Izin --}}
                        <div class="p-3 bg-blue-50 dark:bg-blue-950/40 rounded-xl border border-blue-100 dark:border-blue-800/40 flex flex-col justify-between">
                            <span class="text-[11px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Izin</span>
                            <span class="text-2xl font-black text-blue-700 dark:text-blue-300 font-mono mt-1">{{ $izinCount ?? 0 }}</span>
                        </div>

                        {{-- Sakit --}}
                        <div class="p-3 bg-amber-50 dark:bg-amber-950/40 rounded-xl border border-amber-100 dark:border-amber-800/40 flex flex-col justify-between">
                            <span class="text-[11px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Sakit</span>
                            <span class="text-2xl font-black text-amber-700 dark:text-amber-300 font-mono mt-1">{{ $sakitCount ?? 0 }}</span>
                        </div>

                        {{-- Belum Absen --}}
                        <div class="p-3 bg-slate-100 dark:bg-slate-700/50 rounded-xl border border-slate-200 dark:border-slate-600 flex flex-col justify-between">
                            <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider leading-tight">Belum Absen</span>
                            <span class="text-2xl font-black text-slate-700 dark:text-slate-300 font-mono mt-1">{{ $belumAbsenCount ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-4 border-t border-slate-100 dark:border-slate-700/80">
                    <a href="{{ route('presensi.index') }}"
                        class="px-2 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-white text-[11px] sm:text-xs font-bold rounded-xl flex items-center justify-center text-center transition">
                        <span>QR Saya</span>
                    </a>

                    <a href="{{ route('pengajuan-izin.create') }}"
                        class="px-2 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-white text-[11px] sm:text-xs font-bold rounded-xl flex items-center justify-center text-center transition">
                        <span>Ajukan Izin</span>
                    </a>

                    @if (auth()->user()->canScanPresensi())
                        <a href="{{ route('presensi.scan') }}"
                            class="px-2 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-white text-[11px] sm:text-xs font-bold rounded-xl flex items-center justify-center text-center transition">
                            <span>Scan QR</span>
                        </a>
                    @endif

                    @if (auth()->user()->canViewPanitiaList())
                        <a href="{{ route('presensi.monitoring') }}"
                            class="px-2 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-white text-[11px] sm:text-xs font-bold rounded-xl flex items-center justify-center text-center transition">
                            <span>Monitoring</span>
                        </a>
                    @endif
                </div>
            </section>

            {{-- 4. NOTULENSI SECTION --}}
            <section class="md:col-span-2 lg:col-span-1 bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-5 border border-slate-200/80 dark:border-slate-700 shadow-sm min-w-0">
                <div class="flex flex-col gap-3 mb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-slate-600 dark:text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span>Notulensi Rapat</span>
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Arsip berkas hasil koordinasi kegiatan</p>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('notulensi.index') }}" class="px-2.5 py-2 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-brand-600 dark:hover:text-brand-400 whitespace-nowrap">Lihat semua</a>
                        @if (auth()->user()->canManageSekretariat())
                            <button type="button"
                                @click="openAddNotulensi = true"
                                class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl transition shadow-sm cursor-pointer whitespace-nowrap">
                                + Tambah
                            </button>
                        @endif
                    </div>
                </div>

                <div class="space-y-2">
                    @forelse ($notulensiList as $notulensi)
                        <article x-data="{ open: false }"
                            class="relative bg-slate-50/80 dark:bg-slate-700/30 rounded-xl border border-slate-200/80 dark:border-slate-700 overflow-hidden">
                            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-sky-500"></div>

                            <button type="button" @click="open = !open"
                                class="w-full pl-5 pr-4 py-3.5 flex items-center gap-3 text-left cursor-pointer">
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $notulensi->judul }}</h3>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700 dark:bg-slate-800/60 dark:text-slate-300">
                                            {{ $notulensi->kegiatan?->nama ?? 'Umum' }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-0.5 truncate">
                                        {{ $notulensi->created_at?->diffForHumans() }}
                                        • {{ $notulensi->pembuat?->nama ?? 'Admin' }}
                                    </p>
                                </div>
                                <svg class="w-4 h-4 text-slate-400 shrink-0 transition-transform duration-200" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div x-show="open" x-cloak x-transition.opacity class="pl-5 pr-4 pb-4">
                                @if ($notulensi->isi)
                                    <p class="text-xs leading-relaxed text-slate-600 dark:text-slate-300 whitespace-pre-line">
                                        {{ $notulensi->isi }}
                                    </p>
                                @else
                                    <p class="text-xs text-slate-400">Tidak ada isi notulensi.</p>
                                @endif

                                <div class="mt-4 pt-3 border-t border-slate-200/80 dark:border-slate-700/80 flex flex-wrap items-center justify-between gap-3">
                                    <div>
                                        @if ($notulensi->lampiran)
                                            <a href="{{ asset('storage/' . $notulensi->lampiran) }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-brand-600 dark:text-slate-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                <span>Lihat Lampiran PDF</span>
                                            </a>
                                        @endif
                                    </div>

                                    @if (auth()->user()->canManageSekretariat())
                                        <div class="flex items-center gap-2">
                                            @php
                                                $jsonNotulensi = json_encode([
                                                    'id' => $notulensi->id,
                                                    'judul' => $notulensi->judul,
                                                    'isi' => $notulensi->isi ?? '',
                                                    'kegiatan_id' => $notulensi->kegiatan_id ? (string) $notulensi->kegiatan_id : '',
                                                    'hasLampiran' => (bool) $notulensi->lampiran,
                                                ]);
                                            @endphp
                                            <button type="button" data-item="{{ $jsonNotulensi }}"
                                                @click="selectedNotulensi = JSON.parse($el.dataset.item); openEditNotulensi = true;"
                                                class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded-xl transition">
                                                Edit
                                            </button>
                                            <form action="{{ route('notulensi.destroy', $notulensi) }}" method="POST"
                                                onsubmit="return confirm('Hapus arsip notulensi ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-xl transition">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="text-center py-4 text-xs text-slate-400">
                            Belum ada dokumen notulensi yang diunggah.
                        </div>
                    @endforelse
                </div>
            </section>

        </div>

        {{-- MODAL TAMBAH PENGUMUMAN --}}
        <div x-show="openTambahPengumuman" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="openTambahPengumuman" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="openTambahPengumuman = false"></div>

                <form action="{{ route('pengumuman.store') }}" method="POST" enctype="multipart/form-data"
                    x-show="openTambahPengumuman" x-transition
                    class="relative bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-6 w-full max-w-lg shadow-xl text-xs">
                    @csrf
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Buat Pengumuman Baru</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Judul Pengumuman *</label>
                            <input type="text" name="judul" required maxlength="255" placeholder="Judul pengumuman..."
                                class="w-full bg-slate-50 dark:bg-slate-700 rounded-xl border border-slate-300 dark:border-slate-600 p-2.5 text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Isi Pengumuman *</label>
                            <textarea name="isi" rows="4" required placeholder="Tuliskan isi pengumuman secara detail..."
                                class="w-full bg-slate-50 dark:bg-slate-700 rounded-xl border border-slate-300 dark:border-slate-600 p-2.5 text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status *</label>
                                <select name="status"
                                    class="w-full bg-slate-50 dark:bg-slate-700 rounded-xl border border-slate-300 dark:border-slate-600 p-2.5 text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                                    <option value="draft">Draft (Hanya Divisi Saya)</option>
                                    <option value="published">Publish (Semua Panitia)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Publish</label>
                                <input type="datetime-local" name="tanggal_publish"
                                    x-model="selectedPengumuman.tanggal_publish"
                                    class="w-full bg-slate-50 dark:bg-slate-700 rounded-xl border border-slate-300 dark:border-slate-600 p-2.5 text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Lampiran (PDF, Opsional)</label>
                            <input type="file" name="lampiran" accept="application/pdf"
                                class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" @click="openTambahPengumuman = false"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl shadow-sm transition">
                            Simpan Pengumuman
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL EDIT PENGUMUMAN --}}
        <div x-show="openEditPengumuman" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="openEditPengumuman" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="openEditPengumuman = false"></div>

                <form :action="'{{ url('pengumuman') }}/' + selectedPengumuman.id" method="POST" enctype="multipart/form-data"
                    x-show="openEditPengumuman" x-transition
                    class="relative bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-6 w-full max-w-lg shadow-xl text-xs">
                    @csrf
                    @method('PUT')
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Edit Pengumuman</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Judul Pengumuman *</label>
                            <input type="text" name="judul" x-model="selectedPengumuman.judul" required maxlength="255"
                                class="w-full bg-slate-50 dark:bg-slate-700 rounded-xl border border-slate-300 dark:border-slate-600 p-2.5 text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Isi Pengumuman *</label>
                            <textarea name="isi" rows="4" x-model="selectedPengumuman.isi" required
                                class="w-full bg-slate-50 dark:bg-slate-700 rounded-xl border border-slate-300 dark:border-slate-600 p-2.5 text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status *</label>
                                <select name="status" x-model="selectedPengumuman.status"
                                    class="w-full bg-slate-50 dark:bg-slate-700 rounded-xl border border-slate-300 dark:border-slate-600 p-2.5 text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                                    <option value="published">Publish (Semua Panitia)</option>
                                    <option value="draft">Draft (Hanya Divisi Saya)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Publish</label>
                                <input type="datetime-local" name="tanggal_publish"
                                    x-model="selectedPengumuman.tanggal_publish"
                                    class="w-full bg-slate-50 dark:bg-slate-700 rounded-xl border border-slate-300 dark:border-slate-600 p-2.5 text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Ganti Lampiran (Opsional)</label>
                            <input type="file" name="lampiran" accept="application/pdf"
                                class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" @click="openEditPengumuman = false"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl shadow-sm transition">
                            Perbarui Pengumuman
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL HAPUS PENGUMUMAN --}}
        <div x-show="openDeletePengumuman" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="openDeletePengumuman" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="openDeletePengumuman = false"></div>

                <form :action="'{{ url('pengumuman') }}/' + selectedPengumuman.id" method="POST"
                    x-show="openDeletePengumuman" x-transition
                    class="relative bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-6 w-full max-w-sm shadow-xl text-xs">
                    @csrf
                    @method('DELETE')
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">Hapus Pengumuman?</h3>
                    <p class="text-slate-500 dark:text-slate-400 mb-6">
                        Apakah Anda yakin ingin menghapus pengumuman <strong class="text-slate-900 dark:text-white" x-text="selectedPengumuman.judul"></strong>? Tindakan ini tidak dapat dibatalkan.
                    </p>

                    <div class="flex justify-end gap-2">
                        <button type="button" @click="openDeletePengumuman = false"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow-sm transition">
                            Ya, Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL TAMBAH NOTULENSI --}}
        <div x-show="openAddNotulensi" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="openAddNotulensi" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="openAddNotulensi = false"></div>

                <form action="{{ route('notulensi.store') }}" method="POST" enctype="multipart/form-data"
                    x-show="openAddNotulensi" x-transition
                    class="relative bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-6 w-full max-w-md shadow-xl text-xs">
                    @csrf
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Tambah Notulensi Baru</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kegiatan</label>
                            <select name="kegiatan_id"
                                class="w-full bg-slate-50 dark:bg-slate-700 rounded-xl border border-slate-300 dark:border-slate-600 p-2.5 text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                                <option value="">Umum (tidak terikat kegiatan)</option>
                                @foreach ($kegiatanOptions ?? [] as $kegiatan)
                                    <option value="{{ $kegiatan->id }}" @selected(old('kegiatan_id') == $kegiatan->id)>
                                        {{ $kegiatan->nama }}
                                        @if ($kegiatan->tanggal)
                                            ({{ \Carbon\Carbon::parse($kegiatan->tanggal)->locale('id')->isoFormat('D MMM Y') }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Judul Dokumen *</label>
                            <input type="text" name="judul" required maxlength="150" value="{{ old('judul') }}"
                                placeholder="Contoh: Notulensi Pleno Divisi"
                                class="w-full bg-slate-50 dark:bg-slate-700 rounded-xl border border-slate-300 dark:border-slate-600 p-2.5 text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Isi Notulensi</label>
                            <textarea name="isi" rows="4" placeholder="Ringkasan hasil rapat, keputusan, dan tindak lanjut..."
                                class="w-full bg-slate-50 dark:bg-slate-700 rounded-xl border border-slate-300 dark:border-slate-600 p-2.5 text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500 resize-y">{{ old('isi') }}</textarea>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Lampiran PDF</label>
                            <input type="file" name="lampiran" accept="application/pdf"
                                class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                            <span class="text-[10px] text-slate-400 block mt-1">Opsional. Maksimal 5MB. Isi atau PDF wajib diisi salah satu.</span>
                            @error('lampiran')
                                <span class="text-[10px] text-red-600 block mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" @click="openAddNotulensi = false"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl shadow-sm transition">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL EDIT NOTULENSI --}}
        <div x-show="openEditNotulensi" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="openEditNotulensi" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="openEditNotulensi = false"></div>

                <form :action="'{{ url('notulensi') }}/' + selectedNotulensi.id" method="POST" enctype="multipart/form-data"
                    x-show="openEditNotulensi" x-transition
                    class="relative bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-6 w-full max-w-md shadow-xl text-xs">
                    @csrf
                    @method('PUT')
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Edit Notulensi</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kegiatan</label>
                            <select name="kegiatan_id" x-model="selectedNotulensi.kegiatan_id"
                                class="w-full bg-slate-50 dark:bg-slate-700 rounded-xl border border-slate-300 dark:border-slate-600 p-2.5 text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                                <option value="">Umum (tidak terikat kegiatan)</option>
                                @foreach ($kegiatanOptions ?? [] as $kegiatan)
                                    <option value="{{ $kegiatan->id }}">
                                        {{ $kegiatan->nama }}
                                        @if ($kegiatan->tanggal)
                                            ({{ \Carbon\Carbon::parse($kegiatan->tanggal)->locale('id')->isoFormat('D MMM Y') }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Judul Dokumen *</label>
                            <input type="text" name="judul" x-model="selectedNotulensi.judul" required maxlength="150"
                                class="w-full bg-slate-50 dark:bg-slate-700 rounded-xl border border-slate-300 dark:border-slate-600 p-2.5 text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Isi Notulensi</label>
                            <textarea name="isi" rows="4" x-model="selectedNotulensi.isi"
                                class="w-full bg-slate-50 dark:bg-slate-700 rounded-xl border border-slate-300 dark:border-slate-600 p-2.5 text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500 resize-y"></textarea>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Ganti Lampiran PDF</label>
                            <input type="file" name="lampiran" accept="application/pdf"
                                class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                            <span class="text-[10px] text-slate-400 block mt-1" x-show="selectedNotulensi.hasLampiran">Kosongkan jika ingin tetap memakai lampiran yang ada.</span>
                            <span class="text-[10px] text-slate-400 block mt-1" x-show="!selectedNotulensi.hasLampiran">Opsional. Maksimal 5MB.</span>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" @click="openEditNotulensi = false"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl shadow-sm transition">
                            Perbarui
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>