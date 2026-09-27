<x-app-layout :$title>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight font-oswald tracking-tight">
                        {{ $tim['nama'] }}
                    </h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-brand-100 text-brand-700 dark:bg-brand-950/60 dark:text-brand-300">
                        Kelompok Bimbingan
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Alokasi Pasangan Guider Pembimbing (2 orang) & Distribusi Anggota Peserta
                </p>
            </div>
            <div>
                <a href="{{ route('tim-guider.index') }}"
                    class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-xl transition">
                    &larr; Kembali ke Daftar Tim
                </a>
            </div>
        </div>
    </x-slot>

    <div x-data="{
        openAddMemberModal: false,
        openAddGuiderModal: false,
        isProcessing: false
    }" class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 font-poppins space-y-6 text-xs">

        {{-- Toast / Flash Notification --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-2xl text-emerald-700 dark:text-emerald-300 font-semibold flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="show = false" class="text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-300">&times;</button>
            </div>
        @endif

        {{-- BAGIAN 1: PASANGAN PEMBIMBING (TIM GUIDERS - 2 GUIDER SLOT) --}}
        <section class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5 pb-4 border-b border-slate-100 dark:border-slate-700">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Pasangan Guider Pembimbing (2 Guider per Tim)</span>
                    </h3>
                    <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5">
                        Setiap tim bimbingan didampingi secara khusus oleh pasangan 2 panitia Divisi Guider
                    </p>
                </div>

                @php
                    $guiders = $tim['guiders'] ?? [];
                @endphp

                @if (count($guiders) < 2)
                    <button type="button" @click="openAddGuiderModal = true"
                        class="btn-primary flex items-center gap-1.5 self-start sm:self-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tugaskan Guider</span>
                    </button>
                @else
                    <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Formasi Pasangan Lengkap (2/2)
                    </span>
                @endif
            </div>

            {{-- Slot Grid Pasangan Guider --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @for ($i = 0; $i < 2; $i++)
                    @php
                        $guider = $guiders[$i] ?? null;
                    @endphp

                    @if ($guider)
                        <div class="p-4 bg-brand-50/60 dark:bg-slate-700/40 rounded-2xl border border-brand-200/80 dark:border-slate-600 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-brand-600 text-white font-bold flex items-center justify-center text-sm shadow-sm">
                                    G{{ $i + 1 }}
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-brand-600 dark:text-brand-400 uppercase tracking-wider block">Guider {{ $i + 1 }}</span>
                                    <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $guider['nama'] }}</h4>
                                    <p class="text-[11px] text-slate-400">{{ $guider['email'] }}</p>
                                </div>
                            </div>

                            <form action="{{ route('tim-guider.update', $tim['id']) }}" method="POST" @submit="isProcessing = true">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="remove_guider">
                                <input type="hidden" name="guider_id" value="{{ $guider['id'] }}">
                                <button type="submit" :disabled="isProcessing"
                                    class="px-2.5 py-1.5 bg-red-100 hover:bg-red-200 dark:bg-red-950/60 text-red-600 dark:text-red-400 text-xs font-semibold rounded-xl transition flex items-center gap-1 cursor-pointer">
                                    <span>Copot</span>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="p-4 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-200 dark:bg-slate-700 text-slate-400 font-bold flex items-center justify-center text-sm">
                                    G{{ $i + 1 }}
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Slot Guider {{ $i + 1 }}</span>
                                    <h4 class="font-semibold text-slate-400 italic">Belum Ditugaskan</h4>
                                </div>
                            </div>

                            <button type="button" @click="openAddGuiderModal = true"
                                class="px-3 py-1.5 bg-brand-50 hover:bg-brand-100 dark:bg-slate-700 text-brand-600 dark:text-brand-300 font-bold text-xs rounded-xl transition">
                                + Pilih Guider
                            </button>
                        </div>
                    @endif
                @endfor
            </div>
        </section>

        {{-- BAGIAN 2: ALOKASI ANGGOTA PESERTA (TIM MEMBERS - FLEKSIBEL TANPA LIMIT KUOTA) --}}
        <section class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5 pb-4 border-b border-slate-100 dark:border-slate-700">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Daftar Anggota Peserta ({{ count($tim['members'] ?? []) }} Orang)</span>
                    </h3>
                    <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5">
                        Alokasi peserta bersifat dinamis tanpa batasan kuota minimum / maksimum
                    </p>
                </div>

                <button type="button" @click="openAddMemberModal = true"
                    class="btn-primary flex items-center gap-1.5 self-start sm:self-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    <span>+ Tambah Peserta ke Tim</span>
                </button>
            </div>

            {{-- Table Anggota --}}
            <div class="table-card">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300 divide-y divide-gray-100 dark:divide-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 uppercase text-[11px] font-semibold tracking-wider">
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
                                        {{ $member['nama'] }}
                                    </td>
                                    <td class="py-3.5 px-4 font-mono font-semibold text-slate-700 dark:text-slate-300">
                                        {{ $member['nim'] }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold capitalize {{ $member['jenis_kelamin'] === 'laki-laki' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300' : 'bg-pink-100 text-pink-700 dark:bg-pink-950/60 dark:text-pink-300' }}">
                                            {{ $member['jenis_kelamin'] }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <form action="{{ route('tim-guider.update', $tim['id']) }}" method="POST" @submit="isProcessing = true">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="action" value="remove_member">
                                            <input type="hidden" name="member_id" value="{{ $member['id'] }}">
                                            <button type="submit" :disabled="isProcessing"
                                                class="px-2.5 py-1.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition text-xs cursor-pointer">
                                                Keluarkan
                                            </button>
                                        </form>
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
        <div x-show="openAddGuiderModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="openAddGuiderModal" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="openAddGuiderModal = false"></div>

                <form action="{{ route('tim-guider.update', $tim['id']) }}" method="POST" @submit="isProcessing = true"
                    x-show="openAddGuiderModal" x-transition
                    class="relative bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-6 w-full max-w-md shadow-xl text-xs">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="action" value="add_guider">

                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Tugaskan Guider Pembimbing</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Pilih Panitia Guider <span class="text-red-500">*</span>
                            </label>
                            <select name="pembimbing_id" required class="form-control-app w-full">
                                <option value="">-- Pilih Panitia Guider --</option>
                                @foreach ($availableGuiders as $g)
                                    <option value="{{ $g['id'] }}">{{ $g['nama'] }} ({{ $g['email'] }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" @click="openAddGuiderModal = false" :disabled="isProcessing"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="isProcessing"
                            class="btn-primary inline-flex items-center gap-2 disabled:opacity-50">
                            <template x-if="isProcessing">
                                <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            </template>
                            <span x-text="isProcessing ? 'Menugaskan...' : 'Tugaskan Guider'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL ALOKASI PESERTA --}}
        <div x-show="openAddMemberModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="openAddMemberModal" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="openAddMemberModal = false"></div>

                <form action="{{ route('tim-guider.update', $tim['id']) }}" method="POST" @submit="isProcessing = true"
                    x-show="openAddMemberModal" x-transition
                    class="relative bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-6 w-full max-w-md shadow-xl text-xs">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="action" value="add_member">

                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Tambah Mahasiswa ke Tim</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Pilih Mahasiswa Belum Memiliki Tim <span class="text-red-500">*</span>
                            </label>
                            <select name="anggota_id" required class="form-control-app w-full">
                                <option value="">-- Pilih Mahasiswa --</option>
                                @foreach ($unassignedMembers as $m)
                                    <option value="{{ $m['id'] }}">{{ $m['nama'] }} (NIM: {{ $m['nim'] }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" @click="openAddMemberModal = false" :disabled="isProcessing"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="isProcessing"
                            class="btn-primary inline-flex items-center gap-2 disabled:opacity-50">
                            <template x-if="isProcessing">
                                <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            </template>
                            <span x-text="isProcessing ? 'Menambahkan...' : 'Tambahkan ke Tim'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>