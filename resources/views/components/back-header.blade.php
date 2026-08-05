<div class="mb-6 flex flex-col items-start font-poppins">

    <!-- Tombol Kembali (Di atas) -->
    <a href="{{ $href }}"
        class="mb-2 inline-flex items-center gap-1 text-slate-500 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 transition font-medium text-sm">
        <svg xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="2"
            stroke="currentColor"
            class="w-4 h-4">
            <path stroke-linecap="round"
                stroke-linejoin="round"
                d="M15.75 19.5 8.25 12l7.5-7.5" />
        </svg>

        <span>Kembali</span>
    </a>

    <!-- Judul Title (Di bawah) -->
    <h1 class="text-2xl sm:text-3xl font-bold text-primary-600 dark:text-primary-400">
        {{ $title }}
    </h1>

</div>