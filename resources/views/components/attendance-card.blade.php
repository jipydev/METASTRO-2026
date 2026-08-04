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

    <a href="{{ $item['url'] ?? '#' }}"
        class="mt-4 flex justify-center items-center w-full bg-cyan-800 hover:bg-cyan-900 text-white font-bold rounded-lg py-2.5 transition-transform duration-200 hover:scale-105">
        LIHAT PRESENSI &rarr;

    </a>

</div>