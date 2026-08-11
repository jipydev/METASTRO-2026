<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            {{ __('Review Pengajuan Izin') }}
        </h2>
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
                <span class="icon-[mdi--clipboard-check-outline] text-primary-500 text-2xl"></span>
                Daftar Pengajuan Izin Menunggu Review Anda
            </h3>

            @if($pengajuanList->isEmpty())
                <div class="text-center py-12 text-slate-500 dark:text-slate-400">
                    <span class="icon-[mdi--check-all] text-5xl mb-2 block mx-auto opacity-50"></span>
                    Tidak ada pengajuan izin yang memerlukan review saat ini.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                        <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-700 dark:text-slate-200 uppercase text-xs">
                            <tr>
                                <th class="py-3 px-4 rounded-l-lg">Pemohon</th>
                                <th class="py-3 px-4">Divisi & Jabatan</th>
                                <th class="py-3 px-4">Tanggal Izin</th>
                                <th class="py-3 px-4">Jenis</th>
                                <th class="py-3 px-4">Alasan Detail</th>
                                <th class="py-3 px-4">Lampiran</th>
                                <th class="py-3 px-4 text-center rounded-r-lg">Aksi Review</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach($pengajuanList as $p)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition">
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-900 dark:text-white">{{ $p->user?->name ?? '-' }}</div>
                                        <div class="text-xs text-slate-400">NIM: {{ $p->user?->nim ?? '-' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-medium text-slate-800 dark:text-slate-200">{{ $p->user?->divisi?->nama_divisi ?? '-' }}</div>
                                        <div class="text-xs text-slate-400">{{ $p->user?->jabatan?->nama_jabatan ?? '-' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 font-semibold">
                                        {{ \Carbon\Carbon::parse($p->tanggal_izin)->format('d M Y') }}
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
                                        <div class="flex flex-col gap-1 text-xs">
                                            @if($p->surat_izin)
                                                <a href="{{ asset('storage/' . $p->surat_izin) }}" target="_blank" class="text-primary-500 hover:underline flex items-center gap-1">
                                                    <span class="icon-[grommet-icons--document-pdf]"></span> Surat (PDF)
                                                </a>
                                            @endif
                                            @if($p->bukti)
                                                <a href="{{ asset('storage/' . $p->bukti) }}" target="_blank" class="text-emerald-500 hover:underline flex items-center gap-1">
                                                    <span class="icon-[grommet-icons--image]"></span> Bukti (Foto)
                                                </a>
                                            @endif
                                            @if(!$p->surat_izin && !$p->bukti)
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <form action="{{ route('izin.approve', $p->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menyetujui pengajuan izin ini?')" class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition flex items-center gap-1 shadow-sm">
                                                    <span class="icon-[akar-icons--check]"></span> Accept
                                                </button>
                                            </form>

                                            <form action="{{ route('izin.reject', $p->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menolak pengajuan izin ini?')" class="px-3 py-1.5 bg-rose-500 hover:bg-rose-600 text-white rounded-xl text-xs font-bold transition flex items-center gap-1 shadow-sm">
                                                    <span class="icon-[akar-icons--cross]"></span> Denied
                                                </button>
                                            </form>
                                        </div>
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
