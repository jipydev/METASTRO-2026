<x-app-layout :$title>

    <div x-data="{
        openCreateModal: false,
        openEditModal: false,
        openDeleteModal: false,
        selectedTim: { id: null, slug: '', nama: '' },
        isSaving: false,
        isDeleting: false
    }">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight font-oswald tracking-tight">
                    {{ __('Manajemen Tim & Guider') }}
                </h2>
            </div>
            <div>
                <button type="button" @click="openCreateModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl shadow-sm transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Tim Baru</span>
                </button>
            </div>
        </div>

        <div class="py-6 max-w-7xl mx-auto px-0 font-poppins">

            {{-- Toast Alert --}}
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
            @elseif (session('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                    class="mb-5 p-4 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 rounded-2xl text-red-700 dark:text-red-300 text-xs font-semibold flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400 shrink-0" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" @click="show = false"
                        class="text-red-500 hover:text-red-700 dark:hover:text-red-300">&times;</button>
                </div>
            @endif

            {{-- Top Summary Stats --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6 items-start">
                {{-- Search Bar --}}
                <form method="GET" action="{{ route('dashboard.tim.index') }}" class="filter-bar col-span-2 flex-nowrap!">
                    <div class="flex-1">
                        <div class="relative">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="search" name="search" value="{{ $search }}"
                                placeholder="Cari nama kelompok / tim bimbingan..."
                                class="form-control-app w-full pl-10!">
                        </div>
                    </div>
                    <button type="submit" class="btn-filter">Cari</button>
                    @if ($search !== '')
                        <a href="{{ route('dashboard.tim.index') }}"
                            class="text-xs text-slate-500 hover:text-slate-700 dark:text-slate-400">
                            Reset
                        </a>
                    @endif
                </form>
                <div
                    class="bg-white dark:bg-slate-800 px-4 py-2 rounded-2xl border border-slate-200/80 dark:border-slate-700 shadow-sm flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-brand-100 dark:bg-brand-950/60 text-brand-600 dark:text-brand-400 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-400">Total Tim Aktif</span>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $jumlahTim }} Tim</h3>
                    </div>
                </div>
            </div>


            {{-- Table Card --}}
            <div class="table-card">
                <div class="overflow-x-auto">
                    <table
                        class="w-full text-left text-xs text-slate-600 dark:text-slate-300 divide-y divide-gray-100 dark:divide-slate-700">
                        <thead
                            class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 uppercase text-[11px] font-semibold tracking-wider">
                            <tr>
                                <th class="py-3.5 px-4 rounded-l-xl">Nama Tim</th>
                                <th class="py-3.5 px-4">Pasangan Guider</th>
                                <th class="py-3.5 px-4 text-center">Jumlah Anggota</th>
                                <th class="py-3.5 px-4 text-center rounded-r-xl">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @forelse ($tims as $tim)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/30 transition">
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <div class="font-bold text-slate-900 dark:text-white text-sm">
                                            {{ $tim['nama'] }}
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            @php
                                                $guiders = $tim->guiders ?? [];
                                            @endphp

                                            @if (count($guiders) === 1)
                                                <span
                                                    class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300">
                                                    Kurang 1 Guider Lagi
                                                </span>
                                            @elseif (count($guiders) === 2)
                                                <span
                                                    class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300">
                                                    Lengkap (2/2)
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-center whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-200">
                                            {{ count($tim['members'] ?? []) }} Peserta
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <a href="{{ route('dashboard.tim.show', $tim->slug) }}"
                                                class="px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition text-xs flex items-center gap-1 shadow-sm">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                                </svg>
                                                <span>Detail</span>
                                            </a>

                                            <button type="button"
                                                @click="selectedTim = @js(['id' => $tim['id'], 'slug' => $tim['slug'], 'nama' => $tim['nama']]); openEditModal = true;"
                                                class="cursor-pointer px-2.5 py-1.5 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-lg transition text-xs">
                                                Ubah Nama
                                            </button>

                                            <button type="button"
                                                @click="selectedTim = @js(['id' => $tim['id'], 'slug' => $tim['slug'], 'nama' => $tim['nama']]); openDeleteModal = true;"
                                                class="px-2.5 py-1.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition text-xs">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-8 text-slate-400">Belum ada data
                                        kelompok
                                        bimbingan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="px-4 py-3">
                                    {{ $tims->links('pagination::tailwind') }}
                                </td>
                            </tr>
                    </table>
                </div>
            </div>

            {{-- MODAL TAMBAH TIM --}}
            <div x-show="openCreateModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
                role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div x-show="openCreateModal" x-transition.opacity
                        class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="openCreateModal = false"></div>

                    <form action="{{ route('dashboard.tim.store') }}" method="POST" @submit="isSaving = true"
                        x-show="openCreateModal" x-transition
                        class="relative bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-6 w-full max-w-md shadow-xl text-xs">
                        @csrf
                        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Buat Tim Baru
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Nama Tim <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nama" required maxlength="100"
                                    placeholder="Contoh: Tim Andromeda 03" class="form-control-app w-full">
                                @error('nama')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-2">
                            <button type="button" @click="openCreateModal = false" :disabled="isSaving"
                                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit" :disabled="isSaving"
                                class="btn-primary inline-flex items-center gap-2 disabled:opacity-50">
                                <template x-if="isSaving">
                                    <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                </template>
                                <span x-text="isSaving ? 'Menyimpan...' : 'Simpan Tim'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- MODAL EDIT NAMA TIM --}}
            <div x-show="openEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
                role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div x-show="openEditModal" x-transition.opacity
                        class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="openEditModal = false"></div>

                    <form :action="'{{ url('dashboard/tim') }}/' + selectedTim.slug" method="POST"
                        @submit="isSaving = true" x-show="openEditModal" x-transition
                        class="relative bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-6 w-full max-w-md shadow-xl text-xs">
                        @csrf
                        @method('PUT')
                        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Ubah Nama Tim</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Nama Tim <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nama" x-model="selectedTim.nama" required
                                    maxlength="100" class="form-control-app w-full">
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-2">
                            <button type="button" @click="openEditModal = false" :disabled="isSaving"
                                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit" :disabled="isSaving"
                                class="btn-primary inline-flex items-center gap-2 disabled:opacity-50">
                                <template x-if="isSaving">
                                    <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                </template>
                                <span x-text="isSaving ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- MODAL HAPUS TIM --}}
            <div x-show="openDeleteModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
                role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div x-show="openDeleteModal" x-transition.opacity
                        class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="openDeleteModal = false"></div>

                    <form :action="'{{ url('dashboard/tim') }}/' + selectedTim.slug" method="POST"
                        @submit="isDeleting = true" x-show="openDeleteModal" x-transition
                        class="relative bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-6 w-full max-w-sm shadow-xl text-xs">
                        @csrf
                        @method('DELETE')
                        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">Hapus Kelompok Tim?
                        </h3>
                        <p class="text-slate-500 dark:text-slate-400 mb-6">
                            Apakah Anda yakin ingin menghapus tim <strong class="text-slate-900 dark:text-white"
                                x-text="selectedTim.nama"></strong>? Pemasangan guider dan anggota
                            tim
                            ini
                            akan dibatalkan.
                        </p>

                        <div class="flex justify-end gap-2">
                            <button type="button" @click="openDeleteModal = false" :disabled="isDeleting"
                                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit" :disabled="isDeleting"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow-sm transition inline-flex items-center gap-2 disabled:opacity-50">
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
    </div>
</x-app-layout>
