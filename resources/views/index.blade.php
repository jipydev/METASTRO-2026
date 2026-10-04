<x-guest-layout :$title>


    <!-- Container Mobile View (Tengah di Desktop, Full-width di Mobile) -->
    <main
        class="w-full bg-white dark:bg-slate-900 min-h-screen shadow-2xl relative flex flex-col overflow-x-hidden border-x border-slate-200/60 dark:border-slate-800">



        <!-- 1. TOP NAVBAR -->
        <header
            class="w-full bg-white/50 dark:bg-slate-900/50 backdrop-blur-xs px-4 py-2.5 flex items-center justify-between border-b border-orange-100/80 dark:border-slate-800 fixed left-0 right-0 top-0 z-40">
            <div class="container mx-auto flex justify-between items-center gap-2 sm:gap-4">
                <!-- Brand Logo Metastro 2026 -->
                <div class="flex items-center tracking-tight select-none font-oswald">
                    <span class="font-black text-lg text-[#FF7300]">METASTRO 2026</span>
                </div>

                <!-- Sponsor / Organization Logos -->
                <div class="flex items-center gap-2">
                    @php
                        $sponsorImg = file_exists(public_path('images/peserta/sponsor.webp'))
                            ? asset('images/peserta/sponsor.webp')
                            : (file_exists(public_path('images/peserta/sponsor.png'))
                                ? asset('images/peserta/sponsor.png')
                                : (file_exists(public_path('images/peserta/sponsor.jpg'))
                                    ? asset('images/peserta/sponsor.jpg')
                                    : asset('images/logo.webp')));
                    @endphp
                    <div class="shrink-0">
                        <img src="{{ $sponsorImg }}" alt="Sponsor Logos" class="h-5 md:h-6 object-contain w-auto"
                            onerror="this.src='{{ asset('images/logo.webp') }}'">
                    </div>
                </div>
            </div>
        </header>

        <!-- 2. HERO SECTION -->
        <section
            class="relative w-full min-h-87.5 sm:min-h-95 flex flex-col justify-center items-center text-center px-4 lg:px-0 py-8 mt-12 overflow-hidden">
            @php
                $heroImg = file_exists(public_path('images/peserta/hero.webp'))
                    ? asset('images/peserta/hero.webp')
                    : (file_exists(public_path('images/peserta/hero.jpg'))
                        ? asset('images/peserta/hero.jpg')
                        : (file_exists(public_path('images/peserta/hero.png'))
                            ? asset('images/peserta/hero.png')
                            : asset('images/background-metastro.webp')));
            @endphp
            <!-- Foto Massa Background -->
            <img src="{{ $heroImg }}" alt="Massa METASTRO 2026"
                class="absolute inset-0 w-full h-full object-cover object-center filter brightness-95" />
            <!-- Overlay Vignette & Gradient -->
            <div class="absolute inset-0 bg-linear-to-b from-black/30 via-black/25 to-black/40"></div>

            <!-- Judul & Tagline -->
            <div class="relative z-10 flex flex-col items-center">
                <h1
                    class="font-oswald text-4xl sm:text-[42px] font-bold text-[#FF7300] tracking-tight drop-shadow-[0_4px_10px_rgba(0,0,0,0.85)] leading-none uppercase inline-block">
                    METASTRO <span class="text-white">2026.</span>
                </h1>
                <p
                    class="font-caslon text-[11px] sm:text-xs font-bold text-white tracking-wider uppercase mt-2 drop-shadow-md max-w-full text-center">
                    'SPIRIT OF HIRO, HEART OF SOLDER'
                </p>
            </div>

            <!-- Tombol Aksi: LOGIN & LIHAT TIM -->
            <div class="relative z-10 mt-6 sm:mt-7 w-full flex flex-col items-center">
                <div class="flex items-center justify-center gap-3">
                    @auth
                        <a href="{{ route('dashboard.index') }}"
                            class="inline-flex items-center justify-center px-6 py-2 rounded-lg bg-black/35 hover:bg-black/50 text-white font-poppins text-xs sm:text-sm font-semibold tracking-wider uppercase border border-[#FF7300] shadow-[0_0_15px_rgba(255,115,0,0.35)] backdrop-blur-xs transition-all duration-200 transform hover:scale-105 active:scale-95">
                            DASHBOARD
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center justify-center px-7 py-2 rounded-lg bg-black/15 hover:bg-black/50 text-white font-poppins text-xs sm:text-sm font-semibold tracking-wider uppercase border border-[#FF7300] shadow-[0_0_15px_rgba(255,115,0,0.35)] backdrop-blur-xs transition-all duration-200 transform hover:scale-105 active:scale-95">
                            LOGIN
                        </a>
                    @endauth

                    <a href="#tim"
                        class="inline-flex items-center justify-center px-5 py-2 rounded-lg bg-black/15 hover:bg-black/50 text-white font-poppins text-xs sm:text-sm font-semibold tracking-wider uppercase border border-[#FF7300] shadow-[0_0_15px_rgba(255,115,0,0.35)] backdrop-blur-xs transition-all duration-200 transform hover:scale-105 active:scale-95">
                        LIHAT TIM
                    </a>
                </div>

                @auth
                    <span class="text-[11px] text-white/85 mt-2.5 font-medium drop-shadow">
                        Halo, <strong class="text-amber-300">{{ auth()->user()->nama }}</strong>
                    </span>
                @endauth
            </div>
        </section>

        <!-- 3. INTRO SECTION -->
        <section class="relative px-6 pt-6 pb-10 text-center overflow-visible">
            <!-- Bintang Kecil Atas Tengah -->
            <div class="flex justify-center mb-3">
                <img src="{{ asset('images/peserta/bintang2.png') }}" alt="Bintang" class="w-10 h-10 object-contain">
            </div>

            <!-- Teks Intro METASTRO -->
            <div class="container mx-auto">
                <h2 class="text-2xl sm:text-3xl font-bold text-[#FF7300] mb-2">
                    Selamat Datang di METASTRO 2026
                </h2>
                <p
                    class="text-slate-700 dark:text-slate-300 text-xs sm:text-[13px] leading-relaxed font-normal text-center relative z-10">
                    <strong class="font-bold text-[#fe5a1d]">METASTRO</strong> merupakan program kaderisasi bagi
                    mahasiswa baru yang berfokus pada pembentukan identitas, pengembangan kapasitas, serta penanaman
                    nilai Spirit of HIRO, Heart of SOLDER.
                </p>
            </div>

            <!-- Tali Gelombang Kiri Bawah -->
            <img src="{{ asset('images/peserta/tali.png') }}" alt="Tali"
                class="absolute -bottom-2 left-2 w-16 sm:w-20 h-auto object-contain pointer-events-none z-0">

            <!-- Gradient Glow Kanan Bawah -->
            <img src="{{ asset('images/peserta/gradient.png') }}" alt=""
                class="absolute -bottom-6 -right-4 w-32 sm:w-36 h-auto object-contain pointer-events-none opacity-70 z-20">
            <!-- Bintang Besar Kanan Bawah -->
            <img src="{{ asset('images/peserta/bintang.png') }}" alt="Bintang"
                class="absolute -bottom-6 -right-2 w-16 sm:w-20 h-auto object-contain pointer-events-none z-[1]">
        </section>

        <!-- 4. CORE VALUES CARDS -->
        <div class="container mx-auto px-4 lg:px-0">
            <section class="space-y-6 pt-2">
                <!-- Card 1: SPIRIT OF HIRO -->
                @php
                    $hiroImg = file_exists(public_path('images/peserta/spirit-of-hiro.webp'))
                        ? asset('images/peserta/spirit-of-hiro.webp')
                        : (file_exists(public_path('images/peserta/spirit-of-hiro.jpg'))
                            ? asset('images/peserta/spirit-of-hiro.jpg')
                            : (file_exists(public_path('images/peserta/spirit-of-hiro.png'))
                                ? asset('images/peserta/spirit-of-hiro.png')
                                : asset('images/background-metastro.webp')));
                @endphp
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl p-2.5 pb-4 shadow-sm border border-slate-100 dark:border-slate-700/80 card-bottom-accent">
                    <div class="w-full h-40 sm:h-44 rounded-xl overflow-hidden mb-3 bg-slate-100 dark:bg-slate-700">
                        <img src="{{ $hiroImg }}" alt="Spirit of HIRO"
                            class="w-full h-full object-cover origin-top"
                            onerror="this.src='{{ asset('images/background-metastro.webp') }}'">
                    </div>
                    <div class="text-center">
                        <h2
                            class="font-oswald text-xl sm:text-2xl font-bold text-secondary-600 dark:text-amber-400 tracking-wider uppercase">
                            SPIRIT OF HIRO
                        </h2>
                        <p
                            class="text-[10px] sm:text-[11px] font-semibold text-slate-500 dark:text-slate-400 tracking-[0.2em] uppercase mt-1">
                            INTEGRITY &bull; RESPONSIBILITY &bull; OBJECTIVITY
                        </p>
                    </div>
                </div>

                <!-- Card 2: HEART OF SOLDER -->
                @php
                    $solderImg = file_exists(public_path('images/peserta/heart-of-solder.webp'))
                        ? asset('images/peserta/heart-of-solder.webp')
                        : (file_exists(public_path('images/peserta/heart-of-solder.jpg'))
                            ? asset('images/peserta/heart-of-solder.jpg')
                            : (file_exists(public_path('images/peserta/heart-of-solder.png'))
                                ? asset('images/peserta/heart-of-solder.png')
                                : asset('images/background-metastro.webp')));
                @endphp
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl p-2.5 pb-4 shadow-sm border border-slate-100 dark:border-slate-700/80 card-bottom-accent">
                    <div class="w-full h-40 sm:h-44 rounded-xl overflow-hidden mb-3 bg-slate-100 dark:bg-slate-700">
                        <img src="{{ $solderImg }}" alt="Heart of SOLDER" class="w-full h-full object-cover"
                            onerror="this.src='{{ asset('images/background-metastro.webp') }}'">
                    </div>
                    <div class="text-center">
                        <h2
                            class="font-oswald text-xl sm:text-2xl font-bold text-[#d97706] dark:text-amber-400 tracking-wider uppercase">
                            HEART OF SOLDER
                        </h2>
                        <p
                            class="text-[10px] sm:text-[11px] font-semibold text-slate-500 dark:text-slate-400 tracking-[0.2em] uppercase mt-1">
                            HARMONY &bull; EMPATHY &bull; SYNERGY
                        </p>
                    </div>
                </div>

                <!-- Garis Pemisah Oranye Horizontal -->
                <div class="w-full h-0.5 bg-linear-to-r from-transparent via-[#fe5a1d]/70 to-transparent my-6">
                </div>
            </section>
        </div>

        <!-- 5. TIMELINE KEGIATAN (Glowing Warm Gradient Card) -->
        <section class="pb-12">
            <div class="container mx-auto">
                <div
                    class="relative bg-linear-to-b from-[#fff6ed] via-[#fee7d3] to-[#fcd9b6] dark:from-slate-800/90 dark:via-slate-800 dark:to-orange-950/10 md:rounded-3xl md:mx-4 lg:mx-0 p-6 shadow-md border border-orange-200/70 dark:border-orange-500/20 overflow-hidden">

                    <!-- Fluid Glow Ambient di Pojok Kanan Bawah -->
                    <div
                        class="absolute -right-6 -bottom-6 w-36 h-36 bg-linear-to-tr from-orange-500 to-amber-300 opacity-40 blur-2xl rounded-full pointer-events-none">
                    </div>

                    <h3
                        class="font-oswald text-center text-xl font-bold text-[#b45309] dark:text-amber-400 tracking-wider uppercase mb-7">
                        TIMELINE KEGIATAN
                    </h3>

                    <!-- Timeline List -->

                    <div class="relative pl-6 space-y-6">
                        <!-- Garis Konektor Vertikal Oranye -->
                        <div
                            class="absolute left-1.5 top-2.5 bottom-3 w-0.5 bg-linear-to-b from-amber-500 via-orange-400 to-orange-300">
                        </div>

                        @if (isset($kegiatans) && $kegiatans->count() > 0)
                            @foreach ($kegiatans as $index => $kegiatan)
                                @php
                                    $today = \Carbon\Carbon::today();
                                    $kegiatanDate = \Carbon\Carbon::parse($kegiatan->tanggal_mulai);

                                    if ($kegiatanDate->lt($today)) {
                                        $badgeText = 'selesai';
                                        $badgeClass =
                                            'bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-950/60 dark:text-emerald-300';
                                        $dotColor = 'bg-emerald-500';
                                    } elseif ($kegiatanDate->isToday()) {
                                        $badgeText = 'sedang berjalan';
                                        $badgeClass =
                                            'bg-sky-100 text-sky-700 border-sky-300 animate-pulse dark:bg-sky-950/60 dark:text-sky-300';
                                        $dotColor = 'bg-sky-500';
                                    } else {
                                        $badgeText = 'akan datang';
                                        $badgeClass =
                                            'bg-orange-100/80 text-orange-800 border-orange-300 dark:bg-orange-950/60 dark:text-orange-300';
                                        $dotColor = 'bg-[#fe5a1d]';
                                    }
                                @endphp

                                <div class="relative flex flex-col items-start text-left">
                                    <!-- Dot Indikator -->
                                    <span
                                        class="absolute -left-5.75 top-1 w-3.5 h-3.5 rounded-full {{ $dotColor }} border-2 border-white dark:border-slate-800 shadow-sm"></span>

                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-800 dark:text-white text-sm tracking-wide">
                                            {{ $kegiatan->nama ?? 'DAY ' . ($index + 1) }}
                                        </span>
                                        <span
                                            class="text-[10px] font-semibold px-2 py-0.5 rounded-full border {{ $badgeClass }}">
                                            {{ $badgeText }}
                                        </span>
                                    </div>

                                    <div class="text-xs text-slate-600 dark:text-slate-300 mt-1">
                                        <span
                                            class="font-medium text-slate-800 dark:text-slate-100">{{ $kegiatanDate->translatedFormat('d F') }}</span>
                                        @if ($kegiatan->tanggal_selesai && $kegiatan->tanggal_selesai->ne($kegiatan->tanggal_mulai))
                                            <span> - {{ $kegiatan->tanggal_selesai->translatedFormat('d F') }}</span>
                                        @endif
                                        @if ($kegiatan->waktu_mulai)
                                            <span class="text-slate-400 mx-1">&bull;</span>
                                            <span>{{ \Carbon\Carbon::parse($kegiatan->waktu_mulai)->format('H.i') }}
                                                @if ($kegiatan->waktu_selesai) - {{ \Carbon\Carbon::parse($kegiatan->waktu_selesai)->format('H.i') }} @endif
                                                -
                                                {{ $kegiatan->tempat ?? 'Kampus' }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <!-- Sesuai Persis Tampilan Referensi Gambar -->
                            <div class="relative flex flex-col items-start text-left">
                                <span
                                    class="absolute -left-5.75 top-1 w-3.5 h-3.5 rounded-full bg-[#fe5a1d] border-2 border-white shadow-sm"></span>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-800 dark:text-white text-sm">DAY 1</span>
                                    <span
                                        class="text-[10px] font-semibold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700 border border-emerald-300">selesai</span>
                                </div>
                                <div class="text-xs text-slate-600 dark:text-slate-300 mt-1">
                                    <strong>13 Agustus</strong> &bull; 12.45 - LAB IPA
                                </div>
                            </div>

                            <div class="relative flex flex-col items-start text-left">
                                <span
                                    class="absolute -left-5.75 top-1 w-3.5 h-3.5 rounded-full bg-amber-500 border-2 border-white shadow-sm"></span>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-800 dark:text-white text-sm">DAY 2</span>
                                    <span
                                        class="text-[10px] font-semibold px-2.5 py-0.5 rounded-full bg-sky-100 text-sky-700 border border-sky-300">sedang
                                        berjalan</span>
                                </div>
                                <div class="text-xs text-slate-600 dark:text-slate-300 mt-1">
                                    <strong>20 Agustus</strong> &bull; 12.45 - Ruang 29
                                </div>
                            </div>

                            <div class="relative flex flex-col items-start text-left">
                                <span
                                    class="absolute -left-5.75 top-1 w-3.5 h-3.5 rounded-full bg-[#fe5a1d] border-2 border-white shadow-sm"></span>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-800 dark:text-white text-sm">DAY 3</span>
                                    <span
                                        class="text-[10px] font-semibold px-2.5 py-0.5 rounded-full bg-orange-100 text-orange-800 border border-orange-300">akan
                                        datang</span>
                                </div>
                                <div class="text-xs text-slate-600 dark:text-slate-300 mt-1">
                                    <strong>27 Agustus</strong> &bull; 12.45 - Albar
                                </div>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </section>

        <!-- Bottom Footer Singkat -->
        <footer
            class="w-full py-4 text-center text-[11px] text-slate-400 border-t border-slate-100 dark:border-slate-800">
            &copy; 2026 METASTRO &bull; All Rights Reserved.
        </footer>
    </main>


</x-guest-layout>
