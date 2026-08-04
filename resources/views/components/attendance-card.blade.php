<div class="bg-sky-50 rounded-2xl p-5 mb-4">
    <h3 class="font-bold">
        {{ $timeline->judul }}
    </h3>

    <p>
        {{ $timeline->tanggal_mulai->format('d M Y') }}
    </p>

    <p>
        {{ $timeline->tanggal_mulai->format('H:i') }} - {{ $timeline->tanggal_selesai->format('H:i') }} WIB
        {{ $timeline->ruangan ? 'di ' . $timeline->ruangan : '' }}
    </p>

    <a href="{{ route('dashboard.presensi.show', $timeline->slug) }}"
        class="mt-4 flex justify-center items-center w-full bg-cyan-800 hover:bg-cyan-900 text-white font-bold rounded-lg py-2.5 transition-transform duration-200 hover:scale-105">
        LIHAT PRESENSI &rarr;
    </a>
</div>
