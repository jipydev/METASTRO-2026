<x-app-layout :$title>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        @can('manage-tugas')
            <div>
            <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight font-oswald tracking-tight">
                {{ __('Manajemen Tugas') }}
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Kelola penugasan peserta Metastro 2026 (Individu, Tim, & Angkatan)
            </p>
            </div>
        @endcan
        <div>
            <a href="{{ route('dashboard.tugas.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl shadow-sm transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Buat Tugas Baru</span>
            </a>
        </div>
    </div>

    {{-- Toast Alert & Loading Container --}}
    <div x-data="{
        openDetailModal: false,
        openDeleteModal: false,
        selectedTugas: { id: null, judul: '', deskripsi: '', jenis: '', tenggat_waktu: '', pembuat_nama: '' },
        isDeleting: false
    }" class="py-6 max-w-7xl mx-auto font-poppins">

        {{-- Session Alert / Toast --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                class="mb-5 p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-2xl text-emerald-700 dark:text-emerald-300 text-xs font-semibold flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="show = false"
                    class="text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-300">&times;</button>
            </div>
        @endif

        {{-- Filter & Search Bar --}}
        <form method="GET" action="{{ route('dashboard.tugas.index') }}" class="filter-bar">
            <div class="flex-1 min-w-60">
                <div class="relative">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="search" name="search" value="{{ $search }}" placeholder="Cari judul atau deskripsi penugasan..."
                        class="form-control-app w-full pl-10!">
                </div>
            </div>

            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Jenis Tugas:</label>
                <select name="jenis" class="form-control-app">
                    <option value="" @selected($jenis === null || $jenis === '')>Semua Jenis</option>
                    <option value="individu" @selected($jenis === 'individu')>Individu</option>
                    <option value="tim" @selected($jenis === 'tim')>Tim / Kelompok</option>
                    <option value="angkatan" @selected($jenis === 'angkatan')>Angkatan</option>
                </select>
            </div>
            <button type="submit" class="btn-filter">Cari</button>
            @if ($search !== '' || in_array($jenis, ['individu', 'tim', 'angkatan'], true))
                <a href="{{ route('dashboard.tugas.index') }}"
                    class="text-xs text-slate-500 hover:text-slate-700 dark:text-slate-400">
                    Reset
                </a>
            @endif
        </form>

        {{-- Tabel Daftar Tugas --}}
        <div class="table-card">
            <div class="overflow-x-auto">
                <table
                    class="w-full text-left text-xs text-slate-600 dark:text-slate-300 divide-y divide-gray-100 dark:divide-slate-700">
                    <thead
                        class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 uppercase text-[11px] font-semibold tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4 rounded-l-xl">Judul & Deskripsi Tugas</th>
                            <th class="py-3.5 px-4 text-center">Jenis</th>
                            <th class="py-3.5 px-4">Tenggat Waktu</th>
                            <th class="py-3.5 px-4">Pembuat</th>
                            <th class="py-3.5 px-4 text-center rounded-r-xl">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        @forelse ($tugases as $tugas)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/30 transition">
                                <td class="py-4 px-4">
                                    <div class="font-bold text-slate-900 dark:text-white text-sm">
                                        {{ $tugas['judul'] }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-1 line-clamp-1 max-w-md">
                                        {{ strip_tags($tugas['deskripsi']) }}
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    @if ($tugas['jenis'] === 'individu')
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-sky-100 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300">
                                            Individu
                                        </span>
                                    @elseif ($tugas['jenis'] === 'tim')
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300">
                                            Tim (Perwakilan)
                                        </span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300">
                                            Angkatan
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <div class="font-semibold text-slate-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($tugas['tenggat_waktu'])->translatedFormat('d M Y') }}
                                    </div>
                                    <div
                                        class="text-[11px] text-red-600 dark:text-red-400 font-bold flex items-center gap-1 mt-0.5">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ \Carbon\Carbon::parse($tugas['tenggat_waktu'])->format('H:i') }}
                                            WIB</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap text-slate-600 dark:text-slate-400">
                                    {{ $tugas['pembuat_nama'] ?? 'Divisi Acara' }}
                                </td>
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button"
                                            @click="selectedTugas = @js($tugas); openDetailModal = true;"
                                            class="cursor-pointer px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-lg transition text-xs">
                                            Detail
                                        </button>
                                        @can('manage-tugas')
                                            <a href="{{ route('dashboard.tugas.edit', $tugas['id']) }}"
                                            class="cursor-pointer px-2.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-lg transition text-xs">
                                            Edit
                                            </a>
                                        @endcan
                                        @can('review-tugas')
                                            <a href="{{ route('dashboard.pengumpulan-tugas.index', ['tugas_id' => $tugas['id']]) }}"
                                            class="cursor-pointer px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition text-xs">
                                            Review
                                            </a>
                                        @endcan
                                        @can('manage-tugas')
                                            <button type="button"
                                            @click="selectedTugas = @js($tugas); openDeleteModal = true;"
                                            class="cursor-pointer px-2.5 py-1.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition text-xs">
                                            Hapus
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-8 text-slate-400">Belum ada data tugas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="px-4 py-3">
                                {{ $tugases->links('pagination::tailwind') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- MODAL DETAIL TUGAS --}}
        <div x-show="openDetailModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="openDetailModal" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm"
                    @click="openDetailModal = false"></div>

                <div x-show="openDetailModal" x-transition
                    class="relative bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-6 w-full max-w-lg shadow-xl text-xs">
                    <div
                        class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white" x-text="selectedTugas.judul">
                        </h3>
                        <button type="button" @click="openDetailModal = false"
                            class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                    </div>

                    <div class="py-4 space-y-3">
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Kategori
                                Jenis</span>
                            <span
                                class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-brand-100 text-brand-700 dark:bg-brand-950/60 dark:text-brand-300"
                                x-text="selectedTugas.jenis"></span>
                        </div>

                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Instruksi
                                & Deskripsi Penugasan</span>
                            <div class="rich-text-content mt-1 text-slate-700 dark:text-slate-200 leading-relaxed prose prose-sm dark:prose-invert max-w-none"
                                x-html="selectedTugas.deskripsi"></div>
                        </div>

                        <div class="pt-2">
                            <div class="p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                                <span class="text-[10px] text-red-600 dark:text-red-400 block font-bold uppercase">Tenggat
                                    Waktu</span>
                                <span class="text-xs font-bold text-red-700 dark:text-red-300"
                                    x-text="selectedTugas.tenggat_waktu"></span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-700">
                        <button type="button" @click="openDetailModal = false"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl transition">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL HAPUS TUGAS --}}
        <div x-show="openDeleteModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="openDeleteModal" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm"
                    @click="openDeleteModal = false"></div>

                <form :action="'{{ url('dashboard/tugas') }}/' + selectedTugas.id" method="POST" @submit="isDeleting = true"
                    x-show="openDeleteModal" x-transition
                    class="relative bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-6 w-full max-w-sm shadow-xl text-xs">
                    @csrf
                    @method('DELETE')

                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">Hapus Tugas Ini?</h3>
                    <p class="text-slate-500 dark:text-slate-400 mb-6">
                        Apakah Anda yakin ingin menghapus data master tugas <strong
                            class="text-slate-900 dark:text-white" x-text="selectedTugas.judul"></strong>? Seluruh
                        data submisi peserta terkait akan ikut terhapus.
                    </p>

                    <div class="flex justify-end gap-2">
                        <button type="button" @click="openDeleteModal = false" :disabled="isDeleting"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl transition disabled:opacity-50">
                            Batal
                        </button>
                        <button type="submit" :disabled="isDeleting"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow-sm transition inline-flex items-center gap-2 disabled:opacity-50 cursor-pointer">
                            <template x-if="isDeleting">
                                <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </template>
                            <span x-text="isDeleting ? 'Menghapus...' : 'Ya, Hapus'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
