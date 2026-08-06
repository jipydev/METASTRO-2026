@props(['item'])

<tr x-data="{ openBukti: false, openPdf: false, openImg: false }" 
    class="flex flex-col md:table-row border-b md:border-b border-gray-100 dark:border-slate-700/80 hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors bg-white dark:bg-slate-800 mb-4 md:mb-0 rounded-lg md:rounded-none shadow-sm md:shadow-none p-4 md:p-0">
    
    <!-- Kolom Panitia -->
    <td class="py-2 md:py-3 md:px-4 flex items-center justify-between md:table-cell gap-4">
        <span class="md:hidden text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Panitia</span>
        <div class="flex items-center gap-4 text-right md:text-left">
            @php $inisial = substr($item['nama'], 0, 1); @endphp
            <div class="w-10 h-10 rounded-full bg-primary-50 dark:bg-primary-950/60 text-primary-600 dark:text-primary-400 flex items-center justify-center font-semibold text-lg shrink-0 border border-primary-100 dark:border-primary-900">
                {{ strtoupper($inisial) }}
            </div>
            <span class="font-semibold text-gray-900 dark:text-slate-100 text-[15px]">{{ $item['nama'] }}</span>
        </div>
    </td>

    <!-- Kolom Divisi -->
    <td class="py-2 md:py-3 md:px-4 flex items-center justify-between md:table-cell text-slate-500 dark:text-slate-400 text-[15px]">
        <span class="md:hidden text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Divisi</span>
        {{ $item['divisi'] }}
    </td>

    <!-- Kolom Jabatan -->
    <td class="py-2 md:py-3 md:px-4 flex items-center justify-between md:table-cell text-slate-500 dark:text-slate-400 text-[15px]">
        <span class="md:hidden text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Jabatan</span>
        {{ $item['jabatan'] }}
    </td>

    <!-- Kolom Jam Tap -->
    <td class="py-2 md:py-3 md:px-4 flex items-center justify-between md:table-cell text-gray-900 dark:text-slate-100 font-medium text-[15px]">
        <span class="md:hidden text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Jam Tap</span>
        {{ $item['jam_tap'] }}
    </td>

    <!-- Kolom Tanggal -->
    <td class="py-2 md:py-3 md:px-4 flex items-center justify-between md:table-cell text-slate-500 dark:text-slate-400 text-[15px]">
        <span class="md:hidden text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Tanggal</span>
        {{ $item['tanggal'] }}
    </td>

    <!-- Kolom Status -->
    <td class="py-2 md:py-3 md:px-4 flex items-center justify-between md:table-cell">
        <span class="md:hidden text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Status</span>
        @php
            $statusStyle = match($item['status']) {
                'Hadir' => ['bg' => 'bg-emerald-100 dark:bg-emerald-950/60', 'text' => 'text-emerald-700 dark:text-emerald-400', 'dot' => 'text-emerald-500'],
                'Telat' => ['bg' => 'bg-amber-100 dark:bg-amber-950/60', 'text' => 'text-amber-700 dark:text-amber-400', 'dot' => 'text-amber-500'],
                'Tidak Hadir' => ['bg' => 'bg-blue-100 dark:bg-blue-950/60', 'text' => 'text-blue-700 dark:text-blue-400', 'dot' => 'text-blue-500'],
                'Alpha' => ['bg' => 'bg-red-100 dark:bg-red-950/60', 'text' => 'text-red-700 dark:text-red-400', 'dot' => 'text-red-500'],
                default => ['bg' => 'bg-gray-100 dark:bg-slate-700', 'text' => 'text-gray-700 dark:text-slate-300', 'dot' => 'text-gray-500'],
            };
        @endphp
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[13px] font-semibold {{ $statusStyle['bg'] }} {{ $statusStyle['text'] }}">
            <span class="{{ $statusStyle['dot'] }} text-lg leading-none">&bull;</span> 
            {{ $item['status'] }}
        </span>
    </td>

    <!-- Kolom Scanned By (Aksi diganti Scanned By pada tabel, jadi kita tampilkan langsung disini atau biarkan) -->
    <td class="py-2 md:py-3 md:px-4 flex items-center justify-between md:table-cell text-slate-500 dark:text-slate-400 text-[15px]">
        <span class="md:hidden text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Scanned By</span>
        {{ $item['scanned_by'] ?? '-' }}
    </td>
</tr>