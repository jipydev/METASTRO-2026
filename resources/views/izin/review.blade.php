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

        {{-- Filter Tabs --}}
        <div class="mb-6 flex flex-wrap gap-2 border-b border-slate-200 dark:border-slate-700 pb-3">
            <a href="{{ route('izin.review', ['filter' => 'pending']) }}" class="px-4 py-2 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 {{ $filter === 'pending' ? 'bg-primary-500 text-white shadow-md' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600' }}">
                <span class="icon-[akar-icons--clock]"></span> Menunggu Review
            </a>
            @if(auth()->user()->hasRole('Admin'))
                <a href="{{ route('izin.review', ['filter' => 'limbo']) }}" class="px-4 py-2 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 {{ $filter === 'limbo' ? 'bg-amber-500 text-white shadow-md' : 'bg-amber-500/10 text-amber-600 dark:text-amber-400 hover:bg-amber-500/20' }}">
                    <span class="icon-[akar-icons--warning] text-sm"></span> Terkendala / Limbo
                </a>
            @endif
            <a href="{{ route('izin.review', ['filter' => 'approved']) }}" class="px-4 py-2 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 {{ $filter === 'approved' ? 'bg-emerald-500 text-white shadow-md' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600' }}">
                <span class="icon-[akar-icons--check]"></span> Disetujui
            </a>
            <a href="{{ route('izin.review', ['filter' => 'rejected']) }}" class="px-4 py-2 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 {{ $filter === 'rejected' ? 'bg-rose-500 text-white shadow-md' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600' }}">
                <span class="icon-[akar-icons--cross]"></span> Ditolak
            </a>
            <a href="{{ route('izin.review', ['filter' => 'all']) }}" class="px-4 py-2 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 {{ $filter === 'all' ? 'bg-slate-800 dark:bg-slate-200 text-white dark:text-slate-900 shadow-md' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600' }}">
                <span class="icon-[akar-icons--grid]"></span> Semua Data
            </a>
        </div>

        <div class="bg-white dark:bg-slate-800 shadow-xl rounded-2xl p-6 border border-gray-100 dark:border-slate-700">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <span class="icon-[mdi--clipboard-check-outline] text-primary-500 text-2xl"></span>
                Daftar Pengajuan Izin Panitia
            </h3>

            @if($pengajuanList->isEmpty())
                <div class="text-center py-12 text-slate-500 dark:text-slate-400">
                    <span class="icon-[mdi--check-all] text-5xl mb-2 block mx-auto opacity-50"></span>
                    Tidak ada pengajuan izin yang sesuai dengan kriteria filter saat ini.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                        <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-700 dark:text-slate-200 uppercase text-xs">
                            <tr>
                                <th class="py-3 px-4 rounded-l-lg">Pemohon</th>
                                <th class="py-3 px-4">Divisi & Jabatan</th>
                                <th class="py-3 px-4">Waktu Dikirim</th>
                                <th class="py-3 px-4">Rapat / Timeline</th>
                                <th class="py-3 px-4">Jenis</th>
                                <th class="py-3 px-4">Diagnostik Alur</th>
                                <th class="py-3 px-4">Lampiran</th>
                                <th class="py-3 px-4 text-center rounded-r-lg">Aksi Review</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach($pengajuanList as $p)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition">
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                            <span>{{ $p->user?->name ?? 'User Terhapus' }}</span>
                                            @if($p->user && !$p->user->status_aktif)
                                                <span class="px-1.5 py-0.5 rounded text-[10px] bg-rose-500/10 text-rose-600 border border-rose-500/20 font-mono">Non-Aktif</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-slate-400">NIM: {{ $p->user?->nim ?? '-' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($p->user?->divisi)
                                            <div class="font-medium text-slate-800 dark:text-slate-200">{{ $p->user->divisi->nama_divisi }}</div>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                                ⚠️ Tanpa Divisi
                                            </span>
                                        @endif
                                        <div class="text-xs text-slate-400">{{ $p->user?->jabatan?->nama_jabatan ?? '-' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="font-medium text-slate-900 dark:text-white flex items-center gap-1.5">
                                            <span class="icon-[akar-icons--clock] text-slate-400 text-xs"></span>
                                            <span>{{ $p->created_at ? $p->created_at->format('d M Y, H:i') . ' WIB' : '-' }}</span>
                                        </div>
                                        @if($p->created_at)
                                            <div class="text-[11px] text-slate-400 font-normal">
                                                {{ \Carbon\Carbon::parse($p->created_at)->locale('id')->diffForHumans() }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-semibold text-slate-900 dark:text-white">{{ $p->rapat?->judul ?? 'Rapat' }}</div>
                                        <div class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($p->tanggal_izin)->format('d M Y') }}</div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $p->jenis_izin === 'Sakit' ? 'bg-amber-500/10 text-amber-600 border border-amber-500/20' : 'bg-blue-500/10 text-blue-600 border border-blue-500/20' }}">
                                            {{ $p->jenis_izin }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="flex flex-col gap-1 text-xs">
                                            {{-- Status Koordinator --}}
                                            <div class="flex items-center gap-1">
                                                <span class="text-slate-400 font-mono">Koor:</span>
                                                @if($p->status_koordinator === 'Approved')
                                                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold">✓ Disetujui</span>
                                                    @if($p->reviewed_at_koordinator)
                                                        <span class="text-[10px] text-slate-400">({{ \Carbon\Carbon::parse($p->reviewed_at_koordinator)->format('d/m H:i') }})</span>
                                                    @endif
                                                @elseif($p->status_koordinator === 'Rejected')
                                                    <span class="text-rose-600 dark:text-rose-400 font-semibold">✗ Ditolak</span>
                                                    @if($p->reviewed_at_koordinator)
                                                        <span class="text-[10px] text-slate-400">({{ \Carbon\Carbon::parse($p->reviewed_at_koordinator)->format('d/m H:i') }})</span>
                                                    @endif
                                                @else
                                                    <span class="text-amber-600 dark:text-amber-400 font-semibold">⌛ Pending</span>
                                                @endif
                                            </div>

                                            {{-- Status Ranger --}}
                                            <div class="flex items-center gap-1">
                                                <span class="text-slate-400 font-mono">Ranger:</span>
                                                @if($p->status_ranger === 'Approved')
                                                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold">✓ Disetujui</span>
                                                    @if($p->reviewed_at_ranger)
                                                        <span class="text-[10px] text-slate-400">({{ \Carbon\Carbon::parse($p->reviewed_at_ranger)->format('d/m H:i') }})</span>
                                                    @endif
                                                @elseif($p->status_ranger === 'Rejected')
                                                    <span class="text-rose-600 dark:text-rose-400 font-semibold">✗ Ditolak</span>
                                                    @if($p->reviewed_at_ranger)
                                                        <span class="text-[10px] text-slate-400">({{ \Carbon\Carbon::parse($p->reviewed_at_ranger)->format('d/m H:i') }})</span>
                                                    @endif
                                                @else
                                                    <span class="text-amber-600 dark:text-amber-400 font-semibold">⌛ Pending</span>
                                                @endif
                                            </div>

                                            {{-- Limbo Warning --}}
                                            @if(!$p->user?->divisi_id && $p->status === 'Pending')
                                                <span class="text-[10px] text-rose-500 font-bold bg-rose-50 dark:bg-rose-950/40 p-1 rounded border border-rose-200 dark:border-rose-900">
                                                    ⚠️ Tersangkut (Tanpa Divisi)
                                                </span>
                                            @elseif($p->status_koordinator === 'Pending' && $p->status === 'Pending')
                                                <span class="text-[10px] text-amber-600 dark:text-amber-400 font-medium">
                                                    Belum direview Koordinator
                                                </span>
                                            @endif
                                        </div>
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
                                        @php
                                            $currentUser = auth()->user();
                                            $hasAccepted = false;
                                            $hasDenied = false;

                                            if ($currentUser->isKoordinator() && $p->user?->divisi_id == $currentUser->divisi_id && !$p->user?->hasRole('Ranger') && !$p->user?->hasRole('Stakeholder')) {
                                                if ($p->status_koordinator === 'Approved') $hasAccepted = true;
                                                elseif ($p->status_koordinator === 'Rejected') $hasDenied = true;
                                            } else {
                                                if ($p->status_ranger === 'Approved') $hasAccepted = true;
                                                elseif ($p->status_ranger === 'Rejected') $hasDenied = true;
                                            }
                                        @endphp

                                        @if($hasAccepted)
                                            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">
                                                <span class="icon-[akar-icons--check]"></span> Anda Telah Accept
                                            </span>
                                        @elseif($hasDenied)
                                            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/30">
                                                <span class="icon-[akar-icons--cross]"></span> Anda Telah Denied
                                            </span>
                                        @else
                                            <div class="flex items-center justify-center gap-2">
                                                <form action="{{ route('izin.approve', $p->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menyetujui pengajuan izin ini?')" class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition flex items-center gap-1 shadow-sm cursor-pointer">
                                                        <span class="icon-[akar-icons--check]"></span> Accept
                                                    </button>
                                                </form>

                                                <form action="{{ route('izin.reject', $p->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menolak pengajuan izin ini?')" class="px-3 py-1.5 bg-rose-500 hover:bg-rose-600 text-white rounded-xl text-xs font-bold transition flex items-center gap-1 shadow-sm cursor-pointer">
                                                        <span class="icon-[akar-icons--cross]"></span> Denied
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

                <div class="mt-4">
                    {{ $pengajuanList->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>