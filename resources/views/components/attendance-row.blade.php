@props(['item'])

<tr x-data="{ openBukti: false, openPdf: false, openImg: false }" 
    class="flex flex-col md:table-row border-b md:border-b border-gray-100 dark:border-slate-700/80 hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors bg-white dark:bg-slate-800 mb-4 md:mb-0 rounded-lg md:rounded-none shadow-sm md:shadow-none p-4 md:p-0">
    
    <!-- Kolom Panitia -->
    <td class="py-2 md:py-3 md:px-4 flex items-center justify-between md:table-cell gap-4">
        <span class="md:hidden text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Panitia</span>
        <div class="flex items-center gap-4 text-right md:text-left">
            @php $inisial = substr($item['nama'], 0, 1); @endphp
            <div class="w-10 h-10 rounded-full bg-primary-50 dark:bg-primary-950/60 text-primary-600 dark:text-primary-400 flex items-center justify-center font-semibold text-lg shrink-0">
                {{ $inisial }}
            </div>
            <span class="font-semibold text-gray-900 dark:text-slate-100 text-[15px]">{{ $item['nama'] }}</span>
        </div>
    </td>

    <!-- Kolom Divisi -->
    <td class="py-2 md:py-3 md:px-4 flex items-center justify-between md:table-cell text-slate-500 dark:text-slate-400 text-[15px]">
        <span class="md:hidden text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Divisi</span>
        {{ $item['divisi'] }}
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

    <!-- Kolom Aksi -->
    <td class="py-3 md:px-4 flex items-center justify-between md:table-cell mt-2 md:mt-0 border-t md:border-0 border-gray-100 dark:border-slate-700">
        <span class="md:hidden text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Aksi</span>
        <button type="button" @click="openBukti = true" class="px-3 py-1.5 bg-primary-500 hover:bg-primary-600 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm cursor-pointer">
            Lihat Bukti
        </button>
    </td>

    <!-- ========================================== -->
    <!-- 1. TELEPORT MODAL UTAMA                    -->
    <!-- ========================================== -->
    <template x-teleport="body">
        <div x-show="openBukti" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm" style="display: none;" x-transition>
            <div @click.outside="openBukti = false" class="bg-white dark:bg-slate-800 rounded-2xl p-6 w-full max-w-sm shadow-xl relative border border-gray-100 dark:border-slate-700 font-poppins">
                <button type="button" @click="openBukti = false" class="absolute top-4 right-4 text-gray-400 dark:text-slate-400 hover:text-gray-700 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 border-b border-gray-100 dark:border-slate-700 pb-2">
                    {{ $item['status'] === 'Tidak Hadir' ? 'Detail Tidak Hadir' : 'Bukti Kehadiran' }}
                </h3>
                
                <div class="space-y-3 text-sm text-gray-600 dark:text-slate-300">
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-500 dark:text-slate-400">Hari, Tanggal</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ $item['hari'] ?? 'Senin' }}, {{ $item['tanggal'] }}</span>
                    </div>

                    @if($item['status'] === 'Tidak Hadir')
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-500 dark:text-slate-400">Jenis Izin</span>
                            <span class="text-gray-900 dark:text-white font-semibold">{{ $item['jenis_izin'] ?? '-' }}</span>
                        </div>
                        <div class="pt-4 grid grid-cols-2 gap-3 border-t border-gray-100 dark:border-slate-700 mt-2">
                            <button type="button" @click="openPdf = true" class="w-full py-2 bg-primary-50 dark:bg-slate-700 text-primary-600 dark:text-primary-400 font-semibold rounded-lg border border-primary-200 dark:border-slate-600 hover:bg-primary-100 transition flex items-center justify-center gap-1 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Surat Izin
                            </button>
                            <button type="button" @click="openImg = true" class="w-full py-2 bg-emerald-50 dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 font-semibold rounded-lg border border-emerald-200 dark:border-slate-600 hover:bg-emerald-100 transition flex items-center justify-center gap-1 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Bukti Foto
                            </button>
                        </div>
                    @else
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-500 dark:text-slate-400">Jam Hadir</span>
                            <span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ $item['jam_tap'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-500 dark:text-slate-400">Discan oleh</span>
                            <span class="text-gray-900 dark:text-white">{{ $item['scanned_by'] ?? '-' }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </template>

    <!-- ========================================== -->
    <!-- 2. TELEPORT MODAL PDF & GAMBAR (TERPISAH)  -->
    <!-- ========================================== -->
    @if($item['status'] === 'Tidak Hadir')
        <!-- Teleport untuk PDF -->
        <template x-teleport="body">
            <x-modal-pdf show="openPdf" url="https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf" />
        </template>
        
        <!-- Teleport untuk Gambar -->
        <template x-teleport="body">
            <x-modal-image show="openImg" url="https://placehold.co/800x1200/png?text=Bukti+Foto" />
        </template>
    @endif
</tr>