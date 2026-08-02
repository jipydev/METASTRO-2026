<div class="bg-sky-50 rounded-2xl p-5 mb-4">

    <h3 class="font-bold">

        {{ $item['judul'] }}

    </h3>

    <p>

        {{ $item['tanggal'] }}

    </p>

    <p>

        {{ $item['jam'] }} -
        {{ $item['ruangan'] }}

    </p>

    <button
        class="mt-4 w-full bg-cyan-800 text-white rounded-lg py-2 transition-transform duration-200 hover:scale-105">
        LIHAT PRESENSI →

    </button>

</div>