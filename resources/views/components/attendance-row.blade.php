@props(['presensi'])

@php
    $panitia = optional($presensi->panitia);
    $panitiaName = $panitia->name ?? '-';
    $initial = $panitiaName !== '-' ? strtoupper(substr($panitiaName, 0, 1)) : '?';
    $divisiName = optional($panitia->divisi)->nama_divisi ?? '-';
    $jabatanName = optional($panitia->jabatan)->nama_jabatan ?? '-';
    $attendanceDate = $presensi->waktu_presensi
        ? \Carbon\Carbon::parse($presensi->waktu_presensi)->format('d M Y')
        : '-';
    $attendanceTime = $presensi->waktu_presensi
        ? \Carbon\Carbon::parse($presensi->waktu_presensi)->format('H:i:s')
        : '-';
    $attendanceDateLong = $presensi->waktu_presensi
        ? \Carbon\Carbon::parse($presensi->waktu_presensi)->format('l, d M Y')
        : '-';
    $isProofStatus = $presensi->isStatus('Izin') || $presensi->isStatus('Sakit');
    $showPhotoButton = $isProofStatus;

    $statusValue = trim((string) ($presensi->status ?? ''));
    $normalizedStatus = strtolower($statusValue);

    $statusStyle = match ($normalizedStatus) {
        'hadir' => ['bg' => 'bg-[#e2fae8]', 'text' => 'text-[#0e702c]', 'dot' => 'text-[#0ea53b]'],
        'telat' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'dot' => 'text-red-500'],
        'izin' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'dot' => 'text-green-500'],
        'sakit' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'dot' => 'text-yellow-500'],
        'alpha' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'dot' => 'text-red-500'],
        default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'dot' => 'text-gray-500'],
    };
@endphp

<tr x-data="{ openBukti: false, openPdf: false, openImg: false }"
    class="flex flex-col md:table-row border-b border-gray-100 hover:bg-gray-50 transition-colors bg-white mb-4 md:mb-0 rounded-lg md:rounded-none shadow-sm md:shadow-none p-4 md:p-0">

    <!-- Kolom Panitia -->
    <td class="py-2 md:py-3 md:px-4 flex items-center justify-between md:table-cell gap-4">
        <span class="md:hidden text-xs font-bold text-slate-500 uppercase">Panitia</span>
        <div class="flex items-center gap-4 text-right md:text-left">
            <div
                class="w-10 h-10 rounded-full bg-[#e6f0ff] text-blue-600 flex items-center justify-center font-semibold text-lg shrink-0">
                {{ $initial }}
            </div>
            <span class="font-semibold text-gray-900 text-[15px]">{{ $panitiaName }}</span>
        </div>
    </td>

    <!-- Kolom Divisi -->
    <td class="py-2 md:py-3 md:px-4 flex items-center justify-between md:table-cell text-slate-500 text-[15px]">
        <span class="md:hidden text-xs font-bold text-slate-500 uppercase">Divisi</span>
        {{ $divisiName }}
    </td>

    <!-- Kolom Jabatan -->
    <td class="py-2 md:py-3 md:px-4 flex items-center justify-between md:table-cell text-slate-500 text-[15px]">
        <span class="md:hidden text-xs font-bold text-slate-500 uppercase">Jabatan</span>
        {{ $jabatanName }}
    </td>

    <!-- Kolom Tanggal -->
    <td class="py-2 md:py-3 md:px-4 flex items-center justify-between md:table-cell text-slate-500 text-[15px]">
        <span class="md:hidden text-xs font-bold text-slate-500 uppercase">Tanggal</span>
        {{ $attendanceDate }}
    </td>

    <!-- Kolom Waktu Presensi -->
    <td
        class="py-2 md:py-3 md:px-4 flex items-center justify-between md:table-cell text-gray-900 font-medium text-[15px]">
        <span class="md:hidden text-xs font-bold text-slate-500 uppercase">Waktu Presensi</span>
        {{ $attendanceTime }}
    </td>

    <!-- Kolom Status -->
    <td class="py-2 md:py-3 md:px-4 flex items-center justify-between md:table-cell">
        <span class="md:hidden text-xs font-bold text-slate-500 uppercase">Status</span>
        <span
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[13px] font-semibold {{ $statusStyle['bg'] }} {{ $statusStyle['text'] }}">
            <span class="{{ $statusStyle['dot'] }} text-lg leading-none">&bull;</span>
            {{ $statusValue ?: '-' }}
        </span>
    </td>

    <!-- Kolom Aksi -->
    <td
        class="py-3 md:px-4 flex items-center justify-between md:table-cell mt-2 md:mt-0 border-t md:border-0 border-gray-100">
        <span class="md:hidden text-xs font-bold text-slate-500 uppercase">Aksi</span>
        <button type="button" @click="openBukti = true"
            class="px-3 py-1.5 bg-[#0c5970] text-white text-xs font-semibold rounded hover:bg-[#084254] transition-colors shadow-sm">
            Lihat Bukti
        </button>
    </td>

    <!-- Modal utama -->
    <template x-teleport="body">
        <div x-show="openBukti"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
            style="display: none;" x-transition>
            <div @click.outside="openBukti = false" class="bg-white rounded-2xl p-6 w-full max-w-sm shadow-xl relative">
                <button type="button" @click="openBukti = false"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>

                <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">
                    {{ $isProofStatus ? 'Detail Bukti' : 'Bukti Kehadiran' }}
                </h3>

                <div class="space-y-3 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-500">Hari, Tanggal</span>
                        <span class="text-gray-900">{{ $attendanceDateLong }}</span>
                    </div>

                    @if ($isProofStatus)
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-500">Jenis Izin</span>
                            <span class="text-gray-900 font-semibold">{{ $presensi->jenis_izin ?? '-' }}</span>
                        </div>
                        <div class="pt-4 grid grid-cols-2 gap-3 border-t mt-2">
                            <button type="button" @click="openPdf = true"
                                class="w-full py-2 bg-blue-50 text-blue-700 font-semibold rounded-lg border border-blue-200 hover:bg-blue-100 transition flex items-center justify-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Surat Izin
                            </button>
                            <button type="button" @click="openImg = true"
                                class="w-full py-2 bg-green-50 text-green-700 font-semibold rounded-lg border border-green-200 hover:bg-green-100 transition flex items-center justify-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                Bukti Foto
                            </button>
                        </div>
                    @else
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-500">Jam Hadir</span>
                            <span class="text-gray-900 font-bold text-[#0ea53b]">{{ $attendanceTime }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-500">Discan oleh</span>
                            <span class="text-gray-900">{{ $presensi->scannedByUser->name ?? '-' }}</span>
                        </div>
                        @if ($showPhotoButton)
                            <div class="pt-4">
                                <button type="button" @click="openImg = true"
                                    class="w-full py-2 bg-green-50 text-green-700 font-semibold rounded-lg border border-green-200 hover:bg-green-100 transition flex items-center justify-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    Lihat Bukti Foto
                                </button>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </template>

    <!-- Modal pendukung -->
    @if ($presensi->isStatus('Izin') || $presensi->isStatus('Sakit'))
        <template x-teleport="body">
            <x-modal-pdf show="openPdf" url="https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf" />
        </template>
    @endif

    <template x-teleport="body">
        <x-modal-image show="openImg" url="https://placehold.co/800x1200/png?text=Bukti+Foto+{{ $presensi->id }}" />
    </template>
</tr>
