<x-app-layout>
    <div x-data="{
        showDetailModal: false,
        showEditModal: false,
        selectedUser: {
            id: null,
            name: '',
            nim: '',
            email: '',
            foto: '',
            nomor_hp: '',
            jenis_kelamin: '',
            tanggal_lahir: '',
            alamat: '',
            role: '',
            divisi_id: '',
            divisi_nama: '',
            jabatan_id: '',
            jabatan_nama: '',
            status_aktif: 1,
            qr_token: null
        },
        openDetail(user) {
            this.selectedUser = user;
            this.showDetailModal = true;
        },
        openEdit(user) {
            this.selectedUser = Object.assign({}, user);
            this.showEditModal = true;
        }
    }" class="py-8 font-poppins min-h-screen bg-gray-100 dark:bg-slate-900 transition-colors duration-200">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-primary-600 dark:text-primary-400">Kelola Data User & Role</h1>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Manajemen informasi pengguna, hak akses role, divisi, dan jabatan panitia</p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.manage-users.create') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary-500 hover:bg-primary-600 text-white text-xs font-bold rounded-xl transition shadow-lg shadow-primary-500/20 cursor-pointer">
                        <span class="icon-[akar-icons--plus]"></span>
                        + Tambah User Baru
                    </a>
                    <a href="{{ route('admin.role-request') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl transition shadow-sm cursor-pointer">
                        <span class="icon-[akar-icons--clock]"></span>
                        Permintaan Role
                    </a>
                    <a href="{{ route('admin.users') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-700 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition shadow-sm cursor-pointer">
                        <span class="icon-[material-symbols--qr-code]"></span>
                        Kelola QR Code
                    </a>
                </div>
            </div>

            {{-- Flash Alert --}}
            @if(session('success'))
                <div class="mb-6 px-5 py-4 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 rounded-xl text-sm font-medium flex items-center gap-2">
                    <span class="text-lg">✅</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 px-5 py-4 bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 rounded-xl text-sm font-medium flex items-center gap-2">
                    <span class="text-lg">❌</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- Filter & Search Card --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-5 mb-6">
                <form method="GET" action="{{ route('admin.manage-users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                    
                    {{-- Search Input --}}
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">Cari User</label>
                        <div class="relative">
                            <input type="text"
                                   name="search"
                                   value="{{ $search }}"
                                   placeholder="Ketik Nama, NIM, atau Email..."
                                   class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-200 dark:border-slate-600 rounded-xl py-2.5 px-4 pl-10 text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-primary-500 border-none outline-none">
                            <svg class="w-5 h-5 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    {{-- Role Filter --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">Filter Role</label>
                        <select name="role" class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-200 dark:border-slate-600 rounded-xl py-2.5 px-3 text-sm text-slate-900 dark:text-white border-none focus:ring-2 focus:ring-primary-500">
                            <option value="">-- Semua Role --</option>
                            @foreach($allRoles as $role)
                                <option value="{{ $role->name }}" {{ $roleFilter == $role->name ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Divisi Filter --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">Filter Divisi</label>
                        <select name="divisi_id" class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-200 dark:border-slate-600 rounded-xl py-2.5 px-3 text-sm text-slate-900 dark:text-white border-none focus:ring-2 focus:ring-primary-500">
                            <option value="">-- Semua Divisi --</option>
                            @foreach($allDivisis as $divisi)
                                <option value="{{ $divisi->id }}" {{ $divisiFilter == $divisi->id ? 'selected' : '' }}>
                                    {{ $divisi->nama_divisi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Jabatan Filter & Action --}}
                    <div class="flex gap-2">
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">Jabatan</label>
                            <select name="jabatan_id" class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-200 dark:border-slate-600 rounded-xl py-2.5 px-3 text-sm text-slate-900 dark:text-white border-none focus:ring-2 focus:ring-primary-500">
                                <option value="">-- Semua --</option>
                                @foreach($allJabatans as $jabatan)
                                    <option value="{{ $jabatan->id }}" {{ $jabatanFilter == $jabatan->id ? 'selected' : '' }}>
                                        {{ $jabatan->nama_jabatan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center gap-1.5 pt-6">
                            <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition cursor-pointer shadow-sm">
                                Filter
                            </button>
                            @if($search || $roleFilter || $divisiFilter || $jabatanFilter)
                                <a href="{{ route('admin.manage-users.index') }}" class="bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 text-slate-700 dark:text-slate-200 font-bold px-3 py-2.5 rounded-xl text-xs transition">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </div>

                </form>
            </div>

            {{-- Users Table --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-50 dark:bg-slate-800/90">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Pengguna</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">NIM</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Divisi & Jabatan</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-100 dark:divide-slate-700">
                            @forelse($users as $user)
                                @php
                                    $userRole = $user->roles->first()?->name ?? 'Belum ada role';
                                    $photoUrl = $user->foto
                                        ? asset('storage/' . $user->foto)
                                        : 'https://ui-avatars.com/api/?size=128&background=fe5a1d&color=fff&name=' . urlencode($user->name);
                                    
                                    $userJson = [
                                        'id' => $user->id,
                                        'name' => $user->name,
                                        'nim' => $user->nim,
                                        'email' => $user->email ?? '-',
                                        'foto' => $photoUrl,
                                        'nomor_hp' => $user->nomor_hp ?? '-',
                                        'jenis_kelamin' => $user->jenis_kelamin ?? '-',
                                        'tanggal_lahir' => $user->tanggal_lahir ? \Carbon\Carbon::parse($user->tanggal_lahir)->translatedFormat('d F Y') : '-',
                                        'alamat' => $user->alamat ?? '-',
                                        'role' => $userRole,
                                        'divisi_id' => $user->divisi_id,
                                        'divisi_nama' => $user->divisi?->nama_divisi ?? '-',
                                        'jabatan_id' => $user->jabatan_id,
                                        'jabatan_nama' => $user->jabatan?->nama_jabatan ?? '-',
                                        'status_aktif' => (int) $user->status_aktif,
                                        'qr_token' => $user->qr_token
                                    ];
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                                    {{-- User Info --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $photoUrl }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-slate-600">
                                            <div>
                                                <div class="text-sm font-bold text-gray-900 dark:text-slate-100">{{ $user->name }}</div>
                                                <div class="text-xs text-gray-400 dark:text-slate-400">{{ $user->email ?? 'NIM: ' . $user->nim }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- NIM --}}
                                    <td class="px-6 py-4 whitespace-nowrap font-mono text-sm text-gray-700 dark:text-slate-300">
                                        {{ $user->nim }}
                                    </td>

                                    {{-- Role Badge --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $roleColor = match(strtolower($userRole)) {
                                                'admin' => 'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border-rose-200 dark:border-rose-800',
                                                'peserta' => 'bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                                                default => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $roleColor }}">
                                            {{ $userRole }}
                                        </span>
                                    </td>

                                    {{-- Divisi & Jabatan --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-800 dark:text-slate-200">{{ $user->divisi?->nama_divisi ?? '-' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-slate-400">{{ $user->jabatan?->nama_jabatan ?? '-' }}</div>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($user->status_aktif)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-xs font-semibold rounded-full">
                                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-600 dark:text-rose-400 text-xs font-semibold rounded-full">
                                                <span class="w-1.5 h-1.5 bg-rose-500 rounded-full"></span>
                                                Non-Aktif
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            {{-- Detail Button --}}
                                            <button type="button"
                                                    @click="openDetail({{ json_encode($userJson) }})"
                                                    class="bg-blue-50 hover:bg-blue-100 text-blue-600 dark:bg-slate-700 dark:hover:bg-slate-600 dark:text-blue-400 px-3 py-1.5 rounded-lg font-semibold text-xs transition cursor-pointer">
                                                Detail
                                            </button>

                                            {{-- Edit Role Button --}}
                                            @if($user->id !== auth()->id())
                                                <button type="button"
                                                        @click="openEdit({{ json_encode($userJson) }})"
                                                        class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg font-semibold text-xs transition cursor-pointer shadow-sm">
                                                    Edit Role
                                                </button>
                                            @else
                                                <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 italic px-2 py-1 bg-slate-100 dark:bg-slate-700/60 rounded-lg">
                                                    Akun Anda
                                                </span>
                                            @endif

                                            {{-- Delete Button --}}
                                            @if($user->id !== auth()->id())
                                                <form action="{{ route('admin.manage-users.destroy', $user) }}" method="POST" class="inline"
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $user->name }}? Data pengguna ini akan dihapus secara permanen.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="bg-rose-500 hover:bg-rose-600 text-white px-3 py-1.5 rounded-lg font-semibold text-xs transition cursor-pointer shadow-sm">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-400 dark:text-slate-500">
                                        <div class="flex flex-col items-center gap-2">
                                            <span class="text-4xl">🔍</span>
                                            <span class="text-sm font-semibold">Tidak ditemukan user dengan kriteria filter tersebut.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($users->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

        </div>

        {{-- ==========================================
             MODAL DETAIL USER
           ========================================== --}}
        <div x-show="showDetailModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="showDetailModal" x-transition.opacity @click="showDetailModal = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

                <div x-show="showDetailModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="relative inline-block w-full max-w-lg p-6 overflow-hidden text-left align-bottom bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-slate-700 font-poppins transform transition-all my-8 sm:align-middle">
                    
                    <div class="flex justify-between items-start pb-4 border-b border-gray-100 dark:border-slate-700">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Detail Pengguna</h3>
                        <button type="button" @click="showDetailModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-white font-bold text-xl">&times;</button>
                    </div>

                    <div class="mt-4 flex flex-col items-center text-center">
                        <img :src="selectedUser.foto" :alt="selectedUser.name" class="w-24 h-24 rounded-full object-cover border-4 border-primary-500/20 shadow-md">
                        <h4 class="mt-3 text-xl font-bold text-slate-900 dark:text-white" x-text="selectedUser.name"></h4>
                        <span class="mt-1 px-3 py-1 bg-primary-50 dark:bg-slate-700 text-primary-600 dark:text-primary-400 font-bold text-xs rounded-full" x-text="'Role: ' + selectedUser.role"></span>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-4 text-sm">
                        <div class="bg-gray-50 dark:bg-slate-700/50 p-3 rounded-xl">
                            <span class="text-xs text-gray-400 dark:text-slate-400 font-bold uppercase block mb-1">NIM</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200" x-text="selectedUser.nim"></span>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-700/50 p-3 rounded-xl">
                            <span class="text-xs text-gray-400 dark:text-slate-400 font-bold uppercase block mb-1">Status Akun</span>
                            <span class="font-bold" :class="selectedUser.status_aktif ? 'text-emerald-600' : 'text-rose-500'" x-text="selectedUser.status_aktif ? 'Aktif' : 'Non-Aktif'"></span>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-700/50 p-3 rounded-xl">
                            <span class="text-xs text-gray-400 dark:text-slate-400 font-bold uppercase block mb-1">Divisi</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedUser.divisi_nama"></span>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-700/50 p-3 rounded-xl">
                            <span class="text-xs text-gray-400 dark:text-slate-400 font-bold uppercase block mb-1">Jabatan</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedUser.jabatan_nama"></span>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-700/50 p-3 rounded-xl col-span-2">
                            <span class="text-xs text-gray-400 dark:text-slate-400 font-bold uppercase block mb-1">Email</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedUser.email"></span>
                        </div>

                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="button" @click="showDetailModal = false" class="px-5 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 text-slate-700 dark:text-slate-200 font-bold rounded-xl text-xs transition cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==========================================
             MODAL EDIT ROLE & JABATAN USER
           ========================================== --}}
        <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="showEditModal" x-transition.opacity @click="showEditModal = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

                <form :action="'/admin/manage-users/' + selectedUser.id" method="POST"
                      x-show="showEditModal"
                      x-transition:enter="ease-out duration-300"
                      x-transition:enter-start="opacity-0 scale-95"
                      x-transition:enter-end="opacity-100 scale-100"
                      x-transition:leave="ease-in duration-200"
                      x-transition:leave-start="opacity-100 scale-100"
                      x-transition:leave-end="opacity-0 scale-95"
                      class="relative inline-block w-full max-w-md p-6 overflow-hidden text-left align-bottom bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-slate-700 font-poppins transform transition-all my-8 sm:align-middle">
                    @csrf
                    @method('PUT')

                    <div class="flex justify-between items-center pb-4 border-b border-gray-100 dark:border-slate-700">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Ubah Role & Jabatan</h3>
                        <button type="button" @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-white font-bold text-xl">&times;</button>
                    </div>

                    <div class="mt-4 flex items-center gap-3 bg-gray-50 dark:bg-slate-700/40 p-3 rounded-xl">
                        <img :src="selectedUser.foto" :alt="selectedUser.name" class="w-12 h-12 rounded-full object-cover border">
                        <div>
                            <div class="font-bold text-slate-900 dark:text-white text-sm" x-text="selectedUser.name"></div>
                            <div class="text-xs text-gray-400" x-text="'NIM: ' + selectedUser.nim"></div>
                        </div>
                    </div>

                    <div class="mt-4 space-y-4">
                        {{-- Select Role Spatie --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 uppercase tracking-wider">Role Utama</label>
                            <select name="role" x-model="selectedUser.role" required class="w-full bg-slate-50 dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3 text-sm text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-primary-500">
                                @foreach($allRoles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Select Divisi --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 uppercase tracking-wider">Divisi</label>
                            <select name="divisi_id" x-model="selectedUser.divisi_id" class="w-full bg-slate-50 dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3 text-sm text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="">-- Pilih Divisi --</option>
                                @foreach($allDivisis as $divisi)
                                    <option value="{{ $divisi->id }}">{{ $divisi->nama_divisi }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Select Jabatan --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 uppercase tracking-wider">Jabatan</label>
                            <select name="jabatan_id" x-model="selectedUser.jabatan_id" class="w-full bg-slate-50 dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3 text-sm text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="">-- Pilih Jabatan --</option>
                                @foreach($allJabatans as $jabatan)
                                    <option value="{{ $jabatan->id }}">{{ $jabatan->nama_jabatan }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Status Aktif --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 uppercase tracking-wider">Status Aktif Akun</label>
                            <select name="status_aktif" x-model="selectedUser.status_aktif" class="w-full bg-slate-50 dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3 text-sm text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="1">Aktif</option>
                                <option value="0">Non-Aktif (Di-suspend)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 text-slate-700 dark:text-slate-200 font-bold rounded-xl text-xs transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-primary-500 hover:bg-primary-600 text-white font-bold rounded-xl text-xs transition shadow-sm cursor-pointer">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
