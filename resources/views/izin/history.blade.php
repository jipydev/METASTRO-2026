<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
                {{ __('Riwayat Pengajuan Izin') }}
            </h2>
            <a href="{{ route('izin.create') }}" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white font-bold rounded-xl text-sm transition flex items-center gap-2 shadow-sm">
                <span class="icon-[akar-icons--plus]"></span>
                Ajukan Izin Baru
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 font-poppins">
        {{-- Session Alerts --}}
        @if(session('success'))
            <div class="mb-4 p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-xl text-emerald-600 dark:text-emerald-400 flex items-center gap-3">
                <span class="icon-[akar-icons--circle-check] text-xl"></span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-rose-500/10 border border-rose-500/30 rounded-xl text-rose-600 dark:text-rose-400 flex items-center gap-3">
                <span class="icon-[akar-icons--circle-x] text-xl"></span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="bg-white dark:bg-slate-800 shadow-xl rounded-2xl p-6 border border-gray-100 dark:border-slate-700">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <span class="icon-[mdi--history] text-primary-500 text-2xl"></span>
                Daftar Status Pengajuan Izin Anda
            </h3>

            @if($pengajuanList->isEmpty())
                <div class="text-center py-12 text-slate-500 dark:text-slate-400">
                    <span class="icon-[mdi--clipboard-text-off-outline] text-5xl mb-2 block mx-auto opacity-50"></span>
                    Anda belum pernah melakukan pengajuan izin.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                        <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-700 dark:text-slate-200 uppercase text-xs">
                            <tr>
                                <th class="py-3 px-4 rounded-l-lg">Tanggal Pengajuan</th>
                                <th class="py-3 px-4">Rapat / Timeline</th>
                                <th class="py-3 px-4">Jenis</th>
                                <th class="py-3 px-4">Alasan</th>
                                <th class="py-3 px-4">Approval Koordinator</th>
                                <th class="py-3 px-4">Approval Ranger</th>
                                <th class="py-3 px-4 text-center rounded-r-lg">Status Akhir</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach($pengajuanList as $p)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition">
                                    <td class="py-3.5 px-4 font-medium text-slate-900 dark:text-white">
                                        {{ $p->created_at?->format('d M Y H:i') ?? '-' }}
                                    </td>
                                    <td class="py-3.5 px-4 font-semibold">
                                        <div class="text-slate-900 dark:text-white">{{ $p->rapat?->judul ?? 'Rapat' }}</div>
                                        <div class="text-xs text-slate-400 font-normal">{{ $p->tanggal_izin ? \Carbon\Carbon::parse($p->tanggal_izin)->format('d M Y') : '-' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $p->jenis_izin === 'Sakit' ? 'bg-amber-500/10 text-amber-600 border border-amber-500/20' : 'bg-blue-500/10 text-blue-600 border border-blue-500/20' }}">
                                            {{ $p->jenis_izin }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 max-w-xs truncate" title="{{ $p->alasan }}">
                                        {{ $p->alasan }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($p->status_koordinator === 'Pending')
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-600 border border-amber-500/20">
                                                Pending
                                            </span>
                                        @elseif($p->status_koordinator === 'Approved')
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 flex items-center gap-1 w-max">
                                                <span class="icon-[akar-icons--check]"></span> Disetujui
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-600 border border-rose-500/20 flex items-center gap-1 w-max">
                                                <span class="icon-[akar-icons--cross]"></span> Ditolak
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($p->status_ranger === 'Pending')
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-600 border border-amber-500/20">
                                                Pending
                                            </span>
                                        @elseif($p->status_ranger === 'Approved')
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 flex items-center gap-1 w-max">
                                                <span class="icon-[akar-icons--check]"></span> Disetujui
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-600 border border-rose-500/20 flex items-center gap-1 w-max">
                                                <span class="icon-[akar-icons--cross]"></span> Ditolak
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($p->status === 'Pending')
                                            <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-500 text-white shadow-sm">
                                                Menunggu Review
                                            </span>
                                        @elseif($p->status === 'Diproses')
                                            <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-blue-500 text-white shadow-sm">
                                                Proses Ranger
                                            </span>
                                        @elseif($p->status === 'Approved')
                                            <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-500 text-white shadow-sm">
                                                Disetujui ✅
                                            </span>
                                        @else
                                            <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-500 text-white shadow-sm">
                                                Ditolak ❌
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $pengajuanList->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
