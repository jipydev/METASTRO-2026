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
        openAddNotulensi: {{ $errors->has('lampiran') ? 'true' : 'false' }},
        openEditNotulensi: false,
        selectedNotulensi: { id: null, judul: '', isi: '', kegiatan_id: '', hasLampiran: false }
    }" class="page-shell">

        <div class="page-wrap">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="page-title">Notulensi Rapat</h1>
                    <p class="page-subtitle">Arsip lengkap hasil koordinasi dan keputusan kegiatan</p>
                </div>
                @if (auth()->user()->canManageSekretariat())
                    <button type="button" @click="openAddNotulensi = true" class="btn-primary">
                        + Tambah
                    </button>
                @endif
            </div>

            <form method="GET" action="{{ route('notulensi.index') }}" class="filter-bar">
                <div class="flex-1 min-w-0 w-full">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul atau isi..."
                        class="form-control-app w-full">
                </div>
                <div>
                    <select name="kegiatan_id" class="form-control-app">
                        <option value="">Semua Kegiatan</option>
                        @foreach ($kegiatanOptions as $kegiatan)
                            <option value="{{ $kegiatan->id }}" @selected((string) request('kegiatan_id') === (string) $kegiatan->id)>
                                {{ $kegiatan->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-filter">Filter</button>
                @if (request()->hasAny(['search', 'kegiatan_id']))
                    <a href="{{ route('notulensi.index') }}" class="text-xs text-slate-500 hover:text-slate-700 dark:text-slate-400">Reset</a>
                @endif
            </form>

            <div class="space-y-3">
                @forelse ($notulensis as $notulensi)
                    <article x-data="{ open: false }" class="relative bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm">
                        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-brand-600"></div>
                        <button type="button" @click="open = !open" class="w-full pl-5 pr-4 py-4 flex items-center gap-3 text-left">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">{{ $notulensi->judul }}</h2>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700 dark:bg-slate-800/60 dark:text-slate-300">
                                        {{ $notulensi->kegiatan?->nama ?? 'Umum' }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1">
                                    {{ $notulensi->created_at?->locale('id')->isoFormat('D MMMM Y, HH.mm') }} WIB
                                    • {{ $notulensi->pembuat?->nama ?? 'Admin' }}
                                    ({{ $notulensi->pembuat?->divisi?->nama ?? 'Umum' }})
                                </p>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 shrink-0 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" x-cloak class="pl-5 pr-4 pb-4">
                            @if ($notulensi->isi)
                                <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-300 whitespace-pre-line">{{ $notulensi->isi }}</p>
                            @else
                                <p class="text-sm text-slate-400">Tidak ada isi notulensi.</p>
                            @endif
                            <div class="mt-4 pt-3 border-t border-slate-200 dark:border-slate-700 flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    @if ($notulensi->lampiran)
                                        <a href="{{ asset('storage/' . $notulensi->lampiran) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 dark:text-slate-400">
                                            Lihat Lampiran PDF
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
                                            class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded-xl">Edit</button>
                                        <form action="{{ route('notulensi.destroy', $notulensi) }}" method="POST" onsubmit="return confirm('Hapus arsip notulensi ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-xl">Hapus</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="text-center py-16 text-xs text-slate-400">Belum ada notulensi.</div>
                @endforelse
            </div>

            @if ($notulensis->hasPages())
                <div class="mt-6">{{ $notulensis->links() }}</div>
            @endif
        </div>

        @php
            $fieldClass = 'w-full bg-slate-50 dark:bg-slate-700 rounded-xl border border-slate-300 dark:border-slate-600 p-2.5 text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500';
            $fileClass = 'w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer';
        @endphp

        <div x-show="openAddNotulensi" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
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
                            <select name="kegiatan_id" class="{{ $fieldClass }}">
                                <option value="">Umum (tidak terikat kegiatan)</option>
                                @foreach ($kegiatanOptions as $kegiatan)
                                    <option value="{{ $kegiatan->id }}">{{ $kegiatan->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Judul *</label>
                            <input type="text" name="judul" required maxlength="150" placeholder="Contoh: Notulensi Pleno Divisi" class="{{ $fieldClass }}">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Isi</label>
                            <textarea name="isi" rows="4" placeholder="Ringkasan hasil rapat, keputusan, dan tindak lanjut..." class="{{ $fieldClass }} min-h-[96px] resize-y"></textarea>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Lampiran PDF</label>
                            <input type="file" name="lampiran" accept="application/pdf" class="{{ $fileClass }}">
                            <span class="text-[10px] text-slate-400 block mt-1">Opsional. Maksimal 5MB. Isi atau PDF wajib diisi salah satu.</span>
                            @error('lampiran')<span class="text-red-600 text-[10px] block mt-1">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" @click="openAddNotulensi = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl transition">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl shadow-sm transition">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openEditNotulensi" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="openEditNotulensi" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="openEditNotulensi = false"></div>
                <form :action="'{{ url('notulensi') }}/' + selectedNotulensi.id" method="POST" enctype="multipart/form-data"
                    x-show="openEditNotulensi" x-transition
                    class="relative bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-6 w-full max-w-md shadow-xl text-xs">
                    @csrf @method('PUT')
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Edit Notulensi</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kegiatan</label>
                            <select name="kegiatan_id" x-model="selectedNotulensi.kegiatan_id" class="{{ $fieldClass }}">
                                <option value="">Umum (tidak terikat kegiatan)</option>
                                @foreach ($kegiatanOptions as $kegiatan)
                                    <option value="{{ $kegiatan->id }}">{{ $kegiatan->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Judul *</label>
                            <input type="text" name="judul" x-model="selectedNotulensi.judul" required maxlength="150" class="{{ $fieldClass }}">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Isi</label>
                            <textarea name="isi" rows="4" x-model="selectedNotulensi.isi" class="{{ $fieldClass }} min-h-[96px] resize-y"></textarea>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Ganti Lampiran PDF</label>
                            <input type="file" name="lampiran" accept="application/pdf" class="{{ $fileClass }}">
                            <span class="text-[10px] text-slate-400 block mt-1" x-show="selectedNotulensi.hasLampiran">Kosongkan jika ingin tetap memakai lampiran yang ada.</span>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" @click="openEditNotulensi = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl transition">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl shadow-sm transition">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
