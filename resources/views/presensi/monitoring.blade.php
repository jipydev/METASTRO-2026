<x-app-layout :$title>
    @if (session('success') || session('error') || $errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                @if (session('success'))
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: @json(session('success')), confirmButtonColor: window.appBrandColor(), timer: 2800, showConfirmButton: false });
                @elseif (session('error'))
                    Swal.fire({ icon: 'error', title: 'Gagal', text: @json(session('error')), confirmButtonColor: '#dc2626' });
                @elseif ($errors->any())
                    Swal.fire({ icon: 'error', title: 'Validasi Gagal', html: `{!! implode('<br>', $errors->all()) !!}`, confirmButtonColor: '#dc2626' });
                @endif
            });
        </script>
    @endif

    <div x-data="{
        openTambah: false,
        openImport: false,
        panitia: {{ \Illuminate\Support\Js::from($panitiaOptions->map(fn ($u) => [
            'id' => $u->id,
            'nama' => $u->nama,
            'nim' => (string) $u->nim,
            'divisi' => $u->divisi?->nama ?? 'Umum',
        ])->values()) }},
        kegiatanOptions: {{ \Illuminate\Support\Js::from($kegiatans->map(fn ($k) => [
            'id' => $k->id,
            'jam_mulai' => $k->waktu_mulai ? substr((string) $k->waktu_mulai, 0, 5) : null,
        ])->values()) }},
        selectedKegiatanId: '{{ $selectedKegiatan?->id }}',
        jamTap: '',
        panitiaQuery: '',
        panitiaId: '',
        panitiaOpen: false,
        panitiaLabel(p) {
            return p.nama + ' — ' + p.nim + ' (' + p.divisi + ')';
        },
        defaultJamTap(kegiatanId) {
            const kegiatan = this.kegiatanOptions.find((item) => String(item.id) === String(kegiatanId));
            if (!kegiatan?.jam_mulai) return '';
            const parts = kegiatan.jam_mulai.split(':');
            const date = new Date(2000, 0, 1, Number(parts[0]), Number(parts[1]));
            date.setMinutes(date.getMinutes() - 15);
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return hours + ':' + minutes;
        },
        get filteredPanitia() {
            const q = this.panitiaQuery.trim().toLowerCase();
            if (!q) return this.panitia;
            return this.panitia.filter((p) => (
                p.nama.toLowerCase().includes(q)
                || String(p.nim).toLowerCase().includes(q)
                || p.divisi.toLowerCase().includes(q)
            ));
        },
        openModalTambah() {
            this.panitiaQuery = '';
            this.panitiaId = '';
            this.panitiaOpen = false;
            this.selectedKegiatanId = '{{ $selectedKegiatan?->id }}';
            this.jamTap = this.defaultJamTap(this.selectedKegiatanId);
            this.openTambah = true;
        },
        selectPanitia(p) {
            this.panitiaId = String(p.id);
            this.panitiaQuery = this.panitiaLabel(p);
            this.panitiaOpen = false;
        },
        onPanitiaInput() {
            this.panitiaId = '';
            this.panitiaOpen = true;
        },
        confirmPanitia(event) {
            if (this.panitiaId) return;
            const exact = this.filteredPanitia.find((p) => this.panitiaLabel(p).toLowerCase() === this.panitiaQuery.trim().toLowerCase());
            const match = exact || (this.filteredPanitia.length === 1 ? this.filteredPanitia[0] : null);
            if (match) {
                this.selectPanitia(match);
                return;
            }
            event.preventDefault();
            this.panitiaOpen = true;
        }
    }" class="page-shell">
        <div class="page-wrap">

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="page-title">Monitoring Kehadiran</h1>
                    <p class="page-subtitle">
                        @if ($selectedKegiatan)
                            Rekap kehadiran {{ $selectedKegiatan->nama }}
                            · {{ \Carbon\Carbon::parse($selectedKegiatan->tanggal)->locale('id')->isoFormat('D MMMM Y') }}
                        @else
                            Rekap kehadiran panitia per kegiatan
                        @endif
                    </p>
                </div>
                @if (auth()->user()->canScanPresensi() && $kegiatans->isNotEmpty())
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button" @click="openImport = true" class="btn-filter">Impor</button>
                        <button type="button" @click="openModalTambah()" class="btn-primary">+ Tambah</button>
                    </div>
                @endif
            </div>

            <form method="GET" action="{{ route('presensi.monitoring') }}" class="filter-bar">
                <div class="w-full grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-12 gap-3 items-end">
                    <div class="sm:col-span-2 xl:col-span-4">
                        <label for="filter-search" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Pencarian</label>
                        <input id="filter-search" type="text" name="search" value="{{ $search }}" placeholder="Nama, NIM, divisi, jabatan..."
                            class="form-control-app w-full">
                    </div>

                    <div class="xl:col-span-3">
                        <label for="filter-kegiatan" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Kegiatan</label>
                        <select id="filter-kegiatan" name="kegiatan_id" onchange="this.form.submit()" class="form-control-app w-full">
                            @forelse ($kegiatans as $k)
                                <option value="{{ $k->id }}" @selected($selectedKegiatan && $selectedKegiatan->id == $k->id)>
                                    {{ $k->nama }} ({{ \Carbon\Carbon::parse($k->tanggal)->locale('id')->isoFormat('D MMM Y') }})
                                </option>
                            @empty
                                <option value="">Belum ada kegiatan</option>
                            @endforelse
                        </select>
                    </div>

                    <div class="xl:col-span-2">
                        <label for="filter-status" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Status</label>
                        <select id="filter-status" name="status" onchange="this.form.submit()" class="form-control-app w-full">
                            <option value="">Semua Status</option>
                            @foreach (['hadir' => 'Hadir', 'terlambat' => 'Terlambat', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpa' => 'Alpa', 'belum_hadir' => 'Belum Hadir'] as $key => $label)
                                <option value="{{ $key }}" @selected($statusFilter === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="xl:col-span-2">
                        <label for="filter-sort" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Urutkan</label>
                        <select id="filter-sort" name="sort" onchange="this.form.submit()" class="form-control-app w-full">
                            <option value="waktu" @selected($sort === 'waktu')>Waktu terbaru</option>
                            <option value="divisi" @selected($sort === 'divisi')>Divisi</option>
                            <option value="nama" @selected($sort === 'nama')>Nama</option>
                            <option value="status" @selected($sort === 'status')>Status</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-3 xl:col-span-1 xl:pb-0.5">
                        <button type="submit" class="btn-filter">Cari</button>
                        @if ($search !== '' || $statusFilter || $sort !== 'waktu')
                            <a href="{{ route('presensi.monitoring', array_filter(['kegiatan_id' => $selectedKegiatan?->id])) }}"
                                class="text-xs font-semibold text-slate-500 hover:text-brand-600 dark:text-slate-400 whitespace-nowrap">
                                Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            <div class="table-card">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                        <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200/80 dark:border-slate-700 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            <tr>
                                <th class="px-5 py-3.5">Pengguna</th>
                                <th class="px-5 py-3.5">Divisi / Jabatan</th>
                                <th class="px-5 py-3.5 text-center">Waktu Presensi</th>
                                <th class="px-5 py-3.5 text-center">Status</th>
                                <th class="px-5 py-3.5">Di scan / disetujui oleh</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse ($usersData as $row)
                                @php
                                    $badgeClass = match ($row['status']) {
                                        'hadir' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                                        'terlambat' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                                        'izin' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                                        'sakit' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                                        'alpa' => 'bg-red-50 text-red-700 dark:bg-red-950/60 dark:text-red-300 border-red-200 dark:border-red-800',
                                        default => 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400 border-slate-200 dark:border-slate-600',
                                    };
                                @endphp
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/20 transition">
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $row['foto'] }}" alt="Foto {{ $row['nama'] }}"
                                                class="w-9 h-9 rounded-xl object-cover border border-slate-200 dark:border-slate-600 shrink-0">
                                            <div class="min-w-0">
                                                <div class="font-bold text-slate-900 dark:text-white truncate">{{ $row['nama'] }}</div>
                                                <div class="text-[11px] font-mono text-slate-400">{{ $row['nim'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 font-medium text-slate-800 dark:text-slate-200">
                                        {{ $row['divisi_jabatan'] }}
                                    </td>
                                    <td class="px-5 py-3.5 text-center whitespace-nowrap font-mono text-[11px]">
                                        {{ $row['waktu_presensi'] !== '-' ? $row['waktu_presensi'] . ' WIB' : '-' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold border uppercase tracking-wider {{ $badgeClass }}">
                                            {{ str_replace('_', ' ', $row['status']) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        @if ($row['via_izin'])
                                            <div class="text-[10px] uppercase tracking-wider font-semibold text-slate-400">Disetujui oleh</div>
                                            <div class="font-medium text-slate-900 dark:text-slate-200">{{ $row['izin_reviewer'] ?? '-' }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $row['izin_reviewer_divisi'] ?? 'Ranger' }}</div>
                                        @elseif ($row['scanner_nama'])
                                            <div class="font-medium text-slate-900 dark:text-slate-200">{{ $row['scanner_nama'] }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $row['scanner_divisi'] }}</div>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-400 dark:text-slate-500">
                                        <p class="font-medium">Tidak ada data kehadiran yang sesuai dengan filter.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($usersData->hasPages())
                    <div class="px-5 py-3.5 border-t border-slate-200/80 dark:border-slate-700">
                        {{ $usersData->onEachSide(1)->links() }}
                    </div>
                @endif
            </div>

        </div>

        @if (auth()->user()->canScanPresensi() && $kegiatans->isNotEmpty())
            @php
                $fieldClass = 'w-full bg-slate-50 dark:bg-slate-700 rounded-xl border border-slate-300 dark:border-slate-600 p-2.5 text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500';
                $fileClass = 'w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 dark:file:bg-slate-600 dark:file:text-slate-200 hover:file:bg-slate-200 cursor-pointer';
            @endphp

            <div x-show="openTambah" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div x-show="openTambah" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="openTambah = false"></div>
                    <form action="{{ route('presensi.store') }}" method="POST"
                        x-show="openTambah" x-transition @submit="confirmPanitia($event)"
                        class="relative bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-6 w-full max-w-lg shadow-xl text-xs">
                        @csrf
                        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Tambah Presensi Manual</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-4">
                            Untuk absen kertas saat scanner bermasalah. Sesi presensi tidak perlu sedang dibuka.
                        </p>
                        <div class="space-y-4">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kegiatan *</label>
                                <select name="kegiatan_id" required class="{{ $fieldClass }}"
                                    x-model="selectedKegiatanId"
                                    @change="jamTap = defaultJamTap(selectedKegiatanId)">
                                    @foreach ($kegiatans as $k)
                                        <option value="{{ $k->id }}" @selected($selectedKegiatan && $selectedKegiatan->id == $k->id)>
                                            {{ $k->nama }} ({{ \Carbon\Carbon::parse($k->tanggal)->locale('id')->isoFormat('D MMM Y') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="relative" @click.outside="panitiaOpen = false">
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Panitia *</label>
                                <input type="hidden" name="user_id" x-model="panitiaId">
                                <input type="text"
                                    x-model="panitiaQuery"
                                    @input="onPanitiaInput()"
                                    @focus="panitiaOpen = true"
                                    @keydown.escape.prevent="panitiaOpen = false"
                                    @keydown.enter.prevent="filteredPanitia[0] && selectPanitia(filteredPanitia[0])"
                                    autocomplete="off"
                                    required
                                    placeholder="Cari nama, NIM, atau divisi..."
                                    class="{{ $fieldClass }}">
                                <div x-show="panitiaOpen" x-cloak
                                    class="absolute z-20 mt-1 w-full max-h-48 overflow-y-auto rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 shadow-lg">
                                    <template x-for="p in filteredPanitia" :key="p.id">
                                        <button type="button"
                                            @click="selectPanitia(p)"
                                            class="w-full text-left px-3 py-2 hover:bg-brand-50 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-100"
                                            :class="String(p.id) === panitiaId && 'bg-brand-50 dark:bg-slate-600'">
                                            <div class="font-semibold truncate" x-text="p.nama"></div>
                                            <div class="text-[10px] text-slate-400 font-mono" x-text="p.nim + ' · ' + p.divisi"></div>
                                        </button>
                                    </template>
                                    <div x-show="filteredPanitia.length === 0" class="px-3 py-2.5 text-slate-400">
                                        Panitia tidak ditemukan.
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jam tap</label>
                                <input type="time" name="jam_tap" x-model="jamTap" class="{{ $fieldClass }}">
                                <p class="mt-1 text-[10px] text-slate-400">Default 15 menit sebelum kegiatan dimulai. Bisa diubah.</p>
                            </div>
                        </div>
                        <div class="mt-5 pt-3 flex justify-end gap-2 border-t border-slate-100 dark:border-slate-700">
                            <button type="button" @click="openTambah = false"
                                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl transition">Batal</button>
                            <button type="submit"
                                class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl transition shadow-sm">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            <div x-show="openImport" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div x-show="openImport" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="openImport = false"></div>
                    <form action="{{ route('presensi.import') }}" method="POST" enctype="multipart/form-data"
                        x-show="openImport" x-transition
                        class="relative bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-6 w-full max-w-lg shadow-xl text-xs">
                        @csrf
                        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Impor Presensi</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-4">
                            Unggah CSV atau Excel dari absen kertas. Kolom wajib: <span class="font-mono">nim</span>.
                            Opsional: <span class="font-mono">nama</span>, <span class="font-mono">status</span>, <span class="font-mono">waktu</span>.
                            Baris yang sudah tercatat akan dilewati.
                        </p>
                        <div class="space-y-4">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kegiatan *</label>
                                <select name="kegiatan_id" required class="{{ $fieldClass }}">
                                    @foreach ($kegiatans as $k)
                                        <option value="{{ $k->id }}" @selected($selectedKegiatan && $selectedKegiatan->id == $k->id)>
                                            {{ $k->nama }} ({{ \Carbon\Carbon::parse($k->tanggal)->locale('id')->isoFormat('D MMM Y') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Berkas CSV / Excel *</label>
                                <input type="file" name="file" required accept=".csv,.txt,.xlsx,.xls" class="{{ $fileClass }}">
                            </div>
                            <a href="{{ route('presensi.template') }}" class="inline-flex text-[11px] font-semibold text-brand-600 hover:text-brand-700">
                                Unduh template CSV
                            </a>
                        </div>
                        <div class="mt-5 pt-3 flex justify-end gap-2 border-t border-slate-100 dark:border-slate-700">
                            <button type="button" @click="openImport = false"
                                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl transition">Batal</button>
                            <button type="submit"
                                class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl transition shadow-sm">Impor</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
