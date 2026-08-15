<x-app-layout :$title>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight">
                    {{ __('Review Pengajuan Izin') }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Verifikasi dan validasi permohonan izin ketidakhadiran anggota
                </p>
            </div>

            <a href="{{ route('pengajuan-izin.index') }}"
               class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-200 text-xs font-semibold rounded-xl transition self-start sm:self-auto">
                &larr; Riwayat Izin Saya
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 font-poppins">

        {{-- Flash Alerts --}}
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

        {{-- Filter Tabs --}}
        <div class="mb-6 flex flex-wrap gap-2 border-b border-gray-200 dark:border-slate-700 pb-3">
            <a href="{{ route('pengajuan-izin.review', ['filter' => 'pending']) }}"
               class="px-4 py-2 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 {{ $filter === 'pending' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600' }}">
                <span>⏳</span> Menunggu Review
            </a>
            <a href="{{ route('pengajuan-izin.review', ['filter' => 'approved']) }}"
               class="px-4 py-2 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 {{ $filter === 'approved' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600' }}">
                <span>✅</span> Disetujui
            </a>
            <a href="{{ route('pengajuan-izin.review', ['filter' => 'rejected']) }}"
               class="px-4 py-2 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 {{ $filter === 'rejected' ? 'bg-red-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600' }}">
                <span>❌</span> Ditolak
            </a>
        </div>

        {{-- Content Card --}}
        <div class="bg-white dark:bg-slate-800 shadow-sm rounded-3xl p-6 sm:p-7 border border-gray-100 dark:border-slate-700">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-5 flex items-center gap-2">
                <span>📋</span> Daftar Pengajuan Izin
            </h3>

            @if($pengajuanList->isEmpty())
                <div class="text-center py-12 text-slate-400 dark:text-slate-500">
                    <span class="text-4xl block mb-2">📑</span>
                    <p class="text-xs font-medium">Tidak ada pengajuan izin yang sesuai dengan filter ini.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300 divide-y divide-gray-100 dark:divide-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-700/50 text-gray-500 dark:text-slate-400 uppercase text-[11px] font-semibold tracking-wider">
                            <tr>
                                <th class="py-3 px-4 rounded-l-xl">Pemohon</th>
                                <th class="py-3 px-4">Divisi & Jabatan</th>
                                <th class="py-3 px-4">Kegiatan</th>
                                <th class="py-3 px-4 text-center">Jenis</th>
                                <th class="py-3 px-4">Alasan & Berkas</th>
                                <th class="py-3 px-4">Status Alur</th>
                                <th class="py-3 px-4 text-center rounded-r-xl">Aksi Review</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach($pengajuanList as $p)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/30 transition">
                                    {{-- Pemohon --}}
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-gray-900 dark:text-white">
                                            {{ $p->user?->nama ?? 'Pengguna Dihapus' }}
                                        </div>
                                        <div class="text-[11px] font-mono text-slate-400">
                                            NIM: {{ $p->user?->nim ?? '-' }}
                                        </div>
                                    </td>

                                    {{-- Divisi & Jabatan --}}
                                    <td class="py-3.5 px-4">
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            {{ $p->user?->divisi?->nama ?? 'Tanpa Divisi' }}
                                        </div>
                                        <div class="text-[11px] text-slate-400">
                                            {{ $p->user?->jabatan?->nama ?? 'Anggota' }}
                                        </div>
                                    </td>

                                    {{-- Kegiatan --}}
                                    <td class="py-3.5 px-4">
                                        <div class="font-semibold text-gray-900 dark:text-white truncate max-w-xs">
                                            {{ $p->kegiatan?->judul ?? 'Kegiatan Dihapus' }}
                                        </div>
                                        <div class="text-[11px] text-slate-400">
                                            {{ $p->tanggal_izin ? \Carbon\Carbon::parse($p->tanggal_izin)->translatedFormat('d M Y') : '-' }}
                                        </div>
                                    </td>

                                    {{-- Jenis Izin --}}
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $p->jenis_izin === 'Sakit' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border-amber-200 dark:border-amber-800' : 'bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300 border-blue-200 dark:border-blue-800' }}">
                                            {{ $p->jenis_izin }}
                                        </span>
                                    </td>

                                    {{-- Alasan & Lampiran --}}
                                    <td class="py-3.5 px-4 max-w-xs">
                                        <p class="truncate text-gray-700 dark:text-slate-300 mb-1" title="{{ $p->alasan }}">
                                            {{ $p->alasan }}
                                        </p>
                                        <div class="flex items-center gap-1.5">
                                            @if($p->surat_izin)
                                                <a href="{{ asset('storage/' . $p->surat_izin) }}" target="_blank"
                                                   class="px-2 py-0.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 rounded text-[10px] font-medium text-slate-700 dark:text-slate-300 transition">
                                                    📄 Surat PDF
                                                </a>
                                            @endif
                                            @if($p->bukti)
                                                <a href="{{ asset('storage/' . $p->bukti) }}" target="_blank"
                                                   class="px-2 py-0.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 rounded text-[10px] font-medium text-slate-700 dark:text-slate-300 transition">
                                                    🖼️ Foto Bukti
                                                </a>
                                            @endif
                                            @if(!$p->surat_izin && !$p->bukti)
                                                <span class="text-gray-400 text-[10px]">Tanpa lampiran</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Status Alur --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="space-y-1 text-[11px]">
                                            <div class="flex items-center gap-1">
                                                <span class="text-slate-400 font-mono">Koor:</span>
                                                <span class="font-semibold {{ $p->status_koordinator === 'Approved' ? 'text-emerald-600' : ($p->status_koordinator === 'Rejected' ? 'text-red-600' : 'text-amber-600') }}">
                                                    {{ $p->status_koordinator }}
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <span class="text-slate-400 font-mono">Ranger:</span>
                                                <span class="font-semibold {{ $p->status_ranger === 'Approved' ? 'text-emerald-600' : ($p->status_ranger === 'Rejected' ? 'text-red-600' : 'text-amber-600') }}">
                                                    {{ $p->status_ranger }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Aksi Review --}}
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @php
                                            $user = auth()->user();
                                            $isReviewedByMe = false;

                                            if ($user->isKetuaOrWakil() && ! $user->isAdmin() && ! $user->isRanger()) {
                                                $isReviewedByMe = $p->status_koordinator !== 'Pending';
                                            } else {
                                                $isReviewedByMe = $p->status_ranger !== 'Pending';
                                            }
                                        @endphp

                                        @if($isReviewedByMe)
                                            <span class="inline-block px-2.5 py-1 rounded-xl text-[11px] font-semibold {{ $p->status === 'Approved' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300' : 'bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-300' }}">
                                                {{ $p->status === 'Approved' ? '✅ Selesai (Acc)' : '❌ Selesai (Tolak)' }}
                                            </span>
                                        @else
                                            <div class="flex items-center justify-center gap-1.5">
                                                {{-- Tombol Approve --}}
                                                <form action="{{ route('pengajuan-izin.approve', $p) }}" method="POST"
                                                      onsubmit="return confirm('Setujui permohonan izin dari {{ $p->user?->nama }}?')">
                                                    @csrf
                                                    <button type="submit"
                                                            class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold transition shadow-sm cursor-pointer">
                                                        Setujui
                                                    </button>
                                                </form>

                                                {{-- Tombol Reject --}}
                                                <form action="{{ route('pengajuan-izin.reject', $p) }}" method="POST"
                                                      onsubmit="return confirm('Tolak permohonan izin dari {{ $p->user?->nama }}?')">
                                                    @csrf
                                                    <button type="submit"
                                                            class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-semibold transition shadow-sm cursor-pointer">
                                                        Tolak
                                                    </button>
                                                </form>
                                            </div>
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