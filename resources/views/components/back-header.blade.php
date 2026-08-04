<div class="mb-6 flex flex-col items-start">

    <!-- Tombol Kembali (Di atas) -->
    <a href="{{ $href }}"
        class="mb-2 inline-flex items-center gap-1 text-gray-600 hover:text-cyan-700 transition">
        <svg xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="w-5 h-5">
            <path stroke-linecap="round"
                stroke-linejoin="round"
                d="M15.75 19.5 8.25 12l7.5-7.5" />
        </svg>

        <span class="text-base font-medium">
            Kembali
        </span>
    </a>

    <!-- Judul Title (Di bawah) -->
    <h1 class="text-3xl font-bold text-cyan-800">
        {{ $title }}
    </h1>

</div>