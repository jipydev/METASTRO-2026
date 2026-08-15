<x-app-layout :$title>
    {{-- Notifikasi SweetAlert --}}
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

    <div x-data="{
        showModal: false,
        showDeleteModal: false,
        isEdit: false,
        formAction: '{{ route('kegiatan.store') }}',
        form: { id: null, judul: '', deskripsi: '', tanggal: '', waktu_mulai: '', waktu_selesai: '', tempat: '', status_presensi: 'dijadwalkan' },

        openCreate() {
            this.isEdit = false;
            this.formAction = '{{ route('kegiatan.store') }}';
            this.form = { id: null, judul: '', deskripsi: '', tanggal: '', waktu_mulai: '', waktu_selesai: '', tempat: '', status_presensi: 'dijadwalkan' };
            this.showModal = true;
        },
        openEdit(item) {
            this.isEdit = true;
            this.formAction = '/kegiatan/' + item.id;
            this.form = Object.assign({}, item);
            this.showModal = true;
        },
        openDelete(id, judul) {
            this.form.id = id;
            this.form.judul = judul;
            this.showDeleteModal = true;
        }
    }" class="py-8 min-h-screen bg-gray-50 dark:bg-slate-900 font-poppins transition-colors">

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Timeline & Jadwal Kegiatan</h1>
                    <a href="{{ route('dashboard') }}" class="text-xs text-slate-500 hover:text-indigo-600 transition inline-flex items-center gap-1 mt-1">
                        &larr; Kembali ke Dashboard
                    </a>
                </div>

                @if(auth()->user()->canManageSekretariat())
                    <button type="button" @click="openCreate()"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition self-start sm:self-auto cursor-pointer">
                        + Tambah Kegiatan
                    </button>
                @endif
            </div>

            {{-- Grid Kartu Kegiatan --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($kegiatans as $item)
                    @php
                        $jsonItem = json_encode([
                            'id'              => $item->id,
                            'judul'           => $item->judul,
                            'deskripsi'       => $item->deskripsi ?? '',
                            'tanggal'         => \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d'),
                            'waktu_mulai'     => substr($item->waktu_mulai, 0, 5),
                            'waktu_selesai'   => $item->waktu_selesai ? substr($item->waktu_selesai, 0, 5) : '',
                            'tempat'          => $item->tempat,
                            'status_presensi' => $item->status_presensi ?? 'dijadwalkan',
                        ]);
                    @endphp

                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-5 shadow-sm flex flex-col justify-between relative overflow-hidden">
                        {{-- Aksen Garis --}}
                        <div class="absolute top-0 left-0 bottom-0 w-1.5 {{ $item->status_presensi === 'buka' ? 'bg-emerald-500' : ($item->status_presensi === 'tutup' ? 'bg-slate-400' : 'bg-indigo-600') }}"></div>

                        <div class="pl-2 space-y-3">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white leading-tight">{{ $item->judul }}</h3>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider shrink-0
                                    {{ $item->status_presensi === 'buka' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : ($item->status_presensi === 'tutup' ? 'bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-red-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300') }}">
                                    {{ $item->status_presensi }}
                                </span>
                            </div>

                            <div class="text-xs text-gray-600 dark:text-slate-300 space-y-1 bg-slate-50 dark:bg-slate-700/40 p-3 rounded-xl">
                                <p>📅 {{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
                                <p>⏰ Pukul {{ substr($item->waktu_mulai, 0, 5) }} {{ $item->waktu_selesai ? '- ' . substr($item->waktu_selesai, 0, 5) : '' }} WIB</p>
                                <p>📍 {{ $item->tempat }}</p>
                                <p class="text-indigo-600 dark:text-indigo-400 font-semibold">👥 {{ $item->presensis_count ?? 0 }} Hadir</p>
                            </div>

                            @if($item->deskripsi)
                                <p class="text-[11px] text-gray-400 dark:text-slate-400 line-clamp-2">{{ $item->deskripsi }}</p>
                            @endif
                        </div>

                        @if(auth()->user()->canManageSekretariat())
                            <div class="pl-2 mt-4 pt-3 border-t border-gray-100 dark:border-slate-700 flex items-center justify-between gap-2">
                                <form action="{{ route('presensi.toggle', $item) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status_presensi" value="{{ $item->status_presensi === 'buka' ? 'tutup' : 'buka' }}">
                                    <button type="submit" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition {{ $item->status_presensi === 'buka' ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                        {{ $item->status_presensi === 'buka' ? '🔒 Tutup' : '🔓 Buka' }} Presensi
                                    </button>
                                </form>

                                <div class="flex items-center gap-1.5">
                                    <button type="button" @click="openEdit({{ $jsonItem }})" class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white text-[11px] font-semibold rounded-lg transition">Edit</button>
                                    <button type="button" @click="openDelete({{ $item->id }}, '{{ addslashes($item->judul) }}')" class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700 hover:bg-red-600 hover:text-white text-gray-600 dark:text-slate-300 text-[11px] font-semibold rounded-lg transition">Hapus</button>
                                </div>
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
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 p-0">
                <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showModal = false"></div>

                <form :action="formAction" method="POST" x-show="showModal" x-transition
                      class="relative bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 p-6 w-full max-w-lg shadow-xl text-xs space-y-3">
                    @csrf
                    <template x-if="isEdit"><input type="hidden" name="_method" value="PUT"></template>

                    <h3 class="text-sm font-bold text-gray-900 dark:text-white" x-text="isEdit ? 'Edit Jadwal Kegiatan' : 'Tambah Kegiatan Baru'"></h3>

                    <div>
                        <label class="block font-semibold mb-1 text-gray-700 dark:text-slate-300">Nama Kegiatan *</label>
                        <input type="text" name="judul" x-model="form.judul" required placeholder="Contoh: Rapat Pleno 1"
                               class="w-full bg-slate-50 dark:bg-slate-700 rounded-xl border-gray-300 dark:border-slate-600 p-2.5 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block font-semibold mb-1 text-gray-700 dark:text-slate-300">Deskripsi / Agenda</label>
                        <textarea name="deskripsi" x-model="form.deskripsi" rows="2" placeholder="Catatan singkat..."
                                  class="w-full bg-slate-50 dark:bg-slate-700 rounded-xl border-gray-300 dark:border-slate-600 p-2.5 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                        <div>
                            <label class="block font-semibold mb-1 text-gray-700 dark:text-slate-300">Tanggal *</label>
                            <input type="date" name="tanggal" x-model="form.tanggal" required
                                   class="w-full bg-slate-50 dark:bg-slate-700 rounded-xl border-gray-300 dark:border-slate-600 p-2.5 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block font-semibold mb-1 text-gray-700 dark:text-slate-300">Mulai *</label>
                            <input type="time" name="waktu_mulai" x-model="form.waktu_mulai" required
                                   class="w-full bg-slate-50 dark:bg-slate-700 rounded-xl border-gray-300 dark:border-slate-600 p-2.5 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block font-semibold mb-1 text-gray-700 dark:text-slate-300">Selesai</label>
                            <input type="time" name="waktu_selesai" x-model="form.waktu_selesai"
                                   class="w-full bg-slate-50 dark:bg-slate-700 rounded-xl border-gray-300 dark:border-slate-600 p-2.5 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <div>
                            <label class="block font-semibold mb-1 text-gray-700 dark:text-slate-300">Tempat *</label>
                            <input type="text" name="tempat" x-model="form.tempat" required placeholder="Gedung / Ruangan"
                                   class="w-full bg-slate-50 dark:bg-slate-700 rounded-xl border-gray-300 dark:border-slate-600 p-2.5 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block font-semibold mb-1 text-gray-700 dark:text-slate-300">Status Presensi</label>
                            <select name="status_presensi" x-model="form.status_presensi"
                                    class="w-full bg-slate-50 dark:bg-slate-700 rounded-xl border-gray-300 dark:border-slate-600 p-2.5 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="dijadwalkan">Dijadwalkan</option>
                                <option value="buka">Buka Langsung</option>
                                <option value="tutup">Tutup</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 flex justify-end gap-2 border-t border-gray-100 dark:border-slate-700">
                        <button type="button" @click="showModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-gray-700 dark:text-slate-200 rounded-xl transition">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition shadow-sm" x-text="isEdit ? 'Perbarui' : 'Simpan'"></button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================================================================= --}}
        {{-- MODAL HAPUS                                                       --}}
        {{-- ================================================================= --}}
        <div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 p-0">
                <div x-show="showDeleteModal" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showDeleteModal = false"></div>

                <form :action="'/kegiatan/' + form.id" method="POST" x-show="showDeleteModal" x-transition
                      class="relative bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 p-5 w-full max-w-sm shadow-xl text-center text-xs">
                    @csrf @method('DELETE')
                    <span class="text-3xl block mb-2">🗑️</span>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Hapus Kegiatan?</h3>
                    <p class="text-gray-500 mb-4">Hapus agenda <strong x-text="form.judul"></strong>? Seluruh data presensi terkait akan ikut terhapus.</p>
                    <div class="flex justify-center gap-2">
                        <button type="button" @click="showDeleteModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-gray-700 dark:text-slate-200 rounded-xl transition">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition shadow-sm">Ya, Hapus</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>