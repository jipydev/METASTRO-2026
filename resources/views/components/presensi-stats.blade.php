@props([
    'hadir' => 0,
    'terlambat' => 0,
    'izin' => 0,
    'sakit' => 0,
    'belum' => 0,
    'belumLabel' => 'Belum Absen',
])

<div {{ $attributes->class('grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5') }}>
    <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl border border-emerald-100 dark:border-emerald-800/40 flex flex-col justify-between">
        <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Hadir</span>
        <span class="text-2xl font-black text-emerald-700 dark:text-emerald-300 font-mono mt-1">{{ $hadir }}</span>
    </div>

    <div class="p-3 bg-rose-50 dark:bg-rose-950/40 rounded-xl border border-rose-100 dark:border-rose-800/40 flex flex-col justify-between">
        <span class="text-[11px] font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider">Terlambat</span>
        <span class="text-2xl font-black text-rose-700 dark:text-rose-300 font-mono mt-1">{{ $terlambat }}</span>
    </div>

    <div class="p-3 bg-blue-50 dark:bg-blue-950/40 rounded-xl border border-blue-100 dark:border-blue-800/40 flex flex-col justify-between">
        <span class="text-[11px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Izin</span>
        <span class="text-2xl font-black text-blue-700 dark:text-blue-300 font-mono mt-1">{{ $izin }}</span>
    </div>

    <div class="p-3 bg-amber-50 dark:bg-amber-950/40 rounded-xl border border-amber-100 dark:border-amber-800/40 flex flex-col justify-between">
        <span class="text-[11px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Sakit</span>
        <span class="text-2xl font-black text-amber-700 dark:text-amber-300 font-mono mt-1">{{ $sakit }}</span>
    </div>

    <div class="p-3 bg-slate-100 dark:bg-slate-700/50 rounded-xl border border-slate-200 dark:border-slate-600 flex flex-col justify-between">
        <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider leading-tight">{{ $belumLabel }}</span>
        <span class="text-2xl font-black text-slate-700 dark:text-slate-300 font-mono mt-1">{{ $belum }}</span>
    </div>
</div>
