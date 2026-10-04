<x-app-layout :$title>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <span
                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-brand-100 text-brand-600 dark:bg-brand-950/60 dark:text-brand-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 10a2 2 0 11-4 0 2 2 0 014 0zm14 0a2 2 0 11-4 0 2 2 0 014 0zM15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </span>
                <h2 class="font-oswald text-xl font-bold leading-tight tracking-tight text-slate-900 dark:text-white">
                    {{ $tim['nama'] }}
                </h2>
                <span
                    class="rounded-full bg-brand-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-brand-700 dark:bg-brand-950/50 dark:text-brand-300">
                    Detail Tim
                </span>
            </div>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Kelola guider dan anggota tim.</p>
        </div>
        <div>
            <a href="{{ route('dashboard.tim.index') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-bold text-slate-700 shadow-sm transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                <span aria-hidden="true">&larr;</span> Kembali
            </a>
        </div>
    </div>

    <div x-data="{
        openAddMemberModal: false,
        openAddGuiderModal: false,
        isProcessing: false
    }" class="mx-auto max-w-7xl space-y-6 py-6 font-poppins text-xs">

        {{-- Toast / Flash Notification --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                class="flex items-center justify-between rounded-2xl border border-emerald-200 bg-emerald-50 p-4 font-semibold text-emerald-700 shadow-sm dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300">
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

        {{-- BAGIAN 1: PASANGAN PEMBIMBING (TIM GUIDERS - 2 GUIDER SLOT) --}}
        <section
            class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800 sm:p-6">
            <div
                class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5 pb-4 border-b border-slate-100 dark:border-slate-700">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Pasangan Guider</span>
                    </h3>
                    <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5">
                        Setiap tim didampingi oleh pasangan 2 panitia Divisi Guider
                    </p>
                </div>

                <!-- Perbaikan: Hitung jumlah guider yang terdaftar pada tim, bukan isi $guiders dari controller -->
                @can('manage-tim')
                    @if (count($tim['guiders'] ?? []) < 2)
                        <button type="button" @click="openAddGuiderModal = true"
                        class="btn-primary flex items-center gap-1.5 self-start sm:self-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Tugaskan Guider</span>
                        </button>
                    @else
                        <span
                        class="px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Formasi Pasangan Lengkap (2/2)
                        </span>
                    @endif
                @endcan
            </div>


            {{-- Slot Grid Pasangan Guider --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @for ($i = 0; $i < 2; $i++)
                    @php($guider = $tim->guiders[$i] ?? null)
                    @if ($guider)
                        <div
                            class="p-4 bg-brand-50/60 dark:bg-slate-700/40 rounded-2xl border border-brand-200/80 dark:border-slate-600 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-brand-600 text-white font-bold flex items-center justify-center text-sm shadow-sm">
                                    G{{ $i + 1 }}
                                </div>
                                <div>
                                    <span
                                        class="text-[10px] font-bold text-brand-600 dark:text-brand-400 uppercase tracking-wider block">Guider
                                        {{ $i + 1 }}</span>
                                    <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $guider->pembimbing->nama }}
                                    </h4>
                                    <p class="text-[11px] text-slate-400">{{ $guider->pembimbing->email }}</p>
                                </div>
                            </div>

                            @can('manage-tim')
                                <form action="{{ route('dashboard.guider.update', $tim->id) }}" method="POST"
                                @submit="isProcessing = true">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="remove_guider">
                                <input type="hidden" name="guider_id" value="{{ $guider->id }}">
                                <button type="submit" :disabled="isProcessing"
                                    class="px-2.5 py-1.5 bg-red-100 hover:bg-red-200 dark:bg-red-950/60 text-red-600 dark:text-red-400 text-xs font-semibold rounded-xl transition flex items-center gap-1 cursor-pointer">
                                    <span>Copot</span>
                                </button>
                                </form>
                            @endcan
                        </div>
                    @else
                        <div
                            class="p-4 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-slate-200 dark:bg-slate-700 text-slate-400 font-bold flex items-center justify-center text-sm">
                                    G{{ $i + 1 }}
                                </div>
                                <div>
                                    <span
                                        class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Slot
                                        Guider {{ $i + 1 }}</span>
                                    <h4 class="font-semibold text-slate-400 italic">Belum Ditugaskan</h4>
                                </div>
                            </div>

                            @can('manage-tim')
                                <button type="button" @click="openAddGuiderModal = true"
                                class="px-3 py-1.5 bg-brand-50 hover:bg-brand-100 dark:bg-slate-700 text-brand-600 dark:text-brand-300 font-bold text-xs rounded-xl transition cursor-pointer">
                                + Pilih Guider
                                </button>
                            @endcan
                        </div>
                    @endif
                @endfor
            </div>
        </section>

        {{-- BAGIAN 2: ALOKASI ANGGOTA PESERTA (TIM MEMBERS - FLEKSIBEL TANPA LIMIT KUOTA) --}}
        <section
            class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800 sm:p-6">
            <div
                class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5 pb-4 border-b border-slate-100 dark:border-slate-700">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Daftar Anggota Tim ({{ count($tim->members) }} Orang)</span>
                    </h3>
                </div>

                @can('manage-tim')
                    <button type="button" @click="openAddMemberModal = true"
                    class="btn-primary flex items-center gap-1.5 self-start sm:self-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    <span>+ Tambah Anggota ke Tim</span>
                    </button>
                @endcan
            </div>

            {{-- Table Anggota --}}
            <div class="table-card">
                <div class="overflow-x-auto">
                    <table
                        class="w-full text-left text-xs text-slate-600 dark:text-slate-300 divide-y divide-gray-100 dark:divide-slate-700">
                        <thead
                            class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 uppercase text-[11px] font-semibold tracking-wider">
                            <tr>
                                <th class="py-3 px-4 rounded-l-xl">No</th>
                                <th class="py-3 px-4">Nama Mahasiswa</th>
                                <th class="py-3 px-4">NIM</th>
                                <th class="py-3 px-4 text-center">Jenis Kelamin</th>
                                <th class="py-3 px-4 text-center rounded-r-xl">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @forelse ($tim['members'] ?? [] as $index => $member)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/30 transition">
                                    <td class="py-3.5 px-4 font-bold text-slate-400">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white">
                                        {{ $member->nama }}
                                    </td>
                                    <td class="py-3.5 px-4 font-mono font-semibold text-slate-700 dark:text-slate-300">
                                        {{ $member->nim }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span
                                            class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold capitalize {{ $member->jenis_kelamin === 'laki-laki' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300' : 'bg-pink-100 text-pink-700 dark:bg-pink-950/60 dark:text-pink-300' }}">
                                            {{ $member->jenis_kelamin }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @can('manage-tim')
                                            <form action="{{ route('dashboard.anggota-tim.destroy', [$tim->id, $member->id]) }}" method="POST"
                                            @submit="isProcessing = true">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" :disabled="isProcessing"
                                                class="px-2.5 py-1.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition text-xs cursor-pointer">
                                                Keluarkan
                                            </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-8 text-slate-400">
                                        Belum ada mahasiswa yang dialokasikan ke dalam tim ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        {{-- MODAL ALOKASI GUIDER --}}
        <div x-show="openAddGuiderModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-center justify-center px-4 py-8 sm:px-6">
                <div x-show="openAddGuiderModal" x-transition.opacity
                    class="fixed inset-0 bg-slate-950/60 backdrop-blur-[3px]" @click="openAddGuiderModal = false">
                </div>

                <form action="{{ route('dashboard.guider.update', $tim->id) }}" method="POST"
                    @submit="isProcessing = true" x-show="openAddGuiderModal" x-transition
                    class="relative w-full max-w-lg overflow-hidden rounded-3xl border border-slate-200 bg-white text-xs shadow-2xl dark:border-slate-700 dark:bg-slate-800">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="action" value="add_guider">

                    <div class="border-b border-slate-100 px-6 py-5 dark:border-slate-700 sm:px-7">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Tugaskan Guider</h3>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Pilih guider untuk tim ini.</p>
                    </div>

                    <div class="space-y-2 px-6 py-6 sm:px-7">
                        <div>
                            <label for="pembimbing_id" class="block font-bold text-slate-700 dark:text-slate-200">
                                Pilih Panitia Guider <span class="text-red-500">*</span>
                            </label>
                            <div class="relative mt-2">
                                <select name="pembimbing_id" id="pembimbing_id" required class="team-select">
                                    <option value="" disabled selected>Pilih panitia guider...</option>
                                    @foreach ($guiders as $guider)
                                        <option value="{{ $guider->id }}">{{ $guider->nama }}
                                            ({{ $guider->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex flex-col-reverse gap-2 border-t border-slate-100 bg-slate-50/70 px-6 py-4 dark:border-slate-700 dark:bg-slate-900/20 sm:flex-row sm:justify-end sm:px-7">
                        <button type="button" @click="openAddGuiderModal = false" :disabled="isProcessing"
                            class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 font-bold text-slate-600 transition hover:bg-slate-100 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600">
                            Batal
                        </button>
                        <button type="submit" :disabled="isProcessing"
                            class="btn-primary inline-flex items-center gap-2 disabled:opacity-50">
                            <template x-if="isProcessing">
                                <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </template>
                            <span x-text="isProcessing ? 'Menugaskan...' : 'Tugaskan Guider'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL ALOKASI PESERTA --}}
        <div x-show="openAddMemberModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-center justify-center px-4 py-8 sm:px-6">
                <div x-show="openAddMemberModal" x-transition.opacity
                    class="fixed inset-0 bg-slate-950/60 backdrop-blur-[3px]" @click="openAddMemberModal = false">
                </div>

                <form action="{{ route('dashboard.anggota-tim.store', $tim->id) }}" method="POST"
                    @submit="isProcessing = true" x-show="openAddMemberModal" x-transition
                    class="relative w-full max-w-lg overflow-hidden rounded-3xl border border-slate-200 bg-white text-xs shadow-2xl dark:border-slate-700 dark:bg-slate-800">
                    @csrf

                    <div class="border-b border-slate-100 px-6 py-5 dark:border-slate-700 sm:px-7">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Tambah Mahasiswa ke Tim</h3>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Tambahkan peserta yang belum
                            memiliki tim.</p>
                    </div>

                    <div class="space-y-2 px-6 py-6 sm:px-7">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-200">
                                Pilih Mahasiswa Belum Memiliki Tim <span class="text-red-500">*</span>
                            </label>
                            <div class="relative mt-2">
                                <select name="anggota_id" required class="team-select">
                                    <option value="">Pilih mahasiswa...</option>
                                    @foreach ($pesertas as $peserta)
                                        <option value="{{ $peserta['id'] }}">{{ $peserta['nama'] }} (NIM:
                                            {{ $peserta['nim'] }})</option>
                                    @endforeach
                                </select>
                                <svg class="pointer-events-none absolute right-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="m6 9 6 6 6-6" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex flex-col-reverse gap-2 border-t border-slate-100 bg-slate-50/70 px-6 py-4 dark:border-slate-700 dark:bg-slate-900/20 sm:flex-row sm:justify-end sm:px-7">
                        <button type="button" @click="openAddMemberModal = false" :disabled="isProcessing"
                            class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 font-bold text-slate-600 transition hover:bg-slate-100 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600">
                            Batal
                        </button>
                        <button type="submit" :disabled="isProcessing"
                            class="btn-primary inline-flex items-center gap-2 disabled:opacity-50">
                            <template x-if="isProcessing">
                                <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </template>
                            <span x-text="isProcessing ? 'Menambahkan...' : 'Tambahkan ke Tim'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
