<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Landing Page Peserta METASTRO 2026 - Spirit of Hiro, Heart of Solder.">
    <title>METASTRO 2026 - Spirit of Hiro, Heart of Solder</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Caslon+Text:wght@700&family=Oswald:wght@600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .font-oswald {
            font-family: 'Oswald', sans-serif;
        }
        .font-poppins {
            font-family: 'Poppins', sans-serif;
        }
        .font-caslon {
            font-family: 'Libre Caslon Text', serif;
        }
        /* Aksen garis lengkung di bagian bawah kartu nilai */
        .card-bottom-accent {
            position: relative;
        }
        .card-bottom-accent::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 5%;
            right: 5%;
            height: 3px;
            background: linear-gradient(90deg, transparent, #fe5a1d, #f59e0b, transparent);
            border-radius: 9999px;
        }
    </style>
</head>
<body class="bg-stone-100 dark:bg-slate-950 font-poppins min-h-screen text-slate-800 antialiased flex justify-center selection:bg-orange-500 selection:text-white">

    <!-- Container Mobile View (Tengah di Desktop, Full-width di Mobile) -->
    <main class="w-full max-w-[430px] bg-white dark:bg-slate-900 min-h-screen shadow-2xl relative flex flex-col overflow-x-hidden border-x border-slate-200/60 dark:border-slate-800">

        <!-- 1. TOP NAVBAR -->
        <header class="w-full bg-white/95 dark:bg-slate-900/95 backdrop-blur-md px-4 py-2.5 flex items-center justify-between border-b border-orange-100/80 dark:border-slate-800 sticky top-0 z-40">
            <!-- Brand Logo HIMATRONIKA.AI -->
            <div class="flex items-center tracking-tight select-none">
                <span class="font-black text-lg text-[#FF7300]">HIMATRONIKA</span>
                <span class="font-black text-lg text-[#FF7300]">.AI</span>
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
                <div class=>
                    <img src="{{ $sponsorImg }}" alt="Sponsor Logos" class="h-5 w-auto object-contain max-w-[140px]" onerror="this.src='{{ asset('images/logo.webp') }}'">
                </div>
            </div>
        </header>

        <!-- 2. HERO SECTION -->
        <section class="relative w-full min-h-[350px] sm:min-h-[380px] flex flex-col justify-center items-center text-center px-4 py-8 overflow-hidden">
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
            <div class="absolute inset-0 bg-gradient-to-b from-black/30 via-black/25 to-black/40"></div>

            <!-- Judul & Tagline -->
            <div class="relative z-10 flex flex-col items-center">
                <h1 class="font-oswald text-4xl sm:text-[42px] font-bold text-[#FFEA79] tracking-tight drop-shadow-[0_4px_10px_rgba(0,0,0,0.85)] leading-none uppercase inline-block">
                    METASTRO <span class="text-white">2026.</span>
                </h1>
                <p class="font-caslon text-[11px] sm:text-xs font-bold text-white tracking-wider uppercase mt-2 drop-shadow-md max-w-full text-center">
                    'SPIRIT OF HIRO, HEART OF SOLDER'
                </p>
            </div>

            <!-- Tombol Aksi: LOGIN & LIHAT TIM -->
            <div class="relative z-10 mt-6 sm:mt-7 w-full flex flex-col items-center">
                <div class="flex items-center justify-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}"
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
        <section class="relative px-6 py-6 text-center">
            <!-- Teks Intro METASTRO -->
            <p class="text-slate-700 dark:text-slate-300 text-xs sm:text-[13px] leading-relaxed font-normal text-center px-1">
                <strong class="font-bold text-[#fe5a1d]">METASTRO</strong> merupakan program kaderisasi bagi mahasiswa baru yang berfokus pada pembentukan identitas, pengembangan kapasitas, serta penanaman nilai Spirit of HIRO, Heart of SOLDER.
            </p>
        </section>

        <!-- 4. CORE VALUES CARDS -->
        <section class="px-5 space-y-6 pt-2">
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
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-2.5 pb-4 shadow-sm border border-slate-100 dark:border-slate-700/80 card-bottom-accent">
                <div class="w-full h-40 sm:h-44 rounded-xl overflow-hidden mb-3 bg-slate-100 dark:bg-slate-700">
                    <img src="{{ $hiroImg }}" alt="Spirit of HIRO" class="w-full h-full object-cover" onerror="this.src='{{ asset('images/background-metastro.webp') }}'">
                </div>
                <div class="text-center">
                    <h2 class="font-oswald text-xl sm:text-2xl font-bold text-[#d97706] dark:text-amber-400 tracking-wider uppercase">
                        SPIRIT OF HIRO
                    </h2>
                    <p class="text-[10px] sm:text-[11px] font-semibold text-slate-500 dark:text-slate-400 tracking-[0.2em] uppercase mt-1">
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
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-2.5 pb-4 shadow-sm border border-slate-100 dark:border-slate-700/80 card-bottom-accent">
                <div class="w-full h-40 sm:h-44 rounded-xl overflow-hidden mb-3 bg-slate-100 dark:bg-slate-700">
                    <img src="{{ $solderImg }}" alt="Heart of SOLDER" class="w-full h-full object-cover" onerror="this.src='{{ asset('images/background-metastro.webp') }}'">
                </div>
                <div class="text-center">
                    <h2 class="font-oswald text-xl sm:text-2xl font-bold text-[#d97706] dark:text-amber-400 tracking-wider uppercase">
                        HEART OF SOLDER
                    </h2>
                    <p class="text-[10px] sm:text-[11px] font-semibold text-slate-500 dark:text-slate-400 tracking-[0.2em] uppercase mt-1">
                        HARMONY &bull; EMPATHY &bull; SYNERGY
                    </p>
                </div>
            </div>

            <!-- Garis Pemisah Oranye Horizontal -->
            <div class="w-full h-0.5 bg-gradient-to-r from-transparent via-[#fe5a1d]/70 to-transparent my-6"></div>
        </section>

        <!-- 5. TIMELINE KEGIATAN (Glowing Warm Gradient Card) -->
        <section class="px-5 pb-12">
            <div class="relative bg-gradient-to-b from-[#fff6ed] via-[#fee7d3] to-[#fcd9b6] dark:from-slate-800/90 dark:via-slate-800 dark:to-orange-950/40 rounded-3xl p-6 shadow-md border border-orange-200/70 dark:border-orange-500/20 overflow-hidden">
                
                <!-- Fluid Glow Ambient di Pojok Kanan Bawah -->
                <div class="absolute -right-6 -bottom-6 w-36 h-36 bg-gradient-to-tr from-orange-500 to-amber-300 opacity-40 blur-2xl rounded-full pointer-events-none"></div>

                <h3 class="font-oswald text-center text-xl font-bold text-[#b45309] dark:text-amber-400 tracking-wider uppercase mb-7">
                    TIMELINE KEGIATAN
                </h3>

                <!-- Timeline List -->
                <div class="relative pl-6 space-y-6">
                    <!-- Garis Konektor Vertikal Oranye -->
                    <div class="absolute left-2.5 top-2.5 bottom-3 w-[2px] bg-gradient-to-b from-amber-500 via-orange-400 to-orange-300"></div>

                    @if(isset($kegiatans) && $kegiatans->count() > 0)
                        @foreach($kegiatans as $index => $kegiatan)
                            @php
                                $today = \Carbon\Carbon::today();
                                $kegiatanDate = \Carbon\Carbon::parse($kegiatan->tanggal);
                                
                                if ($kegiatanDate->lt($today)) {
                                    $badgeText = 'selesai';
                                    $badgeClass = 'bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-950/60 dark:text-emerald-300';
                                    $dotColor = 'bg-emerald-500';
                                } elseif ($kegiatanDate->isToday()) {
                                    $badgeText = 'sedang berjalan';
                                    $badgeClass = 'bg-sky-100 text-sky-700 border-sky-300 animate-pulse dark:bg-sky-950/60 dark:text-sky-300';
                                    $dotColor = 'bg-sky-500';
                                } else {
                                    $badgeText = 'akan datang';
                                    $badgeClass = 'bg-orange-100/80 text-orange-800 border-orange-300 dark:bg-orange-950/60 dark:text-orange-300';
                                    $dotColor = 'bg-[#fe5a1d]';
                                }
                            @endphp

                            <div class="relative flex flex-col items-start text-left">
                                <!-- Dot Indikator -->
                                <span class="absolute -left-[23px] top-1 w-3.5 h-3.5 rounded-full {{ $dotColor }} border-2 border-white dark:border-slate-800 shadow-sm"></span>

                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-800 dark:text-white text-sm tracking-wide">
                                        {{ $kegiatan->nama ?? 'DAY ' . ($index + 1) }}
                                    </span>
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full border {{ $badgeClass }}">
                                        {{ $badgeText }}
                                    </span>
                                </div>

                                <div class="text-xs text-slate-600 dark:text-slate-300 mt-1">
                                    <span class="font-medium text-slate-800 dark:text-slate-100">{{ $kegiatanDate->translatedFormat('d F') }}</span>
                                    @if($kegiatan->waktu_mulai)
                                        <span class="text-slate-400 mx-1">&bull;</span>
                                        <span>{{ \Carbon\Carbon::parse($kegiatan->waktu_mulai)->format('H.i') }} - {{ $kegiatan->tempat ?? 'Kampus' }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- Sesuai Persis Tampilan Referensi Gambar -->
                        <div class="relative flex flex-col items-start text-left">
                            <span class="absolute -left-[23px] top-1 w-3.5 h-3.5 rounded-full bg-[#fe5a1d] border-2 border-white shadow-sm"></span>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-800 dark:text-white text-sm">DAY 1</span>
                                <span class="text-[10px] font-semibold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700 border border-emerald-300">selesai</span>
                            </div>
                            <div class="text-xs text-slate-600 dark:text-slate-300 mt-1">
                                <strong>13 Agustus</strong> &bull; 12.45 - LAB IPA
                            </div>
                        </div>

                        <div class="relative flex flex-col items-start text-left">
                            <span class="absolute -left-[23px] top-1 w-3.5 h-3.5 rounded-full bg-amber-500 border-2 border-white shadow-sm"></span>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-800 dark:text-white text-sm">DAY 2</span>
                                <span class="text-[10px] font-semibold px-2.5 py-0.5 rounded-full bg-sky-100 text-sky-700 border border-sky-300">sedang berjalan</span>
                            </div>
                            <div class="text-xs text-slate-600 dark:text-slate-300 mt-1">
                                <strong>20 Agustus</strong> &bull; 12.45 - Ruang 29
                            </div>
                        </div>

                        <div class="relative flex flex-col items-start text-left">
                            <span class="absolute -left-[23px] top-1 w-3.5 h-3.5 rounded-full bg-[#fe5a1d] border-2 border-white shadow-sm"></span>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-800 dark:text-white text-sm">DAY 3</span>
                                <span class="text-[10px] font-semibold px-2.5 py-0.5 rounded-full bg-orange-100 text-orange-800 border border-orange-300">akan datang</span>
                            </div>
                            <div class="text-xs text-slate-600 dark:text-slate-300 mt-1">
                                <strong>27 Agustus</strong> &bull; 12.45 - Albar
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <!-- Bottom Footer Singkat -->
        <footer class="w-full py-4 text-center text-[11px] text-slate-400 border-t border-slate-100 dark:border-slate-800">
            &copy; 2026 METASTRO &bull; HIMATRONIKA
        </footer>

    </main>

</body>
</html>
