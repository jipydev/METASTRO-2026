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
        openViewQrModal: false,
        openImportModal: false,
    
        selectedUser: { id: null, nama: '', nim: '', email: '', role_type: 'peserta', divisi_id: '', jabatan_id: '', qr_token: '', kode_unik: '', status: true },
    
        initDelete(user) {
            this.selectedUser = { ...user };
            this.openDeleteModal = true;
        },
        initViewQr(user) {
            this.selectedUser = { ...user };
            this.openViewQrModal = true;
        }
    }" class="page-shell">

        <div class="page-wrap">

            {{-- Header & Tombol Utama --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="page-title">
                        Kelola Pengguna & QR Code
                    </h1>
                    <p class="page-subtitle">
                        Monitoring data pengguna dan kode QR presensi.
                    </p>
                </div>

<<<<<<< HEAD
                <div class="flex items-center gap-2.5">
=======
                <div class="flex flex-wrap items-center gap-2.5">
                    {{-- Unduh Template Excel --}}
                    <a href="{{ route('admin.users.template') }}"
                        class="px-3.5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition flex items-center gap-1.5 cursor-pointer"
                        title="Unduh Format Spreadsheet Template Excel (.xlsx)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <span>Template Excel</span>
                    </a>

                    {{-- Tombol Import Excel --}}
                    <button type="button" @click="openImportModal = true"
                        class="px-3.5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold rounded-xl shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12">
                            </path>
                        </svg>
                        <span>Import Excel</span>
                    </button>

                    {{-- Re-generate Semua QR --}}
                    <button type="button" @click="openRegenAllModal = true"
                        class="px-3.5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                        <span>Re-generate QR</span>
                    </button>

>>>>>>> c76c3db8bc44a4fc4ec825d7f8903dfb962a5cf9
                    {{-- Tambah Pengguna --}}
                    <a href="{{ route('admin.users.create') }}"
                        class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                        <span>+</span> Tambah Pengguna
                    </a>
                </div>
            </div>

            {{-- Filter & Pencarian --}}
            <form method="GET" action="{{ route('admin.users.index') }}" class="filter-bar">
                <div class="flex-1 min-w-50">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama atau NIM..." class="form-control-app w-full">
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
                                <th class="px-5 py-3.5 text-center">Kode Unik</th>
                                <th class="px-5 py-3.5 text-center">Status QR</th>
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
                                                <img src="{{ asset('storage/' . $user->foto) }}"
                                                    alt="{{ $user->nama }}"
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
                                        @foreach ($user->getRoleNames() as $role)
                                            <span
                                                class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold {{ $role === 'Admin' ? 'bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300' : ($role === 'Panitia' ? 'bg-indigo-100 text-slate-700 dark:bg-slate-800/60 dark:text-slate-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300') }}">
                                                {{ $role }}
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

                                    {{-- Kolom Kode Unik --}}
                                    <td class="px-5 py-3.5 text-center">
                                        @if ($user->kode_unik)
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-mono font-bold bg-brand-50 text-brand-700 dark:bg-brand-950/60 dark:text-brand-300 border border-brand-200/60 dark:border-brand-800/40">
                                                {{ $user->kode_unik }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 font-mono text-[11px]">—</span>
                                        @endif
                                    </td>

                                    {{-- Kolom Status Token QR --}}
                                    <td class="px-5 py-3.5 text-center">
                                        @if ($user->qr_token)
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/40">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Aktif
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-red-50 text-red-700 dark:bg-red-950/60 dark:text-red-300 border border-red-200/60 dark:border-red-800/40">
                                                <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> Belum Ada
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Kolom Aksi Icon Buttons --}}
                                    <td class="px-5 py-3.5 text-right">
                                        <div class="flex items-center justify-end gap-1">

                                            {{-- 1. Tombol Lihat QR --}}
                                            <button type="button"
                                                @click="initViewQr({{ json_encode(['id' => $user->id, 'nama' => $user->nama, 'nim' => $user->nim, 'qr_token' => $user->qr_token, 'kode_unik' => $user->kode_unik, 'divisi' => $user->formatted_divisi_jabatan, 'divisi_badge' => $user->divisi?->badgeClasses() ?? '']) }})"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-brand-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-indigo-950/60 transition cursor-pointer"
                                                title="Lihat QR Code & Kode Unik">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
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
                        <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold border"
                            :class="selectedUser.divisi_badge ||
                                'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-600'"
                            x-text="selectedUser.divisi"></span>
                    </div>

                    {{-- QR Code Image Box --}}
                    <div
                        class="bg-white p-4 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700 inline-block my-2 shadow-inner">
                        <template x-if="selectedUser.qr_token">
                            <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' + encodeURIComponent(JSON
                                .stringify({
                                    user_id: selectedUser.id,
                                    token: selectedUser.qr_token
                                }))"
                                alt="QR Code Presensi" class="w-44 h-44 object-contain mx-auto rounded-lg" />
                        </template>
                        <template x-if="!selectedUser.qr_token">
                            <div
                                class="w-44 h-44 flex flex-col items-center justify-center text-slate-400 text-[11px] p-4 text-center">
                                <span>❌ Belum ada token QR. Silakan reset QR terlebih dahulu.</span>
                            </div>
                        </template>
                    </div>

                    {{-- Kode Unik Box --}}
                    <template x-if="selectedUser.kode_unik">
                        <div class="mt-2 text-center">
                            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-semibold mb-1">
                                Kode Unik Absensi
                            </span>
                            <span class="font-mono text-base font-bold text-brand-600 dark:text-brand-400 bg-slate-100 dark:bg-slate-700/80 px-3.5 py-1.5 rounded-xl border border-slate-200 dark:border-slate-600 inline-block select-all"
                                x-text="selectedUser.kode_unik"></span>
                        </div>
                    </template>

                    <div class="mt-5 flex gap-2">
                        <button type="button" @click="openViewQrModal = false"
                            class="flex-1 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl transition text-[11px]">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL HAPUS PENGGUNA --}}
        <div x-show="openDeleteModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="openDeleteModal" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm"
                    @click="openDeleteModal = false"></div>

                <form :action="'{{ url('dashboard/users') }}/' + selectedUser.id" method="POST" x-show="openDeleteModal"
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

        {{-- MODAL IMPORT PENGGUNA --}}
        <div x-show="openImportModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="openImportModal" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm"
                    @click="openImportModal = false"></div>

                <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data"
                    x-show="openImportModal" x-transition
                    class="relative bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-6 w-full max-w-lg shadow-xl text-xs">
                    @csrf
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-sky-100 dark:bg-sky-900/40 text-sky-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Import Pengguna dari Excel</h3>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Buat akun secara massal dengan file spreadsheet</p>
                            </div>
                        </div>
                        <button type="button" @click="openImportModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Panduan Singkat --}}
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-100 dark:border-slate-700/60 mb-4 space-y-2 text-slate-600 dark:text-slate-300">
                        <div class="font-semibold text-slate-700 dark:text-slate-200 flex items-center justify-between">
                            <span>Format Kolom Spreadsheet:</span>
                            <a href="{{ route('admin.users.template') }}" class="text-brand-600 hover:underline flex items-center gap-1 font-bold">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Unduh Template .xlsx
                            </a>
                        </div>
                        <p class="leading-relaxed text-[11px]">
                            Kolom wajib: <code class="px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-mono">nama</code>, 
                            <code class="px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-mono">nim</code>, 
                            <code class="px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-mono">email</code>, 
                            <code class="px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-mono">role</code> (admin/panitia/peserta).
                        </p>
                        <p class="leading-relaxed text-[11px] text-slate-500 dark:text-slate-400">
                            Kolom opsional: <code class="font-mono">password</code> (default: metastro2026), <code class="font-mono">divisi</code>, <code class="font-mono">jabatan</code>, <code class="font-mono">tim</code>, <code class="font-mono">nomor_hp</code>, <code class="font-mono">jenis_kelamin</code> (L/P), <code class="font-mono">tanggal_lahir</code>.
                        </p>
                    </div>

                    {{-- File Input --}}
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Pilih File Excel / CSV (.xlsx, .xls, .csv) <span class="text-rose-500">*</span>
                        </label>
                        <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                            class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 dark:file:bg-brand-900/40 dark:file:text-brand-300 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer bg-white dark:bg-slate-800 p-1">
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" @click="openImportModal = false"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2 bg-sky-600 hover:bg-sky-700 text-white font-semibold rounded-xl shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Mulai Import</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
