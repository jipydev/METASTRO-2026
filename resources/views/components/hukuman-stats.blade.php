@props([
    'total' => 0,
    'aktif' => 0,
    'menungguPembelaan' => 0,
    'deadline' => 0,
    'selesai' => 0,
    'ringan' => 0,
    'sedang' => 0,
    'berat' => 0,
    'khusus' => 0,
])

<div {{ $attributes }}>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5">
        <div class="p-3 bg-slate-100 dark:bg-slate-700/50 rounded-xl border border-slate-200 dark:border-slate-600 flex flex-col justify-between">
            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total</span>
            <span class="text-2xl font-black text-slate-700 dark:text-slate-300 font-mono mt-1">{{ $total }}</span>
        </div>

        <div class="p-3 bg-sky-50 dark:bg-sky-950/40 rounded-xl border border-sky-100 dark:border-sky-800/40 flex flex-col justify-between">
            <span class="text-[11px] font-bold text-sky-600 dark:text-sky-400 uppercase tracking-wider">Aktif</span>
            <span class="text-2xl font-black text-sky-700 dark:text-sky-300 font-mono mt-1">{{ $aktif }}</span>
        </div>

        <div class="p-3 bg-amber-50 dark:bg-amber-950/40 rounded-xl border border-amber-100 dark:border-amber-800/40 flex flex-col justify-between">
            <span class="text-[10px] sm:text-[11px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider leading-tight">Menunggu Pembelaan</span>
            <span class="text-2xl font-black text-amber-700 dark:text-amber-300 font-mono mt-1">{{ $menungguPembelaan }}</span>
        </div>

        <div class="p-3 bg-rose-50 dark:bg-rose-950/40 rounded-xl border border-rose-100 dark:border-rose-800/40 flex flex-col justify-between">
            <span class="text-[10px] sm:text-[11px] font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider leading-tight">Lewat Deadline</span>
            <span class="text-2xl font-black text-rose-700 dark:text-rose-300 font-mono mt-1">{{ $deadline }}</span>
        </div>

        <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl border border-emerald-100 dark:border-emerald-800/40 flex flex-col justify-between">
            <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Selesai</span>
            <span class="text-2xl font-black text-emerald-700 dark:text-emerald-300 font-mono mt-1">{{ $selesai }}</span>
        </div>
    </div>

    <div class="mt-3 flex flex-wrap gap-2">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300">
            Ringan <span class="font-mono">{{ $ringan }}</span>
        </span>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300">
            Sedang <span class="font-mono">{{ $sedang }}</span>
        </span>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-orange-100 text-orange-700 dark:bg-orange-950/50 dark:text-orange-300">
            Berat <span class="font-mono">{{ $berat }}</span>
        </span>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300">
            Khusus <span class="font-mono">{{ $khusus }}</span>
        </span>
    </div>
</div>
