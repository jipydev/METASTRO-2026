<x-app-layout :$title>
    <div class="py-8 font-poppins min-h-screen bg-gray-50 dark:bg-slate-900 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">Riwayat & Rekap Presensi</h1>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">
                        {{ auth()->user()->canScanPresensi() ? 'Log presensi seluruh peserta dan panitia' : 'Catatan kehadiran pribadi Anda' }}
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('dashboard') }}"
                       class="px-3.5 py-2 bg-gray-200 dark:bg-slate-700 hover:bg-gray-300 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-200 text-xs font-semibold rounded-xl transition">
                        &larr; Dashboard
                    </a>

                    @if(auth()->user()->canScanPresensi())
                        <a href="{{ route('presensi.scan') }}"
                           class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition flex items-center gap-1.5">
                            <span>📷</span> Scanner QR
                        </a>
                    @endif
                </div>
            </div>

            {{-- Filter & Pencarian Sederhana --}}
            <form method="GET" action="{{ route('presensi.history') }}" class="mb-6 flex flex-wrap items-center gap-2 text-xs">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIM, atau kegiatan..."
                       class="rounded-xl border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-xs px-3.5 py-2 focus:ring-indigo-500 focus:border-indigo-500">

                <select name="status" class="rounded-xl border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-xs px-3.5 py-2">
                    <option value="">Semua Status</option>
                    <option value="hadir" {{ request('status') === 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="terlambat" {{ request('status') === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    <option value="izin" {{ request('status') === 'izin' ? 'selected' : '' }}>Izin</option>
                    <option value="alpa" {{ request('status') === 'alpa' ? 'selected' : '' }}>Alpa</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-black text-white text-xs font-semibold rounded-xl transition">
                    Filter
                </button>

                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('presensi.history') }}" class="px-3.5 py-2 bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-slate-200 text-xs rounded-xl">
                        Reset
                    </a>
                @endif
            </form>

            {{-- Tabel Riwayat Presensi --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700 text-left text-xs">
                        <thead class="bg-gray-50 dark:bg-slate-800/90 text-gray-500 dark:text-slate-400 font-semibold uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-3.5">Peserta</th>
                                <th class="px-5 py-3.5">Kegiatan</th>
                                <th class="px-5 py-3.5">Waktu Presensi</th>
                                <th class="px-5 py-3.5">Metode</th>
                                <th class="px-5 py-3.5 text-center">Status Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700 text-gray-700 dark:text-slate-300">
                            @forelse($presensis as $presensi)
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/40 transition-colors">
                                    {{-- Info User --}}
                                    <td class="px-5 py-3.5">
                                        <div class="font-semibold text-gray-900 dark:text-white">
                                            {{ $presensi->user?->nama ?? 'Pengguna Dihapus' }}
                                        </div>
                                        <div class="text-[11px] font-mono text-gray-400 dark:text-slate-500">
                                            {{ $presensi->user?->nim ?? '-' }} • {{ $presensi->user?->divisi?->nama ?? 'Umum' }}
                                        </div>
                                    </td>

                                    {{-- Nama Kegiatan --}}
                                    <td class="px-5 py-3.5 font-medium text-gray-900 dark:text-slate-200">
                                        {{ $presensi->kegiatan?->judul ?? 'Kegiatan Tidak Ditemukan' }}
                                    </td>

                                    {{-- Waktu Masuk --}}
                                    <td class="px-5 py-3.5">
                                        <div>{{ optional($presensi->waktu_presensi)->translatedFormat('d M Y') ?? '-' }}</div>
                                        <div class="text-[11px] font-mono text-gray-400">{{ optional($presensi->waktu_presensi)->format('H:i:s') ?? '-' }} WIB</div>
                                    </td>

                                    {{-- Metode --}}
                                    <td class="px-5 py-3.5 capitalize">
                                        <span class="px-2 py-0.5 rounded bg-gray-100 dark:bg-slate-700 font-mono text-[11px]">
                                            {{ str_replace('_', ' ', $presensi->metode_presensi ?? 'manual') }}
                                        </span>
                                    </td>

                                    {{-- Status Badge --}}
                                    <td class="px-5 py-3.5 text-center">
                                        @php
                                            $status = strtolower($presensi->status_kehadiran);
                                            $badgeClass = match($status) {
                                                'hadir'     => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                                                'terlambat' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                                                'izin'      => 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                                                default     => 'bg-red-50 text-red-700 dark:bg-red-950/60 dark:text-red-300 border-red-200 dark:border-red-800',
                                            };
                                        @endphp

                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $badgeClass }} capitalize">
                                            {{ $presensi->status_kehadiran }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center text-gray-400 dark:text-slate-500">
                                        <span class="text-3xl block mb-2">📋</span>
                                        <p class="font-medium">Belum ada catatan riwayat presensi.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($presensis->hasPages())
                    <div class="px-5 py-3.5 border-t border-gray-100 dark:border-slate-700">
                        {{ $presensis->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>