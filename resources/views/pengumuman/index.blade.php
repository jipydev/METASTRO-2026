<x-app-layout :$title>
    @if (session('success') || session('error') || $errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                @if (session('success'))
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: @json(session('success')), confirmButtonColor: window.appBrandColor(), timer: 2200, showConfirmButton: false });
                @elseif (session('error'))
                    Swal.fire({ icon: 'error', title: 'Gagal', text: @json(session('error')), confirmButtonColor: '#dc2626' });
                @elseif ($errors->any())
                    Swal.fire({ icon: 'error', title: 'Validasi Gagal', html: `{!! implode('<br>', $errors->all()) !!}`, confirmButtonColor: '#dc2626' });
                @endif
            });
        </script>
    @endif

    <div x-data="{
        openTambahPengumuman: false,
        openEditPengumuman: false,
        openDeletePengumuman: false,
        minPublishAt: @js($minPublishAt),
        selectedPengumuman: { id: null, judul: '', isi: '', status: 'draft', tanggal_publish: @js($minPublishAt) }
    }" class="page-shell">

        <div class="page-wrap">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="page-title">Pengumuman</h1>
                    <p class="page-subtitle">Arsip lengkap informasi untuk panitia dan peserta</p>
                </div>
                @if (auth()->user()->canCreatePengumuman())
                    <button type="button"
                        @click="selectedPengumuman = { id: null, judul: '', isi: '', status: 'draft', tanggal_publish: minPublishAt }; openTambahPengumuman = true;"
                        class="btn-primary">
                        + Tambah
                    </button>
                @endif
            </div>

            <form method="GET" action="{{ route('pengumuman.index') }}" class="filter-bar">
                <div class="flex-1 min-w-0 w-full">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul atau isi..."
                        class="form-control-app w-full">
                </div>
                <div>
                    <select name="status" class="form-control-app">
                        <option value="">Semua Status</option>
                        <option value="published" @selected(request('status') === 'published')>Published</option>
                        <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                    </select>
                </div>
                <button type="submit" class="btn-filter">Filter</button>
                @if (request()->hasAny(['search', 'status']))
                    <a href="{{ route('pengumuman.index') }}" class="text-xs text-slate-500 hover:text-slate-700 dark:text-slate-400">Reset</a>
                @endif
            </form>

            <div class="space-y-3">
                @forelse ($pengumumans as $item)
                    <article x-data="{ open: false }" class="relative bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm">
                        <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $item->isPublished() ? 'bg-brand-600' : 'bg-amber-500' }}"></div>
                        <button type="button" @click="open = !open" class="w-full pl-5 pr-4 py-4 flex items-center gap-3 text-left">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">{{ $item->judul }}</h2>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $item->isPublished() ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : ($item->isScheduled() ? 'bg-sky-100 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300') }}">
                                        {{ $item->isScheduled() ? 'Terjadwal' : ucfirst((string) $item->status) }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1">
                                    @if ($item->isPublished())
                                        Dipublikasikan {{ $item->tanggal_publish?->locale('id')->isoFormat('D MMMM Y, HH.mm') ?? '—' }} WIB
                                    @elseif ($item->isScheduled())
                                        Rilis otomatis {{ $item->tanggal_publish->locale('id')->isoFormat('D MMMM Y, HH.mm') }} WIB
                                    @else
                                        {{ $item->tanggal_publish?->locale('id')->isoFormat('D MMMM Y, HH.mm') ?? 'Draft' }} WIB
                                    @endif
                                    • {{ $item->pembuat?->nama ?? 'Admin' }}
                                    ({{ $item->pembuat?->divisi?->nama ?? 'Umum' }})
                                </p>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 shrink-0 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" x-cloak class="pl-5 pr-4 pb-4">
                            <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-300 whitespace-pre-line">{{ $item->isi }}</p>
                            <div class="mt-4 pt-3 border-t border-slate-200 dark:border-slate-700 flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    @if ($item->lampiran)
                                        <a href="{{ asset('storage/' . $item->lampiran) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 dark:text-slate-400">
                                            Lihat Lampiran PDF
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
                                                'status' => strtolower((string) $item->status) === 'publish' ? 'published' : strtolower((string) $item->status),
                                                'tanggal_publish' => $item->tanggal_publish?->format('Y-m-d\TH:i') ?? '',
                                            ]);
                                        @endphp
                                        <button type="button" data-item="{{ $jsonItem }}"
                                            @click="selectedPengumuman = JSON.parse($el.dataset.item); openEditPengumuman = true;"
                                            class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded-xl">Edit</button>
                                        <button type="button"
                                            @click="selectedPengumuman = { id: {{ $item->id }}, judul: {{ \Illuminate\Support\Js::from($item->judul) }} }; openDeletePengumuman = true;"
                                            class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-xl">Hapus</button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="text-center py-16 text-xs text-slate-400">Belum ada pengumuman.</div>
                @endforelse
            </div>

            @if ($pengumumans->hasPages())
                <div class="mt-6">{{ $pengumumans->links() }}</div>
            @endif
        </div>

        @php
            $fieldClass = 'w-full bg-slate-50 dark:bg-slate-700 rounded-xl border border-slate-300 dark:border-slate-600 p-2.5 text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500';
            $fileClass = 'w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer';
        @endphp

        <div x-show="openTambahPengumuman" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="openTambahPengumuman" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="openTambahPengumuman = false"></div>
                <form action="{{ route('pengumuman.store') }}" method="POST" enctype="multipart/form-data"
                    x-show="openTambahPengumuman" x-transition
                    class="relative bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-6 w-full max-w-lg shadow-xl text-xs">
                    @csrf
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Buat Pengumuman Baru</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Judul *</label>
                            <input type="text" name="judul" required placeholder="Judul pengumuman..." class="{{ $fieldClass }}">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Isi *</label>
                            <textarea name="isi" rows="4" required placeholder="Tuliskan isi pengumuman secara detail..." class="{{ $fieldClass }} min-h-[96px] resize-y"></textarea>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status *</label>
                                <select name="status" x-model="selectedPengumuman.status" class="{{ $fieldClass }}">
                                    <option value="draft">Draft (Jadwalkan rilis)</option>
                                    <option value="published">Publish (Langsung sekarang)</option>
                                </select>
                            </div>
                            <div x-show="selectedPengumuman.status === 'draft'" x-cloak>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Rilis *</label>
                                <input type="datetime-local" name="tanggal_publish" :min="minPublishAt" x-model="selectedPengumuman.tanggal_publish" required class="{{ $fieldClass }}">
                                <p class="text-[11px] text-slate-400 mt-1">Minimal waktu sekarang. Otomatis publish saat jadwal tiba.</p>
                            </div>
                            <div x-show="selectedPengumuman.status === 'published'" x-cloak>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Publish</label>
                                <div class="{{ $fieldClass }} bg-slate-100 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 cursor-not-allowed">
                                    Otomatis: waktu simpan (now)
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Lampiran PDF</label>
                            <input type="file" name="lampiran" accept="application/pdf" class="{{ $fileClass }}">
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" @click="openTambahPengumuman = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl transition">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl shadow-sm transition">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openEditPengumuman" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="openEditPengumuman" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="openEditPengumuman = false"></div>
                <form :action="'{{ url('pengumuman') }}/' + selectedPengumuman.id" method="POST" enctype="multipart/form-data"
                    x-show="openEditPengumuman" x-transition
                    class="relative bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-6 w-full max-w-lg shadow-xl text-xs">
                    @csrf @method('PUT')
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Edit Pengumuman</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Judul *</label>
                            <input type="text" name="judul" x-model="selectedPengumuman.judul" required class="{{ $fieldClass }}">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Isi *</label>
                            <textarea name="isi" rows="4" x-model="selectedPengumuman.isi" required class="{{ $fieldClass }} min-h-[96px] resize-y"></textarea>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status *</label>
                                <select name="status" x-model="selectedPengumuman.status" class="{{ $fieldClass }}">
                                    <option value="published">Publish (Langsung sekarang)</option>
                                    <option value="draft">Draft (Jadwalkan rilis)</option>
                                </select>
                            </div>
                            <div x-show="selectedPengumuman.status === 'draft'" x-cloak>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Rilis *</label>
                                <input type="datetime-local" name="tanggal_publish" :min="minPublishAt" x-model="selectedPengumuman.tanggal_publish" class="{{ $fieldClass }}">
                                <p class="text-[11px] text-slate-400 mt-1">Minimal waktu sekarang. Otomatis publish saat jadwal tiba.</p>
                            </div>
                            <div x-show="selectedPengumuman.status === 'published'" x-cloak>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Publish</label>
                                <div class="{{ $fieldClass }} bg-slate-100 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 cursor-not-allowed">
                                    Otomatis: waktu simpan (now)
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Ganti Lampiran</label>
                            <input type="file" name="lampiran" accept="application/pdf" class="{{ $fileClass }}">
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" @click="openEditPengumuman = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl transition">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl shadow-sm transition">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openDeletePengumuman" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="openDeletePengumuman" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="openDeletePengumuman = false"></div>
                <form :action="'{{ url('pengumuman') }}/' + selectedPengumuman.id" method="POST"
                    x-show="openDeletePengumuman" x-transition
                    class="relative bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-6 w-full max-w-sm shadow-xl text-xs">
                    @csrf @method('DELETE')
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">Hapus Pengumuman?</h3>
                    <p class="text-slate-500 dark:text-slate-400 mb-6">Hapus <strong class="text-slate-900 dark:text-white" x-text="selectedPengumuman.judul"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="openDeletePengumuman = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl transition">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow-sm transition">Ya, Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
