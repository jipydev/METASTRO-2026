<x-app-layout :$title>
    <div class="page-shell">
        <div class="page-wrap">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="page-title">Riwayat Presensi</h1>
                    <p class="page-subtitle">
                        {{ auth()->user()->canScanPresensi() ? 'Log presensi seluruh peserta dan panitia' : 'Catatan kehadiran pribadi Anda' }}
                    </p>
                </div>
            </div>

            <form method="GET" action="{{ route('presensi.history') }}" class="filter-bar">
                <div class="flex-1 min-w-0 w-full">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIM, atau kegiatan..."
                        class="form-control-app w-full">
                </div>
                <div>
                    <select name="status" class="form-control-app">
                        <option value="">Semua Status</option>
                        <option value="hadir" @selected(request('status') === 'hadir')>Hadir</option>
                        <option value="terlambat" @selected(request('status') === 'terlambat')>Terlambat</option>
                        <option value="izin" @selected(request('status') === 'izin')>Izin</option>
                        <option value="sakit" @selected(request('status') === 'sakit')>Sakit</option>
                        <option value="alpa" @selected(request('status') === 'alpa')>Alpa</option>
                    </select>
                </div>
                <button type="submit" class="btn-filter">Filter</button>
                @if (request()->hasAny(['search', 'status']))
                    <a href="{{ route('presensi.history') }}" class="text-xs text-slate-500 hover:text-slate-700 dark:text-slate-400">Reset</a>
                @endif
            </form>

            <div class="table-card">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700 text-left text-xs">
                        <thead class="bg-gray-50 dark:bg-slate-800/90 text-gray-500 dark:text-slate-400 font-semibold uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-3.5">Peserta</th>
                                <th class="px-5 py-3.5">Kegiatan</th>
                                <th class="px-5 py-3.5">Waktu Presensi</th>
                                <th class="px-5 py-3.5">Di scan / disetujui oleh</th>
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
                                        {{ $presensi->kegiatan?->nama ?? 'Kegiatan Tidak Ditemukan' }}
                                    </td>

                                    {{-- Waktu --}}
                                    <td class="px-5 py-3.5">
                                        @php
                                            $waktuIzin = $presensi->isIzinAtauSakit()
                                                ? ($presensi->pengajuanIzin?->created_at ?? $presensi->jam_tap)
                                                : $presensi->jam_tap;
                                        @endphp
                                        @if ($waktuIzin)
                                            <div>{{ $waktuIzin->translatedFormat('d M Y') }}</div>
                                            <div class="text-[11px] font-mono text-gray-400">{{ $waktuIzin->format('H:i:s') }} WIB</div>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>

                                    {{-- Di scan / disetujui oleh --}}
                                    <td class="px-5 py-3.5">
                                        @if ($presensi->isIzinAtauSakit())
                                            <div class="text-[10px] uppercase tracking-wider font-semibold text-slate-400">Disetujui oleh</div>
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $presensi->pengajuanIzin?->reviewerRanger?->nama ?? '-' }}</div>
                                            <div class="text-[11px] text-gray-400">{{ $presensi->pengajuanIzin?->reviewerRanger?->divisi?->nama ?? 'Ranger' }}</div>
                                        @elseif ($presensi->scanner)
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $presensi->scanner->nama }}</div>
                                            <div class="text-[11px] text-gray-400">{{ $presensi->scanner->divisi?->nama ?? 'QR Code' }}</div>
                                        @else
                                            <span class="px-2 py-0.5 rounded bg-gray-100 dark:bg-slate-700 font-mono text-[11px]">Manual</span>
                                        @endif
                                    </td>

                                    {{-- Status Badge --}}
                                    <td class="px-5 py-3.5 text-center">
                                        @php
                                            $status = strtolower($presensi->status_tampilan);
                                            $badgeClass = match($status) {
                                                'hadir'     => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                                                'terlambat' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                                                'izin'      => 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                                                'sakit'     => 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                                                default     => 'bg-red-50 text-red-700 dark:bg-red-950/60 dark:text-red-300 border-red-200 dark:border-red-800',
                                            };
                                        @endphp

                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $badgeClass }} capitalize">
                                            {{ $presensi->status_tampilan }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center text-gray-400 dark:text-slate-500">
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