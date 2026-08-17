<x-app-layout :$title>
    {{-- Notifikasi SweetAlert --}}
    @if (session('success') || session('error'))
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
                @endif
            });
        </script>
    @endif

    <div x-data="{
        openDeleteModal: false,
        openResetQrModal: false,
        openRegenAllModal: false,
        openViewQrModal: false,
    
        selectedUser: { id: null, nama: '', nim: '', email: '', role_type: 'peserta', divisi_id: '', jabatan_id: '', qr_token: '', status: true },
    
        initDelete(user) {
            this.selectedUser = { ...user };
            this.openDeleteModal = true;
        },
        initResetQr(user) {
            this.selectedUser = { ...user };
            this.openResetQrModal = true;
        },
        initViewQr(user) {
            this.selectedUser = { ...user };
            this.openViewQrModal = true;
        }
    }"
        class="page-shell">

        <div class="page-wrap">

            {{-- Header & Tombol Utama --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="page-title">
                        Kelola Pengguna & QR Code
                    </h1>
                    <p class="page-subtitle">
                        Monitoring data pengguna dan pengelolaan token QR presensi.
                    </p>
                </div>

                <div class="flex items-center gap-2.5">
                    {{-- Re-generate Semua QR --}}
                    <button type="button" @click="openRegenAllModal = true"
                        class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                        <span>Re-generate Semua QR</span>
                    </button>

                    {{-- Tambah Pengguna --}}
                    <a href="{{ route('admin.users.create') }}"
                        class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                        <span>+</span> Tambah Pengguna
                    </a>
                </div>
            </div>

            {{-- Filter & Pencarian --}}
            <form method="GET" action="{{ route('admin.users.index') }}"
                class="filter-bar">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama atau NIM..."
                        class="form-control-app w-full">
                </div>

                <div>
                    <select name="role" class="form-control-app">
                        <option value="">Semua Role</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="panitia" {{ request('role') === 'panitia' ? 'selected' : '' }}>Panitia</option>
                        <option value="peserta" {{ request('role') === 'peserta' ? 'selected' : '' }}>Peserta</option>
                    </select>
                </div>

                <div>
                    <select name="divisi_id" class="form-control-app">
                        <option value="">Semua Divisi</option>
                        @foreach ($divisis as $divisi)
                            <option value="{{ $divisi->id }}"
                                {{ request('divisi_id') == $divisi->id ? 'selected' : '' }}>
                                {{ $divisi->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="jabatan_id" class="form-control-app">
                        <option value="">Semua Jabatan</option>
                        @foreach ($jabatans as $jabatan)
                            <option value="{{ $jabatan->id }}"
                                {{ request('jabatan_id') == $jabatan->id ? 'selected' : '' }}>
                                {{ $jabatan->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn-filter">Filter</button>

                @if (request()->hasAny(['search', 'role', 'divisi_id', 'jabatan_id']))
                    <a href="{{ route('admin.users.index') }}"
                        class="text-xs text-slate-500 hover:text-slate-700 dark:text-slate-400">
                        Reset
                    </a>
                @endif
            </form>

            {{-- Tabel Pengguna & QR Code --}}
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                        <thead
                            class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200/80 dark:border-slate-700 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            <tr>
                                <th class="px-5 py-3.5">Pengguna</th>
                                <th class="px-5 py-3.5">NIM</th>
                                <th class="px-5 py-3.5">Role</th>
                                <th class="px-5 py-3.5">Divisi / Jabatan</th>
                                <th class="px-5 py-3.5 text-center">Status Token QR</th>
                                <th class="px-5 py-3.5">Terakhir Update QR</th>
                                <th class="px-5 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse ($users as $user)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/20 transition">
                                    {{-- Kolom Pengguna --}}
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-3">
                                            @if ($user->foto)
                                                <img src="{{ asset('storage/' . $user->foto) }}" alt="{{ $user->nama }}"
                                                    class="w-8 h-8 rounded-xl object-cover shrink-0 shadow-sm border border-slate-200 dark:border-slate-600">
                                            @else
                                                <div
                                                    class="w-8 h-8 rounded-xl bg-brand-600 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-sm">
                                                    {{ $user->initials() }}
                                                </div>
                                            @endif
                                            <div class="min-w-0">
                                                <div class="font-bold text-slate-900 dark:text-white truncate">
                                                    {{ $user->nama }}
                                                </div>
                                                <div class="text-[11px] text-slate-400 truncate">
                                                    {{ $user->email ?? 'Belum ada email' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Kolom NIM --}}
                                    <td class="px-5 py-3.5 font-mono font-medium text-slate-700 dark:text-slate-300">
                                        {{ $user->nim }}
                                    </td>

                                    {{-- Kolom Role --}}
                                    <td class="px-5 py-3.5">
                                        @foreach ($user->roles as $role)
                                            <span
                                                class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold {{ $role->name === 'admin' ? 'bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300' : ($role->name === 'panitia' ? 'bg-indigo-100 text-slate-700 dark:bg-slate-800/60 dark:text-slate-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300') }}">
                                                {{ ucfirst($role->name) }}
                                            </span>
                                        @endforeach
                                    </td>

                                    <td class="px-5 py-3.5">
                                        @if ($user->divisi)
                                            <x-divisi-badge :divisi="$user->divisi" :label="$user->formatted_divisi_jabatan" />
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>

                                    {{-- Kolom Status Token QR --}}
                                    <td class="px-5 py-3.5 text-center">
                                        @if ($user->qr_token)
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/40">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Siap
                                                Dipakai
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-red-50 text-red-700 dark:bg-red-950/60 dark:text-red-300 border border-red-200/60 dark:border-red-800/40">
                                                <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> Belum Ada
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-3.5 text-slate-500 dark:text-slate-400 text-[11px]">
                                        {{ $user->qr_updated_at ? $user->qr_updated_at->diffForHumans() : '-' }}
                                    </td>

                                    {{-- Kolom Aksi Icon Buttons --}}
                                    <td class="px-5 py-3.5 text-right">
                                        <div class="flex items-center justify-end gap-1">

                                            {{-- 1. Tombol Lihat QR --}}
                                            <button type="button"
                                                @click="initViewQr({{ json_encode(['id' => $user->id, 'nama' => $user->nama, 'nim' => $user->nim, 'qr_token' => $user->qr_token, 'divisi' => $user->formatted_divisi_jabatan, 'divisi_badge' => $user->divisi?->badgeClasses() ?? '']) }})"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-brand-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-indigo-950/60 transition cursor-pointer"
                                                title="Lihat QR Code">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                                </svg>
                                            </button>

                                            {{-- 2. Tombol Reset QR --}}
                                            <button type="button"
                                                @click="initResetQr({{ json_encode(['id' => $user->id, 'nama' => $user->nama]) }})"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/60 transition cursor-pointer"
                                                title="Re-generate QR Token">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                    </path>
                                                </svg>
                                            </button>

                                            {{-- 3. Tombol Edit --}}
                                            <a href="{{ route('admin.users.edit', $user->id) }}"
                                                class="p-1.5 inline-block rounded-lg text-slate-500 hover:text-brand-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-indigo-950/60 transition cursor-pointer"
                                                title="Edit Pengguna">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                            </a>

                                            {{-- 4. Tombol Hapus (Aktif untuk user lain, Disabled untuk akun sendiri) --}}
                                            @if ($user->id !== auth()->id())
                                                <button type="button"
                                                    @click="initDelete({{ json_encode(['id' => $user->id, 'nama' => $user->nama]) }})"
                                                    class="p-1.5 rounded-lg text-slate-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/60 transition cursor-pointer"
                                                    title="Hapus Pengguna">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            @else
                                                <button type="button" disabled
                                                    class="p-1.5 rounded-lg text-slate-300 dark:text-slate-600 cursor-not-allowed opacity-60"
                                                    title="Tidak dapat menghapus akun sendiri">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            @endif

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-10 text-center text-slate-400">
                                        Tidak ada data pengguna yang sesuai.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($users->hasPages())
                    <div class="px-5 py-4 border-t border-slate-200/80 dark:border-slate-700">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

        </div>

        {{-- MODAL PREVIEW QR PENGGUNA --}}
        <div x-show="openViewQrModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="openViewQrModal" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm"
                    @click="openViewQrModal = false"></div>

                <div x-show="openViewQrModal" x-transition
                    class="relative bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 p-6 w-full max-w-xs text-center shadow-2xl text-xs">

                    <div class="mb-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-400 flex items-center justify-center mx-auto mb-2 font-bold text-base">
                            📱
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white" x-text="selectedUser.nama">
                        </h3>
                        <p class="text-slate-400 text-[11px] font-mono mt-0.5">NIM: <span
                                x-text="selectedUser.nim"></span></p>
                        <span
                            class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold border"
                            :class="selectedUser.divisi_badge || 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-600'"
                            x-text="selectedUser.divisi"></span>
                    </div>

                    {{-- QR Code Image Box --}}
                    <div
                        class="bg-white p-4 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700 inline-block my-2 shadow-inner">
                        <template x-if="selectedUser.qr_token">
                            <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' + encodeURIComponent(
                                selectedUser.qr_token)"
                                alt="QR Code Presensi" class="w-44 h-44 object-contain mx-auto rounded-lg" />
                        </template>
                        <template x-if="!selectedUser.qr_token">
                            <div
                                class="w-44 h-44 flex flex-col items-center justify-center text-slate-400 text-[11px] p-4 text-center">
                                <span>❌ Belum ada token QR. Silakan reset QR terlebih dahulu.</span>
                            </div>
                        </template>
                    </div>

                    <div class="mt-5 flex gap-2">
                        <button type="button" @click="openViewQrModal = false"
                            class="flex-1 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl transition text-[11px]">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL RESET SINGLE QR --}}
        <div x-show="openResetQrModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="openResetQrModal" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm"
                    @click="openResetQrModal = false"></div>

                <form :action="'{{ url('admin/users') }}/' + selectedUser.id + '/reset-qr'" method="POST"
                    x-show="openResetQrModal" x-transition
                    class="relative bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-6 w-full max-w-sm shadow-xl text-xs">
                    @csrf
                    @method('PATCH')
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">Reset Token QR?</h3>
                    <p class="text-slate-500 dark:text-slate-400 mb-6 leading-relaxed">
                        Token QR untuk pengguna <strong class="text-slate-900 dark:text-white"
                            x-text="selectedUser.nama"></strong> akan diperbarui. QR code lama tidak akan berlaku lagi
                        untuk presensi.
                    </p>

                    <div class="flex justify-end gap-2">
                        <button type="button" @click="openResetQrModal = false"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-xl shadow-sm transition">
                            Ya, Reset QR
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL REGENERATE SEMUA QR --}}
        <div x-show="openRegenAllModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="openRegenAllModal" x-transition.opacity
                    class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="openRegenAllModal = false"></div>

                <form action="{{ route('admin.users.reset-all-qr') }}" method="POST" x-show="openRegenAllModal"
                    x-transition
                    class="relative bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-6 w-full max-w-md shadow-xl text-xs">
                    @csrf
                    @method('PATCH')
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">Re-generate Semua QR Token?
                    </h3>
                    <p class="text-slate-500 dark:text-slate-400 mb-6 leading-relaxed">
                        Tindakan ini akan membuat token QR baru untuk <strong>seluruh pengguna</strong> sekaligus.
                        Seluruh QR Code lama yang sudah diunduh atau dicetak panitia/peserta tidak akan bisa digunakan
                        lagi.
                    </p>

                    <div class="flex justify-end gap-2">
                        <button type="button" @click="openRegenAllModal = false"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-xl shadow-sm transition">
                            Ya, Re-generate Semua
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL HAPUS PENGGUNA --}}
        <div x-show="openDeleteModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="openDeleteModal" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm"
                    @click="openDeleteModal = false"></div>

                <form :action="'{{ url('admin/users') }}/' + selectedUser.id" method="POST" x-show="openDeleteModal"
                    x-transition
                    class="relative bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-6 w-full max-w-sm shadow-xl text-xs">
                    @csrf
                    @method('DELETE')
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">Hapus Pengguna?</h3>
                    <p class="text-slate-500 dark:text-slate-400 mb-6">
                        Apakah Anda yakin ingin menghapus akun <strong class="text-slate-900 dark:text-white"
                            x-text="selectedUser.nama"></strong>? Seluruh data riwayat presensi dan perizinannya akan
                        ikut terhapus.
                    </p>

                    <div class="flex justify-end gap-2">
                        <button type="button" @click="openDeleteModal = false"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow-sm transition cursor-pointer">
                            Ya, Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
