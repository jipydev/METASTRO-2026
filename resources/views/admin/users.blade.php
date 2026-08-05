<x-app-layout>

    <div class="py-8 font-poppins min-h-screen bg-gray-100 dark:bg-slate-900 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-primary-600 dark:text-primary-400">Kelola QR Code Panitia</h1>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Manage dan re-generate QR Code untuk absensi panitia</p>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('admin.dashboard') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-slate-200 text-sm font-semibold rounded-xl hover:bg-gray-300 dark:hover:bg-slate-600 transition-colors">
                        &larr; Kembali
                    </a>

                    <form action="{{ route('admin.users.regenerate-all-qr') }}" method="POST"
                          onsubmit="return confirm('Yakin ingin re-generate QR Code untuk SEMUA user? QR lama akan tidak valid.')">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-amber-500 text-white text-sm font-semibold rounded-xl hover:bg-amber-600 transition-colors shadow-sm cursor-pointer">
                            🔄 Re-generate Semua QR
                        </button>
                    </form>
                </div>
            </div>

            {{-- Flash Message --}}
            @if(session('success'))
                <div class="mb-6 px-5 py-4 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 rounded-xl text-sm font-medium flex items-center gap-2">
                    <span>✅</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 px-5 py-4 bg-red-50 dark:bg-red-950/60 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-xl text-sm font-medium flex items-center gap-2">
                    <span>❌</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- Users Table --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-50 dark:bg-slate-800/90">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                    User
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                    NIM
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                    Divisi
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                    Jabatan
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                    QR Status
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-100 dark:divide-slate-700">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                                    {{-- User Info --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            @php
                                                $photoUrl = $user->foto
                                                    ? asset('storage/' . $user->foto)
                                                    : 'https://ui-avatars.com/api/?size=80&background=fe5a1d&color=fff&name=' . urlencode($user->name);
                                            @endphp
                                            <img src="{{ $photoUrl }}"
                                                 alt="{{ $user->name }}"
                                                 class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-slate-600">
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900 dark:text-slate-100">{{ $user->name }}</div>
                                                <div class="text-xs text-gray-400 dark:text-slate-500">ID: {{ $user->id }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- NIM --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-700 dark:text-slate-300 font-mono">{{ $user->nim }}</span>
                                    </td>

                                    {{-- Divisi --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-700 dark:text-slate-300">
                                            {{ $user->divisi?->nama_divisi ?? '—' }}
                                        </span>
                                    </td>

                                    {{-- Jabatan --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-700 dark:text-slate-300">
                                            {{ $user->jabatan?->nama ?? '—' }}
                                        </span>
                                    </td>

                                    {{-- QR Status --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($user->qr_token)
                                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 text-xs font-semibold rounded-full">
                                                <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-red-100 dark:bg-red-950/60 text-red-600 dark:text-red-400 text-xs font-semibold rounded-full">
                                                <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                                Belum ada
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <form action="{{ route('admin.users.regenerate-qr', $user) }}" method="POST"
                                              class="inline"
                                              onsubmit="return confirm('Re-generate QR Code untuk {{ $user->name }}? QR lama akan tidak valid.')">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 px-3 py-2 bg-primary-500 hover:bg-primary-600 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm cursor-pointer">
                                                🔄 Re-generate QR
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-400 dark:text-slate-500">
                                        <div class="flex flex-col items-center gap-2">
                                            <span class="text-4xl">👤</span>
                                            <span class="text-sm">Belum ada data user.</span>
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

            {{-- Stats --}}
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5">
                    <div class="text-xs text-gray-400 dark:text-slate-400 font-semibold uppercase tracking-wider">Total User</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $users->total() }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5">
                    <div class="text-xs text-gray-400 dark:text-slate-400 font-semibold uppercase tracking-wider">QR Aktif</div>
                    <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">
                        {{ \App\Models\User::whereNotNull('qr_token')->count() }}
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5">
                    <div class="text-xs text-gray-400 dark:text-slate-400 font-semibold uppercase tracking-wider">Belum Punya QR</div>
                    <div class="text-2xl font-bold text-red-500 dark:text-red-400 mt-1">
                        {{ \App\Models\User::whereNull('qr_token')->count() }}
                    </div>
                </div>
            </div>

        </div>
    </div>

</x-app-layout>
