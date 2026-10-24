<x-app-layout>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
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
            dikumpulkan_at: '',
            is_late: false // <--- Tambahkan ini
        },
        isSavingReview: false
    }" class="max-w-7xl mx-auto font-poppins space-y-6">

        {{-- Toast Alert / Flash Message --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-2xl text-emerald-700 dark:text-emerald-300 text-xs font-semibold flex items-center justify-between shadow-sm">
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

        {{-- Filters & Search (Responsive Layout) --}}
        <form method="GET" action="{{ route('dashboard.pengumpulan-tugas.index') }}"
            class="flex flex-col lg:flex-row items-stretch lg:items-center gap-3 bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm">
            @if ($tugasId > 0)
                <input type="hidden" name="tugas_id" value="{{ $tugasId }}">
            @endif

            <div class="flex-1 w-full">
                <div class="relative">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="search" name="search" value="{{ $search }}"
                        placeholder="Cari tugas / pengumpul..." class="form-control-app w-full pl-10!">
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                <div class="flex items-center gap-1.5 flex-1 sm:flex-initial">
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-300 shrink-0">Status:</label>
                    <select name="status" class="form-control-app w-full sm:w-auto text-xs">
                        <option value="" @selected(!in_array($status, ['pending', 'reviewed', 'rejected'], true))>Semua</option>
                        <option value="pending" @selected($status === 'pending')>Pending</option>
                        <option value="reviewed" @selected($status === 'reviewed')>Reviewed</option>
                        <option value="rejected" @selected($status === 'rejected')>Rejected</option>
                    </select>
                </div>

                <div class="flex items-center gap-1.5 flex-1 sm:flex-initial">
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-300 shrink-0">Jenis:</label>
                    <select name="jenis" class="form-control-app w-full sm:w-auto text-xs">
                        <option value="" @selected(!in_array($jenis, ['individu', 'tim', 'angkatan'], true))>Semua</option>
                        <option value="individu" @selected($jenis === 'individu')>Individu</option>
                        <option value="tim" @selected($jenis === 'tim')>Tim</option>
                        <option value="angkatan" @selected($jenis === 'angkatan')>Angkatan</option>
                    </select>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto justify-end mt-1 sm:mt-0">
                    <button type="submit" class="btn-filter px-4 py-2 text-xs">Cari</button>
                    @if (
                        $search !== '' ||
                            in_array($status, ['pending', 'reviewed', 'rejected'], true) ||
                            in_array($jenis, ['individu', 'tim', 'angkatan'], true))
                        <a href="{{ route('dashboard.pengumpulan-tugas.index', $tugasId > 0 ? ['tugas_id' => $tugasId] : []) }}"
                            class="text-xs text-slate-500 hover:text-slate-700 dark:text-slate-400 whitespace-nowrap">Reset</a>
                    @endif
                </div>
            </div>
        </form>

        {{-- Table List Submisi --}}
        <div class="table-card overflow-hidden">
            <div class="overflow-x-auto">
                <table
                    class="w-full text-left text-xs text-slate-600 dark:text-slate-300 divide-y divide-gray-100 dark:divide-slate-700">
                    <thead
                        class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 uppercase text-[11px] font-semibold tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4 rounded-l-xl">Kode Tugas</th>
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
                                <td>
                                    <div class="font-bold text-slate-900 dark:text-white text-sm text-center">
                                        {{ $submisi['kode_tugas'] }}
                                    </div>
                                </td>
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
                                        {{ $submisi['perwakilan_label'] }} - {{ $submisi['asal_tim'] }}
                                    </div>
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <div class="font-semibold text-slate-800 dark:text-slate-200">
                                        {{ \Carbon\Carbon::parse($submisi['dikumpulkan_at'])->translatedFormat('d M Y, H:i') }}
                                        WIB
                                    </div>
                                    @if ($submisi['is_late'])
                                        <span
                                            class="text-[10px] text-red-600 dark:text-red-400 font-bold flex items-center gap-1 mt-0.5">
                                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            Terlambat Mengerjakan
                                        </span>
                                    @else
                                        <span
                                            class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1 mt-0.5">
                                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
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
                                <td colspan="5" class="text-center py-8 text-slate-400">
                                    Belum ada submisi pengumpulan tugas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            {{ $submisiList->links('pagination::tailwind') }}
        </div>

        {{-- MODAL REVIEW PENGUMPULAN TUGAS --}}
      <div x-show="openReviewModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto px-3" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen py-4">
        
        {{-- Backdrop --}}
        <div x-show="openReviewModal" x-transition.opacity class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm" @click="openReviewModal = false"></div>

        <form :action="'{{ url('dashboard/pengumpulan-tugas') }}/' + selectedSubmisi.id" method="POST"
            @submit="isSavingReview = true" x-show="openReviewModal" x-transition
            class="relative bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 sm:p-5 w-full max-w-lg shadow-xl text-xs flex flex-col max-h-[90vh] my-auto overflow-hidden z-10">
            @csrf
            @method('PUT')

            {{-- Header --}}
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 shrink-0 gap-2">
                <div>
                    <span class="text-[10px] font-bold text-[#FF5B00] uppercase tracking-wider block" x-text="selectedSubmisi.jenis_tugas"></span>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white mt-0.5" x-text="selectedSubmisi.kode_tugas + ' - ' + selectedSubmisi.judul_tugas"></h3>
                </div>
                <button type="button" @click="openReviewModal = false" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 text-xl font-bold p-1">&times;</button>
            </div>

            {{-- Body --}}
            <div class="overflow-y-auto py-3 space-y-3 pr-1 -mr-1 text-xs flex-1">

                {{-- Info Ringkas & Waktu --}}
                <div class="flex flex-col sm:flex-row justify-between gap-2 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800">
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">Pengumpul</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200" x-text="selectedSubmisi.nama_pengumpul + ' (' + selectedSubmisi.asal_tim + ')'"></span>
                    </div>
                    <div class="sm:text-right">
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">Waktu</span>
                        <span class="font-semibold text-slate-700 dark:text-slate-300" x-text="selectedSubmisi.dikumpulkan_at ? new Date(selectedSubmisi.dikumpulkan_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' }) : ''"></span>
                        <div>
                            <span class="inline-block text-[10px] font-bold mt-1" :class="selectedSubmisi.is_late ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400'" x-text="selectedSubmisi.is_late ? '⚠️ Terlambat' : '✓ Tepat Waktu'"></span>
                        </div>
                    </div>
                </div>

                {{-- Catatan Peserta (Hanya tampil jika ada) --}}
                <template x-if="selectedSubmisi.catatan_peserta">
                    <div>
                        <span class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Catatan Peserta:</span>
                        <div class="p-2.5 bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-200/60 dark:border-slate-800 text-slate-600 dark:text-slate-300 whitespace-pre-line max-h-24 overflow-y-auto"
                            x-text="selectedSubmisi.catatan_peserta"></div>
                    </div>
                </template>

                {{-- Link Berkas --}}
                <div>
                    <a :href="selectedSubmisi.tautan_berkas" target="_blank"
                        class="inline-flex items-center justify-center gap-2 w-full py-2 px-3 bg-orange-50 hover:bg-orange-100 dark:bg-orange-950/30 dark:hover:bg-orange-950/50 text-[#FF5B00] font-bold rounded-xl border border-orange-200 dark:border-orange-900/50 transition">
                        🔗 Buka Berkas Tugas di Tab Baru
                    </a>
                </div>

                {{-- Status Penilaian (Radio) --}}
                <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">Status Penilaian <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition"
                            :class="selectedSubmisi.status === 'reviewed' ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/30 font-bold' : 'border-slate-200 dark:border-slate-800'">
                            <input type="radio" name="status" value="reviewed" x-model="selectedSubmisi.status" class="text-emerald-600 focus:ring-emerald-500">
                            <span class="text-slate-800 dark:text-slate-200">Reviewed (Lulus)</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition"
                            :class="selectedSubmisi.status === 'rejected' ? 'border-red-500 bg-red-50/50 dark:bg-red-950/30 font-bold' : 'border-slate-200 dark:border-slate-800'">
                            <input type="radio" name="status" value="rejected" x-model="selectedSubmisi.status" class="text-red-600 focus:ring-red-500">
                            <span class="text-slate-800 dark:text-slate-200">Rejected (Revisi)</span>
                        </label>
                    </div>
                </div>

                {{-- Catatan Evaluasi Pembimbing --}}
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan Evaluasi (Anonim)</label>
                    <textarea name="catatan_pemeriksa" rows="2" x-model="selectedSubmisi.catatan_pemeriksa"
                        placeholder="Tuliskan catatan atau instruksi perbaikan..."
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 focus:border-[#FF5B00] focus:ring-1 focus:ring-[#FF5B00] p-2.5 resize-y transition"></textarea>
                </div>
            </div>

            {{-- Footer Buttons --}}
            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2 shrink-0">
                <button type="button" @click="openReviewModal = false" :disabled="isSavingReview"
                    class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl transition">
                    Batal
                </button>
                <button type="submit" :disabled="isSavingReview"
                    class="px-4 py-2 bg-[#FF5B00] hover:bg-[#e05000] text-white font-bold rounded-xl transition shadow-sm inline-flex items-center gap-1.5 disabled:opacity-50">
                    <template x-if="isSavingReview">
                        <svg class="animate-spin w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </template>
                    <span x-text="isSavingReview ? 'Menyimpan...' : 'Simpan'"></span>
                </button>
            </div>
        </form>
    </div>
</div>

    </div>
</x-app-layout>
