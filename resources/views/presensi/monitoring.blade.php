<x-app-layout :$title>
    <div class="max-w-6xl mx-auto min-h-screen bg-gray-50 md:bg-white dark:bg-slate-900 md:dark:bg-slate-800 p-4 md:p-6 font-poppins my-4 md:rounded-3xl md:shadow-sm border md:border-gray-100 dark:md:border-slate-700 transition-colors duration-200">
        
        {{-- Header & Tombol Kembali --}}
        <div class="mb-6 flex flex-col items-start">
            <a href="{{ route('dashboard') }}" 
               class="mb-2 inline-flex items-center gap-1.5 text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition font-medium text-xs sm:text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
                <span>Kembali ke Dashboard</span>
            </a>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
                Daftar Kehadiran Kegiatan
            </h1>
        </div>

        {{-- Filter Section --}}
        <div class="mb-6 space-y-4">
            
            {{-- Dropdown Pilih Kegiatan --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <span class="text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Pilih Jadwal:</span>
                
                <form action="{{ request()->url() }}" method="GET" class="flex-1 max-w-sm" x-data x-ref="formFilter">
                    @if($statusFilter)
                        <input type="hidden" name="status" value="{{ $statusFilter }}">
                    @endif
                    @if($divisiFilter)
                        <input type="hidden" name="divisi_id" value="{{ $divisiFilter }}">
                    @endif

                    <select name="kegiatan_id" 
                            @change="$refs.formFilter.submit()"
                            class="w-full rounded-xl border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold py-2 px-3 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm cursor-pointer outline-none">
                        @forelse($kegiatans as $k)
                            <option value="{{ $k->id }}" {{ ($selectedKegiatan && $selectedKegiatan->id == $k->id) ? 'selected' : '' }}>
                                {{ $k->judul }} ({{ \Carbon\Carbon::parse($k->tanggal)->locale('id')->isoFormat('D MMM Y') }})
                            </option>
                        @empty
                            <option value="">-- Belum Ada Jadwal Kegiatan --</option>
                        @endforelse
                    </select>
                </form>
            </div>

            {{-- Filter Status Pills (Horizontal Scrollable) --}}
            <div class="flex items-center gap-2 overflow-x-auto pb-2 hide-scrollbar text-xs font-semibold">
                <span class="text-gray-400 dark:text-slate-500 mr-1 hidden sm:inline">Filter Status:</span>

                {{-- Pill Semua --}}
                <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" 
                   class="px-3.5 py-1.5 rounded-full whitespace-nowrap transition {{ !$statusFilter ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600' }}">
                    Semua
                </a>

                {{-- Status Lain --}}
                @foreach(['hadir' => 'Hadir', 'terlambat' => 'Terlambat', 'izin' => 'Izin', 'alpa' => 'Alpa', 'belum_hadir' => 'Belum Hadir'] as $key => $label)
                    <a href="{{ request()->fullUrlWithQuery(['status' => $key]) }}" 
                       class="px-3.5 py-1.5 rounded-full whitespace-nowrap transition {{ $statusFilter === $key ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Tabel Matriks Kehadiran --}}
        <div class="overflow-x-auto border border-gray-100 dark:border-slate-700 rounded-2xl">
            <table class="w-full text-left border-collapse text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/90 text-gray-500 dark:text-slate-400 uppercase text-[11px] font-bold tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Pengguna</th>
                        <th class="py-3.5 px-4">Divisi & Jabatan</th>
                        <th class="py-3.5 px-4 text-center">Waktu Presensi</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @forelse($usersData as $row)
                        @php
                            $badgeClass = match($row['status']) {
                                'hadir'       => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                                'terlambat'   => 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                                'izin'        => 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                                'alpa'        => 'bg-red-50 text-red-700 dark:bg-red-950/60 dark:text-red-300 border-red-200 dark:border-red-800',
                                default       => 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400 border-slate-200 dark:border-slate-600',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-700/30 transition">
                            {{-- Nama & NIM --}}
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-bold text-gray-900 dark:text-white">{{ $row['nama'] }}</div>
                                <div class="text-[11px] font-mono text-gray-400">NIM: {{ $row['nim'] }}</div>
                            </td>

                            {{-- Divisi & Jabatan --}}
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900 dark:text-slate-200">{{ $row['divisi'] }}</div>
                                <div class="text-[11px] text-gray-400">{{ $row['jabatan'] }}</div>
                            </td>

                            {{-- Waktu Presensi --}}
                            <td class="py-3.5 px-4 text-center whitespace-nowrap font-mono text-[11px]">
                                {{ $row['waktu_presensi'] !== '-' ? $row['waktu_presensi'] . ' WIB' : '-' }}
                            </td>

                            {{-- Status Badge --}}
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold border uppercase tracking-wider {{ $badgeClass }}">
                                    {{ str_replace('_', ' ', $row['status']) }}
                                </span>
                            </td>

                            {{-- Keterangan --}}
                            <td class="py-3.5 px-4 max-w-xs truncate text-gray-500 dark:text-slate-400" title="{{ $row['keterangan'] }}">
                                {{ $row['keterangan'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-gray-400 dark:text-slate-500">
                                <span class="text-3xl block mb-1">📋</span>
                                <p class="font-medium">Tidak ada data kehadiran yang sesuai dengan filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>