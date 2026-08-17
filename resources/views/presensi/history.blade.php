<x-app-layout :$title>
    <div class="page-shell">
        <div class="page-wrap">

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="page-title">Riwayat Presensi</h1>
                    <p class="page-subtitle">Catatan kehadiran Anda di setiap kegiatan</p>
                </div>
            </div>

            <form method="GET" action="{{ route('presensi.history') }}" class="filter-bar">
                <div class="w-full grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-12 gap-3 items-end">
                    <div class="sm:col-span-2 xl:col-span-7">
                        <label for="filter-search" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Pencarian</label>
                        <input id="filter-search" type="text" name="search" value="{{ $search }}" placeholder="Cari nama kegiatan..."
                            class="form-control-app w-full">
                    </div>

                    <div class="xl:col-span-3">
                        <label for="filter-status" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Status</label>
                        <select id="filter-status" name="status" onchange="this.form.submit()" class="form-control-app w-full">
                            <option value="">Semua Status</option>
                            @foreach (['hadir' => 'Hadir', 'terlambat' => 'Terlambat', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpa' => 'Alpa'] as $key => $label)
                                <option value="{{ $key }}" @selected($statusFilter === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-3 xl:col-span-2 xl:pb-0.5">
                        <button type="submit" class="btn-filter">Cari</button>
                        @if ($search !== '' || $statusFilter !== '')
                            <a href="{{ route('presensi.history') }}"
                                class="text-xs font-semibold text-slate-500 hover:text-brand-600 dark:text-slate-400 whitespace-nowrap">
                                Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            <div class="table-card">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                        <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200/80 dark:border-slate-700 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            <tr>
                                <th class="px-5 py-3.5">Kegiatan</th>
                                <th class="px-5 py-3.5 text-center">Waktu Presensi</th>
                                <th class="px-5 py-3.5">Di scan / disetujui oleh</th>
                                <th class="px-5 py-3.5 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse ($presensis as $presensi)
                                @php
                                    $status = $presensi->status_tampilan;
                                    $badgeClass = match ($status) {
                                        'hadir' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                                        'terlambat' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border-rose-200 dark:border-rose-800',
                                        'izin' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                                        'sakit' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                                        'alpa' => 'bg-red-50 text-red-700 dark:bg-red-950/60 dark:text-red-300 border-red-200 dark:border-red-800',
                                        default => 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400 border-slate-200 dark:border-slate-600',
                                    };
                                    $waktuSumber = $presensi->isIzinAtauSakit()
                                        ? ($presensi->pengajuanIzin?->created_at ?? $presensi->jam_tap)
                                        : $presensi->jam_tap;
                                @endphp
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/20 transition">
                                    <td class="px-5 py-3.5">
                                        <div class="font-bold text-slate-900 dark:text-white">
                                            {{ $presensi->kegiatan?->nama ?? 'Kegiatan tidak ditemukan' }}
                                        </div>
                                        @if ($presensi->kegiatan?->tanggal)
                                            <div class="text-[11px] text-slate-400 mt-0.5">
                                                {{ \Carbon\Carbon::parse($presensi->kegiatan->tanggal)->locale('id')->isoFormat('D MMMM Y') }}
                                                @if ($presensi->kegiatan->waktu_mulai)
                                                    · {{ substr((string) $presensi->kegiatan->waktu_mulai, 0, 5) }} WIB
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                        @if ($waktuSumber)
                                            <div class="font-mono text-[11px] text-slate-700 dark:text-slate-200">{{ $waktuSumber->format('H:i') }} WIB</div>
                                            <div class="text-[11px] text-slate-400">{{ $waktuSumber->translatedFormat('d M Y') }}</div>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if ($presensi->isIzinAtauSakit())
                                            <div class="text-[10px] uppercase tracking-wider font-semibold text-slate-400">Disetujui oleh</div>
                                            <div class="font-medium text-slate-900 dark:text-white">{{ $presensi->pengajuanIzin?->reviewerRanger?->nama ?? '-' }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $presensi->pengajuanIzin?->reviewerRanger?->divisi?->nama ?? 'Ranger' }}</div>
                                        @elseif ($presensi->scanner)
                                            <div class="font-medium text-slate-900 dark:text-white">{{ $presensi->scanner->nama }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $presensi->scanner->divisi?->nama ?? 'Umum' }}</div>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="inline-flex flex-col items-center gap-0.5">
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $badgeClass }} capitalize">
                                                {{ str_replace('_', ' ', $status) }}
                                            </span>
                                            @if ($status === 'terlambat')
                                                <span class="text-[10px] font-medium text-rose-500 dark:text-rose-400">
                                                    {{ $presensi->menitTerlambat() }} menit
                                                </span>
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-12 text-center text-slate-400 dark:text-slate-500">
                                        <p class="font-medium">Anda belum memiliki catatan presensi.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($presensis->hasPages())
                    <div class="px-5 py-3.5 border-t border-slate-100 dark:border-slate-700">
                        {{ $presensis->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
