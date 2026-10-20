<x-app-layout>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight font-oswald tracking-tight">
                {{ __('Review Pengumpulan Tugas') }}
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Evaluasi hasil pengerjaan penugasan peserta & berikan masukan pembimbing secara anonim
            </p>
        </div>
    </div>

    <div x-data="{
        openReviewModal: false,
        selectedSubmisi: {
            id: null,
            judul_tugas: '',
            jenis_tugas: '',
            nama_pengumpul: '',
            perwakilan_label: '',
            nim_pengumpul: '',
            asal_tim: '',
            tautan_berkas: '',
            catatan_peserta: '',
            catatan_pemeriksa: '',
            status: 'pending',
            dikumpulkan_at: ''
        },
        isSavingReview: false
    }" class="py-6 max-w-7xl mx-auto font-poppins">

        {{-- Toast Alert / Flash Message --}}
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

        {{-- Filters & Search --}}
        <form method="GET" action="{{ route('dashboard.pengumpulan-tugas.index') }}" class="filter-bar">
            @if ($tugasId > 0)
                <input type="hidden" name="tugas_id" value="{{ $tugasId }}">
            @endif
            <div class="flex-1 min-w-60">
                <div class="relative">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="search" name="search" value="{{ $search }}"
                        placeholder="Cari nama tugas atau nama pengumpul..." class="form-control-app w-full pl-10!">
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <div class="flex items-center gap-1.5">
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Status:</label>
                    <select name="status" class="form-control-app">
                        <option value="" @selected(!in_array($status, ['pending', 'reviewed', 'rejected'], true))>Semua Status</option>
                        <option value="pending" @selected($status === 'pending')>Pending (Belum Direview)</option>
                        <option value="reviewed" @selected($status === 'reviewed')>Reviewed (Telah Direview)</option>
                        <option value="rejected" @selected($status === 'rejected')>Rejected (Butuh Revisi)</option>
                    </select>
                </div>

                <div class="flex items-center gap-1.5">
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Jenis:</label>
                    <select name="jenis" class="form-control-app">
                        <option value="" @selected(!in_array($jenis, ['individu', 'tim', 'angkatan'], true))>Semua Jenis</option>
                        <option value="individu" @selected($jenis === 'individu')>Individu</option>
                        <option value="tim" @selected($jenis === 'tim')>Tim (Perwakilan)</option>
                        <option value="angkatan" @selected($jenis === 'angkatan')>Angkatan (Perwakilan)</option>
                    </select>
                    <button type="submit" class="btn-filter">Cari</button>
                    @if (
                        $search !== '' ||
                            in_array($status, ['pending', 'reviewed', 'rejected'], true) ||
                            in_array($jenis, ['individu', 'tim', 'angkatan'], true))
                        <a href="{{ route('dashboard.pengumpulan-tugas.index', $tugasId > 0 ? ['tugas_id' => $tugasId] : []) }}"
                            class="text-xs text-slate-500 hover:text-slate-700 dark:text-slate-400">Reset</a>
                    @endif
                </div>
        </form>

        {{-- Table List Submisi --}}
        <div class="table-card">
            <div class="overflow-x-auto">
                <table
                    class="w-full text-left text-xs text-slate-600 dark:text-slate-300 divide-y divide-gray-100 dark:divide-slate-700">
                    <thead
                        class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 uppercase text-[11px] font-semibold tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4 rounded-l-xl">Tugas & Jenis</th>
                            <th class="py-3.5 px-4">Pengumpul & Perwakilan</th>
                            <th class="py-3.5 px-4">Waktu Dikumpulkan</th>
                            <th class="py-3.5 px-4 text-center">Status Review</th>
                            <th class="py-3.5 px-4 text-center rounded-r-xl">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        @forelse ($submisiList as $submisi)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/30 transition">
                                <td class="py-4 px-4">
                                    <div class="font-bold text-slate-900 dark:text-white text-sm">
                                        {{ $submisi['judul_tugas'] }}
                                    </div>
                                    <div class="mt-1">
                                        @if ($submisi['jenis_tugas'] === 'individu')
                                            <span
                                                class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-sky-100 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300">
                                                Individu
                                            </span>
                                        @elseif ($submisi['jenis_tugas'] === 'tim')
                                            <span
                                                class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300">
                                                Tugas Tim
                                            </span>
                                        @else
                                            <span
                                                class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300">
                                                Tugas Angkatan
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-bold text-slate-900 dark:text-white">
                                        {{ $submisi['nama_pengumpul'] }}
                                    </div>
                                    <div class="text-[11px] text-slate-400">
                                        {{ $submisi['perwakilan_label'] }} • {{ $submisi['asal_tim'] }}
                                    </div>
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap">
    <div class="font-semibold text-slate-800 dark:text-slate-200">
        {{ \Carbon\Carbon::parse($submisi['dikumpulkan_at'])->translatedFormat('d M Y, H:i') }}
        WIB
    </div>
    @if ($submisi['is_late'])
        <span class="text-[10px] text-red-600 dark:text-red-400 font-bold flex items-center gap-1 mt-0.5">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Terlambat Mengerjakan
        </span>
    @else
        <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1 mt-0.5">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 13l4 4L19 7" />
            </svg>
            Tepat Waktu
        </span>
    @endif
</td>
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    @if ($submisi['status'] === 'pending')
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300">
                                            Pending
                                        </span>
                                    @elseif ($submisi['status'] === 'reviewed')
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300">
                                            Reviewed
                                        </span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-red-300">
                                            Rejected
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    <button type="button"
                                        @click="selectedSubmisi = @js($submisi); openReviewModal = true;"
                                        class="px-3.5 py-1.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-lg transition text-xs shadow-sm cursor-pointer">
                                        Tinjau & Review
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-8 text-slate-400">Belum ada submisi
                                    pengumpulan
                                    tugas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $submisiList->links('pagination::tailwind') }}
        </div>

        {{-- MODAL REVIEW PENGUMPULAN TUGAS --}}
        <div x-show="openReviewModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="openReviewModal" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm"
                    @click="openReviewModal = false"></div>

                <form :action="'{{ url('dashboard/pengumpulan-tugas') }}/' + selectedSubmisi.id" method="POST"
                    @submit="isSavingReview = true" x-show="openReviewModal" x-transition
                    class="relative bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl border border-slate-100 dark:border-slate-700 p-4 sm:p-8 w-full max-w-2xl shadow-xl text-xs flex flex-col max-h-[90vh] my-auto overflow-hidden">
                    @csrf
                    @method('PUT')

                    {{-- Header Modal (Fixed) --}}
                    <div
                        class="flex items-start justify-between pb-3 border-b border-slate-100 dark:border-slate-700 shrink-0 gap-3">
                        <div class="min-w-0 flex-1">
                            <span class="text-[10px] font-bold text-brand-600 uppercase tracking-wider block truncate"
                                x-text="selectedSubmisi.jenis_tugas"></span>
                            <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white leading-snug break-words"
                                x-text="selectedSubmisi.judul_tugas"></h3>
                        </div>
                        <button type="button" @click="openReviewModal = false"
                            class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-2xl leading-none p-1 -mr-1 -mt-1 shrink-0">&times;</button>
                    </div>

                    {{-- Scrollable Form Body --}}
                    <div class="overflow-y-auto py-4 space-y-4 pr-1 -mr-1 custom-scrollbar flex-1">

                        {{-- Submitter Info & File Link --}}
                        <div
                            class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3.5 sm:p-4 bg-slate-50 dark:bg-slate-700/40 rounded-2xl border border-slate-100 dark:border-slate-700">
                            <div class="min-w-0">
                                <span class="text-[10px] font-bold uppercase text-slate-400 block">Pengumpul /
                                    Perwakilan</span>
                                <span class="font-bold text-slate-900 dark:text-white text-xs block mt-0.5 truncate"
                                    x-text="selectedSubmisi.nama_pengumpul"></span>
                                <span class="text-[11px] text-slate-500 block truncate"
                                    x-text="selectedSubmisi.perwakilan_label + ' (' + selectedSubmisi.asal_tim + ')'"></span>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[10px] font-bold uppercase text-slate-400 block">Waktu
                                    Pengumpulan</span>
                                <span
                                    class="font-semibold text-slate-800 dark:text-slate-200 text-xs block mt-0.5 truncate"
                                    x-text="selectedSubmisi.dikumpulkan_at ? new Date(selectedSubmisi.dikumpulkan_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' }) : ''">
                                </span>
                                <span
                                    class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 block mt-0.5">Tepat
                                    Waktu (Sebelum Deadline)</span>
                            </div>
                        </div>

                        {{-- Catatan Peserta --}}
                        <div>
                            <span class="text-[11px] font-bold text-slate-600 dark:text-slate-300 block mb-1">Catatan
                                dari Peserta:</span>
                            <div class="p-3 bg-slate-50 dark:bg-slate-700/30 rounded-xl border border-slate-200/60 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs leading-relaxed break-words max-h-32 overflow-y-auto"
                                x-text="selectedSubmisi.catatan_peserta || 'Tidak ada catatan khusus dari peserta.'">
                            </div>
                        </div>

                        {{-- Tautan Berkas --}}
                        <div>
                            <span class="text-[11px] font-bold text-slate-600 dark:text-slate-300 block mb-1">Tautan
                                Berkas Tugas:</span>
                            <a :href="selectedSubmisi.tautan_berkas" target="_blank"
                                class="inline-flex items-center justify-center sm:justify-start w-full sm:w-auto gap-2 px-3.5 py-2.5 bg-brand-50 hover:bg-brand-100 dark:bg-brand-950/40 dark:hover:bg-brand-950/60 text-brand-600 dark:text-brand-300 font-bold rounded-xl transition border border-brand-200/60 dark:border-brand-800/40 text-center">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                <span class="truncate">Buka Berkas Pengerjaan di Tab Baru</span>
                            </a>
                        </div>

                        {{-- Form Input Status Reviu --}}
                        <div class="pt-3 border-t border-slate-100 dark:border-slate-700">
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-2">
                                Pembaruan Status Kelayakan <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <label
                                    class="flex items-start gap-3 cursor-pointer p-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/40 transition"
                                    :class="selectedSubmisi.status === 'reviewed' ?
                                        'border-emerald-500 bg-emerald-50/40 dark:bg-emerald-950/30 ring-2 ring-emerald-500/20' :
                                        ''">
                                    <input type="radio" name="status" value="reviewed"
                                        x-model="selectedSubmisi.status"
                                        class="mt-0.5 text-brand-600 focus:ring-brand-500 shrink-0">
                                    <div class="min-w-0">
                                        <span class="font-bold text-slate-900 dark:text-white block text-xs">Reviewed
                                            (Lulus)</span>
                                        <p class="text-[10px] text-slate-400 mt-0.5 leading-tight">Pengerjaan telah
                                            memenuhi standar instruksi.</p>
                                    </div>
                                </label>

                                <label
                                    class="flex items-start gap-3 cursor-pointer p-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/40 transition"
                                    :class="selectedSubmisi.status === 'rejected' ?
                                        'border-red-500 bg-red-50/40 dark:bg-red-950/30 ring-2 ring-red-500/20' : ''">
                                    <input type="radio" name="status" value="rejected"
                                        x-model="selectedSubmisi.status"
                                        class="mt-0.5 text-red-600 focus:ring-red-500 shrink-0">
                                    <div class="min-w-0">
                                        <span class="font-bold text-slate-900 dark:text-white block text-xs">Rejected
                                            (Revisi)</span>
                                        <p class="text-[10px] text-slate-400 mt-0.5 leading-tight">Belum sesuai dan
                                            membutuhkan perbaikan lebih lanjut.</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Form Input Catatan Anonim --}}
                        <div>
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-1.5">
                                <label class="font-bold text-slate-700 dark:text-slate-300">
                                    Catatan / Komentar Evaluasi Pembimbing <span class="text-red-500">*</span>
                                </label>
                                <span
                                    class="text-[10px] font-bold text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-950/50 px-2 py-0.5 rounded-full w-fit">
                                    Komentar disampaikan secara anonim
                                </span>
                            </div>
                            <textarea name="catatan_pemeriksa" rows="3" required x-model="selectedSubmisi.catatan_pemeriksa"
                                placeholder="Tuliskan apresiasi, koreksi, atau instruksi perbaikan kepada peserta/kelompok..."
                                class="form-control-app w-full resize-y min-h-20 text-xs"></textarea>
                        </div>
                    </div>

                    {{-- Action Buttons (Fixed at Bottom) --}}
                    <div
                        class="pt-3 border-t border-slate-100 dark:border-slate-700 flex flex-col-reverse sm:flex-row justify-end gap-2 shrink-0">
                        <button type="button" @click="openReviewModal = false" :disabled="isSavingReview"
                            class="w-full sm:w-auto px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl transition text-center">
                            Batal
                        </button>
                        <button type="submit" :disabled="isSavingReview"
                            class="btn-primary w-full sm:w-auto inline-flex items-center justify-center gap-2 disabled:opacity-50 py-2.5">
                            <template x-if="isSavingReview">
                                <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </template>
                            <span x-text="isSavingReview ? 'Menyimpan...' : 'Simpan Status & Komentar'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
