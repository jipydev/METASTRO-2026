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

    <div x-data="{
        showModal: false,
        showDeleteModal: false,
        isEdit: false,
        formAction: '{{ route('kegiatan.store') }}',
        form: {
            id: null,
            nama: '',
            deskripsi: '',
            tanggal: '',
            waktu_mulai: '',
            waktu_selesai: '',
            tempat: '',
            status_presensi: 'dijadwalkan',
            presensi_mulai: '',
            presensi_selesai: ''
        },
    
        // Helper untuk format tanggal-waktu ke input datetime-local (YYYY-MM-DDTHH:mm)
        formatDateTime(tanggal, waktu) {
            if (!tanggal || !waktu) return '';
            return `${tanggal.split('T')[0]}T${waktu.substring(0, 5)}`;
        },
    
        updateDefaultPresensi() {
            if (!this.form.tanggal || !this.form.waktu_mulai) return;
    
            // Buat objek Date dari tanggal dan waktu mulai kegiatan
            let [jamMulai, menitMulai] = this.form.waktu_mulai.split(':');
            let mulaiDateTime = new Date(this.form.tanggal);
            mulaiDateTime.setHours(parseInt(jamMulai), parseInt(menitMulai), 0);
    
            // 1. Kurangi 45 menit untuk Presensi Mulai (Dibuka)
            let bukaOtomatis = new Date(mulaiDateTime.getTime() - (45 * 60000));
    
            // 2. Presensi Selesai: Samakan dengan Waktu Selesai jika ada, jika kosong default +2 jam dari mulai
            let selesaiDateTime;
            if (this.form.waktu_selesai) {
                let [jamSelesai, menitSelesai] = this.form.waktu_selesai.split(':');
                selesaiDateTime = new Date(this.form.tanggal);
                selesaiDateTime.setHours(parseInt(jamSelesai), parseInt(menitSelesai), 0);
            } else {
                selesaiDateTime = new Date(mulaiDateTime.getTime() + (120 * 60000));
            }
    
            let pad = (n) => String(n).padStart(2, '0');
            let formatToLocalIso = (d) => {
                return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
            };
    
            this.form.presensi_mulai = formatToLocalIso(bukaOtomatis);
            this.form.presensi_selesai = formatToLocalIso(selesaiDateTime);
        },
    
        openCreate() {
            this.isEdit = false;
            this.formAction = '{{ route('kegiatan.store') }}';
            let today = new Date().toISOString().split('T')[0];
            this.form = {
                id: null,
                nama: '',
                deskripsi: '',
                tanggal: today,
                waktu_mulai: '08:00',
                waktu_selesai: '10:00',
                tempat: '',
                status_presensi: 'dijadwalkan',
                presensi_mulai: '',
                presensi_selesai: ''
            };
            this.updateDefaultPresensi();
            this.showModal = true;
        },
    
        openEdit(item) {
            this.isEdit = true;
            this.formAction = '/kegiatan/' + item.id;
            this.form = Object.assign({}, item);
            this.showModal = true;
        },
    
        openDelete(id, nama) {
            this.form.id = id;
            this.form.nama = nama;
            this.showDeleteModal = true;
        }
    }" class="page-shell">

        <div class="page-wrap">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="page-title">Kegiatan</h1>
                    <p class="page-subtitle">Timeline dan jadwal lengkap kegiatan METASTRO</p>
                </div>

                @if (auth()->user()->canManageKegiatan())
                    <button type="button" @click="openCreate()" class="btn-primary">
                        + Tambah
                    </button>
                @endif
            </div>

            <form method="GET" action="{{ route('kegiatan.index') }}" class="filter-bar">
                <div class="flex-1 min-w-0 w-full">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, tempat, atau deskripsi..."
                        class="form-control-app w-full">
                </div>
                <button type="submit" class="btn-filter">Filter</button>
                @if (request()->filled('search'))
                    <a href="{{ route('kegiatan.index') }}" class="text-xs text-slate-500 hover:text-slate-700 dark:text-slate-400">Reset</a>
                @endif
            </form>

            {{-- Grid Kartu Kegiatan --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($kegiatans as $item)
                    {{-- Di dalam loop @forelse($kegiatans as $item) --}}
                    @php
                        $jsonItem = json_encode([
                            'id' => $item->id,
                            'nama' => $item->nama,
                            'deskripsi' => $item->deskripsi ?? '',
                            'tanggal' => \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d'),
                            'waktu_mulai' => $item->waktu_mulai ? substr($item->waktu_mulai, 0, 5) : '',
                            'waktu_selesai' => $item->waktu_selesai ? substr($item->waktu_selesai, 0, 5) : '',
                            'tempat' => $item->tempat,
                            'status_presensi' => $item->status_presensi_aktif ?? 'dijadwalkan', // Diperbaiki dari status_presensi_aktif_aktif
                            'presensi_mulai' => $item->presensi_mulai
                                ? \Carbon\Carbon::parse($item->presensi_mulai)->format('Y-m-d\TH:i')
                                : '',
                            'presensi_selesai' => $item->presensi_selesai
                                ? \Carbon\Carbon::parse($item->presensi_selesai)->format('Y-m-d\TH:i')
                                : '',
                        ]);
                    @endphp

                    <div
                        class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-5 shadow-sm flex flex-col justify-between relative overflow-hidden">
                        {{-- Aksen Garis --}}
                        <div
                            class="absolute top-0 left-0 bottom-0 w-1.5 {{ $item->status_presensi_aktif === 'buka' ? 'bg-emerald-500' : ($item->status_presensi_aktif === 'tutup' ? 'bg-slate-400' : 'bg-brand-600') }}">
                        </div>

                        <div class="pl-2 space-y-3">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white leading-tight">
                                    {{ $item->nama }}</h3>
                                <span
                                    class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider shrink-0
                                    {{ $item->status_presensi_aktif === 'buka' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : ($item->status_presensi_aktif === 'tutup' ? 'bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-red-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300') }}">
                                    {{ $item->status_presensi_aktif }}
                                </span>
                            </div>

                            <div
                                class="text-xs text-gray-600 dark:text-slate-300 space-y-1.5 bg-slate-50 dark:bg-slate-700/40 p-3.5 rounded-xl">
                                <p class="flex items-center gap-1.5 font-medium">
                                    <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <span>{{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
                                </p>
                                <p class="flex items-center gap-1.5 font-medium">
                                    <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Pukul {{ substr($item->waktu_mulai, 0, 5) }}
                                        {{ $item->waktu_selesai ? '- ' . substr($item->waktu_selesai, 0, 5) : '' }}
                                        WIB</span>
                                </p>
                                <p class="flex items-center gap-1.5 font-medium">
                                    <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span>{{ $item->tempat }}</span>
                                </p>
                                <p
                                    class="flex items-center gap-1.5 font-semibold text-slate-600 dark:text-slate-400 pt-0.5">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                        </path>
                                    </svg>
                                    <span>{{ $item->presensis_count ?? 0 }} Hadir</span>
                                </p>
                            </div>

                            @if ($item->deskripsi)
                                <p class="text-[11px] text-gray-400 dark:text-slate-400 line-clamp-2">
                                    {{ $item->deskripsi }}</p>
                            @endif
                        </div>

                        @if (auth()->user()->canTogglePresensi() || auth()->user()->canManageKegiatan())
                            <div
                                class="pl-2 mt-4 pt-3 border-t border-gray-100 dark:border-slate-700 flex items-center justify-between gap-2">
                                @if (auth()->user()->canTogglePresensi())
                                <form action="{{ route('presensi.toggle', $item) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status_presensi"
                                        value="{{ $item->status_presensi_aktif === 'buka' ? 'tutup' : 'buka' }}">
                                    <button type="submit"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded-lg transition {{ $item->status_presensi_aktif === 'buka' ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                        @if ($item->status_presensi_aktif === 'buka')
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                            </svg>
                                            Tutup Presensi
                                        @else
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                            </svg>
                                            Buka Presensi
                                        @endif
                                    </button>
                                </form>
                                @else
                                    <span></span>
                                @endif

                                @if (auth()->user()->canManageKegiatan())
                                <div class="flex items-center gap-1.5">
                                    <button type="button" @click="openEdit({{ $jsonItem }})"
                                        class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white text-[11px] font-semibold rounded-lg transition">Edit</button>
                                    <button type="button"
                                        @click="openDelete({{ $item->id }}, '{{ addslashes($item->nama) }}')"
                                        class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700 hover:bg-red-600 hover:text-white text-gray-600 dark:text-slate-300 text-[11px] font-semibold rounded-lg transition">Hapus</button>
                                </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-gray-400 text-xs font-medium">
                        Belum ada timeline kegiatan yang terdaftar.
                    </div>
                @endforelse
            </div>

        </div>

        {{-- ================================================================= --}}
        {{-- MODAL FORM (CREATE / EDIT TERPADU)                                --}}
        {{-- ================================================================= --}}
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog"
            aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 p-0">
                <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm"
                    @click="showModal = false"></div>

                <form :action="formAction" method="POST" x-show="showModal" x-transition
                    class="relative bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 p-6 w-full max-w-lg shadow-xl text-xs space-y-3">
                    @csrf
                    <template x-if="isEdit"><input type="hidden" name="_method" value="PUT"></template>

                    <h3 class="text-sm font-bold text-gray-900 dark:text-white"
                        x-text="isEdit ? 'Edit Jadwal Kegiatan' : 'Tambah Kegiatan Baru'"></h3>

                    <div>
                        <label class="block font-semibold mb-1 text-gray-700 dark:text-slate-300">Nama Kegiatan
                            *</label>
                        <input type="text" name="nama" x-model="form.nama" required maxlength="255"
                            placeholder="Contoh: Rapat Besar 1"
                            class="w-full bg-slate-50 dark:bg-slate-700 rounded-xl border-gray-300 dark:border-slate-600 p-2.5 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                    </div>

                    <div>
                        <label class="block font-semibold mb-1 text-gray-700 dark:text-slate-300">Deskripsi /
                            Agenda</label>
                        <textarea name="deskripsi" x-model="form.deskripsi" rows="2" maxlength="2000" placeholder="Catatan singkat..."
                            class="w-full bg-slate-50 dark:bg-slate-700 rounded-xl border-gray-300 dark:border-slate-600 p-2.5 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500"></textarea>
                    </div>

                    <div class="grid grid-cols-1 gap-2.5">
                        <div>
                            <label class="block font-semibold mb-1 text-gray-700 dark:text-slate-300">Tempat *</label>
                            <input type="text" name="tempat" x-model="form.tempat" required maxlength="255"
                                placeholder="Gedung / Ruangan"
                                class="w-full bg-slate-50 dark:bg-slate-700 rounded-xl border-gray-300 dark:border-slate-600 p-2.5 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                        <div>
                            <label class="block font-semibold mb-1 text-gray-700 dark:text-slate-300">Tanggal
                                *</label>
                            <input type="date" name="tanggal" x-model="form.tanggal"
                                @change="updateDefaultPresensi()" required
                                class="w-full bg-slate-50 dark:bg-slate-700 rounded-xl border-gray-300 dark:border-slate-600 p-2.5 text-xs text-gray-900 dark:text-white outline-none">
                        </div>
                        <div>
                            <label class="block font-semibold mb-1 text-gray-700 dark:text-slate-300">Mulai
                                *</label>
                            <input type="time" name="waktu_mulai" x-model="form.waktu_mulai"
                                @change="updateDefaultPresensi()" required
                                class="w-full bg-slate-50 dark:bg-slate-700 rounded-xl border-gray-300 dark:border-slate-600 p-2.5 text-xs text-gray-900 dark:text-white outline-none">
                        </div>
                        <div>
                            <label class="block font-semibold mb-1 text-gray-700 dark:text-slate-300">Selesai</label>
                            <input type="time" name="waktu_selesai" x-model="form.waktu_selesai"
                                @change="updateDefaultPresensi()"
                                class="w-full bg-slate-50 dark:bg-slate-700 rounded-xl border-gray-300 dark:border-slate-600 p-2.5 text-xs text-gray-900 dark:text-white outline-none">
                        </div>
                    </div>

                    {{-- Field Jadwal Otomatis Presensi (Dapat diedit manual) --}}
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-2 border-t border-gray-100 dark:border-slate-700">
                        <div>
                            <label class="block font-semibold mb-1 text-gray-700 dark:text-slate-300">Presensi Dibuka
                                (Otomatis)</label>
                            <input type="datetime-local" name="presensi_mulai" x-model="form.presensi_mulai"
                                class="w-full bg-slate-50 dark:bg-slate-700 rounded-xl border-gray-300 dark:border-slate-600 p-2 text-xs text-gray-900 dark:text-white outline-none">
                        </div>
                        <div>
                            <label class="block font-semibold mb-1 text-gray-700 dark:text-slate-300">Presensi Ditutup
                                (Otomatis)</label>
                            <input type="datetime-local" name="presensi_selesai" x-model="form.presensi_selesai"
                                class="w-full bg-slate-50 dark:bg-slate-700 rounded-xl border-gray-300 dark:border-slate-600 p-2 text-xs text-gray-900 dark:text-white outline-none">
                        </div>
                    </div>

                    <div class="mt-4 pt-3 flex justify-end gap-2 border-t border-gray-100 dark:border-slate-700">
                        <button type="button" @click="showModal = false"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-gray-700 dark:text-slate-200 rounded-xl transition">Batal</button>
                        <button type="submit"
                            class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl transition shadow-sm"
                            x-text="isEdit ? 'Perbarui' : 'Simpan'"></button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================================================================= --}}
        {{-- MODAL HAPUS                                                       --}}
        {{-- ================================================================= --}}
        <div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 p-0">
                <div x-show="showDeleteModal" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm"
                    @click="showDeleteModal = false"></div>

                <form :action="'/kegiatan/' + form.id" method="POST" x-show="showDeleteModal" x-transition
                    class="relative bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 p-5 w-full max-w-sm shadow-xl text-center text-xs">
                    @csrf @method('DELETE')
                    <span class="text-3xl block mb-2">🗑️</span>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Hapus Kegiatan?</h3>
                    <p class="text-gray-500 mb-4">Hapus agenda <strong x-text="form.nama"></strong>? Seluruh data
                        presensi terkait akan ikut terhapus.</p>
                    <div class="flex justify-center gap-2">
                        <button type="button" @click="showDeleteModal = false"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-gray-700 dark:text-slate-200 rounded-xl transition">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition shadow-sm">Ya,
                            Hapus</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
