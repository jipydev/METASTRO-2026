<div class="bg-primary-50/50 dark:bg-slate-700/60 rounded-2xl p-5 mb-4 border border-primary-100/50 dark:border-slate-700 font-poppins transition-all">

    <h3 class="font-bold text-lg text-primary-600 dark:text-primary-400 mb-1">
        {{ $item['judul'] }}
    </h3>

    <p class="text-slate-700 dark:text-slate-300 text-sm">
        {{ $item['tanggal'] }}
    </p>

    <p class="text-slate-600 dark:text-slate-400 text-xs mt-0.5">
        {{ $item['jam'] }} - {{ $item['ruangan'] }}
    </p>

    <a href="{{ $item['url'] ?? '#' }}"
        class="mt-4 flex justify-center items-center w-full bg-primary-500 hover:bg-primary-600 text-white font-bold text-sm rounded-xl py-2.5 transition-all shadow-sm">
        LIHAT PRESENSI &rarr;
    </a>

</div>