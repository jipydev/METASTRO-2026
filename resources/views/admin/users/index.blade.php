<x-app-layout :$title>
    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 font-poppins">

        {{-- Header & Tombol Tambah --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">Daftar Pengguna</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400">Kelola akun panitia dan peserta</p>
            </div>
            <a href="{{ route('admin.users.create') }}" 
               class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition">
                + Tambah Pengguna
            </a>
        </div>

        {{-- Flash Alert --}}
        @if(session('success'))
            <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 dark:bg-emerald-950/40 dark:border-emerald-800 dark:text-emerald-300 rounded-lg text-xs font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 dark:bg-red-950/40 dark:border-red-800 dark:text-red-300 rounded-lg text-xs font-medium">
                {{ session('error') }}
            </div>
        @endif

        {{-- Filter & Pencarian Sederhana --}}
        <form method="GET" action="{{ route('admin.users.index') }}" class="mb-6 flex flex-wrap gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIM..." 
                   class="text-xs rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">

            <select name="role" class="text-xs rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                <option value="">Semua Role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                        {{ ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>

            <select name="divisi_id" class="text-xs rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                <option value="">Semua Divisi</option>
                @foreach($divisis as $divisi)
                    <option value="{{ $divisi->id }}" {{ request('divisi_id') == $divisi->id ? 'selected' : '' }}>
                        {{ $divisi->nama }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="px-3 py-2 bg-gray-800 hover:bg-black text-white text-xs font-semibold rounded-lg">
                Filter
            </button>

            @if(request()->hasAny(['search', 'role', 'divisi_id']))
                <a href="{{ route('admin.users.index') }}" class="px-3 py-2 bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-slate-200 text-xs rounded-lg">
                    Reset
                </a>
            @endif
        </form>

        {{-- Tabel Simpel --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700 text-left text-xs">
                    <thead class="bg-gray-50 dark:bg-slate-800/80 text-gray-500 dark:text-slate-400 font-semibold uppercase">
                        <tr>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">NIM</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Divisi / Jabatan</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700 text-gray-700 dark:text-slate-300">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/40">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                    {{ $user->nama }}
                                </td>
                                <td class="px-4 py-3 font-mono">
                                    {{ $user->nim }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded bg-gray-100 dark:bg-slate-700 font-semibold">
                                        {{ ucfirst($user->getRoleNames()->first() ?? 'peserta') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    {{ $user->divisi?->nama ?? '—' }} / {{ $user->jabatan?->nama ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($user->status)
                                        <span class="text-emerald-600 dark:text-emerald-400 font-semibold">Aktif</span>
                                    @else
                                        <span class="text-red-500 font-semibold">Non-Aktif</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="inline-flex items-center gap-3">
                                        {{-- Tombol Edit --}}
                                        <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">
                                            Edit
                                        </a>

                                        {{-- Tombol Hapus --}}
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Hapus pengguna {{ $user->nama }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 font-medium">
                                                    Hapus
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400 italic">Akun Anda</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                    Tidak ada data pengguna.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="px-4 py-3 border-t border-gray-100 dark:border-slate-700">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>