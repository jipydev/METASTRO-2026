<x-app-layout :$title>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight">
                    {{ __('Riwayat Pengajuan Izin') }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Pantau status verifikasi pengajuan izin ketidakhadiran kegiatan Anda
                </p>
            </div>
            
            <a href="{{ route('pengajuan-izin.create') }}" 
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-xs shadow-sm transition self-start sm:self-auto">
                <span>+</span> Ajukan Izin Baru
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 font-poppins">
        
        {{-- Flash Notification --}}
        @if(session('success'))
            <div class="mb-5 p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-2xl text-emerald-700 dark:text-emerald-300 text-xs font-semibold flex items-center gap-2.5">
                <span>✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-5 p-4 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 rounded-2xl text-red-600 dark:text-red-400 text-xs font-semibold flex items-center gap-2.5">
                <span>❌</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Tabel Card --}}
        <div class="bg-white dark:bg-slate-800 shadow-sm rounded-3xl p-6 sm:p-7 border border-gray-100 dark:border-slate-700">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-5 flex items-center gap-2">
                <span>📋</span> Status Pengajuan Izin
            </h3>

            @if($pengajuanList->isEmpty())
                <div class="text-center py-12 text-slate-400 dark:text-slate-500">
                    <span class="text-4xl block mb-2">📑</span>
                    <p class="text-xs font-medium">Anda belum pernah membuat pengajuan izin.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300 divide-y divide-gray-100 dark:divide-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-700/50 text-gray-500 dark:text-slate-400 uppercase text-[11px] font-semibold tracking-wider">
                            <tr>
                                <th class="py-3 px-4 rounded-l-xl">Tanggal Pengajuan</th>
                                <th class="py-3 px-4">Kegiatan / Agenda</th>
                                <th class="py-3 px-4 text-center">Jenis</th>
                                <th class="py-3 px-4">Alasan</th>
                                <th class="py-3 px-4">Berkas</th>
                                <th class="py-3 px-4 text-center">Approval Koordinator</th>
                                <th class="py-3 px-4 text-center">Approval Ranger</th>
                                <th class="py-3 px-4 text-center rounded-r-xl">Status Akhir</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach($pengajuanList as $p)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/30 transition">
                                    {{-- Waktu Submit --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap font-medium text-gray-900 dark:text-white">
                                        {{ $p->created_at?->translatedFormat('d M Y H:i') ?? '-' }}
                                    </td>

                                    {{-- Info Kegiatan --}}
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-gray-900 dark:text-white truncate max-w-xs">
                                            {{ $p->kegiatan?->judul ?? 'Kegiatan Telah Dihapus' }}
                                        </div>
                                        <div class="text-[11px] text-slate-400 font-normal">
                                            {{ $p->tanggal_izin ? \Carbon\Carbon::parse($p->tanggal_izin)->translatedFormat('d M Y') : '-' }}
                                        </div>
                                    </td>

                                    {{-- Jenis Izin --}}
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $p->jenis_izin === 'Sakit' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border-amber-200 dark:border-amber-800' : 'bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300 border-blue-200 dark:border-blue-800' }}">
                                            {{ $p->jenis_izin }}
                                        </span>
                                    </td>

                                    {{-- Alasan --}}
                                    <td class="py-3.5 px-4 max-w-xs truncate" title="{{ $p->alasan }}">
                                        {{ $p->alasan }}
                                    </td>

                                    {{-- Berkas Lampiran --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="flex items-center gap-1.5">
                                            @if($p->surat_izin)
                                                <a href="{{ asset('storage/' . $p->surat_izin) }}" target="_blank"
                                                   class="px-2 py-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 rounded text-[11px] font-medium text-slate-700 dark:text-slate-300 transition"
                                                   title="Lihat Surat PDF">
                                                    📄 Surat
                                                </a>
                                            @endif

                                            @if($p->bukti)
                                                <a href="{{ asset('storage/' . $p->bukti) }}" target="_blank"
                                                   class="px-2 py-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 rounded text-[11px] font-medium text-slate-700 dark:text-slate-300 transition"
                                                   title="Lihat Bukti Gambar">
                                                    🖼️ Bukti
                                                </a>
                                            @endif

                                            @if(!$p->surat_izin && !$p->bukti)
                                                <span class="text-gray-400 text-[11px]">—</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Status Koordinator --}}
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @if($p->status_koordinator === 'Pending')
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                                Pending
                                            </span>
                                        @elseif($p->status_koordinator === 'Approved')
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                                Disetujui
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-300 border border-red-200 dark:border-red-800">
                                                Ditolak
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Status Ranger --}}
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @if($p->status_ranger === 'Pending')
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                                Pending
                                            </span>
                                        @elseif($p->status_ranger === 'Approved')
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                                Disetujui
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-300 border border-red-200 dark:border-red-800">
                                                Ditolak
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Status Akhir --}}
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @if($p->status === 'Pending')
                                            <span class="px-3 py-1 rounded-xl text-xs font-bold bg-amber-500 text-white shadow-sm">
                                                Menunggu Review
                                            </span>
                                        @elseif($p->status === 'Diproses')
                                            <span class="px-3 py-1 rounded-xl text-xs font-bold bg-indigo-500 text-white shadow-sm">
                                                Proses Ranger
                                            </span>
                                        @elseif($p->status === 'Approved')
                                            <span class="px-3 py-1 rounded-xl text-xs font-bold bg-emerald-600 text-white shadow-sm">
                                                Disetujui ✅
                                            </span>
                                        @else
                                            <span class="px-3 py-1 rounded-xl text-xs font-bold bg-red-600 text-white shadow-sm">
                                                Ditolak ❌
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($pengajuanList->hasPages())
                    <div class="mt-5 pt-3 border-t border-gray-100 dark:border-slate-700">
                        {{ $pengajuanList->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>